<?php

//$Id: config.php 9240 2018-05-04 03:25:04Z igogo $

include_once "../../include/config.php";
require_once "./module-cfg.php";

 //取得模組參數的類別設定

$m_arr = &get_module_setup("charge_class");
extract($m_arr,EXTR_OVERWRITE);

//取得教師所上年級、班級
$session_tea_sn = $_SESSION['session_tea_sn'] ;
$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
$row = $result->FetchRow() ;
$class_id=$row["class_num"];

$not_allowed="<CENTER><BR><BR><H2>您並非班級導師<BR>或者<BR>系統管理員尚未開放導師操作此功能!</H2></CENTER>";

?>
