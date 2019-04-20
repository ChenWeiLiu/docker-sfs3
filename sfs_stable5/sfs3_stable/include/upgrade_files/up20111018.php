<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `score_ss` ADD `sections` TINYINT UNSIGNED NULL;";
$rs=$CONN->Execute($SQL);

?>