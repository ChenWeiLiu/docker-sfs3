<?php

//$Id: up20030627.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
//¥[¤JÄæ¦ì
$query = "ALTER TABLE `pro_check_new` ADD `set_sn` INT UNSIGNED NOT NULL ,ADD `p_start_date` DATE,ADD `p_end_date` DATE NOT NULL ,ADD `oth_set` VARCHAR( 20 ) NOT NULL ; ";
$res = $CONN->Execute($query);

?>
