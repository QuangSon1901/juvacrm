<?php

namespace App\Http\Controllers\Dashboard\Account\TimeKeeping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimeKeepingController extends Controller
{
    public function index() {
        return view("dashboard.account.timekeeping.index");
    }
}
