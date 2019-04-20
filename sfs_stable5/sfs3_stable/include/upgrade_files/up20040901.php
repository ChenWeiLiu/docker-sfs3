<?php

//$Id: up20040901.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//在score_course表中增加c_kind欄位, 記錄該節是0:正常時數, 1:兼課, 2:代課
$query="ALTER TABLE `score_course` ADD `c_kind` TINYINT(2) UNSIGNED NOT NULL default '0'";
$res=$CONN->Execute($query);
?>