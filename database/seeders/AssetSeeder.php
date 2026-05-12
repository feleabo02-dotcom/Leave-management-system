<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'CAT/IT', 'name' => 'IT Equipment', 'description' => 'Laptops, Servers, and Networking'],
            ['code' => 'CAT/OFF', 'name' => 'Office Furniture', 'description' => 'Desks, Chairs, and Cabinets'],
            ['code' => 'CAT/VEH', 'name' => 'Vehicles', 'description' => 'Company cars and delivery vans'],
            ['code' => 'CAT/MAC', 'name' => 'Machinery', 'description' => 'Manufacturing plant equipment'],
        ];

        foreach ($categories as $cat) {
            $category = AssetCategory::updateOrCreate(['code' => $cat['code']], $cat);

            // Create some assets for each category
            if ($cat['code'] === 'CAT/IT') {
                Asset::updateOrCreate(['code' => 'AST/IT/001'], [
                    'name' => 'MacBook Pro M3',
                    'asset_category_id' => $category->id,
                    'purchase_date' => now()->subMonths(6),
                    'purchase_cost' => 2500,
                    'status' => 'available',
                ]);
            }
        }
    }
}
