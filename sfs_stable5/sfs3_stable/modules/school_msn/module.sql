#$Id$
# 資料表格式： `msn_data`
#
# 請將您的資料表 CREATE TABLE 語法置於下。
# 若無，則請將本檔 module.sql 刪除。

#idnumber 唯一代碼11053110553020, 共14碼
#data_kind 0公開,1私人,2檔案分享(行政),3電子圖文
#folder 若為檔案分享, 此檔案歸為那一個資料夾

#訊息內容 
CREATE TABLE IF NOT EXISTS sc_msn_data (
  id int(5) not null auto_increment,
  idnumber varchar(14) not null,
  teach_id varchar(20) NOT NULL default '' ,
  to_id varchar(20) NOT NULL default '',
  data_kind tinyint(1) unsigned ,
  post_date datetime ,
  last_date tinyint(2) ,
  data text not null,
  ifread tinyint(1) UNSIGNED not null default '0',
  relay varchar(14) not null,
  folder varchar(14) not null,
  primary key (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#帳號, 不記 teacher_sn , 採記 teacher_id的方式
create table IF NOT EXISTS sc_msn_online (
	teach_id varchar(40) not null,
	name varchar(20) not null,
	from_ip varchar(40) not null,
	lasttime datetime not null,
	onlinetime datetime not null,
	ifonline tinyint(1) not null,
	state varchar(20) not null,
	hits int(5) not null,
	sound tinyint(1) not null default '1',
	sound_kind varchar(10) not null default 'sound4',
	is_upload tinyint(1) not null default '0',
	is_email tinyint(1) not null default '0',
	is_showpic tinyint(1) not null default '0',
	primary key (teach_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#檔案, 第一碼對應 msn_data 訊息內容, 表示為該內容的夾檔              
CREATE TABLE IF NOT EXISTS `sc_msn_file` (
  `id` int(5) not null auto_increment,
  `idnumber` varchar(14) NOT NULL,
  `filename` varchar(48)  NOT NULL,
  `filename_r` varchar(128)  NOT NULL,
  `file_download` int(5) NOT NULL,
  primary key (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#分享檔案使用的檔案分類
CREATE TABLE IF NOT EXISTS `sc_msn_folder` (
  `id` int(3) not null auto_increment,
  `idnumber` varchar(14) NOT NULL,
  `foldername` varchar(48)  NOT NULL,
  `open_upload` tinyint(1)  NOT NULL,
  primary key (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#電子文 電子看板
CREATE TABLE IF NOT EXISTS `sc_msn_board_pic` (
  id int(5) not null auto_increment,
  teach_id varchar(20) NOT NULL default '',
  stdate date NOT NULL,
  enddate date NOT NULL,
  delay_sec datetime not Null,
  file_text varchar(64) not null, 
  filename varchar(48) NOT NULL,
  show_off tinyint(1) not null,
  primary key (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SET NAMES 'utf8';
insert into sc_msn_folder (idnumber,foldername,open_upload) values ('140116121212','未分類','0');
insert into sc_msn_folder (idnumber,foldername,open_upload) values ('private','私人檔案','0');
SET NAMES 'latin1';