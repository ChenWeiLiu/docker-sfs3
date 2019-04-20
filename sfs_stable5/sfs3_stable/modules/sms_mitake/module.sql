CREATE TABLE IF NOT EXISTS `sms_mitake_record` (
  `sn` int(11) NOT NULL auto_increment,
  `year_seme` varchar(4) NOT NULL,
  `ask_time` datetime NOT NULL,
  `ask_ip` varchar(40) NOT NULL,
  `teacher_sn` int(11) NOT NULL,
  `private` tinyint(1) default NULL,
  `username` varchar(20) NOT NULL,
  `dstaddr` varchar(20) NOT NULL,
  `DestName` varchar(36) NOT NULL,
  `dlvtime` datetime NOT NULL,
  `smbody` varchar(255) NOT NULL,
  `ClientID` varchar(36) NOT NULL,
  `msgid` varchar(10) NOT NULL,
  `statuscode` char(1) NOT NULL,
  `AccountPoint` int(11) NOT NULL,
  `Duplicate` varchar(1) NOT NULL,
  `donetime` varchar(14) NOT NULL,
  PRIMARY KEY  (`sn`),
  KEY `ask_time` (`ask_time`),
  KEY `teacher_sn` (`teacher_sn`),
  KEY `dstaddr` (`dstaddr`),
  KEY `year_seme` (`year_seme`),
  KEY `statuscode` (`statuscode`),
  KEY `msgid` (`msgid`)
);

