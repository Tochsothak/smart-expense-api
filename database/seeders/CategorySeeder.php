<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     $categories = [
    [
        'name' => 'Salary & Wages',
        'code' => 'salary-wages',
        'description' => 'Employment income and wages',
        'colour_code' => '0xFF4CAF50',
        'icon' => 'work',
        'active' => 1,
    ],
    [
        'name' => 'Business',
        'code' => 'business',
        'description' => 'Business income and expenses',
        'colour_code' => '0xFF2196F3',
        'icon' => 'business',
        'active' => 1,
    ],
    [
        'name' => 'Investment',
        'code' => 'investment',
        'description' => 'Investment returns and investment purchases',
        'colour_code' => '0xFFFF9800',
        'icon' => 'trending_up',
        'active' => 1,
    ],
    [
        'name' => 'Food & Dining',
        'code' => 'food-dining',
        'description' => 'Groceries, restaurants, and dining',
        'colour_code' => '0xFFF44336',
        'icon' => 'restaurant',
        'active' => 1,
    ],
    [
        'name' => 'Transportation',
        'code' => 'transportation',
        'description' => 'Fuel, public transport, taxi, maintenance',
        'colour_code' => '0xFFFF5722',
        'icon' => 'directions_car',
        'active' => 1,
    ],
    [
        'name' => 'Housing',
        'code' => 'housing',
        'description' => 'Rent, mortgage, utilities, maintenance',
        'colour_code' => '0xFF3F51B5',
        'icon' => 'home',
        'active' => 1,
    ],
    [
        'name' => 'Healthcare',
        'code' => 'healthcare',
        'description' => 'Medical expenses and health insurance',
        'colour_code' => '0xFFE91E63',
        'icon' => 'medical_services',
        'active' => 1,
    ],
    [
        'name' => 'Education',
        'code' => 'education',
        'description' => 'School fees, books, courses, education income',
        'colour_code' => '0xFF009688',
        'icon' => 'school',
        'active' => 1,
    ],
    [
        'name' => 'Entertainment',
        'code' => 'entertainment',
        'description' => 'Movies, games, hobbies, subscriptions',
        'colour_code' => '0xFF673AB7',
        'icon' => 'play_circle',
        'active' => 1,
    ],
    [
        'name' => 'Shopping',
        'code' => 'shopping',
        'description' => 'Clothing, electronics, personal items',
        'colour_code' => '0xFFFF9800',
        'icon' => 'shopping_bag',
        'active' => 1,
    ],
    [
        'name' => 'Utilities',
        'code' => 'utilities',
        'description' => 'Electricity, water, internet, phone bills',
        'colour_code' => '0xFF795548',
        'icon' => 'receipt_long',
        'active' => 1,
    ],
    [
        'name' => 'Insurance',
        'code' => 'insurance',
        'description' => 'Life, health, car, property insurance',
        'colour_code' => '0xFF607D8B',
        'icon' => 'shield',
        'active' => 1,
    ],
    [
        'name' => 'Savings & Investment',
        'code' => 'savings-investment',
        'description' => 'Money transfers to savings and investments',
        'colour_code' => '0xFF4CAF50',
        'icon' => 'savings',
        'active' => 1,
    ],
    [
        'name' => 'Debt & Loans',
        'code' => 'debt-loans',
        'description' => 'Loan payments and debt management',
        'colour_code' => '0xFFF44336',
        'icon' => 'credit_card',
        'active' => 1,
    ],
    [
        'name' => 'Travel',
        'code' => 'travel',
        'description' => 'Vacation, business trips, accommodation',
        'colour_code' => '0xFF00BCD4',
        'icon' => 'flight',
        'active' => 1,
    ],
    [
        'name' => 'Personal Care',
        'code' => 'personal-care',
        'description' => 'Haircut, cosmetics, gym, spa',
        'colour_code' => '0xFFE91E63',
        'icon' => 'favorite',
        'active' => 1,
    ],
    [
        'name' => 'Gifts & Donations',
        'code' => 'gifts-donations',
        'description' => 'Gifts, charity, religious donations',
        'colour_code' => '0xFF9C27B0',
        'icon' => 'card_giftcard',
        'active' => 1,
    ],
    [
        'name' => 'Freelance',
        'code' => 'freelance',
        'description' => 'Freelance work and related expenses',
        'colour_code' => '0xFF9C27B0',
        'icon' => 'laptop_mac',
        'active' => 1,
    ],
    [
        'name' => 'Rental',
        'code' => 'rental',
        'description' => 'Rental income and property expenses',
        'colour_code' => '0xFF795548',
        'icon' => 'home_work',
        'active' => 1,
    ],
    [
        'name' => 'Other',
        'code' => 'other',
        'description' => 'Miscellaneous income and expenses',
        'colour_code' => '0xFF757575',
        'icon' => 'more_horiz',
        'active' => 1,
    ],
    [
        'name' => 'Childcare',
        'code' => 'childcare',
        'description' => 'Daycare, babysitters, school supplies for kids',
        'colour_code' => '0xFFFFB300',
        'icon' => 'child_care',
        'active' => 1,
    ],
    [
        'name' => 'Pets',
        'code' => 'pets',
        'description' => 'Pet food, vet bills, grooming',
        'colour_code' => '0xFFFF7043',
        'icon' => 'pets',
        'active' => 1,
    ],
    [
        'name' => 'Taxes',
        'code' => 'taxes',
        'description' => 'Government and business tax payments',
        'colour_code' => '0xFF8D6E63',
        'icon' => 'description',
        'active' => 1,
    ],
    [
        'name' => 'Subscriptions',
        'code' => 'subscriptions',
        'description' => 'Netflix, Spotify, SaaS tools, recurring services',
        'colour_code' => '0xFF7C4DFF',
        'icon' => 'repeat',
        'active' => 1,
    ],
    [
        'name' => 'Bank Fees',
        'code' => 'bank-fees',
        'description' => 'ATM charges, service fees, overdraft penalties',
        'colour_code' => '0xFF546E7A',
        'icon' => 'attach_money',
        'active' => 1,
    ],
    [
        'name' => 'Legal & Professional',
        'code' => 'legal-professional',
        'description' => 'Lawyers, consultants, professional services',
        'colour_code' => '0xFF37474F',
        'icon' => 'gavel',
        'active' => 1,
    ],
    [
        'name' => 'Events & Celebrations',
        'code' => 'events-celebrations',
        'description' => 'Weddings, birthdays, parties, ceremonies',
        'colour_code' => '0xFFFF4081',
        'icon' => 'event',
        'active' => 1,
    ],
    [
        'name' => 'Repairs & Maintenance',
        'code' => 'repairs-maintenance',
        'description' => 'House or vehicle repairs, maintenance costs',
        'colour_code' => '0xFF455A64',
        'icon' => 'build',
        'active' => 1,
    ],
    [
        'name' => 'Groceries',
        'code' => 'groceries',
        'description' => 'Home food supplies, supermarkets',
        'colour_code' => '0xFFA5D6A7',
        'icon' => 'shopping_cart',
        'active' => 1,
    ],
    [
        'name' => 'Emergency Fund',
        'code' => 'emergency-fund',
        'description' => 'Unexpected expenses or emergency savings',
        'colour_code' => '0xFFEF5350',
        'icon' => 'warning',
        'active' => 1,
    ],
];


        Collect($categories)->each(function ($category){
            Category::updateOrCreate(
                ['code'=>$category['code']],
                $category,
            );
        });

    }
}
