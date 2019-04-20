#$Id: module.sql 5311 2009-01-10 08:11:55Z hami $
#
# 資料表格式： `charge_decrease`
#

CREATE TABLE `charge_decrease` (
  `decrease_id` int(11) NOT NULL auto_increment,
  `detail_id` int(11) NOT NULL default '0',
  `student_sn` int(11) NOT NULL default '0',
  `curr_class_num` varchar(6) NOT NULL default '',
  `percent` tinyint(4) NOT NULL default '0',
  `cause` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`detail_id`,`student_sn`,`curr_class_num`),
  UNIQUE KEY `decrease_id` (`decrease_id`)
);

#
# 資料表格式： `charge_detail`
#

CREATE TABLE `charge_detail` (
  `detail_id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL default '0',
  `detail_sort` char(3) default NULL,
  `detail` varchar(50) NOT NULL default '',
  `dollars` varchar(40) NOT NULL default '0',
  PRIMARY KEY  (`detail_id`)
);


#
# 資料表格式： `charge_item`
#

CREATE TABLE `charge_item` (
  `item_id` int(11) NOT NULL auto_increment,
  `year_seme` varchar(6) NOT NULL default '',
  `item_type` varchar(20) default NULL,
  `item` varchar(40) NOT NULL default '',
  `authority` varchar(40) NOT NULL default '',
  `paid_method` varchar(40) NOT NULL default '',
  `announce_note` varchar(40) NOT NULL default '',
  `announce_note2` varchar(40) NOT NULL default '',
  `start_date` date default NULL,
  `end_date` date default NULL,
  `comment` varchar(40) default NULL,
  `creater` varchar(20) default NULL,
  PRIMARY KEY  (`item_id`)
);


#
# 資料表格式： `charge_record`
#

CREATE TABLE `charge_record` (
  `record_id` varchar(10) NOT NULL default '0',
  `student_sn` int(11) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `dollars` int(11) NOT NULL default '0',
  `paid_date` date default NULL,
  `comment` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`item_id`,`record_id`),
  KEY `item_id` (`student_sn`)
);




