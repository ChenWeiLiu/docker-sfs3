<?php
//$Id: chk_login_img.php 7927 2014-03-13 06:18:04Z brucelyc $
include "config.php";

//認證
sfs_check();

$temp_file=$temp_path."/chk_login_img";

//讀設定
if (is_file($temp_file)) {
	$c=chk_login_img("","",2);
	if (empty($_POST['font'])) $_POST['font']=$c['FONT'];
	if ($_POST['font_no']=="") $_POST['font_no']=$c['FONT_NO'];
	if ($_POST['dot']=="") $_POST['dot']=$c['DOT'];
	if ($_POST['slope']=="") $_POST['slope']=$c['SLOPE'];
	if ($_POST['color']=="") $_POST['color']=$c['COLOR'];
	if ($_POST['type']=="") $_POST['type']=$c['TYPE'];
	unlink($temp_file);
	if ($_POST[chk]=="") $_POST[chk]=1;
} else {
	$_POST['font']="";
	$_POST['font_no']="";
	$_POST['dot']="";
	$_POST['slope']="";
	$_POST['color']="";
	$_POST['type']="";
}

//寫設定
if ($_POST[chk]) {
	$fp=fopen($temp_file,"w");
	fputs($fp,"FONT=".$_POST['font']."\n");
	fputs($fp,"FONT_NO=".$_POST['font_no']."\n");
	fputs($fp,"DOT=".$_POST['dot']."\n");
	fputs($fp,"SLOPE=".$_POST['slope']."\n");
	fputs($fp,"COLOR=".$_POST['color']."\n");
	fputs($fp,"TYPE=".$_POST['type']."\n");
	fclose($fp);
}
chk_login_img("","",2);
$font_arr = array(0=>'Harvey',1=>'Sir',2=>'Epilog',3=>'Hotshot',4=>'arial');
$smarty->assign('font_arr',$font_arr);
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","登入圖片檢查");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->display("system_chk_login_img.tpl");
?>
