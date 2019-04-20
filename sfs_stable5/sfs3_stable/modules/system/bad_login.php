<?php
//$Id: bad_login.php 9136 2017-09-01 08:07:32Z smallduh $
include "config.php";

//認證
sfs_check();

check_table_bad_login();

$temp_file=$temp_path."/bad_login_protect";
if ($_POST[clean]) {
	$CONN->Execute("delete from bad_login");
}



$query="select * from bad_login order by log_time desc,log_ip,log_id";
$res=$CONN->Execute($query);
$smarty->assign("rowdata",$res->GetRows());

if ($_POST[export]) {
	header("Content-type: application/csv; Charset=Big5");
	header("Content-Disposition: attachment; filename=bad_login.csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	$smarty->display("system_bad_login_csv.tpl");
	exit;
}

//讀設定
if (is_file($temp_file)) {
	$fp=fopen($temp_file,"r");
	$k=fgets($fp,10);
	fclose($fp);
	unlink($temp_file);
	if ($_POST[lock]=="") $_POST[lock]=1;
}

if (intval($_POST[err_times])<1) {
	$_POST[err_times]=($k)?$k:3;
}

//寫設定
if ($_POST[lock]) {
	$fp=fopen($temp_file,"w");
	fputs($fp,$_POST[err_times]);
	fclose($fp);
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","登入失敗記錄");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->display("system_bad_login.tpl");
?>
