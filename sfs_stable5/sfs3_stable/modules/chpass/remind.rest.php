<?php
include "teach_config.php";
sfs_check();
date_default_timezone_set("Asia/Taipei");



//預設提醒, 90天, 顯示5秒, 預設轉到sfs3 index
$main_page = $SFS_PATH_HTML; //sfs3 首頁
$disable_this ? $disable_this : $disable_this = 0;
$chpass_period ? $duration = $chpass_period : $duration = 90 ;
$display_seconds ? $display_seconds : $display_seconds = 5 ;

$arr = array(
	'main_page' => $main_page,
	'disable_this' => $disable_this,
	'duration' => $duration,
	'display_seconds' => $display_seconds
	);
header('Content-Type: application/json');
print json_encode($arr);

