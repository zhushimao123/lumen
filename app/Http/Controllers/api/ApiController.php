<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller as BaseController;
use app\model\user;

class ApiController extends BaseController
{
    //非对称解密
    public function rsa()
    {
        $data = file_get_contents("php://input");
        $json_str = base64_decode($data);
//        var_dump($json_str);
        $key = openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
//        var_dump($key);
//        $a = openssl_error_string();
//        var_dump($a);die;
        openssl_public_decrypt($json_str,$enty,$key);
//        echo $enty;
        $info = json_decode($enty,true);
        var_dump($info);
    }
    //非对称验证签名
    public  function autograph()
    {
       $sin =  $_GET['sin'];
        $b64 = base64_decode($sin);
//        echo "<br>";
        $json_str = file_get_contents("php://input");
        //验签
        $key = openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
//        var_dump($key);die;
        $a = openssl_verify($json_str,$b64,$key);
        var_dump($a);
    }
    //对称加密
    public function dncrypt()
    {
        $method = 'AES-256-CBC';
        $key = 'zsm123';
        $options = OPENSSL_RAW_DATA;
        $iv = 'aaaaaaaaaaaaaaaa';
        $json_str = file_get_contents("php://input");
        $b64 =base64_decode($json_str);
        $u = openssl_decrypt($b64,$method,$key,$options,$iv);
        $info = json_decode($u,true);
        var_dump($info);
    }
    //注册
    public function userinfo(Request $request)
    {
        $email = $_POST['email'];
        echo json_encode($email)die;
        $res = DB::table('t_user')->where(['email'=> $email])->first();

        if($res){
            $response = [
                'errno'=> '8000',
                'msg'=> '邮箱已存在'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
//        header('Access-Control-Allow-Origin:http://client.1809a.com');
//        $json_str = file_get_contents("php://input");
//
//        $b64 = base64_decode($json_str);
//        $key = openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
//
//
//        openssl_public_decrypt($b64,$enty,$key);
//        $info = json_decode($enty,true);
////        var_dump($info);die;
//        //存表
//        $result = DB::table('t_user')->insert($info);
//        if(!$result){
//            $response = [
//                'errno'=> '8001',
//                'msg'=> '注册失败'
//            ];
//            die(json_encode($response,true));
//        }else{
//            $res2 = DB::table('t_user')->where(['email'=> $info['email']])->first();
//            $uid = $res2-> uid;
////            var_dump($uid);
//            $token = $this-> getToken($uid);
//            $key = 'user:'.$uid;
//            $redis_key = Redis::get($key);
//            if($redis_key){
//                var_dump($redis_key);
//            }else{
//                Redis::set($key, $token);
//                Redis::expire($key, 604800);
//            }
//        }
    }
    //token信息
    protected function getToken($uid)
    {
        $str = substr(sha1(time() . Str::random(10) . $uid.$_SERVER['DB_HOST']), 5, 15);
        return $str;
    }
}
