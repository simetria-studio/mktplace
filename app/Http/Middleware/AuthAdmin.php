<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->guard('admin')->viaRemember()){
            return redirect()->route('dashboard');
        }

        if ( !auth()->guard('admin')->check() ) return redirect()->route('admin.login');

        return $next($request);
    }
}
