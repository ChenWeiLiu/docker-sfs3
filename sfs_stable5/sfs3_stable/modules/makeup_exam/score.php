<?php
//$Id:$
include "config.php";

//認證
sfs_check();

//判斷學期
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$all_sm_arr = get_class_seme();
$year_seme_menu=year_seme_menu($sel_year,$sel_seme,"year_seme",$all_sm_arr);
$_POST['class_year'] = intval($_POST['class_year']);
$class_year_menu=class_year_menu($_POST['class_year']);

if ($_POST['class_year']>0) {
	$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' and seme_class like '".$_POST['class_year']."%' limit 0,10";
	$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	$sn_arr = array();
	while($rr=$res->FetchRow()) {
		$sn_arr[] = $rr['student_sn'];
	}
	$sn_str = "'".implode("','", $sn_arr)."'";
	$query="select distinct seme_year_seme from stud_seme where student_sn in ($sn_str) order by seme_year_seme desc";
	$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	$sm_arr = array();
	while($rr=$res->FetchRow()) {
		if ($all_sm_arr[$rr['seme_year_seme']]) {
			$sm_arr[$rr['seme_year_seme']] = $all_sm_arr[$rr['seme_year_seme']];
		}
	}
	if ($_POST['act_year_seme']) {
		$act_year=intval(substr($_POST['act_year_seme'],0,-1));
		$act_seme=intval(substr($_POST['act_year_seme'],-1,1));
		$_POST['act_year_seme']=sprintf("%03d",$act_year).$act_seme;
		if ($_POST['act_year_seme']>$_POST['year_seme']) $_POST['act_year_seme']="";
	}
	$year_seme_menu2=year_seme_menu($act_year,$act_seme,"act_year_seme",$sm_arr);
	$smarty->assign("sel_class_year",$_POST['class_year']);
}

//陣列資料
$m_arr = array(
	'lang'=>array('e'=>'language', 'c'=>'語文'),
	'math'=>array('e'=>'math', 'c'=>'數學'),
	'natu'=>array('e'=>'nature', 'c'=>'自然'),
	'soci'=>array('e'=>'social', 'c'=>'社會'),
	'heal'=>array('e'=>'health', 'c'=>'健體'),
	'art'=>array('e'=>'art', 'c'=>'藝文'),
	'comp'=>array('e'=>'complex', 'c'=>'綜合'),
);
if ($m_arr[$_POST['subj']]['e']=="") $_POST['subj']="";

//更新成績
if ($_POST['edit']) {
	$i=0;
	$now=date("Y-m-d H:i:s");
	foreach($_POST['nscore'] as $sn=>$d) {
		if($d<>$_POST['old_nscore'][$sn] && floatval($d)>=0 && floatval($d)<=100) {
			if ($d=="")
				$query="update makeup_exam_scope set nscore='', has_score='0', update_time='$now', teacher_sn='".$_SESSION['session_tea_sn']."' where seme_year_seme='".$_POST['act_year_seme']."' and student_sn='$sn' and scope_ename='".$m_arr[$_POST['subj']]['e']."'";
			else
				$query="update makeup_exam_scope set nscore='$d', has_score='1', update_time='$now', teacher_sn='".$_SESSION['session_tea_sn']."' where seme_year_seme='".$_POST['act_year_seme']."' and student_sn='$sn' and scope_ename='".$m_arr[$_POST['subj']]['e']."'";
			$res=$CONN->Execute($query) or user_error("更新失敗！<br>$query",256);
			$chg_arr[$sn]=1;
			$i++;
		}
	}
	$smarty->assign("chg_arr",$chg_arr);
	$smarty->assign("msg","您已成功修改 ".$i." 筆成績");
}

//擇優計算
if ($_POST['act']) {
	cal_better_score($_POST['act_year_seme'], ($_POST['class_year']-($sel_year-$act_year)), $m_arr[$_POST['subj']]['e']);
}

//取出名冊
if ($_POST['class_year']>0 && $_POST['act_year_seme'] && $_POST['subj']) {
	$query="select * from makeup_exam_scope where seme_year_seme='".$_POST['act_year_seme']."' and scope_ename='".$m_arr[$_POST['subj']]['e']."' and class_year='".($_POST['class_year']-($sel_year-$act_year))."'";
	$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	$sn_arr = array();
	while($rr=$res->FetchRow()) {
		$all_arr[$rr['student_sn']]['oscore']=$rr['oscore'];
		$all_arr[$rr['student_sn']]['nscore']=$rr['nscore'];
		$all_arr[$rr['student_sn']]['has_score']=$rr['has_score'];
		$sn_arr[]=$rr['student_sn'];
	}
	$sn_str="'".implode("','",$sn_arr)."'";
	$query="select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,a.stud_study_cond,b.seme_class,b.seme_num from stud_base a left join stud_seme b on a.student_sn=b.student_sn where a.student_sn in ($sn_str) and b.seme_year_seme='".$_POST['year_seme']."' order by b.seme_class,b.seme_num";
	$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	while($rr=$res->FetchRow()) {
		$base_arr[$rr['student_sn']]=array('stud_id'=>$rr['stud_id'], 'seme_class'=>$rr['seme_class'], 'seme_num'=>$rr['seme_num'], 'stud_name'=>$rr['stud_name'], 'stud_sex'=>$rr['stud_sex'], 'stud_study_cond'=>$rr['stud_study_cond']);
	}
	$smarty->assign("data_arr",$all_arr);
	$smarty->assign("base_arr",$base_arr);
}

//秀出網頁布景標頭
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","補行評量成績作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("year_seme_menu2",$year_seme_menu2);
$smarty->assign("class_year_menu",$class_year_menu);
$smarty->display("score.html");
?>
