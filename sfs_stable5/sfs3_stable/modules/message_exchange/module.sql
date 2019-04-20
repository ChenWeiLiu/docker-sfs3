# $Id: module.sql 5311 2009-01-10 08:11:55Z hami $
#
# 資料表格式： `message_record`
#

CREATE TABLE `message_record` (
  `r_id` int(11) NOT NULL auto_increment,
  `sender` varchar(100) NOT NULL,
  `receiver` text NOT NULL,
  `m_date` datetime NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text,
  PRIMARY KEY  (`r_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6  AUTO_INCREMENT=1 ;

#
# 資料表格式： `message_info`
#

CREATE TABLE `message_info` (
  `m_id` int(11) NOT NULL auto_increment,
  `rece_id` varchar(100) NOT NULL,
  `send_id` varchar(100) NOT NULL,
  `r_check` tinyint(4) default '0',
  `r_date` datetime NOT NULL,
  `r_id` int(11) NOT NULL,
  PRIMARY KEY  (`m_id`)
) ENGINE=MyISAM AUTO_INCREMENT=288 AUTO_INCREMENT=1 ;

