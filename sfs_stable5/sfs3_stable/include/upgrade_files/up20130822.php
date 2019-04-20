<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="ALTER TABLE `teacher_title` ADD `rank` INT NOT NULL";
$rs=$CONN->Execute($SQL);

$SQL="UPDATE  `teacher_title` SET `rank`=  `teach_title_id`";
$rs=$CONN->Execute($SQL);


?>