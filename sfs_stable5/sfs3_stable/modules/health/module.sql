# $Id: module.sql 8539 2015-09-23 03:26:41Z chiming $

#
# ¸ê®Æªí®æ¦¡¡G `BMI`
#

CREATE TABLE BMI (
  `year` int(2) unsigned NOT NULL default '0',
  `sex` int(1) unsigned NOT NULL default '0',
  `range` int(1) unsigned NOT NULL default '0',
  `value` float NOT NULL default '0',
  PRIMARY KEY (`year`,`sex`,`range`)
) ENGINE=MyISAM;

INSERT INTO BMI VALUES ( 2,1,1,15.2);
INSERT INTO BMI VALUES ( 2,1,2,17.7);
INSERT INTO BMI VALUES ( 2,1,3,19.0);
INSERT INTO BMI VALUES ( 3,1,1,14.8);
INSERT INTO BMI VALUES ( 3,1,2,17.7);
INSERT INTO BMI VALUES ( 3,1,3,19.1);
INSERT INTO BMI VALUES ( 4,1,1,14.4);
INSERT INTO BMI VALUES ( 4,1,2,17.7);
INSERT INTO BMI VALUES ( 4,1,3,19.3);
INSERT INTO BMI VALUES ( 5,1,1,14.0);
INSERT INTO BMI VALUES ( 5,1,2,17.7);
INSERT INTO BMI VALUES ( 5,1,3,19.4);
INSERT INTO BMI VALUES ( 6,1,1,13.9);
INSERT INTO BMI VALUES ( 6,1,2,17.9);
INSERT INTO BMI VALUES ( 6,1,3,19.7);
INSERT INTO BMI VALUES ( 7,1,1,14.7);
INSERT INTO BMI VALUES ( 7,1,2,18.6);
INSERT INTO BMI VALUES ( 7,1,3,21.2);
INSERT INTO BMI VALUES ( 8,1,1,15.0);
INSERT INTO BMI VALUES ( 8,1,2,19.3);
INSERT INTO BMI VALUES ( 8,1,3,22.0);
INSERT INTO BMI VALUES ( 9,1,1,15.2);
INSERT INTO BMI VALUES ( 9,1,2,19.7);
INSERT INTO BMI VALUES ( 9,1,3,22.5);
INSERT INTO BMI VALUES (10,1,1,15.4);
INSERT INTO BMI VALUES (10,1,2,20.3);
INSERT INTO BMI VALUES (10,1,3,22.9);
INSERT INTO BMI VALUES (11,1,1,15.8);
INSERT INTO BMI VALUES (11,1,2,21.0);
INSERT INTO BMI VALUES (11,1,3,23.5);
INSERT INTO BMI VALUES (12,1,1,16.4);
INSERT INTO BMI VALUES (12,1,2,21.5);
INSERT INTO BMI VALUES (12,1,3,24.2);
INSERT INTO BMI VALUES (13,1,1,17.0);
INSERT INTO BMI VALUES (13,1,2,22.2);
INSERT INTO BMI VALUES (13,1,3,24.8);
INSERT INTO BMI VALUES (14,1,1,17.6);
INSERT INTO BMI VALUES (14,1,2,22.7);
INSERT INTO BMI VALUES (14,1,3,25.2);
INSERT INTO BMI VALUES (15,1,1,18.2);
INSERT INTO BMI VALUES (15,1,2,23.1);
INSERT INTO BMI VALUES (15,1,3,25.5);
INSERT INTO BMI VALUES (16,1,1,18.6);
INSERT INTO BMI VALUES (16,1,2,23.4);
INSERT INTO BMI VALUES (16,1,3,25.6);
INSERT INTO BMI VALUES (17,1,1,19.0);
INSERT INTO BMI VALUES (17,1,2,23.6);
INSERT INTO BMI VALUES (17,1,3,25.6);
INSERT INTO BMI VALUES (18,1,1,19.2);
INSERT INTO BMI VALUES (18,1,2,23.7);
INSERT INTO BMI VALUES (18,1,3,25.6);
INSERT INTO BMI VALUES ( 2,2,1,14.9);
INSERT INTO BMI VALUES ( 2,2,2,17.3);
INSERT INTO BMI VALUES ( 2,2,3,18.3);
INSERT INTO BMI VALUES ( 3,2,1,14.5);
INSERT INTO BMI VALUES ( 3,2,2,17.2);
INSERT INTO BMI VALUES ( 3,2,3,18.5);
INSERT INTO BMI VALUES ( 4,2,1,14.2);
INSERT INTO BMI VALUES ( 4,2,2,17.1);
INSERT INTO BMI VALUES ( 4,2,3,18.6);
INSERT INTO BMI VALUES ( 5,2,1,13.9);
INSERT INTO BMI VALUES ( 5,2,2,17.1);
INSERT INTO BMI VALUES ( 5,2,3,18.9);
INSERT INTO BMI VALUES ( 6,2,1,13.6);
INSERT INTO BMI VALUES ( 6,2,2,17.2);
INSERT INTO BMI VALUES ( 6,2,3,19.1);
INSERT INTO BMI VALUES ( 7,2,1,14.4);
INSERT INTO BMI VALUES ( 7,2,2,18.0);
INSERT INTO BMI VALUES ( 7,2,3,20.3);
INSERT INTO BMI VALUES ( 8,2,1,14.6);
INSERT INTO BMI VALUES ( 8,2,2,18.8);
INSERT INTO BMI VALUES ( 8,2,3,21.0);
INSERT INTO BMI VALUES ( 9,2,1,14.9);
INSERT INTO BMI VALUES ( 9,2,2,19.3);
INSERT INTO BMI VALUES ( 9,2,3,21.6);
INSERT INTO BMI VALUES (10,2,1,15.2);
INSERT INTO BMI VALUES (10,2,2,20.1);
INSERT INTO BMI VALUES (10,2,3,22.3);
INSERT INTO BMI VALUES (11,2,1,15.8);
INSERT INTO BMI VALUES (11,2,2,20.9);
INSERT INTO BMI VALUES (11,2,3,23.1);
INSERT INTO BMI VALUES (12,2,1,16.4);
INSERT INTO BMI VALUES (12,2,2,21.6);
INSERT INTO BMI VALUES (12,2,3,23.9);
INSERT INTO BMI VALUES (13,2,1,17.0);
INSERT INTO BMI VALUES (13,2,2,22.2);
INSERT INTO BMI VALUES (13,2,3,24.6);
INSERT INTO BMI VALUES (14,2,1,17.6);
INSERT INTO BMI VALUES (14,2,2,22.7);
INSERT INTO BMI VALUES (14,2,3,25.1);
INSERT INTO BMI VALUES (15,2,1,18.0);
INSERT INTO BMI VALUES (15,2,2,22.7);
INSERT INTO BMI VALUES (15,2,3,25.3);
INSERT INTO BMI VALUES (16,2,1,18.2);
INSERT INTO BMI VALUES (16,2,2,22.7);
INSERT INTO BMI VALUES (16,2,3,25.3);
INSERT INTO BMI VALUES (17,2,1,18.3);
INSERT INTO BMI VALUES (17,2,2,22.7);
INSERT INTO BMI VALUES (17,2,3,25.3);
INSERT INTO BMI VALUES (18,2,1,18.3);
INSERT INTO BMI VALUES (18,2,2,22.7);
INSERT INTO BMI VALUES (18,2,3,25.3);

