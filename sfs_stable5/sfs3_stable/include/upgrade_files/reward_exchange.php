<?php

//$Id:  $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query = "CREATE TABLE IF NOT EXISTS `reward_exchange` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(10) default '0',
  `reward_year_seme` varchar(6) default NULL,
  `reward_date` date default NULL,
  `reward_kind` varchar(10) NOT NULL default '',
  `reward_numbers` tinyint(4) NOT NULL,
  `reward_reason` text,
  PRIMARY KEY  (`sn`));";
 $CONN->Execute($query);
?>
