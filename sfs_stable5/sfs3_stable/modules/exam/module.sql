
#
# 資料表格式： `exam`
#

CREATE TABLE exam (
  exam_id int(11) NOT NULL auto_increment,
  exam_name varchar(60) NOT NULL default '',
  exam_memo text NOT NULL,
  exam_isopen char(1) NOT NULL default '',
  exam_isupload char(1) NOT NULL default '',
  exam_source_isopen char(1) NOT NULL default '',
  e_kind_id int(4) NOT NULL default '0',
  teach_id varchar(20) NOT NULL default '',
  teach_name varchar(20) NOT NULL default '',
  PRIMARY KEY  (exam_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# 資料表格式： `exam_kind`
#

CREATE TABLE exam_kind (
  e_kind_id int(11) NOT NULL auto_increment,
  e_kind_memo text NOT NULL,
  e_kind_open char(1) NOT NULL default '',
  e_upload_ok char(1) NOT NULL default '',
  teach_id varchar(20) NOT NULL default '',
  teach_name varchar(20) NOT NULL default '',
  class_id varchar(10) NOT NULL default '',
  PRIMARY KEY  (e_kind_id,class_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# 資料表格式： `exam_stud`
#

CREATE TABLE exam_stud (
  exam_id int(11) NOT NULL default '0',
  stud_id varchar(20) NOT NULL default '',
  stud_name varchar(20) NOT NULL default '',
  stud_num tinyint(4) NOT NULL default '0',
  memo text NOT NULL,
  f_name varchar(120) NOT NULL default '',
  f_size float(10,2) NOT NULL default '0.00',
  cool char(1) NOT NULL default '',
  tea_comment varchar(30) NOT NULL default '',
  tea_grade int(11) default NULL,
  f_ctime datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (exam_id,stud_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# 資料表格式： `exam_stud_data`
#

CREATE TABLE exam_stud_data (
  stud_id varchar(20) NOT NULL default '',
  stud_pass varchar(10) NOT NULL default '',
  stud_num tinyint(4) NOT NULL default '0',
  stud_sit_num varchar(5) NOT NULL default '0',
  stud_memo varchar(80) NOT NULL default '',
  stud_c_time tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (stud_id)
) ENGINE=MyISAM;
