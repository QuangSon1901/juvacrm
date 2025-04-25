<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerLeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_customer_lead')->insert([
            [
                'id' => 1,
                'name' => 'Email',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2024-11-02 08:01:29',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 2,
                'name' => 'Nhắn tin FB',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2024-11-02 08:01:29',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 3,
                'name' => 'Gọi điện',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2024-11-02 08:01:29',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 4,
                'name' => 'Gặp mặt trực tiếp',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2024-11-02 08:01:29',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 5,
                'name' => 'Nhắn tin Zalo',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-02-24 04:32:56',
                'color' => 'primary',
                'sort' => 7
            ],
            [
                'id' => 6,
                'name' => 'IG',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2024-11-02 08:01:29',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 7,
                'name' => 'Facebook',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2024-11-02 08:01:29',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 8,
                'name' => 'Zalo',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2024-11-02 08:01:29',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 9,
                'name' => 'Website',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-02-24 04:33:02',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 10,
                'name' => 'Chưa liên hệ',
                'description' => NULL,
                'type' => 2,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-01-22 12:27:16',
                'color' => 'danger',
                'sort' => 1
            ],
            [
                'id' => 11,
                'name' => 'Đang tiếp cận',
                'description' => NULL,
                'type' => 2,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-01-22 12:27:16',
                'color' => 'warning',
                'sort' => 2
            ],
            [
                'id' => 12,
                'name' => 'Quan tâm',
                'description' => NULL,
                'type' => 2,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-01-22 12:27:16',
                'color' => 'primary',
                'sort' => 3
            ],
            [
                'id' => 13,
                'name' => 'Đã liên hệ',
                'description' => NULL,
                'type' => 2,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-01-22 12:27:16',
                'color' => 'primary',
                'sort' => 4
            ],
            [
                'id' => 14,
                'name' => 'Đã chốt',
                'description' => NULL,
                'type' => 2,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-01-22 12:27:16',
                'color' => 'success',
                'sort' => 5
            ],
            [
                'id' => 15,
                'name' => 'Từ chối',
                'description' => NULL,
                'type' => 2,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-01-22 12:26:37',
                'color' => 'neutral',
                'sort' => 6
            ],
            [
                'id' => 16,
                'name' => 'Chưa xác định',
                'description' => NULL,
                'type' => 2,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:01:29',
                'updated_at' => '2025-01-22 12:26:37',
                'color' => 'neutral',
                'sort' => 7
            ]
        ]);
    }
}