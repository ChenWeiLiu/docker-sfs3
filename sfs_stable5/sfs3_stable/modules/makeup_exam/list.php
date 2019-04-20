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
	$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' and seme_class like '".$_POST['class_year']."%'";
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
}

//傳遞日期參數
$smarty->assign("year",date("Y")-1911);
$smarty->assign("month",date("m"));
$smarty->assign("day",date("d"));

//列印多學期通知單
if ($_POST['class_year']>0 && $_POST['notin']) {
	if (count($_POST['chart_seme'])>0) {
		$i = 0;
		foreach($_POST['chart_seme'] as $k=>$v) {
			$chart_seme[] = $k;
			if ($i==0) $s = "";
			elseif ($i==1) $s = " 及 ";
			else $s = " 、 ";
			$cstr = "<B>".$all_sm_arr[$k]."</B>".$s.$cstr;
			$i++;
		}
		if (mb_strlen($cstr,"big5")>40) $cstr = mb_substr($cstr,0,38,"big5")."<BR>　　".mb_substr($cstr,38,(mb_strlen($cstr,"big5")-38),"big5");
		$cs_str = "'".implode("','",$chart_seme)."'";
		$query="select * from makeup_exam_scope where student_sn in ($sn_str) and seme_year_seme in ($cs_str) order by student_sn, seme_year_seme, scope_ename";
		$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
		$sn_arr = array();
		while($rr=$res->FetchRow()) {
			$all_arr[$rr['student_sn']][$rr['seme_year_seme']][$rr['scope_ename']]=$rr['oscore'];
			$sn_arr[]=$rr['student_sn'];
		}
		$sn_str="'".implode("','",$sn_arr)."'";
		$query="select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,a.stud_study_cond,b.seme_class,b.seme_num from stud_base a left join stud_seme b on a.student_sn=b.student_sn where a.student_sn in ($sn_str) and b.seme_year_seme='".$_POST['year_seme']."' order by b.seme_class,b.seme_num";
		$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
		$base_arr = array();
		while($rr=$res->FetchRow()) {
			$base_arr[$rr['student_sn']]=array('stud_id'=>$rr['stud_id'], 'class_year'=>substr($rr['seme_class'],0,-2), 'seme_class'=>intval(substr($rr['seme_class'],-2,2)), 'seme_num'=>$rr['seme_num'], 'stud_name'=>$rr['stud_name'], 'stud_sex'=>$rr['stud_sex'], 'stud_study_cond'=>$rr['stud_study_cond']);
		}
		$smarty->assign("data_arr",$all_arr);
		$smarty->assign("base_arr",$base_arr);
		$smarty->assign("sel_year",$sel_year);
		$smarty->assign("sel_seme",$sel_seme);
		$smarty->assign("seme_arr",$all_sm_arr);
		$smarty->assign("seme_str",$cstr);
		$smarty->assign("school_data",get_school_name());
		$sb = get_school_base();
		$smarty->assign("school_sheng",$sb['sch_sheng']);
		$smarty->display("noti.html");
		exit;
	} else
		$smarty->assign("msg","未選列印學期");
}

//取出課程設定
if ($_POST['class_year']>0 && $_POST['act_year_seme']) {
	//取出科目中文名
	$query="select * from score_subject order by subject_id";
	$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	while($rr=$res->FetchRow()) {
		$subj_arr[$rr['subject_id']] = $rr['subject_name'];
	}

	//取出課程設定
	$query="select * from score_ss where year='$act_year' and semester='$act_seme' and class_year='".($_POST['class_year']-($sel_year-$act_year))."' and link_ss<>'' and enable='1'";
	$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	while($rr=$res->FetchRow()) {
		if(mb_substr($rr['link_ss'],0,2,"big5")=="語文") $rr['link_ss'] = "語文";
		if($rr['subject_id']==0) $rr['subject_id'] = $rr['scope_id'];
		if($rr['class_id']=="") $rr['class_id'] = "全年級";
		$sname = $subj_arr[$rr['subject_id']];
		$setup_arr[]=array($rr['class_id'], $rr['link_ss'], $sname, $rr['ss_id'], $rr['rate']);
	}
	$smarty->assign("sel_class_year",$_POST['class_year']);
	$smarty->assign("setup_arr",$setup_arr);
}

