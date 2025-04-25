<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_system_config')->insert([
            [
                'id' => 1,
                'config_key' => 'checkin_time',
                'config_value' => '08:00',
                'description' => 'Giờ bắt đầu làm việc',
                'is_active' => 1,
                'created_at' => '2024-11-02 06:55:06',
                'updated_at' => '2024-11-02 06:55:06'
            ],
            [
                'id' => 2,
                'config_key' => 'checkout_time',
                'config_value' => '17:00',
                'description' => 'Giờ kết thúc làm việc',
                'is_active' => 1,
                'created_at' => '2024-11-02 06:55:06',
                'updated_at' => '2024-11-02 06:55:06'
            ],
            [
                'id' => 3,
                'config_key' => 'work_days',
                'config_value' => 'Mon-Fri',
                'description' => 'Các ngày đi làm trong tuần',
                'is_active' => 1,
                'created_at' => '2024-11-02 06:55:06',
                'updated_at' => '2024-11-02 06:55:06'
            ],
            [
                'id' => 4,
                'config_key' => 'break_time',
                'config_value' => '12:00-13:00',
                'description' => 'Thời gian nghỉ trưa',
                'is_active' => 1,
                'created_at' => '2024-11-02 06:55:06',
                'updated_at' => '2024-11-02 06:55:06'
            ],
            [
                'id' => 5,
                'config_key' => 'min_working_hours',
                'config_value' => '8',
                'description' => 'Số giờ làm việc tối thiểu mỗi ngày',
                'is_active' => 1,
                'created_at' => '2024-11-02 06:55:06',
                'updated_at' => '2024-11-02 06:55:06'
            ],
            [
                'id' => 6,
                'config_key' => 'annual_leave_max',
                'config_value' => '12',
                'description' => 'Số ngày nghỉ phép tối đa mỗi năm',
                'is_active' => 1,
                'created_at' => '2024-11-02 06:55:06',
                'updated_at' => '2024-11-02 06:55:06'
            ],
            [
                'id' => 7,
                'config_key' => 'contract_commission_percentage',
                'config_value' => '5',
                'description' => 'Phần trăm hoa hồng cho nhân viên khi hoàn tất hợp đồng',
                'is_active' => 1,
                'created_at' => '2025-04-25 16:51:30',
                'updated_at' => '2025-04-25 16:51:30'
            ]
        ]);
    }
}