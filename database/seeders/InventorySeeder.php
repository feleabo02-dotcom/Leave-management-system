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
        $prodLaptop = Product::updateOrCreate(['code' => 'PROD-IT-001'], [
            'name' => 'MacBook Pro M3 16"', 
            'type' => 'stockable', 
            'price' => 2499, 
            'cost' => 1800, 
            'is_active' => true
        ]);
        
        $prodMonitor = Product::updateOrCreate(['code' => 'PROD-IT-002'], [
            'name' => 'Dell UltraSharp 27"', 
            'type' => 'stockable', 
            'price' => 599, 
            'cost' => 400, 
            'is_active' => true
        ]);

        $prodChair = Product::updateOrCreate(['code' => 'PROD-OF-001'], [
            'name' => 'Ergonomic Task Chair', 
            'type' => 'stockable', 
            'price' => 899, 
            'cost' => 450, 
            'is_active' => true
        ]);

        $prodComponent = Product::updateOrCreate(['code' => 'COMP-M3'], [
            'name' => 'M3 Chipset Unit', 
            'type' => 'stockable', 
            'price' => 0, 
            'cost' => 500, 
            'is_active' => true
        ]);

        // 3. Stock Levels
        StockLevel::updateOrCreate(['product_id' => $prodLaptop->id, 'warehouse_id' => $whMain->id], ['quantity' => 15]);
        StockLevel::updateOrCreate(['product_id' => $prodMonitor->id, 'warehouse_id' => $whMain->id], ['quantity' => 25]);
        StockLevel::updateOrCreate(['product_id' => $prodChair->id, 'warehouse_id' => $whMain->id], ['quantity' => 10]);
        StockLevel::updateOrCreate(['product_id' => $prodComponent->id, 'warehouse_id' => $whRaw->id], ['quantity' => 150]);

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
