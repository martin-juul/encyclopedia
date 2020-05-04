<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function showHomePage()
    {
        return view('dashboard.home');
    }
}
