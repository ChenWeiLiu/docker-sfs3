<?php

//$Id: up20040102.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//在出缺席表中加入month以便計算全勤
$sql = "ALTER TABLE `stud_absent` ADD `month` TINYINT( 2 ) UNSIGNED NOT NULL ;";
$CONN->Execute($sql);
$sql = "select sasn,date from stud_absent";
$rs=$CONN->Execute($sql);
if ($rs) {
	while (!$rs->EOF) {
		$sasn=$rs->fields['sasn'];
		$d=$rs->fields['date'];
		$dd="";
		$dd=explode("-",$d);
		$month=intval($dd[1]);
		$CONN->Execute("update stud_absent set month='$month' where sasn='$sasn'");
		$rs->MoveNext();
	}
}
?>
