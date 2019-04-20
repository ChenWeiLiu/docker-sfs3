<?php

//$Id: my_fun2.php 5310 2009-01-10 07:57:56Z hami $

//本校目前該年級該班級目前已有階段成績的選單
function select_stage2($year_seme,$year_name){
	global $CONN,$score_semester,$yorn;
	$sel_year = substr($year_seme,0,3);
	$sel_seme = substr($year_seme,-1);
	$c_year = substr($year_name,0,-2);
	$c_name = substr($year_name,-2);
	$score_semester="score_semester_".intval($sel_year)."_".$sel_seme;
	$class_id = sprintf("%03s_%s_%02s_%02s",$sel_year,$sel_seme,$c_year,$c_name);
	if ($yorn=='n')
		$sql="select DISTINCT test_sort from $score_semester where class_id='$class_id'  order by test_sort";
	else
		$sql="select DISTINCT test_sort from $score_semester where class_id='$class_id' and test_sort<>254  order by test_sort";

	$rs=&$CONN->Execute($sql) or die($sql);
        while (!$rs->EOF) {
		if($rs->fields[0]==255)
			$temp_name="全學期";
		elseif($rs->fields[0]==254)
			$temp_name="平時成績";
		else
			$temp_name="第".$rs->fields[0]."階段";
		$test_sort[$rs->fields[0]]= $temp_name;
		$rs->MoveNext();
        }
	return $test_sort;
}

//取得所有科目陣列
function get_all_subject_arr(){
	global $CONN;
	$query = "select subject_id,subject_name from score_subject";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$res_arr[$res->fields[0]] = $res->fields[1];
		$res->MoveNext();
	}
	return $res_arr;
}
//算出這個值是陣列中第幾大的，a是一個數，b是一個陣列
function  how_big2($a,$b){
	$sort=1;
	while(list($id,$val)=each($b)){
        	if($a<$val) $sort++;
    }
    return  $sort;
}

//是否每一次月考要配合一次平時成績
function  findyorn(){
	global $CONN;
	$rs_yorn=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='score_input' AND pm_item='yorn'");
	$yorn=$rs_yorn->fields['pm_value'];
	return $yorn;
}

?>
