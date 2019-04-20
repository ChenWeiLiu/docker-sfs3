<?php
//$Id: config.php 5908 2010-03-16 23:47:21Z hami $
$dirPath = dirname(__FILE__);
require_once $dirPath."/module-cfg.php";

include_once  realpath("$dirPath/../../include/config.php");
include_once realpath("$dirPath/../../include/sfs_case_dataarray.php");
include_once realpath("$dirPath/../../include/sfs_case_PLlib.php");
include_once realpath("$dirPath/../../open_flash_chart/open_flash_chart_object.php");
require_once $dirPath."/my_fun.php";

require_once $dirPath."/module-upgrade.php";

require_once $dirPath."/class.health.php";

//設定上傳暫存目錄
$path_str = "temp/health/";
$temp_path = $UPLOAD_PATH.$path_str;

//數值極限
$minh=70; //身高低限
$maxh=226; //身高高限
$minw=10; //體重低限
$maxw=150; //體重高限

$Bid_arr=array("0"=>"體重過輕", "1"=>"體重適中", "2"=>"體重過重", "3"=>"體重超重");
?>
