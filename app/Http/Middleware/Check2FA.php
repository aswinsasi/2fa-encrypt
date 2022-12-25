<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class Check2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    { //dd("j");
        if (!Session::has('user_2fa') && auth()->user()) {
            return redirect()->route('2fa.index');
        }else if(!auth()->user()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
