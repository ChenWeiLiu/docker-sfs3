#
# 資料表格式： `authentication_item`
#

CREATE TABLE `authentication_item` (
  `sn` int(11) NOT NULL auto_increment,
  `year_seme` varchar(5) NOT NULL,
  `code` varchar(5) NOT NULL,
  `nature` varchar(20) NOT NULL,
  `title` varchar(60) default NULL,
  `description` text NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_date` date default NULL,
  `end_date` date default NULL,
  `cooperate` varchar(1) default NULL,
  `creater` int(11) default NULL,
  PRIMARY KEY  (`sn`),
  UNIQUE KEY `code` (`code`),
  KEY `room_id` (`room_id`,`start_date`,`end_date`,`creater`),
  KEY `nature` (`nature`),
  KEY `year_seme` (`year_seme`)
) ENGINE=MyISAM AUTO_INCREMENT=1;



#
# 資料表格式： `authentication_record`
#

CREATE TABLE `authentication_record` (
  `sn` int(11) NOT NULL auto_increment,
  `year_seme` varchar(5) NOT NULL,
  `sub_item_sn` int(11) NOT NULL,
  `student_sn` int(11) NOT NULL,
  `teacher_sn` int(11) NOT NULL,
  `date` date NOT NULL,
  `score` float default NULL,
  `note` varchar(20) NOT NULL,
  PRIMARY KEY  (`sn`),
  KEY `year_seme` (`year_seme`),
  KEY `student_sn` (`student_sn`),
  KEY `sub_item_sn` (`sub_item_sn`),
  KEY `date` (`date`)
) ENGINE=MyISAM AUTO_INCREMENT=1;


#
# 資料表格式： `authentication_subitem`
#

CREATE TABLE `authentication_subitem` (
  `sn` int(11) NOT NULL auto_increment,
  `item_sn` int(11) NOT NULL,
  `code` varchar(3) default NULL,
  `title` varchar(60) default NULL,
  `grades` varchar(20) NOT NULL,
  `bonus` tinyint(4) default '0',
  `cooperate` varchar(1) default NULL,
  PRIMARY KEY  (`sn`),
  KEY `item_sn` (`item_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=1;




