<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CurrencySeeder::class,
            CustomerClassSeeder::class,
            CustomerLeadSeeder::class,
            PaymentMethodSeeder::class,
            PermissionSeeder::class,
            ProductSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            SystemConfigSeeder::class,
            TaskConfigSeeder::class,
            TransactionCategorySeeder::class,
            UserSeeder::class,
        ]);
    }
}