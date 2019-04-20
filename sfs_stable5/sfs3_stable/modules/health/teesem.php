<?php

// $Id: teesem.php 5668 2009-09-24 08:27:39Z brucelyc $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['df_item']=="") $_POST['df_item']="default_jh";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","口檢未檢查名單","口檢結果通知單","口檢統計表","含氟漱口水實施表","含氟水漱口統計表","潔牙統計","潔牙記錄表");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST[sub_menu_id]) {
	case "1":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_teeth();
			$health_data->get_checks("Ora");
			if ($_POST['print']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(3);
				$x->addSheet($_POST['class_name']);
				$x->setRowText(array("年級","班級","座號","姓名"));
				$x->items=get_utee($health_data,$_POST['year_seme']);
				$x->writeSheet();
				$x->process();
				exit;
			}
			$smarty->assign("health_data",$health_data);
			$smarty->assign("ifile","health_teesem_unmeasure.tpl");
		}
		break;
	case "2":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			if ($_POST['student_sn']>0) {
				foreach($_POST['student_sn'] as $s) $sn[]=$s;
				$health_data->set_stud($sn,$sel_year,$sel_seme);
				$health_data->get_teeth();
				$health_data->get_checks("Ora");
				$smarty->assign("health_data",$health_data);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("year_data",year_base($sel_year,$sel_seme));
				$smarty->assign("class_data",class_name($sel_year,$sel_seme));
				$smarty->display("Teethnotification.tpl");
				exit;
			}
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_teeth();
			$health_data->get_checks("Ora");
			$smarty->assign("health_data",$health_data);
			$smarty->assign("ifile","health_teesem_noti.tpl");
		}
		break;
	case "3":
		$class_menu="";
		$health_data=new health_chart();
		$health_data->get_stud_base($sel_year,$sel_seme,"all");
		$data_arr=array();
		while(list($seme_class,$v)=each($health_data->stud_data)) {
			$sn=array();
			$y=substr($seme_class,0,-2);
			$c=substr($seme_class,-2,2);
			while(list($seme_num,$vv)=each($v)) {
				$sn[]=$vv[student_sn];
				$data_arr[$y][$c][nums]++;
				$data_arr[$y]['all'][nums]++;
				$data_arr['all']['all'][nums]++;
			}
			if (count($sn)>0) {
				$sn_str="'".implode("','",$sn)."'";
				//計算牙齒狀況
				$query="select no,student_sn,status,count(no) as num from health_teeth where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str) and (status='1' or status='3' or (no<'T51' and status='2') or (no>='T51' and status='4')) group by no,student_sn";
				$res=$CONN->Execute($query);
				$temp_arr=array();
				while(!$res->EOF) {
					$s=$res->fields['student_sn'];
					$k=$res->fields['status'];
					$num=$res->fields['num'];
					$n=($res->fields['no']>="T51")?2:1;
					//齲齒代碼為1, 缺牙代碼為2, 已矯治代碼為3, 待拔牙代碼為4
					if (!in_array($s,$temp_arr)) {
						$temp_arr[]=$s;
						$data_arr[$y][$c]['ptotal']++;
						$data_arr[$y]['all']['ptotal']++;
						$data_arr['all']['all']['ptotal']++;
					}
					$data_arr[$y][$c][$n][$k]+=$num;
					$data_arr[$y][$c][$n]['ttotal']+=$num;
					$data_arr[$y][$c]['ttotal']+=$num;
					$data_arr[$y]['all'][$n][$k]+=$num;
					$data_arr['all']['all'][$n][$k]+=$num;
					$res->MoveNext();
				}
				//計算口腔狀況
				$query="select no,count(no) as num from health_checks_record where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str) and no in ('1','4','5') and subject='Ora' and status='1' group by no";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$n=$res->fields['no'];
					$num=$res->fields['num'];
					if ($n==1) $nn=3;
					elseif ($n==5) $nn=4;
					elseif ($n==4) $nn=5;
					else $nn=0;
					if ($nn>0) {
						$data_arr[$y][$c][$nn]=$num;
						$data_arr[$y]['all'][$nn]+=$num;
						$data_arr['all']['all'][$nn]+=$num;
					}
					$res->MoveNext();
				}
			}
		}
		$smarty->assign("data_arr",$data_arr);
		$smarty->assign("ifile","health_teesem_count.tpl");
		break;
	case "4":
		if ($_POST['class_name']=="") {
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
						$query="select student_sn from stud_base where stud_study_cond in (0,15) and student_sn in ($sn_str)";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							$temp_arr[$k]['num']++;
							$sn_arr[$k][]=$res->fields['student_sn'];
							$res->MoveNext();
						}
						if (count($sn_arr[$k])>0) {
							$sn_str="'".implode("','",$sn_arr[$k])."'";
							$query="select count(agree) as y from health_frecord where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str) and agree='1'";
							$res=$CONN->Execute($query);
							$temp_arr[$k]['y']=$res->fields['y'];
							$query="select count(agree) as n from health_frecord where year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str) and agree='0'";
							$res=$CONN->Execute($query);
							$temp_arr[$k]['n']=$res->fields['n'];
							$temp_arr[$k]['u']=$temp_arr[$k]['num']-$temp_arr[$k]['y']-$temp_arr[$k]['n'];
						}
					}
				}
			}
			$smarty->assign("rowdata",$temp_arr);
			$smarty->assign("ifile","health_teesem_fcount.tpl");
		}
		if ($_POST['class_name']) {
			$query="select * from health_fday where year='$sel_year' and semester='$sel_seme' order by week_no";
			$res=$CONN->Execute($query);
			$smarty->assign("rows",$res->RecordCount()+2);
			$smarty->assign("date_arr",$res->GetRows());
			if ($_POST['allchart']) {
				$c=class_base($_POST['year_seme']);
				$smarty->assign("class_data",$c);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("i_arr",array(1,2,3,4,5));
				while(list($k,$v)=each($c)) {
					$_POST['class_name']=$k;
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$k);
					$health_data->get_frecord();
					$smarty->assign("health_data",$health_data);
					$smarty->display("health_teesem_fchart.tpl");
				}
				exit;
			} else {
				$health_data=new health_chart();
				$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
				$health_data->get_frecord();
				$smarty->assign("health_data",$health_data);
				if ($_POST['chart']) {
					$smarty->assign("class_data",class_base($_POST['year_seme']));
					$smarty->assign("school_data",get_school_base());
					$smarty->assign("i_arr",array(1,2,3,4,5));
					$smarty->display("health_teesem_fchart.tpl");
					exit;
				} else
					$smarty->assign("ifile","health_teesem_fclass.tpl");
			}
		}
		break;
	case "5":
		$class_menu="";
		$query="select * from health_fday where year='$sel_year' and semester='$sel_seme' order by week_no";
		$res=$CONN->Execute($query);
		$r=$res->GetRows();
		$smarty->assign("date_arr",$r);
		$maxd=$r[count($r)-1]['week_no'];
		$smarty->assign("maxd",$maxd);
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
						$res->MoveNext();
					}
					if (count($sn_arr[$k])>0) {
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
						for($i=1;$i<=$maxd;$i++) {
							$query="select count(student_sn) as n from health_frecord where w$i=2 and year='$sel_year' and semester='$sel_seme' and student_sn in ($sn_str)";
							$res=$CONN->Execute($query);
							$temp_arr[$k]['d']+=$res->fields['n'];
						}
					}
				}
			}
		}
		$smarty->assign("rowdata",$temp_arr);
		$smarty->assign("ifile","health_teesem_fcount2.tpl");
		break;
	case "7":
		if ($_POST['class_name']) {
			for($i=1;$i<=31;$i++) $date_arr[$i]=$i;
			$smarty->assign("date_arr",$date_arr);
			$smarty->assign("rows",count($date_arr)+2);
			$smarty->assign("school_data",get_school_base());
			//所有學生下再加五欄
			$smarty->assign("i_arr",array(1,2,3,4,5));
			if ($_POST['allchart']) {
				$c=class_base($_POST['year_seme']);
				$smarty->assign("class_data",$c);
				while(list($k,$v)=each($c)) {
					$_POST['class_name']=$k;
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$k);
					$smarty->assign("health_data",$health_data);
					$smarty->display("health_teesem_cchart.tpl");
				}
				exit;
			} else {
				$health_data=new health_chart();
				$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
				$smarty->assign("health_data",$health_data);
				if ($_POST['chart']) {
					$smarty->assign("class_data",class_base($_POST['year_seme']));
					$smarty->display("health_teesem_cchart.tpl");
					exit;
				} else
					$smarty->assign("ifile","health_teesem_cclass.tpl");
			}
		}
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學生口腔檢查作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_teesem.tpl");
?>
