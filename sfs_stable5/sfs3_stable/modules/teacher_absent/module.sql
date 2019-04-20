--
-- 資料表格式： `teacher_absent`
--

CREATE TABLE IF NOT EXISTS `teacher_absent` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` smallint(5) unsigned NOT NULL default '0',
  `semester` enum('1','2') NOT NULL default '1',
  `month` enum('1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL default '1',
  `day` float NOT NULL,
  `hour` int(11) NOT NULL,
  `teacher_sn` smallint(6) unsigned NOT NULL default '0',
  `reason` text NOT NULL,
  `abs_kind` char(3) NOT NULL default '',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `class_dis` char(3) NOT NULL default '',
  `deputy_sn` smallint(6) unsigned NOT NULL default '0',
  `status` enum('0','1') NOT NULL default '0',
  `enable` enum('0','1') NOT NULL default '1',
  `record_id` varchar(5) NOT NULL default '',
  `record_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `check1_sn` smallint(6) unsigned NOT NULL default '0',
  `check2_sn` smallint(6) unsigned NOT NULL default '0',
  `check3_sn` smallint(6) unsigned NOT NULL default '0',
  `check4_sn` smallint(11) unsigned NOT NULL default '0',
  `deputy_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `check1_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `check2_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `check3_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `check4_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `locale` text NOT NULL,
  `post_k` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- 資料表格式： `teacher_absent_course`
--
CREATE TABLE IF NOT EXISTS `teacher_absent_course` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `travel` enum('0','1') NOT NULL,
  `end_date` text NOT NULL,
  `start_date` text NOT NULL,
  `a_id` int(10) unsigned NOT NULL default '0',
  `d_kind` enum('1','2','3') NOT NULL default '1',
  `class_dis` enum('0','1','2') NOT NULL default '1',
  `teacher_sn` mediumint(9) NOT NULL default '0',
  `deputy_sn` mediumint(9) NOT NULL default '0',
  `class_name` text NOT NULL,
  `deputy_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` enum('0','1') NOT NULL default '0',
  `times` float NOT NULL default '0',
  `outlay1` int(4) NOT NULL default '0',
  `outlay2` int(4) NOT NULL default '0',
  `outlay3` int(4) NOT NULL default '0',
  `outlay4` int(4) NOT NULL default '0',
  `outlay5` int(4) NOT NULL default '0',
  `outlay6` int(4) NOT NULL default '0',
  `outlay7` int(4) NOT NULL default '0',
  `outlay8` int(4) NOT NULL default '0',
  `outlay_a` int(4) NOT NULL default '0',
  `outl_id` text NOT NULL,
  PRIMARY KEY  (`c_id`),
  KEY `class_year` (`deputy_sn`),
  KEY `year` (`a_id`,`d_kind`)
);
