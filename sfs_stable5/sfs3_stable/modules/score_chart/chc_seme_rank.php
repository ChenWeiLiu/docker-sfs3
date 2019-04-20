<?php
//$Id: chc_seme_rank.php 8576 2015-10-27 11:13:27Z qfon $
include "chc_config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "dropmenu.php";
include_once "chc_seme_advance.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/chc_seme_rank.htm";
//建立物件
$obj= new chc_seme_advance($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前
$obj->process();

//秀出網頁布景標頭
head("階段成績進退步查詢");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();



?>
