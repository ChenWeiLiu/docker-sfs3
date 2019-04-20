<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `teacher_base` ADD `cerno` varchar(50) default '';";
$rs=$CONN->Execute($SQL);

?>
