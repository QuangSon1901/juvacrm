<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_task_config')->insert([
            [
                'id' => 1,
                'name' => 'Chưa bắt đầu',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-10 07:57:26',
                'color' => 'warning',
                'sort' => 1
            ],
            [
                'id' => 2,
                'name' => 'Đang chờ',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-10 07:57:26',
                'color' => 'primary',
                'sort' => 2
            ],
            [
                'id' => 3,
                'name' => 'Đang tiến hành',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-19 04:37:21',
                'color' => 'info',
                'sort' => 3
            ],
            [
                'id' => 4,
                'name' => 'Hoàn thành',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-10 07:57:26',
                'color' => 'success',
                'sort' => 4
            ],
            [
                'id' => 5,
                'name' => 'Đã huỷ',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-09 09:49:23',
                'color' => 'danger',
                'sort' => 5
            ],
            [
                'id' => 6,
                'name' => 'Quá hạn',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-10 07:57:31',
                'color' => 'danger',
                'sort' => 6
            ],
            [
                'id' => 7,
                'name' => 'Yêu cầu chỉnh sửa',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2025-03-15 18:49:26',
                'updated_at' => '2025-03-19 03:07:56',
                'color' => 'info',
                'sort' => 7
            ],
            [
                'id' => 8,
                'name' => 'Đã kết thúc',
                'description' => NULL,
                'type' => 1,
                'is_active' => 1,
                'created_at' => '2025-03-19 03:07:56',
                'updated_at' => '2025-03-19 03:07:59',
                'color' => 'neutral',
                'sort' => 8
            ],
            [
                'id' => 14,
                'name' => 'Chưa gặp trường hợp này',
                'description' => NULL,
                'type' => 2,
                'is_active' => 1,
                'created_at' => '2025-01-17 09:22:44',
                'updated_at' => '2025-01-17 10:09:54',
                'color' => 'neutral',
                'sort' => 1
            ],
            [
                'id' => 16,
                'name' => 'Vấn đề nan giải',
                'description' => NULL,
                'type' => 2,
                'is_active' => 0,
                'created_at' => '2025-01-17 09:59:18',
                'updated_at' => '2025-01-17 10:11:14',
                'color' => 'neutral',
                'sort' => 2
            ],
            [
                'id' => 17,
                'name' => 'Chưa Quay',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-09 09:47:22',
                'color' => 'danger',
                'sort' => 2
            ],
            [
                'id' => 18,
                'name' => 'Chưa Sửa FB',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-09 09:47:22',
                'color' => 'danger',
                'sort' => 3
            ],
            [
                'id' => 19,
                'name' => 'Chờ FB',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-09 09:47:22',
                'color' => 'primary',
                'sort' => 4
            ],
            [
                'id' => 20,
                'name' => 'Gửi Demo',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-09 09:47:22',
                'color' => 'primary',
                'sort' => 5
            ],
            [
                'id' => 21,
                'name' => 'Khách Chốt',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-09 09:47:22',
                'color' => 'success',
                'sort' => 6
            ],
            [
                'id' => 22,
                'name' => 'Chụp Xong',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-09 09:47:22',
                'color' => 'gray',
                'sort' => 7
            ],
            [
                'id' => 23,
                'name' => 'Chưa Chụp',
                'description' => NULL,
                'type' => 0,
                'is_active' => 1,
                'created_at' => '2024-11-02 08:13:21',
                'updated_at' => '2025-03-09 09:47:22',
                'color' => 'danger',
                'sort' => 1
            ]
        ]);
    }
}