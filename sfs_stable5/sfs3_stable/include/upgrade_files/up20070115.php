<?php

//$Id: up20070115.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//加入登入錯誤資料表
$sql="CREATE TABLE  if not exists `bad_login` (
  id int(11) NOT NULL auto_increment,
	log_id varchar(20) NOT NULL default '',
	log_ip varchar(15) NOT NULL default '000.000.000.000',
  err_kind varchar(100) NOT NULL default '',
  log_time datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY (id)
)";

$res=$CONN->Execute($sql);
?>
