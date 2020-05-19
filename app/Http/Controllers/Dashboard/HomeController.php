<?php
declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Article;

class HomeController extends Controller
{
    public function showHomePage()
    {
        return view('dashboard.home', [
            'articleCount' => Article::count(),
        ]);
    }
}
