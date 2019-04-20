<?php

// $Id: docup_config.php 5310 2009-01-10 07:57:56Z hami $

include "../../include/config.php";
include "../../include/sfs_case_dataarray.php";
include "../../include/sfs_case_PLlib.php";
include "./module-upgrade.php";

//設定上傳路徑
$filePath = set_upload_path("/school/docup");

//設定上傳路徑暫存檔案
$temp_filePath = set_upload_path("/tmp/docup");

//取得模組設定
$m_arr = get_sfs_module_set("docup");
extract($m_arr, EXTR_OVERWRITE);


/*	權限辨別函式
 *	@param $perr - string - 傳入三個字元第一字元為群組處室,第二字元為校內人員,第三字元為網路來賓
 *	@param $ki - integer - 	身分別(1-群組處室,2-校內人員,3-網路來賓)
 *	@param $pi - integer - 	編修別(1-瀏覽,2-修改,3-刪除)
 *	@return true or flase
 */
 
function getperr($perr,$ki,$pi){
	$vtemp = substr($perr,$ki-1,1);
	$vtemp = $vtemp >> ($pi-1);
	if ($vtemp % 2 == 1)
		return true;
	else
		return false;	
}

?>
