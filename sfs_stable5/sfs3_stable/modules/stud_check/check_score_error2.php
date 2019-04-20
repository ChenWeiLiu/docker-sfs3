<?php
// $Id:$
//載入設定檔
include "stud_check_config.php";
include "my_fun.php";

//認證檢查
sfs_check();

if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$_POST[year_seme]=$year_seme;
} else {
	$year_seme=$_POST['year_seme'];
	$sel_year=intval(substr($year_seme,0,-1));
	$sel_seme=substr($year_seme,-1,1);
}
$score_semester="score_semester_".$sel_year."_".$sel_seme;
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;

//刪除所選錯誤成績
if ($_POST['del']) {
	foreach($_POST['del'] as $seme_class => $d) {
		foreach($d as $ss_id => $dd) {
			$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class'";
			$res=$CONN->Execute($query);
			$sn_arr=array();
			while(!$res->EOF) {
				$sn_arr[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$sn_str="'".implode("','",$sn_arr)."'";
				$query="delete from $score_semester where student_sn in ($sn_str) and ss_id='$ss_id'";
				$CONN->Execute($query);
			}
		}
	}
}

if ($_POST['year_seme']) {
	$c_arr=array();
	//取出全校班級
	$query="select * from school_class where year='$sel_year' and semester='$sel_seme' order by c_year,c_sort";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$c_arr[$res->fields['c_year'].sprintf("%02d",$res->fields['c_sort'])]=$res->fields['c_year']."年".$res->fields['c_name']."班";
		$res->MoveNext();
	}
	

	//取出各班學生流水號
	$class_arr=array();
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' order by seme_class";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$class_arr[$res->fields['seme_class']][]=$res->fields['student_sn'];
		$res->MoveNext();
	}

	//取出課程中文名
	$subj_arr=array();
	$query="select * from score_subject";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$subj_arr[$res->fields['subject_id']]=$res->fields['subject_name'];
		$res->MoveNext();
	}
	

	//取出各班正確的課程
	$ss_arr=array();
	//取出全校課程
	$ss_all_arr=array();
	foreach($class_arr as $c=>$d) {
		$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($c,0,-2),substr($c,-2,2));
		$query="select * from score_ss where year='$sel_year' and semester='$sel_seme' and class_id like '$class_id"."%'";
		$res=$CONN->Execute($query);
		$c_str="[$class_id 班級課程] ";
		if ($res->RecordCount()==0) {
			$query="select * from score_ss where year='$sel_year' and semester='$sel_seme' and class_id='' and class_year like '".substr($c,0,1)."%'";
			$res=$CONN->Execute($query);
			$c_str="[年級課程] ";
		}
		while(!$res->EOF) {
			if ($res->fields['enable']==1) $ss_arr[$c][]=$res->fields['ss_id'];
			$ss_all_arr[$res->fields['ss_id']]=(($res->fields['enable']==0)?"<span style=\"color:red;font-size:10pt;\">(課程已刪除) </span>":"").$c_str.$res->fields['class_year']."年級 : ".$subj_arr[$res->fields['scope_id']].(($res->fields['subject_id']>0)?"-".$subj_arr[$res->fields['subject_id']]:"");
			$res->MoveNext();
		}
	}

	//檢查錯誤成績
	reset($class_arr);
	$rowdata=array();
	foreach($class_arr as $c=>$d) {
		if (count($d)>0 && count($ss_arr[$c])>0) {
			$sn_str="'".implode("','",$d)."'";
			$ss_str="'".implode("','",$ss_arr[$c])."'";
			$query="select ss_id,count(student_sn) as num from $score_semester where student_sn in ($sn_str) and ss_id not in ($ss_str) group by ss_id";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$rowdata[$c][$res->fields['ss_id']]=$res->fields['num'];
				$res->MoveNext();
			}
		}
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","期中成績檢查"); 
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme,"OnChange='chk();'"));
$smarty->assign("rowdata",$rowdata);
$smarty->assign("ss_all_arr",$ss_all_arr);
$smarty->assign("c_arr",$c_arr);
$smarty->display("check_score_error2.tpl");
?>
