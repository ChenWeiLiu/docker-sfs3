<?php

//$Id: up20061028.php 5454 2009-04-20 00:33:08Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 判斷 student_sn 是否加入
 $query = "SHOW FIELDS FROM stud_domicile LIKE  'student_sn'";
 $res=$CONN->Execute($query);
 if ($res->RecordCount()==0) {
 	$query = " ALTER TABLE stud_domicile ADD student_sn int(10) unsigned not null default 0";
 	$res=$CONN->Execute($query);
 }

//在戶籍資料中加入student_sn
$query="select a.student_sn,a.stud_id from stud_base a , stud_domicile b where a.stud_id=b.stud_id and b.student_sn=0";
$res=$CONN->Execute($query);
while(!$res->EOF) {
        $CONN->Execute("update stud_domicile set student_sn='".$res->fields[student_sn]."' where stud_id='".$res->fields[stud_id]."'");
        $res->MoveNext();
}
?>
