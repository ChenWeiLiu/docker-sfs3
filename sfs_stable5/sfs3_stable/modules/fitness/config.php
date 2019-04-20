<?php
// $Id: config.php 7816 2013-12-17 14:10:29Z infodaes $
include "../../include/config.php";
include "module-cfg.php";
include "module-upgrade.php";
include "my_fun.php";
include "../../include/sfs_oo_overlib.php";
include "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_studclass.php";

//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

function stud_class_err() {
        echo "<center><h2>本項作業須具導師資格</h2>";
        echo "<h3>若有疑問請洽 系統管理員</h3></center>";
}

//在學學生編碼 0:在籍, 15:在家自學
$in_study="'0','15'";

//榮譽榜分級
$test = array("身高<br>cm","體重<br>kg","坐姿前彎<br>cm","仰臥起坐<br>次","立定跳遠<br>cm","800/1600公尺<br>秒","檢測單位","檢測年月") ;
$file = array("tall","weigh","test1","test2","test3","test4","organization","test_date") ;
$input_classY="1,2,3,4,5,6,7,8,9,10,11,12";

//管理權
if (checkid($_SERVER['SCRIPT_FILENAME'],1)) $admin=1;

$Bid_arr=array("0"=>"過輕", "1"=>"適中", "2"=>"過重", "3"=>"肥胖");


?>
