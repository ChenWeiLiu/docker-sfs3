<?php
//$Id: up20040927.php 5310 2009-01-10 07:57:56Z hami $

// 同步學生異動檔 (stud_move) 與學籍記錄檔 (stud_base) 資料

if(!$CONN){
        echo "go away !!";
        exit;
}
// 現有資料同步
$query = "select b.student_sn,a.stud_study_cond from stud_base a,stud_move b  where a.student_sn=b.student_sn and a.stud_study_cond<>b.move_kind and a.stud_study_cond not in (0,15)";

$res = $CONN->Execute($query);
while(!$res->EOF){
	$student_sn = $res->fields[student_sn];
	$move_kind= $res->fields[stud_study_cond];
	$query = "update stud_move set move_kind='$move_kind' where student_sn='$student_sn'";
	$CONN->Execute($query);
	$res->MoveNext();
}

?>