#
# ¸ê®Æªí®æ¦¡¡G `GHD`
#

CREATE TABLE GHD (
  `year` int(2) unsigned NOT NULL default '0',
  `sex` int(1) unsigned NOT NULL default '0',
  `value` float NOT NULL default '0',
  PRIMARY KEY (`year`,`sex`)
) ENGINE=MyISAM;

INSERT INTO GHD VALUES (5,1,103);
INSERT INTO GHD VALUES (6,1,106.7);
INSERT INTO GHD VALUES (7,1,110.5);
INSERT INTO GHD VALUES (8,1,116.4);
INSERT INTO GHD VALUES (9,1,120.3);
INSERT INTO GHD VALUES (10,1,125.5);
INSERT INTO GHD VALUES (11,1,129.6);
INSERT INTO GHD VALUES (12,1,134.4);
INSERT INTO GHD VALUES (13,1,140.9);
INSERT INTO GHD VALUES (14,1,148.7);
INSERT INTO GHD VALUES (15,1,154.6);
INSERT INTO GHD VALUES (16,1,157.9);
INSERT INTO GHD VALUES (17,1,159.1);
INSERT INTO GHD VALUES (18,1,159.8);
INSERT INTO GHD VALUES (5,2,102.2);
INSERT INTO GHD VALUES (6,2,106.3);
INSERT INTO GHD VALUES (7,2,110.4);
INSERT INTO GHD VALUES (8,2,115.6);
INSERT INTO GHD VALUES (9,2,119.2);
INSERT INTO GHD VALUES (10,2,124.9);
INSERT INTO GHD VALUES (11,2,131.3);
INSERT INTO GHD VALUES (12,2,138.6);
INSERT INTO GHD VALUES (13,2,143.5);
INSERT INTO GHD VALUES (14,2,146.2);
INSERT INTO GHD VALUES (15,2,147.2);
INSERT INTO GHD VALUES (16,2,148.2);
INSERT INTO GHD VALUES (17,2,148.7);
INSERT INTO GHD VALUES (18,2,148.9);

