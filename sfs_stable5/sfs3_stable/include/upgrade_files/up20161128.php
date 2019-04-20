<?php

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `grad_stud` CHANGE `stud_grad_year` `stud_grad_year` TINYINT(3) NULL DEFAULT NULL;";
$rs=$CONN->Execute($SQL);

?>
