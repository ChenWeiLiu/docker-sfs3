
CREATE TABLE IF NOT EXISTS `career_consultation` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `seme_key` varchar(5) NOT NULL,
  `consultation_date` date default NULL,
  `teacher_name` varchar(20) default NULL,
  `emphasis` text,
  `memo` text,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_contact`
--

CREATE TABLE IF NOT EXISTS `career_contact` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `content` text,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  UNIQUE KEY `student_sn` (`student_sn`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_course`
--

CREATE TABLE IF NOT EXISTS `career_course` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `school` varchar(50) NOT NULL,
  `aspiration_order` tinyint(3) unsigned NOT NULL,
  `course` varchar(50) NOT NULL,
  `memo` varchar(50) default NULL,
  `position` varchar(40) NOT NULL,
  `transportation` varchar(100) NOT NULL,
  `transportation_time` varchar(20) NOT NULL,
  `transportation_toll` int(11) NOT NULL,
  `factor` text,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`)
) ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_exam`
--

CREATE TABLE IF NOT EXISTS `career_exam` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `w` tinyint(3) unsigned NOT NULL,
  `c` tinyint(3) unsigned NOT NULL,
  `e` tinyint(3) unsigned NOT NULL,
  `m` tinyint(3) unsigned NOT NULL,
  `n` tinyint(3) unsigned NOT NULL,
  `s` tinyint(3) unsigned default NULL,
  `update_sn` int(11) default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  UNIQUE KEY `student_sn` (`student_sn`)

);

-- --------------------------------------------------------

--
-- 資料表格式： `career_explore`
--

CREATE TABLE IF NOT EXISTS `career_explore` (
  `sn` int(11) NOT NULL auto_increment,
  `course_id` tinyint(3) unsigned default NULL,
  `seme_key` varchar(4) default NULL,
  `student_sn` int(11) NOT NULL,
  `activity_id` tinyint(3) unsigned default NULL,
  `degree` tinyint(3) unsigned default NULL,
  `self_ponder` varchar(100) default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `year_seme` (`seme_key`,`student_sn`),
  KEY `course_id` (`course_id`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_guidance`
--

CREATE TABLE IF NOT EXISTS `career_guidance` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `guidance_date` date NOT NULL,
  `target` varchar(10) NOT NULL,
  `emphasis` text NOT NULL,
  `teacher_name` varchar(20) NOT NULL,
  `update_sn` int(11) default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`),
  KEY `guidance_date` (`guidance_date`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_mystory`
--

CREATE TABLE IF NOT EXISTS `career_mystory` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `personality` text,
  `interest` text,
  `specialty` text,
  `occupation_suggestion` text,
  `occupation_myown` text,
  `occupation_others` text,
  `occupation_weight` text,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  UNIQUE KEY `student_sn` (`student_sn`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_opinion`
--

CREATE TABLE IF NOT EXISTS `career_opinion` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `parent` varchar(20) default NULL,
  `parent_memo` text,
  `tutor` varchar(20) default NULL,
  `tutor_memo` text,
  `tutor_sn` int(11) default NULL,
  `guidance` varchar(20) default NULL,
  `guidance_memo` text,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  UNIQUE KEY `student_sn` (`student_sn`)
) ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_parent`
--

CREATE TABLE IF NOT EXISTS `career_parent` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `seme_key` varchar(5) NOT NULL,
  `items` text,
  `suggestion` text,
  `suggestion_date` datetime default NULL,
  `tutor_confirm` text,
  `tutor_name` varchar(20) default NULL,
  `confirm_date` datetime default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_race`
--

CREATE TABLE IF NOT EXISTS `career_race` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `squad` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `rank` varchar(10) default NULL,
  `certificate_date` date default NULL,
  `sponsor` varchar(40) default NULL,
  `memo` text,
  `word` varchar(100) NOT NULL,
  `weight` float NOT NULL,
  `update_sn` int(10) unsigned default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_self_ponder`
--

CREATE TABLE IF NOT EXISTS `career_self_ponder` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `id` varchar(5) NOT NULL,
  `content` text NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`,`id`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_test`
--

CREATE TABLE IF NOT EXISTS `career_test` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `id` tinyint(3) unsigned NOT NULL,
  `content` text,
  `study` varchar(50) default NULL,
  `job` varchar(50) default NULL,
  `update_sn` int(11) default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`),
  KEY `id` (`id`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `career_view`
--

CREATE TABLE IF NOT EXISTS `career_view` (
  `sn` int(11) NOT NULL auto_increment,
  `student_sn` int(11) NOT NULL,
  `ponder` text,
  `direction` text,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `student_sn` (`student_sn`)
) AUTO_INCREMENT=1 ;
