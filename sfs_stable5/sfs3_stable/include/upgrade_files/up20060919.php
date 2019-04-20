<?php

//$Id: up20060919.php 6534 2011-09-22 09:46:05Z infodaes $

if(!$CONN){
        echo "go away !!";
        exit;
}

//新增 stud_seme_score_oth,stud_seme_rew,stud_seme_score_nor 三資料表
$query = "CREATE TABLE `stud_seme_score_oth` (`seme_year_seme` VARCHAR( 6 ) NOT NULL ,`stud_id` VARCHAR( 20 ) NOT NULL ,`ss_kind` VARCHAR( 12 ) NOT NULL ,`ss_id` SMALLINT UNSIGNED NOT NULL ,`ss_val` VARCHAR( 20 ) NOT NULL ,PRIMARY KEY ( `seme_year_seme`,`stud_id`,`ss_kind`,`ss_id` )) COMMENT='與科目無關的記錄';";
$CONN->Execute($query);
$query = "CREATE TABLE `stud_seme_rew` (`seme_year_seme` VARCHAR( 6 ) NOT NULL ,`stud_id` VARCHAR( 20 ) NOT NULL ,`sr_kind_id` TINYINT NOT NULL ,`sr_num` TINYINT NOT NULL, PRIMARY KEY ( `seme_year_seme` , `stud_id` ,`sr_kind_id`)) COMMENT='學期獎懲記錄';";
$CONN->Execute($query);
$query = "CREATE TABLE `stud_seme_score_nor` (`seme_year_seme` varchar( 6 ) NOT NULL ,`student_sn` int( 10 ) unsigned NOT NULL default '0',`ss_id` smallint( 5 ) unsigned NOT NULL ,`ss_score` decimal( 4, 2 ) default NULL ,`ss_score_memo` text,PRIMARY KEY ( `seme_year_seme`,`student_sn`,`ss_id` )) ;";
$CONN->Execute($query);

?>
