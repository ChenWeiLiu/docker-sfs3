<?php
//$Id: add_record.php 7726 2013-10-28 08:15:30Z smallduh $
include "config.php";

//認證
sfs_check();

if ($_POST[year_seme]=="") $_POST[year_seme]=sprintf("%03d",curr_year()).curr_seme();

reset($_POST[abs_data]);
if ($_POST[sure]) {
	while(list($stud_id,$v)=each($_POST[abs_data])) {
		reset($v);
		while(list($abs_kind,$abs_days)=each($v)) {
			if ($abs_days<>'') $CONN->Execute("replace stud_seme_abs (seme_year_seme,stud_id,abs_kind,abs_days) values ('$_POST[year_seme]','$stud_id','$abs_kind','$abs_days')");
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
	$query="select * from stud_seme_abs where stud_id in ($ids) and seme_year_seme='$_POST[year_seme]'";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$abs_data[$res->fields[stud_id]][$res->fields[abs_kind]]=$res->fields[abs_days];
		$res->MoveNext();
	}
}
$smarty->assign("abs_data",$abs_data);

$smarty->assign("abs_kind",stud_abs_kind());
$smarty->assign("study_cond",study_cond());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學期缺曠課登記");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->display("absent_add_record.tpl");
?>
