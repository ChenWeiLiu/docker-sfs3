<?php
// $Id: sfs_case_absent.php 5310 2009-01-10 07:57:56Z hami $

function add_absent($mode,$sn,$sel_year,$sel_seme,$month,$abs_kind,$reason,$start_date,$end_date,$agent_sn)
{
	global $CONN;
	if ($mode=="teacher"){
		$query="insert into teacher_absent (year,semester,month,teacher_sn,reason,abs_kind,start_date,end_date,class_dis,deputy_sn,record_id,record_date) values ('$sel_year','$sel_seme','$month','$sn','$reason','$abs_kind','$start_date','$end_date','$class_dis','$agent_sn','$_SESSION[session_log_id]','".date("Y-m-d H:i:s")."')";
		$CONN->Execute($query);
	} elseif ($mode=="student"){
	} else {
		return "請假人員類別錯誤";
	}
}

function modify_absent($mode,$id,$sn,$sel_year,$sel_seme,$month,$abs_kind,$reason,$start_date,$end_date,$agent_sn,$class_dis,$status)
{
	global $CONN;
	if ($mode=="teacher"){
		$query="update teacher_absent set year='$sel_year',semester='$sel_seme',month='$month',teacher_sn='$sn',reason='$reason',abs_kind='$abs_kind',start_date='$start_date',end_date='$end_date',class_dis='$class_dis',deputy_sn='$agent_sn',status='$status',record_id='$_SESSION[session_log_id]',record_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
		echo $query;
	} elseif ($mode=="student"){
	} else {
		return "請假人員類別錯誤";
	}
}

function del_absent($mode,$id)
{
	global $CONN;
	if ($mode=="teacher"){
		$query="delete from teacher_absent where id='$id'";
		$CONN->Execute($query);
	} elseif ($mode=="student"){
	} else {
		return "請假人員類別錯誤";
	}
}

function check_course($teacher_sn,$sel_year,$sel_seme,$start_date,$end_date,$c_year)
{
	global $CONN;
	if ($end_date < $start_date) return "結束時間小於開始時間";
	$s=getdate(strtotime($start_date));
	$e=getdate(strtotime($end_date));
	$query="select day,sector,c_kind from score_course where year='$sel_year' and semester='$sel_seme' and teacher_sn='$teacher_sn' order by day,sector";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$sector[$res->fields[day]][$res->fields[sector]]=$res->fields[c_kind];
		$res->MoveNext();
	}
	
}

function check_absent($stud_id=array(),$seme=array())
{
	global $CONN;
	if (count($stud_id)>0 && count($seme)>0) {
		$all_id="'".implode("','",$stud_id)."'";
		$all_seme="'".implode("','",$seme)."'";
		$seme_num=count($seme);
		$query="select * from stud_seme_abs where stud_id in ($all_id) and seme_year_seme in ($all_seme) and abs_kind in ('1','2','3','4')";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			if ($res->fields[abs_days]>"0") $fin_score[$res->fields[stud_id]]++;
			$res->MoveNext();
		}
		reset($stud_id);
		while(list($id,$v)=each($stud_id)) {
			if (intval($fin_score[$v])==0) $show_score[$v]=$fin_score[$v];
		}
		return $show_score;
	} elseif (count($stud_id)==0) {
		return "沒有傳入學生學號";
	} else {
		return "沒有傳入學期";
	}
}
?>