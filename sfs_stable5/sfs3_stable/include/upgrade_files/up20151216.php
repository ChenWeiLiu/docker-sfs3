<?php
	
//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}

  $SQL="ALTER TABLE `stud_base` CHANGE `stud_name_eng` `stud_name_eng` VARCHAR(50) NULL DEFAULT NULL;";
  $rs=$CONN->Execute($SQL);

   
	
?>