<?php

//$Id: up20040513.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//在學生異動表中加入學生序號
$query="select * from stud_move where 1=0";
$res=$CONN->Execute($query);
if ($res) {
	$query="alter table stud_move add student_sn int(10) unsigned NOT NULL default '0'";
	$CONN->Execute($query);
	$query="select * from stud_move";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$move_id=$res->fields[move_id];
		$move_year_seme=$res->fields[move_year_seme];
		$stud_id=$res->fields[stud_id];
		$seme_year_seme=sprintf("%04d",$move_year_seme);
		$query="select student_sn from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$stud_id'";
		$res1=$CONN->Execute($query);
		$student_sn=$res1->fields[student_sn];
		if (empty($student_sn)) {
			$query="select student_sn from stud_base where stud_id='$stud_id'";
			$res1=$CONN->Execute($query);
			$student_sn=$res1->fields[student_sn];
		}
		if ($student_sn) {
			$query="update stud_move set student_sn='$student_sn' where move_id='$move_id'";
			$CONN->Execute($query);
		}
		$res->MoveNext();
	}
	$CONN->Execute("delete from stud_move where student_sn=''");
}
?>