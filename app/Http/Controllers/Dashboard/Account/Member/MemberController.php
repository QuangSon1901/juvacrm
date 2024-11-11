<?php

namespace App\Http\Controllers\Dashboard\Account\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index() {
        return view("dashboard.account.employee.member.index");
    }

    public function detail() {
        return view("dashboard.account.employee.member.detail");
    }
}
