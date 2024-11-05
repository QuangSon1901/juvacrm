<?php

namespace App\Http\Controllers\Dashboard\Overview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function index() {
        return view("dashboard.overview.index");
    }
}
