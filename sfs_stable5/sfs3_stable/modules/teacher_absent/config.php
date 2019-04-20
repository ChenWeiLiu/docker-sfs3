<?php
//$Id: config.php 8104 2014-09-01 05:56:02Z hami $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
include "../../include/sfs_case_PLlib.php";
include "../../include/sfs_case_dataarray.php";
require_once "./module-upgrade.php";

//您可以自己加入引入檔
$status_kind=array("0"=>"待確認","1"=>"");
$check_arr=array("0"=>"待確認","1"=>"已核章");
$course_kind=array("0"=>"無課務","1"=>"自行調課","2"=>"公費排代","3"=>"教保員代理","4"=>"自費找代");
$c_course_kind=array("1"=>"自行調課","2"=>"公費排代","3"=>"自費找代");
$d_kind_arr=array("1"=>"教學鐘點","2"=>"導師時間","3"=>"全日代課");
$times_kind_arr=array("1"=>"節","2"=>"次","3"=>"日");
$month_arr=array("1"=>"一月","2"=>"二月","3"=>"三月","4"=>"四月","5"=>"五月","6"=>"六月","7"=>"七月","8"=>"八月","9"=>"九月","10"=>"十月","11"=>"十一月","12"=>"十二月");
//$abs_kind_arr=array("11"=>"事假","12"=>"家庭照顧假","21"=>"病假","22"=>"生理假","31"=>"延長病假","41"=>"婚假","42"=>"產前假","43"=>"分娩假","44"=>"流產假","45"=>"陪產假","46"=>"喪假","51"=>"公差假","52"=>"公差","61"=>"休假","62"=>"補休假","71"=>"其他");
$post_kind=post_kind();  //職稱
$check1="單位主管";
$check2="教學組長";
$check3="校長";
$check4="人事主任";
$check5="會計主任";

require_once "./my_fun.php";

if ($_REQUEST[year_seme]) {
	$ys=explode("_",$_REQUEST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	if(empty($sel_year))	$sel_year = curr_year(); //目前學年
	
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	
}

?>
