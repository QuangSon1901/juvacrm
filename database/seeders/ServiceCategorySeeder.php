<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_service_categories')->insert([
            'id' => 1,
            'name' => 'Chụp ảnh sản phâm',
            'is_active' => 1,
            'created_at' => '2025-01-13 09:47:58',
            'updated_at' => '2025-01-13 09:47:58'
        ]);
    }
}