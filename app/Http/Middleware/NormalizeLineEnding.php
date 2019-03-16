<?php

namespace App\Http\Middleware;

use Closure;

/**
 * リクエスト内の改行コードを正規化する。
 * @package App\Http\Middleware
 */
class NormalizeLineEnding
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
        $newInput = [];
        foreach ($request->input() as $key => $value) {
            $newInput[$key] = str_replace(["\r\n", "\r"], "\n", $value);
        }
        $request->replace($newInput);

        return $next($request);
    }
}
