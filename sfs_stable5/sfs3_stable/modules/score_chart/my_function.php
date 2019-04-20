<?php

// $Id: my_function.php 7767 2013-11-15 06:18:35Z smallduh $

//取得科目節數
function get_ss_num($class_id="",$stud_id="",$student_sn="",$ss_id=""){
	global $CONN;

	$sql_select = "select count(*) from score_course where ss_id=$ss_id and class_id='$class_id'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	list($n)= $recordSet->FetchRow();

	return $n;
}



//算出某位學生某科的全學期成績，並以甲乙丙丁為結果
function get_ss_score($class_id="",$stud_id="",$student_sn="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];
	//只求出總成績
	$score=seme_score2($student_sn,$ss_id,$year,$seme);
	// 未輸入
	if ($score == -1)
		$score_name = "無資料";
	else
		$score_name=score2str($score,$class);
	return $score_name;
}

//取得各領域文字描述 
function get_ss_score_memo($class_id="",$stud_id="",$student_sn="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];

	$seme_year_seme = sprintf("%03d%d",$year,$seme);
	$query = "select ss_score_memo from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn = $student_sn and ss_id =$ss_id ";
	$res = $CONN->Execute($query) or trigger_error($query);
	return $res->fields[0];
}

//規則口語化
function &say_rule_2($class){
	$year=$class[0];
	$seme=$class[1];
         //取考試設定資料
        $sm=&get_all_setup("",$year,$seme,$class[3]);
                                                                                                                            
        //分解規則
        $r=explode("\n",$sm[rule]);
        reset($r);
        while(list($k,$v)=each($r)){
                $str=explode("_",$v);
                $main.="<text:p text:style-name=\"P11\">學期分數 ".htmlspecialchars($str[1])." $str[2] 時，等第為『$str[0]』</text:p>";
        }
//	echo $main; exit;
        return $main;
}

//算出某位學生事假總節數
function get_abs_v($class_id="",$stud_id="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];
	if ($seme==1) {
		$abs_sdate=(string)(1911+(integer)$year)."-07-31";
		$abs_edate=(string)(1912+(integer)$year)."-02-01";
	} else {
		$abs_sdate=(string)(1912+(integer)$year)."-01-31";
		$abs_edate=(string)(1912+(integer)$year)."-08-01";
	}

	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and class_id='$class_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='事假' and (section != 'uf' and section != 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$abs_times=$recordSet->fields[0];

	return $abs_times;
}

//算出某位學生病假總節數
function get_abs_s($class_id="",$stud_id="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];
	if ($seme==1) {
		$abs_sdate=(string)(1911+(integer)$year)."-07-31";
		$abs_edate=(string)(1912+(integer)$year)."-02-01";
	} else {
		$abs_sdate=(string)(1912+(integer)$year)."-01-31";
		$abs_edate=(string)(1912+(integer)$year)."-08-01";
	}

	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and class_id='$class_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='病假' and (section != 'uf' and section != 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$abs_times=$recordSet->fields[0];

	return $abs_times;
}

//算出某位學生曠課總節數
function get_abs_c($class_id="",$stud_id="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];
	if ($seme==1) {
		$abs_sdate=(string)(1911+(integer)$year)."-07-31";
		$abs_edate=(string)(1912+(integer)$year)."-02-01";
	} else {
		$abs_sdate=(string)(1912+(integer)$year)."-01-31";
		$abs_edate=(string)(1912+(integer)$year)."-08-01";
	}

	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and class_id='$class_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='曠課' and (section != 'uf' and section != 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$abs_times=$recordSet->fields[0];

	return $abs_times;
}

//算出某位學生集會總節數
function get_abs_f($class_id="",$stud_id="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];
	if ($seme==1) {
		$abs_sdate=(string)(1911+(integer)$year)."-07-31";
		$abs_edate=(string)(1912+(integer)$year)."-02-01";
	} else {
		$abs_sdate=(string)(1912+(integer)$year)."-01-31";
		$abs_edate=(string)(1912+(integer)$year)."-08-01";
	}

	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and class_id='$class_id' and (date>'$abs_sdate' and date<'$abs_edate') and (section = 'uf' or section = 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$abs_times=$recordSet->fields[0];

	return $abs_times;
}

//算出某位學生公假總節數
function get_abs_b($class_id="",$stud_id="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];
	if ($seme==1) {
		$abs_sdate=(string)(1911+(integer)$year)."-07-31";
		$abs_edate=(string)(1912+(integer)$year)."-02-01";
	} else {
		$abs_sdate=(string)(1912+(integer)$year)."-01-31";
		$abs_edate=(string)(1912+(integer)$year)."-08-01";
	}

	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and class_id='$class_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='公假' and (section != 'uf' and section != 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$abs_times=$recordSet->fields[0];

	return $abs_times;
}

//算出某位學生公假總節數
function get_abs_o($class_id="",$stud_id="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];
	if ($seme==1) {
		$abs_sdate=(string)(1911+(integer)$year)."-07-31";
		$abs_edate=(string)(1912+(integer)$year)."-02-01";
	} else {
		$abs_sdate=(string)(1912+(integer)$year)."-01-31";
		$abs_edate=(string)(1912+(integer)$year)."-08-01";
	}

	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and class_id='$class_id' and (date>'$abs_sdate' and date<'$abs_edate')";
	$recordSet = $CONN->Execute($sql_select);
	$abs_times=$recordSet->fields[0];
	$abs_v=get_abs_v($class_id,$stud_id,$ss_id);
	$abs_s=get_abs_s($class_id,$stud_id,$ss_id);
	$abs_c=get_abs_c($class_id,$stud_id,$ss_id);
	$abs_f=get_abs_f($class_id,$stud_id,$ss_id);
	$abs_b=get_abs_b($class_id,$stud_id,$ss_id);
	$abs_times=$abs_times-$abs_v-$abs_s-$abs_c-$abs_f-$abs_b;

	return $abs_times;
}

?>
