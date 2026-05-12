<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Journal;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Chart of Accounts
        $accounts = [
            ['code' => '101000', 'name' => 'Main Bank Account', 'type' => 'bank'],
            ['code' => '102000', 'name' => 'Petty Cash', 'type' => 'cash'],
            ['code' => '110000', 'name' => 'Accounts Receivable', 'type' => 'receivable'],
            ['code' => '210000', 'name' => 'Accounts Payable', 'type' => 'payable'],
            ['code' => '400000', 'name' => 'Product Sales', 'type' => 'income'],
            ['code' => '410000', 'name' => 'Service Income', 'type' => 'income'],
            ['code' => '500000', 'name' => 'Cost of Goods Sold', 'type' => 'expense'],
            ['code' => '510000', 'name' => 'Salary Expense', 'type' => 'expense'],
            ['code' => '520000', 'name' => 'Office Rent', 'type' => 'expense'],
            ['code' => '300000', 'name' => 'Retained Earnings', 'type' => 'equity'],
        ];

        foreach ($accounts as $acc) {
            Account::updateOrCreate(['code' => $acc['code']], $acc);
        }

        // 2. Journals
        $journals = [
            ['code' => 'INV', 'name' => 'Customer Invoices', 'type' => 'sale'],
            ['code' => 'BILL', 'name' => 'Vendor Bills', 'type' => 'purchase'],
            ['code' => 'BNK1', 'name' => 'Bank Operations', 'type' => 'bank'],
            ['code' => 'CSH1', 'name' => 'Cash Operations', 'type' => 'cash'],
            ['code' => 'GEN', 'name' => 'General Journal', 'type' => 'general'],
        ];

        foreach ($journals as $j) {
            Journal::updateOrCreate(['code' => $j['code']], $j);
        }
    }
}
