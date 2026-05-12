<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProcurementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $vendor = Vendor::updateOrCreate(['code' => 'VEND/APPLE'], [
            'name' => 'Apple Inc.', 
            'email' => 'contact@apple.com',
            'phone' => '1-800-MY-APPLE', 
            'address' => 'One Apple Park Way, Cupertino', 
            'is_active' => true
        ]);

        if ($admin) {
            PurchaseOrder::updateOrCreate(['code' => 'PO/2026/001'], [
                'vendor_id' => $vendor->id,
                'date' => now(),
                'status' => 'received',
                'total_amount' => 50000,
                'created_by' => $admin->id,
            ]);
        }
    }
}
