
# 資料庫： 此為本模組所使用的資料表指令, 當安裝此模組時, SFS3 系統會一併執行這裡的 MySQL 指令,建立資料表.
#					 若本模組不需建立資料表, 則留空白即可.
#
#
#
# 生涯輔導參與各項競賽記錄登錄 , 安裝 12bacis_career 即已建立


CREATE TABLE IF NOT EXISTS `career_race` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `squad` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `rank` varchar(10) default NULL,
  `certificate_date` date default NULL,
  `sponsor` varchar(40) default NULL,
  `memo` text,
  `word` VARCHAR( 100 ) NOT NULL,
  `weight` FLOAT NOT NULL,
  `update_sn` int(10) unsigned default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`)
) AUTO_INCREMENT=1 ;




