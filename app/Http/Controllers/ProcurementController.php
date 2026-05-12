<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockLevel;
use App\Models\StockMove;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    public function index()
    {
        $this->authorize('procurement.read');
        $orders = PurchaseOrder::with(['vendor', 'creator'])->latest()->paginate(20);
        return view('erp.procurement.index', compact('orders'));
    }

    public function create()
    {
        $this->authorize('procurement.create');
        $vendors = Vendor::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('erp.procurement.create', compact('vendors', 'products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $this->authorize('procurement.create');
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $order = PurchaseOrder::create([
                'vendor_id' => $validated['vendor_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'date' => $validated['date'],
                'code' => 'PO/' . date('Y') . '/' . str_pad(PurchaseOrder::count() + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $order->lines()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
            }

            $order->update(['total_amount' => $total]);
        });

        return redirect()->route('procurement.index')->with('success', 'Purchase Order created successfully.');
    }

    public function receive(PurchaseOrder $order)
    {
        $this->authorize('procurement.update');
        if ($order->status === 'received') return back()->with('error', 'Order already received.');

        DB::transaction(function () use ($order) {
            foreach ($order->lines as $line) {
                $stockLevel = StockLevel::firstOrCreate(
                    ['product_id' => $line->product_id, 'warehouse_id' => $order->warehouse_id],
                    ['quantity' => 0]
                );
                $stockLevel->quantity += $line->quantity;
                $stockLevel->save();

                StockMove::create([
                    'product_id' => $line->product_id,
                    'to_warehouse_id' => $order->warehouse_id,
                    'quantity' => $line->quantity,
                    'type' => 'incoming',
                    'reference' => 'PO: ' . $order->code,
                ]);
            }
            $order->update(['status' => 'received']);
        });

        return back()->with('success', 'Goods received and inventory updated.');
    }

    public function show(PurchaseOrder $procurement) // route procurement.show
    {
        $order = $procurement->load(['vendor', 'warehouse', 'creator', 'lines.product']);
        return view('erp.procurement.show', compact('order'));
    }
}
