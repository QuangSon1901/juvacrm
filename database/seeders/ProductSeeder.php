<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_products')->insert([
            [
                'id' => 1,
                'name' => 'Áo Thun',
                'is_active' => 1,
                'created_at' => '2025-03-08 16:07:18',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 2,
                'name' => 'Áo Khoác, somi, quần,.. đồ nam',
                'is_active' => 1,
                'created_at' => '2025-03-08 16:07:18',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 3,
                'name' => 'Áo Khoác, somi, quần,.. đồ nữ',
                'is_active' => 1,
                'created_at' => '2025-03-08 16:07:18',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 4,
                'name' => 'Đồ thiết kế',
                'is_active' => 1,
                'created_at' => '2025-04-25 18:24:19',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 5,
                'name' => 'Đồ trẻ em',
                'is_active' => 1,
                'created_at' => '2025-04-25 18:24:19',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 6,
                'name' => 'Đồ lót nam nữ',
                'is_active' => 1,
                'created_at' => '2025-04-25 18:24:19',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 7,
                'name' => 'Đồ Bộ',
                'is_active' => 1,
                'created_at' => '2025-04-25 18:24:19',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 8,
                'name' => 'Túi, mũ, nơ, giày...',
                'is_active' => 1,
                'created_at' => '2025-04-25 18:24:19',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 9,
                'name' => 'Nước hoa, trang sức',
                'is_active' => 1,
                'created_at' => '2025-04-25 18:24:19',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 10,
                'name' => 'Bình giữ nhiệt',
                'is_active' => 1,
                'created_at' => '2025-04-25 18:24:19',
                'updated_at' => '2025-04-25 18:24:19'
            ],
            [
                'id' => 11,
                'name' => 'Đồ ăn',
                'is_active' => 1,
                'created_at' => '2025-04-25 18:24:19',
                'updated_at' => '2025-04-25 18:24:19'
            ]
        ]);
    }
}