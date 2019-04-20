<?php

//$Id: up20051228.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//修正teacher_base之login_pass長度
$query="ALTER TABLE `teacher_base` CHANGE `login_pass` `login_pass` varchar(32) DEFAULT NULL";
$CONN->Execute($query);
$query="select * from teacher_base where LENGTH(login_pass)>=31";
$res=$CONN->Execute($query);
if ($res->RecordCount()==0) {
	$query="select * from teacher_base order by teacher_sn";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$pass=md5($res->fields[login_pass]);
		$pass=substr($pass,10).substr($pass,0,9);
		$CONN->Execute("update teacher_base set login_pass='$pass' where teacher_sn='".$res->fields[teacher_sn]."'");
		$res->MoveNext();
	}
}
?>
