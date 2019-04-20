<?php

// $Id: score_function.php 5310 2009-01-10 07:57:56Z hami $

//新增學科(score_subject)
function add_subject($name="",$kind="",$school=""){
	global $CONN;

	for($i=0;$i<sizeof($school);$i++){
		$school_v.=$school[$i].",";
	}
	$school_v=substr($school_v,0,-1);
	$sql_insert = "insert into score_subject (subject_name,subject_school,subject_kind,enable) values ('$name','$school_v','$kind','1')";
	if($CONN->Execute($sql_insert))	return mysql_insert_id();
	return  false;
}

//刪除（隱藏）學科
function del_subject($subject_id){
	global $CONN,$subject_kind;
	$sql_update = "update score_subject set enable='0' where subject_id = '$subject_id'";
	if($CONN->Execute($sql_update))	header("location: {$_SERVER['PHP_SELF']}?subject_kind=$subject_kind");
	return  false;
}



//新增一筆考試設定
function add_exam_set($sel_year="",$sel_seme="",$exam_times=2,$exam="",$exam_class_year=""){
	global $conID;
	$sql_insert = "insert into score_setup (year,semester,exam_times,exam,exam_class_year,update_date,enable) values ($sel_year,'$sel_seme',$exam_times,'$exam','$exam_class_year',now(),'1')";
	if(mysql_query ($sql_insert,$conID)) return mysql_insert_id();
	return false;
}


//更新一筆考試設定
function update_exam_set($setup_id,$exam_times=2,$exam="",$exam_class_year=""){
	global $conID;
	$sql_update = "update score_setup set exam_times=$exam_times,exam='$exam',exam_class_year='$exam_class_year',update_date=now() where setup_id = '$setup_id'";
	if(mysql_query ($sql_update,$conID)) return true;
	return false;
}


?>
