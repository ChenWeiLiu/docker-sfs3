<?php
//$Id: add_record_person.php 7726 2013-10-28 08:15:30Z smallduh $
include "config.php";

//認證
sfs_check();

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","個人學期缺曠課補登");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("study_cond",study_cond());

if ($_POST['student_sn']=="" && $_POST['stud_id']) {
	$query="select * from stud_base where stud_id='".$_POST['stud_id']."' order by stud_study_year";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	$i=0;
	while(!$res->EOF) {
		$temp_arr[$i]['student_sn']=$res->fields['student_sn'];
		$temp_arr[$i]['stud_name']=$res->fields['stud_name'];
		$temp_arr[$i]['stud_sex']=$res->fields['stud_sex'];
		$temp_arr[$i]['stud_study_year']=$res->fields['stud_study_year'];
		$temp_arr[$i]['stud_study_cond']=$res->fields['stud_study_cond'];
		$i++;
		$res->MoveNext();
	}
	$smarty->assign("stud_rows",$temp_arr);
	$smarty->assign("stud_nums",count($temp_arr));
	$smarty->display("absent_add_record_person.tpl");
	exit;
} elseif ($_POST['student_sn']) {
	$query="select * from stud_base where student_sn='$_POST[student_sn]'";
	$res=$CONN->Execute($query);
	$smarty->assign("stud_name",$res->fields[stud_name]);
	$smarty->assign("stud_study_cond",$res->fields[stud_study_cond]);
	$stud_id=$res->fields['stud_id'];
	
	if ($_POST['sure']) {
		reset($_POST[abs_data]);
		while(list($seme_year_seme,$v)=each($_POST[abs_data])) {
			reset($v);
			while(list($abs_kind,$vv)=each($v)) {
				if ($vv!="") $CONN->Execute("replace stud_seme_abs (seme_year_seme,stud_id,abs_kind,abs_days) values ('$seme_year_seme','$stud_id','$abs_kind','$vv')");
			}
		}
	}

	$max_year=($IS_JHORES==0)?6:3;
	$abs_kind_arr=stud_abs_kind();
	$rowdata=array();
	$all_seme=array();
	for($i=0;$i<$max_year;$i++) {
		for($j=1;$j<=2;$j++) {
			reset($abs_kind_arr);
			while(list($k,$v)=each($abs_kind_arr)) {
				$seme=sprintf("%03d",($res->fields[stud_study_year]+$i)).$j;
				$all_seme[]=$seme;
				$rowdata[$seme][$k]="";
			}
		}
	}
	if (count($all_seme)>0) $all_seme_str="'".implode("','",$all_seme)."'";

	$query="select * from stud_seme_abs where stud_id='$stud_id' and seme_year_seme in ($all_seme_str) order by seme_year_seme";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$rowdata[$res->fields[seme_year_seme]][$res->fields[abs_kind]]=$res->fields[abs_days];
		$res->MoveNext();
	}
	$smarty->assign("rowdata",$rowdata);
	$smarty->assign("abs_data",$abs_data);
	$smarty->assign("abs_kind",stud_abs_kind());
}

$smarty->display("absent_add_record_person.tpl");
?>
