<?php

// app/Http/Middleware/SetEnterpriseFromToken.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\CurrentEnterprise;
use Illuminate\Support\Facades\Log;

class SetEnterpriseFromToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        Log::info('Middleware ejecutado', ['user' => optional($user)->id]);

        if ($user && $request->bearerToken()) {
            $token = $user->currentAccessToken();
            Log::info('Token encontrado', ['abilities' => $token?->abilities]);

            if ($token) {
                $enterpriseScope = collect($token->abilities)->first(fn($ability) => str_starts_with($ability, 'enterprise_id:'));

                if ($enterpriseScope) {
                    $enterpriseId = (int) explode(':', $enterpriseScope)[1];
                    CurrentEnterprise::set($enterpriseId);
                    Log::info('Enterprise ID seteado desde token', ['enterprise_id' => $enterpriseId]);
                }
            }
        }

        return $next($request);
    }
}
