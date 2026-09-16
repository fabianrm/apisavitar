<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Models\RouterMetric;

class RouterMetricController extends Controller
{
    public function index(Router $router)
    {
        $latest = RouterMetric::where('router_id', $router->id)
            ->latest('recorded_at')
            ->first();

        $history = RouterMetric::where('router_id', $router->id)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at')
            ->get(['cpu_load', 'mem_used', 'mem_total', 'disk_used', 'disk_total', 'recorded_at']);

        return response()->json([
            'router' => [
                'id' => $router->id,
                'ip' => $router->ip,
            ],
            'connectivity' => $router->connectivityStatus(),
            'latest' => $latest,
            'history' => $history,
        ]);
    }
}
