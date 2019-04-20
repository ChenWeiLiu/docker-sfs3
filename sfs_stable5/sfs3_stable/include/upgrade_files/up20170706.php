<?php

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `school_class` ADD `c_kind_k12ea` VARCHAR(1) NULL AFTER `c_kind`;";
$rs=$CONN->Execute($SQL);

?>
