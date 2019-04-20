<?php

// $Id: check.php 9240 2018-05-04 03:25:04Z igogo $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['df_item']=="") $_POST['df_item']="default_jh";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","通知單 / 清單","統計表","健康寶寶","健康資料卡","臨時性檢查異常名單");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST[sub_menu_id]) {
	case "2":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",2);
		if ($_POST[class_name]) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$query="select stud_sex, count(*) as nums from stud_base where student_sn in (".$health_data->sn_str.") group by stud_sex";
			$res=$CONN->Execute($query);
			$studnum_arr=array();
			while(!$res->EOF) {
				$studnum_arr[$res->fields['stud_sex']]=$res->fields['nums'];
				$studnum_arr['all']+=$res->fields['nums'];
				$res->MoveNext();
			}
			$smarty->assign("studnum_arr",$studnum_arr);
			$query="select a.subject,a.no,a.status,count(a.student_sn) as nums,b.stud_sex from health_checks_record a left join stud_base b on a.student_sn=b.student_sn where a.year='$sel_year' and a.semester='$sel_seme' and a.student_sn in (".$health_data->sn_str.") group by b.stud_sex,a.subject,a.no,a.status";
			$res=$CONN->Execute($query);
			$rowdata=array();
			while(!$res->EOF) {
				$rowdata[$res->fields['stud_sex']][$res->fields['subject']][$res->fields['no']][$res->fields['status']]=$res->fields['nums'];
				$rowdata['all'][$res->fields['subject']][$res->fields['no']][$res->fields['status']]+=$res->fields['nums'];
				if ($res->fields['status']!=0) {
					$rowdata[$res->fields['stud_sex']][$res->fields['subject']][$res->fields['no']]['un']+=$res->fields['nums'];
					$rowdata['all'][$res->fields['subject']][$res->fields['no']]['un']+=$res->fields['nums'];
					if ($res->fields['status']!=2) $rowdata['all'][$res->fields['subject']][$res->fields['no']]['se']+=$res->fields['nums'];
				}
				$res->MoveNext();
			}
			$arr=array("My","Hy","Ast","Amb");
			foreach($arr as $d) {
				$query="select count(a.student_sn) as nums,b.stud_sex from health_sight a left join stud_base b on a.student_sn=b.student_sn where a.year='$sel_year' and a.semester='$sel_seme' and a.student_sn in (".$health_data->sn_str.") and a.".$d."<>'' group by b.stud_sex";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$rowdata[$res->fields['stud_sex']]['Oph'][$d]=$res->fields['nums'];
					$rowdata['all']['Oph'][$d]+=$res->fields['nums'];
					$res->MoveNext();
				}
			}
			//echo "<pre>";
			//print_r($rowdata);
			//echo "</pre>";
			$smarty->assign("rowdata",$rowdata);
			$smarty->assign("ifile","health_check_count.tpl");
		}
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學生健康檢查作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_check.tpl");
?>
