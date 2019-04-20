CREATE TABLE sport_main (
  id int(5) NOT NULL auto_increment,
  title varchar(250) NOT NULL default '',
  year date NOT NULL default '0000-00-00',
  signtime datetime NOT NULL default '0000-00-00 00:00:00',
  stoptime datetime NOT NULL default '0000-00-00 00:00:00',
  work_start datetime NOT NULL default '0000-00-00 00:00:00',
  work_end datetime NOT NULL default '0000-00-00 00:00:00',
  memo text,
  PRIMARY KEY  (id)
) ENGINE=MyISAM;

CREATE TABLE sport_item (
  id int(10) unsigned NOT NULL auto_increment,
  mid int(5) unsigned NOT NULL default '0',
  item varchar(20) NOT NULL default '',
  enterclass varchar(20) NOT NULL default '',
  sportorder tinyint(3) unsigned NOT NULL default '0',
  sportkind tinyint(3) unsigned NOT NULL default '0',
  sunit varchar(20) NOT NULL default '',
  sord tinyint(2) NOT NULL default '0',
  playera tinyint(3) unsigned NOT NULL default '0',
  passera tinyint(3) unsigned NOT NULL default '0',
  kgp tinyint(3) NOT NULL default '0',
  kgm tinyint(3) NOT NULL default '0',
  place varchar(50) NOT NULL default '',
  kind tinyint(3) default '0',
  skind int(10) unsigned default '0',
  res tinyint(3) unsigned NOT NULL default '0',
  sporttime datetime default NULL,
  overtime datetime default NULL,
  imemo varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;

CREATE TABLE sport_res (
  id bigint(20) NOT NULL auto_increment,
  mid int(5) NOT NULL default '0',
  itemid int(10) NOT NULL default '0',
  kmaster tinyint(2) NOT NULL default '0',
  kgp tinyint(3) NOT NULL default '0',
  kend tinyint(2) NOT NULL default '0',
  stud_id varchar(20) NOT NULL default '',
  sportkind tinyint(3) NOT NULL default '0',
  cname varchar(20) NOT NULL default '',
  idclass varchar(10) NOT NULL default '',
  sportnum varchar(20) NOT NULL default '',
  num int(6) NOT NULL default '0',
  results varchar(12) NOT NULL default '',
  sportorder tinyint(3) NOT NULL default '0',
  memo varchar(120) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;

CREATE TABLE sport_teach (
  id int(10) NOT NULL auto_increment,
  tmid int(10) NOT NULL default '0',
  teacher_sn int(10) NOT NULL default '0',
  pa tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (tmid,teacher_sn),
  UNIQUE KEY id (id)
) ENGINE=MyISAM;


CREATE TABLE sport_var (
  id int(5) NOT NULL auto_increment,
  gp varchar(20) NOT NULL default '',
  kkey varchar(100) NOT NULL default '',
  na varchar(255) NOT NULL default '',
  UNIQUE KEY id (id),
  KEY gp (gp)
) ENGINE=MyISAM;

INSERT INTO sport_var VALUES (1, 'sportname', '1', '�@��');
INSERT INTO sport_var VALUES (2, 'sportname', '2', '�t��');
INSERT INTO sport_var VALUES (3, 'sportname', '3', '�`��');
INSERT INTO sport_var VALUES (4, 'sportname', '4', '�d�r��');
INSERT INTO sport_var VALUES (5, 'sportname', '5', '�Ѫk');
INSERT INTO sport_var VALUES (6, 'sportname', '6', '��Ū');
INSERT INTO sport_var VALUES (7, 'sportname', '7', '����');
INSERT INTO sport_var VALUES (8, 'sportname', '8', '����');
INSERT INTO sport_var VALUES (9, 'sportname', '9', '�βy�Y��');
INSERT INTO sport_var VALUES (10, 'sportname', '10', '���r');
INSERT INTO sport_var VALUES (11, 'sportname', '11', '�S�y�Y��');
INSERT INTO sport_var VALUES (12, 'sportname', '12', '60����');
INSERT INTO sport_var VALUES (13, 'sportname', '13', '80����');
INSERT INTO sport_var VALUES (14, 'sportname', '14', '100����');
INSERT INTO sport_var VALUES (15, 'sportname', '15', '200����');
INSERT INTO sport_var VALUES (16, 'sportname', '16', '�ǥͽլd');
INSERT INTO sport_var VALUES (17, 'sportname', '17', '�j�����O');
INSERT INTO sport_var VALUES (18, 'sportname', '18', '���]�y');
INSERT INTO sport_var VALUES (19, 'sportname', '19', '�K��');
INSERT INTO sport_var VALUES (20, 'sportname', '20', '�кj');
INSERT INTO sport_var VALUES (21, 'sportclass', '1a', '�@�k');
INSERT INTO sport_var VALUES (22, 'sportclass', '1b', '�@�k');
INSERT INTO sport_var VALUES (23, 'sportclass', '2a', '�G�k');
INSERT INTO sport_var VALUES (24, 'sportclass', '2b', '�G�k');
INSERT INTO sport_var VALUES (25, 'sportclass', '3a', '�T�k');
INSERT INTO sport_var VALUES (26, 'sportclass', '3b', '�T�k');
INSERT INTO sport_var VALUES (27, 'sportclass', '4a', '�|�k');
INSERT INTO sport_var VALUES (28, 'sportclass', '4b', '�|�k');
INSERT INTO sport_var VALUES (29, 'sportclass', '5a', '���k');
INSERT INTO sport_var VALUES (30, 'sportclass', '5b', '���k');
INSERT INTO sport_var VALUES (31, 'sportclass', '6a', '���k');
INSERT INTO sport_var VALUES (32, 'sportclass', '6b', '���k');
INSERT INTO sport_var VALUES (33, 'sportclass', '1c', '1�~��');
INSERT INTO sport_var VALUES (34, 'sportclass', '2c', '2�~��');
INSERT INTO sport_var VALUES (35, 'sportclass', '3c', '3�~��');
INSERT INTO sport_var VALUES (36, 'sportclass', '4c', '4�~��');
INSERT INTO sport_var VALUES (37, 'sportclass', '5c', '5�~��');
INSERT INTO sport_var VALUES (38, 'sportclass', '6c', '6�~��');
INSERT INTO sport_var VALUES (39, 'sportclass7', '7a', '��@�k');
INSERT INTO sport_var VALUES (40, 'sportclass7', '7b', '��@�k');
INSERT INTO sport_var VALUES (41, 'sportclass7', '8a', '��G�k');
INSERT INTO sport_var VALUES (42, 'sportclass7', '8b', '��G�k');
INSERT INTO sport_var VALUES (43, 'sportclass7', '9a', '��T�k');
INSERT INTO sport_var VALUES (44, 'sportclass7', '9b', '��T�k');
INSERT INTO sport_var VALUES (45, 'sportclass7', '7c', '1�~��');
INSERT INTO sport_var VALUES (46, 'sportclass7', '8c', '2�~��');
INSERT INTO sport_var VALUES (47, 'sportclass7', '9c', '3�~��');
