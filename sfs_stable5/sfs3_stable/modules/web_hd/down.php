<?php
// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

//轉換成全域變數
$file=($_POST['file'])?"{$_POST['file']}":"{$_GET['file']}";
//$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";

//是本人嗎？
$sql="select teacher_sn,source_name from hd_file where file_sn='$file' ";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);
$teacher_sn=$rs->fields['teacher_sn'];
$source_name=$rs->fields['source_name'];
if($teacher_sn!=$_SESSION['session_tea_sn']) trigger_error("","你沒有權限下載本檔案！");
else{
	if(!is_dir($UPLOAD_PATH."web_hd/tmp")) mkdir ($UPLOAD_PATH."web_hd/tmp", 0700);
	if(!is_dir($UPLOAD_PATH."web_hd/tmp/{$_SESSION['session_tea_sn']}")) mkdir ($UPLOAD_PATH."web_hd/tmp/{$_SESSION['session_tea_sn']}", 0700);
	$handle=opendir($UPLOAD_PATH."web_hd/tmp/{$_SESSION['session_tea_sn']}");
	while ($file2 = readdir($handle)) {
		unlink($UPLOAD_PATH."web_hd/tmp/{$_SESSION['session_tea_sn']}/$file2");
	}
	closedir($handle);

	copy ($UPLOAD_PATH."web_hd/$file",$UPLOAD_PATH."web_hd/tmp/{$_SESSION['session_tea_sn']}/$source_name");

	header("Location:{$UPLOAD_URL}web_hd/tmp/{$_SESSION['session_tea_sn']}/$source_name");

}
?>
