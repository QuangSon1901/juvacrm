<?php

namespace App\Http\Controllers\Dashboard\Account\Team;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index() {
        return view("dashboard.account.employee.team.index");
    }

    public function detail($slug) {
        return view("dashboard.account.employee.team.detail", ["slug" => $slug]);
    }
}
