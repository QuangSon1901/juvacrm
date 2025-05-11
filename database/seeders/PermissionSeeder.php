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
            ['name' => 'Reset Member Password', 'slug' => 'reset-member-password', 'description' => 'Quyền đặt lại mật khẩu thành viên', 'module' => 'Member'],
            ['name' => 'Lock Member Account', 'slug' => 'lock-member-account', 'description' => 'Quyền khóa tài khoản thành viên', 'module' => 'Member'],
            
            // Team permissions
            ['name' => 'View Team', 'slug' => 'view-team', 'description' => 'Quyền xem nhóm', 'module' => 'Team'],
            ['name' => 'Create Team', 'slug' => 'create-team', 'description' => 'Quyền tạo nhóm', 'module' => 'Team'],
            ['name' => 'Edit Team', 'slug' => 'edit-team', 'description' => 'Quyền chỉnh sửa nhóm', 'module' => 'Team'],
            ['name' => 'Delete Team', 'slug' => 'delete-team', 'description' => 'Quyền xóa nhóm', 'module' => 'Team'],
            ['name' => 'Add Team Member', 'slug' => 'add-team-member', 'description' => 'Quyền thêm thành viên vào nhóm', 'module' => 'Team'],
            ['name' => 'Remove Team Member', 'slug' => 'remove-team-member', 'description' => 'Quyền xóa thành viên khỏi nhóm', 'module' => 'Team'],
            
            // Role permissions
            ['name' => 'View Role', 'slug' => 'view-role', 'description' => 'Quyền xem vai trò', 'module' => 'Role'],
            ['name' => 'Edit Role', 'slug' => 'edit-role', 'description' => 'Quyền phân quyền', 'module' => 'Role'],
            ['name' => 'Assign Permissions', 'slug' => 'assign-permissions', 'description' => 'Quyền gán quyền cho vai trò', 'module' => 'Role'],
            
            // Task permissions
            ['name' => 'View Task', 'slug' => 'view-task', 'description' => 'Quyền xem công việc', 'module' => 'Task'],
            ['name' => 'Create Task', 'slug' => 'create-task', 'description' => 'Quyền tạo công việc', 'module' => 'Task'],
            ['name' => 'Edit Task', 'slug' => 'edit-task', 'description' => 'Quyền chỉnh sửa công việc', 'module' => 'Task'],
            ['name' => 'Delete Task', 'slug' => 'delete-task', 'description' => 'Quyền xóa công việc', 'module' => 'Task'],
            ['name' => 'Assign Task', 'slug' => 'assign-task', 'description' => 'Quyền gán công việc', 'module' => 'Task'],
            ['name' => 'Claim Task', 'slug' => 'claim-task', 'description' => 'Quyền nhận công việc', 'module' => 'Task'],
            ['name' => 'Add Task Comment', 'slug' => 'add-task-comment', 'description' => 'Quyền thêm bình luận', 'module' => 'Task'],
            ['name' => 'Add Task Feedback', 'slug' => 'add-task-feedback', 'description' => 'Quyền thêm phản hồi', 'module' => 'Task'],
            ['name' => 'Resolve Task Feedback', 'slug' => 'resolve-task-feedback', 'description' => 'Quyền giải quyết phản hồi', 'module' => 'Task'],
            ['name' => 'View Task Config', 'slug' => 'view-task-config', 'description' => 'Quyền xem cấu hình công việc', 'module' => 'Task'],
            ['name' => 'Manage Task Config', 'slug' => 'manage-task-config', 'description' => 'Quyền quản lý cấu hình công việc', 'module' => 'Task'],
            ['name' => 'Manage Task Contributions', 'slug' => 'manage-task-contributions', 'description' => 'Quyền quản lý đóng góp công việc', 'module' => 'Task'],
            ['name' => 'Manage Task Missions', 'slug' => 'manage-task-missions', 'description' => 'Quyền quản lý nhiệm vụ công việc', 'module' => 'Task'],
            
            // Customer permissions
            ['name' => 'View Customer', 'slug' => 'view-customer', 'description' => 'Quyền xem khách hàng', 'module' => 'Customer'],
            ['name' => 'Create Customer', 'slug' => 'create-customer', 'description' => 'Quyền tạo khách hàng', 'module' => 'Customer'],
            ['name' => 'Edit Customer', 'slug' => 'edit-customer', 'description' => 'Quyền chỉnh sửa khách hàng', 'module' => 'Customer'],
            ['name' => 'Delete Customer', 'slug' => 'delete-customer', 'description' => 'Quyền xóa khách hàng', 'module' => 'Customer'],
            ['name' => 'Support Customer', 'slug' => 'support-customer', 'description' => 'Quyền hỗ trợ khách hàng', 'module' => 'Customer'],
            ['name' => 'View Customer Leads', 'slug' => 'view-customer-leads', 'description' => 'Quyền xem khách hàng tiềm năng', 'module' => 'Customer'],
            ['name' => 'Manage Customer Leads', 'slug' => 'manage-customer-leads', 'description' => 'Quyền quản lý khách hàng tiềm năng', 'module' => 'Customer'],
            ['name' => 'View Customer Support', 'slug' => 'view-customer-support', 'description' => 'Quyền xem hỗ trợ khách hàng', 'module' => 'Customer'],
            ['name' => 'Manage Customer Consultation', 'slug' => 'manage-customer-consultation', 'description' => 'Quyền quản lý tư vấn khách hàng', 'module' => 'Customer'],
            ['name' => 'View Customer Appointment', 'slug' => 'view-customer-appointment', 'description' => 'Quyền xem lịch hẹn', 'module' => 'Customer'],
            ['name' => 'Manage Customer Appointment', 'slug' => 'manage-customer-appointment', 'description' => 'Quyền quản lý lịch hẹn', 'module' => 'Customer'],
            ['name' => 'Complete Customer Appointment', 'slug' => 'complete-customer-appointment', 'description' => 'Quyền hoàn thành lịch hẹn', 'module' => 'Customer'],
            
            // Contract permissions
            ['name' => 'View Contract', 'slug' => 'view-contract', 'description' => 'Quyền xem hợp đồng', 'module' => 'Contract'],
            ['name' => 'Create Contract', 'slug' => 'create-contract', 'description' => 'Quyền tạo hợp đồng', 'module' => 'Contract'],
            ['name' => 'Edit Contract', 'slug' => 'edit-contract', 'description' => 'Quyền chỉnh sửa hợp đồng', 'module' => 'Contract'],
            ['name' => 'Delete Contract', 'slug' => 'delete-contract', 'description' => 'Quyền xóa hợp đồng', 'module' => 'Contract'],
            ['name' => 'Approve Contract', 'slug' => 'approve-contract', 'description' => 'Quyền duyệt hợp đồng', 'module' => 'Contract'],
            ['name' => 'Manage Contract Services', 'slug' => 'manage-contract-services', 'description' => 'Quyền quản lý dịch vụ hợp đồng', 'module' => 'Contract'],
            ['name' => 'Manage Contract Payments', 'slug' => 'manage-contract-payments', 'description' => 'Quyền quản lý thanh toán hợp đồng', 'module' => 'Contract'],
            ['name' => 'Export Contract', 'slug' => 'export-contract', 'description' => 'Quyền xuất hợp đồng', 'module' => 'Contract'],
            ['name' => 'Create Contract Tasks', 'slug' => 'create-contract-tasks', 'description' => 'Quyền tạo công việc từ hợp đồng', 'module' => 'Contract'],
            
            // Timekeeping permissions
            ['name' => 'View Timekeeping', 'slug' => 'view-timekeeping', 'description' => 'Quyền xem chấm công', 'module' => 'Timekeeping'],
            ['name' => 'Edit Timekeeping', 'slug' => 'edit-timekeeping', 'description' => 'Quyền sửa chấm công', 'module' => 'Timekeeping'],
            ['name' => 'Approve Timekeeping', 'slug' => 'approve-timekeeping', 'description' => 'Quyền duyệt chấm công', 'module' => 'Timekeeping'],
            ['name' => 'Mark Absent', 'slug' => 'mark-absent', 'description' => 'Quyền đánh dấu vắng mặt', 'module' => 'Timekeeping'],
            ['name' => 'Check In Out', 'slug' => 'check-in-out', 'description' => 'Quyền check in/out', 'module' => 'Timekeeping'],
            ['name' => 'View Attendance Report', 'slug' => 'view-attendance-report', 'description' => 'Quyền xem báo cáo chấm công', 'module' => 'Timekeeping'],
            
            // Schedule permissions
            ['name' => 'View Schedule', 'slug' => 'view-schedule', 'description' => 'Quyền xem lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Create Schedule', 'slug' => 'create-schedule', 'description' => 'Quyền tạo lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Edit Schedule', 'slug' => 'edit-schedule', 'description' => 'Quyền sửa lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Delete Schedule', 'slug' => 'delete-schedule', 'description' => 'Quyền xóa lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Approve Schedule', 'slug' => 'approve-schedule', 'description' => 'Quyền duyệt lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Batch Approve Schedule', 'slug' => 'batch-approve-schedule', 'description' => 'Quyền duyệt hàng loạt lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'Approve Cancel Schedule', 'slug' => 'approve-cancel-schedule', 'description' => 'Quyền duyệt hủy lịch làm việc', 'module' => 'Schedule'],
            ['name' => 'View Schedule Statistics', 'slug' => 'view-schedule-statistics', 'description' => 'Quyền xem thống kê lịch làm việc', 'module' => 'Schedule'],
            
            // Salary permissions
            ['name' => 'View Salary', 'slug' => 'view-salary', 'description' => 'Quyền xem lương', 'module' => 'Salary'],
            ['name' => 'Edit Salary', 'slug' => 'edit-salary', 'description' => 'Quyền sửa lương', 'module' => 'Salary'],
            ['name' => 'Approve Salary', 'slug' => 'approve-salary', 'description' => 'Quyền duyệt lương', 'module' => 'Salary'],
            ['name' => 'Configure Salary', 'slug' => 'configure-salary', 'description' => 'Quyền cấu hình lương', 'module' => 'Salary'],
            ['name' => 'Calculate Salary', 'slug' => 'calculate-salary', 'description' => 'Quyền tính lương', 'module' => 'Salary'],
            ['name' => 'Process Salary', 'slug' => 'process-salary', 'description' => 'Quyền xử lý lương', 'module' => 'Salary'],
            ['name' => 'Bulk Process Salary', 'slug' => 'bulk-process-salary', 'description' => 'Quyền xử lý hàng loạt lương', 'module' => 'Salary'],
            ['name' => 'View Salary Advances', 'slug' => 'view-salary-advances', 'description' => 'Quyền xem tạm ứng lương', 'module' => 'Salary'],
            ['name' => 'Manage Salary Advances', 'slug' => 'manage-salary-advances', 'description' => 'Quyền quản lý tạm ứng lương', 'module' => 'Salary'],
            
            // Accounting permissions
            ['name' => 'View Transaction', 'slug' => 'view-transaction', 'description' => 'Quyền xem giao dịch', 'module' => 'Accounting'],
            ['name' => 'Create Transaction', 'slug' => 'create-transaction', 'description' => 'Quyền tạo giao dịch', 'module' => 'Accounting'],
            ['name' => 'Edit Transaction', 'slug' => 'edit-transaction', 'description' => 'Quyền sửa giao dịch', 'module' => 'Accounting'],
            ['name' => 'Delete Transaction', 'slug' => 'delete-transaction', 'description' => 'Quyền xóa giao dịch', 'module' => 'Accounting'],
            ['name' => 'View Deposit Receipt', 'slug' => 'view-deposit-receipt', 'description' => 'Quyền xem biên nhận cọc', 'module' => 'Accounting'],
            ['name' => 'Create Deposit Receipt', 'slug' => 'create-deposit-receipt', 'description' => 'Quyền tạo biên nhận cọc', 'module' => 'Accounting'],
            ['name' => 'Edit Deposit Receipt', 'slug' => 'edit-deposit-receipt', 'description' => 'Quyền sửa biên nhận cọc', 'module' => 'Accounting'],
            ['name' => 'Cancel Deposit Receipt', 'slug' => 'cancel-deposit-receipt', 'description' => 'Quyền hủy biên nhận cọc', 'module' => 'Accounting'],
            ['name' => 'Export Deposit Receipt', 'slug' => 'export-deposit-receipt', 'description' => 'Quyền xuất biên nhận cọc', 'module' => 'Accounting'],
            ['name' => 'View Report', 'slug' => 'view-report', 'description' => 'Quyền xem báo cáo', 'module' => 'Accounting'],
            ['name' => 'Export Report', 'slug' => 'export-report', 'description' => 'Quyền xuất báo cáo', 'module' => 'Accounting'],
            ['name' => 'View Commission', 'slug' => 'view-commission', 'description' => 'Quyền xem hoa hồng', 'module' => 'Accounting'],
            ['name' => 'Process Commission', 'slug' => 'process-commission', 'description' => 'Quyền xử lý hoa hồng', 'module' => 'Accounting'],
            ['name' => 'Bulk Process Commission', 'slug' => 'bulk-process-commission', 'description' => 'Quyền xử lý hàng loạt hoa hồng', 'module' => 'Accounting'],
            ['name' => 'View Transaction Category', 'slug' => 'view-transaction-category', 'description' => 'Quyền xem danh mục giao dịch', 'module' => 'Accounting'],
            ['name' => 'Manage Transaction Category', 'slug' => 'manage-transaction-category', 'description' => 'Quyền quản lý danh mục giao dịch', 'module' => 'Accounting'],
            
            // Service permissions
            ['name' => 'View Service', 'slug' => 'view-service', 'description' => 'Quyền xem dịch vụ', 'module' => 'Service'],
            ['name' => 'Create Service', 'slug' => 'create-service', 'description' => 'Quyền tạo dịch vụ', 'module' => 'Service'],
            ['name' => 'Edit Service', 'slug' => 'edit-service', 'description' => 'Quyền sửa dịch vụ', 'module' => 'Service'],
            ['name' => 'Delete Service', 'slug' => 'delete-service', 'description' => 'Quyền xóa dịch vụ', 'module' => 'Service'],
            ['name' => 'View Service Category', 'slug' => 'view-service-category', 'description' => 'Quyền xem danh mục dịch vụ', 'module' => 'Service'],
            ['name' => 'Manage Service Category', 'slug' => 'manage-service-category', 'description' => 'Quyền quản lý danh mục dịch vụ', 'module' => 'Service'],
            
            // Setting permissions
            ['name' => 'View Setting', 'slug' => 'view-setting', 'description' => 'Quyền xem cài đặt', 'module' => 'Setting'],
            ['name' => 'Edit Setting', 'slug' => 'edit-setting', 'description' => 'Quyền sửa cài đặt', 'module' => 'Setting'],
            ['name' => 'View System Config', 'slug' => 'view-system-config', 'description' => 'Quyền xem cấu hình hệ thống', 'module' => 'Setting'],
            ['name' => 'Manage System Config', 'slug' => 'manage-system-config', 'description' => 'Quyền quản lý cấu hình hệ thống', 'module' => 'Setting'],
            ['name' => 'View Payment Method', 'slug' => 'view-payment-method', 'description' => 'Quyền xem phương thức thanh toán', 'module' => 'Setting'],
            ['name' => 'Manage Payment Method', 'slug' => 'manage-payment-method', 'description' => 'Quyền quản lý phương thức thanh toán', 'module' => 'Setting'],
            ['name' => 'View Currency', 'slug' => 'view-currency', 'description' => 'Quyền xem đơn vị tiền tệ', 'module' => 'Setting'],
            ['name' => 'Manage Currency', 'slug' => 'manage-currency', 'description' => 'Quyền quản lý đơn vị tiền tệ', 'module' => 'Setting'],

            // File Explorer permissions
            ['name' => 'View File Explorer', 'slug' => 'view-file-explorer', 'description' => 'Quyền xem quản lý file', 'module' => 'FileExplorer'],
            ['name' => 'Upload File', 'slug' => 'upload-file', 'description' => 'Quyền tải lên file', 'module' => 'FileExplorer'],
            ['name' => 'Delete File', 'slug' => 'delete-file', 'description' => 'Quyền xóa file', 'module' => 'FileExplorer'],
            ['name' => 'Download File', 'slug' => 'download-file', 'description' => 'Quyền tải xuống file', 'module' => 'FileExplorer'],

            // Activity Logs permissions
            ['name' => 'View Activity Logs', 'slug' => 'view-logs', 'description' => 'Quyền xem nhật ký hoạt động', 'module' => 'Logs'],
            ['name' => 'Export Activity Logs', 'slug' => 'export-logs', 'description' => 'Quyền xuất nhật ký hoạt động', 'module' => 'Logs'],

            // Profile permissions
            ['name' => 'View Profile', 'slug' => 'view-profile', 'description' => 'Quyền xem thông tin cá nhân', 'module' => 'Profile'],
            ['name' => 'Edit Profile', 'slug' => 'edit-profile', 'description' => 'Quyền chỉnh sửa thông tin cá nhân', 'module' => 'Profile'],
            ['name' => 'View My Salary', 'slug' => 'view-my-salary', 'description' => 'Quyền xem lương cá nhân', 'module' => 'Profile'],
            ['name' => 'View My Schedule', 'slug' => 'view-my-schedule', 'description' => 'Quyền xem lịch làm việc cá nhân', 'module' => 'Profile'],
            ['name' => 'Manage My Schedule', 'slug' => 'manage-my-schedule', 'description' => 'Quyền quản lý lịch làm việc cá nhân', 'module' => 'Profile'],
            ['name' => 'View My Timesheet', 'slug' => 'view-my-timesheet', 'description' => 'Quyền xem bảng chấm công cá nhân', 'module' => 'Profile'],
            ['name' => 'View My Commission', 'slug' => 'view-my-commission', 'description' => 'Quyền xem hoa hồng cá nhân', 'module' => 'Profile'],
            ['name' => 'Request Salary Advance', 'slug' => 'request-salary-advance', 'description' => 'Quyền yêu cầu tạm ứng lương', 'module' => 'Profile'],

            // Notification permissions
            ['name' => 'View Notifications', 'slug' => 'view-notifications', 'description' => 'Quyền xem thông báo', 'module' => 'Notification'],
            ['name' => 'Manage Notifications', 'slug' => 'manage-notifications', 'description' => 'Quyền quản lý thông báo', 'module' => 'Notification'],

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