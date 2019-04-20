<?php

// $Id: inject.php 5693 2009-10-19 08:20:46Z brucelyc $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['df_item']=="") $_POST['df_item']="default_jh";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","接種通知單","接種記錄清單","持卡率及接種統計","應補種針劑清單");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST[sub_menu_id]) {
	case "1":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST[class_name]) {
			$health_data=new health_chart();
			if ($_POST['print'] && count($_POST['student_sn'])>0) {
				foreach($_POST['student_sn'] as $s) $sn[]=$s;
				$health_data->set_stud($sn,$sel_year,$sel_seme);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("year_data",year_base($sel_year,$sel_seme));
				$smarty->assign("class_data",class_name($sel_year,$sel_seme));
				$smarty->assign("health_data",$health_data);
				$smarty->display("Injectnotification.tpl");
				exit;
			}
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$smarty->assign("health_data",$health_data);
			$smarty->assign("ifile","health_inject_noti.tpl");
		}
		break;
	case "2":
	case "4":
		if ($_POST[class_name]) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_inject();
			$smarty->assign("health_data",$health_data);
			if ($_POST['print']) {
				$smarty->assign("school_data",get_school_base());
				if ($_POST[sub_menu_id]==4) {
					$smarty->assign("inject_arr",get_inject_item());
					$smarty->display("health_inject_record_print2.tpl");
				} else
					$smarty->display("health_inject_record_print.tpl");
				exit;
			}
		}
		if ($_POST[sub_menu_id]==4) {
			$smarty->assign("inject_arr",get_inject_item());
			$smarty->assign("ifile","health_inject_record2.tpl");
		} else
			$smarty->assign("ifile","health_inject_record.tpl");
		break;
	case "3":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",2);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_inject();
			$inj_arr=get_inject_item();
			//print_r($inj_arr);
			$temp_arr=array();
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$temp_arr[$seme_class][total]++;
					$temp_arr[total][total]++;
					$h=$health_data->health_data[$vv[student_sn]][inject];
					if ($h[0][0][times]==1) {
						$temp_arr[$seme_class][y]++;
						$temp_arr[total][y]++;
					}
					reset($inj_arr[times]);
					while(list($i,$j)=each($inj_arr[times])) {
						for ($k=1;$k<=$j;$k++) {
							if ($h[0][$i][times]>=$k) {
								$temp_arr[$seme_class][$i][$k]++;
								$temp_arr[total][$i][$k]++;
							}
						}
					}
					//if ($h[0][0][times]==1) $temp_arr[$seme_class][y]++;
				}
			}
			$smarty->assign("rowdata",$temp_arr);
			if ($_POST['print']) {
				$smarty->assign("school_data",get_school_base());
				$smarty->display("health_inject_count_print.tpl");
				exit;
			}
			$smarty->assign("ifile","health_inject_count.tpl");
		}
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學生預防接種作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_inject.tpl");
?>
