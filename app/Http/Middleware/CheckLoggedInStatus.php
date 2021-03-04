<?php

namespace App\Http\Middleware;

use Closure;

class CheckLoggedInStatus
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
        if (isset($request->member) || isset($request->administrator)) {
        } else {
            return redirect()->action("Member\\LoginController@index");
        }
        return $next($request);
    }
}
