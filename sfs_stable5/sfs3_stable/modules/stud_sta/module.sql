# $Id: module.sql 8546 2015-10-01 01:38:43Z infodaes $
# phpMyAdmin MySQL-Dump
# version 2.3.3pl1
# http://www.phpmyadmin.net/ (download page)
#
# 主機: localhost
# 建立日期: May 22, 2003 at 11:36 AM
# 伺服器版本: 3.23.54
# PHP 版本: 4.2.3
# 資料庫: `sfs3`
# --------------------------------------------------------

#
# 資料表格式： `stud_sta`
#

CREATE TABLE stud_sta (
  prove_id bigint(20) NOT NULL auto_increment,
  stud_id varchar(20) NOT NULL default '',
  prove_year_seme varchar(6) NOT NULL default '',
  purpose varchar(30) NOT NULL default '',
  prove_date date NOT NULL default '0000-00-00',
  set_id varchar(20) NOT NULL default '',
  set_ip varchar(15) NOT NULL default '',
  prove_cancel char(2) NOT NULL default '0',
  PRIMARY KEY  (stud_id,prove_id),
  UNIQUE KEY prove_id (prove_id)
) ;

