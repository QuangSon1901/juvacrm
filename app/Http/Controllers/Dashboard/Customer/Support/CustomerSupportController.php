<?php

namespace App\Http\Controllers\Dashboard\Customer\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerSupportController extends Controller
{
    public function index() {
        return view("dashboard.customer.support.index");
    }

    public function consultation() {
        return view("dashboard.customer.support.consultation");
    }
}
