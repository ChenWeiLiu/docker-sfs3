<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="ALTER TABLE `sfs_module` ADD `auth_kind` INT NOT NULL DEFAULT '0'";
$rs=$CONN->Execute($SQL);

?>