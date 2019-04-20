
# 資料庫： 此為本模組所使用的資料表指令, 當安裝此模組時, SFS3 系統會一併執行這裡的 MySQL 指令,建立資料表.
#					 若本模組不需建立資料表, 則留空白即可.
#
#
#	server_ip		, LDAP server 的連線 ip 或 dn
# ldap_port 	, LDAP 連接 port
# bind_dn			, 帳號 bind 時 @ 後面要加的 DN  , 如  smallduh@fnjh.tc.edu.tw 
#	base_dn			, 讀取帳號資訊要使用的 DN , 如: OU=Users, DC=fnjh, DC=tcc, DC=edu, DC=tw 
# filter			, ldap_search 篩選名單條件
# attributes	, ldap_search 篩選條件中的列示項目
# nologin			, 禁止登入學務系統的帳號列表
# chpass_url	, 前往變更密碼的超連結


CREATE TABLE IF NOT EXISTS `ldap` (
  `sn` int(4) NOT NULL auto_increment,
	`enable` tinyint(1) not null,
	`server_ip` varchar(64) not null,
	`server_port` int(5) not null,
  `bind_dn` text NOT NULL,
	`base_dn` text,
	`filter` text,
	`attributes` text,
  `nologin` text,
  `chpass_url` text,
  PRIMARY KEY  (`sn`)
) AUTO_INCREMENT=1 ;

insert into `ldap` (enable,server_ip,server_port,bind_dn,base_dn,filter,attributes,nologin,chpass_url) values ('0','','389','???.???.edu.tw','DC=???, DC=???, DC=edu, DC=tw','','','','http://');




