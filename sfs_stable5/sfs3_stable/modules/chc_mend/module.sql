
CREATE TABLE `chc_mend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,#流水號
  `student_sn` int(10) unsigned NOT NULL,#系統內學生sn
  `seme` varchar(6) NOT NULL DEFAULT '',#學年度學期
  `scope` varchar(6) NOT NULL DEFAULT '',#領域名稱 
  `score_src` float NOT NULL DEFAULT '0', #領域原始成績(計算)
  `score_test` float NOT NULL DEFAULT '0', #補考成績原始成績
  `score_end` float NOT NULL DEFAULT '0', #補考成績(採計用)
  `passok` varchar(20) NOT NULL DEFAULT '',#是否通過
  `update_sn` int(10) unsigned NOT NULL,#登錄者
  `cr_time` datetime DEFAULT NULL,#建立/更新時間
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_sn` (`student_sn`,`seme`,`scope`)
) ENGINE=MyISAM ;
