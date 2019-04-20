#
# 資料表格式： `health_disease_report`
#

CREATE TABLE health_disease_report (
  id int(10) unsigned NOT NULL auto_increment,
  student_sn int(10) unsigned NOT NULL default '0',
  dis_date date NOT NULL default '0000-00-00',
  dis_kind int(6) unsigned NOT NULL default '0',
  sym_str text NOT NULL default '',
  status varchar(2) NOT NULL default '',
  diag_date date NOT NULL default '0000-00-00',
  diag_hos varchar(50) NOT NULL default '',
  diag_name varchar(50) NOT NULL default '',
  chk_date date NOT NULL default '0000-00-00',
  chk_report varchar(50) NOT NULL default '',
  update_date timestamp,
  oth_chk text NOT NULL default '',
  oth_txt text NOT NULL default '',
  teacher_sn int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (student_sn,dis_date),
  KEY `id` (`id`)
) ENGINE=MyISAM;

#
# 資料表格式： `health_inflection_item`
#

CREATE TABLE health_inflection_item (
  iid int(10) unsigned NOT NULL default '0',
  name varchar(50) NOT NULL default '',
  memo text NOT NULL default '',
  enable varchar(1) NOT NULL default '1',
  PRIMARY KEY (iid)
) ENGINE=MyISAM;
INSERT INTO health_inflection_item VALUES (1,'類流感','急性呼吸道感染且具有下列症狀：1.突然發病有發燒（耳溫≧38℃）及呼吸道感染 2.且有肌肉酸痛或頭痛或極度厭倦感',1);
INSERT INTO health_inflection_item VALUES (2,'手足口病或&#30129;疹性咽峽炎','手足口病：口、手掌、腳掌及或膝蓋、臀部出現小水泡或紅疹；疹性咽峽炎：發燒且咽部出現小水泡或潰瘍',1);
INSERT INTO health_inflection_item VALUES (3,'腹瀉','每日腹瀉三次以上，且合併下列任何一項以上：1.嘔吐 2.發燒 3.黏液液狀或血絲 4.水瀉',1);
INSERT INTO health_inflection_item VALUES (4,'發燒','發燒（耳溫≧38℃）且未有前述疾病或症狀',1);
INSERT INTO health_inflection_item VALUES (5,'紅眼症','眼睛刺痛、灼熱、怕光、易流淚、霧視；眼結膜呈鮮紅色，有時會有結膜下出血；眼睛產生大量黏性分泌物；有時耳前淋巴結腫大、壓痛',1);
INSERT INTO health_inflection_item VALUES (99,'其他','前列項目外之特殊傳染病',1);

#
# 資料表格式： `health_inflection_record`
#

CREATE TABLE health_inflection_record (
  id int(10) unsigned NOT NULL auto_increment,
  student_sn int(10) unsigned NOT NULL default '0',
  iid int(10) unsigned NOT NULL default '0',
  dis_date date NOT NULL default '0000-00-00',
  weekday int(4) unsigned NOT NULL default '0',
  status varchar(2) NOT NULL default '',
  rmemo text NOT NULL default '',
  update_date timestamp,
  teacher_sn int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (student_sn,dis_date),
  KEY `id` (`id`)
) ENGINE=MyISAM;
