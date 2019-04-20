<?php
//$Id: add_record.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_case_dataarray.php";

//認證
sfs_check();

if ($_POST[year_seme]=="") $_POST[year_seme]=sprintf("%03d",curr_year()).curr_seme();
if ($_POST[sure]) {
	while(list($stud_id,$v)=each($_POST[reward_data])) {
		reset($v);
		while(list($reward_kind,$vv)=each($v)) {
			if ($vv<>'') $CONN->Execute("replace stud_seme_rew (seme_year_seme,stud_id,sr_kind_id,sr_num) values ('$_POST[year_seme]','$stud_id','$reward_kind','$vv')");
		}
	}
}

//學年選單
$sel1 = new drop_select();
$sel1->s_name="year_seme";
$sel1->id= $_POST[year_seme];
$sel1->arr = get_class_seme();
$sel1->has_empty = false;
$sel1->is_submit = true;
$smarty->assign("year_seme_sel",$sel1->get_select());

//年級選單
$class_arr=class_base($_POST[year_seme]);
if ($_POST[class_year]=="") $_POST[class_year]=key($class_arr);
$sel1 = new drop_select();
$sel1->s_name="class_year";
$sel1->id= $_POST[class_year];
$sel1->arr = $class_arr;
$sel1->has_empty = false;
$sel1->is_submit = true;
$smarty->assign("class_year_sel",$sel1->get_select());

//學生資料
$query="select a.student_sn,a.seme_num,b.stud_name,b.stud_id,b.stud_study_cond from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$_POST[year_seme]' and a.seme_class='$_POST[class_year]'";
$res=$CONN->Execute($query);
$i=0;
while(!$res->EOF) {
	$all_id[]=$res->fields[stud_id];
	$rowdata[$i]=$res->FetchRow();
	$i++;
}
$smarty->assign("rowdata",$rowdata);

if (count($all_id)>0) {
	$ids="'".implode("','",$all_id)."'";
	$query="select * from stud_seme_rew where stud_id in ($ids) and seme_year_seme='$_POST[year_seme]'";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$reward_data[$res->fields[stud_id]][$res->fields[sr_kind_id]]=$res->fields[sr_num];
		$res->MoveNext();
	}
}
$smarty->assign("reward_data",$reward_data);

$smarty->assign("reward_kind",stud_rep_kind());
$smarty->assign("study_cond",study_cond());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學期獎懲補登");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->display("reward_add_record.tpl");
?>
