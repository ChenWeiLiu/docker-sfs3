# $Id: module.sql 8149 2014-09-27 02:32:17Z smallduh $
CREATE TABLE grad_stud (
  grad_sn int(10) NOT NULL auto_increment,
  stud_grad_year tinyint(3) unsigned default NULL,
  class_year char(2) default NULL,
  class_sort tinyint(2) unsigned default NULL,
  stud_id varchar(20) default NULL,
  grad_kind tinyint(1) unsigned default NULL,
  grad_date date default NULL,
  grad_word varchar(20) default NULL,
  grad_num varchar(20) default NULL,
  grad_score float unsigned default NULL,
  UNIQUE KEY grad_sn (grad_sn)
) ENGINE=MyISAM;
