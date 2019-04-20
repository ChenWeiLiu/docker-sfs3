#
# Table structure for table 'magazine'
#記錄各期

CREATE TABLE magazine (
  id int(11) NOT NULL auto_increment,
  num int(11) NOT NULL default '0',
  publish_date date NOT NULL default '0000-00-00',
  publish varchar(250) NOT NULL default '',
  setpasswd varchar(10) NOT NULL default '',
  admin varchar(250) NOT NULL default '',
  is_fin tinyint(4) NOT NULL default '0',
  ed_begin date default NULL,
  ed_end date default NULL,
  book_path varchar(10) NOT NULL default '',
  themes varchar(60) NOT NULL default '',
  PRIMARY KEY  (id)
) ;






#
# Table structure for table 'magazine_chap'
#記錄各期中各有那些章節

CREATE TABLE magazine_chap (
  id bigint(20) NOT NULL auto_increment,
  book_num int(11) NOT NULL default '0',
  chap_name varchar(20) NOT NULL default '',
  cmode tinyint(4) default NULL,
  chap_sort tinyint(4) default NULL,
  chap_path varchar(10) NOT NULL default '',
  small_pic tinyint(4) NOT NULL default '0',
  new_win tinyint(4) NOT NULL default '0',
  stud_upload tinyint(4) NOT NULL default '0',
  include_mode tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id)
) ;





#
# Table structure for table 'magazine_paper'
#每一篇文章
CREATE TABLE magazine_paper (
  id bigint(20) NOT NULL auto_increment,
  chap_num bigint(20) NOT NULL default '0',
  tmode tinyint(4) NOT NULL default '0',
  title varchar(80) NOT NULL default '',
  author varchar(24) NOT NULL default '',
  type_name varchar(12) default NULL,
  teacher varchar(12) default NULL,
  parent varchar(24) default NULL,
  doc text,
  judge varchar(250) default NULL,
  classnum varchar(10) default NULL,
  class_name varchar(10) default NULL,
  pwd varchar(12) NOT NULL default '0',
  pic_name varchar(80) default NULL,
  isDel tinyint(4) NOT NULL default '0',
  editId varchar(20) default NULL,
  editDate datetime default NULL,
  PRIMARY KEY  (id)
) ;

#
#tmode(類型) 0->"文章",1->"圖檔",2->"班級訊息",3->"網頁"
#