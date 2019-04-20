<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="ALTER TABLE `teacher_base` ADD `edu_key` VARCHAR( 65 ) NULL";
$rs=$CONN->Execute($SQL);

$SQL="ALTER TABLE `stud_base` ADD `edu_key` VARCHAR( 65 ) NULL";
$rs=$CONN->Execute($SQL);

// 更新所有教師 sha key
$sql = "SELECT teach_person_id ,teacher_sn FROM teacher_base WHERE teach_person_id<>'' ";
$res = $CONN->Execute($sql)or die($sql);

foreach ($res as $row) {	
	$teach_person_id = hash('sha256', strtoupper($row['teach_person_id']));
	
	$sql = "UPDATE teacher_base SET edu_key='$teach_person_id' WHERE teacher_sn='{$row['teacher_sn']}'";
	
	$CONN->Execute($sql) ;
}
// 更新所有學生 sha key
$sql = "SELECT stud_person_id ,student_sn FROM stud_base WHERE stud_person_id<>'' ";
$res = $CONN->Execute($sql)or die($sql);

foreach ($res as $row) {
	$stud_person_id = hash('sha256', strtoupper($row['stud_person_id']));

	$sql = "UPDATE stud_base SET edu_key='$stud_person_id' WHERE student_sn='{$row['student_sn']}'";

	$CONN->Execute($sql) ;
}