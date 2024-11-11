<?php

namespace App\Http\Controllers\Dashboard\Account\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index() {
        return view("dashboard.account.task.index");
    }

    public function detail() {
        return view("dashboard.account.task.detail");
    }

    public function config() {
        return view("dashboard.account.task.config");
    }
}
