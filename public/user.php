<?php
$info =[
    'name'=> 'qiuqiu',
    'pass' => '******',
    'email'=> 'admin123@163.com'
];
$js_str = json_encode($info);
$a = 'userinfo('.$js_str.')';
echo $a;