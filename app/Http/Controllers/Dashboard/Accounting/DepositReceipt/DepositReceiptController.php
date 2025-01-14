<?php

namespace App\Http\Controllers\Dashboard\Accounting\DepositReceipt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepositReceiptController extends Controller
{
    public function index() {
        return view("dashboard.accounting.deposit_receipt.index");
    }
}
