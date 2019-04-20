<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="ALTER TABLE `stud_base` CHANGE `email_pass` `email_pass` VARCHAR(32) NULL DEFAULT NULL";
$rs=$CONN->Execute($SQL);

?>