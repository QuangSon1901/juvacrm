<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_payment_methods')->insert([
            [
                'id' => 1,
                'name' => 'Tiền mặt',
                'description' => '',
                'is_active' => 1,
                'created_at' => '2024-11-02 07:08:55',
                'updated_at' => '2024-11-02 07:08:55'
            ],
            [
                'id' => 2,
                'name' => 'Credit Card',
                'description' => '',
                'is_active' => 1,
                'created_at' => '2024-11-02 07:08:55',
                'updated_at' => '2024-11-02 07:08:55'
            ],
            [
                'id' => 3,
                'name' => 'Chuyển khoản ngân hàng',
                'description' => '',
                'is_active' => 1,
                'created_at' => '2024-11-02 07:08:55',
                'updated_at' => '2024-11-02 07:08:55'
            ]
        ]);
    }
}