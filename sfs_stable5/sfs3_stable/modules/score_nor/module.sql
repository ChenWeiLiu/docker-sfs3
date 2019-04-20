CREATE TABLE seme_score_nor (
  seme_year_seme varchar(6) NOT NULL default '',
  stud_id varchar(20) NOT NULL default '',
  score1 smallint(3) NOT NULL default '0',
  score2 smallint(3) NOT NULL default '0',
  score3 smallint(3) NOT NULL default '0',
  score4 smallint(3) NOT NULL default '0',
  score5 smallint(3) NOT NULL default '0',
  score6 smallint(3) NOT NULL default '0',
  score7 smallint(3) NOT NULL default '0',
  PRIMARY KEY  (seme_year_seme,stud_id),
  KEY stud_id (stud_id)
) ENGINE=MyISAM;