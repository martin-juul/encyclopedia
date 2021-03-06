<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Sys\PostgresDatabase;
use App\Models\Sys\ProfileReport;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        return view('statistics.index', [
            'dbtables' => PostgresDatabase::getAllTableSizes(),
            'routes'   => \Route::getRoutes()->getRoutesByName(),
        ]);
    }

    public function showProfile(Request $request, string $id)
    {
        $profile = ProfileReport::whereId($id)->firstOrFail();
        $report = $profile->getReport();

        return view('profiles.xhprof.report', [
            'id'        => $profile->id,
            'context'   => $profile->context,
            'timestamp' => (string)$profile->created_at,
            'report'    => $report->original,
            'runtime'   => $report->getMainRuntime(),
        ]);
    }
}
