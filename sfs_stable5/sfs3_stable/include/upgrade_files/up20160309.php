<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="ALTER TABLE `cita_kind` ADD `teach_id` varchar(20) DEFAULT NULL";
$rs=$CONN->Execute($SQL);


?>