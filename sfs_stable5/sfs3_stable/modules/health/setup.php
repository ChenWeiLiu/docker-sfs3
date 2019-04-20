<?php

// $Id: setup.php 5987 2010-08-17 03:58:11Z brucelyc $

// 取得設定檔
include "config.php";

sfs_check();



if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array(0=>"請選擇作業項目",1=>"健檢資料設定",2=>"實驗室檢查設定",3=>"醫院及診所設定",8=>'保險設定',4=>"傷病日誌選項設定",6=>"含氟漱口水實施日設定",7=>"身高體重輸入設定");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST[sub_menu_id]) {
	case "1":
		if ($_POST['sure']) {
			$health_data=new health_chart();
			while(list($k,$v)=each($_POST['sel'])) {
				$health_data->get_stud_base($sel_year,$sel_seme,$v,1);
				$health_data->update_checks_doctor($sel_year,$sel_seme,$_POST['hospital'],$_POST['doctor'],$_POST['chkdate'],$_POST['checks']);
			}
		}
		if ($_POST['del']['subject'] && $_POST['del']['hospital'] && $_POST['del']['doctor']) {
			$query="delete from health_checks_doctor where year='$sel_year' and semester='$sel_seme' and subject='".$_POST['del']['subject']."' and hospital='".$_POST['del']['hospital']."' and doctor='".$_POST['del']['doctor']."'";
			$res=$CONN->Execute($query);
		}
		$query="select distinct concat(subject,hospital,doctor),year,semester,subject,hospital,doctor,measure_date,cyear,count(*) as nums from health_checks_doctor where year='$sel_year' and semester='$sel_seme' and cyear='".$_POST['class_name']."' group by subject,hospital,doctor order by subject,hospital,doctor";
		$res=$CONN->Execute($query);
		$smarty->assign("rowdata",$res->GetRows());
		$smarty->assign("checks_item_arr",array("Oph"=>"眼","Ent"=>"耳鼻喉","Hea"=>"頭頸、胸、腹、脊柱四肢","Uro"=>"泌尿生殖","Der"=>"皮膚","Ora"=>"口腔"));
		$smarty->assign("third_menu",$year_seme_menu." ".class_menu($sel_year,$sel_seme,$_POST['class_name'],"",2));
		$smarty->assign("class_arr",class_base($_POST['year_seme'],array($_POST['class_name'])));
		$smarty->assign("ifile","health_setup_check.tpl");
		break;
	case "2":
		$smarty->assign("ifile","health_setup_lob.tpl");
		break;
	case "3":
		$hos_arr=get_hospital();
		$smarty->assign("hos_arr",$hos_arr);
		$smarty->assign("ifile","health_setup_hos.tpl");
		break;
	case "4":
		if ($_POST['third_menu_id']=="") $_POST['third_menu_id']="health_accident_place";
		$item_arr=get_accident_item(0,$_POST['third_menu_id']);
		$third_menu_arr=array("health_accident_place"=>"傷病地點","health_accident_reason"=>"傷病原因","health_accident_part"=>"傷病部位","health_accident_status"=>"傷病狀況","health_accident_attend"=>"傷病處置方式");
		$smarty->assign("third_menu",sub_menu($third_menu_arr,$_POST['third_menu_id'],"third_menu_id"));
		$smarty->assign("item_arr",$item_arr);
		$smarty->assign("third_menu_arr",$third_menu_arr);
		$smarty->assign("ifile","health_setup_place.tpl");
		break;
	case "6":
		$rowdata=get_week_arr($sel_year,$sel_seme);
		if ($_POST['fday'] && count($_POST['w'])>0 && $_POST['sure']) {
			while(list($k,$v)=each($_POST['w'])) {
				$d=date("Y-m-d",strtotime($rowdata[$v])+86400*$_POST['fday']);
				$query="replace into health_fday (year,semester,week_no,do_date) values ('$sel_year','$sel_seme','$v','$d')";
				$CONN->Execute($query);
			}
		}
		if ($_POST['del']) {
			$query="delete from health_fday where year='$sel_year' and semester='$sel_seme' and week_no='".$_POST['del']."'";
			$CONN->Execute($query);
		}
		$smarty->assign("weekN",$weekN);
		$smarty->assign("rowdata",$rowdata);
		$smarty->assign("rowdata2",get_fday());
		$smarty->assign("ifile","health_setup_fday.tpl");
		break;
	case "7":
		$path_str="/system";
		$temp_file=$UPLOAD_PATH.$path_str."/health_input";
		//讀設定
		if (is_file($temp_file)) {
			$c=read_health_conf($temp_file);
			if ($_POST['wh_input']=="" && !$_POST['sure']) $_POST['wh_input']=$c['WH_INPUT'];
		}
		//寫設定
		if ($_POST['wh_input']) {
			$fp=fopen($temp_file,"w");
			fputs($fp,"WH_INPUT=".$_POST['wh_input']);
			fclose($fp);
		}
		$smarty->assign("ifile","health_setup_wh.tpl");
		break;

	case "8": // 保險設定
		// AJAX 檢查
		if ($_POST['checkKind'])  {
			switch ($_POST['checkKind'])	{
				// 檢查是否有學生資料
				case 'insurance_record' :
					$query = "SELECT COUNT(*) AS cc FROM health_insurance_record  WHERE id='".intval($_POST['id'])."'";
					$res = $CONN->Execute($query);
					echo $res->fields['cc'];
					break;
				// 將保險機關刪除
				case 'delete_insurance_record' :
					$CONN->Execute("DELETE  FROM health_insurance WHERE  id='".intval($_POST['id'])."'");
					break;
			}
			die();
		} else {
			$insurancer=get_insurance();
			$smarty->assign("insurance",$insurancer);
			$smarty->assign("ifile","health_setup_insurance.tpl");
		}
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","系統選項設定");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_setup.tpl");
?>
