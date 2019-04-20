<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$SQL="
CREATE TABLE IF NOT EXISTS `seme_course_date` (
 seme_year_seme varchar(6) NOT NULL default '',
 days tinyint(3) unsigned NOT NULL default '0',
 UNIQUE KEY seme_year_seme (seme_year_seme)
) COMMENT='學期上課日數';

ALTER TABLE `seme_course_date` ADD `school_days` text NOT NULL;";

$rs=$CONN->Execute($SQL);
