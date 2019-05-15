<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class Checklog
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(empty($_GET['uid']) || empty($_GET['token'])){
            exit('请登陆');
        }
        $key = 'usre:'.$_GET['uid'];
        $redis_key = Redis::get($key);
//        echo "<pre>";print_r($redis_key);echo "<pre>";die;
        if($_GET['token'] != $redis_key){
            exit('无效的token');
        }
        return $next($request);
    }


}
