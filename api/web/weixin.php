<?php
$signature = $_GET["signature"];
$timestamp = $_GET["timestamp"];
$nonce = $_GET["nonce"];
$echostr = $_GET["echostr"];
file_put_contents('1.txt',var_export($_GET,true));
$token = 'wpjiucaishuo';
$tmpArr = array($token, $timestamp, $nonce);
sort($tmpArr, SORT_STRING);
$tmpStr = implode( $tmpArr );
$tmpStr = sha1( $tmpStr );
$new['echostr'] = $echostr;
if( $tmpStr == $signature ){
   echo $echostr;
}else{
    return false;
}