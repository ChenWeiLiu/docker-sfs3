<?php

//$Id:$

include_once "../../include/config.php";
//require_once "./module-cfg.php";

 //取得模組參數的類別設定
$m_arr = get_module_setup("salary");

$Tr_BGColor=$m_arr['Tr_BGColor'];

//取得基本資料項目並轉換為陣列
$BasisData1_arr=split(',',$m_arr['BasisData1']);
$BasisData2_arr=split(',',$m_arr['BasisData2']);
//取得應給項目並轉換為陣列
$Mg_arr=split(',',$m_arr['Mg']);
//取得代收項目並轉換為陣列
$Mh_arr=split(',',$m_arr['Mh']);
//取得代扣項目並轉換為陣列
$Mi_arr=split(',',$m_arr['Mi']);

//取得教師身分證字號
$session_tea_sn = $_SESSION['session_tea_sn'] ;

$query =" select teach_person_id from teacher_base where teacher_sn='$session_tea_sn'";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
$row = $result->FetchRow() ;
$person_id=$row["teach_person_id"];

$empty_person_id="<CENTER><BR><BR><H2>您的身分證字號尚未設定於學務系統-教師管理內, 請向系統管理員查詢!</H2></CENTER>";

$is_admin = checkid($_SERVER[SCRIPT_FILENAME],1);
if ($is_admin)
	$menu_p = array('list.php'=>'薪資查詢','man.php'=>'資料設定與上傳');
else
	$menu_p = array('list.php'=>'薪資查詢');

