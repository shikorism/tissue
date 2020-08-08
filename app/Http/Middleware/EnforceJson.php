<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Request headerに Accept: application/json を上書きする。APIエンドポイント用。
 */
class EnforceJson
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
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