//篩選學生
if ($_POST['cal'] || $_POST['export'] || $_POST['insert'] || $_POST['noti1']  || $_POST['list']) {
	$query="select a.student_sn,a.stud_id,a.seme_class,a.seme_num,b.stud_name,b.stud_sex from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='".$_POST['year_seme']."' and mid(a.seme_class,1,LENGTH(a.seme_class)-2)='".$_POST['class_year']."' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	while($rr=$res->FetchRow()) {
		$sn_arr[] = $rr['student_sn'];
		$base_arr[$rr['student_sn']]=array('stud_id'=>$rr['stud_id'], 'seme_class'=>$rr['seme_class'], 'seme_num'=>$rr['seme_num'], 'stud_name'=>$rr['stud_name'], 'stud_sex'=>$rr['stud_sex'], 'class_year'=>substr($rr['seme_class'],0,-2), 'class_num'=>substr($rr['seme_class'],-2,2));
	}
	$seme_arr = array($_POST['act_year_seme']);
	$all_arr = cal_fin_score($sn_arr,$seme_arr);
	$smarty->assign("data_arr",$all_arr);
	$smarty->assign("base_arr",$base_arr);
	$m_arr = array(
		'lang'=>array('e'=>'language', 'c'=>'語文'),
		'math'=>array('e'=>'math', 'c'=>'數學'),
		'natu'=>array('e'=>'nature', 'c'=>'自然'),
		'soci'=>array('e'=>'social', 'c'=>'社會'),
		'heal'=>array('e'=>'health', 'c'=>'健體'),
		'art'=>array('e'=>'art', 'c'=>'藝文'),
		'comp'=>array('e'=>'complex', 'c'=>'綜合'),
	);
	if ($_POST['export'] && $m_arr[$_POST['subj']]['e']<>"") {
			$filename=$sel_year."-".$sel_seme."-".$_POST['class_year']."年級-補測".$m_arr[$_POST['subj']]['c'].".csv";
			if(preg_match("/MSIE/i",$_SERVER['HTTP_USER_AGENT'])) {
				$filename=urlencode($filename);
			}
			header("Content-disposition: attachment; filename=$filename");
			header("Content-type: application/octetstream; Charset=Big5");
			header("Cache-Control: max-age=0");
			header("Pragma: public");
			header("Expires: 0");
			$smarty->assign("ename",$m_arr[$_POST['subj']]['e']);
			$smarty->assign("cname",$m_arr[$_POST['subj']]['c']);
			$smarty->display("list_csv.html");
			exit;
	}
	if ($_POST['insert']) {
		//先取出先前的名冊
		$query="select * from makeup_exam_scope where seme_year_seme='".$_POST['act_year_seme']."' and class_year='".($_POST['class_year']-($sel_year-$act_year))."' order by student_sn,scope_ename";
		$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
		$temp_arr = array();
		while($rr=$res->FetchRow()) {
			$temp_arr[$rr['student_sn']][$rr['scope_ename']]=array('oscore'=>$rr['oscore'], 'nscore'=>$rr['nscore'], 'has_score'=>$rr['has_score'], 'act'=>$rr['act'], 'update_time'=>$rr['update_time'], 'teacher_sn'=>$rr['teacher_sn']);
		}
		//刪除名冊以免沒有更新到
		$query="delete from makeup_exam_scope where seme_year_seme='".$_POST['act_year_seme']."' and class_year='".($_POST['class_year']-($sel_year-$act_year))."' and act<>'1'";
		$res=$CONN->Execute($query) or user_error("寫入失敗！<br>$query",256);
		//重新新增名冊並把先前的資料寫入
		$i=0;
		$now=date("Y-m-d H:i:s");
		foreach($base_arr as $sn=>$d) {
			foreach($m_arr as $dd) {
				$score=$all_arr[$sn][$dd['e']]['avg']['score'];
				if ($score<60 and $temp_arr[$sn][$dd['e']]['act']<>1) {
					if ($temp_arr[$sn][$dd['e']]['update_time']<>"") $update_time = $temp_arr[$sn][$dd['e']]['update_time'];
					else $update_time = $now;
					if ($temp_arr[$sn][$dd['e']]['teacher_sn']<>"") $update_time = $temp_arr[$sn][$dd['e']]['teacher_sn'];
					else $teacher_sn = $_SESSION['session_tea_sn'];
					$query="insert into makeup_exam_scope (seme_year_seme,student_sn,scope_ename,class_year,oscore,nscore,has_score,act,update_time,teacher_sn) values ('".$_POST['act_year_seme']."','$sn','".$dd['e']."','".($_POST['class_year']-($sel_year-$act_year))."','$score','".$temp_arr[$sn][$dd['e']]['nscore']."','".$temp_arr[$sn][$dd['e']]['has_score']."','".$temp_arr[$sn][$dd['e']]['act']."','$update_time','$teacher_sn')";
					$res=$CONN->Execute($query) or user_error("寫入失敗！<br>$query",256);
					$i++;
				}
			}
		}
		$smarty->assign("msg","已完成 ".$i." 筆資料寫入");
	}
	if ($_POST['list']) {
		$smarty->assign("sel_year",$act_year);
		$smarty->assign("sel_seme",$act_seme);
		$smarty->display("count.html");
		exit;
	}
	//print_r($all_arr);
}

//列印單學期通知單
if ($_POST['class_year']>0 && $_POST['noti1']) {
	$cstr = $all_sm_arr[$_POST['act_year_seme']];
	$smarty->assign("sel_year",$sel_year);
	$smarty->assign("sel_seme",$sel_seme);
	$smarty->assign("seme_arr",$all_sm_arr);
	$smarty->assign("seme_str",$cstr);
	$smarty->assign("school_data",get_school_name());
	$sb = get_school_base();
	$smarty->assign("school_sheng",$sb['sch_sheng']);
	$smarty->display("noti2.html");
	exit;
}

//秀出網頁布景標頭
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","單學期學習領域學績不及格學生篩選作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("year_seme_menu2",$year_seme_menu2);
$smarty->assign("class_year_menu",$class_year_menu);
$smarty->assign("seme_arr",$sm_arr);
$smarty->display("list.html");
?>
