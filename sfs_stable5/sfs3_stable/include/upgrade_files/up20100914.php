<?php

//$Id: up20100914.php 6187 2010-09-22 16:43:55Z brucelyc $

if(!$CONN){
        echo "go away !!";
        exit;
}

up_ver();

function up_ver() {
	global $SFS_PATH, $SFS_VER_DECLARE, $UPLOAD_PATH;

	if ( function_exists('version_compare') && version_compare( phpversion(), '5.0.0', '>=' ) ) {
	
		if (file_exists("$SFS_PATH/sfs-release.php")) {
			include_once "$SFS_PATH/sfs-release.php";
		}
		if (strtoupper(substr(PHP_OS,0,3)=='WIN')) $title_img="sch_title_img.png";
		else $title_img="sch_title_img";
		if (!is_dir($UPLOAD_PATH."school/")) mkdir($UPLOAD_PATH."school/", 0755);
		if ($SFS_VER_DECLARE=="3.1" && !is_file($UPLOAD_PATH."school/".$title_img)) {
			$frp=fopen($SFS_PATH."/themes/new/images/logo2.png","r");
			$fwp=fopen($UPLOAD_PATH."school/".$title_img,"w");
			while(!feof($frp)) {
				fputs($fwp,fgets($frp,1024));
			}
			fclose($fwp);
			fclose($frp);
		}
	}
}
?>
