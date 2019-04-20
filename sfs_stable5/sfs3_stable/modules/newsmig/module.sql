# $Id: module.sql 8067 2014-06-17 07:38:27Z smallduh $
#
# 資料表格式： 'newsmig
#

CREATE TABLE `newsmig` (
  `news_sno` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(60) default NULL,
  `posterid` varchar(10) default NULL,
  `news` text,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newslink` varchar(70) default NULL,
  PRIMARY KEY  (`news_sno`)
) ENGINE=MyISAM AUTO_INCREMENT=0 ;
