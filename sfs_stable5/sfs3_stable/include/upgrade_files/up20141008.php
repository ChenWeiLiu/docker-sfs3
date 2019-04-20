<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `stud_base` ADD `obtain` VARCHAR(1) NULL , ADD `safeguard` VARCHAR(1) NULL ;";
$rs=$CONN->Execute($SQL);

?>