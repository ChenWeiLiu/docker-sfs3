#
# 資料表格式： `book`
#

CREATE TABLE book (
  bookch1_id char(3) NOT NULL default '',
  book_id varchar(8) NOT NULL default '',
  book_name varchar(40) default NULL,
  book_num int(11) default NULL,
  book_author varchar(20) default NULL,
  book_maker varchar(20) default NULL,
  book_myear varchar(8) default NULL,
  book_bind varchar(6) default NULL,
  book_dollar varchar(8) default NULL,
  book_price int(11) default NULL,
  book_gid varchar(10) default NULL,
  book_content varchar(40) default NULL,
  book_isborrow tinyint(4) default NULL,
  book_isbn varchar(10) default NULL,
  book_isout tinyint(4) NOT NULL default '0',
  book_buy_date datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY pk_book (book_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# 資料表格式： `borrow`
#

CREATE TABLE borrow (
  b_num int(11) NOT NULL auto_increment,
  stud_id varchar(20) NOT NULL default '',
  bookch1_id char(3) NOT NULL default '',
  book_id varchar(8) NOT NULL default '',
  out_date datetime NOT NULL default '0000-00-00 00:00:00',
  in_date datetime NOT NULL default '0000-00-00 00:00:00',
  curr_class_num int(10) unsigned NOT NULL default '0',
  UNIQUE KEY pk_borrow (b_num)
) ENGINE=MyISAM;

#
# 資料表格式： `bookch1`
#

CREATE TABLE bookch1 (
  bookch1_id char(3) NOT NULL default '',
  bookch1_name char(20) default NULL,
  bookch2_name char(20) default NULL,
  tolnum int(11) NOT NULL default '0',
  UNIQUE KEY pk_bookch1 (bookch1_id)
) ENGINE=MyISAM;

#
# 列出以下資料庫的數據： `bookch1`
#

INSERT INTO bookch1 VALUES ('600', '中國史地類', '中國史地類', 0);
INSERT INTO bookch1 VALUES ('700', '世界史地類', '世界史地類', 0);
INSERT INTO bookch1 VALUES ('300', '自然科學類', '自然科學類', 0);
INSERT INTO bookch1 VALUES ('200', '宗教類', '宗教類', 0);
INSERT INTO bookch1 VALUES ('500', '社會科學類', ' 社會科學類', 0);
INSERT INTO bookch1 VALUES ('900', '美術類', '美術類', 0);
INSERT INTO bookch1 VALUES ('100', '哲學類', '哲學類', 0);
INSERT INTO bookch1 VALUES ('800', '語文類', '語文類', 0);
INSERT INTO bookch1 VALUES ('400', '應用科學類', '應用科學類', 0);
INSERT INTO bookch1 VALUES ('000', '總類', '總類', 0);
