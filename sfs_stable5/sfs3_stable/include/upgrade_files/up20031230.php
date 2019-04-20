<?php

//$Id: up20031230.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//«Ø¥ßname_listªí
$name_list="name_list_".curr_year()."_".curr_seme();
$create_table_sql="
	CREATE TABLE if not exists $name_list (
	seme_year_seme varchar(6) NOT NULL default '',
	student_sn int(10) unsigned NOT NULL default '0',
	course_id int(10) unsigned NOT NULL default '0',
	site_num smallint(3) unsigned NOT NULL default '0',
	PRIMARY KEY (seme_year_seme,student_sn,course_id)
	)";
$rs=$CONN->Execute($create_table_sql);
$sql="select course_id,class_id,ss_id from course_table order by class_id,course_id";
$rs=$CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$course_id[$rs->fields['class_id']][$rs->fields['ss_id']]=$rs->fields['course_id'];
		$rs->MoveNext();
	}
}
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
$sql="select seme_year_seme,seme_class,seme_num,student_sn from stud_seme where student_sn > '0' and seme_year_seme='$seme_year_seme' order by seme_year_seme,seme_class,seme_num";
$rs=$CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$seme_year_seme=$rs->fields['seme_year_seme'];
		$seme_class=$rs->fields['seme_class'];
		$seme_num=$rs->fields['seme_num'];
		$student_sn=$rs->fields['student_sn'];
		$year=substr($seme_year_seme,0,3);
		$seme=substr($seme_year_seme,3,1);
		$class_year=substr($seme_class,0,-2);
		$class_num=substr($seme_class,-2,2);
		$class_id=sprintf("%03d_%d_%02d_%02d",$year,$seme,$class_year,$class_num);
		if (count($course_id[$class_id])>0) reset($course_id[$class_id]);
		while (list($k,$v)=each($course_id[$class_id])) {
			$sql_i="insert into $name_list (seme_year_seme,student_sn,course_id,site_num) values ('$seme_year_seme','$student_sn','$v','$seme_num')"; 
			$rs_i=$CONN->Execute($sql_i);
		}
		$rs->MoveNext();
	}
}
?>
