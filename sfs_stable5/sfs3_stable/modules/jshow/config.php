<?php
// $Id: board_config.php 7779 2013-11-20 16:09:00Z smallduh $

	//系統設定檔
	include_once "./module-cfg.php";
	include_once "../../include/config.php";
	require_once "../../include/sfs_case_PLlib.php";
	require_once "../../include/sfs_case_dataarray.php";

  //模組更新檔
  include "module-upgrade.php";
	
	//載入自己的函式
	include_once "my_functions.php";

//取得模組設定
$m_arr = &get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

/* 上傳檔案目的目錄 */
$path_str = "school/jshow/";
set_upload_path($path_str);

$USR_DESTINATION = $UPLOAD_PATH.$path_str;
$USR_DESTINATION_URL =$UPLOAD_URL.$path_str;
/*下載路徑 */
$download_path = $UPLOAD_URL.$path_str;

//讀取可上傳附件大小的值 '' 
	$query="SELECT @@global.max_allowed_packet";
	$res=$CONN->Execute($query);
	$M1=$res->fields(0);  //MySQL
	$M1=floor($M1/(1024*1024));
	
	$M2=ini_get('post_max_size');
	$M2=substr($M2,0,strlen($M2)-1);
	
	$M3=ini_get('upload_max_filesize');
	$M3=substr($M3,0,strlen($M3)-1);
	
	$Max_upload=round(min($M1,$M2,$M3)/1.34,2);
	

$DISPLAY_M[0]="此分類圖片依序秀出";
$DISPLAY_M[1]="此分類圖片依亂數秀出";
$DISPLAY_M[2]="依指定日期秀出此分類特定圖片";

$MON=array(1=>31,2=>29,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

?>
