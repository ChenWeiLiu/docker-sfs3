CREATE TABLE IF NOT EXISTS `score_eduh_teacher` (
  `eduh_id` int(10) unsigned NOT NULL auto_increment,
  `year_seme` varchar(4) NOT NULL,
  `ss_id` smallint(4) unsigned NOT NULL,
  `update_sn` int(10) unsigned NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`eduh_id`),
  KEY `ss_id` (`ss_id`)
) ENGINE=MyISAM;

