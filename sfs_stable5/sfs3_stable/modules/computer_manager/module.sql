
# ��Ʈw�G `comp_roomsite` ���c����
#

#�q���Ыǹq��IP�O��
CREATE TABLE comp_roomsite (
  net_edit int(3) not null COMMENT '�Ыǽs��',
  net_ip varchar(18) not null COMMENT '�q��ip',
  site_num int(2) not null COMMENT '�y��s��',
  iflock tinyint(1) not null COMMENT '�O�_��w',
  primary key (net_edit)
) ENGINE=MyISAM ;