#
# ¸ê®Æªí®æ¦¡¡G `health_WH`
#

CREATE TABLE health_WH (
  `year` smallint(5) unsigned NOT NULL default '0',
  `semester` enum('1','2') NOT NULL default '1',
  `student_sn` int(10) unsigned NOT NULL default '0',
  `weight` decimal(4,1) NOT NULL default '0.0',
  `height` decimal(4,1) NOT NULL default '0.0',
  `measure_date` date NOT NULL default '0000-00-00',
  `diag_id` tinyint(3) NOT NULL default '0',
  `diag_place` varchar(40) NOT NULL default '',
  `diag` varchar(40) NOT NULL default '',
  `update_date` timestamp,
  `teacher_sn` int(11) NOT NULL default '0',
  PRIMARY KEY (`year`,`semester`,`student_sn`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_sight`
#

CREATE TABLE health_sight (
  `year` smallint(5) unsigned NOT NULL default '0',
  `semester` enum('1','2') NOT NULL default '1',
  `student_sn` int(10) unsigned NOT NULL default '0',
  `side` char(1) NOT NULL default '',
  `sight_o` varchar(3) NOT NULL default '',
  `sight_r` varchar(3) NOT NULL default '',
  `My` char(1) NOT NULL default '',
  `Hy` char(1) NOT NULL default '',
  `Ast` char(1) NOT NULL default '',
  `Amb` char(1) NOT NULL default '',
  `other` char(1) NOT NULL default '',
  `measure_date` date NOT NULL default '0000-00-00',
  `manage_id` char(1) NOT NULL default '',
  `manage` text NOT NULL default '',
  `diag_id` char(1) NOT NULL default '',
  `diag` text NOT NULL default '',
  `update_date` timestamp,
  `teacher_sn` int(11) NOT NULL default '0',
  PRIMARY KEY (`year`,`semester`,`student_sn`,`side`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_sight_ntu`
#

CREATE TABLE health_sight_ntu (
  `student_sn` int(10) unsigned NOT NULL default '0',
  `ntu` varchar(1) NOT NULL default '', 
  `update_date` timestamp,
  `teacher_sn` int(11) NOT NULL default '0',
  PRIMARY KEY (student_sn)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_worm`
#

CREATE TABLE health_worm (
  `year` smallint(5) unsigned NOT NULL default '0',
  `semester` enum('1','2') NOT NULL default '1',
  `student_sn` int(10) unsigned NOT NULL default '0',
  `no` int(1) unsigned NOT NULL default '1',
  `worm` varchar(1) NOT NULL default '',
  `med` varchar(1) NOT NULL default '',
  `measure_date` date NOT NULL default '0000-00-00',
  `update_date` timestamp,
  `teacher_sn` int(11) NOT NULL default '0',
  PRIMARY KEY (`year`,`semester`,`student_sn`,`no`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_uri`
#

CREATE TABLE health_uri (
  `year` smallint(5) unsigned NOT NULL default '0',
  `semester` enum('1','2') NOT NULL default '1',
  `student_sn` int(10) unsigned NOT NULL default '0',
  `no` int(1) unsigned NOT NULL default '1',
  `pro` varchar(1) NOT NULL default '',
  `bld` varchar(1) NOT NULL default '',
  `glu` varchar(1) NOT NULL default '',
  `ph` float NOT NULL default '0',
  `memo` text NOT NULL default '',
  `measure_date` date NOT NULL default '0000-00-00',
  `update_date` timestamp,
  `teacher_sn` int(11) NOT NULL default '0',
  PRIMARY KEY (`year`,`semester`,`student_sn`,`no`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_disease`
#

CREATE TABLE health_disease (
  `student_sn` int(10) unsigned NOT NULL default '0',
  `di_id` char(2) NOT NULL default '',
  `enter_date` date NOT NULL default '0000-00-00',
  `state` text NOT NULL default '',
  `treate` text NOT NULL default '',
  `update_date` timestamp,
  `teacher_sn` int(11) NOT NULL default '0',
  PRIMARY KEY (`student_sn`,`di_id`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_diseaseserious`
#

CREATE TABLE health_diseaseserious (
  `student_sn` int(10) unsigned NOT NULL default '0',
  `di_id` char(2) NOT NULL default '',
  `enter_date` date NOT NULL default '0000-00-00',
  `update_date` timestamp,
  `teacher_sn` int(11) NOT NULL default '0',
  PRIMARY KEY (`student_sn`,`di_id`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_bodymind`
#

CREATE TABLE health_bodymind (
  student_sn int(10) unsigned NOT NULL default '0',
  bm_id char(2) NOT NULL default '',
  bm_level char(1) NOT NULL default '',
  enter_date date NOT NULL default '0000-00-00',
  update_date timestamp,
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY (student_sn)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_inherit`
#

CREATE TABLE health_inherit (
  student_sn int(10) unsigned NOT NULL default '0',
  folk_id char(2) NOT NULL default '',
  di_id char(2) NOT NULL default '',
  enter_date date NOT NULL default '0000-00-00',
  update_date timestamp,
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY (student_sn,folk_id)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_checks_item`
#

CREATE TABLE health_checks_item (
	subject varchar(50) NOT NULL default'',
	`no` int(5) NOT NULL default '0',
	item varchar(50) NOT NULL default '',
	ps int(4) NOT NULL default '0',
	PRIMARY KEY (subject,`no`)
) ENGINE=MyISAM;
INSERT INTO health_checks_item VALUES ( 'Oph',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Oph',1,'µø¤O¤£¨}',0);
INSERT INTO health_checks_item VALUES ( 'Oph',2,'¿ë¦â¤O²§±`',0);
INSERT INTO health_checks_item VALUES ( 'Oph',3,'±×µø',9);
INSERT INTO health_checks_item VALUES ( 'Oph',4,'·û¤ò­Ë´¡',0);
INSERT INTO health_checks_item VALUES ( 'Oph',5,'²´²y¾_Å¸',0);
INSERT INTO health_checks_item VALUES ( 'Oph',6,'²´Â¥¤U««',0);
INSERT INTO health_checks_item VALUES ( 'Ent',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Ent',1,'Å¥¤O²§±`',9);
INSERT INTO health_checks_item VALUES ( 'Ent',2,'ºÃ¦ü¤¤¦Õª¢',0);
INSERT INTO health_checks_item VALUES ( 'Ent',3,'¦Õ¹D·î«¬',0);
INSERT INTO health_checks_item VALUES ( 'Ent',4,'®BÃEµõ',0);
INSERT INTO health_checks_item VALUES ( 'Ent',5,'ºc­µ²§±`',0);
INSERT INTO health_checks_item VALUES ( 'Ent',6,'¦Õ«e•©ºÞ',0);
INSERT INTO health_checks_item VALUES ( 'Ent',7,'Íªô²®ê¶ë',0);
INSERT INTO health_checks_item VALUES ( 'Ent',8,'ºC©Ê»óª¢',0);
INSERT INTO health_checks_item VALUES ( 'Ent',9,'¹L±Ó©Ê»óª¢',0);
INSERT INTO health_checks_item VALUES ( 'Ent',10,'«ó®ç¸¢¸~¤j',0);
INSERT INTO health_checks_item VALUES ( 'Hea',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Hea',1,'±×ÀV',0);
INSERT INTO health_checks_item VALUES ( 'Hea',2,'¥Òª¬¸¢¸~',0);
INSERT INTO health_checks_item VALUES ( 'Hea',3,'²O¤Ú¸¢¸~¤j',0);
INSERT INTO health_checks_item VALUES ( 'Pul',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Pul',1,'¯Ý¹ø²§±`',0);
INSERT INTO health_checks_item VALUES ( 'Pul',2,'¤ßÂø­µ',0);
INSERT INTO health_checks_item VALUES ( 'Pul',3,'¤ß«ß¤£¾ã',0);
INSERT INTO health_checks_item VALUES ( 'Pul',4,'©I§lÁn²§±`',0);
INSERT INTO health_checks_item VALUES ( 'Dig',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Dig',1,'¨xµÊ¸~¤j',0);
INSERT INTO health_checks_item VALUES ( 'Dig',2,'ª·®ð',0);
INSERT INTO health_checks_item VALUES ( 'Spi',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Spi',1,'¯á¬W°¼Ås',0);
INSERT INTO health_checks_item VALUES ( 'Spi',2,'¦h¨Ö«ü',0);
INSERT INTO health_checks_item VALUES ( 'Spi',3,'«CµìªÏ',0);
INSERT INTO health_checks_item VALUES ( 'Spi',4,'Ãö¸`ÅÜ§Î',0);
INSERT INTO health_checks_item VALUES ( 'Spi',5,'¤ô¸~',0);
INSERT INTO health_checks_item VALUES ( 'Uro',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Uro',1,'Áô¸A',1);
INSERT INTO health_checks_item VALUES ( 'Uro',2,'³±Ån¸~¤j',1);
INSERT INTO health_checks_item VALUES ( 'Uro',3,'¥]¥Ö²§±`',1);
INSERT INTO health_checks_item VALUES ( 'Uro',4,'ºë¯ÁÀR¯ß¦±±i',1);
INSERT INTO health_checks_item VALUES ( 'Der',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Der',1,'Å~',0);
INSERT INTO health_checks_item VALUES ( 'Der',2,'¬Ð',0);
INSERT INTO health_checks_item VALUES ( 'Der',3,'¬Î½H',0);
INSERT INTO health_checks_item VALUES ( 'Der',4,'µµ´³',0);
INSERT INTO health_checks_item VALUES ( 'Der',5,'Àã¯l',0);
INSERT INTO health_checks_item VALUES ( 'Der',6,'²§¦ì©Ê¥Ö½§ª¢',0);
INSERT INTO health_checks_item VALUES ( 'Ora',0,'µL²§ª¬',0);
INSERT INTO health_checks_item VALUES ( 'Ora',1,'¤fµÄ½Ã¥Í¤£¨}',0);
INSERT INTO health_checks_item VALUES ( 'Ora',2,'¤úµ²¥Û',0);
INSERT INTO health_checks_item VALUES ( 'Ora',3,'¤ú©Pª¢',0);
INSERT INTO health_checks_item VALUES ( 'Ora',4,'¾¦¦C«r¦X¤£¥¿',0);
INSERT INTO health_checks_item VALUES ( 'Ora',5,'¤úÅiª¢',0);
INSERT INTO health_checks_item VALUES ( 'Ora',6,'¤fµÄÂH½¤²§±`',0);
INSERT INTO health_checks_item VALUES ( 'Ora',7,'ÆT¾¦',0);
INSERT INTO health_checks_item VALUES ( 'Ora',8,'¯Ê¤ú',0);

#
# ¸ê®Æªí®æ¦¡¡G `health_checks_record`
#

CREATE TABLE health_checks_record (
	`year` smallint(5) unsigned NOT NULL default '0',
	semester enum('0','1','2') NOT NULL default '0',
	student_sn int(10) unsigned NOT NULL default '0',
	subject varchar(50) NOT NULL default'',
	`no` int(4) NOT NULL default '0',
	`status` varchar(5) NOT NULL default '',
	`ps` varchar(50) NOT NULL default '',
	update_date timestamp,
	teacher_sn int(11) NOT NULL default '0',
	PRIMARY KEY (`year`,`semester`,student_sn,subject,`no`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_teeth`
#

CREATE TABLE health_teeth (
	`year` smallint(5) unsigned NOT NULL default '0',
	semester enum('0','1','2') NOT NULL default '0',
	student_sn int(10) unsigned NOT NULL default '0',
	`no` varchar(3) NOT NULL default '',
	`status` varchar(3) NOT NULL default '',
	update_date timestamp,
	teacher_sn int(11) NOT NULL default '0',
	PRIMARY KEY (`year`,semester,student_sn,`no`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_hospital`
#

CREATE TABLE health_hospital (
  id int(6) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  enable varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_hospital_record`
#

CREATE TABLE health_hospital_record (
  student_sn int(10) unsigned NOT NULL default '0',
  `no` int(1) unsigned NOT NULL default '1',
  id int(6) unsigned NOT NULL default '1',
  update_date timestamp,
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY (student_sn,`no`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_insurance`
#

CREATE TABLE health_insurance (
  id int(6) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;

INSERT INTO health_insurance VALUES ( 1,'¥þ¥Á°·«O',1);
INSERT INTO health_insurance VALUES ( 2,'¾Ç¥Í¹ÎÅé«OÀI',1);

#
# ¸ê®Æªí®æ¦¡¡G `health_insurance_record`
#

CREATE TABLE health_insurance_record (
  student_sn int(10) unsigned NOT NULL default '0',
  `no` int(1) unsigned NOT NULL default '1',
  id int(6) unsigned NOT NULL default '1',
  update_date timestamp,
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY (student_sn,`no`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_exam_item`
#

CREATE TABLE health_exam_item (
  id int(6) unsigned NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_exam_item VALUES ( 1,'ÀY½¾ÀË¬d',1);
INSERT INTO health_exam_item VALUES ( 2,'¤ßÅ¦¶W­µªi¿zÀË',1);

#
# ¸ê®Æªí®æ¦¡¡G `health_exam_record`
#

CREATE TABLE health_exam_record (
  `year` smallint(5) unsigned NOT NULL default '0',
  `semester` enum('0','1','2') NOT NULL default '0',
  student_sn int(10) unsigned NOT NULL default '0',
  id int(6) unsigned NOT NULL auto_increment,
  measure_date date NOT NULL default '0000-00-00',
  `diag` varchar(100) NOT NULL default '',
  diag_hos int(6) unsigned NOT NULL default '1',
  rediag varchar(100) NOT NULL default '',
  update_date timestamp,
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY (`year`,semester,student_sn,id)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_inject_item`
#

CREATE TABLE health_inject_item (
  id int(6) unsigned NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  lname varchar(200) NOT NULL default '',
  `times` int(4) unsigned NOT NULL default '0',
  ltimes int(4) unsigned NOT NULL default '0',
  lack0 varchar(10) NOT NULL default '',
  lack1 varchar(10) NOT NULL default '',
  lack2 varchar(10) NOT NULL default '',
  lack3 varchar(10) NOT NULL default '',
  lack4 varchar(10) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  `memo` text NOT NULL default '',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_inject_item VALUES ( 1,'¥d¤¶­]','¥d¤¶­]',1,1,'1','1','','','',1,'');
INSERT INTO health_inject_item VALUES ( 2,'B«¬¨xª¢¬Ì­]','B«¬¨xª¢¬Ì­]',3,3,'3','3','2,3','1,2,3','',1,'');
INSERT INTO health_inject_item VALUES ( 3,'¤p¨à³Â·ô¤fªA¬Ì­]','¤p¨à³Â·ô¤fªA¬Ì­]',4,4,'1','1,4','1,2,3,4','1,2,3,4','1,2,3,4',1,'');
INSERT INTO health_inject_item VALUES ( 4,'¥Õ³ï¯}¶Ë­·¦Ê¤é«y²V¦X¬Ì­]','¯}¶Ë­·´î¶q¥Õ³ï²V¦X¬Ì­]',4,3,'1','1','1,3','1,2,3','1,2,3',1,'');
INSERT INTO health_inject_item VALUES ( 5,'¤é¥»¸£ª¢¬Ì­]','¤é¥»¸£ª¢¬Ì­]',3,3,'1','1,3','1,2,3','1,2,3','',1,'');
INSERT INTO health_inject_item VALUES ( 6,'³Â¯l¬Ì­]','',1,0,'','','','','',1,'');
INSERT INTO health_inject_item VALUES ( 7,'MMR','MMR',2,2,'1','1','1,2','','',1,'');

#
# ¸ê®Æªí®æ¦¡¡G `health_inject_record`
#

CREATE TABLE health_inject_record (
  student_sn int(10) unsigned NOT NULL default '0',
  id int(6) unsigned NOT NULL default '0',
  `times` int(4) unsigned NOT NULL default '0',
  date0 date NOT NULL default '0000-00-00',
  date1 date NOT NULL default '0000-00-00',
  date2 date NOT NULL default '0000-00-00',
  date3 date NOT NULL default '0000-00-00',
  date4 date NOT NULL default '0000-00-00',
  update_date timestamp,
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY (student_sn,id)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_yellowcard`
#

CREATE TABLE health_yellowcard (
	student_sn int(10) unsigned NOT NULL default '0',
	`value` tinyint(1) unsigned NOT NULL default '0',
	PRIMARY KEY (student_sn)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_place`
#

CREATE TABLE health_accident_place (
  id int(6) unsigned NOT NULL default '1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_place VALUES ( 1,'¾Þ³õ',1);
INSERT INTO health_accident_place VALUES ( 2,'¹CÀ¸¹B°Ê¾¹§÷',1);
INSERT INTO health_accident_place VALUES ( 3,'´¶³q±Ð«Ç',1);
INSERT INTO health_accident_place VALUES ( 4,'±M¬ì±Ð«Ç',1);
INSERT INTO health_accident_place VALUES ( 5,'¨«´Y',1);
INSERT INTO health_accident_place VALUES ( 6,'¼Ó±è',1);
INSERT INTO health_accident_place VALUES ( 7,'¦a¤U«Ç',1);
INSERT INTO health_accident_place VALUES ( 8,'Åé¨|À]¬¡°Ê¤¤¤ß',1);
INSERT INTO health_accident_place VALUES ( 9,'´Z©Ò',1);
INSERT INTO health_accident_place VALUES ( 10,'®Õ¥~',1);
INSERT INTO health_accident_place VALUES ( 999,'¨ä¥L',1);

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_reason`
#

CREATE TABLE health_accident_reason (
  id int(6) unsigned NOT NULL default '1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_reason VALUES ( 1,'¤U½Ò¹CÀ¸',1);
INSERT INTO health_accident_reason VALUES ( 2,'¤W¤U½Ò³~¤¤',1);
INSERT INTO health_accident_reason VALUES ( 3,'¤ÉºX',1);
INSERT INTO health_accident_reason VALUES ( 4,'¥´¯}¬Á¼þ',1);
INSERT INTO health_accident_reason VALUES ( 5,'¥´±½',1);
INSERT INTO health_accident_reason VALUES ( 6,'¤W¤U¼Ó±è',1);
INSERT INTO health_accident_reason VALUES ( 7,'ÃÀ¯à½Ò',1);
INSERT INTO health_accident_reason VALUES ( 8,'Åé¨|½Ò',1);
INSERT INTO health_accident_reason VALUES ( 999,'¨ä¥L',1);

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_part`
#

CREATE TABLE health_accident_part (
  id int(6) unsigned NOT NULL default '1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_part VALUES ( 1,'ÀY',1);
INSERT INTO health_accident_part VALUES ( 2,'ÀV',1);
INSERT INTO health_accident_part VALUES ( 3,'ªÓ',1);
INSERT INTO health_accident_part VALUES ( 4,'¯Ý',1);
INSERT INTO health_accident_part VALUES ( 5,'¸¡',1);
INSERT INTO health_accident_part VALUES ( 6,'­I',1);
INSERT INTO health_accident_part VALUES ( 7,'²´',1);
INSERT INTO health_accident_part VALUES ( 8,'ÃC­±',1);
INSERT INTO health_accident_part VALUES ( 9,'¤fµÄ',1);
INSERT INTO health_accident_part VALUES ( 10,'¦Õ»ó³ï',1);
INSERT INTO health_accident_part VALUES ( 11,'¤WªÏ',1);
INSERT INTO health_accident_part VALUES ( 12,'¸y',1);
INSERT INTO health_accident_part VALUES ( 13,'¤UªÏ',1);
INSERT INTO health_accident_part VALUES ( 14,'Áv³¡',1);
INSERT INTO health_accident_part VALUES ( 15,'·|³±³¡',1);

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_status`
#

CREATE TABLE health_accident_status (
  id int(6) unsigned NOT NULL default'1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_status VALUES ( 1,'À¿¶Ë',1);
INSERT INTO health_accident_status VALUES ( 2,'µõ³Î¨ë¶Ë',1);
INSERT INTO health_accident_status VALUES ( 3,'§¨À£¶Ë',1);
INSERT INTO health_accident_status VALUES ( 4,'®À¼²¶Ë',1);
INSERT INTO health_accident_status VALUES ( 5,'§á¶Ë',1);
INSERT INTO health_accident_status VALUES ( 6,'¨`¿S¶Ë',1);
INSERT INTO health_accident_status VALUES ( 7,'¥m«r¶Ë',1);
INSERT INTO health_accident_status VALUES ( 8,'°©§é',1);
INSERT INTO health_accident_status VALUES ( 9,'ÂÂ¶Ë',1);
INSERT INTO health_accident_status VALUES ( 10,'¥~¬ì¨ä¥L',1);
INSERT INTO health_accident_status VALUES ( 11,'µo¿N',1);
INSERT INTO health_accident_status VALUES ( 12,'·w¯t',1);
INSERT INTO health_accident_status VALUES ( 13,'äú¤ß¹Ã¦R',1);
INSERT INTO health_accident_status VALUES ( 14,'ÀYµh',1);
INSERT INTO health_accident_status VALUES ( 15,'¤úµh',1);
INSERT INTO health_accident_status VALUES ( 16,'­Gµh',1);
INSERT INTO health_accident_status VALUES ( 17,'¸¡µh',1);
INSERT INTO health_accident_status VALUES ( 18,'¸¡Âm',1);
INSERT INTO health_accident_status VALUES ( 19,'¸gµh',1);
INSERT INTO health_accident_status VALUES ( 20,'®ð³Ý',1);
INSERT INTO health_accident_status VALUES ( 21,'¬y»ó¦å',1);
INSERT INTO health_accident_status VALUES ( 22,'¯lÄo',1);
INSERT INTO health_accident_status VALUES ( 23,'²´¯e',1);
INSERT INTO health_accident_status VALUES ( 24,'¤º¬ì¨ä¥L',1);

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_attend`
#

CREATE TABLE health_accident_attend (
  id int(6) unsigned NOT NULL default'1',
  `name` varchar(100) NOT NULL default '',
  `enable` varchar(1) NOT NULL default '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO health_accident_attend VALUES ( 1,'¶Ë¤f³B²z',1);
INSERT INTO health_accident_attend VALUES ( 2,'¦B¼Å',1);
INSERT INTO health_accident_attend VALUES ( 3,'¼ö¼Å',1);
INSERT INTO health_accident_attend VALUES ( 4,'¥ð®§Æ[¹î',1);
INSERT INTO health_accident_attend VALUES ( 5,'³qª¾®aªø',1);
INSERT INTO health_accident_attend VALUES ( 6,'®aªø±a¦^',1);
INSERT INTO health_accident_attend VALUES ( 7,'®Õ¤è°eÂå',1);
INSERT INTO health_accident_attend VALUES ( 8,'½Ã¥Í±Ð¨|',1);
INSERT INTO health_accident_attend VALUES ( 999,'¨ä¥L³B²z',1);

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_record`
#

CREATE TABLE health_accident_record (
	id int(10) unsigned NOT NULL auto_increment,
	`year` smallint(5) unsigned NOT NULL default '0',
	semester enum('0','1','2') NOT NULL default '0',
	student_sn int(10) unsigned NOT NULL default '0',
	sign_time datetime NOT NULL default '0000-00-00 00:00:00',
	obs_min int(6) unsigned NOT NULL default '0',
	temp decimal(3,1) NOT NULL default '0.0',
	place_id int(6) unsigned NOT NULL default '0',
	reason_id int(6) unsigned NOT NULL default '0',
	`memo` text NOT NULL default '',
	update_date timestamp,
	teacher_sn int(11) NOT NULL default '0',
	PRIMARY KEY (id)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_part_record`
#

CREATE TABLE health_accident_part_record (
	`pid` int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	part_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (`pid`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_status_record`
#

CREATE TABLE health_accident_status_record (
	`sid` int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	status_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (`sid`)
) ENGINE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `health_accident_attend_record`
#

CREATE TABLE health_accident_attend_record (
	aid int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	attend_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (aid)
)ENGINE=MyISAM;
