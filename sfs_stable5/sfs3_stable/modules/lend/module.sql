#
# 資料表格式： `equ_board`
#

CREATE TABLE `equ_board` (
  `sn` int(11) NOT NULL auto_increment,
  `announce_date` date default NULL,
  `announce_limit` date default NULL,
  `manager_sn` int(11) default NULL,
  `title` varchar(100) default NULL,
  `detail` text,
  `receiver_sn` text,
  `received_sn` text,
  `modified` int(11) default '0',
  PRIMARY KEY  (`sn`),
  KEY `manager_sn` (`manager_sn`)
) ;


#
# 資料表格式： `equ_equipments`
#

CREATE TABLE `equ_equipments` (
  `sn` int(11) NOT NULL auto_increment,
  `serial` varchar(15) default NULL,
  `barcode` varchar(15) default NULL,
  `asset_no` varchar(15) default NULL,
  `item` varchar(40) default NULL,
  `importance` varchar(6) default NULL,
  `nature` varchar(30) default NULL,
  `maker` varchar(10) default NULL,
  `model` varchar(10) default NULL,
  `position` varchar(5) default NULL,
  `saler` varchar(20) default NULL,
  `sign_date` date default NULL,
  `warranty` date default NULL,
  `healthy` varchar(10) default NULL,
  `days_limit` smallint(6) default NULL,
  `cost` int(11) default NULL,
  `usage_years` int(11) default '1',
  `crashed_reason` varchar(20) default NULL,
  `crash_date` date default NULL,
  `manager_sn` int(11) default NULL,
  `crash_teacher_sn` int(11) default NULL,
  `opened` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`sn`),
  UNIQUE KEY `serial` (`manager_sn`,`serial`),
  KEY `asset_no` (`asset_no`),
  KEY `nature` (`nature`)
);


#
# 資料表格式： `equ_record`
#

CREATE TABLE `equ_record` (
  `sn` int(11) NOT NULL auto_increment,
  `year_seme` varchar(6) default NULL,
  `teacher_sn` int(11) default NULL,
  `ask_date` datetime default NULL,
  `allowed_date` date default NULL,
  `lend_date` datetime default NULL,
  `equ_serial` varchar(15) NOT NULL default '0',
  `refund_limit` date default NULL,
  `refund_date` date default NULL,
  `memo` varchar(20) default NULL,
  `manager_sn` int(11) default NULL,
  `receiver_sn` int(11) default NULL,
  PRIMARY KEY  (`sn`),
  KEY `equ_serial` (`equ_serial`),
  KEY `manager_sn` (`manager_sn`),
  KEY `teacher_sn` (`teacher_sn`),
  KEY `year_seme` (`year_seme`)
) ;

#
# 資料表格式： `equ_request`
#

CREATE TABLE `equ_request` (
  `sn` int(11) NOT NULL auto_increment,
  `teacher_sn` int(11) default NULL,
  `ask_date` datetime default NULL,
  `equ_serial` varchar(15) default NULL,
  `manager_sn` int(11) default NULL,
  `allowed_date` datetime default NULL,
  `status` varchar(10) default NULL,
  `memo` varchar(10) default NULL,
  PRIMARY KEY  (`sn`),
  KEY `teacher_sn` (`teacher_sn`),
  KEY `manager_sn` (`manager_sn`)
) ;
    