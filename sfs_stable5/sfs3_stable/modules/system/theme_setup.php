<?php
//$Id: theme_setup.php 5388 2009-02-10 07:14:59Z brucelyc $
include "config.php";

//認證
sfs_check();

$temp_file=$temp_path."/theme";

//讀設定
if (is_file($temp_file)) {
	$fp=fopen($temp_file,"r");
	while(!feof($fp)) {
		$temp_str=fgets($fp,50);
		$temp_arr=explode("=",$temp_str);
		if (!empty($temp_arr[0])) $temp[strtoupper($temp_arr[0])]=trim($temp_arr[1]);
	}
	if ($temp["FOLDER"]!=""&&$_POST['folder']=="") $_POST['folder']=$temp["FOLDER"];
	if ($temp["ICON"]!=""&&$_POST['icon']=="") $_POST['icon']=$temp["ICON"];
	fclose($fp);
	unlink($temp_file);
	if ($_POST['chk']=="") $_POST['chk']=1;
	if ($_POST['chki']=="" && $temp["ICON"]) $_POST['chki']=1;
}

//寫設定
if ($_POST['chk']) {
	$fp=fopen($temp_file,"w");
	fputs($fp,"FOLDER=".$_POST['folder']."\n");
	if ($_POST['chki']) fputs($fp,"ICON=".$_POST['icon']);
	fclose($fp);
	$FOLDER="folder_".$_POST['folder'].".png";
	$FOLDER_OPEN="folder_".$_POST['folder']."_open.png";
} else {
	$FOLDER="fc.gif";
	$FOLDER_OPEN="fo.gif";
}

//分類顏色陣列
$folder_id_arr=array("blue","cyan","green","grey","orange","violet","yellow");
$folder_value_arr=array("藍","青","綠","灰","橘","紫","黃");

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","登入圖片檢查");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("folder_id_arr",$folder_id_arr);
$smarty->assign("folder_value_arr",$folder_value_arr);
$smarty->display("system_theme_setup.tpl");
?>
