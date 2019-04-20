<?php

//$Id: up20031128.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//rewardªí§ó·s
$query = "ALTER TABLE `reward` CHANGE `move_year_seme` `reward_year_seme` VARCHAR(6) DEFAULT NULL";
$CONN->Execute($query);
$query = "ALTER TABLE `reward` CHANGE `move_date` `reward_date` DATE DEFAULT '0000-00-00'";
$CONN->Execute($query);
$query = "ALTER TABLE `reward` CHANGE `move_c_date` `reward_c_date` DATE DEFAULT '0000-00-00'";
$CONN->Execute($query);
$query1 = "ALTER TABLE `reward` ADD `dep_id` BIGINT(20) DEFAULT '0'";
$query2 = "ALTER TABLE `reward` ADD `student_sn` INT(10) DEFAULT '0'";
if ($CONN->Execute($query1) && $CONN->Execute($query2)) {
	$sql="update reward set dep_id=reward_id";
	$rs=$CONN->Execute($sql);
	$sql="select distinct stud_id from reward order by stud_id";
	$rs=$CONN->Execute($sql);
	if ($rs){
		while (!$rs->EOF) {
			$stud_id=$rs->fields["stud_id"];
			$all_id.="'".$stud_id."',";
			$rs->MoveNext();
		}
	}
	$all_id=substr($all_id,0,-1);
	if ($all_id) {
		$sql="select stud_id,student_sn from stud_base where stud_id in ($all_id)";
		$rs=$CONN->Execute($sql);
		while (!$rs->EOF) {
			$student_sn=$rs->fields["student_sn"];
			$stud_id=$rs->fields["stud_id"];
			$sql_s="update reward set student_sn='$student_sn' where stud_id='$stud_id'";
			$rs_s=$CONN->Execute($sql_s);
			$rs->MoveNext();
		}
	}
}
?>
