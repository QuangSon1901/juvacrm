<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default levels/roles
        $levels = [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Manager'],
            ['id' => 3, 'name' => 'Team Leader'],
            ['id' => 4, 'name' => 'Staff'],
            ['id' => 5, 'name' => 'Intern'],
        ];

        // Insert levels if they don't exist
        foreach ($levels as $level) {
            $exists = DB::table('tbl_levels')->where('id', $level['id'])->exists();
            
            if (!$exists) {
                DB::table('tbl_levels')->insert([
                    'id' => $level['id'],
                    'name' => $level['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Default departments
        $departments = [
            ['id' => 1, 'name' => 'Owner', 'keyword' => 'OWNER', 'note' => 'Chủ doanh nghiệp', 'is_active' => 1],
            ['id' => 2, 'name' => 'Sale', 'keyword' => 'SALE', 'note' => 'Phòng kinh doanh', 'is_active' => 1],
            ['id' => 3, 'name' => 'Technical', 'keyword' => 'TECHNICAL', 'note' => 'Phòng kỹ thuật', 'is_active' => 1],
            ['id' => 4, 'name' => 'Accounting', 'keyword' => 'ACCOUNTING', 'note' => 'Phòng kế toán', 'is_active' => 1],
        ];
        
        // Insert departments if they don't exist
        foreach ($departments as $department) {
            $exists = DB::table('tbl_departments')->where('id', $department['id'])->exists();
            
            if (!$exists) {
                DB::table('tbl_departments')->insert([
                    'id' => $department['id'],
                    'name' => $department['name'],
                    'keyword' => $department['keyword'],
                    'note' => $department['note'],
                    'is_active' => $department['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Set up default role permissions for various roles
        $rolePermissions = [
            // Manager role permissions
            [
                'level_id' => 2,
                'department_id' => 2, // Sales department
                'permissions' => [
                    'view-dashboard', 'view-customer', 'create-customer', 'edit-customer', 
                    'support-customer', 'view-contract', 'create-contract', 'edit-contract',
                    'view-task', 'create-task', 'edit-task', 'assign-task',
                    'view-timekeeping', 'view-schedule', 'create-schedule',
                ]
            ],
            [
                'level_id' => 2,
                'department_id' => 3, // Technical department
                'permissions' => [
                    'view-dashboard', 'view-task', 'create-task', 'edit-task', 'assign-task',
                    'view-member', 'view-team', 'view-timekeeping', 'view-schedule', 'create-schedule',
                ]
            ],
            [
                'level_id' => 2,
                'department_id' => 4, // Accounting department
                'permissions' => [
                    'view-dashboard', 'view-contract', 'view-transaction', 'create-transaction',
                    'edit-transaction', 'view-report', 'view-salary', 'edit-salary', 'approve-salary',
                    'configure-salary', 'view-timekeeping', 'edit-timekeeping'
                ]
            ],
            
            // Team Leader permissions
            [
                'level_id' => 3,
                'department_id' => 2, // Sales team
                'permissions' => [
                    'view-dashboard', 'view-customer', 'create-customer', 'edit-customer', 
                    'support-customer', 'view-contract', 'create-contract',
                    'view-task', 'create-task', 'edit-task',
                ]
            ],
            [
                'level_id' => 3,
                'department_id' => 3, // Technical team
                'permissions' => [
                    'view-dashboard', 'view-task', 'create-task', 'edit-task',
                    'view-member', 'view-team',
                ]
            ],
            
            // Staff permissions
            [
                'level_id' => 4,
                'department_id' => 2, // Sales staff
                'permissions' => [
                    'view-dashboard', 'view-customer', 'create-customer', 
                    'support-customer', 'view-contract',
                    'view-task',
                ]
            ],
            [
                'level_id' => 4,
                'department_id' => 3, // Technical staff
                'permissions' => [
                    'view-dashboard', 'view-task',
                ]
            ],
            [
                'level_id' => 4,
                'department_id' => 4, // Accounting staff
                'permissions' => [
                    'view-dashboard', 'view-transaction', 
                    'view-report', 'view-salary',
                ]
            ],
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $rolePermission) {
            $levelId = $rolePermission['level_id'];
            $departmentId = $rolePermission['department_id'];
            
            foreach ($rolePermission['permissions'] as $permissionSlug) {
                // Get the permission ID
                $permission = DB::table('tbl_permissions')
                    ->where('slug', $permissionSlug)
                    ->first();
                
                if ($permission) {
                    // Check if this role already has this permission
                    $exists = DB::table('tbl_role_permissions')
                        ->where('level_id', $levelId)
                        ->where('department_id', $departmentId)
                        ->where('permission_id', $permission->id)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('tbl_role_permissions')->insert([
                            'level_id' => $levelId,
                            'department_id' => $departmentId,
                            'permission_id' => $permission->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}