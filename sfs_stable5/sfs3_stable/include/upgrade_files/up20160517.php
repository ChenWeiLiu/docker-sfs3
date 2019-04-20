<?php
if(!$CONN){
        echo "go away !!";
        exit;
}

$sql = "ALTER TABLE `teacher_base` ADD `last_chpass_time` date NOT NULL DEFAULT '2016-05-17'";
$CONN->Execute($sql);
$sql = "ALTER TABLE `teacher_base` ADD `mem_array` text NULL ";
$CONN->Execute($sql);

?>
