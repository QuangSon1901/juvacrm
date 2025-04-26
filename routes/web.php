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
use App\Http\Controllers\Dashboard\Account\Timekeeping\AttendanceController;
use App\Http\Controllers\Dashboard\Account\Tranning\Document\DocumentController;
use App\Http\Controllers\Dashboard\Accounting\Category\TransactionCategoryController;
use App\Http\Controllers\Dashboard\Accounting\Commissions\CommissionController;
use App\Http\Controllers\Dashboard\Accounting\Currency\CurrencyController;
use App\Http\Controllers\Dashboard\Accounting\DepositReceipt\DepositReceiptController;
use App\Http\Controllers\Dashboard\Accounting\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Dashboard\Accounting\Report\ReportController;
use App\Http\Controllers\Dashboard\Accounting\Transaction\TransactionController;
use App\Http\Controllers\Dashboard\Assets\FileExplorerController;
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

// Route::get('/test', function() {
//     return response()->json(['url' => 'success'], 200);
// });

// Route::post('/upload', [BizFlyController::class, 'uploadFile'])->name('bizfly.upload');
// Route::get('/file-url/{key}', [BizFlyController::class, 'getFileUrl'])->name('bizfly.get_url');

// Route::get('google/login', [GoogleDriveController::class, 'redirectToGoogle'])->name('google.login');
// Route::get('google/callback', [GoogleDriveController::class, 'handleGoogleCallback']);
// Route::get('google/drive/files', [GoogleDriveController::class, 'listFiles'])->name('google.drive.files');
// Route::post('google/drive/upload', [GoogleDriveController::class, 'uploadFile']);

// Route::get('google/drive/files', [GoogleDriveController::class, 'listFiles'])->name('google.drive.files');
// Route::post('google/drive/upload', [GoogleDriveController::class, 'uploadFile']);
// Route::get('google/drive/storage-info', [GoogleDriveController::class, 'getStorageInfo'])->name('google.drive.storage-info');
/************************************************** Group Dashboard **************************************************/

