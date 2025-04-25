<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_customer_class')->insert([
            [
                'id' => 1,
                'name' => 'Mới',
                'description' => NULL,
                'is_active' => 1,
                'created_at' => '2024-11-02 07:52:53',
                'updated_at' => '2025-01-22 12:25:31',
                'color' => 'primary',
                'sort' => 1
            ],
            [
                'id' => 2,
                'name' => 'Lần đầu',
                'description' => NULL,
                'is_active' => 1,
                'created_at' => '2024-11-02 07:52:53',
                'updated_at' => '2025-01-22 12:25:37',
                'color' => 'success',
                'sort' => 2
            ],
            [
                'id' => 3,
                'name' => 'Cũ',
                'description' => NULL,
                'is_active' => 1,
                'created_at' => '2024-11-02 07:52:53',
                'updated_at' => '2025-01-22 12:25:37',
                'color' => 'warning',
                'sort' => 3
            ],
            [
                'id' => 4,
                'name' => 'Quen',
                'description' => NULL,
                'is_active' => 1,
                'created_at' => '2024-11-02 07:52:53',
                'updated_at' => '2025-01-22 12:25:37',
                'color' => 'primary',
                'sort' => 4
            ],
            [
                'id' => 5,
                'name' => 'Đối Tác',
                'description' => NULL,
                'is_active' => 1,
                'created_at' => '2024-11-02 07:52:53',
                'updated_at' => '2025-01-22 12:25:37',
                'color' => 'primary',
                'sort' => 5
            ]
        ]);
    }
}