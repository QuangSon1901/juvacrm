<?php

namespace App\Http\Controllers\Dashboard\Customer\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeadsController extends Controller
{
    public function index() {
        return view("dashboard.customer.manage.leads.index");
    }
}
