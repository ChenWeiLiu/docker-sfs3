<?php
// $Id: chc_940531.php 6986 2012-10-31 06:39:15Z infodaes $
include "report_config.php";
//認證檢查
sfs_check();

head();
print_menu($menu_p);



//取得任教班級代號
$class_id = get_teach_class();
if ($class_id == '') {
	head("權限錯誤");
	stud_class_err();
	foot();
	exit;
}

$pr_class_id_1=substr($class_id,0,1);
$pr_class_id_2=substr($class_id,1,2);
$pr_class_id=sprintf("%03d",curr_year())."_".curr_seme()."_".sprintf("%02d",$pr_class_id_1)."_".sprintf("%02d",$pr_class_id_2);


//$curr_seme = curr_year().curr_seme(); //現在學年學期
// 1.smarty物件
//$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//$template_file=$template_dir."chc_940531.htm";
$template_file= $SFS_PATH."/".get_store_path()."/chc_940531.htm";


$smarty->assign("class_id",$pr_class_id);

$smarty->display($template_file);


foot();
?>