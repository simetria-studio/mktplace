<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $blockedIps = [
            '45.84.197.198', // Adicione os IPs que deseja bloquear
        ];

        if (in_array($request->ip(), $blockedIps)) {
            // Retorne uma resposta de erro ou redirecione para outra página
            return response()->json(['message' => 'Seu IP está bloqueado.'], 403);
        }

        return $next($request);
    }
}
