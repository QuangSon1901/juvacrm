<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BizFlyController;
use App\Http\Controllers\CloudinaryController;
use App\Http\Controllers\Dashboard\Account\Member\MemberController;
use App\Http\Controllers\Dashboard\Account\Role\RoleController;
use App\Http\Controllers\Dashboard\Account\Salary\SalaryAdvanceController;
use App\Http\Controllers\Dashboard\Account\Salary\SalaryConfigurationController;
use App\Http\Controllers\Dashboard\Account\Salary\SalaryController;
use App\Http\Controllers\Dashboard\Account\Schedule\ScheduleController;
use App\Http\Controllers\Dashboard\Account\Task\TaskConfigController;
use App\Http\Controllers\Dashboard\Account\Task\TaskController;
use App\Http\Controllers\Dashboard\Account\Task\TaskMissionController;
use App\Http\Controllers\Dashboard\Account\Team\TeamController;
use App\Http\Controllers\Dashboard\Account\TimeKeeping\AttendanceController;
use App\Http\Controllers\Dashboard\Account\Tranning\Document\DocumentController;
use App\Http\Controllers\Dashboard\Accounting\Category\TransactionCategoryController;
use App\Http\Controllers\Dashboard\Accounting\Commissions\CommissionController;
use App\Http\Controllers\Dashboard\Accounting\Currency\CurrencyController;
use App\Http\Controllers\Dashboard\Accounting\DepositReceipt\DepositReceiptController;
use App\Http\Controllers\Dashboard\Accounting\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Dashboard\Accounting\Report\ReportController;
use App\Http\Controllers\Dashboard\Accounting\Transaction\TransactionController;
use App\Http\Controllers\Dashboard\Assets\FileExplorerController;
use App\Http\Controllers\Dashboard\Assets\ProductController;
use App\Http\Controllers\Dashboard\Contract\ContractController;
use App\Http\Controllers\Dashboard\Customer\Client\CustomerController;
use App\Http\Controllers\Dashboard\Customer\Client\CustomerLeadController;
use App\Http\Controllers\Dashboard\Customer\Manage\CustomerTypeController;
use App\Http\Controllers\Dashboard\Customer\Manage\LeadsController;
use App\Http\Controllers\Dashboard\Customer\Support\AppointmentController;
use App\Http\Controllers\Dashboard\Logs\Activity\ActivityController;
use App\Http\Controllers\Dashboard\Overview\OverviewController;
use App\Http\Controllers\Dashboard\Profile\ProfileController;
use App\Http\Controllers\Dashboard\Setting\SettingController;
use App\Http\Controllers\Dashboard\Customer\Support\CustomerSupportController;
use App\Http\Controllers\Dashboard\Notification\NotificationController;
use App\Http\Controllers\Dashboard\Profile\MyScheduleController;
use App\Http\Controllers\Dashboard\Service\ServiceController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

/************************************************** Group Auth **************************************************/
Route::group(
    ['namespace' => 'Auth', 'as' => 'auth.', 'middleware' => []],
    function () {
        Route::group(
            ['middleware' => [RedirectIfAuthenticated::class]],
            function () {
                Route::get('/login', [LoginController::class, "index"])->name("login");
                Route::post('/login', [LoginController::class, "login"])->name("login.post");
            }
        );

        Route::post('/logout', [LoginController::class, "logout"])->name("logout");
    }
);

/************************************************** Group Dashboard **************************************************/

