<?php

// $Id: other.php 7707 2013-10-23 12:13:23Z smallduh $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['df_item']=="") $_POST['df_item']="default_jh";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","個人疾病史清單","特殊疾病統計","班級人數","傳染病監視通報系統資料匯出");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST[sub_menu_id]) {
	case "1":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$rowtext=array("年級","班級","座號","姓名","疾病","陳述","照護");
			$d_arr=hDiseaseKind();
			if ($_POST['ods_all']) {
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$temp_class[]=substr($_POST['class_name'],0,1);
				$temp_arr=class_base(sprintf("%03d",$sel_year).$sel_seme,$temp_class);
				$x->setRowText($rowtext);
				while(list($k,$v)=each($temp_arr)) {
					$x->addSheet($k);
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$k,1);
					$health_data->get_disease();
					$a_arr=array();
					while(list($sn,$d)=each($health_data->stud_base)) {
						if ($d[disease]) {
							while(list($kk,$vv)=each($d[disease])) {
								$a_arr[]=array(substr($d[seme_class],0,-2),substr($d[seme_class],-2,2),$d[seme_num],$d[stud_name],$d_arr[$vv],br2nl($d[status_record][disease][$vv]),br2nl($d[diag_record][disease][$vv]));
							}
						}
					}
					$x->items=$a_arr;
					$x->writeSheet();
				}
				$x->process();
				exit;
			}
			if ($_POST['xls']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(1);
				$x->addSheet($_POST['class_name']);
				$x->setRowText($rowtext);
				$health_data=new health_chart();
				$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name'],1);
				$health_data->get_disease();
				$a_arr=array();
				while(list($sn,$d)=each($health_data->stud_base)) {
					if ($d[disease]) {
						while(list($kk,$vv)=each($d[disease])) {
							$a_arr[]=array(substr($d[seme_class],0,-2),substr($d[seme_class],-2,2),$d[seme_num],$d[stud_name],$d_arr[$vv],br2nl($d[status_record][disease][$vv]),br2nl($d[diag_record][disease][$vv]));
						}
					}
				}
				$x->items=$a_arr;
				$x->writeSheet();
				$x->process();
				exit;
			}
			if ($_POST['ods']) {
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$x->addSheet($_POST['class_name']);
				$x->setRowText($rowtext);
				$health_data=new health_chart();
				$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name'],1);
				$health_data->get_disease();
				$a_arr=array();
				while(list($sn,$d)=each($health_data->stud_base)) {
					if ($d[disease]) {
						while(list($kk,$vv)=each($d[disease])) {
							$a_arr[]=array(substr($d[seme_class],0,-2),substr($d[seme_class],-2,2),$d[seme_num],$d[stud_name],$d_arr[$vv],br2nl($d[status_record][disease][$vv]),br2nl($d[diag_record][disease][$vv]));
						}
					}
				}
				$x->items=$a_arr;
				$x->writeSheet();
				$x->process();
				exit;
			}
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name'],1);
			if ($_POST['save'] && $_POST['update']) $health_data->update_disease($_POST['update']);
			$health_data->get_disease();
			$smarty->assign("health_data",$health_data);
			$smarty->assign("disease_kind_arr",hDiseaseKind());
			if ($_POST['edit'])
				$smarty->assign("ifile","health_other_dis_input.tpl");
			else
				$smarty->assign("ifile","health_other_dis_list.tpl");
		}
		break;
	case "2":
		$class_menu="";
		$query="select distinct di_id from health_disease order by di_id";
		$res=$CONN->Execute($query);
		$smarty->assign("dis_arr",$res->GetRows());
		$query="select student_sn from health_disease";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sn_arr[]=$res->fields['student_sn'];
			$res->MoveNext();
		}
		if (count($sn_arr)>0) {
			$sn_str="'".implode("','",$sn_arr)."'";
			$sn_arr=array();
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."' and student_sn in ($sn_str)";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn_arr[substr($res->fields['seme_class'],0,1)][]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$temp_arr=array();
				while(list($k,$v)=each($sn_arr)) {
					if (count($sn_arr[$k])>0) {
						$sn_str="'".implode("','",$sn_arr[$k])."'";
						$query="select count(student_sn) as n,di_id from health_disease where student_sn in ($sn_str) group by di_id";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							$temp_arr[$k][$res->fields['di_id']]=$res->fields['n'];
							$temp_arr['all'][$res->fields['di_id']]+=$res->fields['n'];
							$res->MoveNext();
						}
					}
				}
			}
		}
		$smarty->assign("rowdata",$temp_arr);
		$smarty->assign("class_year",$class_year);
		$smarty->assign("disease_kind_arr",hDiseaseKind());
		$smarty->assign("ifile","health_other_count.tpl");
		break;
	case "3":
		$year_seme_menu="";
		$class_menu="";
		$query="select sum(stud_sex=1) as boy, sum(stud_sex=2) as girl, substring(curr_class_num,1,3) as c from stud_base where stud_study_cond in (0,15) group by c order by c";
		$res=$CONN->Execute($query);
		$temp_arr=array();
		while(!$res->EOF) {
			$c=$res->fields['c'];
			if (substr($c,-2,2)!="00") {
				$temp_arr[$c][1]=$res->fields['boy'];
				$temp_arr[$c][2]=$res->fields['girl'];
				$temp_arr[$c]['all']=$temp_arr[$c][1]+$temp_arr[$c][2];
				$cc=substr($c,0,1);
				$temp_arr[$cc][1]+=$temp_arr[$c][1];
				$temp_arr[$cc][2]+=$temp_arr[$c][2];
				$temp_arr[$cc]['all']+=$temp_arr[$c]['all'];
				$temp_arr[$cc]['nums']++;
				$temp_arr['all'][1]+=$temp_arr[$c][1];
				$temp_arr['all'][2]+=$temp_arr[$c][2];
				$temp_arr['all']['all']+=$temp_arr[$c]['all'];
				$temp_arr['all']['nums']++;
			}
			$res->MoveNext();
		}
		$query="select * from stud_base where stud_study_cond='15' order by curr_class_num";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$c=substr($res->fields['curr_class_num'],0,3);
			$n=substr($res->fields['curr_class_num'],-2,2);
			$temp_arr2[$c][$n]['stud_name']=$res->fields['stud_name'];
			$temp_arr2[$c][$n]['stud_sex']=$res->fields['stud_sex'];
			$res->MoveNext();
		}	
		$smarty->assign("nums_arr",$temp_arr);
		$smarty->assign("pers_arr",$temp_arr2);
		$smarty->assign("class_arr",class_base($_POST['year_seme']));
		$smarty->assign("year_arr",year_base($sel_year,$sel_seme));
		$smarty->assign("ifile","health_other_stud_num.tpl");
		break;
	case "4":
		$class_menu="";
		$s_arr=get_school_base();
		if ($_POST['cdc']) {
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."' order by seme_class,seme_num";
			$res=$CONN->Execute($query);
			$temp_arr=array();
			while(!$res->EOF) {
				$temp_arr[]=$res->fields['student_sn'];
				$temp_arr2[$res->fields['student_sn']]['seme_class']=$res->fields['seme_class'];
				$temp_arr2[$res->fields['student_sn']]['seme_num']=$res->fields['seme_num'];
				$res->MoveNext();
			}
			if (count($temp_arr)>0) {
				$sn_str="'".implode("','",$temp_arr)."'";
				$query="select student_sn,stud_name,stud_person_id,stud_sex from stud_base where student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn=$res->fields['student_sn'];
					$temp_arr2[$sn]['stud_name']=$res->fields['stud_name'];
					$temp_arr2[$sn]['stud_sex']=$res->fields['stud_sex'];
					$res->MoveNext();
				}
				header("Content-disposition: attachment; filename=CDCSt".$s_arr['sch_id']."_".$sel_year.$sel_seme.".csv");
				header("Content-type: text/x-csv ; Charset=Big5");
				//header("Pragma: no-cache");
								//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

				header("Expires: 0");
				$smarty->assign("s_arr",$s_arr);
				$smarty->assign("rowdata",$temp_arr2);
				$smarty->display("health_other_cdc.tpl");
			}
			exit;
		} elseif ($_POST['inject']) {
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."' order by seme_class,seme_num";
			$res=$CONN->Execute($query);
			$temp_arr=array();
			while(!$res->EOF) {
				$temp_arr[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($temp_arr)>0) {
				$sn_str="'".implode("','",$temp_arr)."'";
				$query="select student_sn,stud_name,stud_person_id,stud_birthday,curr_class_num from stud_base where student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				$temp_arr=array();
				while(!$res->EOF) {
					$sn=$res->fields['student_sn'];
					$temp_arr[$sn]['stud_name']=$res->fields['stud_name'];
					$temp_arr[$sn]['stud_person_id']=$res->fields['stud_person_id'];
					$temp_arr[$sn]['stud_birthday']=$res->fields['stud_birthday'];
					$temp_arr[$sn]['seme_year']=substr($res->fields['curr_class_num'],0,-4);
					$res->MoveNext();
				}
				$query="select * from health_inject_item";
				$res=$CONN->Execute($query);
				$temp_arr3=array();
				while(!$res->EOF) {
					$temp_arr3[$res->fields['id']]=$res->fields['code'];
					$res->MoveNext();
				}
				$d_arr=curr_year_seme_day();
				$query="select * from health_inject_record where update_date>='$d_arr[start]'";
				$res=$CONN->Execute($query);
				$temp_arr2=array();
				while(!$res->EOF) {
					for($i=1;$i<=4;$i++) {
						if ($res->fields['date'.$i]>=$d_arr[start]) {
							$temp_arr2[$res->fields['student_sn']]=array("id"=>$temp_arr3[$res->fields['id']],"no"=>$i,"date"=>$res->fields['date'.$i]);
						}
					}
					$res->MoveNext();
				}
				header("Content-disposition: attachment; filename=".$s_arr['sch_id']."預防接種".$sel_year.$sel_seme.".csv");
				header("Content-type: text/x-csv ; Charset=Big5");
				header("Pragma: no-cache");
				header("Expires: 0");
				$smarty->assign("s_arr",$s_arr);
				$smarty->assign("rowdata",$temp_arr2);
				$smarty->assign("basedata",$temp_arr);
				$smarty->display("health_other_inject.tpl");
			}
			exit;
		}
		$smarty->assign("s_arr",$s_arr);
		$smarty->assign("ifile","health_other_disease.tpl");
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","其他報表");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_general.tpl");
?>
