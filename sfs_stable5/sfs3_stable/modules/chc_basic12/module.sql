CREATE TABLE `chc_basic12`(
  `sn` int(10) unsigned NOT NULL auto_increment,#流水號
  `academic_year` tinyint(3) unsigned NOT NULL,#學年度
  `student_sn` int(10) unsigned NOT NULL, #系統內學生sn
  `kind_id` tinyint(3) unsigned default '0',#學生身分別--8類
  `special` char(2) NOT NULL default '0',#身心障礙 12類 0 ~ 9、A~B
  `unemployed` tinyint(1) unsigned default '0',#失業勞工--0/否，1/是。
  `graduation` tinyint(1) unsigned default '1',#畢肄業-- 0/肄業，1/畢業
  `income` tinyint(1) unsigned default '0',#經濟弱勢 0否 2低收入戶 1中低收入戶
  `score_nearby` tinyint(3) unsigned default NULL,#2.就近入學分數  
  `score_service` float default NULL,#3.品德服務--服務學習
  `score_reward` float default NULL,#4.品德服務--獎勵記錄
  `score_fault` tinyint(3) unsigned default NULL,#5.品德服務--無記過記錄
  `score_balance` tinyint(3) unsigned default NULL,#6.績優表現--均衡學習總得分  
  `score_club` tinyint(3) unsigned default NULL,#6.績優表現(社團參與105.10.26新增)
  `score_race` float default NULL, #7.績優表現--競賽表現  
  `score_physical` tinyint(3) unsigned default NULL,#8.績優表現--體適能
  `score_exam` tinyint(3) unsigned default NULL,#績優表現--教育會考總分
  `update_sn` int(10) unsigned NOT NULL, #更新者
  `update_time` timestamp NOT NULL ,
  PRIMARY KEY  (`sn`),
  UNIQUE KEY `academic_year` (`academic_year`,`student_sn`),
  KEY `student_sn` (`student_sn`)
) ENGINE=MyISAM ;

