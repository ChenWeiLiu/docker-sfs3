
# 資料庫： 此為本模組所使用的資料表指令, 當安裝此模組時, SFS3 系統會一併執行這裡的 MySQL 指令,建立資料表.
#					 若本模組不需建立資料表, 則留空白即可.
#
#
#
# 班級小考成績管理

#成績單設定 , 學期、班級、成績單名稱、建檔老師流水號、可key成績的學生、是否開放輸入、是否統計平均、是否顯示排名、學生能否查詢全班成績
CREATE TABLE IF NOT EXISTS `class_report_setup` (
  `sn` int(11) NOT NULL auto_increment,
  `seme_year_seme` varchar(6) NOT NULL,
  `seme_class` varchar(10) NOT NULL,
  `title` varchar(128) NOT NULL,
  `teacher_sn` int(10) NOT NULL,
  `student_sn` int(10) NOT NULL,
  `open_input` tinyint(1) NOT NULL,
  `open_read` tinyint(1) NOT NULL,
  `rep_classmates` tinyint(1) not null,
  `rep_sum` tinyint(1) not NULL,
  `rep_avg` tinyint(1) not NULL,
  `rep_rank` tinyint(1) not null,
  `last_edit_sn` int(10) unsigned default NULL,
  `last_edit_time` datetime NOT NULL,
  `locked` tinyint(1) not null,
  `update_sn` int(10) unsigned default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`)
) AUTO_INCREMENT=1 ;

#成績單中的考試科目等, 考試日期、科目名稱、列入加總、附註
CREATE TABLE  IF NOT EXISTS `class_report_test` (
  `sn` int(11) NOT NULL auto_increment,
  `report_sn` int(11)NOT NULL,
  `subject` varchar(32) NOT NULL,
  `test_date` date NOT NULL,
  `memo` varchar(64) NULL,
  `real_sum` tinyint(1) NOT NULL,
  `update_sn` int(10) unsigned default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`)
) AUTO_INCREMENT=1 ;

#學生成績, 流水號,學生流水號,科目流水號,成績
CREATE TABLE IF NOT EXISTS `class_report_score` (
  `sn` int(11) NOT NULL auto_increment,
  `test_sn` int(11)NOT NULL,
  `student_sn` int(10) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `update_sn` int(10) unsigned default NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`)
) AUTO_INCREMENT=1 ;

