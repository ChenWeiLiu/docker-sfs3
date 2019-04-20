<?php

//$Id: up20090922.php 9240 2018-05-04 03:25:04Z igogo $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query="alter table `login_log_new` add `ip` varchar(15) NOT NULL default ''";
mysql_query($query);
?>
