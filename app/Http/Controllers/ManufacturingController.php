<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\ManufacturingOrder;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMove;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturingController extends Controller
{
    public function index()
    {
        $this->authorize('manufacturing.read');
        $orders = ManufacturingOrder::with(['product', 'creator'])->latest()->paginate(20);
        return view('erp.manufacturing.index', compact('orders'));
    }

    public function create()
    {
        $this->authorize('manufacturing.create');
        $products = Product::where('is_active', true)->get();
        $boms = Bom::all();
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('erp.manufacturing.create', compact('products', 'boms', 'warehouses'));
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.create');
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'bom_id' => 'required|exists:boms,id',
            'quantity' => 'required|numeric|min:0.01',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        ManufacturingOrder::create([
            'code' => 'MO/' . date('Y') . '/' . str_pad(ManufacturingOrder::count() + 1, 3, '0', STR_PAD_LEFT),
            'product_id' => $validated['product_id'],
            'bom_id' => $validated['bom_id'],
            'quantity' => $validated['quantity'],
            'warehouse_id' => $validated['warehouse_id'],
            'status' => 'confirmed',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('manufacturing.index')->with('success', 'Manufacturing Order created.');
    }

    public function complete(ManufacturingOrder $order)
    {
        $this->authorize('manufacturing.update');
        if ($order->status === 'done') return back()->with('error', 'Order already completed.');

        DB::transaction(function () use ($order) {
            $bom = $order->bom;
            
            // 1. Consume Components
            foreach ($bom->lines as $line) {
                $consumeQty = $line->quantity * ($order->quantity / $bom->quantity);
                
                $stock = StockLevel::where('product_id', $line->product_id)
                    ->where('warehouse_id', $order->warehouse_id)
                    ->first();
                
                if (!$stock || $stock->quantity < $consumeQty) {
                    throw new \Exception("Insufficient stock for component: " . $line->product->name);
                }

                $stock->decrement('quantity', $consumeQty);
                
                StockMove::create([
                    'product_id' => $line->product_id,
                    'from_warehouse_id' => $order->warehouse_id,
                    'quantity' => $consumeQty,
                    'type' => 'internal',
                    'reference' => 'MO Consume: ' . $order->code,
                ]);
            }

            // 2. Produce Finished Product
            $finishedStock = StockLevel::firstOrCreate(
                ['product_id' => $order->product_id, 'warehouse_id' => $order->warehouse_id],
                ['quantity' => 0]
            );
            $finishedStock->increment('quantity', $order->quantity);

            StockMove::create([
                'product_id' => $order->product_id,
                'to_warehouse_id' => $order->warehouse_id,
                'quantity' => $order->quantity,
                'type' => 'internal',
                'reference' => 'MO Produce: ' . $order->code,
            ]);

            $order->update(['status' => 'done']);
        });

        return back()->with('success', 'Manufacturing order completed. Inventory updated.');
    }

    public function boms()
    {
        $this->authorize('manufacturing.read');
        $boms = Bom::with(['product', 'lines.product'])->get();
        return view('erp.manufacturing.boms', compact('boms'));
    }

    public function show(ManufacturingOrder $manufacturing)
    {
        $order = $manufacturing->load(['product', 'bom.lines.product', 'warehouse', 'creator']);
        return view('erp.manufacturing.show', compact('order'));
    }
}
