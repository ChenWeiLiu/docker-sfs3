<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="CREATE TABLE IF NOT EXISTS `pipa` (
  `pipa_sn` int(11) NOT NULL auto_increment,
  `teacher_sn` int(11) unsigned NOT NULL,
  `teacher_name` char(20) NOT NULL,
  `ip` char(40) default NULL,
  `doTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `script_name` varchar(255) NOT NULL,
  `comment` tinytext,
  PRIMARY KEY  (`pipa_sn`),
  KEY `teacher_sn` (`teacher_sn`)
)";
$rs=$CONN->Execute($SQL);

?>