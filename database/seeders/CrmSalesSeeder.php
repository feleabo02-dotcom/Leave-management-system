<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Opportunity;
use Illuminate\Database\Seeder;

class CrmSalesSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Global Tech Solutions',
                'email' => 'contact@globaltech.com',
                'phone' => '+1 555-0123',
                'address' => '789 Innovation Dr, San Jose, CA',
                'company' => 'Global Tech Solutions',
                'type' => 'customer',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@gmail.com',
                'phone' => '+1 555-0456',
                'address' => '123 Maple St, Seattle, WA',
                'type' => 'customer',
            ],
        ];

        foreach ($customers as $custData) {
            $customer = Customer::updateOrCreate(['email' => $custData['email']], $custData);

            // Create an opportunity for the company
            if (isset($custData['company'])) {
                Opportunity::updateOrCreate(['title' => 'Software License Renewal'], [
                    'customer_id' => $customer->id,
                    'expected_revenue' => 50000,
                    'probability' => 70,
                    'stage' => 'proposition',
                    'closing_date' => now()->addMonths(1),
                ]);
            }
        }
    }
}
