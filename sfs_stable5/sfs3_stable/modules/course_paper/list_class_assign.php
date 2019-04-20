<?php

// $Id: list_class_assign.php 7705 2013-10-23 08:58:49Z smallduh $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$year_seme=$_GET['year_seme'];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

$filename="course_班級配課表_".$sel_year.$sel_seme.".csv";
header("Content-disposition: attachment;filename=$filename");
header("Content-type: text/x-csv ; Charset=Big5");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

header("Expires: 0");

$class_base=class_base(sprintf("%03d",$sel_year).$sel_seme);
$query="select * from teacher_base";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$teacher_name_arr[$res->fields[teacher_sn]]=$res->fields[name];
	$res->MoveNext();
}
$query="select * from score_subject";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$subject_name_arr[$res->fields[subject_id]]=$res->fields[subject_name];
	$res->MoveNext();
}
$query="select * from score_ss where year='$sel_year' and semester='$sel_seme' and enable='1' order by class_id,sort,sub_sort";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$subject_id=$res->fields[subject_id];
	if ($subject_id=="0") $subject_id=$res->fields[scope_id];
	$subject_arr[$subject_id]=$subject_name_arr[$subject_id];
	$res->MoveNext();
}
$query="select a.class_id,count(a.sector),a.teacher_sn,b.scope_id,b.subject_id from score_course a,score_ss b where a.ss_id=b.ss_id and a.year='$sel_year' and a.semester='$sel_seme' group by a.class_id,a.ss_id,a.teacher_sn order by a.class_id,b.sort,b.sub_sort";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$subject_id=$res->fields[subject_id];
	if ($subject_id=="0") $subject_id=$res->fields[scope_id];
	$class_id=$res->fields[class_id];
	for ($i=0;$i<=4;$i++) {
		$j=$i;
		if ($ss[$class_id][$subject_id][teacher_sn][$j]=="") break;
	}
	$ss[$class_id][$subject_id][teacher_sn][$j]=$res->fields[teacher_sn];
	$ss[$class_id][$subject_id][sector][$j]=$res->fields[1];
	$res->MoveNext();
}
reset($ss);
while(list($class_id,$v)=each($ss)){
	reset($v);
	while(list($subject_id,$vv)=each($v)) {
		while(list($j,$vvv)=each($ss[$class_id][$subject_id][teacher_sn])) {
			if ($subject_num[$subject_id]<$j) $subject_num[$subject_id]=$j;
		}
	}
}

echo "班級,";
reset($subject_arr);
while(list($subject_id,$subject_name)=each($subject_arr)) {
	for ($i=0;$i<=$subject_num[$subject_id];$i++) {
		echo $subject_name.",節數,";
	}
}
echo "\n";
reset($ss);
while(list($class_id,$v)=each($ss)){
	$c=explode("_",$class_id);
	echo $class_base[intval($c[2]).$c[3]].",";
	reset($subject_arr);
	while(list($subject_id,$subject_name)=each($subject_arr)) {
		for ($i=0;$i<=$subject_num[$subject_id];$i++) {
			echo $teacher_name_arr[$ss[$class_id][$subject_id][teacher_sn][$i]].",".$ss[$class_id][$subject_id][sector][$i].",";
		}
	}
	echo "\n";
}
?>
