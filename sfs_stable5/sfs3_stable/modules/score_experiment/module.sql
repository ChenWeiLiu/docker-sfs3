
#  實驗教育成績管理

CREATE TABLE IF NOT EXISTS `score_experiment` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `student_sn` int(10) NOT NULL,
  `year_seme` varchar(4) NOT NULL,
  `score` int(3) NOT NULL,
  `score_level` varchar(2) NOT NULL,
  `hard_level` varchar(10) NOT NULL,
  `score_memo` varchar(200) NOT NULL,
	`append_memo` text NOT NULL,
  `append_file` varchar(100) NOT NULL,
  `score_source` varchar(30) NOT NULL,
  `update_sn` int(10) unsigned NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`)
) ;

