<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TelegramToken
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

        $hash = md5(env('TELEGRAM_BOT_TOKEN'));

        if($request->hash != $hash) {
            return response()->json(['Unauthorized'], 401);
        }

        return $next($request);
    }
}
