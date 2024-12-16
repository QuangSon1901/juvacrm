<?php

namespace App\Http\Controllers\Dashboard\Customer\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function detail() {
        return view("dashboard.customer.client.detail");
    }

    public function leads() {
        return view("dashboard.customer.client.leads");
    }
}
