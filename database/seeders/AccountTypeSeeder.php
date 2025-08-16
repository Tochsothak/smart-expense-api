<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
             [
                'name' => 'Cash',
                'code' => 'cash',
                'description'=> 'Cash Accounts',
                'active' => 1,
            ],
             [
                'name' => 'General Account',
                'code' => 'general',
                'description'=> 'General Accounts',
                'active' => 1,
            ],
             [
                'name' => 'Mobile Money',
                'code' => 'momo',
                'description'=> 'Mobile Money Accounts',
                'active' => 1,
            ],
             [
                'name' => 'Saving Account',
                'code' => 'saving-account',
                'description'=> 'Saving Accounts',
                'active' => 1,
            ],
             [
                'name' => 'Current Account',
                'code' => 'current-account',
                'description'=> 'Current Accounts',
                'active' => 1,
            ],
             [
                'name' => 'Investment Account',
                'code' => 'investment-account',
                'description'=> 'Investment Accounts',
                'active' => 1,
            ],
             [
                'name' => 'Insurance Account',
                'code' => 'insurance-account',
                'description'=> 'Insurance Accounts',
                'active' => 1,
            ],
             [
                'name' => 'Loan Account',
                'code' => 'loan',
                'description'=> 'Loan Accounts',
                'active' => 1,
            ],
             [
                'name' => 'Credit Card',
                'code' => 'credit-card',
                'description'=> 'Credit Card Accounts',
                'active' => 1,
            ],
        ];
        collect($types)->each(function ($type){
            AccountType::updateOrCreate(
                [
                    'code' => $type['code']
                ],
                $type
            );
        });
    }
}
