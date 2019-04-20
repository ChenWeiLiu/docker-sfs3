<?php

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `score_ss` CHANGE `k12ea_lanuage` `k12ea_language` TINYINT(4) NULL DEFAULT NULL;";
$rs=$CONN->Execute($SQL);

?>
