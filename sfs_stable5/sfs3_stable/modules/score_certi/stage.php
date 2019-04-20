<?php 
// $Id: stage.php 9240 2018-05-04 03:25:04Z igogo $

// 載入設定檔
include_once "../../include/config.php";

// 認證檢查
sfs_check();

if ($_POST['kind']=="各學期定考成績單") {
	$student_sn=intval($_POST['student_sn']);
	if ($student_sn>0) {
		//取得在校各學期班別
		$query="select * from stud_seme where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$temp_arr=array();
		while(!$res->EOF) {
			$temp_arr[$res->fields['seme_year_seme']]=$res->fields['seme_class'];
			$res->MoveNext();
		}
		//取得在校各學期課程
		$std_ss=array("語文-本國語文"=>"1","語文-英語"=>"2","數學"=>"3","自然與生活科技"=>"4","社會"=>"5","藝術與人文"=>"6","健康與體育"=>"7","綜合活動"=>"8");
		$subj_arr=array();
		$query="select * from score_subject";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$subj_arr[$res->fields['subject_id']]=$res->fields['subject_name'];
			$res->MoveNext();
		}
		$all_ss=array();
		$all_map=array();
		$all_score=array();
		for($i=1;$i<=8;$i++) $all_ss[$i]=array();
		foreach($temp_arr as $seme_year_seme=>$seme_class) {
			$year=intval(substr($seme_year_seme,0,-1));
			$seme=intval(substr($seme_year_seme,-1,1));
			$c_year=intval(substr($seme_class,0,-2));
			$c_class=intval(substr($seme_class,-2,2));
			$class_id=sprintf("%03d_%d_%02d_%02d",$year,$seme,$c_year,$c_class);
			$temp_ss=array();
			$query="select * from score_ss where year='$year' and semester='$seme' and class_id='$class_id' and enable='1' and print='1' order by year,semester";
			$res=$CONN->Execute($query);
			if ($res->RecordCount()==0) {
				$query="select * from score_ss where year='$year' and semester='$seme' and class_id='' and class_year='$c_year' and enable='1' and print='1' order by year,semester";
				$res=$CONN->Execute($query);
			}
			while(!$res->EOF) {
				$subject_id=$res->fields['subject_id'];
				if ($subject_id==0) $subject_id=$res->fields['scope_id'];
				$all_ss[$std_ss[$res->fields['link_ss']]][$subj_arr[$subject_id]][]=$res->fields['ss_id'];
				$all_map[$res->fields['ss_id']]=$subj_arr[$subject_id];
				$temp_ss[]=$res->fields['ss_id'];
				$res->MoveNext();
			}
			//取得當學期所有定考成績
			if (count($temp_ss)>0) {
				$ss_str="'".implode("','",$temp_ss)."'";
				$query="select * from score_semester_".$year."_".$seme." where student_sn='$student_sn' and test_kind='定期評量' and ss_id in ($ss_str) order by test_sort";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$all_score[$year][$seme][$all_map[$res->fields['ss_id']]][$res->fields['test_sort']]=$res->fields['score'];
					$res->MoveNext();
				}
			}
		}
		$query="select * from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$smarty->assign("stud_name",$res->fields['stud_name']);
		$smarty->assign("stud_id",$res->fields['stud_id']);
		$sb=explode("-",$res->fields['stud_birthday']);
		$smarty->assign("stud_birthday",($sb[0]-1911)."年".$sb[1]."月".$sb[2]."日");
		$smarty->assign("stud_study_year",$res->fields['stud_study_year']);
		$smarty->assign("all_ss",$all_ss);
		$smarty->assign("all_score",$all_score);
		$smarty->assign("test_sort",array(1,2,3));
		$smarty->assign("year",Num2CNum(intval(date("Y")-1911)));
		$smarty->assign("month",Num2CNum(intval(date("m"))));
		$smarty->assign("day",Num2CNum(intval(date("d"))));
		$smarty->assign("sch",get_school_base());
		$smarty->display("score_certi_stage_print.tpl");
		exit;
	}
}
?>
