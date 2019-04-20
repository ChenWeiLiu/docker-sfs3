<?php
//$Id: secure_path.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
global $UPLOAD_PATH ;
@unlink($UPLOAD_PATH.'/Module_Path.php');

//利用  .htaccess 限定 data 下 的檔案不可執行 php程式
	$fp = fopen ($UPLOAD_PATH.".htaccess", "aw") or user_error("無法開啟 $UPLOAD_PATH 目錄",256);
	fputs($fp, "php_flag engine off"); 
	fclose($fp); 
?>
