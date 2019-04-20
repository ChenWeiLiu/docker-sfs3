<?php

//$Id: up20070105.php 6534 2011-09-22 09:46:05Z infodaes $

if(!$CONN){
        echo "go away !!";
        exit;
}

$sql="CREATE TABLE  if not exists `stud_seme_score_nor_chk` (
  seme_year_seme varchar(6) NOT NULL default '',
        student_sn int(10) unsigned NOT NULL default '0',
        ss_id smallint(5) unsigned NOT NULL default '0',
        main tinyint(2) unsigned NOT NULL default '0',
        sub tinyint(2) unsigned NOT NULL default '0',
        ms_score tinyint(2) NOT NULL default '0',
        ms_memo text NOT NULL default '',
        PRIMARY KEY  (seme_year_seme,student_sn,main,sub)
)  ;";

$res=$CONN->Execute($sql);
?>

