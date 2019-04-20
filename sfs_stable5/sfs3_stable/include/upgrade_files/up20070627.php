<?php

//$Id: up20070627.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query="CREATE TABLE if not exists class_comment_admin (
	ccm_id int(10) unsigned NOT NULL auto_increment,
	teacher_sn smallint(6) unsigned NOT NULL default '0',
	class_id varchar(11) NOT NULL default '',
	sel_year smallint(5) NOT NULL default '0',
	sel_seme enum('1','2') NOT NULL default '1',
	update_time datetime NOT NULL default '0000-00-00 00:00:00',
	update_teacher_sn smallint(6) unsigned NOT NULL default '0',
	sendmit enum('0','1') NOT NULL default '1',
	PRIMARY KEY (ccm_id))";
mysql_query($query);
?>
