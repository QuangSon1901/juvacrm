<?php

namespace App\Http\Controllers\Dashboard\Account\Tranning\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index() {
        return view("dashboard.account.training.document.index");
    }

    public function blank() {
        return view("dashboard.account.training.classes.index");
    }
}
