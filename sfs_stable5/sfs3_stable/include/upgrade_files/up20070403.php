<?php

//$Id: up20070403.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query = "ALTER TABLE `stud_seme_rew` ADD `student_sn` int(10) unsigned NOT NULL default 0";
$CONN->Execute($query);
$query = "select * from stud_seme_rew where 1=0"; 
if ($CONN->Execute($query)) {
	$query = "select distinct stud_id from stud_seme_rew where student_sn=0 order by stud_id desc limit 0,10";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$stud_id[]=$res->fields['stud_id'];
		$res->MoveNext();
	}
	if (count($stud_id)>0) {
		$ids="'".implode("','",$stud_id)."'";
		$query="select stud_id,student_sn from stud_base where stud_id in ($ids)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$query="update stud_seme_rew set student_sn='".$res->fields['student_sn']."' where stud_id='".$res->fields['stud_id']."'";
			$CONN->Execute($query);
			$res->MoveNext();
		}
	}
}
?>
