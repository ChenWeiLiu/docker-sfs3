CREATE TABLE lunchtb (
  pDate date NOT NULL default '0000-00-00',
  pMday tinyint(4) NOT NULL default '0',
  pFood varchar(20) default NULL,
  pMenu varchar(120) default NULL,
  pFruit varchar(20) default NULL,
  pPs varchar(20) default NULL,
  pDesign varchar(20) default NULL,
  PRIMARY KEY  (pDate)
) ENGINE=MyISAM;

