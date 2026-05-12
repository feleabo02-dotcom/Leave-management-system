<?php

namespace Database\Seeders;

use App\Models\SalaryComponent;
use App\Models\SalaryStructure;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $structure = SalaryStructure::updateOrCreate(['name' => 'Standard Professional'], [
            'code' => 'STR/STD/001',
            'is_active' => true
        ]);

        SalaryComponent::updateOrCreate(['salary_structure_id' => $structure->id, 'code' => 'COMP/BASIC'], [
            'name' => 'Basic Salary',
            'type' => 'allowance', 
            'amount_type' => 'fixed', 
            'amount' => 5000
        ]);

        SalaryComponent::updateOrCreate(['salary_structure_id' => $structure->id, 'code' => 'COMP/HOUSING'], [
            'name' => 'Housing Allowance',
            'type' => 'allowance', 
            'amount_type' => 'percentage', 
            'amount' => 20
        ]);

        SalaryComponent::updateOrCreate(['salary_structure_id' => $structure->id, 'code' => 'COMP/TRANS'], [
            'name' => 'Transport Allowance',
            'type' => 'allowance', 
            'amount_type' => 'fixed', 
            'amount' => 500
        ]);

        SalaryComponent::updateOrCreate(['salary_structure_id' => $structure->id, 'code' => 'COMP/TAX'], [
            'name' => 'Tax Deduction',
            'type' => 'deduction', 
            'amount_type' => 'percentage', 
            'amount' => 15
        ]);
    }
}
