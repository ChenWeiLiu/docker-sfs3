#
# 資料表格式： `cal_elps`
#

CREATE TABLE cal_elps (
id int(10) NOT NULL auto_increment,
syear varchar(6) NOT NULL default '',
week tinyint(3) NOT NULL default '0',
unit varchar(20) NOT NULL default '',
user varchar(20) NOT NULL default '',
day datetime NOT NULL default '0000-00-00 00:00:00',
event text NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM;

#
# 資料表格式： `cal_elps_set`
#

CREATE TABLE cal_elps_set (
syear varchar(6) NOT NULL default '',
sday date NOT NULL default '0000-00-00',
weeks tinyint(3) NOT NULL default '0',
unit varchar(255) NOT NULL default '',
PRIMARY KEY (syear),
UNIQUE KEY syear (syear)
) ENGINE=MyISAM;


