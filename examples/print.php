<?php
require_once __DIR__."/../vendor/autoload.php";

$app_id = '80000××';

$app_secret = '××××××××××××××××××××××××××××';

$device_id = '123457';

//$device_id = '12346434';

$device_secret = 'jnxiaer7';

//=============================================
//将以上参数替换成开发者的实际参数
//=============================================
//$device_secret = 'd15ebbny';

$rpc = new \zhongwu\protocol\RpcClient($app_id, $app_secret, 'http://api.zhongwuyun.com');

$Zprinter = new \zhongwu\Printer($rpc);



$printdata = '中午云拥有自主研发的云打印机，提供稳定高效，高可用的云打印方案';

$printdata = '张三';

try {

    var_dump($Zprinter->set_args($device_id, $device_secret)->cloud_print($printdata));

} catch (Exception $e) {
    echo $e;
}