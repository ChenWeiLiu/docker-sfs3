<?php

// $Id: function.php 7726 2013-10-28 08:15:30Z smallduh $

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
function getOneMdata($stud_id,$sel_year,$sel_seme,$date,$mode="",$start_date=""){
	global $CONN;
	$start_str=(empty($start_date))?" and year='$sel_year' and semester='$sel_seme'":"and date >= '$start_date'";
	$sql_select="select section, absent_kind from stud_absent where stud_id='$stud_id' $start_str and date <= '$date'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($section,$kind)=$recordSet->FetchRow()){
		if($mode=="種類"){
			$n=($section=="allday")?7:1;
			$m=($section=="allday")?2:1;
			if ($kind=="曠課" && ($section=="uf" || $section=="df")) $theData[f]+=$m;
			if ($section!="uf" && $section!="df") $theData[$kind]+=$n;
		}else{
			$theData[$section]+=1;	
		}
		
	}
	return $theData;
}

//取得某一學生某日的各種缺曠課次數
function getOneDdata($stud_id,$date){
	global $CONN;
	$sql_select="select section, absent_kind from stud_absent where stud_id='$stud_id' and date='$date'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($section,$kind)=$recordSet->FetchRow()){
		$theData[$section]+=1;		
	}
	return $theData;
}

//取得某一筆資料
function getOneDaydata($stud_id,$year,$month,$day){
	global $CONN;
	$sql_select="select section, absent_kind from stud_absent where stud_id='$stud_id' and date='$year-$month-$day'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($section,$kind)=$recordSet->FetchRow()){
		$theData[$section]=$kind;
	}
	return $theData;
}

//統計當學期總出缺席紀錄
//請先取得$seme_day[start]=開學日,$seme_day[end]=學期結束日
function sum_abs($sel_year,$sel_seme,$stud_id) {
	global $CONN,$abskind;

	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$sql="select * from school_day where year='$sel_year' and seme='$sel_seme'";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$seme_day[$rs->fields['day_kind']]=$rs->fields['day'];
		$rs->MoveNext();
	}
	$sql="select seme_class from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$stud_id'";
	$rs=$CONN->Execute($sql);
	$seme_class=$rs->fields['seme_class'];
	$class_year=substr($seme_class,0,-2);
	$sql="select sections from score_setup where year='$sel_year' and semester='$sel_seme' and class_year='$class_year'";
	$rs=$CONN->Execute($sql);
	$all_sections=$rs->fields['sections'];
	$sql="select * from stud_absent where year='$sel_year' and semester='$sel_seme' and date>='".$seme_day[st_start]."' and date<='".$seme_day[st_end]."' and stud_id='$stud_id'";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$abs_kind=$rs->fields['absent_kind'];
		$section=$rs->fields['section'];
		//echo $abs_kind."=".$section."<br>";
		if ($section=='uf' || $section=='df') {
			if ($abs_kind=='曠課') $all_abs[4]++;
		} else {
			$add_day=($section=='allday')?$all_sections:1;
			if ($abskind[$abs_kind]!="")
				$all_abs[$abskind[$abs_kind]]+=$add_day;
			else
				$all_abs[6]+=$add_day;
		}
		$rs->MoveNext();
	}
	$sql="select * from stud_seme_abs where seme_year_seme='$seme_year_seme' and stud_id='$stud_id'";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$h_abs[$rs->fields['abs_kind']]=$rs->fields['abs_days'];
		$rs->MoveNext();
	}
	for ($i=1;$i<=6;$i++) {
		if ($h_abs[$i] != $all_abs[$i]) {
			if ($h_abs[$i]!="")
				$sql="update stud_seme_abs set abs_days='$all_abs[$i]' where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and abs_kind='$i'";
			else 
				$sql="insert into stud_seme_abs (seme_year_seme,stud_id,abs_kind,abs_days) values ('$seme_year_seme','$stud_id','$i','$all_abs[$i]')";
			$CONN->Execute($sql);
			//echo $sql."<br>";
		}
	}
}
//檢查今天有沒有上課
function chk_school_day($day) {
	global $CONN,$seme_year_seme;
	$sql="select day from school_work_day where day='$day'";
	$res=$CONN->Execute($sql);
	if ($res->RecordCount()>0) {
	  return 1;
	} else {
		return 0;
	}
}
?>
