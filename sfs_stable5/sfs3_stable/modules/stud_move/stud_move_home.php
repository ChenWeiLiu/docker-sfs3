<?php
// $Id: stud_move_home.php 5310 2009-01-10 07:57:56Z hami $
include "stud_move_config.php";

//認證
sfs_check();

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
$stud_class=$_POST[stud_class];
$c=explode("_",$stud_class);
$seme_class=intval($c[2].$c[3]);

//按鍵處理
switch($_REQUEST[do_key]) {
	case $postHome :
		//記註為在家自學
		$sql = "update stud_base set stud_study_cond='15' where student_sn='$_POST[student_sn]'";
		$CONN->Execute($sql) or die($sql);
	break;

	case "delete" :
		//記註為在籍
		$sql = "update stud_base set stud_study_cond='0' where student_sn='$_GET[student_sn]'";
		$CONN->Execute($sql) or die($sql);
	break;
}

//班級選單
$smarty->assign("class_sel",get_class_select(curr_year(),curr_seme(),"","stud_class","this.form.submit",$stud_class));

$query="select a.student_sn,a.stud_name,a.stud_sex,b.seme_num from stud_base a right join stud_seme b on a.student_sn=b.student_sn where a.stud_study_cond='0' and b.seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and b.seme_class='$seme_class' order by b.seme_num ";
$res=$CONN->Execute($query) or die ($query);
while(!$res->EOF) {
	$stud_arr[$res->fields[student_sn]]=$res->fields[seme_num]."--".$res->fields[stud_name];
	$sex_arr[$res->fields[student_sn]]=$res->fields[stud_sex];
	$res->MoveNext();
}

//學生選單
$sel1=new drop_select();
$sel1->s_name="student_sn";
$sel1->id=$_POST[student_sn];
$sel1->arr=$stud_arr;
$sel1->has_empty=false;
$sel1->is_display_color=true;
$sel1->color_index_arr=$sex_arr;
$sel1->color_item=array("black","blue","red");
$sel1->is_submit=true;
$smarty->assign("stud_sel",$sel1->get_select());


//取得在家自學記錄
$query="select stud_id,student_sn,stud_name,left(curr_class_num,length(curr_class_num)-2) as stud_class from stud_base where stud_study_cond='15' order by curr_class_num,stud_id";
$res=$CONN->Execute($query) or die ($query);
$smarty->assign("stud_move",$res->GetRows());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","在家自學作業");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->assign("class_list",class_base());
$smarty->display("stud_move_stud_move_home.tpl");
?>