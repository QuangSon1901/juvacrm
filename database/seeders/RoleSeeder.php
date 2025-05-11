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
            // Sales Manager permissions
            [
                'level_id' => 2,
                'department_id' => 2, // Sales department
                'permissions' => [
                    // Dashboard
                    'view-dashboard',
                    
                    // Customer management
                    'view-customer', 'create-customer', 'edit-customer', 'support-customer',
                    'view-customer-leads', 'manage-customer-leads', 'view-customer-support', 
                    'manage-customer-consultation', 'view-customer-appointment', 
                    'manage-customer-appointment', 'complete-customer-appointment',
                    
                    // Contract management
                    'view-contract', 'create-contract', 'edit-contract', 'manage-contract-services',
                    'manage-contract-payments', 'export-contract', 'create-contract-tasks',
                    
                    // Task management
                    'view-task', 'create-task', 'edit-task', 'assign-task', 'add-task-comment',
                    'add-task-feedback',
                    
                    // Team management
                    'view-team',
                    
                    // Schedule & timekeeping
                    'view-timekeeping', 'view-schedule', 'create-schedule', 'view-schedule-statistics',
                    'view-attendance-report',
                    
                    // Basic profile
                    'view-profile', 'edit-profile', 'view-my-salary', 'view-my-schedule',
                    'manage-my-schedule', 'view-my-timesheet', 'view-my-commission',
                    
                    // Notifications
                    'view-notifications', 'manage-notifications',
                ]
            ],
            
            // Technical Manager permissions
            [
                'level_id' => 2,
                'department_id' => 3, // Technical department
                'permissions' => [
                    // Dashboard
                    'view-dashboard',
                    
                    // Task management
                    'view-task', 'create-task', 'edit-task', 'assign-task', 'claim-task',
                    'add-task-comment', 'add-task-feedback', 'resolve-task-feedback',
                    'view-task-config', 'manage-task-config', 'manage-task-contributions',
                    'manage-task-missions',
                    
                    // Member & team management
                    'view-member', 'view-team', 'create-team', 'edit-team', 'add-team-member',
                    'remove-team-member',
                    
                    // Schedule & timekeeping
                    'view-timekeeping', 'edit-timekeeping', 'view-schedule', 'create-schedule', 
                    'approve-schedule', 'view-schedule-statistics', 'view-attendance-report',
                    
                    // Contract (limited view)
                    'view-contract',
                    
                    // Basic profile
                    'view-profile', 'edit-profile', 'view-my-salary', 'view-my-schedule',
                    'manage-my-schedule', 'view-my-timesheet',
                    
                    // File management
                    'view-file-explorer', 'upload-file', 'download-file',
                    
                    // Notifications
                    'view-notifications', 'manage-notifications',
                ]
            ],
            
            // Accounting Manager permissions
            [
                'level_id' => 2,
                'department_id' => 4, // Accounting department
                'permissions' => [
                    // Dashboard
                    'view-dashboard',
                    
                    // Contracts (financial aspects)
                    'view-contract', 'view-contract', 'manage-contract-payments',
                    
                    // Accounting
                    'view-transaction', 'create-transaction', 'edit-transaction', 'delete-transaction',
                    'view-deposit-receipt', 'create-deposit-receipt', 'edit-deposit-receipt',
                    'cancel-deposit-receipt', 'export-deposit-receipt', 'view-report', 'export-report',
                    'view-commission', 'process-commission', 'bulk-process-commission',
                    'view-transaction-category', 'manage-transaction-category',
                    
                    // Salary management
                    'view-salary', 'edit-salary', 'approve-salary', 'configure-salary',
                    'calculate-salary', 'process-salary', 'bulk-process-salary',
                    'view-salary-advances', 'manage-salary-advances',
                    
                    // Timekeeping (for salary calculation)
                    'view-timekeeping', 'edit-timekeeping', 'approve-timekeeping',
                    'view-attendance-report',
                    
                    // Basic settings
                    'view-setting', 'view-payment-method', 'manage-payment-method',
                    'view-currency', 'manage-currency',
                    
                    // Basic profile
                    'view-profile', 'edit-profile', 'view-my-salary', 'view-my-schedule',
                    'manage-my-schedule', 'view-my-timesheet',
                    
                    // Notifications
                    'view-notifications', 'manage-notifications',
                ]
            ],
            
            // Sales Team Leader permissions
            [
                'level_id' => 3,
                'department_id' => 2, // Sales department
                'permissions' => [
                    // Dashboard
                    'view-dashboard',
                    
                    // Customer management (more limited than manager)
                    'view-customer', 'create-customer', 'edit-customer', 'support-customer',
                    'view-customer-leads', 'view-customer-support', 'manage-customer-consultation',
                    'view-customer-appointment', 'manage-customer-appointment',
                    'complete-customer-appointment',
                    
                    // Contract management (more limited than manager)
                    'view-contract', 'create-contract', 'export-contract',
                    
                    // Task management (more limited than manager)
                    'view-task', 'create-task', 'edit-task', 'add-task-comment',
                    
                    // Basic profile
                    'view-profile', 'edit-profile', 'view-my-salary', 'view-my-schedule',
                    'manage-my-schedule', 'view-my-timesheet', 'view-my-commission',
                    
                    // Notifications
                    'view-notifications', 'manage-notifications',
                ]
            ],
            
            // Technical Team Leader permissions
            [
                'level_id' => 3,
                'department_id' => 3, // Technical department
                'permissions' => [
                    // Dashboard
                    'view-dashboard',
                    
                    // Task management
                    'view-task', 'create-task', 'edit-task', 'claim-task', 'add-task-comment',
                    'add-task-feedback', 'resolve-task-feedback', 'view-task-config',
                    'manage-task-contributions',
                    
                    // Member & team management (limited)
                    'view-member', 'view-team',
                    
                    // Basic profile
                    'view-profile', 'edit-profile', 'view-my-salary', 'view-my-schedule',
                    'manage-my-schedule', 'view-my-timesheet',
                    
                    // File management
                    'view-file-explorer', 'upload-file', 'download-file',
                    
                    // Notifications
                    'view-notifications', 'manage-notifications',
                ]
            ],
            
            // Sales Staff permissions
            [
                'level_id' => 4,
                'department_id' => 2, // Sales department
                'permissions' => [
                    // Dashboard
                    'view-dashboard',
                    
                    // Customer management (basic)
                    'view-customer', 'create-customer', 'support-customer',
                    'view-customer-leads', 'view-customer-support',
                    'view-customer-appointment', 'complete-customer-appointment',
                    
                    // Contract management (very limited)
                    'view-contract',
                    
                    // Task management (basic)
                    'view-task', 'add-task-comment',
                    
                    // Basic profile
                    'view-profile', 'edit-profile', 'view-my-salary', 'view-my-schedule',
                    'manage-my-schedule', 'view-my-timesheet', 'view-my-commission',
                    
                    // Notifications
                    'view-notifications', 'manage-notifications',
                ]
            ],
            
            // Technical Staff permissions
            [
                'level_id' => 4,
                'department_id' => 3, // Technical department
                'permissions' => [
                    // Dashboard
                    'view-dashboard',
                    
                    // Task management (basic)
                    'view-task', 'claim-task', 'add-task-comment', 'add-task-feedback',
                    'resolve-task-feedback', 'manage-task-contributions',
                    
                    // Basic profile
                    'view-profile', 'edit-profile', 'view-my-salary', 'view-my-schedule',
                    'manage-my-schedule', 'view-my-timesheet',
                    
                    // File management (basic)
                    'view-file-explorer', 'upload-file', 'download-file',
                    
                    // Notifications
                    'view-notifications', 'manage-notifications',
                ]
            ],
            
            // Accounting Staff permissions
            [
                'level_id' => 4,
                'department_id' => 4, // Accounting department
                'permissions' => [
                    // Dashboard
                    'view-dashboard',
                    
                    // Accounting (basic)
                    'view-transaction', 'view-deposit-receipt', 'view-report',
                    'view-commission', 'view-transaction-category',
                    
                    // Salary management (limited)
                    'view-salary',
                    
                    // Basic profile
                    'view-profile', 'edit-profile', 'view-my-salary', 'view-my-schedule',
                    'manage-my-schedule', 'view-my-timesheet',
                    
                    // Notifications
                    'view-notifications', 'manage-notifications',
                ]
            ],
            
            // Intern permissions (all departments get the same basic permissions)
            [
                'level_id' => 5,
                'department_id' => 2, // Sales department
                'permissions' => [
                    'view-dashboard', 'view-profile', 'edit-profile', 'view-my-salary',
                    'view-my-schedule', 'manage-my-schedule', 'view-my-timesheet',
                    'view-notifications', 'manage-notifications',
                ]
            ],
            [
                'level_id' => 5,
                'department_id' => 3, // Technical department
                'permissions' => [
                    'view-dashboard', 'view-profile', 'edit-profile', 'view-my-salary',
                    'view-my-schedule', 'manage-my-schedule', 'view-my-timesheet',
                    'view-notifications', 'manage-notifications', 'view-file-explorer',
                    'upload-file', 'download-file',
                ]
            ],
            [
                'level_id' => 5,
                'department_id' => 4, // Accounting department
                'permissions' => [
                    'view-dashboard', 'view-profile', 'edit-profile', 'view-my-salary',
                    'view-my-schedule', 'manage-my-schedule', 'view-my-timesheet',
                    'view-notifications', 'manage-notifications',
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