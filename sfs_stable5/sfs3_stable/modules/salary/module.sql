# phpMyAdmin SQL Dump
# version 2.5.4
# http://www.phpmyadmin.net
#
# 主機: localhost
# 建立日期: Apr 26, 2008, 12:10 PM
# 伺服器版本: 3.23.54
# PHP 版本: 4.2.2
# 
# 資料庫: `sfs3`
# 

# --------------------------------------------------------

#
# 資料表結構： `salary`
#

CREATE TABLE `salary` (
  `No` int(11) NOT NULL auto_increment,
  `InType` varchar(50) NOT NULL default '',
  `AnnounceDate` timestamp(14) NOT NULL,
  `ID` varchar(10) NOT NULL default '',
  `Name` varchar(20) NOT NULL default '',
  `DutyType` varchar(20) NOT NULL default '',
  `JobType` varchar(50) NOT NULL default '',
  `JobTitle` varchar(50) NOT NULL default '',
  `MaxPoint` int(11) NOT NULL default '0',
  `MaxExtPoint` int(11) NOT NULL default '0',
  `Point` int(11) NOT NULL default '0',
  `Thirty` char(2) NOT NULL default '',
  `ClassTMFactor` float NOT NULL default '0',
  `Insurance1Factor` float NOT NULL default '0',
  `Insurance2Factor` float NOT NULL default '0',
  `Insurance3Factor` float NOT NULL default '0',
  `InsureAmount` int(11) NOT NULL default '0',
  `InsuranceLevel` float NOT NULL default '0',
  `Family` float NOT NULL default '0',
  `Memo` varchar(255) default NULL,
  `BankName1` varchar(20) default NULL,
  `AccountID1` varchar(20) default NULL,
  `BankName2` varchar(20) default NULL,
  `AccountID2` varchar(20) default NULL,
  `BankName3` varchar(20) default NULL,
  `AccountID3` varchar(20) default NULL,
  `Mg1` int(11) default '0',
  `Mg2` int(11) default '0',
  `Mg3` int(11) default '0',
  `Mg4` int(11) default '0',
  `Mg5` int(11) default '0',
  `Mg6` int(11) default '0',
  `Mg7` int(11) default '0',
  `Mg8` int(11) default '0',
  `Mg9` int(11) default '0',
  `Mh1` int(11) default '0',
  `Mh2` int(11) default '0',
  `Mh3` int(11) default '0',
  `Mh4` int(11) default '0',
  `Mh5` int(11) default '0',
  `Mh6` int(11) default '0',
  `Mh7` int(11) default '0',
  `Mh8` int(11) default '0',
  `Mh9` int(11) default '0',
  `Mi1` int(11) default '0',
  `Mi2` int(11) default '0',
  `Mi3` int(11) default '0',
  `Mi4` int(11) default '0',
  `Mi5` int(11) default '0',
  `Mi6` int(11) default '0',
  `Mi7` int(11) default '0',
  `Mi8` int(11) default '0',
  `Mi9` int(11) default '0',
  KEY `Person_ID` (`ID`),
  KEY `upload_date` (`AnnounceDate`),
  KEY `No` (`No`)
) ENGINE=MyISAM;

