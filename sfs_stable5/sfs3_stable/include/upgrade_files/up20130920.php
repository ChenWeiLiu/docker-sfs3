<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="ALTER TABLE `teacher_base` ADD `ldap_password` VARCHAR( 60 ) NULL";
$rs=$CONN->Execute($SQL);

$SQL="ALTER TABLE `stud_base` ADD `ldap_password` VARCHAR( 60 ) NULL";
$rs=$CONN->Execute($SQL);

// 更新所有學生密碼
$sql = "SELECT student_sn, email_pass FROM stud_base";
$res = $CONN->Execute($sql)or die($sql);

foreach ($res as $row) {	
	$ldap_password = createLdapPassword($row['email_pass']);
	
	$sql = "UPDATE stud_base SET ldap_password='$ldap_password' WHERE student_sn='{$row['student_sn']}'";
	
	$CONN->Execute($sql) ;
}

?>