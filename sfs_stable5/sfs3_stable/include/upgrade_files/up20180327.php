<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$query1 = "ALTER TABLE `stud_base` ADD `experiment_kind` tinyint(1) not null default 0;";
$rs=$CONN->Execute($query1);
$query2 = "ALTER TABLE `stud_base` ADD `exp_group_name` varchar(30) null;";
$rs=$CONN->Execute($query2);

?>
