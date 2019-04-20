CREATE TABLE `authentication_empower` (
  `sn` int(11) NOT NULL auto_increment,
  `year_seme` varchar(4) default NULL,
  `subitem_sn` int(11) default NULL,
  `class_id` varchar(5) default NULL,
  `teacher_sn` int(11) default NULL,
  `empowered_sn` int(11) default NULL,
  `empowered_date` datetime default NULL,
  PRIMARY KEY  (`sn`)
) ENGINE=MyISAM;