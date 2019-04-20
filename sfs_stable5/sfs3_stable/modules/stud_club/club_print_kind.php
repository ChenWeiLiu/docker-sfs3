<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $
header('Content-type: text/html; charset=big5');
ini_set ('display_errors', 'Off');

//取得設定檔
include_once "config.php";

if ($_SESSION['session_who'] != "教師") {
	echo "很抱歉！本功能模組為教師專用！";
	exit();
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=$_GET['year_seme'];

//目前選定年級，100指未指定
$club_sn=$_GET['club_sn'];
	 print_name_list($c_curr_seme,$club_sn);
	 
?>
