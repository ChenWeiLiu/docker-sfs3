<?php

//$Id: up20040130.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//在課程表中加入sections節數,test_times考試次數,ratio_chg是否可由老師修改分數比例,times_chg是否可由老師修改考試次數,year,semester,score_mode,display_mode顯示方式(0:顯示時加上班級,1:顯示時不加班級)
$sql = "ALTER TABLE `course_table` MODIFY `test_ratio` VARCHAR(255)";
$CONN->Execute($sql);
$sql = "ALTER TABLE `course_table` ADD `year` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'";
$CONN->Execute($sql);
$sql = "ALTER TABLE `course_table` ADD `semester` ENUM('1','2') NOT NULL DEFAULT '1';";
$CONN->Execute($sql);
$sql = "ALTER TABLE `course_table` ADD `sections` TINYINT( 2 ) UNSIGNED NOT NULL ;";
$CONN->Execute($sql);
$sql = "ALTER TABLE `course_table` ADD `test_times` TINYINT( 2 ) UNSIGNED NOT NULL ;";
$CONN->Execute($sql);
$sql = "ALTER TABLE `course_table` ADD `score_mode` ENUM('all','severally')  NOT NULL DEFAULT 'all';";
$CONN->Execute($sql);
$sql = "ALTER TABLE `course_table` ADD `ratio_chg` ENUM('0','1') NOT NULL DEFAULT '0';";
$CONN->Execute($sql);
$sql = "ALTER TABLE `course_table` ADD `times_chg` ENUM('0','1') NOT NULL DEFAULT '0';";
$CONN->Execute($sql);
$sql = "ALTER TABLE `course_table` ADD `display_mode` ENUM('0','1') NOT NULL DEFAULT '0';";
$CONN->Execute($sql);
$sql = "select * from score_setup where enable='1'";
$rs = $CONN->Execute($sql);
while (!$rs->EOF) {
	$y=$rs->fields['year'];
	$s=$rs->fields['semester'];
	$c=$rs->fields['class_year'];
	$smode[$y][$s][$c]=$rs->fields['score_mode'];
	$ratio[$y][$s][$c]=$rs->fields['test_ratio'];
	$times[$y][$s][$c]=$rs->fields['performance_test_times'];
	$rs->MoveNext();
}
$sql = "select count(ss_id) as cc,year,semester,class_id,ss_id from score_course group by class_id,ss_id";
$rs = $CONN->Execute($sql);
while (!$rs->EOF) {
	$y=$rs->fields['year'];
	$s=$rs->fields['semester'];
	$c=$rs->fields['class_id'];
	$ss_id=$rs->fields['ss_id'];
	$sections[$y][$s][$c][$ss_id]=$rs->fields['cc'];
	$rs->MoveNext();
}
$sql="select ss_id,year,semester,class_year,print from score_ss where enable='1'";
$rs = $CONN->Execute($sql);
while (!$rs->EOF) {
	$y=$rs->fields['year'];
	$s=$rs->fields['semester'];
	$c=$rs->fields['class_year'];
	$ss_id=$rs->fields['ss_id'];
	$print[$y][$s][$c][$ss_id]=$rs->fields['print'];
	$rs->MoveNext();
}

$sql = "select course_id,class_id,ss_id from course_table";
$rs = $CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$course_id=$rs->fields['course_id'];
		$class_id=$rs->fields['class_id'];
		$ss_id=$rs->fields['ss_id'];
		$cc=explode("_",$class_id);
		$year=intval($cc[0]);
		$semester=$cc[1];
		$cyear=intval($cc[2]);
		if ($print[$year][$semester][$cyear][$ss_id]==1) {
			$sm=$smode[$year][$semester][$cyear];
			$tr=$ratio[$year][$semester][$cyear];
			$tt=$times[$year][$semester][$cyear];
		} else {
			$sm="";
			$tr="";
			$tt="";
		}
		$sc=$sections[$year][$semester][$class_id][$ss_id];
		$sql_chg="update course_table set year='$year',semester='$semester',score_mode='$sm',test_ratio='$tr',test_times='$tt',sections='$sc' where course_id='$course_id'";
		$CONN->Execute($sql_chg);
		$rs->MoveNext();
	}
}
?>
