<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BizFlyController;
use App\Http\Controllers\Dashboard\Account\Member\MemberController;
use App\Http\Controllers\Dashboard\Account\Role\RoleController;
use App\Http\Controllers\Dashboard\Account\Task\TaskController;
use App\Http\Controllers\Dashboard\Account\Team\TeamController;
use App\Http\Controllers\Dashboard\Account\TimeKeeping\TimeKeepingController;
use App\Http\Controllers\Dashboard\Account\Tranning\Document\DocumentController;
use App\Http\Controllers\Dashboard\Accounting\DepositReceipt\DepositReceiptController;
use App\Http\Controllers\Dashboard\Contract\ContractController;
use App\Http\Controllers\Dashboard\Customer\Client\CustomerController;
use App\Http\Controllers\Dashboard\Customer\Manage\CustomerTypeController;
use App\Http\Controllers\Dashboard\Customer\Manage\LeadsController;
use App\Http\Controllers\Dashboard\Logs\Activity\ActivityController;
use App\Http\Controllers\Dashboard\Overview\OverviewController;
use App\Http\Controllers\Dashboard\Profile\ProfileController;
use App\Http\Controllers\Dashboard\Setting\SettingController;
use App\Http\Controllers\Dashboard\Customer\Support\CustomerSupportController;
use App\Http\Controllers\Dashboard\Service\ServiceController;
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


/************************************************** Group Dashboard **************************************************/
Route::group(
    ['namespace' => 'Dashboard', 'as' => 'dashboard.', 'middleware' => [Authenticate::class]],
    function () {


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
                        Route::get('/customer/{id}', [CustomerController::class, "detail"])->name("customer-detail");
                        Route::get('/customer-leads', [CustomerController::class, "leads"])->name("customer-leads");
                    }
                );

                Route::group(
                    ['namespace' => 'Support', 'as' => 'support.', 'middleware' => []],
                    function () {
                        Route::get('/customer-support', [CustomerSupportController::class, "index"])->name("customer-support");
                        Route::get('/customer-support-data', [CustomerSupportController::class, "data"])->name("customer-support-data");
                        Route::get('/customer-support/{id}', [CustomerSupportController::class, "detail"])->name("customer-support-detail");
                        Route::get('/customer-consultation/{id}', [CustomerSupportController::class, "consultation"])->name("customer-consultation");
                        Route::post('/consultation/create', [CustomerSupportController::class, "consultationCreate"])->name("consultation-create");
                        Route::post('/consultation/add-log', [CustomerSupportController::class, "consultationAddLog"])->name("consultation-add-log");
                    }
                );

                Route::group(
                    ['namespace' => 'Manage', 'as' => 'manage.', 'middleware' => []],
                    function () {
                        Route::get('/leads', [LeadsController::class, "index"])->name("leads");
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
                Route::get('/contract/123', [ContractController::class, "detail"])->name("detail");
            }
        );

        Route::group(
            ['namespace' => 'Account', 'as' => 'account.', 'middleware' => []],
            function () {

                Route::group(
                    ['namespace' => 'Team', 'as' => 'team.', 'middleware' => []],
                    function () {
                        Route::get('/team', [TeamController::class, "index"])->name("team");
                        Route::get('/team/data', [TeamController::class, "data"])->name("data");
                        Route::get('/team/employee-by-department/{id}', [TeamController::class, "employeeByDepartment"])->name("employeeByDepartment");
                        Route::get('/team/create', [TeamController::class, "create"])->name("create");
                        Route::post('/team/create', [TeamController::class, "createPost"])->name("createPost");
                        Route::post('/team/change-status/{id}', [TeamController::class, "changeStatus"])->name("changeStatus");

                        Route::get('/team/add-member/{id}', [TeamController::class, "addMemberView"])->name("addMemberView");
                        Route::post('/team/add-member', [TeamController::class, "addMemberSave"])->name("addMemberSave");
                        Route::post('/team/remove-member', [TeamController::class, 'removeMember']);

                        Route::post('/team/update', [TeamController::class, 'update']);

                        Route::get('/team/{id}', [TeamController::class, "detail"])->name("detail");
                    }
                );

                Route::group(
                    ['namespace' => 'Role', 'as' => 'role.', 'middleware' => []],
                    function () {
                        Route::get('/role/{level_id}/{department_id}', [RoleController::class, "detail"])->name("detail");
                        Route::get('/role/employee-in-role', [RoleController::class, "memberInRole"])->name("memberInRole");
                    }
                );

                Route::group(
                    ['namespace' => 'Member', 'as' => 'member.', 'middleware' => []],
                    function () {
                        Route::get('/member', [MemberController::class, "index"])->name("member");
                        Route::get('/member/data', [MemberController::class, "data"])->name("data");
                        Route::get('/member/create-view', [MemberController::class, "createView"])->name("createView");
                        Route::post('/member/create', [MemberController::class, "create"])->name("create");
                        Route::post('/member/update', [MemberController::class, "update"])->name("update");
                        Route::post('/member/reset-password', [MemberController::class, "resetPassword"])->name("resetPassword");
                        Route::post('/member/lock-account', [MemberController::class, "lockAccount"])->name("lockAccount");
                        Route::get('/member/{id}', [MemberController::class, "detail"])->name("detail");
                    }
                );

                Route::group(
                    ['namespace' => 'TimeKeeping', 'as' => 'timekeeping.', 'middleware' => []],
                    function () {
                        Route::get('/timekeeping', [TimeKeepingController::class, "index"])->name("timekeeping");
                    }
                );

                Route::group(
                    ['namespace' => 'Task', 'as' => 'task.', 'middleware' => []],
                    function () {
                        Route::get('/task', [TaskController::class, "index"])->name("task");
                        Route::get('/task-data', [TaskController::class, "data"])->name("task-data");
                        Route::get('/task/create', [TaskController::class, "createView"])->name("task-create-view");
                        Route::post('/task/create', [TaskController::class, "create"])->name("task-create-post");
                        Route::post('/task/update', [TaskController::class, "update"])->name("task-update");
                        Route::post('/task/add-comment', [TaskController::class, "addComment"])->name("task-add-comment");
                        Route::post('/task/update-sub-task', [TaskController::class, "updateSubTask"])->name("task-update-sub-task");
                        Route::get('/config-task', [TaskController::class, "config"])->name("config");
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
                Route::group(
                    ['namespace' => 'DepositReceipt', 'as' => 'deposit-receipt.', 'middleware' => []],
                    function () {
                        Route::get('/deposit-receipt', [DepositReceiptController::class, "index"])->name("deposit-receipt");
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
            }
        );
    }
);
