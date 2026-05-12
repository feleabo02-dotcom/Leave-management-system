<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMove;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.read');
        $products = Product::with(['stockLevels.warehouse'])->latest()->paginate(20);
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('erp.inventory.index', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:products,code',
            'type' => 'required|in:stockable,consumable,service',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($validated);

        return back()->with('success', 'Product created successfully.');
    }

    public function adjustStock(Request $request)
    {
        $this->authorize('inventory.update');
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric',
            'type' => 'required|in:incoming,outgoing,adjustment',
            'reference' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $stockLevel = StockLevel::firstOrCreate(
                ['product_id' => $request->product_id, 'warehouse_id' => $request->warehouse_id],
                ['quantity' => 0]
            );

            $change = $request->quantity;
            if ($request->type === 'outgoing') $change = -$change;

            if ($request->type === 'adjustment') {
                $diff = $request->quantity - $stockLevel->quantity;
                $stockLevel->quantity = $request->quantity;
                
                StockMove::create([
                    'product_id' => $request->product_id,
                    'to_warehouse_id' => $request->warehouse_id,
                    'quantity' => abs($diff),
                    'type' => 'adjustment',
                    'reference' => $request->reference ?: 'Manual Adjustment',
                ]);
            } else {
                $stockLevel->quantity += $change;
                
                StockMove::create([
                    'product_id' => $request->product_id,
                    $request->type === 'incoming' ? 'to_warehouse_id' : 'from_warehouse_id' => $request->warehouse_id,
                    'quantity' => $request->quantity,
                    'type' => $request->type,
                    'reference' => $request->reference,
                ]);
            }

            $stockLevel->save();
        });

        return back()->with('success', 'Stock updated successfully.');
    }

    public function show(Product $inventory) // route inventory.show
    {
        $product = $inventory->load(['stockLevels.warehouse', 'stockMoves.fromWarehouse', 'stockMoves.toWarehouse']);
        return view('erp.inventory.show', compact('product'));
    }
}
