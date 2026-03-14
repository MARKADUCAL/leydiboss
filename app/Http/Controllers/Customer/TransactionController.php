<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        return view('pages.customer.sections.transaction');
    }

    public function show(int $id)
    {
        return view('pages.customer.sections.transaction', compact('id'));
    }
}