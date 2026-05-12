<?php

namespace App\Http\Controllers;

use App\Models\RepairOrder;
use App\Models\RepairStage;
use App\Models\RepairLine;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.read');
        $orders = RepairOrder::with(['product', 'customer'])->latest()->paginate(20);
        return view('erp.repair.index', compact('orders'));
    }

    public function create()
    {
        $this->authorize('inventory.create');
        $products = Product::all();
        $customers = Customer::all();
        $users = User::all();
        return view('erp.repair.create', compact('products', 'customers', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'assigned_to' => 'nullable|exists:users,id',
            'diagnosis' => 'nullable|string',
            'priority' => 'required|string|max:50',
            'date_requested' => 'required|date',
        ]);

        RepairOrder::create($validated);

        return back()->with('success', 'Repair order created successfully.');
    }

    public function show(RepairOrder $order)
    {
        $this->authorize('inventory.read');
        $order->load(['product', 'customer', 'assignee', 'lines']);
        $products = Product::all();
        return view('erp.repair.show', compact('order', 'products'));
    }

    public function updateStatus(Request $request, RepairOrder $order)
    {
        $this->authorize('inventory.update');
        $validated = $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $data = ['status' => $validated['status']];
        if ($validated['status'] === 'done') {
            $data['date_completed'] = now();
        }

        $order->update($data);

        return back()->with('success', 'Order status updated successfully.');
    }

    public function addLine(Request $request, RepairOrder $order)
    {
        $this->authorize('inventory.update');
        $validated = $request->validate([
            'description' => 'required|string',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'cost' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'type' => 'required|string|max:50',
        ]);

        RepairLine::create([
            'repair_order_id' => $order->id,
            'description' => $validated['description'],
            'quantity' => $validated['quantity'],
            'cost' => $validated['cost'] ?? 0,
            'price' => $validated['price'] ?? 0,
            'type' => $validated['type'],
        ]);

        return back()->with('success', 'Line added successfully.');
    }

    public function destroyLine(RepairLine $line)
    {
        $this->authorize('inventory.delete');
        $line->delete();
        return back()->with('success', 'Line deleted successfully.');
    }
}
