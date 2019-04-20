<?php

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `score_ss` ADD `k12ea_category` TINYINT NULL AFTER `sections`, ADD `k12ea_area` TINYINT NULL AFTER `k12ea_category`, ADD `k12ea_subject` TINYINT NULL AFTER `k12ea_area`, ADD `k12ea_lanuage` TINYINT NULL AFTER `k12ea_subject`, ADD `k12ea_frequency` TINYINT NULL AFTER `k12ea_lanuage`;";
$rs=$CONN->Execute($SQL);

?>
