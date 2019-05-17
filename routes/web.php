<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
//非对称加密
$router->post('/rsa','api\ApiController@rsa');
//验证签名 qianming
$router->post('/qianming','api\ApiController@autograph');
//对称解密
$router->post('/dncrypt','api\ApiController@dncrypt');
//接受数据
$router->post('/userinfo','api\ApiController@userinfo');
$router->options('/userinfo',function(){
    return [];
});
//登陆
$router->post('/login','api\ApiController@login');
//个人中心
//$router->get('/users','api\ApiController@users')->Middleware('checklog');
$router->group(['middleware' => 'checklog'], function ($router){
    $router->get('/users','api\ApiController@users');
    $router->get('/goods','api\ApiController@goodslist');
    $router->get('/goodsinfo','api\ApiController@goodsinfo');
});


$router->get('/user','api\ApiController@user');


