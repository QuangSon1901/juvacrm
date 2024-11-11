<?php

namespace App\Http\Controllers\Dashboard\Account\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function detail($lug) {
        return view("dashboard.account.employee.role.detail");
    }
}