Route::group(
    ['namespace' => 'Dashboard', 'as' => 'dashboard.', 'middleware' => [Authenticate::class]],
    function () {

        Route::group(
            ['namespace' => 'Upload', 'as' => 'upload.', 'middleware' => []],
            function () {
                Route::post('/upload-file-cloud', [CloudinaryController::class, "uploadFile"])->name("upload-file-cloud")->middleware('permission:upload-file');
                Route::post('/upload-file', [GoogleDriveController::class, "uploadFile"])->name("upload-file")->middleware('permission:upload-file');
            }
        );


        Route::group(
            ['namespace' => 'Overview', 'as' => 'overview.', 'middleware' => []],
            function () {
                Route::get('/', [OverviewController::class, "index"])->name("overview")->middleware('permission:view-dashboard');
            }
        );

        Route::group(
            ['namespace' => 'Customer', 'as' => 'customer.', 'middleware' => []],
            function () {
                Route::group(
                    ['namespace' => 'Client', 'as' => 'client.', 'middleware' => []],
                    function () {
                        // IMPORTANT: Specific routes before parameterized routes
                        // Customer creation - specific route before dynamic /customer/{id}
                        Route::middleware(['permission:create-customer'])->group(function() {
                            Route::get('/customer/create-view', [CustomerController::class, "createView"])->name("customer-view");
                            Route::post('/customer/create', [CustomerController::class, "create"])->name("customer-create");
                        });
                        
                        // Customer editing
                        Route::middleware(['permission:edit-customer'])->group(function() {
                            Route::post('/customer/update', [CustomerController::class, "update"])->name("customer-update");
                        });

                        // Customer deletion
                        Route::middleware(['permission:delete-customer'])->group(function() {
                            Route::post('/customer/black-list', [CustomerController::class, "blackList"])->name("customer-black-list");
                        });

                        // Customer leads management
                        Route::middleware(['permission:view-customer-leads'])->group(function() {
                            Route::get('/customer-leads', [CustomerLeadController::class, "leads"])->name("customer-leads");
                            Route::get('/customer-leads-data', [CustomerLeadController::class, "data"])->name("customer-leads-data");
                            Route::get('/customer-leads/statistics', [CustomerLeadController::class, "getLeadStatistics"])->name("lead-statistics");
                        });
                        
                        Route::middleware(['permission:manage-customer-leads'])->group(function() {
                            Route::post('/customer-leads/convert-to-prospect', [CustomerLeadController::class, "convertToProspect"])->name("convert-to-prospect");
                        });
                        
                        // Basic customer management - dynamic route last
                        Route::middleware(['permission:view-customer'])->group(function() {
                            Route::get('/customer/{id}', [CustomerController::class, "detail"])->name("customer-detail");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Support', 'as' => 'support.', 'middleware' => []],
                    function () {
                        // Complete appointment
                        Route::middleware(['permission:complete-customer-appointment'])->group(function() {
                            Route::post('/appointment/complete', [AppointmentController::class, "completeAppointment"])->name("appointment-complete");
                        });
                        
                        // View customer support
                        Route::middleware(['permission:view-customer-support'])->group(function() {
                            Route::get('/customer-support', [CustomerSupportController::class, "index"])->name("customer-support");
                            Route::get('/customer-support-data', [CustomerSupportController::class, "data"])->name("customer-support-data");
                            Route::get('/customer-support/customers-needing-attention', [CustomerSupportController::class, "getCustomersNeedingAttention"])->name("customers-needing-attention");
                            Route::get('/consultation/log', [CustomerSupportController::class, "consultationLog"])->name("consultation-log");
                        });

                        // View appointments - specific routes first
                        Route::middleware(['permission:view-customer-appointment'])->group(function() {
                            Route::get('/appointment/appointment-data', [AppointmentController::class, "dataAppointment"])->name("appointment-data");
                            Route::get('/appointment/detail/{id?}', [AppointmentController::class, "detail"])->name("appointment-detail");
                            Route::get('/appointment/customer/{customer_id}', [AppointmentController::class, "getCustomerAppointments"])->name("appointment-customer");
                        });

                        // Manage customer consultation
                        Route::middleware(['permission:manage-customer-consultation'])->group(function() {
                            Route::post('/consultation/create', [CustomerSupportController::class, "consultationCreate"])->name("consultation-create");
                            Route::post('/consultation/update', [CustomerSupportController::class, "consultationUpdate"])->name("consultation-update");
                            Route::post('/consultation/add-log', [CustomerSupportController::class, "consultationAddLog"])->name("consultation-add-log");
                            Route::post('/consultation/upload-file', [CustomerSupportController::class, 'uploadFile'])->name('consultation-upload-file');
                            Route::post('/consultation/remove', [CustomerSupportController::class, "consultationRemove"])->name("consultation-remove");
                        });

                        // Manage appointments
                        Route::middleware(['permission:manage-customer-appointment'])->group(function() {
                            Route::post('/appointment/create', [AppointmentController::class, "create"])->name("appointment-create");
                            Route::post('/appointment/update', [AppointmentController::class, "update"])->name("appointment-update");
                            Route::post('/appointment/delete', [AppointmentController::class, "delete"])->name("appointment-delete");
                        });
                        
                        // Customer support detail - dynamic routes last
                        Route::middleware(['permission:view-customer-support'])->group(function() {
                            Route::get('/customer-support/{id}', [CustomerSupportController::class, "detail"])->name("customer-support-detail");
                            Route::get('/customer-consultation/{id}', [CustomerSupportController::class, "consultation"])->name("customer-consultation");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Manage', 'as' => 'manage.', 'middleware' => []],
                    function () {
                        // View customer management
                        Route::middleware(['permission:view-customer-leads'])->group(function() {
                            Route::get('/leads', [LeadsController::class, "index"])->name("leads");
                            Route::get('/customer-type', [CustomerTypeController::class, "index"])->name("customer-type");
                        });

                        // Manage customer leads
                        Route::middleware(['permission:manage-customer-leads'])->group(function() {
                            Route::post('/leads/post', [LeadsController::class, "leadsPost"])->name("leads-post");
                            Route::post('/leads/change-status', [LeadsController::class, "leadsChangeStatus"])->name("leads-change-status");
                        });
                    }
                );
            }
        );

        Route::group(
            ['namespace' => 'Contract', 'as' => 'contract.', 'middleware' => []],
            function () {
                // Basic contract view
                Route::middleware(['permission:view-contract'])->group(function() {
                    Route::get('/contracts', [ContractController::class, "index"])->name("contract");
                    Route::get('/contract-data', [ContractController::class, "data"])->name("data");
                });
                
                // Contract creation - specific route before dynamic
                Route::middleware(['permission:create-contract'])->group(function() {
                    Route::get('/contract/create-view', [ContractController::class, "createView"])->name("create-view");
                    Route::post('/contract/create', [ContractController::class, "create"])->name("create");
                });
                
                // Create tasks from contract
                Route::middleware(['permission:create-contract-tasks'])->group(function() {
                    Route::post('/contract/create-task', [ContractController::class, 'createContractTasks'])->name('contract.create-task');
                    Route::post('/contract/sync-contract-tasks', [ContractController::class, 'syncTasks'])->name('contract.sync-contract-tasks');
                });

                // Basic contract editing
                Route::middleware(['permission:edit-contract'])->group(function() {
                    Route::post('/contract/update-info', [ContractController::class, "update"])->name("update-info");
                });
                
                // Manage contract services
                Route::middleware(['permission:manage-contract-services'])->group(function() {
                    Route::post('/contract/update', [ContractController::class, "updateContractServices"])->name("update");
                    Route::post('/contract/add-service', [ContractController::class, 'addService'])->name('contract.addService');
                    Route::post('/contract/update-service', [ContractController::class, 'updateService'])->name('contract.updateService');
                    Route::post('/contract/cancel-service', [ContractController::class, 'cancelService'])->name('contract.cancelService');
                });
                
                // Manage contract payments
                Route::middleware(['permission:manage-contract-payments'])->group(function() {
                    Route::post('/contract/add-payment', [ContractController::class, 'addPayment'])->name('contract.addPayment');
                    Route::post('/contract/update-payment', [ContractController::class, 'updatePayment'])->name('contract.updatePayment');
                    Route::post('/contract/cancel-payment', [ContractController::class, 'cancelPayment'])->name('contract.cancelPayment');
                });

                // Contract deletion/cancellation
                Route::middleware(['permission:delete-contract'])->group(function() {
                    Route::post('/contract/cancel', [ContractController::class, 'cancelContract'])->name('contract.cancel');
                });

                // Contract approval
                Route::middleware(['permission:approve-contract'])->group(function() {
                    Route::post('/contract/complete', [ContractController::class, "complete"])->name("complete");
                });
                
                // Contract export
                Route::middleware(['permission:export-contract'])->group(function() {
                    Route::get('/contract/{id}/export-pdf', [ContractController::class, 'exportPdf'])->name('export-pdf');
                    Route::get('/contract/{id}/export-excel', [ContractController::class, 'exportExcel'])->name('export-excel');
                });
                
                // Contract detail - dynamic route last
                Route::middleware(['permission:view-contract'])->group(function() {
                    Route::get('/contract/{id}', [ContractController::class, 'detail'])->name('contract.detail');
                });
            }
        );

        Route::group(
            ['namespace' => 'Account', 'as' => 'account.', 'middleware' => []],
            function () {

                Route::group(
                    ['namespace' => 'Team', 'as' => 'team.', 'middleware' => []],
                    function () {
                        // View teams - list routes
                        Route::middleware(['permission:view-team'])->group(function () {
                            Route::get('/team', [TeamController::class, "index"])->name("team");
                            Route::get('/team/data', [TeamController::class, "data"])->name("data");
                            Route::get('/team/employee-by-department/{id}', [TeamController::class, "employeeByDepartment"])->name("employeeByDepartment");
                        });
                        
                        // Create teams - specific routes before dynamic
                        Route::middleware(['permission:create-team'])->group(function () {
                            Route::get('/team/create', [TeamController::class, "create"])->name("create");
                            Route::post('/team/create', [TeamController::class, "createPost"])->name("createPost");
                        });
                        
                        // Add team members
                        Route::middleware(['permission:add-team-member'])->group(function () {
                            Route::get('/team/add-member/{id}', [TeamController::class, "addMemberView"])->name("addMemberView");
                            Route::post('/team/add-member', [TeamController::class, "addMemberSave"])->name("addMemberSave");
                        });
                        
                        // Edit teams
                        Route::middleware(['permission:edit-team'])->group(function () {
                            Route::post('/team/change-status/{id}', [TeamController::class, "changeStatus"])->name("changeStatus");
                            Route::post('/team/update', [TeamController::class, 'update']);
                        });
                        
                        // Remove team members
                        Route::middleware(['permission:remove-team-member'])->group(function () {
                            Route::post('/team/remove-member', [TeamController::class, 'removeMember']);
                        });
                        
                        // Team detail - dynamic route last
                        Route::middleware(['permission:view-team'])->group(function () {
                            Route::get('/team/{id}', [TeamController::class, "detail"])->name("detail");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Role', 'as' => 'role.', 'middleware' => []],
                    function () {
                        // View roles
                        Route::middleware(['permission:view-role'])->group(function () {
                            Route::get('/role/employee-in-role', [RoleController::class, "memberInRole"])->name("memberInRole");
                        });
                        
                        // Assign permissions to roles
                        Route::middleware(['permission:assign-permissions'])->group(function () {
                            Route::get('/role/{level_id}/{department_id}/permissions', [RoleController::class, "getPermissions"])->name("permissions");
                            Route::post('/role/{level_id}/{department_id}/permissions', [RoleController::class, "savePermissions"])->name("savePermissions");
                        });
                        
                        // Role detail - parameter route at the end
                        Route::middleware(['permission:view-role'])->group(function () {
                            Route::get('/role/{level_id}/{department_id}', [RoleController::class, "detail"])->name("detail");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Member', 'as' => 'member.', 'middleware' => []],
                    function () {
                        // View members - list routes
                        Route::middleware(['permission:view-member'])->group(function () {
                            Route::get('/member', [MemberController::class, "index"])->name("member");
                            Route::get('/member/data', [MemberController::class, "data"])->name("data");
                        });
                        
                        // Create members - specific routes before dynamic
                        Route::middleware(['permission:create-member'])->group(function () {
                            Route::get('/member/create-view', [MemberController::class, "createView"])->name("createView");
                            Route::post('/member/create', [MemberController::class, "create"])->name("create");
                        });
                        
                        // Edit members
                        Route::middleware(['permission:edit-member'])->group(function () {
                            Route::post('/member/update', [MemberController::class, "update"])->name("update");
                        });
                        
                        // Reset member password
                        Route::middleware(['permission:reset-member-password'])->group(function () {
                            Route::post('/member/reset-password', [MemberController::class, "resetPassword"])->name("resetPassword");
                        });
                        
                        // Lock member account
                        Route::middleware(['permission:lock-member-account'])->group(function () {
                            Route::post('/member/lock-account', [MemberController::class, "lockAccount"])->name("lockAccount");
                        });
                        
                        // Member detail - dynamic route last
                        Route::middleware(['permission:view-member'])->group(function () {
                            Route::get('/member/{id}', [MemberController::class, "detail"])->name("detail");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'TimeKeeping', 'as' => 'timekeeping.', 'middleware' => []],
                    function () {
                        // View timekeeping
                        Route::middleware(['permission:view-timekeeping'])->group(function () {
                            Route::get('/account/timekeeping', [AttendanceController::class, "timekeeping"])->name("timekeeping");
                            Route::get('/account/timekeeping/data', [AttendanceController::class, "attendanceData"])->name("data");
                        });
                        
                        // Check in/out feature
                        Route::get('/account/timekeeping/check-in-out', [AttendanceController::class, "checkInOut"])->name("check-in-out");
                        Route::post('/account/timekeeping/do-check-in', [AttendanceController::class, "doCheckIn"])->name("do-check-in");
                        Route::post('/account/timekeeping/do-check-out', [AttendanceController::class, "doCheckOut"])->name("do-check-out");
                        
                        // Edit timekeeping
                        Route::middleware(['permission:edit-timekeeping'])->group(function () {
                            Route::post('/account/timekeeping/update', [AttendanceController::class, "updateAttendance"])->name("update");
                        });

                        // Mark absent
                        Route::middleware(['permission:mark-absent'])->group(function () {
                            Route::post('/account/timekeeping/mark-absent', [AttendanceController::class, "markAbsent"])->name("mark-absent");
                        });
                        
                        // Get user schedules for timekeeping
                        Route::middleware(['permission:view-schedule'])->group(function () {
                            Route::get('/account/schedule/get-user-schedules', [ScheduleController::class, "getUserSchedules"])->name("get-user-schedules");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Schedule', 'as' => 'schedule.', 'middleware' => []],
                    function () {
                        // View schedules
                        Route::middleware(['permission:view-schedule'])->group(function () {
                            Route::get('/account/schedule', [ScheduleController::class, "schedule"])->name("schedule");
                            Route::get('/account/schedule/data', [ScheduleController::class, "scheduleData"])->name("data");
                            Route::get('/account/schedule/users-list', [ScheduleController::class, "getUsersList"])->name("users-list");
                            Route::get('/account/schedule/calendar-data', [ScheduleController::class, "getCalendarData"])->name("calendar-data");
                        });
                        
                        // View schedule statistics
                        Route::middleware(['permission:view-schedule-statistics'])->group(function () {
                            Route::get('/account/schedule/statistics', [ScheduleController::class, "getStatistics"])->name("statistics");
                        });
                        
                        // Create schedules (Admin)
                        Route::middleware(['permission:create-schedule'])->group(function () {
                            Route::post('/account/schedule/create', [ScheduleController::class, "createSchedule"])->name("create");
                        });
                        
                        // Delete schedules (Admin)
                        Route::middleware(['permission:delete-schedule'])->group(function () {
                            Route::post('/account/schedule/delete', [ScheduleController::class, "deleteSchedule"])->name("delete");
                        });
                        
                        // Batch approve schedules
                        Route::middleware(['permission:batch-approve-schedule'])->group(function () {
                            Route::post('/account/schedule/batch-approve', [ScheduleController::class, "batchApprove"])->name("batch-approve");
                        });
                        
                        // Approve schedules (Admin)
                        Route::middleware(['permission:approve-schedule'])->group(function () {
                            Route::post('/account/schedule/update-status', [ScheduleController::class, "updateScheduleStatus"])->name("update-status");
                        });
                        
                        // Approve schedule cancellation
                        Route::middleware(['permission:approve-cancel-schedule'])->group(function () {
                            Route::post('/account/schedule/approve-cancel', [ScheduleController::class, "approveCancelRequest"])->name("approve-cancel");
                            Route::post('/account/schedule/reject-cancel', [ScheduleController::class, "rejectCancelRequest"])->name("reject-cancel");
                        });
                        
                        // Edit schedules
                        Route::middleware(['permission:edit-schedule'])->group(function () {
                            Route::get('/account/schedule/{id}/edit', [ScheduleController::class, "getScheduleEdit"])->name("edit");
                        });
                        
                        // Schedule detail - dynamic route last
                        Route::middleware(['permission:view-schedule'])->group(function () {
                            Route::get('/account/schedule/{id}/detail', [ScheduleController::class, "getScheduleDetail"])->name("detail");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Salary', 'as' => 'salary.', 'middleware' => []],
                    function () {
                        // View salary information
                        Route::middleware(['permission:view-salary'])->group(function() {
                            Route::get('/account/salary/payroll', [SalaryController::class, "index"])->name("payroll");
                            Route::get('/account/salary/payroll-data', [SalaryController::class, "data"])->name("payroll-data");
                            Route::get('/account/salary/get-pending-ids', [SalaryController::class, "getPendingSalaryIds"])->name("get-pending-ids");
                            Route::get('/account/salary/get-processed-ids', [SalaryController::class, "getProcessedSalaryIds"])->name("get-processed-ids");
                        });
                        
                        // Calculate salary
                        Route::middleware(['permission:calculate-salary'])->group(function() {
                            Route::get('/account/salary/calculate', [SalaryController::class, "calculateSalary"])->name("calculate");
                        });
                        
                        // Process individual salary
                        Route::middleware(['permission:process-salary'])->group(function() {
                            Route::post('/account/salary/process-salary', [SalaryController::class, "processSalary"])->name("process-salary");
                        });
                        
                        // Bulk process salary
                        Route::middleware(['permission:bulk-process-salary'])->group(function() {
                            Route::post('/account/salary/bulk-process-salary', [SalaryController::class, "bulkProcessSalary"])->name("bulk-process-salary");
                            Route::post('/account/salary/bulk-process-all-pending', [SalaryController::class, "bulkProcessAllPending"])->name("bulk-process-all-pending");
                            Route::post('/account/salary/bulk-pay-all-processed', [SalaryController::class, "bulkPayAllProcessed"])->name("bulk-pay-all-processed");
                        });
                        
                        // Salary detail - dynamic route last
                        Route::middleware(['permission:view-salary'])->group(function() {
                            Route::get('/account/salary/payroll/{id}/detail', [SalaryController::class, "getSalaryDetail"])->name("salary-detail");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Task', 'as' => 'task.', 'middleware' => []],
                    function () {
                        // View task configuration
                        Route::prefix('task-config')->middleware(['permission:view-task-config'])->group(function () {
                            Route::get('/', [TaskConfigController::class, 'index'])->name('task.config');
                        });
                        
                        // Manage task configuration
                        Route::prefix('task-config')->middleware(['permission:manage-task-config'])->group(function () {
                            Route::post('/update', [TaskConfigController::class, 'update']);
                            Route::post('/change-status', [TaskConfigController::class, 'changeStatus']);
                        });

                        // Manage task missions
                        Route::prefix('task-mission')->middleware(['permission:manage-task-missions'])->group(function () {
                            Route::post('/update', [TaskMissionController::class, 'update']);
                            Route::post('/change-status', [TaskMissionController::class, 'changeStatus']);
                        });
                        
                        // View tasks - list routes
                        Route::middleware(['permission:view-task'])->group(function() {
                            Route::get('/task', [TaskController::class, "index"])->name("task");
                            Route::get('/task-data', [TaskController::class, "data"])->name("task-data");
                            Route::get('/config-task', [TaskController::class, "config"])->name("config");
                            Route::get('/task/get-list-by-ids', [TaskController::class, 'getTaskByIDs']);
                            Route::get('/task/available-tasks', [TaskController::class, 'getAvailableTasks'])->name('available-tasks');
                            Route::get('/task/contributions', [TaskController::class, 'getUserContributions'])->name('user-contributions');
                            Route::get('/task/missions', [TaskController::class, 'getMissions'])->name('get-missions');
                            Route::get('/task/task-missions', [TaskController::class, 'getTaskMissions'])->name('get-task-missions');
                            Route::get('/task/show-feedback-form', [TaskController::class, 'showFeedbackForm']);
                            Route::get('/task/feedbacks', [TaskController::class, 'getFeedbacks']);
                            Route::get('/task/feedback-item-details', [TaskController::class, 'getFeedbackItemDetails']);
                            Route::get('/task/get-status/{id}', [TaskController::class, 'getTaskStatus']);
                        });
                        
                        // Task dashboard - specific routes with parameters
                        Route::middleware(['permission:view-task'])->group(function() {
                            Route::get('/task/dashboard/project/{id}', [TaskController::class, 'projectDashboard'])->name('project-dashboard');
                            Route::get('/task/dashboard/user', [TaskController::class, 'userDashboard'])->name('user-dashboard');
                        });

                        // Create tasks - specific route before dynamic
                        Route::middleware(['permission:create-task'])->group(function() {
                            Route::get('/task/create', [TaskController::class, "createView"])->name("task-create-view");
                            Route::post('/task/create', [TaskController::class, "create"])->name("task-create-post");
                        });
                        
                        // Add task comments
                        Route::middleware(['permission:add-task-comment'])->group(function() {
                            Route::post('/task/add-comment', [TaskController::class, "addComment"])->name("task-add-comment");
                        });
                        
                        // Upload task files
                        Route::middleware(['permission:upload-file'])->group(function() {
                            Route::post('/task/upload-file-task', [TaskController::class, "uploadFileTask"])->name("task-upload-file-task");
                        });
                        
                        // Add task feedback
                        Route::middleware(['permission:add-task-feedback'])->group(function() {
                            Route::post('/task/add-feedback', [TaskController::class, 'addFeedback']);
                        });
                        
                        // Report mission progress
                        Route::middleware(['permission:manage-task-missions'])->group(function() {
                            Route::post('/task/report-mission', [TaskController::class, 'reportMission'])->name('report-mission');
                        });

                        // Edit tasks
                        Route::middleware(['permission:edit-task'])->group(function() {
                            Route::post('/task/update', [TaskController::class, "update"])->name("task-update");
                            Route::post('/task/update-sub-task', [TaskController::class, "updateSubTask"])->name("task-update-sub-task");
                        });
                        
                        // Manage task configuration
                        Route::middleware(['permission:manage-task-config'])->group(function() {
                            Route::post('/config-task/post', [TaskController::class, "configPost"])->name("config-post");
                            Route::post('/config-task/change-status', [TaskController::class, "configChangeStatus"])->name("config-change-status");
                        });
                        
                        // Manage task contributions
                        Route::middleware(['permission:manage-task-contributions'])->group(function() {
                            Route::post('/task/add-contribution', [TaskController::class, "addContribution"])->name("add-contribution");
                        });
                        
                        // Resolve task feedback
                        Route::middleware(['permission:resolve-task-feedback'])->group(function() {
                            Route::post('/task/resolve-feedback-item', [TaskController::class, 'resolveFeedbackItem']);
                            Route::post('/task/confirm-feedback-resolved', [TaskController::class, 'confirmFeedbackResolved']);
                            Route::post('/task/request-feedback-revision', [TaskController::class, 'requestFeedbackRevision']);
                        });

                        // Claim tasks
                        Route::middleware(['permission:claim-task'])->group(function() {
                            Route::post('/task/claim', [TaskController::class, "claimTask"])->name("claimTask");
                        });

                        // Delete task content
                        Route::middleware(['permission:delete-task'])->group(function() {
                            Route::post('/task/remove-attachment-task', [TaskController::class, "removeAttachment"])->name("task-remove-attachment-task");
                            Route::post('/task/delete-contribution', [TaskController::class, 'deleteContribution'])->name('delete-contribution');
                            Route::post('/task/delete-mission-report', [TaskController::class, 'deleteMissionReport'])->name('delete-mission-report');
                        });
                        
                        // Task mission detail - parameter route
                        Route::prefix('task-mission')->middleware(['permission:manage-task-missions'])->group(function () {
                            Route::get('/{id}', [TaskMissionController::class, 'show']);
                        });

                        // Task detail - dynamic route last
                        Route::middleware(['permission:view-task'])->group(function() {
                            Route::get('/task/{id}', [TaskController::class, "detail"])->name("detail");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Training', 'as' => 'training.', 'middleware' => []],
                    function () {
                        Route::group(
                            ['namespace' => 'Document', 'as' => 'document.', 'middleware' => []],
                            function () {
                                Route::get('/document', [DocumentController::class, "index"])->name("document");
                                Route::get('/blank', [DocumentController::class, "blank"])->name("blank");
                            }
                        );
                    }
                );
            }
        );

        Route::group(
            ['namespace' => 'Profile', 'as' => 'profile.', 'middleware' => []],
            function () {
                // View profile
                Route::middleware(['permission:view-profile'])->group(function() {
                    Route::get('/profile', [ProfileController::class, "index"])->name("profile");
                });
                
                // Edit profile
                Route::middleware(['permission:edit-profile'])->group(function() {
                    Route::post('/profile/update', [ProfileController::class, "update"])->name("update");
                });
                
                // View personal salary
                Route::middleware(['permission:view-my-salary'])->group(function() {
                    Route::get('/profile/my-salary', [SalaryController::class, "mySalary"])->name("my-salary");
                });
                
                // View personal timesheet
                Route::middleware(['permission:view-my-timesheet'])->group(function() {
                    Route::get('/profile/my-timesheet', [AttendanceController::class, "myTimesheet"])->name("my-timesheet");
                });
                
                // View personal schedule
                Route::middleware(['permission:view-my-schedule'])->group(function() {
                    Route::get('/profile/my-schedule', [MyScheduleController::class, "index"])->name("my-schedule");
                });
                
                // Manage personal schedule
                Route::middleware(['permission:manage-my-schedule'])->group(function() {
                    Route::post('/profile/my-schedule/create', [MyScheduleController::class, "createSchedule"])->name("my-schedule.create");
                    Route::post('/profile/my-schedule/cancel', [MyScheduleController::class, "cancelSchedule"])->name("my-schedule.cancel");
                    Route::post('/profile/my-schedule/request-cancel', [MyScheduleController::class, "requestCancelSchedule"])->name("my-schedule.request-cancel");
                });
                
                // View personal commission
                Route::middleware(['permission:view-my-commission'])->group(function() {
                    Route::get('/profile/my-commission', [CommissionController::class, "myCommission"])->name("my-commission");
                });
                
                // Detailed views with parameters - at the end
                Route::middleware(['permission:view-my-salary'])->group(function() {
                    Route::get('/profile/my-salary/{id}/detail', [SalaryController::class, "getMySalaryDetail"])->name("my-salary-detail");
                });
                
                Route::middleware(['permission:view-my-schedule'])->group(function() {
                    Route::get('/profile/my-schedule/{id}/detail', [MyScheduleController::class, "getScheduleDetail"])->name("my-schedule.detail");
                });
            }
        );

        Route::group(
            ['namespace' => 'Service', 'as' => 'service.', 'middleware' => []],
            function () {
                // View services
                Route::middleware(['permission:view-service'])->group(function() {
                    Route::get('/services', [ServiceController::class, "index"])->name("services");
                });
                
                // Create, Edit, Delete routes would be added here when implemented
            }
        );

        Route::group(
            ['namespace' => 'Accounting', 'as' => 'accounting.', 'middleware' => []],
            function () {
                // Biên nhận cọc
                Route::group(
                    ['namespace' => 'DepositReceipt', 'as' => 'deposit-receipt.', 'middleware' => []],
                    function () {
                        // View deposit receipts
                        Route::middleware(['permission:view-deposit-receipt'])->group(function() {
                            Route::get('/deposit-receipt', [DepositReceiptController::class, "index"])->name("deposit-receipt");
                            Route::get('/deposit-receipt/data', [DepositReceiptController::class, "data"])->name("data");
                        });
                        
                        // Create deposit receipts
                        Route::middleware(['permission:create-deposit-receipt'])->group(function() {
                            Route::post('/deposit-receipt/create', [DepositReceiptController::class, "create"])->name("create");
                        });

                        // Edit deposit receipts
                        Route::middleware(['permission:edit-deposit-receipt'])->group(function() {
                            Route::post('/deposit-receipt/update', [DepositReceiptController::class, "update"])->name("update");
                        });

                        // Cancel deposit receipts
                        Route::middleware(['permission:cancel-deposit-receipt'])->group(function() {
                            Route::post('/deposit-receipt/cancel', [DepositReceiptController::class, "cancel"])->name("cancel");
                        });
                        
                        // Export deposit receipts - parameter route at the end
                        Route::middleware(['permission:export-deposit-receipt'])->group(function() {
                            Route::get('/deposit-receipt/{id}/export-pdf', [DepositReceiptController::class, "exportPaymentReceipt"])->name("export-pdf");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Commissions', 'as' => 'commissions.', 'middleware' => []],
                    function () {
                        // View commissions
                        Route::middleware(['permission:view-commission'])->group(function() {
                            Route::get('/accounting/commissions-report', [CommissionController::class, "report"])->name("report");
                            Route::get('/accounting/commissions/report-data', [CommissionController::class, "reportData"])->name("report-data");
                        });

                        // Process individual commissions
                        Route::middleware(['permission:process-commission'])->group(function() {
                            Route::post('/accounting/commissions/pay', [CommissionController::class, "payCommission"])->name("pay");
                        });
                        
                        // Process bulk commissions
                        Route::middleware(['permission:bulk-process-commission'])->group(function() {
                            Route::post('/accounting/commissions/bulk-pay', [CommissionController::class, "bulkPayCommission"])->name("bulk-pay");
                        });
                    }
                );
        
                // Phiếu thu chi
                Route::group(
                    ['namespace' => 'Transaction', 'as' => 'transaction.', 'middleware' => []],
                    function () {
                        // View transactions
                        Route::middleware(['permission:view-transaction'])->group(function() {
                            Route::get('/transaction', [TransactionController::class, "index"])->name("transaction");
                            Route::get('/transaction/data', [TransactionController::class, "data"])->name("data");
                        });
                        
                        // Create transactions
                        Route::middleware(['permission:create-transaction'])->group(function() {
                            Route::post('/transaction/create', [TransactionController::class, "create"])->name("create");
                        });

                        // Edit transactions
                        Route::middleware(['permission:edit-transaction'])->group(function() {
                            Route::post('/transaction/update', [TransactionController::class, "update"])->name("update");
                        });

                        // Delete transactions
                        Route::middleware(['permission:delete-transaction'])->group(function() {
                            Route::post('/transaction/cancel', [TransactionController::class, "cancel"])->name("cancel");
                        });
                        
                        // Export transactions - parameter route at the end
                        Route::middleware(['permission:export-report'])->group(function() {
                            Route::get('/transaction/{id}/export-pdf', [TransactionController::class, "exportTransactionReceipt"])->name("export-pdf");
                        });
                    }
                );
        
                // Danh mục thu chi
                Route::group(
                    ['namespace' => 'Category', 'as' => 'category.', 'middleware' => []],
                    function () {
                        // View transaction categories
                        Route::middleware(['permission:view-transaction-category'])->group(function() {
                            Route::get('/transaction-category', [TransactionCategoryController::class, "index"])->name("category");
                            Route::get('/transaction-category/data', [TransactionCategoryController::class, "data"])->name("data");
                        });

                        // Manage transaction categories
                        Route::middleware(['permission:manage-transaction-category'])->group(function() {
                            Route::post('/transaction-category/create', [TransactionCategoryController::class, "create"])->name("create");
                            Route::post('/transaction-category/update', [TransactionCategoryController::class, "update"])->name("update");
                        });
                    }
                );
        
                // Báo cáo tổng hợp
                Route::group(
                    ['namespace' => 'Report', 'as' => 'report.', 'middleware' => []],
                    function () {
                        // View financial reports
                        Route::middleware(['permission:view-report'])->group(function() {
                            Route::get('/financial-report', [ReportController::class, "index"])->name("financial");
                            Route::get('/financial-report/data', [ReportController::class, "getFinancialReport"])->name("financial-data");
                        });
                        
                        // Export financial reports
                        Route::middleware(['permission:export-report'])->group(function() {
                            Route::get('/financial-report/export', [ReportController::class, "exportReport"])->name("financial-export");
                        });
                    }
                );

                // Phương thức thanh toán
                Route::group(
                    ['namespace' => 'PaymentMethod', 'as' => 'payment-method.', 'middleware' => []],
                    function () {
                        // View payment methods
                        Route::middleware(['permission:view-payment-method'])->group(function() {
                            Route::get('/payment-method', [PaymentMethodController::class, "index"])->name("payment-method");
                            Route::get('/payment-method/data', [PaymentMethodController::class, "data"])->name("data");
                        });

                        // Manage payment methods
                        Route::middleware(['permission:manage-payment-method'])->group(function() {
                            Route::post('/payment-method/create', [PaymentMethodController::class, "create"])->name("create");
                            Route::post('/payment-method/update', [PaymentMethodController::class, "update"])->name("update");
                        });
                    }
                );

                // Đơn vị tiền tệ
                Route::group(
                    ['namespace' => 'Currency', 'as' => 'currency.', 'middleware' => []],
                    function () {
                        // View currencies
                        Route::middleware(['permission:view-currency'])->group(function() {
                            Route::get('/currency', [CurrencyController::class, "index"])->name("currency");
                            Route::get('/currency/data', [CurrencyController::class, "data"])->name("data");
                        });

                        // Manage currencies
                        Route::middleware(['permission:manage-currency'])->group(function() {
                            Route::post('/currency/create', [CurrencyController::class, "create"])->name("create");
                            Route::post('/currency/update', [CurrencyController::class, "update"])->name("update");
                        });
                    }
                );
            }
        );

        Route::group(
            ['namespace' => 'Assets', 'as' => 'assets.', 'middleware' => []],
            function () {
                Route::group(
                    ['namespace' => 'FileExplorer', 'as' => 'file-explorer.', 'middleware' => []],
                    function () {
                        // View file explorer
                        Route::middleware(['permission:view-file-explorer'])->group(function() {
                            Route::get('/file-explorer', [FileExplorerController::class, "index"])->name("file-explorer");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Product', 'as' => 'product.', 'middleware' => []],
                    function () {
                        // View product list
                        Route::middleware(['permission:view-assets'])->group(function() {
                            Route::get('/product', [ProductController::class, "index"])->name("product");
                            Route::get('/product/data', [ProductController::class, "data"])->name("data");
                        });

                        // Manage products (add, edit, change status)
                        Route::middleware(['permission:manage-assets'])->group(function() {
                            Route::post('/product/create', [ProductController::class, "create"])->name("create");
                            Route::post('/product/update', [ProductController::class, "update"])->name("update");
                            Route::post('/product/change-status', [ProductController::class, "changeStatus"])->name("change-status");
                        });
                    }
                );
            }
        );

        Route::group(
            ['namespace' => 'Logs', 'as' => 'logs.', 'middleware' => []],
            function () {
                Route::group(
                    ['namespace' => 'Activity', 'as' => 'activity.', 'middleware' => []],
                    function () {
                        // View activity logs
                        Route::middleware(['permission:view-logs'])->group(function() {
                            Route::get('/activity', [ActivityController::class, "index"])->name("activity");
                        });
                    }
                );
            }
        );

        Route::group(
            ['namespace' => 'Setting', 'as' => 'setting.', 'middleware' => []],
            function () {
                // View general settings
                Route::middleware(['permission:view-setting'])->group(function() {
                    Route::get('/setting', [SettingController::class, "index"])->name("setting");
                });
                
                // View salary settings
                Route::middleware(['permission:configure-salary'])->group(function() {
                    Route::get('/setting/salary', [SettingController::class, "salarySettings"])->name("salary-settings");
                });
                
                // View commission settings
                Route::middleware(['permission:view-commission'])->group(function() {
                    Route::get('/setting/commissions', [SettingController::class, "commissions"])->name("commissions");
                });

                // Edit general settings
                Route::middleware(['permission:edit-setting'])->group(function() {
                    Route::post('/setting/update', [SettingController::class, "update"])->name("update");
                });
                
                // Edit salary settings
                Route::middleware(['permission:configure-salary'])->group(function() {
                    Route::post('/setting/salary/update', [SettingController::class, "updateSalarySettings"])->name("update-salary-settings");
                });
            }
        );

        Route::group(
            ['namespace' => 'Notification', 'as' => 'notification.', 'middleware' => []],
            function () {
                // View notifications
                Route::middleware(['permission:view-notifications'])->group(function() {
                    Route::get('/notifications', [NotificationController::class, "index"])->name("index");
                    Route::get('/notifications/data', [NotificationController::class, "getData"])->name("data");
                    Route::get('/notifications/unread', [NotificationController::class, "getUnreadNotifications"])->name("unread");
                });
                
                // Manage notifications
                Route::middleware(['permission:manage-notifications'])->group(function() {
                    Route::post('/notifications/mark-read', [NotificationController::class, "markAsRead"])->name("mark-read");
                    Route::post('/notifications/delete', [NotificationController::class, "delete"])->name("delete");
                });
            }
        );
    }
);