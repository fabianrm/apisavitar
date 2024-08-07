<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DatabaseSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientID = $request->header('X-Client-ID');
        Log::info('Soy el cliente Id: ' . $clientID);

        if ($clientID === 'savitar') {
            Config::set('database.default', 'mysql');
        } elseif ($clientID === 'corales') {
            Config::set('database.default', 'mysql_corales');
        }else{
            return 0;
        }
        return $next($request);
    }
}
