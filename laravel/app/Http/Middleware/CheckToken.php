<?php

namespace App\Http\Middleware;

use Closure;

class CheckToken
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
        if(empty($token)){
            return response()->json([
                'code' => 400,
                'message' => '无token令牌'
            ]);
        }elseif ($token != 'leiwenfeng' && !empty($token)){
            return response()->json([
                'code' => 401,
                'message' => 'token令牌错误'
            ]);
        }
        return $next($request);
    }
}
