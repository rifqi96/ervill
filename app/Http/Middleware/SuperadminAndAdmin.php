<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperadminAndAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() &&  (Auth::user()->role->name == 'admin' || Auth::user()->role->name == 'superadmin')) {
            return $next($request);
        }

        if(Auth::user()){
            Auth::logout();
        }

        return redirect()->intended('/')->withErrors(['Anda tidak memiliki hak akses page ini']);
    }
}
