<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Dashboard permissions
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'description' => 'Quyền xem dashboard', 'module' => 'Dashboard'],
            
            // Member permissions
            ['name' => 'View Member', 'slug' => 'view-member', 'description' => 'Quyền xem thành viên', 'module' => 'Member'],
            ['name' => 'Create Member', 'slug' => 'create-member', 'description' => 'Quyền tạo thành viên', 'module' => 'Member'],
            ['name' => 'Edit Member', 'slug' => 'edit-member', 'description' => 'Quyền chỉnh sửa thành viên', 'module' => 'Member'],
            ['name' => 'Delete Member', 'slug' => 'delete-member', 'description' => 'Quyền xóa thành viên', 'module' => 'Member'],
            
            // Team permissions
            ['name' => 'View Team', 'slug' => 'view-team', 'description' => 'Quyền xem nhóm', 'module' => 'Team'],
            ['name' => 'Create Team', 'slug' => 'create-team', 'description' => 'Quyền tạo nhóm', 'module' => 'Team'],
            ['name' => 'Edit Team', 'slug' => 'edit-team', 'description' => 'Quyền chỉnh sửa nhóm', 'module' => 'Team'],
            ['name' => 'Delete Team', 'slug' => 'delete-team', 'description' => 'Quyền xóa nhóm', 'module' => 'Team'],
            
            // Role permissions
            ['name' => 'View Role', 'slug' => 'view-role', 'description' => 'Quyền xem vai trò', 'module' => 'Role'],
            ['name' => 'Edit Role', 'slug' => 'edit-role', 'description' => 'Quyền phân quyền', 'module' => 'Role'],
            
            // Task permissions
            ['name' => 'View Task', 'slug' => 'view-task', 'description' => 'Quyền xem công việc', 'module' => 'Task'],
            ['name' => 'Create Task', 'slug' => 'create-task', 'description' => 'Quyền tạo công việc', 'module' => 'Task'],
            ['name' => 'Edit Task', 'slug' => 'edit-task', 'description' => 'Quyền chỉnh sửa công việc', 'module' => 'Task'],
            ['name' => 'Delete Task', 'slug' => 'delete-task', 'description' => 'Quyền xóa công việc', 'module' => 'Task'],
            ['name' => 'Assign Task', 'slug' => 'assign-task', 'description' => 'Quyền gán công việc', 'module' => 'Task'],
            
            // Customer permissions
            ['name' => 'View Customer', 'slug' => 'view-customer', 'description' => 'Quyền xem khách hàng', 'module' => 'Customer'],
            ['name' => 'Create Customer', 'slug' => 'create-customer', 'description' => 'Quyền tạo khách hàng', 'module' => 'Customer'],
            ['name' => 'Edit Customer', 'slug' => 'edit-customer', 'description' => 'Quyền chỉnh sửa khách hàng', 'module' => 'Customer'],
            ['name' => 'Delete Customer', 'slug' => 'delete-customer', 'description' => 'Quyền xóa khách hàng', 'module' => 'Customer'],
            ['name' => 'Support Customer', 'slug' => 'support-customer', 'description' => 'Quyền hỗ trợ khách hàng', 'module' => 'Customer'],
            
            // Contract permissions
            ['name' => 'View Contract', 'slug' => 'view-contract', 'description' => 'Quyền xem hợp đồng', 'module' => 'Contract'],
            ['name' => 'Create Contract', 'slug' => 'create-contract', 'description' => 'Quyền tạo hợp đồng', 'module' => 'Contract'],
            ['name' => 'Edit Contract', 'slug' => 'edit-contract', 'description' => 'Quyền chỉnh sửa hợp đồng', 'module' => 'Contract'],
            ['name' => 'Delete Contract', 'slug' => 'delete-contract', 'description' => 'Quyền xóa hợp đồng', 'module' => 'Contract'],
            ['name' => 'Approve Contract', 'slug' => 'approve-contract', 'description' => 'Quyền duyệt hợp đồng', 'module' => 'Contract'],
            
            // Timekeeping permissions
            ['name' => 'View Timekeeping', 'slug' => 'view-timekeeping', 'description' => 'Quyền xem chấm công', 'module' => 'Timekeeping'],
            ['name' => 'Edit Timekeeping', 'slug' => 'edit-timekeeping', 'description' => 'Quyền sửa chấm công', 'module' => 'Timekeeping'],
            ['name' => 'Approve Timekeeping', 'slug' => 'approve-timekeeping', 'description' => 'Quyền duyệt chấm công', 'module' => 'Timekeeping'],
            
            // Schedule permissions
            ['name' => 'View Schedule', 'slug' => 'view-schedule', 'description' => 'Quyền xem lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Create Schedule', 'slug' => 'create-schedule', 'description' => 'Quyền tạo lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Edit Schedule', 'slug' => 'edit-schedule', 'description' => 'Quyền sửa lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Approve Schedule', 'slug' => 'approve-schedule', 'description' => 'Quyền duyệt lịch làm việc', 'module' => 'Schedule'],
            
            // Salary permissions
            ['name' => 'View Salary', 'slug' => 'view-salary', 'description' => 'Quyền xem lương', 'module' => 'Salary'],
            ['name' => 'Edit Salary', 'slug' => 'edit-salary', 'description' => 'Quyền sửa lương', 'module' => 'Salary'],
            ['name' => 'Approve Salary', 'slug' => 'approve-salary', 'description' => 'Quyền duyệt lương', 'module' => 'Salary'],
            ['name' => 'Configure Salary', 'slug' => 'configure-salary', 'description' => 'Quyền cấu hình lương', 'module' => 'Salary'],
            
            // Accounting permissions
            ['name' => 'View Transaction', 'slug' => 'view-transaction', 'description' => 'Quyền xem giao dịch', 'module' => 'Accounting'],
            ['name' => 'Create Transaction', 'slug' => 'create-transaction', 'description' => 'Quyền tạo giao dịch', 'module' => 'Accounting'],
            ['name' => 'Edit Transaction', 'slug' => 'edit-transaction', 'description' => 'Quyền sửa giao dịch', 'module' => 'Accounting'],
            ['name' => 'Delete Transaction', 'slug' => 'delete-transaction', 'description' => 'Quyền xóa giao dịch', 'module' => 'Accounting'],
            ['name' => 'View Report', 'slug' => 'view-report', 'description' => 'Quyền xem báo cáo', 'module' => 'Accounting'],
            
            // Service permissions
            ['name' => 'View Service', 'slug' => 'view-service', 'description' => 'Quyền xem dịch vụ', 'module' => 'Service'],
            ['name' => 'Create Service', 'slug' => 'create-service', 'description' => 'Quyền tạo dịch vụ', 'module' => 'Service'],
            ['name' => 'Edit Service', 'slug' => 'edit-service', 'description' => 'Quyền sửa dịch vụ', 'module' => 'Service'],
            ['name' => 'Delete Service', 'slug' => 'delete-service', 'description' => 'Quyền xóa dịch vụ', 'module' => 'Service'],
            
            // Setting permissions
            ['name' => 'View Setting', 'slug' => 'view-setting', 'description' => 'Quyền xem cài đặt', 'module' => 'Setting'],
            ['name' => 'Edit Setting', 'slug' => 'edit-setting', 'description' => 'Quyền sửa cài đặt', 'module' => 'Setting'],

            // File Explorer permissions
            ['name' => 'View File Explorer', 'slug' => 'view-file-explorer', 'description' => 'Quyền xem quản lý file', 'module' => 'FileExplorer'],
            ['name' => 'Upload File', 'slug' => 'upload-file', 'description' => 'Quyền tải lên file', 'module' => 'FileExplorer'],
            ['name' => 'Delete File', 'slug' => 'delete-file', 'description' => 'Quyền xóa file', 'module' => 'FileExplorer'],

            // Activity Logs permissions
            ['name' => 'View Activity Logs', 'slug' => 'view-logs', 'description' => 'Quyền xem nhật ký hoạt động', 'module' => 'Logs'],

            // Profile permissions
            ['name' => 'View Profile', 'slug' => 'view-profile', 'description' => 'Quyền xem thông tin cá nhân', 'module' => 'Profile'],
            ['name' => 'Edit Profile', 'slug' => 'edit-profile', 'description' => 'Quyền chỉnh sửa thông tin cá nhân', 'module' => 'Profile'],

            // Bổ sung cho module Assets
            ['name' => 'View Assets', 'slug' => 'view-assets', 'description' => 'Quyền xem tài sản', 'module' => 'Assets'],
            ['name' => 'Manage Assets', 'slug' => 'manage-assets', 'description' => 'Quyền quản lý tài sản', 'module' => 'Assets'],
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            // Don't insert duplicates
            $exists = DB::table('tbl_permissions')
                ->where('slug', $permission['slug'])
                ->exists();
                
            if (!$exists) {
                DB::table('tbl_permissions')->insert([
                    'name' => $permission['name'],
                    'slug' => $permission['slug'],
                    'description' => $permission['description'],
                    'module' => $permission['module'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Default permissions for Owner/Admin role
        $adminLevelId = 1; // Assume level ID 1 is Admin/Owner
        $ownerDepartmentId = 1; // Assume department ID 1 is Owner department
        
        // Get all permission IDs
        $permissionIds = DB::table('tbl_permissions')->pluck('id')->toArray();
        
        // Assign all permissions to Owner/Admin
        foreach ($permissionIds as $permissionId) {
            // Don't insert duplicates
            $exists = DB::table('tbl_role_permissions')
                ->where('level_id', $adminLevelId)
                ->where('department_id', $ownerDepartmentId)
                ->where('permission_id', $permissionId)
                ->exists();
                
            if (!$exists) {
                DB::table('tbl_role_permissions')->insert([
                    'level_id' => $adminLevelId,
                    'department_id' => $ownerDepartmentId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}