<?php

//$Id: up20100815.php 6099 2010-09-07 07:03:19Z infodaes $

if(!$CONN){
        echo "go away !!";
        exit;
}

//如果 stud_domicile 表中沒有 student_sn 欄位的話則加入此欄位
$query="show columns from stud_domicile where Field='student_sn'";
$res=$CONN->Execute($query);
if ($res->RecordCount()==0) {
	$query="ALTER TABLE `stud_domicile` add `student_sn` INT(10) unsigned not NULL default '0'";
	$CONN->Execute($query);
}

//找出 student_sn=0 的所有學號
$query="select * from stud_domicile where student_sn='0'";
$res=$CONN->Execute($query);
$temp_arr=array();
while(!$res->EOF) {
	if ($res->fields['stud_id']>0) $temp_arr[]=$res->fields['stud_id'];
	$res->MoveNext();
}

//如果有 student_sn=0 的紀錄, 則更新 student_sn
if (count($temp_arr)>0) {
	$stud_str="'".implode("','",$temp_arr)."'";
	$query="select * from stud_base where stud_id in ($stud_str) order by stud_study_year";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$student_sn=$res->fields['student_sn'];
		$stud_id=$res->fields['stud_id'];
		if ($student_sn>0) $CONN->Execute("update stud_domicile set student_sn='$student_sn' where student_sn='0' and stud_id='$stud_id'");
		$res->MoveNext();
	}
	//為了把 student_sn 改變為 primary key, 所以刪除 student_sn=0 的所有紀錄
	$CONN->Execute("delete from stud_domicile where student_sn=0");
	//把 primary key 改成 student_sn
	$CONN->Execute("ALTER TABLE `stud_domicile` DROP PRIMARY KEY,ADD PRIMARY KEY (`student_sn`)");
}

$temp_arr=array();
?>
