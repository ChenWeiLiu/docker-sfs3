<?php
//$Id$
ini_set ('display_errors', 'off');
//預設的引入檔，不可移除。
include_once "../../include/config.php";
require_once "./module-cfg.php";
//您可以自己加入引入檔
// 
//結合學務系統, 非完全真正 UTF8 , 會變亂碼
//mysql_query("SET NAMES 'utf8'");

//檔案下載路徑 字串末要保留 / 符號
$path_str = "school_msn/";
set_upload_path($path_str);

$download_path=$UPLOAD_PATH."school_msn/"; 

//電子圖檔上傳地點
$UPLOAD_PIC=$UPLOAD_PATH."school_msn/pic/";
$UPLOAD_PIC_URL =$UPLOAD_URL."school_msn/pic/";
if (!is_dir($UPLOAD_PIC))	mkdir($UPLOAD_PIC,0700);

$PHP_FILE_ATTR="jpg;jpeg;png;gif;swf;wmv";

//讀取模組變數 $MAX_MB , $PRESERVE_DAYS , $CLEAN_MODE
$m_arr = &get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE); 

//檢驗是否為內部IP
$insite_arr = explode(",",$insite_ip);
$is_home_ip = check_home_ip($insite_arr);

//讀取可上傳附件大小的值 '' 
	$M1=ini_get('post_max_size');
	$M1=substr($M1,0,strlen($M1)-1);
	
	$M2=ini_get('upload_max_filesize');
	$M2=substr($M2,0,strlen($M2)-1);
	
	$MAX_MB=round(min($M1,$M2),2);  //單位 MB

//變數
$ONLINE[0]="離線";
$ONLINE[1]="上線";

?>
