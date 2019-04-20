<?php

// $Id: wh.php 9240 2018-05-04 03:25:04Z igogo $

// 取得設定檔
include "config.php";
include "../health/class.health.php";

sfs_check();

$health_data=new health_chart();
$health_data->get_stud_base(curr_year(),curr_seme(),$class_num);
$health_data->get_wh();

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","班級學生身高體重");
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("year_seme",sprintf("%03d",curr_year()).curr_seme());
$smarty->assign("Bid_arr",array("0"=>"體重過輕", "1"=>"體重適中", "2"=>"體重過重", "3"=>"體重超重"));
$smarty->assign("health_data",$health_data);
$smarty->display("class_health_wh.tpl");
?>