<?php

// $Id: score_input.php 5869 2010-02-25 15:11:29Z brucelyc $

// --系統設定檔
include "select_data_config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

$ss_link=array(1=>"chinese",2=>"english",3=>"math",4=>"nature",5=>"social",6=>"health",7=>"art",8=>"complex");
if ($IS_JHORES==0)
	$f_year=5;
else
	$f_year=2;

if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$ss_link=array(1=>"chinese",2=>"english",3=>"math",4=>"nature",5=>"social",6=>"health",7=>"art",8=>"complex");

if ($_POST['year_name']) {
	//取出入學年
	$stud_study_year=get_stud_study_year($seme_year_seme,$_POST['year_name']);

	$times=array();
	for($i=$stud_study_year;$i<=$stud_study_year+2;$i++) {
		for($j=1;$j<=2;$j++) {
			$k=sprintf("%03d",$i).$j;
			$semes[$k]=$i."學年度第".$j."學期";
			$query="select * from score_setup where year='".intval($i)."' and semester='$j' and class_year='".($i-$stud_study_year+$IS_JHORES+1)."' and enable='1'";
			$res=$CONN->Execute($query);
			$times[$k]=$res->fields['performance_test_times'];
		}
	}

	//取出所有轉入學生資料
	$query="select * from stud_move where move_kind='2' order by move_date";
	$res=$CONN->Execute($query);
	$sn_arr=array();
	$rowdata=array();
	while(!$res->EOF) {
		$sn_arr[]=$res->fields['student_sn'];
		$rowdata[$res->fields['student_sn']][move_year_seme]=sprintf("%04d",$res->fields['move_year_seme']);
		$rowdata[$res->fields['student_sn']][move_date]=$res->fields['move_date'];
		$res->MoveNext();
	}
	if (count($sn_arr)>0) {
		$sn_str="'".implode("','",$sn_arr)."'";
		$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '".$_POST['year_name']."%' and student_sn in ($sn_str)";
		$res=$CONN->Execute($query);
		$sn_arr=array();
		while(!$res->EOF) {
			$sn_arr[]=$res->fields['student_sn'];
			$rowdata[$res->fields['student_sn']][seme_class]=$res->fields['seme_class'];
			$rowdata[$res->fields['student_sn']][seme_num]=$res->fields['seme_num'];
			$res->MoveNext();
		}
		if (count($sn_arr)>0) {
			$sn_str="'".implode("','",$sn_arr)."'";
			$query="select * from stud_base where student_sn in ($sn_str) and stud_study_cond in ('0','5','15') order by curr_class_num";
			$res=$CONN->Execute($query);
			$sn_arr=array();
			while(!$res->EOF) {
				if ($res->fields['student_sn']) {
					$sn_arr[]=$res->fields['student_sn'];
					$rowdata[$res->fields['student_sn']][stud_name]=$res->fields['stud_name'];
					$rowdata[$res->fields['student_sn']][stud_sex]=$res->fields['stud_sex'];
				}
				$res->MoveNext();
			}
		}
	}

	$_POST['student_sn']=intval($_POST['student_sn']);
	if (in_array($_POST['student_sn'],$sn_arr)) {
			$smarty->assign("input_sn",$_POST['student_sn']);
			$smarty->assign("ss_link",$ss_link);
			$smarty->assign("s_arr",array(1=>"本國語文",2=>"英語",3=>"數學",4=>"自然與生活科技",5=>"社會",6=>"健康與體育",7=>"藝術與人文",8=>"綜合活動"));
			for($i=1;$i<=5;$i++) for($j=1;$j<=$i;$j++) $temp_arr[$i][$j]=$j;
			$query="select * from score_semester_move where student_sn='".$_POST['student_sn']."'";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$score_arr[$_POST['student_sn']][sprintf("%03d",$res->fields['year']).$res->fields['semester']][$res->fields['ss_id']][$res->fields['test_sort']]=$res->fields['score'];
				$res->MoveNext();
			}
			$smarty->assign("score_arr",$score_arr);
	} elseif (count($_POST['score'])>0) {
		foreach($_POST['score'] as $sn=>$d) {
			if (in_array($sn,$sn_arr)) {
				$query="delete from score_semester_move where student_sn='$sn'";
				$res=$CONN->Execute($query);
				foreach($d as $score_seme=>$dd) {
					foreach($dd as $score_subj=>$ddd) {
						foreach($ddd as $score_stage=>$score) {
							$score=floatval($score);
							if ($semes[$score_seme] && $ss_link[$score_subj] && $score_stage>0 && $score_stage<=$times[$score_seme] && $score<=100 && $score>=0) {
								$year=substr($score_seme,0,3);
								$semester=substr($score_seme,-1,1);
								$query="insert into score_semester_move (year,semester,student_sn,ss_id,score,test_kind,test_sort,update_time) values ('$year','$semester','$sn','$score_subj','$score','定期評量','$score_stage','".date("Y-m-d H:i:s")."')";
								$res=$CONN->Execute($query);
							}
						}
					}
				}
			}
		}
	}
	$smarty->assign("sn_arr",$sn_arr);
	$smarty->assign("rowdata",$rowdata);
	$smarty->assign("semes",$semes);
	$smarty->assign("times",$times);
	$smarty->assign("ff",$temp_arr);
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","免試入學定考成績補登"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name]));
$smarty->assign("seme_year_seme",$seme_year_seme);
$smarty->display("stud_basic_test_score_input.tpl");
?>
