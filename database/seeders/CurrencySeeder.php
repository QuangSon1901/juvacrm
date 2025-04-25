<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_currencies')->insert([
            'id' => 1,
            'currency_code' => 'VND',
            'currency_name' => 'Việt Nam Đồng',
            'symbol' => 'đ',
            'is_active' => 1,
            'created_at' => '2025-04-25 18:19:42',
            'updated_at' => '2025-04-25 18:19:42'
        ]);
    }
}