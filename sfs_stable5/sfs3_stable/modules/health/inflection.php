<?php

// $Id: inflection.php 9240 2018-05-04 03:25:04Z igogo $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['df_item']=="") $_POST['df_item']="default_jh";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","傳染病登錄","傳染病上傳","傳染病統計","病假日誌(分班)","傳染病日期查詢");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST[sub_menu_id]) {
	case "1":
		//取得週次
		$weeks_array=get_week_arr($sel_year,$sel_seme,$_POST[temp_reward_date]);
		$sel_week=$_POST['sel_week'];
		if ($sel_week) $weeks_array[0]=$sel_week;
		if ($weeks_array[0]=="") $weeks_array[0]=1;
		$sel_week=$weeks_array[0];
		$smarty->assign("weeks_arr",$weeks_array);

		//計算本週日期
		$tt=strtotime($weeks_array[$weeks_array[0]]);
		$dd=getdate($tt);
		for($i=1;$i<=5;$i++) $weekday_arr[$i]=date("Y-m-d",($tt+86400*($i-$dd['wday'])));
		$smarty->assign("cweekday",array("1"=>"週一","2"=>"週二","3"=>"週三","4"=>"週四","5"=>"週五"));
		$smarty->assign("weekday_arr",$weekday_arr);

		//取得通報項目
		$query="select * from health_inflection_item where enable='1' order by iid";
		$res=$CONN->Execute($query);
		$smarty->assign("inf_arr",$res->GetRows());

		//刪除記錄
		if ($_POST['act']=="del" && $_POST['student_sn'] && $_POST['iid'] && $_POST['sel_week']) {
			$query="delete from health_inflection_record where student_sn='".$_POST['student_sn']."' and iid='".$_POST['iid']."' and dis_date>='".$weekday_arr[1]."' and dis_date<='".$weekday_arr[5]."'";
			$CONN->Execute($query);
		}

		//確定新增資料
		if ($_POST['act']=="sure") {
			$sval=0;
			if (count($_POST['id'])>0) foreach($_POST['id'] as $v) {
				$query="delete from health_inflection_record where id='$v'";
				$CONN->Execute($query);
			}
			foreach($_POST['status'] as $k=>$v) {
				if ($v!="" && in_array($k,$weekday_arr)) {
					$sval=1;
					$weekday=array_search($k,$weekday_arr);
					$query="replace into health_inflection_record (student_sn,iid,dis_date,weekday,status,rmemo,teacher_sn) values ('".$_POST['student_sn']."','".$_POST['iid']."','$k','$weekday','$v','".nl2br(trim($_POST['rmemo']))."','".$_SESSION['session_tea_sn']."')";
					$res=$CONN->Execute($query);
				}
			}
		}

		if ($_POST['act']=="add") {
			//新增記錄
			$smarty->assign("class_menu2",$class_menu);
			if ($_POST['class_name']) $smarty->assign("stud_menu",stud_menu($sel_year,$sel_seme,$_POST['class_name'],$_POST['student_sn']));
			$smarty->assign("ifile","health_inflection_form.tpl");
		} elseif ($_POST['act']=="edit" && $_POST['student_sn'] && $_POST['iid'] && $_POST['sel_week']) {
			//編修記錄
			$query="select * from health_inflection_record where student_sn='".$_POST['student_sn']."' and iid='".$_POST['iid']."' and dis_date>='".$weekday_arr[1]."' and dis_date<='".$weekday_arr[5]."'";
			$res=$CONN->Execute($query);
			$temp_arr=array();
			while(!$res->EOF) {
				$temp_arr['weekday'][$res->fields['weekday']]=$res->fields['status'];
				$temp_arr['id'][]=$res->fields['id'];
				$res->MoveNext();
			}
			$smarty->assign("rowdata",$temp_arr);
			//取得學生班級資料
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."' and student_sn='".$_POST['student_sn']."'";
			$res=$CONN->Execute($query);
			$c_arr=class_base($_POST['year_seme']);
			$smarty->assign("class_menu2","<span style=\"color:blue;\">".$c_arr[$res->fields['seme_class']]."</span><input type=\"hidden\" name=\"class_name\" value=\"".$res->fields['seme_class']."\">");
			//取得學生基本資料
			$query="select * from stud_base where student_sn='".$_POST['student_sn']."'";
			$res=$CONN->Execute($query);
			$smarty->assign("stud_menu","<span style=\"color:".(($res->fields['stud_sex']==1)?"blue":"red").";\">".$res->fields['stud_name']."</span><input type=\"hidden\" name=\"student_sn\" value=\"".$_POST['student_sn']."\">");
			$smarty->assign("ifile","health_inflection_form.tpl");
		} else {
			//取得記錄
			$query="select a.*,b.seme_class,b.seme_num from health_inflection_record a left join stud_seme b on a.student_sn=b.student_sn where b.seme_year_seme='".$_POST['year_seme']."' and a.dis_date>='".$weekday_arr[1]."' and a.dis_date<='".$weekday_arr[5]."' order by b.seme_class,b.seme_num,a.dis_date";
			$res=$CONN->Execute($query);
			$temp_arr=array();
			$temp_sn=array();
			while(!$res->EOF) {
				$sn=$res->fields['student_sn'];
				$temp_sn[$sn]=$sn;
				$temp_arr[$sn]['year_name']=substr($res->fields['seme_class'],0,-2);
				$temp_arr[$sn]['class_name']=substr($res->fields['seme_class'],-2,2);
				$temp_arr[$sn]['seme_num']=$res->fields['seme_num'];
				$temp_arr[$sn]['iid'][$res->fields['iid']][$res->fields['weekday']]=$res->fields['status'];
				$res->MoveNext();
			}
			if (count($temp_sn)>0) {
				$sn_str="'".implode("','",$temp_sn)."'";
				$query="select * from stud_base where student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn=$res->fields['student_sn'];
					$temp_arr[$sn]['stud_name']=$res->fields['stud_name'];
					$temp_arr[$sn]['stud_sex']=$res->fields['stud_sex'];
					$res->MoveNext();
				}
			}
			$smarty->assign("rowdata",$temp_arr);
			$smarty->assign("ifile","health_inflection_list.tpl");
		}
		$class_menu="";
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","傳染病作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_inflection.tpl");
?>
