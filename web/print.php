<?php
require_once __DIR__ . "/../vendor/autoload.php";

$app_id = $_POST['appid'];

$app_secret = $_POST['appsecret'];

$device_id = $_POST['device_id'];

$device_secret = $_POST['device_secret'];

$rpc = new \zhongwu\protocol\RpcClient($app_id, $app_secret, 'http://api.zhongwuyun.com');

$Zprinter = new \zhongwu\Printer($rpc);


$printdata = empty($_POST['printdata']) ? '中午云拥有自主研发的云打印机，提供稳定高效，高可用的云打印方案' : $_POST['printdata'];


try {

    var_dump($Zprinter->set_args($device_id, $device_secret)->cloud_print($printdata));

} catch (Exception $e) {

    echo $e;

}