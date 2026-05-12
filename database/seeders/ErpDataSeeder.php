<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ErpDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AccountingSeeder::class,
            AssetSeeder::class,
            CrmSalesSeeder::class,
            InventorySeeder::class,
            ProcurementSeeder::class,
            ProjectSeeder::class,
            PayrollSeeder::class,
        ]);

        $this->command->info('✅ Master ERP Data Seeded Successfully.');
    }
}
