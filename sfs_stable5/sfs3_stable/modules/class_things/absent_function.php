<?php

// $Id: absent_function.php 5310 2009-01-10 07:57:56Z hami $

//取得該班課程節數
function get_class_cn($class_id=""){
	global $CONN;
	//取得某班學生陣列
	$c=class_id_2_old($class_id);
	
	//取得該班有幾節課
	$sql_select = "select sections from score_setup where year = '$c[0]' and semester='$c[1]' and class_year='$c[3]'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("SQL語法錯誤： $sql_select", E_USER_ERROR);
	list($all_sections) = $recordSet->FetchRow();
	return $all_sections;
}

//取得某一學生某月的各種缺曠課累積次數
function getOneMdata($stud_id,$sel_year,$sel_seme,$date,$mode=""){
	global $CONN;
	$sql_select="select section, absent_kind from stud_absent where stud_id='$stud_id' and year=$sel_year and semester='$sel_seme' and  date <= '$date'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($section,$kind)=$recordSet->FetchRow()){
		if($mode=="種類"){
			$n=($section=="allday")?7:1;
			$theData[$kind]+=$n;
		}else{
			$theData[$section]+=1;	
		}
		
	}
	return $theData;
}

//取得某一學生某日的各種缺曠課次數
function getOneDdata($stud_id,$date){
	global $CONN;
	$sql_select="select section, absent_kind from stud_absent where stud_id='$stud_id' and  date = '$date'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($section,$kind)=$recordSet->FetchRow()){
		$theData[$section]+=1;		
	}
	return $theData;
}
?>
