<?php

use App\Http\Controllers\Auth\LoginController;
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
use Illuminate\Support\Facades\Route;

/************************************************** Group Auth **************************************************/
Route::group(
    ['namespace' => 'Auth', 'as' => 'auth.', 'middleware' => []],
    function () {
        Route::get('/login', [LoginController::class, "index"])->name("login");
    }
);

/************************************************** Group Dashboard **************************************************/
Route::group(
    ['namespace' => 'Dashboard', 'as' => 'dashboard.', 'middleware' => []],
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
                    }
                );

                Route::group(
                    ['namespace' => 'Member', 'as' => 'member.', 'middleware' => []],
                    function () {
                        Route::get('/member', [MemberController::class, "index"])->name("member");
                        Route::get('/member/data', [MemberController::class, "data"])->name("data");
                        Route::get('/member/create-view', [MemberController::class, "createView"])->name("createView");
                        Route::post('/member/create', [MemberController::class, "create"])->name("create");
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
                        Route::get('/task/{slug}', [TaskController::class, "detail"])->name("detail");
                        Route::get('/config-task', [TaskController::class, "config"])->name("config");
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
