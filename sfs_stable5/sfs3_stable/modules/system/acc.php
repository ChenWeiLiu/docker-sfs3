<?php
//$Id: acc.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

$path_str="/system";
$temp_path=$UPLOAD_PATH.$path_str;
$temp_file=$temp_path."/speed_smarty";
$_POST['dir_name']=stripslashes($_POST['dir_name']);

if ($_POST[test]) {
	$dir_name=$_POST['dir_name'];
	set_upload_path($path_str);
	if (is_dir($dir_name)) {
		if (is_writeable($dir_name)) {
			mkdir($dir_name."/templates_c", 0755);
			$fp=fopen($temp_file,"w");
			$k=fputs($fp,$dir_name);
			fclose($fp);
			$err_msg=($k)?"寫入成功\":"寫入失敗";
		} else {
			$err_msg="目錄無法寫入";
		}
	} else {
		$err_msg="目錄不存在";
	}
}

if ($_POST[cancel]) {
	if (is_file($temp_file)) {
		unlink($temp_file);
	}
}

if (is_file($temp_file)) {
	$fp=fopen($temp_file,"r");
	$_POST['dir_name']=fgets($fp,1024);
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","系統加速設定");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("err_msg",$err_msg);
$smarty->assign("status",is_file($temp_file));
$smarty->display("system_acc.tpl");
?>
