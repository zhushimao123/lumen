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
//        header('Access-Control-Allow-Origin:*');
//        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); //支持的http动作
//        header('Access-Control-Allow-Headers:x-requested-with,content-type');  //响应头 请按照自己需求添加。

        $email = $_POST['name'];
        $pass = $_POST['pass'];
        $post_arr = [
            'email' =>  $email,
            'pass' => $pass
        ];
        $post_json = json_encode($post_arr);
        //url
        $url = 'http://www.mneddx.com/appuser';
        //初始化 创建新资源
        $ch = curl_init();
        // 设置 URL 和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);
        //发送post请求
        curl_setopt($ch, CURLOPT_POST, 1);
        //禁止浏览器输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //发送数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        //字符串文本
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:text/plain']);
        //抓取 URL 并把它传递给浏览器
        $rs = curl_exec($ch);  //data 数据
        echo $rs;
        //错误码
//        var_dump(curl_error($ch));
        // 关闭 cURL 资源，并且释放系统资源
        curl_close($ch);

//        $json_str = file_get_contents("php://input");
//
//        $b64 = base64_decode($json_str);
//        $key = openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
//
//
//        openssl_public_decrypt($b64,$enty,$key);
//        $info = json_decode($enty,true);
////        var_dump($info);die;

        //else{
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
    //登陆
    public function login()
    {
        $email = $_POST['name'];
        $pass = $_POST['pass'];
        $post_arr = [
            'email' =>  $email,
            'pass' => $pass
        ];
        $post_json = json_encode($post_arr);
        $url = 'http://www.mneddx.com/applogin';
        //初始化
        $ch = curl_init();
        // 设置 URL 和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);
        //发送post请求
        curl_setopt($ch, CURLOPT_POST, 1);
        //禁止浏览器输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //发送数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        //字符串文本
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:text/plain']);
        //抓取 URL 并把它传递给浏览器
        $rs = curl_exec($ch);  //data 数据
        echo $rs;
        //错误码
//        var_dump(curl_error($ch));
        // 关闭 cURL 资源，并且释放系统资源
        curl_close($ch);

    }
    //个人中心
    public function users()
    {
        $uid = $_GET['uid'];
        $res = DB::table('t_user')->where(['uid'=> $uid])->first();
        if($res){
            $response = [
                'msg'=> $res,
                'erron' => 'ok'
            ];
            echo json_encode($response);die;
        }else{
            $response = [
                'msg'=> '用户不存在',
                'erron' => 'no'
            ];
            echo json_encode($response);die;
        }

    }
    //商品列表
    public function  goodslist()
    {
        $goodsinfo = DB::table('shop_goods')->where(['goods_new'=>1])->get();
        $response = [
           'errno' => 'ok',
            'data' => [
                'goodsinfo'=> $goodsinfo
            ]
        ];
        die(json_encode($response,JSON_UNESCAPED_UNICODE));
    }
    //商品详情
    public function  goodsinfo(){
       $goods_id =  $_GET['goods_id'];
       if(!$goods_id){
           $response = [
               'errno' => 'no',
                'msg' => '此商品信息不存在'
           ];
           die(json_encode($response,JSON_UNESCAPED_UNICODE));
       }
       $info = DB::table('shop_goods')->where(['goods_id'=>$goods_id])->first();
        $response = [
            'errno' => 'ok',
            'data' => [
                'info'=> $info
            ]
        ];
        die(json_encode($response,JSON_UNESCAPED_UNICODE));
    }
    //购物车
    public function goodscart()
    {
      $goods_id =  $_GET['goods_id']; //商品id
      $user_id = $_GET['uid'];  //用户id
      $buy_number = $_GET['buy_number'];
      $info =[
          'goods_id' => $goods_id,
          'buy_number' => $buy_number,
          'user_id' => $user_id
      ];
        $url = 'http://www.mneddx.com/appcart';
      $this-> getcurl($info,$url);
    }
    //购物车展示
    public  function cartlist()
    {
       $user_id = $_GET['uid'];
        $where = [
            'cart_status'=>1,
            'user_id' => $user_id,
        ];
        $arr = DB::table('shop_cart')->join('shop_goods','shop_goods.goods_id','=','shop_cart.goods_id')->where($where)->get();
        $response = [
            'errno' => 'ok',
            'data' => [
                'info'=> $arr
            ]
        ];
        die(json_encode($response,JSON_UNESCAPED_UNICODE));
    }
    //总价
    public  function countprice(Request $request)
    {
       $g_id =  $request-> all();
       $goods_id = implode($g_id);
       $goods = explode(',',$goods_id);
       $cartinfo = DB::table('shop_cart')->whereIn('goods_id',$goods)->get();
       $goodsinfo = DB::table('shop_goods')->whereIn('goods_id',$goods)->get();
       $contprice = 0;
       foreach($cartinfo as $k=>$v){
           foreach ($goodsinfo as $key=>$val){
               if($v-> goods_id == $val-> goods_id){
                   $contprice = $contprice + $v-> buy_number * $val-> goods_price;
               }
           }
       }
       $respose = [
           'errno'=> 'ok',
           'msg' => $contprice
       ];
        echo json_encode($respose);die;
    }
    //清空购物车
    public function delecart()
    {
        $res = DB::table('shop_cart')->delete();
    }
    //点击结算
    public function order()
    {
//        var_dump($_GET);
        $goods_id = $_GET['goods_id'];

        $user_id = $_GET['uid'];

        $goods = explode(',',$goods_id);
        //确认订单
        $orderinfo = DB::table('shop_cart')->whereIn('shop_cart.goods_id',$goods)
            ->where(['user_id'=>$user_id])->join('shop_goods','shop_cart.goods_id','=','shop_goods.goods_id')
            ->get();
//        var_dump($orderinfo);die;
        $countprice = 0;
        foreach ($orderinfo as $k=> $v){
            $countprice = $countprice + $v-> goods_price * $v-> buy_number;
        }
        $response = [
            'errno' => 'ok',
            'countprice'=> $countprice,
            'data'=> [
                'orderinfo'=> $orderinfo
            ]
        ];
        die(json_encode($response,JSON_UNESCAPED_UNICODE));
    }
    //确认订单
    public function orderdo(Request $request)
    {

        $goods_id = $request-> goods_id;
        $countprice = $request-> countprice;
        $pay_type = $request-> pay_type;
        $g_id = explode(',',$goods_id);
        if(!$g_id){
            $response=[
                'errno'=> 'no',
                'msg' => '请选择一个商品'
            ];
            echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        $pay = [1,2];
        if(!in_array($pay_type,$pay)){
            $response=[
                'errno'=> 'no',
                'msg' => '请选择一个支付方式'
            ];
            echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        //存入订单表
        $user_id = $_GET['user_id'];
        $orderinfo['user_id']=$user_id;
        $orderinfo['order_amount'] = $countprice;
        $orderinfo['order_no'] = $this-> getorderno();
        $orderinfo['create_time'] = time();
        $orderinfo['pay_type'] = $pay_type;
        $res = DB::table('shop_order')->insert($orderinfo);
        if(!$res){
            $response=[
                'errno'=> 'no',
                'msg' => '订单生成失败'
            ];
            echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        //订单详情
        $res2 = DB::table('shop_cart')->whereIn('shop_cart.goods_id',$g_id)->where(['user_id'=>$user_id])
            ->join('shop_goods','shop_cart.goods_id','=','shop_goods.goods_id')->get();
        var_dump($res2);
    }
    //订单号
    public function getorderno()
    {
        $str = substr(sha1(time() . Str::random(10).'PIKAI_'."_"), 5, 15);
        return $str;

    }
    //curl
    public function getcurl($info,$url)
    {

        $post_json = json_encode($info);
        //初始化 创建新资源
        $ch = curl_init();
        // 设置 URL 和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);
        //发送post请求
        curl_setopt($ch, CURLOPT_POST, 1);
        //禁止浏览器输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //发送数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        //字符串文本
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:text/plain']);
        //抓取 URL 并把它传递给浏览器
        $rs = curl_exec($ch);  //data 数据
        echo $rs;
        //错误码
        $errno = curl_error($ch);
        if($errno){
            $response = [
                'errno' =>"error no 6211"
            ];
            die(json_encode($response));
        }
        // 关闭 cURL 资源，并且释放系统资源
        curl_close($ch);

    }
}
