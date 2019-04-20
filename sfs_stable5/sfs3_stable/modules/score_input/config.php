<?php

// $Id: config.php 8418 2015-05-12 02:10:21Z smallduh $
include "../../include/config.php";
include "../../include/sfs_oo_overlib.php";
include "../../include/sfs_case_PLlib.php";
include "../../include/sfs_case_subjectscore.php";
include "module-upgrade.php";

//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

if(!$is_new_nor) $is_new_nor='y';
if(!$is_mod_nor) $is_mod_nor='y';

//列出橫向的連結選單模組
$year_name = get_teach_class();
$menu_p = array("stud_list.php"=>"名冊列印","group_stud_list.php"=>"分組班名冊列印","normal.php"=>"平時成績", "manage2.php"=>"管理學期成績","write_memo.php"=>"學習描述文字編修","tol.php"=>"班級學期成績","make.php"=>"套用自訂成績單","upload.php"=>"上傳成績單","stick.php"=>"成績貼條");

if ($is_print=="y" or $year_name) $menu_p["print_tol.php"]="顯示階段成績";
if ($year_name != '') $menu_p["../academic_record/"]="製作成績單 ^";
$menu_p["test.php"]="使用說明";

function stud_class_err() {
	echo "<center><h2>本項作業須具導師資格</h2>";
	echo "<h3>若有疑問請洽 系統管理員</h3></center>";
}

//在學學生編碼 0:在籍, 15:在家自學
$in_study="'0'";

//建立學科能力分組資料表
function creat_elective(){
global $CONN;
$creat1="
CREATE TABLE `elective_tea` (
  `group_id` int(11) NOT NULL auto_increment,
  `group_name` varchar(40) NOT NULL default '',
  `ss_id` int(11) NOT NULL default '0',
  `teacher_sn` int(11) NOT NULL default '0',
  `member` tinyint(3) unsigned NOT NULL default '0',
  `open` set('是','否') NOT NULL default '否',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `group_name` (`group_name`,`ss_id`,`teacher_sn`)
)  AUTO_INCREMENT=1 ;";

$creat2="
CREATE TABLE `elective_stu` (
  `elective_stu_sn` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL default '0',
  `student_sn` int(11) NOT NULL default '0',
  PRIMARY KEY  (`elective_stu_sn`),
  UNIQUE KEY `ss_id` (`group_id`,`student_sn`)
)  AUTO_INCREMENT=1 ;";

$s1="select * from elective_tea where 1=0";
$r1=$CONN->Execute($s1);
if(!$r1) $CONN->Execute($creat1) or trigger_error("無法自動建立elective_tea資料表\n<br>請將以下語法以手動建立\n<br>$creat1",256);

$s2="select * from elective_stu where 1=0";
$r2=$CONN->Execute($s2);
if(!$r2) $CONN->Execute($creat2) or trigger_error("無法自動建立elective_stu資料表\n<br>請將以下語法以手動建立\n<br>$creat2",256);


return 0;
}

//取得排除名單
function get_manage_out($sel_year,$sel_seme) {
 global $CONN;
 $sql="select student_sn from score_manage_out where year='$sel_year' and semester='$sel_seme'";
 $res=$CONN->Execute($sql) or trigger_error($sql,256);
 $student_out=array();
 while ($row=$res->fetchRow()) {
  $student_sn=$row['student_sn'];
  $student_out[$student_sn]=1;
 }
 return $student_out;
}
?>
