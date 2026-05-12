<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index()
    {
        $this->authorize('sales.read');
        $orders = SalesOrder::with(['customer', 'creator'])->latest()->paginate(20);
        return view('erp.sales.index', compact('orders'));
    }

    public function create()
    {
        $this->authorize('sales.create');
        $customers = Customer::where('status', 'active')->get();
        $products = Product::where('is_active', true)->get();
        
        return view('erp.sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('sales.create');
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $order = SalesOrder::create([
                'customer_id' => $validated['customer_id'],
                'date' => $validated['date'],
                'code' => 'SO/' . date('Y') . '/' . str_pad(SalesOrder::count() + 1, 3, '0', STR_PAD_LEFT),
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

        return redirect()->route('sales.index')->with('success', 'Sales Order created successfully.');
    }

    public function confirm(SalesOrder $order)
    {
        $this->authorize('sales.update');
        if ($order->status !== 'draft' && $order->status !== 'sent') return back()->with('error', 'Order cannot be confirmed.');

        $order->update(['status' => 'confirmed']);
        return back()->with('success', 'Sales Order confirmed.');
    }

    public function show(SalesOrder $sale) // route sales.show
    {
        $order = $sale->load(['customer', 'creator', 'lines.product']);
        return view('erp.sales.show', compact('order'));
    }
}