Route::group(
    ['namespace' => 'Dashboard', 'as' => 'dashboard.', 'middleware' => [Authenticate::class]],
    function () {

        Route::group(
            ['namespace' => 'Upload', 'as' => 'upload.', 'middleware' => []],
            function () {
                Route::post('/upload-file-cloud', [CloudinaryController::class, "uploadFile"])->name("upload-file-cloud");
                Route::post('/upload-file', [GoogleDriveController::class, "uploadFile"])->name("upload-file");
            }
        );


        Route::group(
            ['namespace' => 'Overview', 'as' => 'overview.', 'middleware' => []],
            function () {
                Route::get('/', [OverviewController::class, "index"])->name("overview");
            }
        );

        Route::group(
            ['namespace' => 'Customer', 'as' => 'customer.', 'middleware' => []],
            function () {
                Route::group(
                    ['namespace' => 'Client', 'as' => 'client.', 'middleware' => []],
                    function () {
                        Route::get('/customer/create-view', [CustomerController::class, "createView"])->name("customer-view");
                        Route::post('/customer/create', [CustomerController::class, "create"])->name("customer-create");
                        Route::post('/customer/update', [CustomerController::class, "update"])->name("customer-update");
                        Route::post('/customer/black-list', [CustomerController::class, "blackList"])->name("customer-black-list");
                        Route::get('/customer/{id}', [CustomerController::class, "detail"])->name("customer-detail");

                        Route::get('/customer-leads', [CustomerLeadController::class, "leads"])->name("customer-leads");
                        Route::get('/customer-leads-data', [CustomerLeadController::class, "data"])->name("customer-leads-data");
                        Route::post('/customer-leads/convert-to-prospect', [CustomerLeadController::class, "convertToProspect"])->name("convert-to-prospect");
                        Route::get('/customer-leads/statistics', [CustomerLeadController::class, "getLeadStatistics"])->name("lead-statistics");
                    }
                );

                Route::group(
                    ['namespace' => 'Support', 'as' => 'support.', 'middleware' => []],
                    function () {
                        Route::get('/customer-support', [CustomerSupportController::class, "index"])->name("customer-support");
                        Route::get('/customer-support-data', [CustomerSupportController::class, "data"])->name("customer-support-data");
                        Route::get('/customer-support/customers-needing-attention', [CustomerSupportController::class, "getCustomersNeedingAttention"])->name("customers-needing-attention");
                        Route::get('/customer-support/{id}', [CustomerSupportController::class, "detail"])->name("customer-support-detail");

                        Route::get('/customer-consultation/{id}', [CustomerSupportController::class, "consultation"])->name("customer-consultation");
                        Route::post('/consultation/create', [CustomerSupportController::class, "consultationCreate"])->name("consultation-create");
                        Route::post('/consultation/update', [CustomerSupportController::class, "consultationUpdate"])->name("consultation-update");
                        Route::get('/consultation/log', [CustomerSupportController::class, "consultationLog"])->name("consultation-log");
                        Route::post('/consultation/remove', [CustomerSupportController::class, "consultationRemove"])->name("consultation-remove");
                        Route::post('/consultation/add-log', [CustomerSupportController::class, "consultationAddLog"])->name("consultation-add-log");
                        Route::post('/consultation/upload-file', [CustomerSupportController::class, 'uploadFile'])->name('consultation-upload-file');

                        Route::get('/appointment/appointment-data', [AppointmentController::class, "dataAppointment"])->name("appointment-data");
                        Route::get('/appointment/detail/{id?}', [AppointmentController::class, "detail"])->name("appointment-detail");
                        Route::post('/appointment/create', [AppointmentController::class, "create"])->name("appointment-create");
                        Route::post('/appointment/update', [AppointmentController::class, "update"])->name("appointment-update");
                        Route::post('/appointment/delete', [AppointmentController::class, "delete"])->name("appointment-delete");
                        Route::get('/appointment/customer/{customer_id}', [AppointmentController::class, "getCustomerAppointments"])->name("appointment-customer");
                    }
                );

                Route::group(
                    ['namespace' => 'Manage', 'as' => 'manage.', 'middleware' => []],
                    function () {
                        Route::get('/leads', [LeadsController::class, "index"])->name("leads");
                        Route::post('/leads/post', [LeadsController::class, "leadsPost"])->name("leads-post");
                        Route::post('/leads/change-status', [LeadsController::class, "leadsChangeStatus"])->name("leads-change-status");
                        Route::get('/customer-type', [CustomerTypeController::class, "index"])->name("customer-type");
                    }
                );
            }
        );

        Route::group(
            ['namespace' => 'Contract', 'as' => 'contract.', 'middleware' => []],
            function () {
                Route::get('/contracts', [ContractController::class, "index"])->name("contract");
                Route::get('/contract/create-view', [ContractController::class, "createView"])->name("create-view");
                Route::post('/contract/create', [ContractController::class, "create"])->name("create");
                Route::post('/contract/update-info', [ContractController::class, "update"])->name("update-info");
                Route::post('/contract/update', [ContractController::class, "updateContractServices"])->name("update");
                Route::post('/contract/complete', [ContractController::class, "complete"])->name("complete");
                Route::get('/contract-data', [ContractController::class, "data"])->name("data");
                Route::get('/contract/{id}', [ContractController::class, 'detail'])->name('contract.detail');
                Route::post('/contract/create-task', [ContractController::class, 'createContractTasks'])->name('contract.create-task');
                Route::post('/contract/cancel', [ContractController::class, 'cancelContract'])->name('contract.cancel');
                Route::post('/contract/add-service', [ContractController::class, 'addService'])->name('contract.addService');
                Route::post('/contract/update-service', [ContractController::class, 'updateService'])->name('contract.updateService');
                Route::post('/contract/cancel-service', [ContractController::class, 'cancelService'])->name('contract.cancelService');
                Route::post('/contract/add-payment', [ContractController::class, 'addPayment'])->name('contract.addPayment');
                Route::post('/contract/update-payment', [ContractController::class, 'updatePayment'])->name('contract.updatePayment');
                Route::post('/contract/cancel-payment', [ContractController::class, 'cancelPayment'])->name('contract.cancelPayment');
                Route::get('/contract/{id}/export-pdf', [ContractController::class, 'exportPdf'])->name('export-pdf');
                Route::get('/contract/{id}/export-excel', [ContractController::class, 'exportExcel'])->name('export-excel');
                Route::post('/contract/sync-contract-tasks', [ContractController::class, 'syncTasks'])->name('contract.sync-contract-tasks');
            }
        );

        Route::group(
            ['namespace' => 'Account', 'as' => 'account.', 'middleware' => []],
            function () {

                Route::group(
                    ['namespace' => 'Team', 'as' => 'team.', 'middleware' => []],
                    function () {
                        Route::group(['middleware' => ['permission:view-team']], function () {
                            Route::get('/team', [TeamController::class, "index"])->name("team");
                            Route::get('/team/data', [TeamController::class, "data"])->name("data");
                            Route::get('/team/employee-by-department/{id}', [TeamController::class, "employeeByDepartment"])->name("employeeByDepartment");
                            Route::get('/team/{id}', [TeamController::class, "detail"])->name("detail");
                        });
                        
                        Route::group(['middleware' => ['permission:create-team']], function () {
                            Route::get('/team/create', [TeamController::class, "create"])->name("create");
                            Route::post('/team/create', [TeamController::class, "createPost"])->name("createPost");
                            Route::get('/team/add-member/{id}', [TeamController::class, "addMemberView"])->name("addMemberView");
                            Route::post('/team/add-member', [TeamController::class, "addMemberSave"])->name("addMemberSave");
                        });
                        
                        Route::group(['middleware' => ['permission:edit-team']], function () {
                            Route::post('/team/change-status/{id}', [TeamController::class, "changeStatus"])->name("changeStatus");
                            Route::post('/team/update', [TeamController::class, 'update']);
                        });
                        
                        Route::group(['middleware' => ['permission:delete-team']], function () {
                            Route::post('/team/remove-member', [TeamController::class, 'removeMember']);
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Role', 'as' => 'role.', 'middleware' => []],
                    function () {
                        Route::group(['middleware' => ['permission:view-role']], function () {
                            Route::get('/role/{level_id}/{department_id}', [RoleController::class, "detail"])->name("detail");
                            Route::get('/role/employee-in-role', [RoleController::class, "memberInRole"])->name("memberInRole");
                        });
                        
                        Route::group(['middleware' => ['permission:edit-role']], function () {
                            Route::get('/role/{level_id}/{department_id}/permissions', [RoleController::class, "getPermissions"])->name("permissions");
                            Route::post('/role/{level_id}/{department_id}/permissions', [RoleController::class, "savePermissions"])->name("savePermissions");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Member', 'as' => 'member.', 'middleware' => []],
                    function () {
                        Route::group(['middleware' => ['permission:create-member']], function () {
                            Route::get('/member/create-view', [MemberController::class, "createView"])->name("createView");
                            Route::post('/member/create', [MemberController::class, "create"])->name("create");
                        });
                        
                        Route::group(['middleware' => ['permission:edit-member']], function () {
                            Route::post('/member/update', [MemberController::class, "update"])->name("update");
                            Route::post('/member/reset-password', [MemberController::class, "resetPassword"])->name("resetPassword");
                            Route::post('/member/lock-account', [MemberController::class, "lockAccount"])->name("lockAccount");
                        });

                        
                        Route::group(['middleware' => ['permission:view-member']], function () {
                            Route::get('/member', [MemberController::class, "index"])->name("member");
                            Route::get('/member/data', [MemberController::class, "data"])->name("data");
                            Route::get('/member/{id}', [MemberController::class, "detail"])->name("detail");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'TimeKeeping', 'as' => 'timekeeping.', 'middleware' => []],
                    function () {
                        Route::group(['middleware' => ['permission:view-timekeeping']], function () {
                            Route::get('/account/timekeeping', [AttendanceController::class, "timekeeping"])->name("timekeeping");
                            Route::get('/account/timekeeping/data', [AttendanceController::class, "attendanceData"])->name("data");
                            Route::get('/account/timekeeping/check-in-out', [AttendanceController::class, "checkInOut"])->name("check-in-out");
                        });
                        
                        Route::post('/account/timekeeping/do-check-in', [AttendanceController::class, "doCheckIn"])->name("do-check-in");
                        Route::post('/account/timekeeping/do-check-out', [AttendanceController::class, "doCheckOut"])->name("do-check-out");
                        
                        Route::group(['middleware' => ['permission:edit-timekeeping']], function () {
                            Route::post('/account/timekeeping/update', [AttendanceController::class, "updateAttendance"])->name("update");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Schedule', 'as' => 'schedule.', 'middleware' => []],
                    function () {
                        Route::group(['middleware' => ['permission:view-schedule']], function () {
                            Route::get('/account/schedule', [ScheduleController::class, "schedule"])->name("schedule");
                            Route::get('/account/schedule/data', [ScheduleController::class, "scheduleData"])->name("data");
                            Route::get('/account/schedule/part-time', [ScheduleController::class, "partTime"])->name("part-time");
                            Route::get('/account/schedule/statistics', [ScheduleController::class, "getStatistics"])->name("statistics");
                            Route::get('/account/schedule/users-list', [ScheduleController::class, "getUsersList"])->name("users-list");
                            Route::get('/account/schedule/calendar-data', [ScheduleController::class, "getCalendarData"])->name("calendar-data");
                            Route::get('/account/schedule/{id}/detail', [ScheduleController::class, "getScheduleDetail"])->name("detail");
                            Route::get('/account/schedule/{id}/edit', [ScheduleController::class, "getScheduleEdit"])->name("edit");
                        });
                        
                        Route::group(['middleware' => ['permission:create-schedule']], function () {
                            Route::post('/account/schedule/create', [ScheduleController::class, "createSchedule"])->name("create");
                        });
                        
                        Route::group(['middleware' => ['permission:edit-schedule']], function () {
                            Route::post('/account/schedule/delete', [ScheduleController::class, "deleteSchedule"])->name("delete");
                            Route::post('/account/schedule/cancel', [ScheduleController::class, "cancelSchedule"])->name("cancel");
                        });
                        
                        Route::group(['middleware' => ['permission:approve-schedule']], function () {
                            Route::post('/account/schedule/update-status', [ScheduleController::class, "updateScheduleStatus"])->name("update-status");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Salary', 'as' => 'salary.', 'middleware' => []],
                    function () {
                        Route::group(['middleware' => ['permission:view-salary']], function () {
                            Route::get('/account/salary/payroll', [SalaryController::class, "payroll"])->name("payroll");
                            Route::get('/account/salary/payroll-data', [SalaryController::class, "payrollData"])->name("payroll-data");
                            Route::get('/account/salary/advance', [SalaryAdvanceController::class, "index"])->name("advance");
                            Route::get('/account/salary/advance-data', [SalaryAdvanceController::class, "advanceData"])->name("advance-data");
                        });
                        
                        Route::group(['middleware' => ['permission:edit-salary']], function () {
                            Route::get('/account/salary/calculate', [SalaryController::class, "calculateSalary"])->name("calculate");
                            Route::post('/account/salary/save-salary', [SalaryController::class, "saveSalary"])->name("save-salary");
                            Route::post('/account/salary/create-advance', [SalaryAdvanceController::class, "createAdvance"])->name("create-advance");
                        });
                        
                        Route::group(['middleware' => ['permission:approve-salary']], function () {
                            Route::post('/account/salary/process-salary', [SalaryController::class, "processSalary"])->name("process-salary");
                            Route::post('/account/salary/update-advance-status', [SalaryAdvanceController::class, "updateAdvanceStatus"])->name("update-advance-status");
                        });
                        
                        Route::group(['middleware' => ['permission:configure-salary']], function () {
                            Route::get('/account/configuration', [SalaryConfigurationController::class, "index"])->name("configuration");
                            Route::get('/account/user-config', [SalaryConfigurationController::class, "getUserConfig"])->name("user-config");
                            Route::post('/account/salary/save-config', [SalaryConfigurationController::class, "saveConfig"])->name("save-config");
                        });
                    }
                );

                Route::group(
                    ['namespace' => 'Task', 'as' => 'task.', 'middleware' => []],
                    function () {
                        // Routes cho quản lý cấu hình công việc
                        Route::prefix('task-config')->group(function () {
                            Route::get('/', [TaskConfigController::class, 'index'])->name('task.config');
                            Route::post('/update', [TaskConfigController::class, 'update']);
                            Route::post('/change-status', [TaskConfigController::class, 'changeStatus']);
                        });

                        // Routes cho quản lý nhiệm vụ
                        Route::prefix('task-mission')->group(function () {
                            Route::get('/{id}', [TaskMissionController::class, 'show']);
                            Route::post('/update', [TaskMissionController::class, 'update']);
                            Route::post('/change-status', [TaskMissionController::class, 'changeStatus']);
                        });

                        Route::get('/task', [TaskController::class, "index"])->name("task");
                        Route::get('/task-data', [TaskController::class, "data"])->name("task-data");
                        Route::get('/task/create', [TaskController::class, "createView"])->name("task-create-view");
                        Route::post('/task/create', [TaskController::class, "create"])->name("task-create-post");
                        Route::post('/task/update', [TaskController::class, "update"])->name("task-update");
                        Route::post('/task/add-comment', [TaskController::class, "addComment"])->name("task-add-comment");
                        Route::post('/task/update-sub-task', [TaskController::class, "updateSubTask"])->name("task-update-sub-task");
                        Route::post('/task/upload-file-task', [TaskController::class, "uploadFileTask"])->name("task-upload-file-task");
                        Route::post('/task/remove-attachment-task', [TaskController::class, "removeAttachment"])->name("task-remove-attachment-task");
                        Route::get('/config-task', [TaskController::class, "config"])->name("config");
                        Route::post('/config-task/post', [TaskController::class, "configPost"])->name("config-post");
                        Route::post('/config-task/change-status', [TaskController::class, "configChangeStatus"])->name("config-change-status");
                        Route::post('/task/claim', [TaskController::class, "claimTask"])->name("claimTask");
                        Route::post('/task/add-contribution', [TaskController::class, "addContribution"])->name("add-contribution");
                        Route::post('/task/delete-contribution', [TaskController::class, 'deleteContribution'])->name('delete-contribution');
                        Route::get('/task/available-tasks', [TaskController::class, 'getAvailableTasks'])->name('available-tasks');
                        Route::get('/task/contributions', [TaskController::class, 'getUserContributions'])->name('user-contributions');
                        Route::get('/task/dashboard/project/{id}', [TaskController::class, 'projectDashboard'])->name('project-dashboard');
                        Route::get('/task/dashboard/user', [TaskController::class, 'userDashboard'])->name('user-dashboard');

                        Route::get('/task/missions', [TaskController::class, 'getMissions'])->name('get-missions');
                        Route::get('/task/task-missions', [TaskController::class, 'getTaskMissions'])->name('get-task-missions');
                        Route::post('/task/report-mission', [TaskController::class, 'reportMission'])->name('report-mission');
                        Route::post('/task/delete-mission-report', [TaskController::class, 'deleteMissionReport'])->name('delete-mission-report');

                        Route::get('/task/show-feedback-form', [TaskController::class, 'showFeedbackForm']);
                        Route::get('/task/feedbacks', [TaskController::class, 'getFeedbacks']);
                        Route::post('/task/add-feedback', [TaskController::class, 'addFeedback']);
                        Route::post('/task/resolve-feedback-item', [TaskController::class, 'resolveFeedbackItem']);
                        Route::post('/task/confirm-feedback-resolved', [TaskController::class, 'confirmFeedbackResolved']);
                        Route::post('/task/request-feedback-revision', [TaskController::class, 'requestFeedbackRevision']);
                        Route::get('/task/feedback-item-details', [TaskController::class, 'getFeedbackItemDetails']);

                        Route::get('/task/get-status/{id}', [TaskController::class, 'getTaskStatus']);
                        Route::get('/task/get-list-by-ids', [TaskController::class, 'getTaskByIDs']);

                        Route::get('/task/{id}', [TaskController::class, "detail"])->name("detail");
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
                Route::get('/profile', [ProfileController::class, "index"])->name("profile");
                Route::post('/profile/update', [ProfileController::class, "update"])->name("update");
                Route::get('/profile/my-salary', [SalaryController::class, "mySalary"])->name("my-salary");
                Route::get('/profile/my-timesheet', [AttendanceController::class, "myTimesheet"])->name("my-timesheet");
                Route::get('/profile/my-schedule', [ScheduleController::class, "mySchedule"])->name("my-schedule");
                Route::get('/profile/my-commission', [CommissionController::class, "myCommission"])->name("my-commission");
            }
        );

        Route::group(
            ['namespace' => 'Service', 'as' => 'service.', 'middleware' => []],
            function () {
                Route::get('/services', [ServiceController::class, "index"])->name("services");
            }
        );

        Route::group(
            ['namespace' => 'Accounting', 'as' => 'accounting.', 'middleware' => []],
            function () {
                // Biên nhận cọc
                Route::group(
                    ['namespace' => 'DepositReceipt', 'as' => 'deposit-receipt.', 'middleware' => []],
                    function () {
                        Route::get('/deposit-receipt', [DepositReceiptController::class, "index"])->name("deposit-receipt");
                        Route::get('/deposit-receipt/data', [DepositReceiptController::class, "data"])->name("data");
                        Route::post('/deposit-receipt/create', [DepositReceiptController::class, "create"])->name("create");
                        Route::post('/deposit-receipt/update', [DepositReceiptController::class, "update"])->name("update");
                        Route::post('/deposit-receipt/cancel', [DepositReceiptController::class, "cancel"])->name("cancel");
                        Route::get('/deposit-receipt/{id}/export-pdf', [DepositReceiptController::class, "exportPaymentReceipt"])->name("export-pdf");
                    }
                );

                Route::group(
                    ['namespace' => 'Commissions', 'as' => 'commissions.', 'middleware' => []],
                    function () {
                        Route::get('/accounting/commissions-report', [CommissionController::class, "report"])->name("report");
                        Route::get('/accounting/commissions/report-data', [CommissionController::class, "reportData"])->name("report-data");
                        Route::post('/accounting/commissions/pay', [CommissionController::class, "payCommission"])->name("pay");
                        Route::post('/accounting/commissions/bulk-pay', [CommissionController::class, "bulkPayCommission"])->name("bulk-pay");
                    }
                );
        
                // Phiếu thu chi
                Route::group(
                    ['namespace' => 'Transaction', 'as' => 'transaction.', 'middleware' => []],
                    function () {
                        Route::get('/transaction', [TransactionController::class, "index"])->name("transaction");
                        Route::get('/transaction/data', [TransactionController::class, "data"])->name("data");
                        Route::post('/transaction/create', [TransactionController::class, "create"])->name("create");
                        Route::post('/transaction/update', [TransactionController::class, "update"])->name("update");
                        Route::post('/transaction/cancel', [TransactionController::class, "cancel"])->name("cancel");
                        Route::get('/transaction/{id}/export-pdf', [TransactionController::class, "exportTransactionReceipt"])->name("export-pdf");
                    }
                );
        
                // Danh mục thu chi
                Route::group(
                    ['namespace' => 'Category', 'as' => 'category.', 'middleware' => []],
                    function () {
                        Route::get('/transaction-category', [TransactionCategoryController::class, "index"])->name("category");
                        Route::get('/transaction-category/data', [TransactionCategoryController::class, "data"])->name("data");
                        Route::post('/transaction-category/create', [TransactionCategoryController::class, "create"])->name("create");
                        Route::post('/transaction-category/update', [TransactionCategoryController::class, "update"])->name("update");
                    }
                );
        
                // Báo cáo tổng hợp
                Route::group(
                    ['namespace' => 'Report', 'as' => 'report.', 'middleware' => []],
                    function () {
                        Route::get('/financial-report', [ReportController::class, "index"])->name("financial");
                        Route::get('/financial-report/data', [ReportController::class, "getFinancialReport"])->name("financial-data");
                        Route::get('/financial-report/export', [ReportController::class, "exportReport"])->name("financial-export");
                    }
                );

                // Phương thức thanh toán
                Route::group(
                    ['namespace' => 'PaymentMethod', 'as' => 'payment-method.', 'middleware' => []],
                    function () {
                        Route::get('/payment-method', [PaymentMethodController::class, "index"])->name("payment-method");
                        Route::get('/payment-method/data', [PaymentMethodController::class, "data"])->name("data");
                        Route::post('/payment-method/create', [PaymentMethodController::class, "create"])->name("create");
                        Route::post('/payment-method/update', [PaymentMethodController::class, "update"])->name("update");
                    }
                );

                // Đơn vị tiền tệ
                Route::group(
                    ['namespace' => 'Currency', 'as' => 'currency.', 'middleware' => []],
                    function () {
                        Route::get('/currency', [CurrencyController::class, "index"])->name("currency");
                        Route::get('/currency/data', [CurrencyController::class, "data"])->name("data");
                        Route::post('/currency/create', [CurrencyController::class, "create"])->name("create");
                        Route::post('/currency/update', [CurrencyController::class, "update"])->name("update");
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
                        Route::get('/file-explorer', [FileExplorerController::class, "index"])->name("file-explorer");
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
                        Route::get('/activity', [ActivityController::class, "index"])->name("activity");
                    }
                );
            }
        );

        Route::group(
            ['namespace' => 'Setting', 'as' => 'setting.', 'middleware' => []],
            function () {
                Route::get('/setting', [SettingController::class, "index"])->name("setting");
                Route::post('/setting/update', [SettingController::class, "update"])->name("update");
                Route::get('/setting/commissions', [SettingController::class, "commissions"])->name("commissions");
            }
        );
    }
);
