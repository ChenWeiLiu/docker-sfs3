<?php

// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$mang=($_POST['mang'])?"{$_POST['mang']}":"{$_GET['mang']}";
$new_dir_name=($_POST['new_dir_name'])?"{$_POST['new_dir_name']}":"{$_GET['new_dir_name']}";
$upd_dir_name=($_POST['upd_dir_name'])?"{$_POST['upd_dir_name']}":"{$_GET['upd_dir_name']}";
$file_arr=$_POST['file_arr'];
$comment=$_POST['comment'];
$moveto=$_POST['moveto'];
$new_name=$_POST['new_name'];
//$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";



//
//您的程式碼由此開始
$q_mess=over_quota();

//新增一個子目錄
if($mang=="new_sub" && $q_mess[0]) new_sub_fun($act,$new_dir_name);

//修改目錄名稱
elseif($mang=="upname_dir") upd_sub_fun($act,$upd_dir_name);

//刪除本目錄
elseif($mang=="del_dir") {
	$ms_arr=del_sub_fun($act);
	if($ms_arr[0]=="sn") $act=$ms_arr[1];
	elseif($ms_arr[0]=="string") $ms=$ms_arr[1];
}

//刪除所選擇的檔案
elseif($mang=="刪除選擇") $act=del_file_fun($act,$file_arr);

//上傳新檔案到目前的目錄
 elseif($mang=="上傳" && $q_mess[0]) {
	$act=upload_file($act);
	header("Location:{$_SERVER['PHP_SELF']}?act=$act");
 }

  elseif($mang=="搬移到") {
	$act=move_file($act,$file_arr,$moveto);
	header("Location:{$_SERVER['PHP_SELF']}?act=$act");
 }

 elseif($mang=="更名為") {
	$act=rename_file($act,$file_arr,$new_name);
	header("Location:{$_SERVER['PHP_SELF']}?act=$act");
 }
$sql="select count(*) from hd_dir where teacher_sn='{$_SESSION['session_tea_sn']}' and struct='0' and enable=1 ";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);

//第一次進入時，自動建立個人的根目錄
if($rs->fields['0']==0) {
	$sql="INSERT INTO hd_dir ( struct , chinese_name , teacher_sn , level , enable ) VALUES ( '0', '根目錄', '{$_SESSION['session_tea_sn']}', 'a', '1')";
	$CONN->Execute($sql) or trigger_error($sql,256);
	header("Location:./");
}
// 叫用 SFS3 的版頭
head("網路硬碟");
//目錄畫面
 $left=reco ();

//檔案畫面
$right=file_list($act,$ms);

$dir_mag=dir_mag($act);
$file_mag=file_mag($act);
$q_mess=over_quota();







$main="<table bgcolor='#A2A2A2' cellspacing='1' cellpadding='5' width='100%'><tr bgcolor='#E7E7E7'><td colspan='2'>  $q_mess[1] </td></tr><tr><td valign='top' width='30%' bgcolor='#E9E9E9' nowrap>$left<p></p><table bgcolor='#211BC7' cellspacing='1' width='100%'><tr bgcolor='#CFD4FF'><td>$dir_mag</td></tr></table></td><td valign='top' width='70%' bgcolor='#FFFFFF'>$right<table bgcolor='#211BC7' cellspacing='1' width='100%'><tr bgcolor='#CFD4FF'><td>$file_mag</td></tr></table></td></tr></table>\n";
echo $main;


// SFS3 的版尾
foot();

?>
<script language="JavaScript1.2">

	function clear1(oj){
		oj.value='';
		oj.focus();
	}

</script>
