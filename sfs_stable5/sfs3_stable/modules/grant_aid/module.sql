
# 資料表格式： `grant_aid`
#
# 請將您的資料表 CREATE TABLE 語法置於下。
# 若無，則請將本檔 module.sql 刪除。

CREATE TABLE `grant_aid` (
  `type` char(20) default '',
  `year_seme` varchar(6) default '',
  `student_sn` int(10) unsigned default '0',
  `class_num` varchar(6) default '',
  `dollar` int(10) unsigned default '0',
  `sn` int(10) unsigned auto_increment,
  PRIMARY KEY  (`sn`),
  KEY `year_seme` (`year_seme`)
);

