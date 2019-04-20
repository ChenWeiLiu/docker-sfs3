<?php

//$Id: up20051014.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//在戶籍資料中加入student_sn
$query="ALTER TABLE `stud_domicile` add `student_sn` INT(10) unsigned not NULL default '0'";
if ($CONN->Execute($query)) {
	$query="select student_sn,stud_id from stud_base";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
	        $CONN->Execute("update stud_domicile set student_sn='".$res->fields[student_sn]."' where stud_id='".$res->fields[stud_id]."'");
	        $res->MoveNext();
	}
}
?>
