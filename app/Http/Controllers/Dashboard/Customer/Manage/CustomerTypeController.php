<?php

namespace App\Http\Controllers\Dashboard\Customer\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    public function index() {
        return view("dashboard.customer.manage.customer_type.index");
    }
}
