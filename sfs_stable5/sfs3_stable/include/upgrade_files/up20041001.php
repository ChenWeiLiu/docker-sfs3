<?php

//$Id: up20041001.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//增加開學日及結業日
$query="select * from school_day order by year,seme";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$d[$res->fields[year]][$res->fields[seme]][$res->fields[day_kind]]=$res->fields[day];
	$res->MoveNext();
}
while(list($year,$a)=each($d)) {
	reset($a);
	while(list($seme,$b)=each($a)) {
		if (empty($d[$year][$seme][st_start])) {
			$sql_insert = "replace into school_day (day_kind,day,year,seme) values ('st_start','".$d[$year][$seme][start]."','$year','$seme')";
			$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
			
		}
		if (empty($d[$year][$seme][st_end])) {
			$sql_insert = "replace into school_day (day_kind,day,year,seme) values ('st_end','".$d[$year][$seme][end]."','$year','$seme')";
			$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
			
		}
	}
}
?>