<?php

//$Id: up20061126.php 6534 2011-09-22 09:46:05Z infodaes $

if(!$CONN){
        echo "go away !!";
        exit;
}

//加入檢核表項目資料表
$sql="CREATE TABLE  if not exists `score_nor_chk_item` (
  year smallint(5) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
	main tinyint(2) unsigned NOT NULL default '0',
	sub tinyint(2) unsigned NOT NULL default '0',
	item text NOT NULL default '',
	PRIMARY KEY  (year,seme,main,sub)
) ;";

$res=$CONN->Execute($sql);
?>
