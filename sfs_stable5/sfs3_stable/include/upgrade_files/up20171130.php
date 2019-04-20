<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$query1 = "ALTER TABLE `teacher_appraisal` ADD `spec_allowance` int(5) NOT NULL AFTER `base_salary` ;";
$query2 = "ALTER TABLE `teacher_appraisal` CHANGE `allowance` `leader_allowance` INT( 5 ) NOT NULL ;";

$rs=$CONN->Execute($query1);
$rs=$CONN->Execute($query2);

?>
