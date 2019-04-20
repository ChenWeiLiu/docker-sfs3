
# 資料庫： `comp_roomsite` 結構說明
#

#電腦教室電腦IP記錄
CREATE TABLE comp_roomsite (
  net_edit int(3) not null COMMENT '教室編號',
  net_ip varchar(18) not null COMMENT '電腦ip',
  site_num int(2) not null COMMENT '座位編號',
  iflock tinyint(1) not null COMMENT '是否鎖定',
  primary key (net_edit)
) ENGINE=MyISAM ;


