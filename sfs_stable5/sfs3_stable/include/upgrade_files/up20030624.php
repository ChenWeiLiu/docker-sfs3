<?php

//$Id: up20030624.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
//加入欄位
$query = "ALTER TABLE `stud_seme` ADD `student_sn` INT UNSIGNED NOT NULL";
$res = $CONN->Execute($query);
//if ($res) {
	//將 student_sn 加入 stud_seme 中
	$query = "select stud_id,student_sn from stud_base ";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$query = "update stud_seme set student_sn='".$res->fields[1]."' where stud_id='".$res->fields[0]."'";
		$CONN->Execute($query);
		$res->MoveNext();
	}
//}

?>
