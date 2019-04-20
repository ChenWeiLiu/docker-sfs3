<?php
// $Id: config.php 6401 2011-03-30 05:50:15Z infodaes $

/*入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";

//引入函數
include "./my_fun.php";

//選單
$menu_p = array("scope_tol.php"=>"領域總表");
//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

//取得教師所上年級、班級
$session_tea_sn = $_SESSION['session_tea_sn'] ;
$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
$row = $result->FetchRow() ;
$class_id=$row["class_num"];

?>