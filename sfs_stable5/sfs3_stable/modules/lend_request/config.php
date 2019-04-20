<?php

//$Id: config.php 6732 2012-03-28 01:54:06Z infodaes $

include_once "../../include/config.php";
require_once "./module-cfg.php";
require_once "./module-upgrade.php";

 //取得模組參數的類別設定
$m_arr = get_module_setup("lend_request");
extract($m_arr,EXTR_OVERWRITE);

//取得教師身分證字號
$session_tea_sn = $_SESSION['session_tea_sn'];

//取得教師EMAIL
$query ="select * from teacher_connect where teacher_sn=$session_tea_sn";
$result = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
$teacher_email=$result->fields['email'];

//取得教職員職稱列表
$title_array=array();
$query =" select * from teacher_title";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
while(!$result->EOF)
{
	$title_array[$result->fields[teach_title_id]]=$result->fields[title_name];
	$result->MoveNext();
}

//取得教職員姓名與編號
$teacher_array=array();
$query =" select a.teacher_sn,a.name,a.teach_condition,b.teach_title_id from teacher_base a,teacher_post b where a.teacher_sn=b.teacher_sn";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
while(!$result->EOF)
{
	$teacher_array[$result->fields[teacher_sn]]['name']=$result->fields[name];
	$teacher_array[$result->fields[teacher_sn]]['condition']=$result->fields[teach_condition];
	$teacher_array[$result->fields[teacher_sn]]['title']=$title_array[$result->fields[teach_title_id]];
	$result->MoveNext();
}


//echo "<PRE>";
//print_r($teacher_array);
//echo "</PRE>";
//exit;

?>
