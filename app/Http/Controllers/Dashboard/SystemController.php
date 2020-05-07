<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    public function showOverviewPage()
    {
        return view('dashboard.sections.system.overview');
    }
}
