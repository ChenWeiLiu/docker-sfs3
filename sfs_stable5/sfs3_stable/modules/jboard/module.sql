CREATE TABLE jboard_kind (
  bk_id varchar(12) NOT NULL default '0',
  board_name varchar(20) NOT NULL default '',
  board_date date NOT NULL default '0000-00-00',
  board_k_id char(1) NOT NULL default '',
  board_last_date date NOT NULL default '0000-00-00',
  board_is_upload char(1) NOT NULL default '',
  board_is_public char(1) NOT NULL default '',
  board_admin varchar(100) NOT NULL default '',
  bk_order tinyint NOT NULL,
  PRIMARY KEY  (bk_id)
) Engine=MyISAM;


#
# 列出以下資料庫的數據： `board_kind`
#

INSERT INTO jboard_kind VALUES ('office1', '教務處', '2013-11-27', '0', '0000-00-00', '1', '1', '','1');
INSERT INTO jboard_kind VALUES ('office2', '學務處', '2013-11-27', '0', '0000-00-00', '1', '1', '','2');


CREATE TABLE jboard_check (
  pc_id int(11) NOT NULL auto_increment,
  pro_kind_id varchar(12) NOT NULL default '0',
  post_office tinyint(4) NOT NULL default '-1',
  teach_id varchar(20) NOT NULL default 'none',
  teach_title_id tinyint(4) NOT NULL default '-1',
  is_admin char(1) NOT NULL default '',
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY  (pc_id)
) Engine=MyISAM;



CREATE TABLE jboard_p (
  b_id bigint(20) unsigned NOT NULL auto_increment,
  bk_id varchar(12) NOT NULL default '0',
  b_open_date date NOT NULL default '0000-00-00',
  b_days smallint(6) NOT NULL default '0',
  b_unit varchar(20) NOT NULL default '',
  b_title varchar(60) NOT NULL default '',
  b_name varchar(20) NOT NULL default '',
  b_sub varchar(100) NOT NULL default '',
  b_con mediumtext NOT NULL,
  b_hints smallint(6) NOT NULL default '0',
  b_url varchar(150) NOT NULL default '',
  b_post_time datetime default NULL,
  b_own_id varchar(20) NOT NULL default '',
  b_is_intranet char(1) NOT NULL default '0',
  b_is_marquee char(1) not NULL DEFAULT '0',
  b_signs text NOT NULL default '',
  b_is_sign char(1) NOT NULL DEFAULT '0',
  teacher_sn int(11) NOT NULL default '0',
  b_sort smallint(6) NOT NULL default '100',
  PRIMARY KEY  (b_id)
) Engine=MyISAM;


create table jboard_files (
id int(5) not null auto_increment,
b_id int(5) not null,
org_filename varchar(100) not null,
new_filename varchar(60) not null,
filesize char(50) not null,
filetype char(50) not null,
content longblob null,
primary key (id)
) ENGINE=MyISAM;

create table jboard_images (
id int(5) not null auto_increment,
b_id int(5) not null,
filename varchar(25) not null,
filesize char(50) not null,
filetype char(50) not null,
content longblob null,
primary key (id)
) ENGINE=MyISAM;



INSERT INTO jboard_check VALUES (1, 'office1', 2, 'none', 0, '', 0);
INSERT INTO jboard_check VALUES (2, 'office2', 3, 'none', 0, '', 0);

