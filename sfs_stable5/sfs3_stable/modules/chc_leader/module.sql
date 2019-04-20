
CREATE TABLE `chc_leader` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,#流水號
  `student_sn` int(10) unsigned NOT NULL,#系統內學生sn
  `seme` varchar(6) NOT NULL DEFAULT '',#學年度
  `kind` tinyint(3) unsigned DEFAULT '0',#幹部類別0班級幹部,1社團幹部,2全校性幹部
  `org_name` varchar(50) NOT NULL DEFAULT '',#班名/社團名/組織名
  `title` varchar(50) NOT NULL DEFAULT '',#幹部名稱
  `memo` varchar(120) NOT NULL DEFAULT '',#備註
  `update_sn` int(10) unsigned NOT NULL,#更新者
  `cr_time` datetime DEFAULT NULL,#建立/更新時間
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_sn` (`student_sn`,`seme`,`kind`,`org_name`,`title`)
) ENGINE=MyISAM ;
