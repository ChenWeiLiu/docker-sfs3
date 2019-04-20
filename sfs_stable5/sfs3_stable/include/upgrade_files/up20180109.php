<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$query1 = "ALTER TABLE `stud_base` DROP INDEX `stud_id`;";
$rs=$CONN->Execute($query1);
$query2 = "ALTER TABLE `stud_seme` DROP INDEX `stud_id`;";
$rs=$CONN->Execute($query2);

?>
