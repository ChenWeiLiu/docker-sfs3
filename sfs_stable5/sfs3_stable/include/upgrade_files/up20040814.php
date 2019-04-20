<?php

//$Id: up20040814.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
//更正期中成績表的class_id錯誤
$query="select * from stud_move where student_sn='0' and stud_id not like 'g%'";
$res=$CONN->Execute($query) or die($query);
while (!$res->EOF) {
        $query_find="select student_sn from stud_base where stud_id='".$res->fields[stud_id]."'";
        $res_find=$CONN->Execute($query_find) or die($query_find);
        if ($res_find->RecordCount()==0)
                $query_update="delete from  stud_move  where stud_id='".$res->fields[stud_id]."'";
        else
                $query_update="update stud_move set student_sn=".$res_find->fields[student_sn]." where move_id='".$res->fields[move_id]."'";
        $CONN->Execute($query_update) or die($query_update);
        $res->MoveNext();
}
?>
