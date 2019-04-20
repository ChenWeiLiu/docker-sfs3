<?php

//$Id: up20031206.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//建立time_table表
$create_table_sql="
	CREATE TABLE if not exists time_table (
	seme_year_seme varchar(6) NOT NULL default '',
	class_id varchar(11) NOT NULL default '',
	day enum('0','1','2','3','4','5','6') NOT NULL default '0',
	sector tinyint(1) unsigned NOT NULL default '0',
	course_id int(10) unsigned NOT NULL default '0',
	teacher_sn smallint(5) unsigned NOT NULL default '0',
	room_id smallint(5) unsigned NOT NULL default '0',
	PRIMARY KEY (seme_year_seme,class_id,day,sector)
	)";
$rs=$CONN->Execute($create_table_sql);
//重建course_table表
$drop_table_sql="DROP TABLE course_table";
$rs=$CONN->Execute($drop_table_sql);
$create_table_sql="
	CREATE TABLE if not exists course_table (
	course_id int(10) unsigned NOT NULL auto_increment,
	class_id varchar(11) NOT NULL default '',
	ss_id smallint(5) unsigned NOT NULL default '0',
	course_name text,
	test_ratio varchar(10) default NULL,
	PRIMARY KEY (class_id,ss_id),
	KEY (course_id)
	)";
$rs=$CONN->Execute($create_table_sql);
$sql="select subject_id,subject_name from score_subject where enable='1'";
$rs=$CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$subject_id=$rs->fields['subject_id'];
		$sname_arr[$subject_id]=$rs->fields['subject_name'];
		$rs->MoveNext();
	}
}
$sql="select ss_id,scope_id,subject_id from score_ss where enable=1 order by year,semester,sort,sub_sort";
$rs=$CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$subject_id=$rs->fields['subject_id'];
		if ($subject_id==0) $subject_id=$rs->fields['scope_id'];
		$ss_id=$rs->fields['ss_id'];
		$subject_arr[$ss_id]=$sname_arr[$subject_id];
		$rs->MoveNext();
	}
}
$sql="select year,semester,class_year,test_ratio from score_setup where enable='1'";
$rs=$CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$year=$rs->fields['year'];
		$semester=$rs->fields['semester'];
		$class_year=$rs->fields['class_year'];
		$test_ratio[$year][$semester][$class_year]=$rs->fields['test_ratio'];
		$rs->MoveNext();
	}
}
$sql="select distinct concat(class_id,'_',ss_id),class_id,ss_id,year,semester,class_year from score_course order by class_id,ss_id";
$rs=$CONN->Execute($sql);
if ($rs){
	while (!$rs->EOF) {
		$class_id=$rs->fields["class_id"];
		$ss_id=$rs->fields["ss_id"];
		$year=$rs->fields['year'];
		$semester=$rs->fields['semester'];
		$class_year=$rs->fields['class_year'];
		$t=$test_ratio[$year][$semester][$class_year];
		$sql_i="insert into course_table (class_id,ss_id,course_name,test_ratio) values ('$class_id','$ss_id','$subject_arr[$ss_id]','$t')";
		$rs_i=$CONN->Execute($sql_i);
		$rs->MoveNext();
	}
}
$sql="select course_id,class_id,ss_id from course_table";
$rs=$CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$course[$rs->fields['class_id']][$rs->fields['ss_id']]=$rs->fields['course_id'];
		$rs->MoveNext();
	}
}
$sql="select year,semester,class_id,day,sector,ss_id,teacher_sn from score_course order by year,semester,class_id,day,sector,ss_id";
$rs=$CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$year=$rs->fields['year'];
		$semester=$rs->fields['semester'];
		$class_id=$rs->fields['class_id'];
		$day=$rs->fields['day'];
		$sector=$rs->fields['sector'];
		$ss_id=$rs->fields['ss_id'];
		$teacher_sn=$rs->fields['teacher_sn'];
		$seme_year_seme=sprintf("%03d",$year).$semester;
		$course_id=$course[$class_id][$ss_id];
		$sql_i="insert time_table (seme_year_seme,class_id,day,sector,course_id,teacher_sn,room_id) values ('$seme_year_seme','$class_id','$day','$sector','$course_id','$teacher_sn','')";
		$rs_i=$CONN->Execute($sql_i);
		$rs->MoveNext();
	}
}
?>
