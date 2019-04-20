<?php
//$Id: fix_grad.php 5310 2009-01-10 07:57:56Z hami $
include_once "config.php";
include "../../include/sfs_case_dataarray.php";

//使用者認證
sfs_check();

head("畢業修正工具");
print_menu($school_menu_p);

$now_seme=sprintf("%03d",curr_year()).curr_seme();//目前學期//目前學年
// smarty template 路徑
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// 使用 smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//如果有選取則進行處理
if ($_POST['v']) {
	$query="update stud_base set stud_study_cond='0' where stud_study_year='".$_POST['v']."' and stud_study_cond='5'";
	$CONN->Execute($query);
	$query="delete from stud_move where move_kind='5' and move_year_seme='".($_POST['v']+($IS_JHORES==0?6:3)-1)."2'";
	$CONN->Execute($query);
}
//取得學籍表中資料
$query="select stud_study_year,stud_study_cond,count(student_sn) as num from stud_base group by stud_study_year,stud_study_cond order by stud_study_year,stud_study_cond";
$res=$CONN->Execute($query);
// 替代變數
$smarty->assign("template_dir",$template_dir);
$smarty->assign("rowdata",$res->GetRows());
$smarty->assign("cond_arr",study_cond());
$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
$smarty->display("$template_dir/fix_grad.htm");
foot();
?>
