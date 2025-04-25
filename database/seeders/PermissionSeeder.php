<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            'dashboard' => ['view'],
            'member' => ['view', 'create', 'edit', 'delete'],
            'team' => ['view', 'create', 'edit', 'delete'],
            'role' => ['view', 'edit'],
            'task' => ['view', 'create', 'edit', 'delete', 'assign'],
            'customer' => ['view', 'create', 'edit', 'delete', 'support'],
            'contract' => ['view', 'create', 'edit', 'delete', 'approve'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'slug' => "$action-$module"
                ], [
                    'name' => ucfirst($action) . ' ' . ucfirst($module),
                    'description' => 'Quyền ' . strtolower($action) . ' ' . strtolower($module),
                    'module' => ucfirst($module),
                ]);
            }
        }
    }
}