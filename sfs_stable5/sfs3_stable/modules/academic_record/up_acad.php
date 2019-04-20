<?php

//$Id: up_acad.php 7727 2013-10-28 08:26:17Z smallduh $

if(!$CONN){
	echo "go away";
	exit;
}
$query = "select * from score_input_value ";
$res = $CONN->Execute($query);
while(!$res->EOF) {
	$stud_id = $res->fields[stud_id];
	//求得學生ID	
	$student_sn=stud_id2student_sn($stud_id);
	$seme_year_seme = sprintf("%03d%d",$res->fields[sel_year],$res->fields[sel_seme]);	
	
	$temp_arr = explode("^^", $res->fields[value]);
	reset($temp_arr);
	while(list($id,$val) = each($temp_arr)) {
		$temp_arr_2 = explode("：",$val);
		$res_temp[$temp_arr_2[0]] = $temp_arr_2[1];
	}
	//生活評量評語
	$query = "replace into stud_seme_score_nor(seme_year_seme,student_sn,ss_id,ss_score_memo)values('$seme_year_seme','$student_sn',0,'$res_temp[6]')";
	
	$CONN->Execute($query) ;
	//其他設定
	for($i=1;$i<=4;$i++){
		$ii = $i+1;
		$query = "replace into stud_seme_score_oth(seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values('$seme_year_seme','$stud_id','生活表現評量',$i,'$res_temp[$ii]')";
	
		$CONN->Execute($query) ;
	}
	//學生缺席狀況 獎懲
	for($i=1;$i<=6;$i++) {
		$ii = $i+8;
		$iii = $i+15;
		$query = "replace into stud_seme_abs(seme_year_seme,stud_id,abs_kind,abs_days) values('$seme_year_seme','$stud_id',$i,'$res_temp[$ii]')";
		$CONN->Execute($query) ;
		$query = "replace into stud_seme_rew(seme_year_seme,stud_id,sr_kind_id,sr_num) values('$seme_year_seme','$stud_id',$i,'$res_temp[$iii]')";
		$CONN->Execute($query) ;
	}
	
	$query = "replace into stud_seme_score_oth(seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values('$seme_year_seme','$stud_id','其他設定',0,'$res_temp[22]')";
		$CONN->Execute($query) ;
	$res->MoveNext();
}

