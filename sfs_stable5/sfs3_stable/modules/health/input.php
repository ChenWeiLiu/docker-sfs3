<?php

// $Id: input.php 7707 2013-10-23 12:13:23Z smallduh $

// 取得設定檔
include "config.php";

sfs_check();
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
if ($_POST['whole']) $_POST['student_sn']="";

$sub_menu_arr=array("請選擇登錄項目","身高體重","視力檢查","視力狀況","立體感","辨色力異常","臨時性檢查","寄生蟲檢查","尿液篩檢(初查)","預防接種","傷病日誌","含氟漱口水","個人整體資料");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","健檢資料登錄");
$smarty->assign("SFS_MENU",$school_menu_p);

if ($_POST['class_name'] && $_POST['sub_menu_id']<12) {
	$health_data=new health_chart();
	$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
}

if ($_POST['sub_menu_id']<10 && !empty($_POST['sub_menu_id'])) {
	$smarty->assign("mfile","health_measure_date.tpl");
}
switch ($_POST['sub_menu_id']) {
	case "1":
		if ($_FILES['upload_file']['size']) {
			$fp=fopen($tmp_path."/".basename($_FILES['upload_file']['tmp_name']),"r");
			while($tt=sfs_fgetcsv($fp,2000,",")) {
				if (count($tt)==5) {
					for($i=0;$i<=4;$i++) $tt[$i]=trim($tt[$i]);
					$now=date("Y-m-d h:i:s");
					$measure_date=$_POST['update']['myear']."-".$_POST['update']['mmonth']."-".$_POST['update']['mday'];
					if ($tt[0] && $tt[1] && $tt[2] && $tt[3]>=70 && $tt[3]<=226 && $tt[4]>=10 && $tt[4]<=150) {
						$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."' and seme_class='".$tt[0]."' and seme_num='".$tt[1]."'";
						$res=$CONN->Execute($query);
						if ($res->fields['stud_id']==$tt[2]) {
							$query="replace into health_WH (year,semester,student_sn,weight,height,measure_date,update_date,teacher_sn) values ('$sel_year','$sel_seme','".$res->fields['student_sn']."','".$tt[4]."','".$tt[3]."','$measure_date','$now','".$_SESSION['session_tea_sn']."')";
							$res=$CONN->Execute($query);
						}
					}
				} else
					break;
			}
		}
		if ($_POST['class_name']) {
			if ($_POST['save']) $health_data->update_wh($_POST['update']);
			$health_data->get_wh();
			if ($_POST['csv']) {
				header("Content-disposition: attachment; filename=".$_POST['class_name']."身高體重表.csv");
				header("Content-type: text/x-csv ; Charset=Big5");
				//header("Pragma: no-cache");
								//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

				header("Expires: 0");
				$smarty->assign("health_data",$health_data);
				$smarty->display("health_wh_csv.tpl");
				exit;
			}
			$c=read_health_conf($UPLOAD_PATH."system/health_input");
			$_POST['wh_input']=$c['WH_INPUT'];
			$smarty->assign("ifile","health_input_wh.tpl");
		}
		break;
	case "2":
		if ($_POST['class_name']) {

			if ($_POST['save']) $health_data->update_sight($_POST['update']);
			$health_data->get_sight();
			$smarty->assign("measure_date",$measure_date);
			$smarty->assign("ifile","health_input_sight.tpl");
		}
		break;
	case "3":
		if ($_POST['class_name']) {
			if ($_POST['save']) $health_data->update_sight($_POST['update']);
			$health_data->get_sight();
			$manage_item = array(
			"1"=>"視力保建",
			"2"=>"點藥治療",
			"3"=>"配鏡矯治",
			"4"=>"家長未處理",
			"5"=>"更換鏡片",
			"6"=>"定期檢查",
			"7"=>"遮眼治療",
			"8"=>"另類治療",
			"9"=>"配戴隱型眼鏡",
			"N"=>"其它");

			$smarty->assign('manage_item', $manage_item);
			$smarty->assign("ifile","health_input_sight_status.tpl");
		}
		$smarty->assign("mfile","");
		break;
	case "4":
		if ($_POST['class_name']) {
			if ($_POST['save']) $health_data->update_ntu($_POST['update']);
			$health_data->get_ntu();
			$smarty->assign("ifile","health_input_ntu.tpl");
		}
		$smarty->assign("mfile","");
		break;
	case "5":
		if ($_POST['class_name']) {
			if ($_POST['save']) $health_data->update_co($_POST['update']);
			$health_data->get_co();
			$smarty->assign("ifile","health_input_co.tpl");
		}
		$smarty->assign("mfile","");
		break;
	case "6":
		if ($_POST['class_name']) {
			$health_data->get_sight();
			$smarty->assign("ifile","health_input_louse.tpl");
		}
		break;
	case "7":
		if ($_POST['class_name']) {
			if ($_POST['save']) $health_data->update_worm($_POST['update']);
			$health_data->get_worm();
			$smarty->assign("ifile","health_input_worm.tpl");
		}
		break;
	case "8":
		if ($_POST['class_name']) {
			if ($_POST['save']) $health_data->update_uri($_POST['update']);
			$health_data->get_uri();
			$smarty->assign("ifile","health_input_uri.tpl");
		}
		break;
	case "9":
		$smarty->assign("mfile","");
		if ($_POST['class_name']) {
			if ($_POST['save']) $health_data->update_inject($_POST['update']);
			$health_data->get_inject(1);
			$inject_arr=get_inject_item();
			$smarty->assign("work_menu",sub_menu(array(0=>"學前接種記錄",1=>"補接種記錄"),$_POST['work_id'],"work_id"));
			if ($_POST['work_id']==1) {
				$temp_arr=array_keys($inject_arr['litem']);
				if (!in_array($_POST['work_id2'],$temp_arr)) $_POST['work_id2']=array_shift($temp_arr);
				$smarty->assign("work_menu2",sub_menu($inject_arr['litem'],$_POST['work_id2'],"work_id2"));
				$smarty->assign("ifile","health_input_inject2.tpl");
			} else {
				$smarty->assign("work_menu2",sub_menu($inject_arr['item'],$_POST['work_id2'],"work_id2"));
				$smarty->assign("ifile","health_input_inject.tpl");
			}
			$smarty->assign("inject_arr",$inject_arr);
		}
		break;
	case "10":
		$smarty->assign("mfile","health_class_num.tpl");
		if ($_POST['class_num'] && strlen($_POST['class_num'])==5) {
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."' and seme_class='".substr($_POST['class_num'],0,3)."' and seme_num='".intval(substr($_POST['class_num'],-2,2))."'";
			$res=$CONN->Execute($query);
			if ($res->fields['student_sn']) {
				$_POST['student_sn']=$res->fields['student_sn'];
				$_POST['class_name']=substr($_POST['class_num'],0,3);
			}
			$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);
			$smarty->assign("class_menu",$class_menu);
		}
		if ($_POST['class_name']) {
			$smarty->assign("stud_menu",stud_menu($sel_year,$sel_seme,$_POST['class_name'],$_POST['student_sn']));
			if ($_POST['student_sn']) {
				if ($_POST['save']) $health_data->update_accident($_POST['update']);
				if ($_POST['act']) {
					if ($_POST['act']=="del") $health_data->update_accident($_POST['update']);
					elseif ($_POST['act']=="edit") $smarty->assign("rowdata",get_accident($_POST['update']['del'][0]));
				}
				if (!is_object($health_data)) $health_data=new health_chart();
				$ON_LOAD="sel_input(0)";
				$health_data->set_stud(array($_POST['student_sn']));
				$health_data->get_accident();
				$smarty->assign("aplace",get_accident_item(0,"health_accident_place"));
				$smarty->assign("areason",get_accident_item(0,"health_accident_reason"));
				$smarty->assign("apart",get_accident_item(0,"health_accident_part"));
				$smarty->assign("astatus",get_accident_item(0,"health_accident_status"));
				$smarty->assign("aattend",get_accident_item(0,"health_accident_attend"));
				$smarty->assign("ifile","health_input_accident.tpl");
			}
		}
		break;
	case "11":
		$query="select * from health_fday where year='$sel_year' and semester='$sel_seme' order by week_no";
		$res=$CONN->Execute($query);
		$r=$res->GetRows();
		$smarty->assign("date_arr",$r);
		$maxd=$r[count($r)-1]['week_no'];
		$smarty->assign("maxd",$maxd);
		if ($_POST['class_name']=="" || $_POST['all'] || $_POST['act']) {
			$sn_arr=array();
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."' order by seme_class";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn_arr[$res->fields['seme_class']][]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$temp_arr=array();
				while(list($k,$v)=each($sn_arr)) {
					if (count($sn_arr[$k])>0) {
						$sn_str="'".implode("','",$sn_arr[$k])."'";
						$sn_arr[$k]=array();
						$query="select * from health_frecord where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str)";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							$a_arr[$res->fields['student_sn']]=$res->fields['agree'];
							$res->MoveNext();
						}
						$query="select student_sn from stud_base where stud_study_cond in (0,15) and student_sn in ($sn_str)";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							$temp_arr[$k]['num']++;
							$sn=$res->fields['student_sn'];
							$sn_arr[$k][]=$sn;
							if ($_POST['all']) {
								if ($a_arr[$sn]=="")
									$query="insert into health_frecord (year,semester,student_sn,agree,update_date,teacher_sn) values ('$sel_year','$sel_seme','$sn','1','','".$_SESSION['session_tea_sn']."')";
								elseif ($a_arr[$sn]==0)
									$query="update health_frecord set agree='1',teacher_sn='".$_SESSION['session_tea_sn']."' where year='$sel_year' and semester='$sel_seme' and student_sn='$sn'";
								else
									$query="";
								if ($query) $CONN->Execute($query);
							} elseif ($_POST['act']) {
								$query="";
								for($i=1;$i<=$maxd;$i++) $query.="w$i='2',";
								$query="update health_frecord set ".substr($query,0,-1)." where year='$sel_year' and semester='$sel_seme' and student_sn='$sn' and agree='1'";
								$CONN->Execute($query);
							}
							$res->MoveNext();
						}
						if (count($sn_arr[$k])>0 && $_POST['class_name']=="") {
							$sn_str="'".implode("','",$sn_arr[$k])."'";
							$query="select count(agree) as y from health_frecord where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str) and agree='1'";
							$res=$CONN->Execute($query);
							$temp_arr[$k]['y']=$res->fields['y'];
							$query="select count(agree) as n from health_frecord where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str) and agree='0'";
							$res=$CONN->Execute($query);
							$temp_arr[$k]['n']=$res->fields['n'];
							$temp_arr[$k]['u']=$temp_arr[$k]['num']-$temp_arr[$k]['y']-$temp_arr[$k]['n'];
							for($i=1;$i<=$maxd;$i++) {
								$query="select count(w$i) as n,w$i from health_frecord where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str) group by w$i";
								$res=$CONN->Execute($query);
								while(!$res->EOF) {
									if ('w'.$i) $temp_arr[$k]['w'.$i]=$res->fields['n'];
									$res->MoveNext();
								}
							}
						}
					}
				}
			}
			if ($_POST['class_name']=="") {
				$smarty->assign("rowdata",$temp_arr);
				$smarty->assign("list",1);
				$smarty->assign("ifile","health_input_fcount.tpl");
			}
		}
		if ($_POST['save']) $health_data->update_frecord($_POST['update']);
		if ($_POST['class_name']) {
			$health_data->get_frecord();
			$smarty->assign("ifile","health_input_fclass.tpl");
		}
		break;
	case "12":
		//選單
		$gridBgcolor="#DDDDDC";
		$upstr="$sub_menu<br>$year_seme_menu<br>$class_menu";
		$downstr="<hr width=\"90%\"><p class=\"small\">下載健康記錄卡<br><a href=\"#\" OnClick=\"document.gridform.whole.value=1;document.gridform.submit();\">回班級輸入畫面</a></p><input type=\"hidden\" name=\"whole\">";
		if ($_POST[act]) $downstr="<input type=\"hidden\" name=\"act\" value=\"".$_POST[act]."\">";
		$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //建立選單
		$grid1->key_item = "student_sn";  // 索引欄名
		$grid1->display_item = array("seme_num","stud_name");  // 顯示欄名
		$grid1->bgcolor = $gridBgcolor;
		$grid1->display_color = array("1"=>"blue","2"=>"#FF6633");
		$grid1->color_index_item ="stud_sex" ; //顏色判斷值
		$grid1->class_ccs = "class=\"leftmenu\"";  // 顏色顯示
		if (!$_POST['student_sn']) $grid1->top_option = "請選擇學生";
		$grid1->sql_str = "select a.student_sn,a.stud_name,a.stud_sex,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond in ($study_str) and  b.seme_year_seme='$seme_year_seme' and b.seme_class='".$_POST[class_name]."' order by b.seme_num ";   //SQL 命令
		$grid1->do_query(); //執行命令
		$smarty->assign("stud_menu",$grid1->get_grid_str($_POST[student_sn],$upstr,$downstr)); // 顯示畫面

		if ($_POST['student_sn']) {
			$health_data=new health_chart();
			$health_data->set_stud(array($_POST['student_sn']));
		}

		switch ($_POST[act]) {
			case "disease_st":
				if ($_POST['update']) $health_data->update_disease($_POST['update']);
				if ($_POST['del']) $health_data->update_disease($_POST['del'],"del");
				$health_data->get_disease();
				$smarty->assign("module_name","學生健康資料處理 - 個人疾病史管理");
				$smarty->assign("disease_kind_arr",hDiseaseKind());
				$smarty->assign("disease_kind_str",arr_to_str(hDiseaseKind()));
				break;
			case "serious_st":
				if ($_POST['update']) $health_data->update_disease($_POST['update']);
				if ($_POST['del']) $health_data->update_disease($_POST['del'],"del");
				$health_data->get_disease();
				$smarty->assign("module_name","學生健康資料處理 - 重大傷病卡管理");
				$smarty->assign("disease_kind_arr",hSeriousDiseaseKind());
				$smarty->assign("disease_kind_str",arr_to_str(hSeriousDiseaseKind()));
				break;
			case "bodymind_st":
				if ($_POST['update'] && $_POST['sure']) $health_data->update_bodymind($_POST['update']);
				if ($_POST['del']) $health_data->update_bodymind($_POST['del'],"del");
				$health_data->get_bodymind();
				$smarty->assign("module_name","學生健康資料處理 - 身心障礙手冊管理");
				$smarty->assign("bodymind_kind_arr",hBodyMindKind());
				$smarty->assign("bodymind_level_arr",hBodyMindLevel());
				$smarty->assign("bodymind_kind_str",arr_to_str(hBodyMindKind()));
				$smarty->assign("level_str",arr_to_str(hBodyMindLevel()));
				break;
			case "inherit_st":
				if ($_POST['update'] && $_POST['sure']) $health_data->update_inherit($_POST['update']);
				if ($_POST['del']) $health_data->update_inherit($_POST['del'],"del");
				$health_data->get_inherit();
				$smarty->assign("module_name","學生健康資料處理 - 家族疾病史管理");
				$smarty->assign("folk_kind_arr",get_folk_kind());
				$hDiseaseKind_arr=array("01"=>"糖尿病","02"=>"血友病","05"=>"蠶豆症","31"=>"高血壓","99"=>"其他");
				$smarty->assign("hereditary_disease_kind_str",arr_to_str($hDiseaseKind_arr));
				$smarty->assign("hereditary_disease_kind_arr",$hDiseaseKind_arr);
				break;
			case "hospital_st":
				if ($_POST['new_hos']) {
					$_POST['update'][$_POST['student_sn']]['health_hospital_record']['id']=get_hospital(1);
					$_POST[sure]="新增";
				}
				if ($_POST['update'] && $_POST['sure']) $health_data->update_hospital($_POST['update']);
				if ($_POST['del']) $health_data->update_hospital($_POST['del'],"del");
				$health_data->get_hospital();
				$smarty->assign("module_name","學生健康資料處理 - 護送醫院管理");
				$hos_arr=get_hospital();
				$smarty->assign("hos_arr",$hos_arr);
				$smarty->assign("hos_str",arr_to_str($hos_arr));
				break;
			case "insurance_st":
				if ($_POST['update'] && $_POST['sure']) $health_data->update_insurance($_POST['update']);
				if ($_POST['del']) $health_data->update_insurance($_POST['del'],"del");
				$health_data->get_insurance();
				$insurance_arr=get_insurance();

				$smarty->assign("ins_arr",$insurance_arr);
				$smarty->assign("module_name","學生健康資料處理 - 保險管理");
				break;
			case "ntu_st":
				$health_data->get_ntu();
				$smarty->assign("module_name","學生健康資料處理 - 立體感管理");
				break;
			case "accserious_st":
				$smarty->assign("module_name","學生健康資料處理 - 在校期間重大傷病管理");
				break;
			case "wh_st":
				if ($_POST['update']) $health_data->update_wh($_POST['update']);
				if ($_POST['del']) $health_data->update_wh($_POST['del'],"del");
				$health_data->get_wh();
				$smarty->assign("module_name","學生健康資料處理 - 身高體重管理");
				break;
			case "sight_st":

				if ($_POST['update']) $health_data->update_sight($_POST['update']);
				if ($_POST['del']) $health_data->update_sight($_POST['del'],"del");
				$health_data->get_sight();

				$smarty->assign("sight_kind_str",arr_to_str(hSightManage()));
				$smarty->assign("side_arr",array("r","l"));
				$smarty->assign("module_name","學生健康資料處理 - 視力管理");
				break;
			case "oral_st":
				$health_data->get_teeth();
				$health_data->get_checks("Ora");
				$smarty->assign("teesb",array("1"=>"C","2"=>"X","3"=>"Δ","4"=>"/","5"=>"φ","6"=>"Sp."));
				$smarty->assign("module_name","學生健康資料處理 - 口腔檢查管理");
				break;
			case "tee_st":
				if ($_POST['update'] && $_POST['sure']) $health_data->update_teeth($_POST['update']);
				$tee_arr=array();
				for($i=8;$i>=1;$i--) $tee_arr[1][]=10+$i;
				for($i=1;$i<=8;$i++) $tee_arr[1][]=20+$i;
				for($i=5;$i>=1;$i--) $tee_arr[2][]=50+$i;
				for($i=1;$i<=5;$i++) $tee_arr[2][]=60+$i;
				for($i=5;$i>=1;$i--) $tee_arr[3][]=80+$i;
				for($i=1;$i<=5;$i++) $tee_arr[3][]=70+$i;
				for($i=8;$i>=1;$i--) $tee_arr[4][]=40+$i;
				for($i=1;$i<=8;$i++) $tee_arr[4][]=30+$i;
				$health_data->get_teeth();
				$tee_chk_arr=array("0"=>"無異狀","1"=>"齲齒","2"=>"缺牙","3"=>"已矯治","4"=>"待拔牙","5"=>"阻生牙","6"=>"贅生牙");
				$smarty->assign("tee_chk_str",arr_to_str($tee_chk_arr));
				$smarty->assign("tee_arr",$tee_arr);
				$smarty->assign("module_name","學生健康資料處理 - 口檢表");
				break;
			case "checkinput_st":
				if ($_POST['update'] && $_POST['sure']) $health_data->update_checks($_POST['update']);
				$smarty->assign("teesb",array("1"=>"C","2"=>"X","3"=>"Δ","4"=>"/","5"=>"φ","6"=>"Sp."));
				$health_data->get_teeth();
				if ($_POST['ajax']) {
					header("Content-Type: text/html; charset=BIG5");
					$smarty->assign("health_data",$health_data);
					$smarty->display("health_checkinput_st_ora.tpl");
					exit;
				}
				$health_data->get_checks();
				$health_data->get_checks_doctor();
				$health_data->get_sight();
//				echo "<pre>";print_r($health_data);echo "</pre>";
				$smarty->assign("module_name","學生健康資料處理 - 全身健檢資料管理");
				$smarty->assign("squint_kind_arr",hSquintKind());
				$smarty->assign("audition_kind_arr",array("1"=>"左","2"=>"右","3"=>"左右"));
				$smarty->assign("diag_str","0.無異狀,1.初檢異常,2.複檢正常,4.複檢異常,9.未受檢");
				break;
			case "healthmanage_st":
				$smarty->assign("module_name","學生健康資料處理 - 健康管理");
				$smarty->assign("check_dep_str",implode(",",hCheckDep()));
				break;
		}

		if ($_POST[act]) {
			$smarty->assign("health_data",$health_data);
			$smarty->display("health_".$_POST[act].".tpl");
			exit;
		}

		if ($_POST[student_sn]) {
			$smarty->assign("disease_kind_arr",hDiseaseKind());
			$smarty->assign("serious_kind_arr",hSeriousDiseaseKind());
			$health_data->get_disease();
			$smarty->assign("bodymind_kind_arr",hBodyMindKind());
			$smarty->assign("bodymind_level_arr",hBodyMindLevel());
			$health_data->get_bodymind();
			$health_data->get_ntu();
			$health_data->get_wh();
			$smarty->assign("Bid_arr",$Bid_arr);
			$health_data->get_sight();
			$smarty->assign("folk_kind_arr",get_folk_kind());
			$smarty->assign("hereditary_disease_kind_arr",array("01"=>"糖尿病","02"=>"血友病","05"=>"蠶豆症","31"=>"高血壓","99"=>"其他"));
			$health_data->get_inherit();
			$smarty->assign("hos_arr",get_hospital());
			$health_data->get_hospital();
			$smarty->assign("ins_arr",get_insurance());
			$health_data->get_insurance();
			$smarty->assign("teesb",array("1"=>"C","2"=>"X","3"=>"Δ","4"=>"/","5"=>"φ","6"=>"Sp."));
			$smarty->assign("teest",array("0"=>"無異狀","1"=>"初檢異常","2"=>"複檢正常","4"=>"複檢異常","9"=>"未受檢"));
			$health_data->get_teeth();
			$health_data->get_checks("Ora");
			$health_data->get_inject();
//			echo "<pre>";
//			print_r($health_data);
//			echo "</pre>";
			$smarty->assign("health_data",$health_data);
		} elseif ($_POST['class_name']) {
			$kmenu_arr=array(""=>"請選擇登錄項目","disease_gr"=>"個人疾病史","serious_gr"=>"重大傷病卡","bodymind_gr"=>"身心障礙手冊","inherit_gr"=>"家族疾病史","hospital_gr"=>"護送醫院","insurance_gr"=>"保險","cioph_gr"=>"健檢-眼","cient_gr"=>"健檢-耳鼻喉","cihea_gr"=>"健檢-頭頸","cipul_gr"=>"健檢-胸部","cidig_gr"=>"健檢-腹部","cispi_gr"=>"健檢-脊柱四肢","ciuro_gr"=>"健檢-泌尿生殖","cider_gr"=>"健檢-皮膚");
			$smarty->assign("kmenu",sub_menu($kmenu_arr,$_POST['kmenu_id'],"kmenu_id"));
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			switch ($_POST[kmenu_id]) {
				case "disease_gr":
					if ($_POST['update']) $health_data->update_disease($_POST['update']);
					if ($_POST['del']) $health_data->update_disease($_POST['del'],"del");
					$health_data->get_disease();
					$smarty->assign("module_name","學生健康資料處理 - 疾病史管理");
					$smarty->assign("disease_kind_arr",hDiseaseKind());
					$smarty->assign("ifile","health_disease_gr.tpl");
					break;
				case "serious_gr":
					if ($_POST['update']) $health_data->update_disease($_POST['update']);
					if ($_POST['del']) $health_data->update_disease($_POST['del'],"del");
					$health_data->get_disease();
					$smarty->assign("module_name","學生健康資料處理 - 重大傷病卡管理");
					$smarty->assign("disease_kind_arr",hSeriousDiseaseKind());
					$smarty->assign("ifile","health_serious_gr.tpl");
					break;
				case "bodymind_gr":
					if ($_POST['update'] && $_POST['sure']) $health_data->update_bodymind($_POST['update']);
					if ($_POST['del']) $health_data->update_bodymind($_POST['del'],"del");
					$health_data->get_bodymind();
					$smarty->assign("module_name","學生健康資料處理 - 身心障礙手冊管理");
					$smarty->assign("bodymind_kind_arr",hBodyMindKind());
					$smarty->assign("bodymind_level_arr",hBodyMindLevel());
					$smarty->assign("ifile","health_bodymind_gr.tpl");
					break;
				case "inherit_gr":
					if ($_POST['update'] && $_POST['sure']) $health_data->update_inherit($_POST['update']);
					if ($_POST['del']) $health_data->update_inherit($_POST['del'],"del");
					$health_data->get_inherit();
					$smarty->assign("module_name","學生健康資料處理 - 家族疾病史管理");
					$smarty->assign("folk_kind_arr",get_folk_kind());
					$hDiseaseKind_arr=array("01"=>"糖尿病","02"=>"血友病","05"=>"蠶豆症","31"=>"高血壓","99"=>"其他");
					$smarty->assign("hereditary_disease_kind_arr",$hDiseaseKind_arr);
					$smarty->assign("ifile","health_inherit_gr.tpl");
					break;
				case "hospital_gr":
					if ($_POST['update']) $health_data->update_hospital($_POST['update']);
					if ($_POST['del']) $health_data->update_hospital($_POST['del'],"del");
					$health_data->get_hospital();
					$smarty->assign("module_name","學生健康資料處理 - 護送醫院管理");
					$hos_arr=get_hospital();
					$smarty->assign("hos_arr",$hos_arr);
					$smarty->assign("ifile","health_hospital_gr.tpl");
					break;
				case "insurance_gr":
					if ($_POST['update'] && $_POST['sure']) $health_data->update_insurance($_POST['update'],"ins");
					if ($_POST['del']) $health_data->update_insurance($_POST['del'],"del");
					$health_data->get_insurance();
					$insurance_arr=get_insurance();
					$smarty->assign("ins_arr",$insurance_arr);
					$smarty->assign("module_name","學生健康資料處理 - 保險管理");
					$smarty->assign("ifile","health_insurance_gr.tpl");
					break;
				case "cioph_gr":
					if ($_POST['update'] && $_POST['sure']) $health_data->update_checks($_POST['update']);
					if ($_POST['del']) $health_data->update_checks($_POST['del'],"del");
					$health_data->get_checks("Oph");
					$health_data->get_checks_doctor();
					$insurance_arr=get_insurance();
					$smarty->assign("squint_kind_arr",hSquintKind());
					$smarty->assign("diag_arr",array("0"=>"無異狀","1"=>"異常","2"=>"複檢正常","4"=>"複檢異常","9"=>"未受檢"));
					$smarty->assign("module_name","學生健康資料處理 - 全身健檢(眼)");
					$smarty->assign("ifile","health_cioph_gr.tpl");
					break;
				}
			$smarty->assign("health_data",$health_data);
		}
		break;
}

//echo "<pre>";print_r($health_data);echo "</pre>";

if ($_POST[class_name] && $_POST[sub_menu_id]<12) {
	$smarty->assign("health_data",$health_data);
}

if ($_POST[sub_menu_id]==12 && $_POST['ajax'] && in_array($_POST['colnum'],array(1,2,3,4))) {
	header("Content-Type: text/html; charset=BIG5");
	$smarty->assign("sight_kind",hSightManage());
	$smarty->display("health_whole_renew".$_POST['colnum'].".tpl");
} else
	$smarty->display("health_input.tpl");
?>
