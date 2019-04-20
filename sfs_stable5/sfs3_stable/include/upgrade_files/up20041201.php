<?php

//$Id: up20041201.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//修正專科教室新增限制節次欄未建立的錯誤
$query="ALTER TABLE `spec_classroom` ADD `notfree_time` VARCHAR(250)";
$CONN->Execute($query);
?>