# // $Id: module.sql 5916 2010-03-24 11:27:00Z hami $
#
# Table structure for table 'fixedtb'
#


 CREATE TABLE `fixedtb` (
  `ID` mediumint(9) NOT NULL auto_increment,
  `even_T` varchar(255) NOT NULL default '',
  `even_doc` text NOT NULL,
  `unitId` varchar(12) NOT NULL default '',
  `user` varchar(12) NOT NULL default '',
  `even_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `even_mode` tinyint(4) NOT NULL default '0',
  `rep_date` datetime default NULL,
  `rep_user` varchar(12) default NULL,
  `rep_doc` text,
  `rep_mode` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;
# Table structure for table 'fixed_kind'
#

CREATE TABLE fixed_kind (
   bk_id varchar(12) DEFAULT '0' NOT NULL,
   board_name varchar(20) NOT NULL,
   Email_list varchar(100),
   PRIMARY KEY (bk_id)
);

CREATE TABLE `fixed_check` (
  `pc_id` int(11) NOT NULL auto_increment,
  `pro_kind_id` varchar(12) NOT NULL default '',
  `post_office` tinyint(4) NOT NULL default '-1',
  `teach_id` varchar(20) NOT NULL default 'none',
  `teach_title_id` tinyint(4) NOT NULL default '-1',
  `is_admin` char(1) NOT NULL default '',
  PRIMARY KEY  (`pc_id`)
) ENGINE=MyISAM;



