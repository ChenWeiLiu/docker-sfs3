
#
# 資料表格式： `stud_service`
#

#服務明細  
# year_seme 學年度 ,service_date日期 , department登錄單位 , item服務學習類型 , memo服務細目說明, update_sn記錄者 , update_time記錄時間

CREATE TABLE IF NOT EXISTS `stud_service` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `year_seme` varchar(4) NOT NULL,
  `service_date` date NOT NULL,
  `department` tinyint(3) unsigned NOT NULL,
  `item` varchar(100) NOT NULL,
  `memo` text NOT NULL,
  `update_sn` int(10) unsigned NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `year_seme` (`year_seme`),
  KEY `department` (`department`),
  KEY `service_date` (`service_date`)
) ;

#每位學生的服務時數記綠
# student_sn學生 ,item_sn關連的服務明細, minutes服務了多久（分） , bonus積分

CREATE TABLE IF NOT EXISTS `stud_service_detail` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `student_sn` int(10) unsigned NOT NULL,
  `item_sn` int(10) unsigned NOT NULL,
  `minutes` tinyint(3) unsigned NOT NULL,
  `bonus` tinyint(3) unsigned NOT NULL,
  `studmemo` varchar(30) NOT NULL,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`),
  KEY `item_sn` (`item_sn`)
) ;