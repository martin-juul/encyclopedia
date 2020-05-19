<?php
declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    public function showOverviewPage()
    {
        return view('dashboard.sections.system.overview');
    }
}
