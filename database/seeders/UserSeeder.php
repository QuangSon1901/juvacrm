<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_users')->insert([
            'id' => 2,
            'name' => 'Nguyễn Trung Hiếu',
            'birth_date' => '1992-02-02',
            'phone' => '0123456790',
            'email' => 'b@example.com',
            'address' => 'Hải Phòng',
            'gender' => 1,
            'cccd' => '987654321',
            'avatar' => NULL,
            'username' => 'user_b',
            'password' => '$2y$10$aRPzO.6MZlAyDPPtQcflU.YtDPE.McB9s1jfEhL2HU82xCiqoMIi6',
            'salary' => '0',
            'status' => 1,
            'is_active' => 1,
            'last_login' => NULL,
            'note' => NULL,
            'login_attempts' => NULL,
            'ended_at' => NULL,
            'created_at' => '2024-11-02 06:56:26',
            'updated_at' => '2025-04-25 07:49:22',
            'created_by' => NULL,
            'is_super_admin' => 1
        ]);
    }
}