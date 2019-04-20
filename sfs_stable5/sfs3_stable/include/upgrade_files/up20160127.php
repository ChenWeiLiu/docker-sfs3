<?php
	
//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}

  $SQL="ALTER TABLE `pro_check_new` CHANGE COLUMN `is_admin` `is_admin` enum('0','1','2') NOT NULL DEFAULT '0'";
  $rs=$CONN->Execute($SQL);

   
	
?>