<?php
// $Id: act_data_config.php 5310 2009-01-10 07:57:56Z hami $
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
//include_once "../../include/sfs_core_menu.php";
//include_once "../../include/sfs_core_schooldata.php";
include_once "act_data_function.php";

/* 上傳檔案暫存目錄 */
$path_str = "temp/student/";
set_upload_path($path_str);
$temp_path = $UPLOAD_PATH.$path_str;
$menu_p = array("act_stu_data.php"=>"匯出健康中心資料");

//取得縣市鄉鎮陣列
function get_zip_arr() {
	global $CONN;
	$query = "select zip,country,town from stud_addr_zip order by zip";
	$res= $CONN->Execute($query) or trigger_error("語法錯誤!",E_USER_ERROR);
	while(!$res->EOF){
		$zip_arr[$res->fields[0]] = $res->fields[1].$res->fields[2];
		$res->MoveNext();
	}
	return $zip_arr;
}

?>