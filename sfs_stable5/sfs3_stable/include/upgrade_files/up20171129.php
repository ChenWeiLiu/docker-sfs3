<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$query = "ALTER TABLE `teacher_appraisal` ADD `salary_kind` VARCHAR( 20 ) NOT NULL AFTER `years_of_service` ;";

$rs=$CONN->Execute($query);

?>
