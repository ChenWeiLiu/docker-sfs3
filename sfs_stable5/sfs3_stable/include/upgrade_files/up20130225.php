<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="ALTER TABLE `stud_base` CHANGE `stud_study_year` `stud_study_year` INT(10) NOT NULL DEFAULT '0'";
$rs=$CONN->Execute($SQL);

?>