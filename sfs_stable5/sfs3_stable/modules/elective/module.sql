#
# 資料表格式： `elective_tea`
#

CREATE TABLE `elective_tea` (
  `group_id` int(11) NOT NULL auto_increment,
  `group_name` varchar(40) NOT NULL default '',
  `ss_id` int(11) NOT NULL default '0',
  `teacher_sn` int(11) NOT NULL default '0',
  `member` tinyint(3) unsigned NOT NULL default '0',
  `open` set('是','否') NOT NULL default '否',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `group_name` (`group_name`,`ss_id`,`teacher_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

#
# 資料表格式： `elective_stu`
#

CREATE TABLE `elective_stu` (
  `elective_stu_sn` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL default '0',
  `student_sn` int(11) NOT NULL default '0',
  PRIMARY KEY  (`elective_stu_sn`),
  UNIQUE KEY `ss_id` (`group_id`,`student_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
