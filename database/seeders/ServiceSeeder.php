<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            ['id' => 33, 'name' => 'Fatlay', 'category_id' => 1],
            ['id' => 34, 'name' => 'Combo Flatlay', 'category_id' => 1],
            ['id' => 35, 'name' => 'Combo F TMDT', 'category_id' => 1],
            ['id' => 36, 'name' => 'Treo xóa móc', 'category_id' => 1],
            ['id' => 37, 'name' => 'Combo 2D', 'category_id' => 1],
            ['id' => 38, 'name' => 'Combo 2D TMDT', 'category_id' => 1],
            ['id' => 39, 'name' => '3D', 'category_id' => 1],
            ['id' => 40, 'name' => 'Combo 3D', 'category_id' => 1],
            ['id' => 41, 'name' => 'Combo 3D TMDT', 'category_id' => 1],
            ['id' => 42, 'name' => 'Treo', 'category_id' => 1],
            ['id' => 43, 'name' => 'Combo Treo', 'category_id' => 1],
            ['id' => 44, 'name' => 'Combo T TMDT', 'category_id' => 1],
            ['id' => 45, 'name' => 'Concept có sẵn', 'category_id' => 1],
            ['id' => 46, 'name' => 'Concept không có sẵn', 'category_id' => 1],
            ['id' => 47, 'name' => 'Combo chụp nhiều từ 50 tấm', 'category_id' => 1],
            ['id' => 48, 'name' => 'Tách nền', 'category_id' => 1],
            ['id' => 49, 'name' => 'Chụp treo nhiều màu (tối đa 3 màu)', 'category_id' => 1],
            ['id' => 50, 'name' => 'Chụp treo từ màu thứ 4 trở lên', 'category_id' => 1],
            ['id' => 51, 'name' => 'Phụ kiện khác | Nền trắng', 'category_id' => 1],
            ['id' => 52, 'name' => 'Phụ kiện khác | Combo 5 Tấm', 'category_id' => 1],
            ['id' => 53, 'name' => 'Phụ kiện khác | Combo 9 tấm', 'category_id' => 1],
            ['id' => 54, 'name' => 'Phụ kiện khác | Concept ánh sáng + Decor sẵn', 'category_id' => 1],
            ['id' => 55, 'name' => 'Phụ kiện khác | Concept không có sẵn', 'category_id' => 1],
            ['id' => 56, 'name' => 'Ghép hình stu chụp', 'category_id' => 1],
            ['id' => 57, 'name' => 'Ghép poster/banner', 'category_id' => 1],
            ['id' => 58, 'name' => 'Chụp lookbook với mẫu', 'category_id' => 1],
            ['id' => 59, 'name' => 'Quay Video Lẻ', 'category_id' => 1],
            ['id' => 60, 'name' => 'Quay Video Tháng', 'category_id' => 1],
        ];

        foreach ($services as $service) {
            DB::table('tbl_services')->insert([
                'id' => $service['id'],
                'name' => $service['name'],
                'description' => NULL,
                'is_active' => 1,
                'created_at' => '2025-04-25 18:29:27',
                'updated_at' => '2025-04-25 18:29:27',
                'category_id' => $service['category_id']
            ]);
        }
    }
}