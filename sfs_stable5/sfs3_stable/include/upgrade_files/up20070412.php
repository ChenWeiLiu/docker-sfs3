<?php

//$Id: up20070412.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query = "ALTER TABLE `stud_base` DROP PRIMARY KEY,ADD PRIMARY KEY (`stud_study_year`,`stud_id`)";
$CONN->Execute($query);
?>
