<?php

// $Id: person_seme_input.php 5460 2009-04-26 09:21:31Z brucelyc $

/*引入設定檔*/
include "config.php";

//使用者認證
sfs_check();

if ($_POST['year_seme']=="") {
	$sel_year=curr_year();
	$sel_seme=curr_seme();
} else {
	$sel_year=intval(substr($_POST['year_seme'],0,-1));
	$sel_seme=intval(substr($_POST['year_seme'],-1,1));
	if ($sel_year==0) $sel_year=curr_year();
	if ($sel_seme==0) $sel_seme=curr_seme();
}

if ($_POST['old_year_seme']) {
	if ($_POST['old_me']!=$_POST['me']) $_POST['student_sn']="";
	if ($_POST['old_year_name']!=$_POST['year_name']) {
		$_POST['student_sn']="";
		$_POST['me']="";
	}
	if ($_POST['old_year_seme']!=$_POST['year_seme'] && $_POST['old_year_seme']!="") {
		$_POST['student_sn']="";
		$_POST['me']="";
		$_POST['year_name']="";
	}
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
if ($_POST['change']) $_POST['student_sn']="";
$student_sn=intval($_POST['student_sn']);
$year_name=intval($_POST['year_name']);
$me=intval($_POST['me']);

if ($student_sn) {
	$query="select * from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query) or trigger_error("錯誤訊息： $query", E_USER_ERROR);
	$stud_id=$res->fields['stud_id'];
	if ($stud_id==$_POST['stud_id']) {
		$start_year=$res->fields['stud_study_year'];
		$smarty->assign("stud_name",$res->fields['stud_name']);
		if ($_POST['year_seme']) {
			$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and student_sn='$student_sn'";
			$res=$CONN->Execute($query) or trigger_error("錯誤訊息： $query", E_USER_ERROR);
			$year_name=$sel_year-$start_year+1+$IS_JHORES;
			$me=(strlen($res->fields['seme_class'])==3)?intval(substr($res->fields['seme_class'],-2,2)):0;
		}
	}
} elseif ($_POST['stud_id'] && $_POST['student_sn']==0) {
	$query="select * from stud_base where stud_id='".$_POST['stud_id']."' order by stud_study_year";
	$res=$CONN->Execute($query) or trigger_error("錯誤訊息： $query", E_USER_ERROR);
	$smarty->assign("stud_arr",$res->GetRows());
	$smarty->assign("cond_arr",study_cond());
}

if ($_POST['year_seme'] && $student_sn) {
	$class_id= sprintf ("%03d_%1d_%02d_%02d",$sel_year,$sel_seme,$year_name,$me);
	$w_arr=array("","表現優異","表現良好","表現尚可","需再加油","有待改進");
	$k_arr=each($w_arr);
	$query="select * from score_ss where class_id='$class_id' and enable='1'";
	$res=$CONN->Execute($query);
	if ($res->RecordCount() ==0){
		$query="select ss_id,scope_id,subject_id from score_ss where class_year='$year_name' and year='$sel_year' and semester='$sel_seme' and enable='1' and need_exam='1' and class_id='' order by sort,sub_sort";
		$res=$CONN->Execute($query);
	}
	if(is_object($res)) {
		$i=0;
		while (!$res->EOF) {
			$subject_id[$i]=$res->fields["subject_id"];
			$ss_id[$i]=$res->fields["ss_id"];
			if ($subject_id[$i]==0) $subject_id[$i]=$res->fields["scope_id"];
			$query2="select subject_name from score_subject where subject_id='$subject_id[$i]'";
			$res2=$CONN->Execute($query2);
			$subject[$i]=$res2->fields["subject_name"];
			$i++;
			$res->MoveNext();
		}
	}
	$max_ss=$i;
	if ($_POST['save']) {
		for ($i=0;$i<$max_ss;$i++) {
			//處理各科成績
			$score_ss=number_format($_POST['score'][$ss_id[$i]],2);
			if (($score_ss>100) || ($score_ss<0)) $score_ss="";
			//處理各科文字描述
			$ss_memo=str_replace("'","",trim($_POST['memo'][$ss_id[$i]]));
			$query="select student_sn from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='$ss_id[$i]'";
			$res=$CONN->Execute($query);
			if ($res->fields['student_sn']) {
				$query="update stud_seme_score set ss_score='$score_ss',ss_score_memo='$ss_memo',teacher_sn='$_SESSION[session_tea_sn]' where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='$ss_id[$i]'";
				$res=$CONN->Execute($query);
			} else {
				$query="insert into stud_seme_score (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo,teacher_sn) values ('$seme_year_seme','$student_sn','$ss_id[$i]','$score_ss','$ss_memo','$_SESSION[session_tea_sn]')";
				$res=$CONN->Execute($query);
			}
			//處理努力程度
			$val_ss=$_POST['ss_val'][$ss_id[$i]];
			if (!in_array($val_ss,$w_arr)) $val_ss="";
			$query="select stud_id from stud_seme_score_oth where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_id='$ss_id[$i]' and ss_kind='努力程度'";
			$res=$CONN->Execute($query);
			if ($res->fields['stud_id']) {
				$query="update stud_seme_score_oth set ss_val='$val_ss' where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_id='$ss_id[$i]' and ss_kind='努力程度'";
				$res=$CONN->Execute($query);
			} else {
				$query="insert into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values ('$seme_year_seme','$stud_id','努力程度','$ss_id[$i]','$val_ss')";
				$res=$CONN->Execute($query);
			}
		}
		//處理日常成績
		$score_nor=number_format($_POST['score_nor'],2);
		//處理日常文字描述
		$memo_nor=str_replace("'","",trim($_POST['memo_nor']));
		$query="select student_sn from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='0'";
		$res=$CONN->Execute($query);
		if ($res->fields['student_sn']) {
			$query="update stud_seme_score_nor set ss_score='$score_nor',ss_score_memo='$memo_nor' where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='0'";
			$res=$CONN->Execute($query);
		} else {
			$query="insert into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo) values ('$seme_year_seme','$student_sn','0','$score_nor','$memo_nor')";
			$res=$CONN->Execute($query);
		}
		//處理日常努力程度
		for ($i=1;$i<=4;$i++) {
			$query="select stud_id from stud_seme_score_oth where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_id='$i' and ss_kind='生活表現評量'";
			$res=$CONN->Execute($query);
			$val_nor=$_POST['nor_val'][$i];
			if (!in_array($val_nor,$w_arr)) $val_nor="";
			if ($res->fields['stud_id']) {
				$query="update stud_seme_score_oth set ss_val='$val_nor' where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_id='$i' and ss_kind='生活表現評量'";
				$res=$CONN->Execute($query);
			} else {
				$query="insert into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values ('$seme_year_seme','$stud_id','生活表現評量','$i','$val_nor')";
				$res=$CONN->Execute($query);
			}
		}
	}
	$sm=get_all_setup("",$sel_year,$sel_seme,$year_name);
	$rule_arr=explode("\n",$sm[rule]);
	for ($i=0;$i<$max_ss;$i++) {
		$query="select ss_val from stud_seme_score_oth where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_id='$ss_id[$i]' and ss_kind='努力程度'";
		$res=$CONN->Execute($query) or trigger_error("錯誤訊息： $query", E_USER_ERROR);
		$ssval=$res->fields['ss_val'];//努力程度
		for ($j=0;$j<count($w_arr);$j++){
			$selected=($ssval==$w_arr[$j])?"selected":""; 
			$ss_val[$ss_id[$i]].="<option $selected>".$w_arr[$j]."</option>";
		}
		$query="select count(*) as cc from score_course where class_id='$class_id' and ss_id='$ss_id[$i]'";
		$res=$CONN->Execute($query) or trigger_error("錯誤訊息： $query", E_USER_ERROR);
		$ss_sec[$ss_id[$i]]=$res->fields[cc];//上課節數
		$query="select ss_score,ss_score_memo from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='$ss_id[$i]'";
		$res=$CONN->Execute($query) or trigger_error("錯誤訊息： $query", E_USER_ERROR);
		$memo[$ss_id[$i]]=$res->fields['ss_score_memo'];//文字描述
		$score[$ss_id[$i]]=$res->fields['ss_score'];//各科成績
		if (($sc=="-100")||($sc=="")) $sc="";
		$cstr[$ss_id[$i]]=score2str($res->fields['ss_score'],"",$rule_arr);//等第
	} 
	$query="select ss_score,ss_score_memo from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='0'";
	$res=$CONN->Execute($query) or trigger_error("錯誤訊息： $query", E_USER_ERROR);
	$score_nor=$res->fields['ss_score'];//日常成績
	$score_nor_str=score2str($res->fields['ss_score'],"",$rule_arr);//等第
	$memo_nor=$res->fields['ss_score_memo'];//日常文字描述
	$query="select ss_id,ss_val from stud_seme_score_oth where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_kind='生活表現評量' order by ss_id";
	$res=$CONN->Execute($query) or trigger_error("錯誤訊息： $query", E_USER_ERROR);
	while (!$res->EOF) {
		$i=$res->fields['ss_id'];//日常項次
		$ssval=$res->fields['ss_val'];//日常努力程度
		for ($j=0;$j<count($w_arr);$j++){
			$selected=($ssval==$w_arr[$j])?"selected":""; 
			$nor_val[$i].="<option $selected>".$w_arr[$j]."</option>";
		}
		$res->MoveNext();
	}
	$rowdata['ss_id']=$ss_id;
	$rowdata['subject']=$subject;
	$rowdata['ss_sec']=$ss_sec;
	$rowdata['ss_val']=$ss_val;
	$rowdata['score']=$score;
	$rowdata['cstr']=$cstr;
	$rowdata['memo']=$memo;
	$rowdata['nor']['ss_item']=array(1=>"日常行為表現",2=>"團體活動表現",3=>"公共服務",4=>"校外特殊表現");
	$rowdata['nor']['ss_val']=$nor_val;
	$rowdata['nor']['score']=$score_nor;
	$rowdata['nor']['cstr']=$score_nor_str;
	$rowdata['nor']['memo']=$memo_nor;
	$smarty->assign("rowdata",$rowdata);
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","成績補登/修改"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("sel_year",$sel_year);
$smarty->assign("sel_seme",$sel_seme);
if ($_POST['stud_id']) {
	$smarty->assign("seme_menu",seme_menu($start_year,$_POST['year_seme']));
	//$smarty->display('score_input_all_new_person_seme_input2.tpl');
} else {
	$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme));
	$smarty->assign("year_name_menu",class_year_menu($sel_year,$sel_seme,$_POST['year_name']));
	if ($_POST['year_seme'] && $_POST['year_name']) $smarty->assign("class_name_menu",class_name_menu($sel_year,$sel_seme,$_POST['year_name'],$_POST['me']));
	if ($_POST['year_seme'] && $_POST['year_name'] && $_POST['me']) $smarty->assign("stud_menu",stud_menu($sel_year,$sel_seme,$_POST['year_name'],$_POST['me'],$_POST['student_sn']));
}
$smarty->display('score_input_all_new_person_seme_input.tpl');
?>
