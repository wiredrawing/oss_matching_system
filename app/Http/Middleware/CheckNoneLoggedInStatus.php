<?php

namespace App\Http\Middleware;

use Closure;

class CheckNoneLoggedInStatus
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
        // このミドルウェアを通るとき、ログイン済みの場合は強制的にマイページへ
        if (isset($request->member) === true) {
            return redirect()->action("Member\\IndexController@index");
        }
        return $next($request);
    }
}
