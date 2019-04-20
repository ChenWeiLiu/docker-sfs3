<?php

//$Id: up20091215.php 9240 2018-05-04 03:25:04Z igogo $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query="alter table stud_base change addr_zip addr_zip varchar(5)";
mysql_query($query);
?>
