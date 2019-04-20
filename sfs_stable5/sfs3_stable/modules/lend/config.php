<?php

//$Id: config.php 5679 2009-10-06 15:52:32Z infodaes $

include_once "../../include/config.php";
require_once "./module-cfg.php";

 //取得模組參數的類別設定
$m_arr = get_module_setup("lend");
extract($m_arr,EXTR_OVERWRITE);

$split_str='{*}';  //post給mail function要組合及分解的判斷依據

//取得教師身分證字號
$session_tea_sn = $_SESSION['session_tea_sn'];

//取得教職員職稱列表
$title_array=array();
$query ="select * from teacher_title";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
while(!$result->EOF)
{
	$title_array[$result->fields[teach_title_id]]=$result->fields[title_name];
	$result->MoveNext();
}

//取得教職員姓名與編號
$teacher_array=array();
$teach_id_array=array();
$query =" select a.teacher_sn,a.teach_id,a.name,a.teach_condition,b.teach_title_id from teacher_base a,teacher_post b where a.teacher_sn=b.teacher_sn ORDER BY b.teach_title_id";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
while(!$result->EOF)
{
	
	$teacher_array[$result->fields[teacher_sn]]['name']=$result->fields[name];
	$teacher_array[$result->fields[teacher_sn]]['condition']=$result->fields[teach_condition];
	$teacher_array[$result->fields[teacher_sn]]['title']=$title_array[$result->fields[teach_title_id]];
	
	if(!$result->fields[teach_condition]) $teach_id_array[$result->fields['teach_id']]=$result->fields[teacher_sn];
	
	$result->MoveNext();
}

//取得教職員email 加入$teacher_array中
$query =" select * from teacher_connect";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
while(!$result->EOF)
{
	$teacher_array[$result->fields[teacher_sn]]['email']=$result->fields[email];
	$result->MoveNext();
}


?>
