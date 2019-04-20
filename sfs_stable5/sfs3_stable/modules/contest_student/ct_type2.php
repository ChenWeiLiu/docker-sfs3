<?php
header('Content-type: text/html;charset=big5');
//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("網路應用競賽 - 英打比賽");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

$ACTIVE=6; //競賽種類

include_once("typingrace.inc");

?>