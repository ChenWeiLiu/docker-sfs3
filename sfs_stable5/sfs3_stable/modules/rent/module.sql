#$Id: module.sql 5311 2009-01-10 08:11:55Z hami $

#
# 資料表格式： `rent_record`
#
CREATE TABLE `rent_record` (
  `record_id` int(11) NOT NULL auto_increment,
  `ask_time` datetime default NULL,
  `ask_ip` varchar(20) NOT NULL default '',
  `rent_place` varchar(20) NOT NULL default '',
  `purpose` varchar(60) NOT NULL default '',
  `borrower` varchar(20) NOT NULL default '',
  `borrower_type` varchar(10) default NULL,
  `borrower_password` varchar(10) NOT NULL default '',
  `rent_date` date default NULL,
  `morning` enum('Y','N') NOT NULL default 'N',
  `afternoon` enum('Y','N') NOT NULL default 'N',
  `evening` enum('Y','N') NOT NULL default 'N',
  `dressing_date` date default NULL,
  `note` varchar(40) NOT NULL default '',
  `head_count` int(11) NOT NULL default '0',
  `contact` varchar(30) NOT NULL default '',
  `allowed` enum('Y','N') NOT NULL default 'N',
  `rent` int(11) NOT NULL default '0',
  `prove` int(11) NOT NULL default '0',
  `clean` int(11) NOT NULL default '0',
  `allow_date` date default NULL,
  `reply` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`record_id`),
  KEY `place` (`rent_place`)
);


#
# 資料表格式： `rent_place`
#

CREATE TABLE `rent_place` (
  `rank` char(2) default NULL,
  `rent_place` varchar(20) NOT NULL default '',
  `note` varchar(100) NOT NULL default '',
  `rent_public` int(11) NOT NULL default '0',
  `rent_private` int(11) NOT NULL default '0',
  `rent_special` int(11) NOT NULL default '0',
  `prove_public` int(11) NOT NULL default '0',
  `prove_private` int(11) NOT NULL default '0',
  `prove_special` int(11) NOT NULL default '0',
  `clean_public` int(11) NOT NULL default '0',
  `clean_private` int(11) NOT NULL default '0',
  `clean_special` int(11) NOT NULL default '0',
  PRIMARY KEY  (`rent_place`),
  KEY `rank` (`rank`)
) ;


