<?php
//$Id: file_man.php 9240 2018-05-04 03:25:04Z igogo $
include "config.php";

//認證
sfs_check();

$d_arr=array("..",".htaccess","templates_c","system","Module_Path.txt","upgrade");
if (count($_POST['path'])>0) {
	$up=0;
	$max=count($_POST['path']);
	while(list($k,$v)=each($_POST['path'])) {
		$_POST['path'][intval($k)]=trim($v);
		if ($v==".." && $k==$max-1) $up=1;
	}
	if (!in_array($_POST['path'][0],$d_arr)) {
		$i=0;
		$temp_arr=array();
		reset($_POST['path']);
		while(list($k,$v)=each($_POST['path'])) {
			if ($v!="." && $v!=".." && ($up!=1 || $k!=$max-2) && $v!="") {
				$temp_arr[$i]=$v;
				$i++;
			}
		}
		$_POST['path']=$temp_arr;
		$dpath=implode("/",$_POST['path']);
	} else {
		$_POST['path']=array();
	}
}
$_POST['del']=trim(str_replace("\\","",$_POST['del']));
$_POST['del']=trim(str_replace("/","",$_POST['del']));
if ($_POST['del'] && file_exists($UPLOAD_PATH.$dpath."/".$_POST['del'])) unlink($UPLOAD_PATH.$dpath."/".$_POST['del']);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("url",$SFS_PATH_HTML.$UPLOAD_URL.$dpath);
$smarty->assign("module_name","檔案管理");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("rowdata",read_dir($dpath));
$smarty->display("system_file_man.tpl");

function read_dir($path="") {
	global $UPLOAD_PATH, $d_arr;

	$temp_arr=array();
	$full_path=$UPLOAD_PATH."/".$path;
	$handle=opendir($full_path);
	$i=0;
	while ($file = readdir($handle)) {
		if ($path!="" || !in_array($file,$d_arr)) {
			$temp_arr[$i][name]=$file;
			$temp_arr[$i][kind]=is_dir($full_path."/".$file)?"dir":"file";
		}
		$i++;
	}
	closedir($handle);
	return $temp_arr;
}
?>
