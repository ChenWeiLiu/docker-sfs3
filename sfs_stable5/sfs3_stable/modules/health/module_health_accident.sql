# $Id: module_health_accident.sql 8539 2015-09-23 03:26:41Z chiming $
CREATE TABLE if not exists  health_accident_place (
  id int(6) unsigned NOT NULL default '1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_place VALUES ( 1,'操場',1);
INSERT INTO health_accident_place VALUES ( 2,'遊戲運動器材',1);
INSERT INTO health_accident_place VALUES ( 3,'普通教室',1);
INSERT INTO health_accident_place VALUES ( 4,'專科教室',1);
INSERT INTO health_accident_place VALUES ( 5,'走廊',1);
INSERT INTO health_accident_place VALUES ( 6,'樓梯',1);
INSERT INTO health_accident_place VALUES ( 7,'地下室',1);
INSERT INTO health_accident_place VALUES ( 8,'體育館活動中心',1);
INSERT INTO health_accident_place VALUES ( 9,'廁所',1);
INSERT INTO health_accident_place VALUES ( 10,'校外',1);
INSERT INTO health_accident_place VALUES ( 999,'其他',1);

#
# 資料表格式： `health_accident_reason`
#

CREATE TABLE if not exists health_accident_reason (
  id int(6) unsigned NOT NULL default '1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_reason VALUES ( 1,'下課遊戲',1);
INSERT INTO health_accident_reason VALUES ( 2,'上下課途中',1);
INSERT INTO health_accident_reason VALUES ( 3,'升旗',1);
INSERT INTO health_accident_reason VALUES ( 4,'打破玻璃',1);
INSERT INTO health_accident_reason VALUES ( 5,'打掃',1);
INSERT INTO health_accident_reason VALUES ( 6,'上下樓梯',1);
INSERT INTO health_accident_reason VALUES ( 7,'藝能課',1);
INSERT INTO health_accident_reason VALUES ( 8,'體育課',1);
INSERT INTO health_accident_reason VALUES ( 999,'其他',1);

#
# 資料表格式： `health_accident_part`
#

CREATE TABLE  if not exists health_accident_part (
  id int(6) unsigned NOT NULL default '1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_part VALUES ( 1,'頭',1);
INSERT INTO health_accident_part VALUES ( 2,'頸',1);
INSERT INTO health_accident_part VALUES ( 3,'肩',1);
INSERT INTO health_accident_part VALUES ( 4,'胸',1);
INSERT INTO health_accident_part VALUES ( 5,'腹',1);
INSERT INTO health_accident_part VALUES ( 6,'背',1);
INSERT INTO health_accident_part VALUES ( 7,'眼',1);
INSERT INTO health_accident_part VALUES ( 8,'顏面',1);
INSERT INTO health_accident_part VALUES ( 9,'口腔',1);
INSERT INTO health_accident_part VALUES ( 10,'耳鼻喉',1);
INSERT INTO health_accident_part VALUES ( 11,'上肢',1);
INSERT INTO health_accident_part VALUES ( 12,'腰',1);
INSERT INTO health_accident_part VALUES ( 13,'下肢',1);
INSERT INTO health_accident_part VALUES ( 14,'臀部',1);
INSERT INTO health_accident_part VALUES ( 15,'會陰部',1);

#
# 資料表格式： `health_accident_status`
#

CREATE TABLE if not exists  health_accident_status (
  id int(6) unsigned NOT NULL default'1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_status VALUES ( 1,'擦傷',1);
INSERT INTO health_accident_status VALUES ( 2,'裂割刺傷',1);
INSERT INTO health_accident_status VALUES ( 3,'夾壓傷',1);
INSERT INTO health_accident_status VALUES ( 4,'挫撞傷',1);
INSERT INTO health_accident_status VALUES ( 5,'扭傷',1);
INSERT INTO health_accident_status VALUES ( 6,'灼燙傷',1);
INSERT INTO health_accident_status VALUES ( 7,'叮咬傷',1);
INSERT INTO health_accident_status VALUES ( 8,'骨折',1);
INSERT INTO health_accident_status VALUES ( 9,'舊傷',1);
INSERT INTO health_accident_status VALUES ( 10,'外科其他',1);
INSERT INTO health_accident_status VALUES ( 11,'發燒',1);
INSERT INTO health_accident_status VALUES ( 12,'暈眩',1);
INSERT INTO health_accident_status VALUES ( 13,'噁心嘔吐',1);
INSERT INTO health_accident_status VALUES ( 14,'頭痛',1);
INSERT INTO health_accident_status VALUES ( 15,'牙痛',1);
INSERT INTO health_accident_status VALUES ( 16,'胃痛',1);
INSERT INTO health_accident_status VALUES ( 17,'腹痛',1);
INSERT INTO health_accident_status VALUES ( 18,'腹瀉',1);
INSERT INTO health_accident_status VALUES ( 19,'經痛',1);
INSERT INTO health_accident_status VALUES ( 20,'氣喘',1);
INSERT INTO health_accident_status VALUES ( 21,'流鼻血',1);
INSERT INTO health_accident_status VALUES ( 22,'疹癢',1);
INSERT INTO health_accident_status VALUES ( 23,'眼疾',1);
INSERT INTO health_accident_status VALUES ( 24,'內科其他',1);

#
# 資料表格式： `health_accident_attend`
#

CREATE TABLE if not exists  health_accident_attend (
  id int(6) unsigned NOT NULL default'1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_attend VALUES ( 1,'傷口處理',1);
INSERT INTO health_accident_attend VALUES ( 2,'冰敷',1);
INSERT INTO health_accident_attend VALUES ( 3,'熱敷',1);
INSERT INTO health_accident_attend VALUES ( 4,'休息觀察',1);
INSERT INTO health_accident_attend VALUES ( 5,'通知家長',1);
INSERT INTO health_accident_attend VALUES ( 6,'家長帶回',1);
INSERT INTO health_accident_attend VALUES ( 7,'校方送醫',1);
INSERT INTO health_accident_attend VALUES ( 8,'衛生教育',1);
INSERT INTO health_accident_attend VALUES ( 999,'其他處理',1);

#
# 資料表格式： `health_accident_record`
#

CREATE TABLE if not exists  health_accident_record (
	id int(10) unsigned NOT NULL auto_increment,
	`year` smallint(5) unsigned NOT NULL default '0',
	`semester` enum('0','1','2') NOT NULL default '0',
	student_sn int(10) unsigned NOT NULL default '0',
	sign_time datetime NOT NULL default '0000-00-00 00:00:00',
	obs_min int(6) unsigned NOT NULL default '0',
	`temp` decimal(3,1) NOT NULL default '0.0',
	place_id int(6) unsigned NOT NULL default '0',
	reason_id int(6) unsigned NOT NULL default '0',
	`memo` text NOT NULL default '',
	update_date timestamp,
	teacher_sn int(11) NOT NULL default '0',
	PRIMARY KEY (id)
) ENGINE=MyISAM;

#
# 資料表格式： `health_accident_part_record`
#

CREATE TABLE if not exists  health_accident_part_record (
	`pid` int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	part_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (`pid`)
) ENGINE=MyISAM;

#
# 資料表格式： `health_accident_status_record`
#

CREATE TABLE if not exists  health_accident_status_record (
	`sid` int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	status_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (`sid`)
) ENGINE=MyISAM;

#
# 資料表格式： `health_accident_attend_record`
#

CREATE TABLE if not exists  health_accident_attend_record (
	aid int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	attend_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (aid)
) ENGINE=MyISAM;
 
