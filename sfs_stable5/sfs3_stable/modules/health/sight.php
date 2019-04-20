<?php

// $Id: sight.php 6808 2012-06-22 08:14:46Z smallduh $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['df_item']=="") $_POST['df_item']="default_jh";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","視力未測量名單","視力不良通知單","班級視力清單","裸眼視力正常清單","裸眼視力不良清單","矯正視力未達標準清單","視力統計表","視力不良診治結果統計表","視力統計圓餅圖","視力分布圓餅圖","裸視視力不良統計圖","矯正視力未達標準統計圖","立體感異常清單","辨色力異常清單");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST[sub_menu_id]) {
	case "1":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			if ($_POST['update']) $health_data->update_sight($_POST['update']);
			if (strlen($_POST['class_name'])==1)
			$class_name_str="and seme_class like '".$_POST['class_name']."%'";
			elseif ($_POST['class_name']=="all")
			$class_name_str="";
			else
			$class_name_str="and seme_class='".$_POST['class_name']."'";
			$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' $class_name_str";
			$res=$CONN->Execute($query);
			$sn=array();
			while(!$res->EOF) {
				$sn[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn)>0) {
				$sn_str="'".implode("','",$sn)."'";
				$sn=array();
				$query="select * from stud_base where stud_study_cond='0' and student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn[]=$res->fields['student_sn'];
					$res->MoveNext();
				}
				if (count($sn)>0) {
					$sn_str="'".implode("','",$sn)."'";
					$query="select student_sn from health_sight where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str) and (sight_o<>'' or sight_r<>'')";
					$res=$CONN->Execute($query);
					while(!$res->EOF) {
						$sn_chk[$res->fields['student_sn']]++;
						$res->MoveNext();
					}
					$sn_arr=array();
					while(list($k,$s)=each($sn)) {
						if ($sn_chk[$s]<2) $sn_arr[]=$s;
					}
					if (count($sn_arr)>0) {
						$health_data->set_stud($sn_arr,$sel_year,$sel_seme);
						$health_data->get_sight();
					}
				}
			}
			if ($_POST['print']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(3);
				$x->setRowText(array("年級","班級","座號","姓名","裸視右眼","裸視左眼","矯視右眼","矯視左眼"));
				$x->items=get_whs($health_data,$_POST['year_seme'],"",1);
				$x->writeFile();
				$x->process();
				exit;
			}
			$smarty->assign("ifile","health_sight_unmeasure.tpl");
			$smarty->assign("mfile","health_measure_date.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "2":
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
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			if ($_POST['print'] && count($_POST['student_sn'])>0) {
				foreach($_POST['student_sn'] as $s) $sn[]=$s;

				$health_data->set_stud($sn,$sel_year,$sel_seme);
				//new
				$health_data->get_sight();
				$smarty->assign("health_data",$health_data);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("year_data",year_base($sel_year,$sel_seme));
				$smarty->assign("class_data",class_name($sel_year,$sel_seme));

				$smarty->display("Sightnotification.tpl");
				exit;
			}
			if (strlen($_POST['class_name'])==1)
			$class_name_str="and seme_class like '".$_POST['class_name']."%'";
			elseif ($_POST['class_name']=="all")
			$class_name_str="";
			else
			$class_name_str="and seme_class='".$_POST['class_name']."'";
			$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' $class_name_str";
			$res=$CONN->Execute($query);
			$sn=array();
			while(!$res->EOF) {
				$sn[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn)>0) {
				$sn_str="'".implode("','",$sn)."'";
				$sn=array();
				$query="select * from stud_base where stud_study_cond='0' and student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn[]=$res->fields['student_sn'];
					$res->MoveNext();
				}
				if (count($sn)>0) {
					$sn_str="'".implode("','",$sn)."'";
					$sn=array();
					//$query="select student_sn from health_sight where year='$sel_year' and semester='$sel_seme' and sight_o<'0.9' and student_sn in ($sn_str)";
					$query="select student_sn from health_sight where year='$sel_year' and semester='$sel_seme' and ((sight_o < '0.9' and sight_r = '') or (sight_r != '' and sight_r < '0.5')) and student_sn in ($sn_str)";
					$res=$CONN->Execute($query);
					while(!$res->EOF) {
						if (!in_array($res->fields['student_sn'],$sn)) $sn[]=$res->fields['student_sn'];
						$res->MoveNext();
					}
					if (count($sn)>0) {
						$health_data->set_stud($sn,$sel_year,$sel_seme);
						$health_data->get_sight();
					}
				}
			}
			$smarty->assign("ifile","health_sight_noti.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "3":
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$rowtext=array("年級","班級","座號","姓名","裸視右","裸視左","矯正右","矯正左");
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_sight();
			if ($_POST['xls']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(6);
				$x->setRowText($rowtext);
				$x->addSheet($_POST['class_name']);
				$x->items=get_whs($health_data,$_POST['year_seme'],"s");
				$x->writeSheet();
				$x->process();
				exit;
			}
			if ($_POST['ods']) {
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$x->setRowText($rowtext);
				$x->addSheet($_POST['class_name']);
				$x->items=get_whs($health_data,$_POST['year_seme'],"s");
				$x->writeSheet();
				$x->process();
				exit;
			}
			$smarty->assign("ifile","health_sight_list.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "4":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$rowtext=array("年級","班級","座號","姓名","裸視右","裸視左","矯正右","矯正左");
			if (strlen($_POST['class_name'])==1)
			$class_name_str="and seme_class like '".$_POST['class_name']."%'";
			elseif ($_POST['class_name']=="all")
			$class_name_str="";
			else
			$class_name_str="and seme_class='".$_POST['class_name']."'";
			$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' $class_name_str";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn_arr[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$sn_str="'".implode("','",$sn_arr)."'";
				$sn_arr=array();
				$query="select student_sn from health_sight where year='$sel_year' and semester='$sel_seme' and sight_o>='0.9' and student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$tmp_arr[$res->fields['student_sn']]+=1;
					$res->MoveNext();
				}
				foreach($tmp_arr as $s => $value) {
					if ($value==2) $sn_arr[]=$s;
				}
				if (count($sn_arr)>0) {
					$sn_str="'".implode("','",$sn_arr)."'";
					$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' and student_sn in ($sn_str)";
					$res=$CONN->Execute($query);
					while(!$res->EOF) {
						$sn[]=$res->fields['student_sn'];
						$res->MoveNext();
					}
					if (count($sn)>0) {
						$health_data->set_stud($sn,$sel_year,$sel_seme);
						$health_data->get_sight();
						if ($_POST['xls']) {
							require_once "../../include/sfs_case_excel.php";
							$x=new sfs_xls();
							$x->setUTF8();
							$x->setBorderStyle(6);
							$x->setRowText($rowtext);
							$x->addSheet($_POST['class_name']);
							$x->items=get_whs($health_data,$_POST['year_seme'],"s");
							$x->writeSheet();
							$x->process();
							exit;
						}
						if ($_POST['ods']) {
							require_once "../../include/sfs_case_ooo.php";
							$x=new sfs_ooo();
							$x->setRowText($rowtext);
							$x->addSheet($_POST['class_name']);
							$x->items=get_whs($health_data,$_POST['year_seme'],"s");
							$x->writeSheet();
							$x->process();
							exit;
						}
					}
				}
			}
			$smarty->assign("ifile","health_sight_list.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "5":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$rowtext=array("年級","班級","座號","姓名","裸視右","裸視左","矯正右","矯正左");
			if (strlen($_POST['class_name'])==1)
			$class_name_str="and seme_class like '".$_POST['class_name']."%'";
			elseif ($_POST['class_name']=="all")
			$class_name_str="";
			else
			$class_name_str="and seme_class='".$_POST['class_name']."'";
			$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' $class_name_str";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn_arr[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$sn_str="'".implode("','",$sn_arr)."'";
				$sn_arr=array();
				$query="select student_sn from health_sight where year='$sel_year' and semester='$sel_seme' and sight_o<'0.9' and student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$tmp_arr[$res->fields['student_sn']]+=1;
					$res->MoveNext();
				}
				foreach($tmp_arr as $s => $value) {
					$sn_arr[]=$s;
				}
				if (count($sn_arr)>0) {
					$sn_str="'".implode("','",$sn_arr)."'";
					$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' and student_sn in ($sn_str)";
					$res=$CONN->Execute($query);
					while(!$res->EOF) {
						$sn[]=$res->fields['student_sn'];
						$res->MoveNext();
					}
					if (count($sn)>0) {
						$health_data->set_stud($sn,$sel_year,$sel_seme);
						$health_data->get_sight();
						if ($_POST['xls']) {
							require_once "../../include/sfs_case_excel.php";
							$x=new sfs_xls();
							$x->setUTF8();
							$x->setBorderStyle(6);
							$x->setRowText($rowtext);
							$x->addSheet($_POST['class_name']);
							$x->items=get_whs($health_data,$_POST['year_seme'],"s");
							$x->writeSheet();
							$x->process();
							exit;
						}
						if ($_POST['ods']) {
							require_once "../../include/sfs_case_ooo.php";
							$x=new sfs_ooo();
							$x->setRowText($rowtext);
							$x->addSheet($_POST['class_name']);
							$x->items=get_whs($health_data,$_POST['year_seme'],"s");
							$x->writeSheet();
							$x->process();
							exit;
						}
					}
				}
			}
			$smarty->assign("ifile","health_sight_list.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "6":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$rowtext=array("年級","班級","座號","姓名","裸視右","裸視左","矯正右","矯正左");
			if (strlen($_POST['class_name'])==1)
			$class_name_str="and seme_class like '".$_POST['class_name']."%'";
			elseif ($_POST['class_name']=="all")
			$class_name_str="";
			else
			$class_name_str="and seme_class='".$_POST['class_name']."'";
			$query="select a.student_sn from stud_seme a left join health_sight b on a.student_sn=b.student_sn where a.seme_year_seme='".$_POST['year_seme']."' $class_name_str and b.year='$sel_year' and b.semester='$sel_seme' and b.sight_o<'0.9' and (b.sight_r<'0.5' or b.sight_r is NULL)";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				if (!in_array($res->fields['student_sn'],$sn)) $sn[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			$health_data->set_stud($sn,$sel_year,$sel_seme);
			$health_data->get_sight();
			if ($_POST['xls']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(6);
				$x->setRowText($rowtext);
				$x->addSheet($_POST['class_name']);
				$x->items=get_whs($health_data,$_POST['year_seme'],"s");
				$x->writeSheet();
				$x->process();
				exit;
			}
			if ($_POST['ods']) {
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$x->setRowText($rowtext);
				$x->addSheet($_POST['class_name']);
				$x->items=get_whs($health_data,$_POST['year_seme'],"s");
				$x->writeSheet();
				$x->process();
				exit;
			}
			$smarty->assign("ifile","health_sight_list.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "7":
		$class_menu="";
		if ($_POST['year_seme']) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,"all");
			$health_data->get_sight();
			$svalue=array("sight_o","sight_r");
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$year_name=substr($seme_class,0,-2);
					$sex=$health_data->stud_base[$vv[student_sn]][stud_sex];
					$ro=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['r'][sight_o];
					$rr=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['r'][sight_r];
					$lo=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['l'][sight_o];
					$lr=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['l'][sight_r];
					if ($ro>=0.9 && $lo>=0.9) {
						//裸視兩眼均>=0.9
						$data_arr[$year_name][0][$sex][0]++;
						$data_arr['all'][0][$sex][0]++;
						$data_arr[$year_name][0][$sex]['all']++;
						$data_arr['all'][0][$sex]['all']++;
						$data_arr[$year_name][0]['all']++;
						$data_arr['all'][0]['all']++;
					} elseif ($ro<0.1 || $lo<0.1) {
						//裸視有一眼<0.1
						$data_arr[$year_name][0][$sex][3]++;
						$data_arr['all'][0][$sex][3]++;
						$data_arr[$year_name][0][$sex]['all']++;
						$data_arr['all'][0][$sex]['all']++;
						$data_arr[$year_name][0]['all']++;
						$data_arr['all'][0]['all']++;
						$data_arr[$year_name][0][$sex]['dis']++;
						$data_arr['all'][0][$sex]['dis']++;
						$data_arr[$year_name][0]['dis']++;
						$data_arr['all'][0]['dis']++;
					} elseif ($ro<0.5 || $lo<0.5) {
						//裸視有一眼<0.5
						$data_arr[$year_name][0][$sex][2]++;
						$data_arr['all'][0][$sex][2]++;
						$data_arr[$year_name][0][$sex]['all']++;
						$data_arr['all'][0][$sex]['all']++;
						$data_arr[$year_name][0]['all']++;
						$data_arr['all'][0]['all']++;
						$data_arr[$year_name][0][$sex]['dis']++;
						$data_arr['all'][0][$sex]['dis']++;
						$data_arr[$year_name][0]['dis']++;
						$data_arr['all'][0]['dis']++;
					} elseif ($ro<0.9 || $lo<0.9) {
						//裸視有一眼<0.9
						$data_arr[$year_name][0][$sex][1]++;
						$data_arr['all'][0][$sex][1]++;
						$data_arr[$year_name][0][$sex]['all']++;
						$data_arr['all'][0][$sex]['all']++;
						$data_arr[$year_name][0]['all']++;
						$data_arr['all'][0]['all']++;
						$data_arr[$year_name][0][$sex]['dis']++;
						$data_arr['all'][0][$sex]['dis']++;
						$data_arr[$year_name][0]['dis']++;
						$data_arr['all'][0]['dis']++;
					}
					if ($ro<0.9 && $rr>=0.5 && $ro<0.9 && $lr>=0.5) {
						//矯正兩眼均>=0.5
						$data_arr[$year_name][1][$sex][0]++;
						$data_arr['all'][1][$sex][0]++;
						$data_arr[$year_name][1][$sex]['all']++;
						$data_arr['all'][1][$sex]['all']++;
						$data_arr[$year_name][1]['all']++;
						$data_arr['all'][1]['all']++;
					} elseif (($ro<0.9 && $rr<0.1) || ($lo<0.9 && $lr<0.1)) {
						//矯正有一眼<0.1
						$data_arr[$year_name][1][$sex][2]++;
						$data_arr['all'][1][$sex][2]++;
						$data_arr[$year_name][1][$sex]['all']++;
						$data_arr['all'][1][$sex]['all']++;
						$data_arr[$year_name][1]['all']++;
						$data_arr['all'][1]['all']++;
						$data_arr[$year_name][1][$sex]['dis']++;
						$data_arr[$year_name][1]['dis']++;
					} elseif (($ro<0.9 && $rr<0.5) || ($ro<0.9 && $lr<0.5)) {
						//矯正有一眼<0.5
						$data_arr[$year_name][1][$sex][1]++;
						$data_arr['all'][1][$sex][1]++;
						$data_arr[$year_name][1][$sex]['all']++;
						$data_arr['all'][1][$sex]['all']++;
						$data_arr[$year_name][1]['all']++;
						$data_arr['all'][1]['all']++;
						$data_arr[$year_name][1][$sex]['dis']++;
						$data_arr[$year_name][1]['dis']++;
					}
				}
			}
			$smarty->assign("data_arr",$data_arr);
			$smarty->assign("ifile","health_sight_count.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "8":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['year_seme'] && $_POST['class_name']) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_sight();
			$health_data->get_checks("Oph");
			$dis_arr=array("My","Hy","Ast","Amb","other");
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$year_name=substr($seme_class,0,-2);
					$sex=$health_data->stud_base[$vv[student_sn]][stud_sex];
					$r=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['r'];
					$l=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['l'];
					//有任何視力記錄的視為有參與檢查
					if ($r[sight_o] || $l[sight_o] || $r[sight_r] || $l[sight_r]) {
						$data_arr[$year_name]['all'][$sex]++;
						$data_arr['all']['all'][$sex]++;
					}
					//裸視任一眼<0.9
					if ($r[sight_o]<0.9 || $l[sight_o]<0.9) {
						$data_arr[$year_name]['dis'][$sex]++;
						$data_arr['all']['dis'][$sex]++;
					}
					//統計單一狀況
					reset($dis_arr);
					foreach($dis_arr as $d) if ($r[$d] || $l[$d]) {
						$data_arr[$year_name][$d][$sex]++;
						$data_arr['all'][$d][$sex]++;
					}
					//統計斜視
					if (($health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['checks']['Oph3'])) {
						$data_arr[$year_name]['Oph3'][$sex]++;
						$data_arr['all']['Oph3'][$sex]++;
					}
					//統計近視+散光
					if (($r['My'] || $l['My']) && ($r['Amb'] || $l['Amb'])) {
						$data_arr[$year_name]['MA'][$sex]++;
						$data_arr['all']['MA'][$sex]++;
					}
					//統計遠視+散光
					if (($r['Hy'] || $l['Hy']) && ($r['Amb'] || $l['Amb'])) {
						$data_arr[$year_name]['HA'][$sex]++;
						$data_arr['all']['HA'][$sex]++;
					}
				}
			}
			$smarty->assign("data_arr",$data_arr);
			$smarty->assign("ifile","health_sight_diag_list.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "9":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['year_seme'] && $_POST['class_name']) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_sight();
			$cal_arr=array();
			$ydata=array();
			for($i=0;$i<=3;$i++) $ydata[$i]=0;
			while(list($sn,$v)=each($health_data->health_data)) {
				if ($v[$_POST['year_seme']]['r']['sight_o']>=0.9 && $v[$_POST['year_seme']]['l']['sight_o']>=0.9) $cal_arr[0]++;
				if ($v[$_POST['year_seme']]['r']['sight_r']>=0.5 && $v[$_POST['year_seme']]['l']['sight_r']>=0.5) $cal_arr[1]++;
				if (($v[$_POST['year_seme']]['r']['sight_o']<0.9 && $v[$_POST['year_seme']]['r']['sight_r']<0.5) || ($v[$_POST['year_seme']]['l']['sight_o']<0.9 && $v[$_POST['year_seme']]['l']['sight_r']<0.5)) {
					if ($v[$_POST['year_seme']]['r']['sight_r']==0 && $v[$_POST['year_seme']]['l']['sight_r']==0)
					$cal_arr[3]++;
					else
					$cal_arr[2]++;
				}
			}
			while(list($k,$v)=each($cal_arr)) {
				$ydata[$k]=$v;
			}

			//畫圖
			$sch=get_school_base();
			//session_register("ydata");
			$_SESSION["ydata"]=$ydata;
			//session_register("mtitle");
			$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 學生視力測量結果統計圖";
			//session_register("legend");
			$_SESSION["legend"]=array("正常","矯正後達標準","矯正後未達標準","未矯正");
			if (!in_array($_POST["graph_kind"],array("pie","flashpie"))) $_POST["graph_kind"]="pie";
			$_SESSION["graph_kind"]=$_POST["graph_kind"];
			$smarty->assign("ifile","health_graph_sel2.tpl");
		}
		break;
	case "10":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['year_seme'] && $_POST['class_name']) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_sight();
			$cal_arr=array();
			$ydata=array();
			$total_num=0;
			for($i=0;$i<=3;$i++) $ydata[$i]=0;
			while(list($sn,$v)=each($health_data->health_data)) {
				if ($v[$_POST['year_seme']]['r']['sight_o']>=0.9 && $v[$_POST['year_seme']]['l']['sight_o']>=0.9) {
					$cal_arr[0]++;
					$total_num++;
				} elseif ($v[$_POST['year_seme']]['r']['sight_o']<0.1 || $v[$_POST['year_seme']]['l']['sight_o']<0.1) {
					$cal_arr[3]++;
					$total_num++;
				} elseif ($v[$_POST['year_seme']]['r']['sight_o']<0.5 || $v[$_POST['year_seme']]['l']['sight_o']<0.5) {
					$cal_arr[2]++;
					$total_num++;
				} elseif ($v[$_POST['year_seme']]['r']['sight_o']<0.9 || $v[$_POST['year_seme']]['l']['sight_o']<0.9) {
					$cal_arr[1]++;
					$total_num++;
				}
			}
			while(list($k,$v)=each($cal_arr)) {
				$ydata[$k]=round($v/$total_num*100,2);
			}

			//畫圖
			$sch=get_school_base();
			//session_register("ydata");
			$_SESSION["ydata"]=$ydata;
			//session_register("mtitle");
			$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 學生裸視視力數值分布比率圖";
			//session_register("legend");
			$_SESSION["legend"]=array("兩眼裸視均達0.9以上","任一眼裸視0.8~0.5","任一眼裸視0.4~0.1","任一眼裸視未達0.1");
			//session_register("num_format");
			$_SESSION["num_format"]="%.2f";
			//session_register("unit");
			$_SESSION["unit"]="％";
			if (!in_array($_POST["graph_kind"],array("pie","flashpie"))) $_POST["graph_kind"]="pie";
			$_SESSION["graph_kind"]=$_POST["graph_kind"];
			$smarty->assign("ifile","health_graph_sel2.tpl");
		}
		break;
	case "11":
	case "12":
		$class_menu="";
		if ($_POST['year_seme']) {
			if ($_POST['sub_menu_id']==11) {
				$ctitle="裸視視力不良";
			} else {
				$ctitle="矯正視力未達標準";
			}
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,"all");
			$health_data->get_sight();
			$cal_arr=array();
			$ydata=array();
			$dy=($IS_JHORES==0)?6:3;
			for($i=0;$i<$dy;$i++) for($j=0;$j<2;$j++) $ydata[$i][$j]=0;
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$year_name=substr($seme_class,0,strlen($seme_class)-2);
					if ($_POST['sub_menu_id']==11) {
						if ($health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['r']['sight_o']<0.9 || $health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['l']['sight_o']<0.9)
						$cal_arr[$year_name][$health_data->stud_base[$vv[student_sn]][stud_sex]]++;
					} else {
						if (($health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['r']['sight_o']<0.9 && $health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['r']['sight_r']<0.5) || ($health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['l']['sight_o']<0.9 && $health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['l']['sight_r']<0.5))
						$cal_arr[$year_name][$health_data->stud_base[$vv[student_sn]][stud_sex]]++;
					}
				}
			}

			$dy=($IS_JHORES==0)?1:7;
			while(list($k,$v)=each($cal_arr)) {
				$ydata[0][$k-$dy]=$v[1];
				$ydata[1][$k-$dy]=$v[2];
				$ydata[2][$k-$dy]=$v[1]+$v[2];
				$xlabel[]=$k;
			}

			//畫圖
			//session_register("ydata");
			$_SESSION["ydata"]=$ydata;
			//session_register("mtitle");
			$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 ".$ctitle."各年級人數統計圖";
			//session_register("xtitle");
			$_SESSION["xtitle"]="年級";
			//session_register("ytitle");
			$_SESSION["ytitle"]="人數 (人)";
			//session_register("legend");
			$_SESSION["legend"]=array("男生","女生","全部");
			//session_register("xlabel");
			$_SESSION["xlabel"]=$xlabel;
			//session_register("graph_kind");
			if (!in_array($_POST["graph_kind"],array("bar","flashbar"))) $_POST["graph_kind"]="bar";
			$_SESSION["graph_kind"]=$_POST["graph_kind"];
			$smarty->assign("ifile","health_graph_sel.tpl");
		}
		break;
	case "13":
		$class_menu="";
		if ($_POST['year_seme']) {
			if (count($_POST['student_sn'])>0) {
				$health_data=new health_chart();
				foreach($_POST['student_sn'] as $s) $sn[]=$s;
				$health_data->set_stud($sn,$sel_year,$sel_seme);
				$health_data->get_sight();
				$health_data->set_class_num($sel_year,$sel_seme);
				$smarty->assign("health_data",$health_data);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("year_data",array("1"=>"一","2"=>"二","3"=>"三","4"=>"四","5"=>"五","6"=>"六","7"=>"七","8"=>"八","9"=>"九"));
				$smarty->assign("class_data",class_name($sel_year,$sel_seme));
				if ($_POST['noti'])
				$smarty->display("NTUnotification.tpl");
				else
				$smarty->display("health_sight_ntu_list.tpl");
				exit;
			}
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."'";
			$res=$CONN->Execute($query);
			$sn_arr=array();
			while(!$res->EOF) {
				$sn_arr[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$sn_str="'".implode("','",$sn_arr)."'";
				$sn_arr=array();
				$query="select * from health_sight_ntu where student_sn in (".$sn_str.") and ntu='2'";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn_arr[]=$res->fields['student_sn'];
					$res->MoveNext();
				}
				if (count($sn_arr)>0) {
					$health_data=new health_chart();
					$health_data->set_stud($sn_arr,$sel_year,$sel_seme);
				}
			}
			$smarty->assign("ifile","health_sight_ntu_noti.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "14":
		$class_menu="";
		if ($_POST['year_seme']) {
			if (count($_POST['student_sn'])>0) {
				$health_data=new health_chart();
				foreach($_POST['student_sn'] as $s) $sn[]=$s;
				$health_data->set_stud($sn,$sel_year,$sel_seme);
				$health_data->get_sight();
				$health_data->set_class_num($sel_year,$sel_seme);
				$smarty->assign("health_data",$health_data);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("year_data",array("1"=>"一","2"=>"二","3"=>"三","4"=>"四","5"=>"五","6"=>"六","7"=>"七","8"=>"八","9"=>"九"));
				$smarty->assign("class_data",class_name($sel_year,$sel_seme));
				if ($_POST['noti'])
				$smarty->display("COnotification.tpl");
				else
				$smarty->display("health_sight_co_list.tpl");
				exit;
			}
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."'";
			$res=$CONN->Execute($query);
			$sn_arr=array();
			while(!$res->EOF) {
				$sn_arr[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$sn_str="'".implode("','",$sn_arr)."'";
				$sn_arr=array();
				$query="select * from health_sight_co where student_sn in (".$sn_str.") and co='2'";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn_arr[]=$res->fields['student_sn'];
					$res->MoveNext();
				}
				if (count($sn_arr)>0) {
					$health_data=new health_chart();
					$health_data->set_stud($sn_arr,$sel_year,$sel_seme);
				}
			}
			$smarty->assign("ifile","health_sight_co_noti.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學生視力作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_sight.tpl");
?>
