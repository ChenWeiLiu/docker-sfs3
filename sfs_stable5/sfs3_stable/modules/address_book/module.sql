#$Id:$
#

CREATE TABLE IF NOT EXISTS `address_book` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `nature` varchar(20) default NULL,
  `room` varchar(20) default NULL,
  `title` varchar(100) default NULL,
  `header` varchar(200) default NULL,
  `footer` varchar(200) default NULL,
  `fields` varchar(200) default NULL,
  `columns` tinyint(4) unsigned default '1',
  `creater` varchar(20) default NULL,
  `update_time` datetime default NULL,
  PRIMARY KEY  (`sn`)
);
