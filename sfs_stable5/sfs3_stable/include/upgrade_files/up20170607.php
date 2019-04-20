<?php

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `stud_psy_test` CHANGE `explanation` `explanation` TEXT NULL DEFAULT NULL;";
$rs=$CONN->Execute($SQL);

?>
