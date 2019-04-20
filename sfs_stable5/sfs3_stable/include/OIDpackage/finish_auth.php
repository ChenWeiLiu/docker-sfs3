<?php

require_once "commonclass.php";
$obj = new TC_OID_BASE();
session_start();

if( $obj->getResponseStatus($msg) >0) {
  $arr= $obj->getResponseArray();
  header("Content-Type:text/html; charset=utf-8");
  print "<pre>";
  print_r($arr);
  /*
Array
(
    [identity] => http://example.openid.tc.edu.tw/
    [fullname] => 張OO
    [email] => example@tc.edu.tw
    [schooldistrict] => 西屯區
    [schoolname] => OO國中
    [schooltitle] => 專任教師
    [schooltype] => 市立國民中學
)
  */
}else print $msg;


?>
