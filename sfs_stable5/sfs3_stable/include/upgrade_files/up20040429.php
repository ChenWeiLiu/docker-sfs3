<?php

//$Id: up20040429.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//在學生基本資料中加入郵遞區號
$query="alter table stud_base add addr_zip varchar(3)";
$CONN->Execute($query);
$query="select * from stud_addr_zip";
$res=$CONN->Execute($query);
while (!$res->EOF) {
	$z[$res->fields[country]][$res->fields[town]]=$res->fields[zip];
	$res->MoveNext();
}
$query="select student_sn,stud_addr_2,stud_addr_a,stud_addr_b from stud_base";
$res=$CONN->Execute($query);
while (!$res->EOF) {
	$student_sn=$res->fields[student_sn];
	$stud_addr_2=$res->fields[stud_addr_2];
	$stud_addr_a=substr($stud_addr_2,0,6);
	$stud_addr_b=substr($stud_addr_2,6,6);
	$num=$z[$stud_addr_a][$stud_addr_b];
	if (empty($num)) {
		$stud_addr_a=$res->fields[stud_addr_a];
		$stud_addr_b=$res->fields[stud_sddr_b];
		$num=$z[$stud_addr_a][$stud_addr_b];
	}
	if ($num) $CONN->Execute("update stud_base set addr_zip='$num' where student_sn='$student_sn'");
	$res->MoveNext();
}
?>