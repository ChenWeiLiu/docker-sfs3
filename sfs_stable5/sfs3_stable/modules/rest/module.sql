
# 資料庫： 此為本模組所使用的資料表指令, 當安裝此模組時, SFS3 系統會一併執行這裡的 MySQL 指令,建立資料表.
#					 若本模組不需建立資料表, 則留空白即可.
#

CREATE TABLE rest_record (
  sn int(10) NOT NULL auto_increment,
  request_ip varchar(15) NOT NULL,
  request_method varchar(10) NOT NULL,
  request_result  int(1) NOT NULL,
  params text NOT NULL,
  request_time  timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (sn)
) ENGINE=MyISAM;

CREATE TABLE rest_manage (
  sn int(10) NOT NULL auto_increment,
  s_id varchar(30) NOT NULL,
  s_pwd varchar(30) NOT NULL,
  allow_ip text NOT NULL,
  method_post text NOT NULL,
  method_get text NOT NULL,
  PRIMARY KEY  (sn)
) ENGINE=MyISAM;