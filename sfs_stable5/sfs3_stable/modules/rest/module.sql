
# ��Ʈw�G �������ҲթҨϥΪ���ƪ���O, ��w�˦��Ҳծ�, SFS3 �t�η|�@�ְ���o�̪� MySQL ���O,�إ߸�ƪ�.
#					 �Y���Ҳդ��ݫإ߸�ƪ�, �h�d�ťէY�i.
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