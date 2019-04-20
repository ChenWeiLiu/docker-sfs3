<?php

// $Id: index.php 8627 2015-12-07 15:52:49Z qfon $

//啟動 session
//session_start();

/* 取得學務系統設定檔 */
// 1. 引入 include/config.php
// 2. config.php 引入 sfs_API.php (主要功能函式檔)
// 3. sfs_API.php 再引入 $THEME_FILE_function.php
if (is_file('./include/config.php'))
	require "include/config.php";
if (empty($SFS_PATH)){
	if(is_readable('./install.php'))
		header("Location: install.php");
	else {
		check_text_table("安裝程式錯誤!","請先將 ".dirname(__FILE__)."/install.php 設為可讀取");
		exit;
	}

}

//  --程式檔頭
head("首頁","",1);

//列出模組
// print_module 這個函式是在各個 THEME 的 $THEME_FILE_function.php 中來定義
sys_check();

print_module($_REQUEST['_Msn'],"",$nocols);

//找出管理者姓名
$str=get_admin_name();
$ax=get_module_setup("chang_root");
if ($ax["root_homeview"]==1)$str="";


//去除彰化縣顯示網管姓名
if ($SCHOOL_BASE['sch_sheng']=='彰化縣') $str='';

//  --程式檔尾
foot($str);


function sys_check(){
	global $SFS_PATH;
	$text = '';
	if(is_writable ($SFS_PATH."/include/config.php")){
		$text = "<li>您的 $SFS_PATH"."/include/config.php 檔目前是可以寫入的，請將之屬性改為唯讀。</li>";
	}
//	if(is_readable('./install.php'))
//		$text .= "<li>請移除 $SFS_PATH"."/install.php 這支安裝程式,<BR /> 或改為不可讀取 chmod 600 $SFS_PATH"."/install.php </li>";
	if ($text<>''){
		check_text_table("系統安全警告",$text);
		foot();
		exit;
	}

}

function check_text_table($title,$msg){
	echo "
	<table style='background-gcolor:#ED4112;margin:auto' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FDD235'>
	<td>$title</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<td>
	$msg
	</td></tr>
	</table>
	";
}
?>
