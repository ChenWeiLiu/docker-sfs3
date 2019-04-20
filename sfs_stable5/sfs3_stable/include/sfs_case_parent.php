<?php
// $Id: sfs_case_parent.php 5310 2009-01-10 07:57:56Z hami $


//由家長的流水號找出他的所有小孩
function &get_child($parent_sn=""){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	$parent_sn=$_SESSION['session_tea_sn'];
	if (!$parent_sn) user_error("沒有傳入家長代碼！請檢查！",256);
	
	//找出家長的身份正號
	$sql="select parent_id from parent_auth where parent_sn='$parent_sn'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
	//如沒有建資料時回傳空陣列
	if ($rs->RecordCount()==0)
		return array();
	$parent_id=$rs->fields['parent_id'];
	
	//找出監護的學生名單
	$sql_st="select stud_id from stud_domicile where  guardian_p_id='$parent_id'";
	//echo $sql_st;
	$rs_st=$CONN->Execute($sql_st) or trigger_error("SQL語法錯誤： $sql_st", E_USER_ERROR);	
	
	//初始化陣列
	$child_A=array();
	
	$i=0;
	while(!$rs_st->EOF){
		$stud_id[$i]=$rs_st->fields['stud_id'];
			//只顯示在學者
			$sql_bs="select * from stud_base where stud_study_cond=0 and stud_id='$stud_id[$i]'";
			//echo $sql_bs;
			$rs_bs=$CONN->Execute($sql_bs);		
			$child_A[$i][id]=$rs_bs->fields['stud_id'];
			$child_A[$i][Cclass]=substr($rs_bs->fields['curr_class_num'],0,-2);
			$child_A[$i][num]=substr($rs_bs->fields['curr_class_num'],-2);
			$child_A[$i][name]=$rs_bs->fields['stud_name'];
			//echo $child_A[$i][id];
			if(count($child_A[$i][id])==0) {$rs_st->MoveNext(); continue;}
		$i++;
		$rs_st->MoveNext();		
	}
		
	return $child_A;
}


//由小孩的流水號找家長流水號和姓名
function &get_parent($student_sn_A){
	global $CONN;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);	
	
	if (!$student_sn_A) user_error("沒有傳入學生代碼！請檢查！",256);
	
	//初始化陣列
	$parent_A=array();
	
	for($i=0;$i<count($student_sn_A);$i++){
		//echo $i;
		//小孩sn變id
		$sql="select stud_id,stud_name from stud_base where student_sn='$student_sn_A[$i]'";
		$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
		$stud_id[$i]=$rs->fields['stud_id'];
		$stud_name[$i]=$rs->fields['stud_name'];
		//echo $stud_id[$i];
		//找到監護人的id		
		$sql="select guardian_p_id,guardian_name from stud_domicile where stud_id='$stud_id[$i]'";
		$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
		$parent_id[$i]=$rs->fields['guardian_p_id'];
		$parent_name[$i]=$rs->fields['guardian_name'];
		//echo $parent_name[$i];
		//echo $sql;
		if($parent_name[$i]!=""){
			//找出家長流水號
			$sql="select parent_sn from parent_auth where parent_id='$parent_id[$i]'";
			$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
			$parent_sn[$i]=$rs->fields['parent_sn'];		
			if($parent_sn[$i]!=""){
				$parent_A[$i][sn]=$parent_sn[$i];//家長流水號
				$parent_A[$i][name]=$parent_name[$i];//家長姓名
				$parent_A[$i][child]=$stud_name[$i];//貴子弟
			}
			else continue;
		}
		else continue;
	}
	
	return $parent_A;
}

//由家長的流水號找出他的姓名
function &get_guardian_name($parent_sn){
	global $CONN;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);	
	
	if (!$parent_sn) user_error("沒有傳入家長代碼！請檢查！",256);
	
	//找出家長的身份正號
	$sql="select parent_id from parent_auth where parent_sn='$parent_sn'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
	$parent_id=$rs->fields['parent_id'];
	//找出家長名單
	$sql_name="select guardian_name from stud_domicile where  guardian_p_id='$parent_id'";
	$rs_name=$CONN->Execute($sql_name) or trigger_error("SQL語法錯誤： $sql_name", E_USER_ERROR);
	$guardian_name=$rs_name->fields['guardian_name'];	
	return $guardian_name;
}

?>
