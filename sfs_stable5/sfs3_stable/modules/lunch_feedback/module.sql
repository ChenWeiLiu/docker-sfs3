#
# 資料表格式： `lunch_feedback`
#

CREATE TABLE `lunch_feedback` (
  `record_id` int(11) NOT NULL auto_increment,
  `class_id` varchar(5) NOT NULL default '',
  `pdate` date NOT NULL,
  `item` varchar(30) NOT NULL default '',
  `quantity` varchar(20) default NULL,
  `taste` varchar(20) default NULL,
  `hygiene` varchar(20) default NULL,
  `memo` varchar(100) default NULL,
  `teacher_sn` int(11) NOT NULL,
  `update_date` date NOT NULL,
  PRIMARY KEY  (`record_id`)
) ;
