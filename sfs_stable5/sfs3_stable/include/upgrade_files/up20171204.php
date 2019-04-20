<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$query = "ALTER TABLE `school_class` ADD `campus` VARCHAR(10) NULL COMMENT '校區' AFTER `c_sort`;";
$rs=$CONN->Execute($query);

?>
