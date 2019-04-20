<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}

$SQL="ALTER TABLE `pro_check_new` ADD `auth_kind` varchar(1) default '0';";
$rs=$CONN->Execute($SQL);

?>