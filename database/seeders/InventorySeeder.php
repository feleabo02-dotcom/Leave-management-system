<?php

namespace Database\Seeders;

use App\Models\Bom;
use App\Models\BomLine;
use App\Models\ManufacturingOrder;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // 1. Warehouses
        $whMain = Warehouse::updateOrCreate(['code' => 'WH/MAIN'], ['name' => 'Main Warehouse', 'location' => 'Building A', 'is_active' => true]);
        $whRaw = Warehouse::updateOrCreate(['code' => 'WH/RAW'], ['name' => 'Raw Materials', 'location' => 'Building B', 'is_active' => true]);

        // 2. Products
        $prodLaptop = Product::updateOrCreate(['code' => 'PROD/001'], [
            'name' => 'MacBook Pro M3', 
            'type' => 'stockable', 
            'price' => 2500, 
            'cost' => 1800, 
            'is_active' => true
        ]);
        
        $prodComponent = Product::updateOrCreate(['code' => 'COMP/001'], [
            'name' => 'M3 Chipset', 
            'type' => 'stockable', 
            'price' => 0, 
            'cost' => 500, 
            'is_active' => true
        ]);

        // 3. Stock Levels
        StockLevel::updateOrCreate(['product_id' => $prodLaptop->id, 'warehouse_id' => $whMain->id], ['quantity' => 50]);
        StockLevel::updateOrCreate(['product_id' => $prodComponent->id, 'warehouse_id' => $whRaw->id], ['quantity' => 200]);

        // 4. BOM
        $bom = Bom::updateOrCreate(['code' => 'BOM/MBP/001'], [
            'product_id' => $prodLaptop->id,
            'quantity' => 1,
        ]);

        BomLine::updateOrCreate(['bom_id' => $bom->id, 'product_id' => $prodComponent->id], [
            'quantity' => 1,
        ]);

        // 5. Manufacturing Order
        if ($admin) {
            ManufacturingOrder::updateOrCreate(['code' => 'MO/2026/001'], [
                'product_id' => $prodLaptop->id,
                'bom_id' => $bom->id,
                'quantity' => 10,
                'status' => 'done',
                'warehouse_id' => $whMain->id,
                'created_by' => $admin->id,
            ]);
        }
    }
}
