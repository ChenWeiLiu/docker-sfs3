# 請將您的資料表 CREATE TABLE 語法置於下。
# 若無，則請將本檔 module.sql 刪除。
#
# 資料表格式： `blog_home`
#

CREATE TABLE `blog_home` (
  `bh_sn` int(11) NOT NULL auto_increment,
  `style` varchar(20) NOT NULL default 'default',
  `main` text NOT NULL,
  `direction` text NOT NULL,
  `owner_id` int(11) NOT NULL default '0',
  `alias` varchar(20) NOT NULL default '',
  `start` date NOT NULL default '0000-00-00',
  `enable` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`bh_sn`),
  UNIQUE KEY `owner_id` (`owner_id`),
  KEY `owner_id_2` (`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

#
# 資料表格式： `blog_content`
#

CREATE TABLE `blog_content` (
  `bc_sn` int(11) NOT NULL auto_increment,
  `kind_sn` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `content2` text NOT NULL,
  `bh_sn` int(11) NOT NULL default '0',
  `owner_id` int(11) NOT NULL default '0',
  `dater` datetime NOT NULL default '0000-00-00 00:00:00',
  `freq` int(11) NOT NULL default '0',
  `enable` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`bc_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

#
# 資料表格式： `blog_kind`
#

CREATE TABLE `blog_kind` (
  `kind_sn` int(11) NOT NULL auto_increment,
  `bh_sn` int(11) NOT NULL default '0',
  `owner_id` int(11) NOT NULL default '0',
  `kind_name` varchar(100) NOT NULL default '',
  `enable` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`kind_sn`),
  UNIQUE KEY `bh_sn` (`bh_sn`,`kind_name`,`enable`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

#
# 資料表格式： `blog_feelback`
#

CREATE TABLE `blog_feelback` (
  `bf_sn` int(11) NOT NULL auto_increment,
  `bc_sn` int(11) NOT NULL default '0',
  `name` varchar(20) NOT NULL default '',
  `feel_cont` text NOT NULL,
  `feel_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`bf_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

#
# 資料表格式： `hd_quota`
#

CREATE TABLE `blog_quota` (
  `teacher_sn` int(11) NOT NULL default '0',
  `size` int(11) NOT NULL default '0',
  `many` int(11) NOT NULL default '0',
  `enable` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`teacher_sn`)
) ENGINE=MyISAM;






