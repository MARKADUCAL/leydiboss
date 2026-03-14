<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $title = 'Dashboard — Admin';

        return view('pages.admin.sections.dashboard', compact('admin', 'title'));
    }
}
