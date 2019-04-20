<?php
// $Id: create_data_config.php 7547 2013-09-19 03:35:30Z hami $
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "create_data_function.php";

/* 上傳檔案暫存目錄 */
$path_str = "temp/student/";
set_upload_path($path_str);
$temp_path = $UPLOAD_PATH.$path_str;
$menu_p = array("mstudent2.php"=>"匯入學生資料","trans_dos.php"=>"匯入教育廳版學生資料","explode_stu.php"=>"轉出萬豐版資料","old_seme.php"=>"補以前的學期資料");

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

//取得縣市鄉鎮轉 zip 陣列
function get_addr_zip_arr() {
	global $CONN;
	$query = "select zip,country,town from stud_addr_zip order by zip";
	$res= $CONN->Execute($query) or trigger_error("語法錯誤!",E_USER_ERROR);
	while(!$res->EOF){
		$addr =   $res->fields[1].$res->fields[2];
		$zip_arr[$addr]=$res->fields[0] ;
		//$zip_arr[$res->fields[0]] = $res->fields[1].$res->fields[2];
		$res->MoveNext();
	}
	return $zip_arr;
}
?>
