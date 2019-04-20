<?php
//$Id: stud_move_cal.php 5310 2009-01-10 07:57:56Z hami $
include "stud_move_config.php";
include "../../include/sfs_case_dataarray.php";    

//認證
sfs_check();

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

$sel_year = curr_year(); //選擇學年
$sel_seme = curr_seme(); //選擇學期
$curr_seme=$sel_year.$sel_seme;
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$c=$class_year;
$c[tol]="小計";

$query="select stud_sex,count(student_sn) as num,left(curr_class_num,length(curr_class_num)-4) as c_year from stud_base where stud_study_cond in ('0','15') group by c_year,stud_sex";
$res=$CONN->Execute($query) or die($query);
$a=array();
while(!$res->EOF) {
	$a[$res->fields[c_year]][$res->fields[stud_sex]]=$res->fields[num];
	$a[tol][$res->fields[stud_sex]]+=$res->fields[num];
	$res->MoveNext();
}
$smarty->assign("in_arr",$a);

$query="select b.stud_sex,a.move_kind,count(a.move_kind) as num,left(c.seme_class,length(c.seme_class)-2) as c_year from stud_move a, stud_base b,stud_seme c where a.student_sn=b.student_sn and a.student_sn=c.student_sn and a.move_year_seme='$curr_seme' and c.seme_year_seme='$seme_year_seme' group by c_year,b.stud_sex,a.move_kind order by CEILING(a.move_kind)";
$res=$CONN->Execute($query) or die($query);
$a=array();
while(!$res->EOF) {
	$a[$res->fields[move_kind]][$res->fields[c_year]][$res->fields[stud_sex]]=$res->fields[num];
	$a[$res->fields[move_kind]][tol][$res->fields[stud_sex]]+=$res->fields[num];
	$res->MoveNext();
}
$smarty->assign("data_arr",$a);
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","異動報表");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->assign("class_year",$c);
$smarty->assign("c_num",count($class_year));
$smarty->assign("kind_arr",study_cond());
$smarty->display("stud_move_stud_move_cal.tpl");
?>
