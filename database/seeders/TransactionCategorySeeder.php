<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_transaction_categories')->insert([
            [
                'id' => 1,
                'type' => 0,
                'name' => 'Final Payment',
                'note' => 'Hạng mục cho Final Payment',
                'is_active' => 1,
                'created_at' => '2025-02-24 08:45:59',
                'updated_at' => '2025-02-24 08:45:59'
            ],
            [
                'id' => 2,
                'type' => 1,
                'name' => 'Deduction',
                'note' => 'Hạng mục cho Deduction',
                'is_active' => 1,
                'created_at' => '2025-02-24 09:43:19',
                'updated_at' => '2025-02-24 09:43:19'
            ],
            [
                'id' => 3,
                'type' => 0,
                'name' => 'Deposit',
                'note' => 'Hạng mục cho Deposit',
                'is_active' => 1,
                'created_at' => '2025-02-24 11:43:50',
                'updated_at' => '2025-02-24 11:43:50'
            ],
            [
                'id' => 4,
                'type' => 0,
                'name' => 'Bonus',
                'note' => 'Hạng mục cho Bonus',
                'is_active' => 1,
                'created_at' => '2025-03-09 08:12:45',
                'updated_at' => '2025-03-09 08:12:45'
            ]
        ]);
    }
}