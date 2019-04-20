# $Id: stud_base_temp.sql 5311 2009-01-10 08:11:55Z hami $
DROP TABLE IF EXISTS stud_eduh;
CREATE TABLE stud_eduh (
  eh_id bigint(20) unsigned NOT NULL auto_increment,
  stud_id varchar(20) default NULL,
  eh_from tinyint(3) unsigned default NULL,
  eh_from_date date default NULL,
  eh_teacher varchar(20) default NULL,
  eh_caes varchar(60) default NULL,
  eh_meth varchar(60) default NULL,
  eh_resion_memo varchar(40) default NULL,
  eh_is_over tinyint(3) unsigned default NULL,
  eh_over_memo varchar(40) default NULL,
  eh_over_date date default NULL,
  eh_case_date date default NULL,
  eh_case_memo text,
  eh_case_relation varchar(30) default NULL,
  PRIMARY KEY  (eh_id)
) TYPE=MyISAM;


DROP TABLE IF EXISTS subj_hour;
CREATE TABLE subj_hour (
  sj_id char(2) NOT NULL default '',
  sj_b_time varchar(4) NOT NULL default '',
  sj_e_time varchar(4) NOT NULL default '',
  sj_kind tinyint(3) unsigned NOT NULL default '0',
  sj_memo varchar(20) NOT NULL default '',
  PRIMARY KEY  (sj_id),
  UNIQUE KEY sj_id (sj_id)
) TYPE=MyISAM COMMENT='¤W½Ò®É¶¡³]©w';


DROP TABLE IF EXISTS subj_cour;
CREATE TABLE subj_cour (
  seme_year_seme varchar(6) NOT NULL default '',
  seme_class varchar(10) NOT NULL default '',
  subj_id smallint(5) unsigned NOT NULL default '0',
  sc_num tinyint(3) unsigned NOT NULL default '0',
  sc_perr double NOT NULL default '0',
  sc_five_kind tinyint(3) unsigned NOT NULL default '0',
  sc_percent double NOT NULL default '0',
  sc_order tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (seme_year_seme,seme_class),
  UNIQUE KEY seme_year_seme (seme_year_seme,seme_class)
) TYPE=MyISAM COMMENT='¦~¯Å¶}½Ò';

/*==============================================================*/
/* Database name:  student                                      */
/* DBMS name:      MySQL 3.23                                   */
/* Created on:     2002/9/24 ¤W¤È 03:57:56                        */
/*==============================================================*/

DROP TABLE IF EXISTS stud_base_temp;
CREATE TABLE stud_base_temp (
  stud_id varchar(20) NOT NULL default '',
  student_sn int(10) unsigned NOT NULL auto_increment,
  stud_name varchar(20) default NULL,
  stud_sex tinyint(3) unsigned default NULL,
  stud_birthday date default NULL,
  stud_blood_type tinyint(3) unsigned default NULL,
  stud_birth_place tinyint(3) unsigned default NULL,
  stud_kind varchar(60) default NULL,
  stud_country varchar(20) default NULL,
  stud_country_kind tinyint(3) unsigned default NULL,
  stud_person_id varchar(20) default NULL,
  stud_country_name varchar(20) default NULL,
  stud_addr_1 varchar(60) default NULL,
  stud_addr_2 varchar(60) default NULL,
  stud_tel_1 varchar(20) default NULL,
  stud_tel_2 varchar(20) default NULL,
  stud_tel_3 varchar(20) default NULL,
  stud_mail varchar(50) default NULL,
  stud_addr_a varchar(6) default NULL,
  stud_addr_b varchar(12) default NULL,
  stud_addr_c varchar(12) default NULL,
  stud_addr_d varchar(6) default NULL,
  stud_addr_e varchar(20) default NULL,
  stud_addr_f varchar(6) default NULL,
  stud_addr_g varchar(8) default NULL,
  stud_addr_h varchar(6) default NULL,
  stud_addr_i varchar(8) default NULL,
  stud_addr_j varchar(8) default NULL,
  stud_addr_k varchar(6) default NULL,
  stud_addr_l varchar(6) default NULL,
  stud_addr_m varchar(12) default NULL,
  stud_class_kind tinyint(3) unsigned default NULL,
  stud_spe_kind tinyint(3) unsigned default NULL,
  stud_spe_class_kind tinyint(3) unsigned default NULL,
  stud_spe_class_id tinyint(3) unsigned default NULL,
  stud_preschool_status tinyint(3) unsigned default NULL,
  stud_preschool_id varchar(8) default NULL,
  stud_preschool_name varchar(40) default NULL,
  stud_Mschool_status tinyint(3) unsigned default NULL,
  stud_mschool_id varchar(8) default NULL,
  stud_mschool_name varchar(40) default NULL,
  email_pass varchar(10) default NULL,
  stud_study_year int(10) unsigned default NULL,
  curr_class_num varchar(6) default NULL,
  stud_study_cond tinyint(3) unsigned default NULL,
  PRIMARY KEY  (stud_id),
  UNIQUE KEY student_sn (student_sn)
) TYPE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `stud_seme_abs`
#

CREATE TABLE stud_seme_abs (
  seme_year_seme varchar(6) NOT NULL default '',
  stud_id varchar(20) NOT NULL default '',
  abs_kind tinyint(3) unsigned default NULL,
  abs_days int(10) unsigned default NULL,
  PRIMARY KEY  (seme_year_seme,stud_id)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_seme_abs`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `stud_seme_eduh`
#

CREATE TABLE stud_seme_eduh (
  seme_year_seme varchar(6) NOT NULL default '',
  stud_id varchar(20) NOT NULL default '',
  sse_relation tinyint(3) unsigned default NULL,
  sse_family_kind tinyint(3) unsigned default NULL,
  sse_family_air tinyint(3) unsigned default NULL,
  sse_farther tinyint(3) unsigned default NULL,
  sse_mother tinyint(3) unsigned default NULL,
  sse_live_state tinyint(3) unsigned default NULL,
  sse_rich_state tinyint(3) unsigned default NULL,
  sse_s1 varchar(40) default NULL,
  sse_s2 varchar(40) default NULL,
  sse_s3 varchar(40) default NULL,
  sse_s4 varchar(40) default NULL,
  sse_s5 varchar(40) default NULL,
  sse_s6 varchar(40) default NULL,
  sse_s7 varchar(40) default NULL,
  sse_s8 varchar(40) default NULL,
  sse_s9 varchar(40) default NULL,
  sse_s10 varchar(40) default NULL,
  sse_s11 varchar(40) default NULL,
  PRIMARY KEY  (seme_year_seme,stud_id)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_seme_eduh`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `stud_seme_score`
#

CREATE TABLE stud_seme_score (
  sss_id bigint(20) unsigned NOT NULL auto_increment,
  seme_year_seme varchar(6) default NULL,
  student_sn int(10) unsigned NOT NULL default '0',
  ss_id smallint(5) unsigned default NULL,
  ss_score decimal(4,2) default NULL,
  ss_score_memo text,
  PRIMARY KEY  (sss_id)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_seme_score`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `stud_seme_score_s`
#

CREATE TABLE stud_seme_score_s (
  sss_id bigint(20) unsigned NOT NULL default '0',
  ss_id bigint(20) unsigned default NULL,
  sss_kind tinyint(3) unsigned default NULL,
  sss_score decimal(4,2) default NULL,
  PRIMARY KEY  (sss_id)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_seme_score_s`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `stud_seme_spe`
#
CREATE TABLE stud_seme_spe (
  ss_id bigint(20) unsigned NOT NULL auto_increment,
  seme_year_seme varchar(6) default NULL,
  stud_id varchar(20) default NULL,
  sp_date date default NULL,
  sp_memo text,
  teach_id varchar(20) NOT NULL default '',
  update_time timestamp(14) NOT NULL,
  PRIMARY KEY  (ss_id)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_seme_spe`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `stud_seme_talk`
#
CREATE TABLE stud_seme_talk (
  sst_id bigint(20) unsigned NOT NULL auto_increment,
  seme_year_seme varchar(6) default NULL,
  stud_id varchar(20) default NULL,
  sst_date date default NULL,
  sst_name varchar(20) default NULL,
  sst_main varchar(40) default NULL,
  sst_memo text,
  teach_id varchar(20) NOT NULL default '',
  update_time timestamp(14) NOT NULL,
  PRIMARY KEY  (sst_id)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_seme_talk`
#



#
# ¸ê®Æªí®æ¦¡¡G `stud_seme_test`
#

CREATE TABLE stud_seme_test (
  st_id bigint(20) unsigned NOT NULL default '0',
  seme_year_seme varchar(6) default NULL,
  stud_id varchar(20) default NULL,
  st_numb varchar(20) default NULL,
  st_name varchar(20) default NULL,
  st_score_numb varchar(20) default NULL,
  st_data_from varchar(40) default NULL,
  st_chang_numb varchar(20) default NULL,
  st_name_long varchar(40) default NULL,
  PRIMARY KEY  (st_id)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_seme_test`
#

# --------------------------------------------------------


# --------------------------------------------------------


drop table if exists stud_mid_sco;

drop table if exists stud_mid_rep;

drop table if exists stud_mid_abs;

drop table if exists stud_family;

drop table if exists stud_psy_tests;

drop table if exists stud_score;

drop table if exists stud_tea_parent;

drop table if exists pro_kind_old;

drop table if exists pro_share;

 



/*==============================================================*/
/* Table : stud_mid_abs                                         */
/*==============================================================*/
create table if not exists stud_mid_abs
(
   sma_year                       varchar(4)                     not null,
   sma_month                      varchar(2)                     not null,
   stud_id                        varchar(20)                    not null,
   sma_kind                       tinyint unsigned               not null,
   sma_days                       int unsigned,
   primary key (stud_id, sma_year, sma_month, sma_kind)
);

/*==============================================================*/
/* Table : stud_mid_rep                                         */
/*==============================================================*/
create table if not exists stud_mid_rep
(
   smr_id                         bigint unsigned                not null auto_increment ,
   stud_id                        varchar(20),
   smr_date                       date,
   smr_kind                       tinyint unsigned,
   smr_num                        tinyint unsigned,
   smr_res                        text,
   primary key (smr_id)
);
#
# ¸ê®Æªí®æ¦¡¡G `stud_mid_sco`
#

CREATE TABLE stud_mid_sco (
  smc_id bigint(20) unsigned NOT NULL default '0',
  stud_id varchar(20) default NULL,
  smc_date date default NULL,
  smc_name varchar(20) default NULL,
  smc_score decimal(4,2) default NULL,
  smc_class1 varchar(60) default NULL,
  smc_class2 varchar(60) default NULL,
  smc_class3 varchar(60) default NULL,
  PRIMARY KEY  (smc_id)
) TYPE=MyISAM;


/*==============================================================*/
/* Database name:  student                                      */
/* DBMS name:      MySQL 3.23                                   */
/* Created on:     2002/9/24 ¤W¤È 04:06:39                        */
/*==============================================================*/


drop table if exists stud_grad;

/*==============================================================*/
/* Table : stud_grad                                            */
/*==============================================================*/
create table if not exists stud_grad
(
   stud_id                        varchar(20)                    not null,
   grad_kind                      varchar(10),
   grad_date                      date,
   grad_word                      varchar(20),
   grad_numb                      varchar(10),
   primary key (stud_id)
);

#
# ¸ê®Æªí®æ¦¡¡G `sys_data_field`
#

CREATE TABLE sys_data_field (
  d_table_name varchar(30) NOT NULL default '',
  d_field_name varchar(30) NOT NULL default '',
  d_field_cname varchar(30) NOT NULL default '',
  d_field_type varchar(30) NOT NULL default '',
  d_field_order tinyint(4) NOT NULL default '0',
  d_is_display tinyint(4) NOT NULL default '0',
  d_field_xml varchar(40) NOT NULL default '',
  PRIMARY KEY  (d_table_name,d_field_name),
  UNIQUE KEY d_table_name (d_table_name,d_field_name)
) TYPE=MyISAM COMMENT='¸ê®ÆÄæ¦ì';

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `sys_data_field`
#
#
# Dumping data for table `sys_data_field`
#
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_Mschool_status', '', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_mschool_id', '±Ð¨|³¡¥N¸¹', 'varchar(8)', 0, 0, '±Ð¨|³¡¾Ç®Õ¥N¸¹_°ê¤p');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_spe_class_id', '¯S®í¯Z¤W½Ò©Ê½è', 'tinyint(3) unsigned', 0, 0, '¯S®í¯Z¤W½Ò©Ê½è');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_preschool_status', '¤J¾Ç¸ê®æ', 'tinyint(3) unsigned', 0, 0, '¥®¸X¶é¤J¾Ç¸ê®æ');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_spe_class_kind', '¯S®í¯Z¯Z§O', 'tinyint(3) unsigned', 0, 0, '¯S®í¯Z¯Z§O');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_spe_kind', '¯S®í¯ZÃþ§O', 'tinyint(3) unsigned', 0, 0, '¯S®í¯ZÃþ§O');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_a', '¿¤/¥«', 'varchar(6)', 0, 0, '¿¤¥«¦W');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_b', '¶m/Âí/¥«/°Ï', 'varchar(12)', 0, 0, '¶mÂí¥«°Ï¦W');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_c', '§ø/¨½', 'varchar(12)', 0, 0, '§ø¨½');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_d', '¾F', 'varchar(6)', 0, 0, '¾F');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_e', '¸ô/µó', 'varchar(20)', 0, 0, '¸ôµó');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_f', '¬q', 'varchar(6)', 0, 0, '¬q');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_g', '«Ñ', 'varchar(8)', 0, 0, '«Ñ');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_h', '§Ë', 'varchar(6)', 0, 0, '§Ë');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_i', '¸¹', 'varchar(8)', 0, 0, '¸¹');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_j', '¤§', 'varchar(8)', 0, 0, '¤§');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_k', '¼Ó', 'varchar(6)', 0, 0, '¼Ó');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_l', '¼Ó¤§', 'varchar(6)', 0, 0, '¼Ó¤§');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_m', '¨ä¥L', 'varchar(12)', 0, 0, '¨ä¥L');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_class_kind', '¯Z¯Å©Ê½è', 'tinyint(3) unsigned', 0, 0, '¯Z¯Å©Ê½è');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '¾Ç¸¹');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_name', '©m¦W', 'varchar(20)', 0, 0, '¾Ç¥Í©m¦W');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_sex', '©Ê§O', 'tinyint(3) unsigned', 0, 0, '©Ê§O');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_birthday', '¥X¥Í¦~¤ë¤é', 'date', 0, 0, '¥X¥Í¦~¤ë¤é');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_blood_type', '¦å«¬', 'tinyint(3) unsigned', 0, 0, '¦å«¬');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_birth_place', '¥X¥Í¦a', 'tinyint(3) unsigned', 0, 0, '¥X¥Í¦a');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_kind', '¾Ç¥Í¨­¤À§O', 'varchar(60)', 0, 0, '¾Ç¥Í¨­¤À§O');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_country', '°êÄy', 'varchar(20)', 0, 0, '°êÄy');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_country_kind', 'ÃÒ·ÓºØÃþ', 'tinyint(3) unsigned', 0, 0, 'ÃÒ·ÓºØÃþ');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_person_id', '¨­¤ÀÃÒ¸¹½X', 'varchar(20)', 0, 0, '¨­¤ÀÃÒ¸¹½X');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_country_name', '¹´©~¦a', 'varchar(20)', 0, 0, '¹´©~¦a');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_1', '¤áÄy¦a§}', 'varchar(60)', 0, 0, '¤áÄy¦a§}');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_addr_2', '³sµ¸¦a§}', 'varchar(60)', 0, 0, '³sµ¸¦a§}');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_tel_1', '¤áÄy¹q¸Ü', 'varchar(20)', 0, 0, '¤áÄy¹q¸Ü');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'grandmoth_birthyear', '', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'grandmoth_alive', '¯ª¥À¦sª\\.', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'grandmoth_name', '¯ª¥À©m¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'grandfath_alive', '¯ª¤÷¦sª\\.', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'grandfath_birthyear', '', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'grandfath_name', '¯ª¤÷©m¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_email', '¹q¤l¶l¥ó', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_hand_phone', '¦æ°Ê¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_work_name', 'Â¾ºÙ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_p_id', '¨­¤ÀÃÒÃÒ·Ó', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_unit', 'ªA°È³æ¦ì', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_relation', '»PºÊÅ@¤HÃö«Y', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_address', '¦a§}', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_phone', 'ºÊÅ@¤H¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'guardian_name', 'ºÊÅ@¤H©m¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'is_same_gua', '', 'char(1)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_note', '', 'tinytext', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_email', '¹q¤l¶l¥ó', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_hand_phone', '¦æ°Ê¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_home_phone', '¹q¸Ü(¦v)', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_phone', '¹q¸Ü(¤½)', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_work_name', 'Â¾ºÙ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_unit', 'ªA°È³æ¦ì', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_occupation', 'Â¾·~', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_education', '±Ð¨|µ{«×', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_abroad', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_p_id', '¨­¤ÀÃÒÃÒ·Ó', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_country', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_relation', '»P¥ÀÃö«Y', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_alive', '¦sª\\.', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_birthyear', '¥X¥Í¦~¦¸', 'varchar(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'moth_name', '¥À¿Ë©m¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_email', '¹q¤l¶l¥ó', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_note', '', 'tinytext', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_hand_phone', '¦æ°Ê¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_home_phone', '¹q¸Ü(¦v)', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_phone', '¹q¸Ü(¤½)', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_work_name', 'Â¾ºÙ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_unit', 'ªA°È³æ¦ì', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_occupation', 'Â¾·~', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_abroad', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_education', '±Ð¨|µ{«×', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_p_id', '¨­¤ÀÃÒÃÒ·Ó', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_country', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_alive', '¦sª\\.', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_relation', '»P¤÷Ãö«Y', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_birthyear', '¥X¥Í¦~¦¸', 'varchar(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'fath_name', '¤÷¿Ë©m¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'addr_id', '', 'bigint(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'update_time', '', 'timestamp(14)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_domicile', 'update_id', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_brother_sister', 'bs_birthyear', '¦~¦¸', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_brother_sister', 'bs_gradu', '´NÅª¾Ç®Õ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_brother_sister', 'bs_id', '¥N¸¹', 'bigint(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_brother_sister', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_brother_sister', 'bs_name', '©m¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_brother_sister', 'bs_calling', 'ºÙ¿×', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_kinfolk', 'kin_id', '¿ËÄÝ¥N¸¹', 'bigint(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_kinfolk', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_kinfolk', 'kin_name', '©m¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_kinfolk', 'kin_calling', 'ºÙ¿×', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_kinfolk', 'kin_phone', '¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_kinfolk', 'kin_hand_phone', '¤â¾÷', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_kinfolk', 'kin_email', '¹q¤l¶l¥ó', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_id', '­Ó®×½s¸¹', 'bigint(20) unsigned', 0, 0, '­Ó®×½s¸¹');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '¾Ç¸¹');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_from', '­Ó®×¨Ó·½', 'tinyint(3) unsigned', 0, 0, '­Ó®×¨Ó·½');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_from_date', '±µ®×¤é´Á', 'date', 0, 0, '±µ®×¤é´Á');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_teacher', '»{»²¦Ñ®v', 'varchar(20)', 0, 0, '»{»²¦Ñ®v');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_caes', '°ÝÃDÃþ§O', 'varchar(60)', 0, 0, '°ÝÃDÃþ§O');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_meth', '°¾®t¦æ¬°', 'varchar(60)', 0, 0, '°¾®t¦æ¬°');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_resion_memo', '¾AÀ³¤£¨}­ì¦]', 'varchar(40)', 0, 0, '¾AÀ³¤£¨}­ì¦]');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_is_over', 'µ²®×§_', 'tinyint(3) unsigned', 0, 0, 'µ²®×§_');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_over_memo', 'µ²®×­ì¦]', 'varchar(40)', 0, 0, 'µ²®×­ì¦]');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_over_date', 'µ²®×¤é´Á', 'date', 0, 0, 'µ²®×¤é´Á');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_case_date', '»²¾É¤é´Á', 'date', 0, 0, '»²¾É¤é´Á');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_case_memo', '»²¾É¤º®e', 'text', 0, 0, '»²¾É¤º®e');
INSERT INTO sys_data_field VALUES ('stud_eduh', 'eh_case_relation', '­Ó®×ÃöÁp½s¸¹', 'varchar(30)', 0, 0, '­Ó®×ÃöÁp½s¸¹');
INSERT INTO sys_data_field VALUES ('subj_hour', 'sj_id', '¸`¦¸', 'char(2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('subj_hour', 'sj_b_time', '¶}©l®É¶¡', 'varchar(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('subj_hour', 'sj_e_time', 'µ²§ô®É¶¡', 'varchar(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('subj_hour', 'sj_kind', '±Æ½ÒÃþ§O', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('subj_hour', 'sj_memo', '¬¡°Ê¤º®e', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('subj_cour', 'sc_five_kind', '¤­¨|Ãþ§O', 'tinyint(3) unsigned', 0, 0, '¤­¨|Ãþ§O');
INSERT INTO sys_data_field VALUES ('subj_cour', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '¾Ç¦~¾Ç´Á');
INSERT INTO sys_data_field VALUES ('subj_cour', 'seme_class', '¯Z¯Å', 'varchar(10)', 0, 0, '¯Z¯Å');
INSERT INTO sys_data_field VALUES ('subj_cour', 'subj_id', '»â°ì¦WºÙ', 'smallint(5) unsigned', 0, 0, '»â°ì¦WºÙ');
INSERT INTO sys_data_field VALUES ('subj_cour', 'sc_num', '¸`¼Æ', 'tinyint(3) unsigned', 0, 0, '¸`¼Æ');
INSERT INTO sys_data_field VALUES ('subj_cour', 'sc_perr', 'Åv¼Æ', 'double', 0, 0, 'Åv¼Æ');
INSERT INTO sys_data_field VALUES ('subj_cour', 'sc_percent', '¤­¨|¤ñ¨Ò', 'double', 0, 0, '¤­¨|¤ñ¨Ò');
INSERT INTO sys_data_field VALUES ('subj_cour', 'sc_order', '¦C¦L±Æ§Ç', 'tinyint(3) unsigned', 0, 0, '¦C¦L±Æ§Ç');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_preschool_id', '±Ð¨|³¡¥N¸¹', 'varchar(8)', 0, 0, '±Ð¨|³¡¾Ç®Õ¥N¸¹_¥®¸X¶é');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_preschool_name', '¾Ç®Õ¦WºÙ', 'varchar(40)', 0, 0, '¾Ç®Õ¦WºÙ_¥®¸X¶é');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_tel_2', '³sµ¸¹q¸Ü', 'varchar(20)', 0, 0, '³sµ¸¹q¸Ü');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_tel_3', '¦æ°Ê¹q¸Ü', 'varchar(20)', 0, 0, '¦æ°Ê¹q¸Ü');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_mail', '¹q¤l¶l¥ó«H½c', 'varchar(50)', 0, 0, '¹q¤l¶l¥ó«H½c');
INSERT INTO sys_data_field VALUES ('stud_mid_abs', 'sma_year', '¦~', 'varchar(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_abs', 'sma_month', '¤ë', 'char(2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_abs', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_abs', 'sma_kind', '¯Ê®uÃþ§O', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_abs', 'sma_days', '¯Ê®u¤Ñ¼Æ', 'int(10) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_rep', 'smr_id', '¬y¤ô¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_rep', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_rep', 'smr_date', '¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_rep', 'smr_kind', 'Ãþ§O', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_rep', 'smr_num', '¦¸¼Æ', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_rep', 'smr_res', '¨Æ¥Ñ', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_sco', 'smc_id', '¬y¤ô¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_sco', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_sco', 'smc_date', '¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_sco', 'smc_name', 'ªÀ¹Î¦WºÙ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_sco', 'smc_score', 'ªÀ¹Î¦¨ÁZ', 'decimal(4,2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_sco', 'smc_class1', '¯Z¯Å¬¡°Ê', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_sco', 'smc_class2', '¾Ç¥Í¦Ûªv·|¬¡°Ê', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_mid_sco', 'smc_class3', '¾Ç®Õ¨Ò¦æ¬¡°Ê', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'move_id', '¬y¤ô¸¹', 'bigint(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'move_kind', '²§°ÊÃþ§O', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'move_year_seme', '²§°Ê¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'move_date', '²§°Ê¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'move_c_unit', '²§°Ê®Ö­ã¾÷Ãö¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'move_c_date', '®Ö­ã¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'move_c_word', '®Ö­ã¦r', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'move_c_num', '®Ö­ã¸¹', 'varchar(14)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'update_time', '§ó°Ê®É¶¡', 'datetime', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'update_id', '§ó°Ê¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_move', 'update_ip', '§ó°ÊIP', 'varchar(15)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_grad', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_grad', 'grad_kind', '²¦·~Ãþ§O', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_grad', 'grad_date', '²¦·~¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_grad', 'grad_word', '²¦·~¦r', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_grad', 'grad_numb', '²¦·~¸¹', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme', 'seme_class', '¦~¯Å', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme', 'seme_class_name', '¯Z¯Å', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme', 'seme_num', '®y¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme', 'seme_class_year_s', '¤À´²¦¡_¦~¯Å', 'int(10) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme', 'seme_class_s', '¤À´²¦¡_¯Z¯Å', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme', 'seme_num_s', '¤À´²¦¡_®y¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_abs', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_abs', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_abs', 'abs_kind', '¯Ê®uÃþ§O', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_abs', 'abs_days', '¯Ê®u¤Ñ¼Æ', 'int(10) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_relation', '¤÷¥ÀÃö«Y', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_family_kind', '®a®xÃþ«¬', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_family_air', '®a®x®ðª^', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_farther', '¤÷ºÞ±Ð¤è¦¡', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_mother', '¥ÀºÞ±Ð¤è¦¡', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_live_state', '©~¦í±¡§Î', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_rich_state', '¸gÀÙª¬ªp', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s1', '³Ì³ß·R¬ì¥Ø', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s2', '³Ì§xÃø¬ì¥Ø', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s3', '¯S®í¤~¯à', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s4', '¿³½ì', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s5', '¥Í¬¡²ßºD', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s6', '¤H»ÚÃö«Y', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s7', '¥~¦V¦æ¬°', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s8', '¤º¦V¦æ¬°', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s9', '¾Ç²ß¦æ¬°', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s10', '¤£¨}²ßºD', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_eduh', 'sse_s11', 'µJ¼{¦æ¬°', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score', 'ss_id', '¬y¤ô¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score', 'ss_subject', '¬ì¥Ø', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score', 'ss_score', '¦¨ÁZ', 'decimal(4,2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score', 'ss_score_memo', '¤å¦r´y­z', 'varchar(200)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score_s', 'sss_id', '¬y¤ô¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score_s', 'ss_id', '»â°ì¥N¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score_s', 'sss_kind', '¤À¶µ¬ì¥Ø', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_score_s', 'sss_score', '¦¨ÁZ', 'decimal(4,2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_spe', 'ss_id', '¬y¤ô¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_spe', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_spe', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_spe', 'sp_date', '¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_spe', 'sp_memo', 'Àu¨}ªí²{¨Æ¥Ñ', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_talk', 'sst_id', '¬y¤ô¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_talk', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_talk', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_talk', 'sst_date', '°O¿ý¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_talk', 'sst_name', '³sµ¸¹ï¶H', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_talk', 'sst_main', '³sµ¸¨Æ¶µ', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_talk', 'sst_memo', '¤º®e­nÂI', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'st_id', '¬y¤ô¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'st_numb', '¤ß²z´úÅç½s¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'st_name', '´úÅç¦¨ÁZªº¤¤¤åÂ²ºÙ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'st_score_numb', '¦¨ÁZ½s¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'st_data_from', '¸ê®Æ¨Ó·½', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'st_chang_numb', '¨Ï¥ÎªºÂà´«ªí½s¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_seme_test', 'st_name_long', '´úÅç¦¨ÁZªº¤¤¤å¥þ¦W', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_mschool_name', '¾Ç®Õ¦WºÙ', 'varchar(40)', 0, 0, '¾Ç®Õ¦WºÙ_°ê¤p');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_study_year', '¤J¾Ç¦~', 'int(10) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_base', 'curr_class_num', '¥Ø«e¯Z¯Å', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_base', 'stud_study_cond', '', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_base', 'email_pass', '', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_title', 'teach_title_id', 'Â¾ºÙ¥N¸¹', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_title', 'title_name', 'Â¾ºÙ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_title', 'title_kind', 'Â¾ºÙÃþ§O', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_title', 'title_short_name', 'Â²ºÙ', 'varchar(12)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_title', 'room_id', '³B«Ç¥N¸¹', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_title', 'title_memo', '³Æµù', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_id', '±Ð®v¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_person_id', '¨­¤ÀÃÒ¦r¸¹', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'name', '©m¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'sex', '©Ê§O', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'age', '¦~ÄÖ', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'birthday', '¥Í¤é', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'birth_place', '¥X¥Í¦a', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'marriage', 'µ²±Bª¬ºA', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'address', '¦í§}', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'home_phone', '®a¤¤¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'cell_phone', '¤â¾÷', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'office_home', '¿ì¤½«Ç¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_condition', '¦bÂ¾ª¬ºA', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_memo', '³Æµù', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'login_pass', 'µn¤J±K½X', 'varchar(12)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_edu_kind', '³Ì°ª¾Ç¾ú', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_edu_abroad', '¾Ç¾ú°ê§O', 'varchar(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_sub_kind', '±Ð®vÀË©w¬ì', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_check_kind', 'ÀË©w¸ê®æ', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_check_word', '±Ð®vÃÒ®Ñµn°O¦r¸¹', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'teach_is_cripple', '¬O§_»â¦³´Ý»Ù¤â¥U', 'char(2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'update_time', '', 'timestamp(14)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_base', 'update_id', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'teach_id', '±Ð®v¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'email', '¹q¤l¶l¥ó¤@', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'email2', '¹q¤l¶l¥ó¤G', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'email3', '¹q¤l¶l¥ó¤T', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'selfweb', '­Ó¤H­º­¶¤@', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'selfweb2', '­Ó¤H­º­¶¤G', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'classweb', '¯Z¯Å­º­¶¤@', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'classweb2', '¯Z¯Å­º­¶¤G', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_connect', 'ICQ', 'ICQ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_table', 'd_table_name', '¸ê®Æªí¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_table', 'd_table_cname', '¤¤¤å¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_table', 'd_table_group', '¸s²Õ¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_field', 'd_table_name', '¸ê®Æªí¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_field', 'd_field_name', 'Äæ¦ì¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_field', 'd_field_cname', '¤¤¤åÄæ¦ì¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_field', 'd_field_type', 'Äæ¦ì«¬ºA', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_field', 'd_field_order', '±Æ§Ç', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_field', 'd_is_display', '¬O§_Åã¥Ü', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('sys_data_field', 'd_field_xml', 'XML TAG', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'pro_kind_id', '¼Ò²Õ½s¸¹', 'smallint(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'pro_kind_name', '¼Ò²Õ¦WºÙ', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'pro_kind_order', '±Æ¦C¦¸§Ç', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'home_index', '­º­¶µ{¦¡', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'store_path', 'µ{¦¡¸ô®|', 'varchar(200)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'pro_author', '§@ªÌ', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'pro_parent', '¤W¼h¼Ò²Õ½s¸¹', 'smallint(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'pro_isopen', '¤¹³\\¶i¤J', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_kind', 'pro_islive', '¬O§_±Ò¥Î', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check_stu', 'pc_id', '»{ÃÒ¬y¤ô¸¹', 'int(11)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check_stu', 'pro_kind_id', '¼Ò²Õ½s¸¹', 'smallint(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check_stu', 'stud_id', '¾Ç¥Í¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check_stu', 'teach_id', '±ÂÅv±Ð®v¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check_stu', 'use_date', '±Ò¥Î¤é´Á', 'datetime', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check_stu', 'use_last_date', 'µ²§ô±Ò¥Î¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check_stu', 'class_num', '¯Z¯Å½s¸¹', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check', 'pc_id', '»{ÃÒ¬y¤ô¸¹', 'bigint(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check', 'pro_kind_id', '¼Ò²Õ½s¸¹', 'smallint(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check', 'post_office', '©Ò¦b³B«Ç½s¸¹', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check', 'teach_id', '±Ð®v¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check', 'teach_title_id', 'Â¾ºÙ¥N¸¹', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_check', 'is_admin', '¬O§_¬°ºÞ²zªÌ', 'char(1)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'teach_id', '±Ð®v¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'post_kind', 'Â¾§O½s¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'post_office', '©Ò¦b³B«Ç½s¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'post_level', 'Â¾µ¥', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'official_level', '©xµ¥', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'post_class', 'Â¾¯Å', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'post_num', '¥»Â¾¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'bywork_num', '­ÝÂ¾¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'salay', 'Á~­Ä', 'mediumint(9)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'appoint_date', 'ªì¥ôÂ¾¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'arrive_date', '¨ìÂ¾¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'approve_date', '¥ôÂ¾®Ö­ã¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'approve_number', '¥ôÂ¾®Ö­ã¤å¸¹', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'teach_title_id', 'Â¾ºÙ', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'class_num', '¥ô±Ð¯Z¯Å', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'update_time', '§ó·s®É¶¡', 'timestamp(14)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('teacher_post', 'update_id', '§ó·sªÌID', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_id', '¾Ç®Õ¥N¸¹', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_attr_id', '¾Ç®ÕÄÝ©Ê', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_cname', '¥þ»Î', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_cname_s', 'Â²ºÙ', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_cname_ss', 'µuºÙ', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_ename', '­^¤å¦WºÙ', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_sheng', '©Ò¦b¿¤¥«', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_cdate', '«Ø®Õ¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_mark', 'µù°O', 'varchar(8)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_class', '¯Å§O', 'varchar(8)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_montain', '¤s¦aÃÑ§O', 'char(2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_area_tol', '®Õ¦aÁ`­±¿n', 'float(10,2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_area_ext', '®Õ¦aÁ`©µ­±¿n', 'float(10,2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_area_pin', '«Ø©W­±¿n', 'float(10,2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_money', '¸ê¥»ªù¤ä¥X', 'float(10,2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_money_o', '¸g±`ªù¤ä¥X', 'float(10,2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_local_name', '¶mÂí¥«°Ï§O', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_post_num', '¶l»¼°Ï¸¹', 'varchar(5)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_addr', '¾Ç®Õ¦a§}', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_phone', '¾Ç®Õ¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_fax', '¶Ç¯u', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_area', '¾Ç®Õ¦æ¬F°Ï', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_kind', '¾Ç®ÕÃþ«¬', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_url', '¾Ç®Õºô§}', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'sch_email', '¹q¤l¶l¥ó', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'update_time', '', 'datetime', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'update_id', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_base', 'update_ip', '', 'varchar(15)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'enable', '¬O§_¦s¦b', 'enum(\'1\',\'0\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'c_year', '¦~', 'tinyint(2) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'c_name', '¯Z¦W', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'c_kind', 'Ãþ«¬', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'c_sort', '¯Z¦W±Æ§Ç', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'class_sn', '¬y¤ô¸¹', 'mediumint(8) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'class_id', '¯Z¯Å¥N¸¹', 'varchar(11)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'year', '¾Ç¦~«×', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_class', 'semester', '¾Ç´Á', 'enum(\'1\',\'2\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module', 'pm_id', '¬y¤ô¸¹', 'bigint(11)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module', 'pm_name', '¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module', 'pm_item', '¿ï¶µ', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module', 'pm_memo', '»¡©ú', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module', 'pm_value', '³]©w­È', 'varchar(100)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module_main', 'pm_name', 'µ{¦¡¥N¸¹', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module_main', 'm_display_name', 'µ{¦¡¦WºÙ', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module_main', 'm_group_name', '¸s²Õ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module_main', 'm_ver', 'ª©¥»', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module_main', 'm_create_date', '«Ø¥ß®É¶¡', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('pro_module_main', 'm_path', '¹w³]¦s©ñ¥Ø¿ý', 'varchar(60)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'seme_year_seme', '¾Ç¦~¾Ç´Á', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'stud_id', '¾Ç¥Í¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_p_relation', '¤÷¥ÀÃö«Y', 'varchar(8)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_air', '®a®x®ðª^', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_edu_fath', '¤÷ºÞ±Ð¤è¦¡', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_edu_moth', '¥ÀºÞ±Ð¤è¦¡', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_live', '©~¦í±¡§Î', 'varchar(12)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_camer', '¸gÀÙª¬ªp', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_sub_like', '³Ì³ß·R¬ì¥Ø', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_sub_diff', '³Ì§xÃø¬ì¥Ø', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_spec', '¯S®í¤~¯à', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_hobby', '¿³½ì', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_habit', '²ßºD', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_relation', '¤H»ÚÃö«Y', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_behave_o', '¥~¦V¦æ¬°', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_behave_i', '¤º¦V¦æ¬°', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_behave_edu', '¾Ç²ß¦æ¬°', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_habit_bad', '¤£¨}²ßºD', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_behave_agi', 'µJ¼{¦æ¬°', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_temp1', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'guid_temp2', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'update_time', '', 'datetime', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guidance', 'update_id', '', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_id', '­Ó®×½s¸¹', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_from', '­Ó®×¨Ó·½', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_bdate', '±µ®×¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_teacher', '»{»²¦Ñ®v', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_kind', '°ÝÃDÃþ§O', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_behave', '°¾®t¦æ¬°', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_reason', '¾AÀ³¤£¨}­ì¦]', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_isover', 'µ²®×§_', 'char(2)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_over_reason', 'µ²®×¤é´Á', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'guid_c_edate', '»²¾É¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'update_time', '§ó·s®É¶¡', 'datetime', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case', 'update_id', '§ó·sªÌ¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case_list', 'guid_l_id', '¬y¤ô¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case_list', 'guid_c_id', '­Ó®×½s¸¹', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case_list', 'guid_l_date', '»²¾É¤é´Á', 'date', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case_list', 'guid_l_con', '»²¾É¤º', 'varchar(40)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case_list', 'update_id', '§ó·sªÌ¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case_u', 'guid_u_id', '­Ó®×½s¸¹', 'bigint(20) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('stud_guid_case_u', 'guid_c_id', 'ÃöÁp½s¸¹', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'current_school_year', '¾Ç¦~¾Ç´Á', 'smallint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'teach_id', '±Ð®v¥N¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'teach_title_id', '±Ð®vÂ¾ºÙ¥N¸¹', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'class_num', '¥ô±Ð¯Z¯Å', 'varchar(6)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'subject_id1', '¥ô±Ð¬ì¥Ø¤@', 'tinyint(3)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'subject_id2', '¥ô±Ð¬ì¥Ø¤G', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'subject_id3', '¥ô±Ð¬ì¥Ø¤T', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'subject_id4', '¥ô±Ð¬ì¥Ø¥|', 'tinyint(3)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'subject_id5', '¥ô±Ð¬ì¥Ø¤­', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('seme_class', 'subject_id6', '¥ô±Ð¬ì¥Ø¤»', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_room', 'room_id', '³B«Ç½s¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_room', 'room_name', '³B«Ç¦WºÙ', 'varchar(30)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_room', 'room_tel', '³B«Ç¹q¸Ü', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_room', 'room_fax', '³B«Ç¶Ç¯u', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('school_room', 'enable', '¬O§_§@¥Î', 'enum(\'1\',\'0\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment', 'serial', '¬y¤ô¸¹', 'smallint(8) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment', 'teacher_id', '±Ð®v¥N¸¹', 'int(8) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment', 'subject', '¬ì¥Ø', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment', 'property', '', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment', 'kind', 'Ãþ§O', 'tinyint(2) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment', 'level', 'µ¥¯Å', 'varchar(10)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment', 'comm', 'µû»y¤º®e', 'varchar(200)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment_kind', 'kind_serial', '¬y¤ô¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment_kind', 'kind_teacher_id', '±Ð®v¥N¸¹', 'int(8) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment_kind', 'kind_name', 'Ãþ§O¦WºÙ', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment_level', 'level_serial', '¬y¤ô¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment_level', 'level_teacher_id', '±Ð®v¥N¸¹', 'int(8) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('comment_level', 'level_name', 'µ¥¯Å', 'varchar(50)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_sn', '¬y¤ô¸¹', 'smallint(5) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'interface_sn', '¬É­±¥N¸¹', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_text', 'Äæ¦ì¦WºÙ', 'varchar(255)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_value', 'Äæ¦ì¿ï¶µ', 'varchar(255)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_type', 'Äæ¦ì±±¨î¶µ', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_fn', '¨ú­È¨ç¦¡', 'varchar(255)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_ss', '¬ì¥Ø¬ÛÃö', 'enum(\'n\',\'y\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_comment', '©I¥s¿ï¶ñ', 'enum(\'n\',\'y\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_check', 'ÀË¬dªÅ­È', 'enum(\'0\',\'1\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'col_date', '«Ø¥ß®É¶¡', 'datetime', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_col', 'enable', '¬O§_§@¥Î', 'enum(\'1\',\'0\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_interface', 'interface_sn', '¼Ëª©¥N¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_interface', 'title', '¼ÐÃD', 'varchar(255)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_interface', 'text', '»¡©ú', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_interface', 'html', '', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_interface', 'sshtml', '', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_interface', 'xml', '', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_interface', 'all_ss', '', 'enum(\'n\',\'y\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_value', 'sc_sn', '¬y¤ô¸¹', 'int(10) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_value', 'interface_sn', '¬É­±¥N¸¹', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_value', 'date', '«Ø¥ß¤é´Á', 'datetime', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_value', 'stud_id', '¾Ç¸¹', 'varchar(20)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_value', 'class_id', '¯Z¯Å¥N¸¹', 'varchar(11)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_value', 'value', '­È', 'text', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_value', 'sel_year', '¾Ç¦~', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_input_value', 'sel_seme', '¾Ç´Á', 'enum(\'1\',\'2\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'setup_id', '¬y¤ô¸¹', 'smallint(5) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'year', '¾Ç¦~', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'semester', '¾Ç´Á', 'enum(\'1\',\'2\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'class_year', '¦~¯Å', 'tinyint(2) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'performance_test_times', '©w´Á¦Ò¬d¦¸¼Æ', 'tinyint(1) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'practice_test_times', '¹w³]¦Ò¸Õ¦¸¼Æ', 'tinyint(2) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'test_ratio', '¤ñ²v', 'varchar(255)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'rule', 'µ¥²Ä³]©w', 'varchar(255)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'score_mode', '¦U¦~¯Å±Ä¥Î¬Û¦P³]©w', 'enum(\'all\',\'severally\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'sections', '¨C¤é¤W½Ò¸`¼Æ', 'tinyint(4)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'interface_sn', '¦¨ÁZ³æ¬É­±', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'update_date', '§ó·s®É¶¡', 'datetime', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_setup', 'enable', '¬O§_§@¥Î', 'enum(\'1\',\'0\',\'always\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_subject', 'subject_id', '¬ì¥Ø¬y¤ô¸¹', 'tinyint(3) unsigned', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_subject', 'subject_name', '¬ì¥Ø¦WºÙ', 'varchar(255)', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_subject', 'subject_school', '²Å¦X¬ì¥Ø¦~¯Å', 'set(\'0\',\'1\',\'2\',\'3\',\'4\',\'5\',\'6', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_subject', 'subject_kind', '¬ì¥ØÃþ§O(»â°ì©Î¾Ç¬ì)', 'enum(\'scope\',\'subject\')', 0, 0, '');
INSERT INTO sys_data_field VALUES ('score_subject', 'enable', '¬O§_§@¥Î', 'enum(\'1\',\'0\')', 0, 0, '');

#
# ¸ê®Æªí®æ¦¡¡G `sys_data_table`
#

CREATE TABLE sys_data_table (
  d_table_name varchar(30) NOT NULL default '',
  d_table_cname varchar(30) NOT NULL default '',
  d_table_group varchar(30) NOT NULL default '',
  PRIMARY KEY  (d_table_name),
  UNIQUE KEY d_table_name (d_table_name)
) TYPE=MyISAM COMMENT='¸ê®Æ®w¦WºÙ';

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `sys_data_table`
#

#
# Dumping data for table `sys_data_table`
#
INSERT INTO sys_data_table VALUES ('score_input_col', '¦¨ÁZ³æ¬É­±-Äæ¦ì', '¦¨ÁZ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('score_input_interface', '¦¨ÁZ³æ¬É­±', '¦¨ÁZ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('compclass', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('borrow', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('bookch1', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('book', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('board_p', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('board_kind', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('board_check', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('stud_guidance', '»²¾É°ò¥»¸ê®Æ', '¾Ç¥Í»²¾É¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_guid_case_u', '»²¾É­Ó®×¸ê®Æ-ÃöÁp°O¿ý', '¾Ç¥Í»²¾É¸ê®Æ');
INSERT INTO sys_data_table VALUES ('pro_check', '¼Ò²Õ»{ÃÒ', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('pro_check_stu', '¾Ç¥Í»{ÃÒ', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('pro_kind', '¼Ò²Õ¶µ¥Ø', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('stud_behabior', '', '¾Ç¥Í»²¾É¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_guid_case_list', '»²¾É­Ó®×¸ê®Æ-»²¾É¤º®e', '¾Ç¥Í»²¾É¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_guid_case', '»²¾É­Ó®×¸ê®Æ', '¾Ç¥Í»²¾É¸ê®Æ');
INSERT INTO sys_data_table VALUES ('pro_module_main', 'µ{¦¡³]©w°O¿ý', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('stud_base', '¾Ç¥Í°ò¥»¸ê®Æ', '¾Ç¥Í¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_brother_sister', '¾Ç¥Í¥S§Ì©j©f', '¾Ç¥Í¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_domicile', '¾Ç¥Í®a®x¸ê®Æ', '¾Ç¥Í¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_eduh', '»²¾É­Ó®×¸ê®Æ', '¾Ç¥Í¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_grad', '²¦­×·~¬ö¿ý', '¾Ç¥Í¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_kinfolk', '¨ä¥L¿ËÄÝ', '¾Ç¥Í¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_mid_abs', '¾Ç´Á¯Ê®u', '´Á¤¤¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_mid_rep', '´Á¤¤¼úÃg°O¿ý', '´Á¤¤¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_mid_sco', 'ªÀ¹Î¸ê®Æ', '´Á¤¤¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_move', '´Á¤¤²§°Ê', '´Á¤¤¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_seme', '­Ó§O¾Ç´Á¸ê®Æ', '¾Ç´Á¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_seme_abs', '¾Ç´Á¯Ê®u', '¾Ç´Á¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_seme_eduh', '¾Ç´Á»²¾É°ò¥»¸ê®Æ', '¾Ç´Á¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_seme_score', '¾Ç´Á¦¨ÁZ', '¾Ç´Á¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_seme_score_s', '¾Ç´Á¦¨´Á-¤À¶µ¦¨ÁZ', '¾Ç´Á¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_seme_spe', '¯S®íÀu¨}ªí²{', '¾Ç´Á¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_seme_talk', '¾Ç´Á»²¾É³X½Í°O¿ý', '¾Ç´Á¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_seme_test', '¤ß²z´úÅç¦¨ÁZ', '¾Ç´Á¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_sick_f', '¾Ç¥Í®a®x¯f¥v', '¾Ç¥Í°·±d¸ê®Æ');
INSERT INTO sys_data_table VALUES ('stud_sick_p', '¾Ç¥Í­Ó¤H¯f¥v', '¾Ç¥Í°·±d¸ê®Æ');
INSERT INTO sys_data_table VALUES ('subj_cour', '¦~¯Å¶}½ÒºûÅ@', '½Ò°ÈºÞ²z');
INSERT INTO sys_data_table VALUES ('subj_hour', '¤W½Ò®É¶¡³]©w', '½Ò°ÈºÞ²z');
INSERT INTO sys_data_table VALUES ('sys_data_field', '¨t²ÎÄæ¦ì', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('sys_data_table', '¨t²Î¸ê®Æªí', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('teacher_base', '±Ð®v°ò¥»¸ê®Æ', '±Ð®v¸ê®Æ');
INSERT INTO sys_data_table VALUES ('teacher_connect', '±Ð®vºô¸ô¸ê®Æ', '±Ð®v¸ê®Æ');
INSERT INTO sys_data_table VALUES ('teacher_post', '±Ð®v¥ôÂ¾¸ê®Æ', '±Ð®v¸ê®Æ');
INSERT INTO sys_data_table VALUES ('teacher_subject', '±Ð®v¥ô±Ð¬ì¥Ø', '±Ð®v¸ê®Æ');
INSERT INTO sys_data_table VALUES ('teacher_title', '±Ð®vÂ¾ºÙ¸ê®Æ', '±Ð®v¸ê®Æ');
INSERT INTO sys_data_table VALUES ('pro_module', 'µ{¦¡¿ï¶µ³]©w', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('school_class', '¯Z¯Å¸ê®Æ', '¾Ç®Õ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('school_base', '¾Ç®Õ°ò¥»¸ê®Æ', '¾Ç®Õ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('comment', 'µû»y', '¦¨ÁZ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('comment_kind', 'µû»yºØÃþ', '¦¨ÁZ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('comment_level', 'µû»yµ¥¯Å', '¦¨ÁZ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('docup', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('docup_p', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('exam', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('exam_kind', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('exam_stud', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('exam_stud_data', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('file_db', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('fixed_check', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('fixed_kind', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('fixedtb', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('form_all', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('form_col', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('form_fill_in', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('form_value', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('goodstu', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('gscore', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('inquire', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('new_board', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('quire_data', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('sch_doc1', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('sch_doc1_unit', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('school_board', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('soft', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('softm', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('stud_addr', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('tape', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('tapem', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('tool', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('toolm', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('webcal_entry', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('webcal_entry_repeats', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('webcal_entry_user', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('webcal_user', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('webcal_user_layers', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('webcal_user_pref', '', '®Õ°È¼Ò²Õ');
INSERT INTO sys_data_table VALUES ('sfs_log', '', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('sfs_text', '', '¨t²Î³]©w');
INSERT INTO sys_data_table VALUES ('school_class_num', '¾Ç¦~¯Z¯Å¼Æ', '¾Ç®Õ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('school_room', '³B«Ç¸ê®Æ', '¾Ç®Õ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('score_course', '¾Ç´Á±Æ½Ò°O¿ý', '½Ò°ÈºÞ²z');
INSERT INTO sys_data_table VALUES ('score_input_value', '¦¨ÁZ³æ¬É­±-­È', '¦¨ÁZ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('score_setup', '¦¨ÁZ°ò¥»³]©w', '¦¨ÁZ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('score_subject', '¾Ç¬ì¸ê®Æ', '¦¨ÁZ¸ê®Æ');
INSERT INTO sys_data_table VALUES ('seme_class', '¾Ç´Á¯Z¯Å°O¿ý', '¾Ç´Á¸ê®Æ');


INSERT INTO sfs_text VALUES ('550', 'non_display', 3, 0, '¤£Åã¥Ü¥Ø¿ý', '', 0, '');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 0, 'include', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 1, 'images', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 2, 'image', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 3, 'doc', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 4, 'upgrade', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 5, 'pass', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 6, 'setup', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 7, 'db', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 8, 'themes', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 9, 'docs', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 10, 'templates', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 11, 'util', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 12, 'includes', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 13, 'translations', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 14, 'pnadodb', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 15, 'updata', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 16, 'CVS', '550,', 550, '.');
INSERT INTO sfs_text VALUES ('', 'non_display', 3, 17, 'data', '550,', 550, '.');

CREATE TABLE IF NOT EXISTS compclass (
  pDate date NOT NULL default '0000-00-00',
  pMday tinyint(4) NOT NULL default '0',
  pCNum tinyint(4) NOT NULL default '0',
  pClass varchar(20) default NULL,
  pTeacher varchar(20) default NULL,
  teach_id varchar(20) default NULL,
  PRIMARY KEY  (pDate,pMday,pCNum)
) TYPE=MyISAM;


#
# ¸ê®Æªí®æ¦¡¡G `comment`
#

CREATE TABLE comment (
  serial smallint(8) unsigned NOT NULL auto_increment,
  teacher_id int(8) unsigned default NULL,
  subject tinyint(3) unsigned default NULL,
  property tinyint(3) unsigned default NULL,
  kind tinyint(2) unsigned default NULL,
  level varchar(10) NOT NULL default '',
  comm varchar(200) NOT NULL default '',
  PRIMARY KEY  (serial)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `comment`
#

INSERT INTO comment VALUES (1, 0, 0, 0, 2, '1', '¤O¾Ç¤£­Â«æ¤½¦n¸q');
INSERT INTO comment VALUES (2, 0, 0, 0, 2, '1', 'ªA°È¼ö¤ß«~¼wÀu¨}');
INSERT INTO comment VALUES (3, 0, 0, 0, 2, '1', '¶Ô¾Ä¦n¾Ç¼ö¤ß¤½¯q');
INSERT INTO comment VALUES (4, 0, 0, 0, 2, '1', '¼ö¸Û©MÄ§¿í¦u®Õ³W');
INSERT INTO comment VALUES (5, 0, 0, 0, 2, '1', '§V¤OÁ¾®¥´`³WÁÐ¯x');
INSERT INTO comment VALUES (6, 0, 0, 0, 2, '1', '¼ö¤ß¤½¯q«~¦æºÝ¥¿');
INSERT INTO comment VALUES (7, 0, 0, 0, 2, '1', '«~¦æºÝ¥¿¾Ç·~Àu¨}');
INSERT INTO comment VALUES (8, 0, 0, 0, 2, '1', '¶Ô«j¤£¾Ó´`³WÁÐ¯x');
INSERT INTO comment VALUES (9, 0, 0, 0, 2, '1', '§Ó¤Á¤W¶i«~¼wÀu¨}');
INSERT INTO comment VALUES (10, 0, 0, 0, 2, '1', 'ªA°È¼ö¤ßÅª®Ñ»{¯u');
INSERT INTO comment VALUES (11, 0, 0, 0, 2, '1', 'ÄY¦u®Õ³W¦æ¬°ÀËÂI');
INSERT INTO comment VALUES (12, 0, 0, 0, 2, '1', '±M¤ß¦V¾Ç¿w¹ê¦u¤À');
INSERT INTO comment VALUES (13, 0, 0, 0, 2, '1', '¦W¦C«e­T«~¾Ç­ÝÀu');
INSERT INTO comment VALUES (14, 0, 0, 0, 2, '1', '»á³ô³y´N¸ÛÀµ¿w¹ê');
INSERT INTO comment VALUES (15, 0, 0, 0, 2, '1', '¦³«í¦³¦u¶Ô¾Ä¦n¾Ç');
INSERT INTO comment VALUES (16, 0, 0, 0, 2, '1', '¼ö¤ßªA°È¦u³W»{¯u');
INSERT INTO comment VALUES (17, 0, 0, 0, 2, '1', '§V¤O¶i¨ú«~¦æÀu¨}');
INSERT INTO comment VALUES (18, 0, 0, 0, 2, '1', '¦n¾Ç¤£­Â¼ä¨­¦Û·R');
INSERT INTO comment VALUES (19, 0, 0, 0, 2, '1', '¤Í·R¦P¾Ç¼ö¤ß¤½¯q');
INSERT INTO comment VALUES (20, 0, 0, 0, 2, '1', '¥Í¬¡³W«ß¶Ô±Ó¦V¾Ç');
INSERT INTO comment VALUES (21, 0, 0, 0, 2, '1', '³B¨Æ¶Ô±Ó©Ê±¡©Mµ½');
INSERT INTO comment VALUES (22, 0, 0, 0, 2, '1', '³ô¥H³y´N¾Ç¦æ§¡Àu');
INSERT INTO comment VALUES (23, 0, 0, 0, 2, '1', '«~¾Ç­ÝÀu¼ö¤ß¤½°È');
INSERT INTO comment VALUES (24, 0, 0, 0, 2, '1', '¾Ç·~Àu¨}«~¦æºÝ¤è');
INSERT INTO comment VALUES (25, 0, 0, 0, 2, '1', '§Ó¦æ³ô¹Å¸ÛÀµ¾ë¹ê');
INSERT INTO comment VALUES (26, 0, 0, 0, 2, '1', 'Á¾®¥¦³Â§©Z¥Õª½²n');
INSERT INTO comment VALUES (27, 0, 0, 0, 2, '1', '¦æ¬°¨}¦n«ä·Q¯Â¥¿');
INSERT INTO comment VALUES (28, 0, 0, 0, 2, '1', '°µ¨Æ±Ó±¶©Ê±¡·Å¨}');
INSERT INTO comment VALUES (29, 0, 0, 0, 2, '1', '±Ë¤v¬°¸s¼ö¤ß¤½¯q');
INSERT INTO comment VALUES (30, 0, 0, 0, 2, '1', '½Ò·~»{¯u¬¡¼â¶i¨ú');
INSERT INTO comment VALUES (31, 0, 0, 0, 2, '1', '¼ö¤ß¤½¯q¨è­W­@³Ò');
INSERT INTO comment VALUES (32, 0, 0, 0, 2, '1', '«Ý¤H©M®ð«~¾Ç­ÝÀu');
INSERT INTO comment VALUES (33, 0, 0, 0, 2, '1', '§Ó¦æ¯Â¼ä©¾«p¶Ô¹²');
INSERT INTO comment VALUES (34, 0, 0, 0, 2, '1', '²`ª¾¦Û·R¨IÀR»{¯u');
INSERT INTO comment VALUES (35, 0, 0, 0, 2, '1', '³ß©ê¤£¥­´I¥¿¸q·P');
INSERT INTO comment VALUES (36, 0, 0, 0, 2, '1', '¤u§@§V¤O»ö®e¾ã¼ä');
INSERT INTO comment VALUES (37, 0, 0, 0, 2, '1', '¦æ¬°ºÝ²ø«ä·Q¯Â¥¿');
INSERT INTO comment VALUES (38, 0, 0, 0, 2, '1', '®IÀY¶i¨ú§V¤O¨Dª¾');
INSERT INTO comment VALUES (39, 0, 0, 0, 2, '1', 'ª¾¹L§Y§ï¾Üµ½©T°õ');
INSERT INTO comment VALUES (40, 0, 0, 0, 2, '1', '±M§Ó¦V¾Ç¦æ¬°ºÝ¥¿');
INSERT INTO comment VALUES (41, 0, 0, 0, 2, '1', '±M¤ß¦V¾ÇÄY¿w¦u¤À');
INSERT INTO comment VALUES (42, 0, 0, 0, 2, '1', '«~¾Ç­ÝÀu¼ö¤ß¬°¤½');
INSERT INTO comment VALUES (43, 0, 0, 0, 2, '1', 'Á¾®¥¦³Â§Áo©ú©MÄ§');
INSERT INTO comment VALUES (44, 0, 0, 0, 2, '1', '§¶§¶¤£­Â¶Ô¾Ç¦u³W');
INSERT INTO comment VALUES (45, 0, 0, 0, 2, '1', 'Â§»ª©P¨ìºë¯«®¶§@');
INSERT INTO comment VALUES (46, 0, 0, 0, 2, '1', '´Â®ð½´«k§@®§¦³§Ç');
INSERT INTO comment VALUES (47, 0, 0, 0, 2, '1', 'µ½¤_«Ý¤HªA°È¼ö¤ß');
INSERT INTO comment VALUES (48, 0, 0, 0, 2, '1', '¤Ñ©Êµ½¨}©¾«p¯Â¾ë');
INSERT INTO comment VALUES (49, 0, 0, 0, 2, '1', 'Áo©ú§D«W­è¼Ý²nª½');
INSERT INTO comment VALUES (50, 0, 0, 0, 2, '1', '§V¤O¶i¨ú«~¦æÀu¨}');
INSERT INTO comment VALUES (51, 0, 0, 0, 2, '1', '§J¤v§U¤H¦u³W¶Ô¾Ç');
INSERT INTO comment VALUES (52, 0, 0, 0, 2, '1', '¼ö¸Û©MÄ§¿í¦u®Õ³W');
INSERT INTO comment VALUES (53, 0, 0, 0, 2, '1', '³B¨Æ¶Ô±Ó©Ê±¡©Mµ½');
INSERT INTO comment VALUES (54, 0, 0, 0, 2, '1', '¤O¾Ç¤£­Â¶Ô¸ÛÃ­­«');
INSERT INTO comment VALUES (55, 0, 0, 0, 2, '1', '¦³³d¥ô·PªA°È¼ö¸Û');
INSERT INTO comment VALUES (56, 0, 0, 0, 2, '1', '¶Ô«j¤£¾Ó´`³WÁÐ¯x');
INSERT INTO comment VALUES (57, 0, 0, 0, 2, '1', '¬¡¼â¿Ë¤Á¯Â¾ë½ñ¹ê');
INSERT INTO comment VALUES (58, 0, 0, 0, 2, '1', '»á³ô³y´N§Ó¤Á¤W¶i');
INSERT INTO comment VALUES (59, 0, 0, 0, 2, '1', '§Ó¦æ³ô¹Å¬¡¼â¶i¨ú');
INSERT INTO comment VALUES (60, 0, 0, 0, 2, '1', '«ù¤§¦³«í¨è­W¦n¾Ç');
INSERT INTO comment VALUES (61, 0, 0, 0, 2, '1', 'ªA°È¼ö¤ß·q·~¼Ö¸s');
INSERT INTO comment VALUES (62, 0, 0, 0, 2, '1', '«~¾Ç¶W¸s®ð¦t°a©ù');
INSERT INTO comment VALUES (63, 0, 0, 0, 2, '1', '²`ª¾¦Û·R¨IÀR»{¯u');
INSERT INTO comment VALUES (64, 0, 0, 0, 2, '1', 'Á¾©M¦³Â§«~¾Ç­ÝÀu');
INSERT INTO comment VALUES (65, 0, 0, 0, 2, '1', '±M¤ß¦V¾Ç¸Û¿w¦u¤À');
INSERT INTO comment VALUES (66, 0, 0, 0, 2, '1', '«~¾Ç­ÝÀu¼ö¤ß¬°¤½');
INSERT INTO comment VALUES (67, 0, 0, 0, 2, '1', '¶Ô«j¦³¬°¼ö¤ß¤½°È');
INSERT INTO comment VALUES (68, 0, 0, 0, 2, '1', 'Á¾®¥¦³Â§Áo©ú©MÄ§');
INSERT INTO comment VALUES (69, 0, 0, 0, 2, '1', '§¶§¶¤£­Â¶Ô¾Ç¦u³W');
INSERT INTO comment VALUES (70, 0, 0, 0, 2, '1', '±Ë¤v¬°¸s¼ö¤ß¤½¯q');
INSERT INTO comment VALUES (71, 0, 0, 0, 2, '1', '¦³³d¥ô¤ß´I¥¿¸q·P');
INSERT INTO comment VALUES (72, 0, 0, 0, 2, '1', '¼ö¤ßªA°È¦u³W»{¯u');
INSERT INTO comment VALUES (73, 0, 0, 0, 2, '1', '¥ô³Ò¥ô«è©Z¥Õ¯Â¸Û');
INSERT INTO comment VALUES (74, 0, 0, 0, 2, '1', '¤O¹Ï¤W¶iºë²Ó¶g¸Ô');
INSERT INTO comment VALUES (75, 0, 0, 0, 2, '1', '¤u§@»{¯u«~¾Ç­ÝÀu');
INSERT INTO comment VALUES (76, 0, 0, 0, 2, '1', '«i©ó­t³d«~¾Ç­ÝÀu');
INSERT INTO comment VALUES (77, 0, 0, 0, 2, '1', '¦æ¬°ºÝ²ø«ä·Q¯Â¥¿');
INSERT INTO comment VALUES (78, 0, 0, 0, 2, '1', 'ª¾¹L§Y§ï¾Üµ½©T°õ');
INSERT INTO comment VALUES (79, 0, 0, 0, 2, '1', '«ù¤§¦³«í¨è­W¦n¾Ç');
INSERT INTO comment VALUES (80, 0, 0, 0, 2, '1', '¦X¸s¦uªk¶ÔÀµ¼ö¸Û');
INSERT INTO comment VALUES (81, 0, 0, 0, 2, '1', '®IÀY¶i¨ú§V¤O¨Dª¾');
INSERT INTO comment VALUES (82, 0, 0, 0, 2, '1', '±M§Ó¦V¾Ç¦æ¬°ºÝ¥¿');
INSERT INTO comment VALUES (83, 0, 0, 0, 2, '1', '§Ó¦æ³ô¹Å¸ÛÀµ¾ë¹ê');
INSERT INTO comment VALUES (84, 0, 0, 0, 2, '1', '«ñ¦u®Õ³W¨è­W¦ÛÀy');
INSERT INTO comment VALUES (85, 0, 0, 0, 2, '1', '¦Û°Ê¦Ûµo¶Ô«j¦V¤W');
INSERT INTO comment VALUES (86, 0, 0, 0, 2, '1', '©Ê±¡·Å©MªA°È¶Ô³Ò');
INSERT INTO comment VALUES (87, 0, 0, 0, 2, '1', '¯Õª½©¾¸Û¾Ä«j¦ÛÀy');
INSERT INTO comment VALUES (88, 0, 0, 0, 2, '1', '¼ö¤ß¤½¯qµê¤ß¾Ç²ß');
INSERT INTO comment VALUES (89, 0, 0, 0, 2, '1', '·q·~¼Ö¸s±l±l¦³Â§');
INSERT INTO comment VALUES (90, 0, 0, 0, 2, '1', '­t³dºÉÂ¾§¶§¶¾Ä«j');
INSERT INTO comment VALUES (91, 0, 0, 0, 2, '1', '¶Ô±Ó¦n¾ÇªÃ©Ê¯Â«p');
INSERT INTO comment VALUES (92, 0, 0, 0, 2, '1', 'ºÝ²ø¸Û¿w·Å©M¼_ÀR');
INSERT INTO comment VALUES (93, 0, 0, 0, 2, '1', '´`³WÁÐ¯x´°«~Ày¾Ç');
INSERT INTO comment VALUES (94, 0, 0, 0, 2, '1', '¤¬§U¦X§@ÂÔ¨¥·V¦æ');
INSERT INTO comment VALUES (95, 0, 0, 0, 2, '1', 'Áo¿o¶Ô±Ó¤Ñ¯u¿w¹ê');
INSERT INTO comment VALUES (96, 0, 0, 0, 2, '1', '°í­W¦ÛÀyªA°È¼ö¤ß');
INSERT INTO comment VALUES (97, 0, 0, 0, 2, '1', '¤Oª§¤W´å§V¤O¤£¾Ó');
INSERT INTO comment VALUES (98, 0, 0, 0, 2, '1', '´I¶i¨ú¤ß©|ª¾¦Û·R');
INSERT INTO comment VALUES (99, 0, 0, 0, 2, '1', '§JºÉÂ¾¦uºë©ú·F½m');
INSERT INTO comment VALUES (100, 0, 0, 0, 2, '1', '¥ô³Ò¥ô«è¤¬§U¦X§@');
INSERT INTO comment VALUES (101, 0, 0, 0, 2, '1', '¦u¥¿¤£ªü«~¦æºÝ²ø');
INSERT INTO comment VALUES (102, 0, 0, 0, 2, '1', '¸`»ü¾ë¹ê¨è­W¦ÛÀy');
INSERT INTO comment VALUES (103, 0, 0, 0, 2, '1', '­Ó©Ê¬¡¼â¸ê½è¿o®©');
INSERT INTO comment VALUES (104, 0, 0, 0, 2, '1', '«Ý¤H¸Û&#65533;°­Ó©Êµ½¨}');
INSERT INTO comment VALUES (105, 0, 0, 0, 2, '1', 'Á|¤î¦w¸ÔºÝ²ø«ù­«');
INSERT INTO comment VALUES (106, 0, 0, 0, 2, '1', '¶Ô¾Ç¤£­Â²ø­«¤j¤è');
INSERT INTO comment VALUES (107, 0, 0, 0, 2, '1', '§t»W¦³Â§©¾«p¶ÔÀµ');
INSERT INTO comment VALUES (108, 0, 0, 0, 2, '1', 'Áo©ú¤j¤è¶ÔÀµ©Z²v');
INSERT INTO comment VALUES (109, 0, 0, 0, 2, '1', '»áª¾¶i¨ú¾Ç²ß±M¤ß');
INSERT INTO comment VALUES (110, 0, 0, 0, 2, '1', '¼ä¨­¦Û·R¦w¤À¦u¤v');
INSERT INTO comment VALUES (111, 0, 0, 0, 2, '1', '¯àª¾¦Û·R¦u¬ö¦X¸s');
INSERT INTO comment VALUES (112, 0, 0, 0, 2, '1', '¨IÀR¦ÛÀy¸Û¹ê¯Â¾ë');
INSERT INTO comment VALUES (113, 0, 0, 0, 2, '1', '»ö®e¾ã¼ä¦Û«j¦ÛÀy');
INSERT INTO comment VALUES (114, 0, 0, 0, 2, '1', '­è¼Ý¨IµÛ»ö®eºÝ²ø');
INSERT INTO comment VALUES (115, 0, 0, 0, 2, '1', '¦uªk­«¬ö§JºÉÂ¾³d');
INSERT INTO comment VALUES (116, 0, 0, 0, 2, '1', '¦Û°Ê¦ÛÄ±¦uªk­«¬ö');
INSERT INTO comment VALUES (117, 0, 0, 0, 2, '1', '®ð«×°ª©|·Å¤åº¸¶®');
INSERT INTO comment VALUES (118, 0, 0, 0, 2, '1', 'Á¾®¥¦³Â§ºA«×ÄYÂÔ');
INSERT INTO comment VALUES (119, 0, 0, 0, 2, '1', '«Ý¤H©MÄ§¾÷´¼±Ó±¶');
INSERT INTO comment VALUES (120, 0, 0, 0, 2, '1', '§V¤O¤£¾Ó·Å©M¯Â¾ë');
INSERT INTO comment VALUES (121, 0, 0, 0, 2, '1', '«i©ó§U¤H©¾«p¸Û¹ê');
INSERT INTO comment VALUES (122, 0, 0, 0, 2, '1', '´°«p¸Û¹ê¨IÀq¹è¨¥');
INSERT INTO comment VALUES (123, 0, 0, 0, 2, '1', '¶Ô¾Ä¤£¾Ó»áª¾¶i¨ú');
INSERT INTO comment VALUES (124, 0, 0, 0, 2, '1', '¤O¨D¤W¶i¤åÀR¤j¤è');
INSERT INTO comment VALUES (125, 0, 0, 0, 2, '1', '¯Âµ½¥i·R¼ÖÆ[¦X¸s');
INSERT INTO comment VALUES (126, 0, 0, 0, 2, '1', '¨Dª¾¤ß¤Á¬¡¼âÁo©ú');
INSERT INTO comment VALUES (127, 0, 0, 0, 2, '1', 'ªA°È¶Ô«j¼ö¤ß¤½¯q');
INSERT INTO comment VALUES (128, 0, 0, 0, 2, '1', '°í¼ÝªG¨M«i©ó­t³d');
INSERT INTO comment VALUES (129, 0, 0, 0, 2, '1', '´`³WÁÐ¯xªÃ©Ê¯Â«p');
INSERT INTO comment VALUES (130, 0, 0, 0, 2, '1', '¼Ö©ó§U¤H¼ö¤ßªA°È');
INSERT INTO comment VALUES (131, 0, 0, 0, 2, '1', '¤O¾Ç¤£¾ÓÁ¾©M¦³Â§');
INSERT INTO comment VALUES (132, 0, 0, 0, 2, '1', '¿w¦æ­@³Ò¦w¤À¦u¤v');
INSERT INTO comment VALUES (133, 0, 0, 0, 2, '2', '©|ª¾»{¯uÂÔ¨¥·V¦æ');
INSERT INTO comment VALUES (134, 0, 0, 0, 2, '2', '©|ª¾§V¤O©Z¥Õ¸ÛÀµ');
INSERT INTO comment VALUES (135, 0, 0, 0, 2, '2', '©|ªÖ¾Ç²ß¦u¤À¦Ûªv');
INSERT INTO comment VALUES (136, 0, 0, 0, 2, '2', '¤½¨p¤À©ú©¾«pÃ­­«');
INSERT INTO comment VALUES (137, 0, 0, 0, 2, '2', '¦u¯´§Ç·R¾ã¼ä');
INSERT INTO comment VALUES (138, 0, 0, 0, 2, '2', '½Ò·~¥­¥­«~¦æºÝ¥¿');
INSERT INTO comment VALUES (139, 0, 0, 0, 2, '2', '¾Ç·~¥ç¨Î«ä·Q¯Â¥¿');
INSERT INTO comment VALUES (140, 0, 0, 0, 2, '2', '¥çª¾»{¯uÁ¾®¥¦³Â§');
INSERT INTO comment VALUES (141, 0, 0, 0, 2, '2', 'µê¤ß¾Ç²ß¦w¤À¦u¬ö');
INSERT INTO comment VALUES (142, 0, 0, 0, 2, '2', '¨IÀq¹è¨¥¦u³W¦n¾Ç');
INSERT INTO comment VALUES (143, 0, 0, 0, 2, '2', 'Â§»ª©P¨ìªA°È§V¤O');
INSERT INTO comment VALUES (144, 0, 0, 0, 2, '2', '¾Ç·~©|¨Î¦w¤À¦u¤v');
INSERT INTO comment VALUES (145, 0, 0, 0, 2, '2', '©|¯à¦X¸sÂÔ¨¥·V¦æ');
INSERT INTO comment VALUES (146, 0, 0, 0, 2, '2', '¾Ç²ß»{¯u©Ê±¡·Å©M');
INSERT INTO comment VALUES (147, 0, 0, 0, 2, '2', 'ªA°È¼ö¤ßÂ§»ª©P¨ì');
INSERT INTO comment VALUES (148, 0, 0, 0, 2, '2', '«Ý¤H¦³Â§¯À©Ê¬y§Q');
INSERT INTO comment VALUES (149, 0, 0, 0, 2, '2', '©|ª¾¥Î¤ß«ä·Q¯Â¥¿');
INSERT INTO comment VALUES (150, 0, 0, 0, 2, '2', 'ªA°È¼ö¤ß©|ª¾¶Ô¾Ç');
INSERT INTO comment VALUES (151, 0, 0, 0, 2, '2', '©|¯à¦X¸s´`³WÁÐ¯x');
INSERT INTO comment VALUES (152, 0, 0, 0, 2, '2', 'ª¾¹L¯à§ï»á¦³»{ÃÑ');
INSERT INTO comment VALUES (153, 0, 0, 0, 2, '2', '«~¾Ç©|¨Îª¾Â§¦uªk');
INSERT INTO comment VALUES (154, 0, 0, 0, 2, '2', '¤£ª¾»{¯u¸ê½è¼z±Ó');
INSERT INTO comment VALUES (155, 0, 0, 0, 2, '2', '¾Ç·~©|¨Î¼Ö©ó§U¤H');
INSERT INTO comment VALUES (156, 0, 0, 0, 2, '2', '¾Ç·~©|¨ÎÅé®æ°·¥þ');
INSERT INTO comment VALUES (157, 0, 0, 0, 2, '2', '§ó«Ý¶i¨ú¾Ç²ß©|¨Î');
INSERT INTO comment VALUES (158, 0, 0, 0, 2, '2', '¯àÃÑ¤jÅé©¾«pÃ­­«');
INSERT INTO comment VALUES (159, 0, 0, 0, 2, '2', '¾Ç²ßµê¤ß«~¦æºÝ¥¿');
INSERT INTO comment VALUES (160, 0, 0, 0, 2, '2', '³ô¥H³y´N¥»½è¯Â¥¿');
INSERT INTO comment VALUES (161, 0, 0, 0, 2, '2', '©|ª¾§V¤O¶ÔÂÔ¦Û¦u');
INSERT INTO comment VALUES (162, 0, 0, 0, 2, '2', '¹J¨Æ¼ÖÆ[¨IµÛ§V¤O');
INSERT INTO comment VALUES (163, 0, 0, 0, 2, '2', '©|ª¾¾Ä«j¶Ô³Ò§J­W');
INSERT INTO comment VALUES (164, 0, 0, 0, 2, '2', '©|¯à«ß¤v©Ê±¡­Ï±j');
INSERT INTO comment VALUES (165, 0, 0, 0, 2, '2', 'µy¤í¶Ô¾Ä©Ê±¡«O¦u');
INSERT INTO comment VALUES (166, 0, 0, 0, 2, '2', '©|ª¾¶Ô¾ÄªÃ©Ê©¾¸Û');
INSERT INTO comment VALUES (167, 0, 0, 0, 2, '2', 'µy¤í¬¡¼â¨IÀR¦n¾Ç');
INSERT INTO comment VALUES (168, 0, 0, 0, 2, '2', '©|¶Ô¤_¾Ç¼ö¤ß¤½¯q');
INSERT INTO comment VALUES (169, 0, 0, 0, 2, '2', '¾Ç¦æ©|¨ÎÅé®æ°·¥þ');
INSERT INTO comment VALUES (170, 0, 0, 0, 2, '2', '¾Ç²ß»{¯u©Ê±¡·Å©M');
INSERT INTO comment VALUES (171, 0, 0, 0, 2, '2', 'ÀH¹J¦Ó¦w©Ê±¡©M¶¶');
INSERT INTO comment VALUES (172, 0, 0, 0, 2, '2', '©|ª¾§V¤O¦w¤À¦u¤v');
INSERT INTO comment VALUES (173, 0, 0, 0, 2, '2', '¥çª¾¦X¸s¶Ô»ü¦u³W');
INSERT INTO comment VALUES (174, 0, 0, 0, 2, '2', '¶Ô©ó¾Ç²ß¼ö¤ß¤½°È');
INSERT INTO comment VALUES (175, 0, 0, 0, 2, '2', '¿Wµ½¨ä¨­¦u³W»{¯u');
INSERT INTO comment VALUES (176, 0, 0, 0, 2, '2', '©|ª¾»{¯u¦uªkª¾Â§');
INSERT INTO comment VALUES (177, 0, 0, 0, 2, '2', '­×¾iµy®t©Ê±¡ª½²v');
INSERT INTO comment VALUES (178, 0, 0, 0, 2, '2', '¼Ö©ó§U¤H¤u§@­t³d');
INSERT INTO comment VALUES (179, 0, 0, 0, 2, '2', '¾Ç·~©|¨Î¦w¤À¦u¯x');
INSERT INTO comment VALUES (180, 0, 0, 0, 2, '2', '²¤¤í¿n·¥¶Ô»ü¦V¾Ç');
INSERT INTO comment VALUES (181, 0, 0, 0, 2, '2', '©|ª¾§V¤O¥­±e¦Û¦u');
INSERT INTO comment VALUES (182, 0, 0, 0, 2, '2', '§ó«Ý¶i¨ú¾Ç¦æ©|¨Î');
INSERT INTO comment VALUES (183, 0, 0, 0, 2, '2', '¿í¦u®Õ³W¦w¤À¦u¤v');
INSERT INTO comment VALUES (184, 0, 0, 0, 2, '2', '³ô¥H³y´N«ä·Q¯Â¥¿');
INSERT INTO comment VALUES (185, 0, 0, 0, 2, '2', 'ªA°È¼ö¤ßÂ§»ª©P¨ì');
INSERT INTO comment VALUES (186, 0, 0, 0, 2, '2', '­×¾iµy®t©Ê±¡ª½²v');
INSERT INTO comment VALUES (187, 0, 0, 0, 2, '2', '¾Ç·~¥­§¡©|¿í®Õ³W');
INSERT INTO comment VALUES (188, 0, 0, 0, 2, '2', '©úÃÑ¤jÅé©¾«pÃ­­«');
INSERT INTO comment VALUES (189, 0, 0, 0, 2, '2', '©|¯à­t³d©Ê±¡­Ï±j');
INSERT INTO comment VALUES (190, 0, 0, 0, 2, '2', '©|¯à­t³d¼ö¤ß¤½°È');
INSERT INTO comment VALUES (191, 0, 0, 0, 2, '2', 'µê¤ß¾Ç²ß¦w¤À¦u¤v');
INSERT INTO comment VALUES (192, 0, 0, 0, 2, '2', 'µy¤í¶Ô¾Ä©Ê±¡«O¦u');
INSERT INTO comment VALUES (193, 0, 0, 0, 2, '2', '«Ý¤H¦³Â§ªÃ©Ê·Å©M');
INSERT INTO comment VALUES (194, 0, 0, 0, 2, '2', '©|¶Ô©ó¾Ç·q·R®vªø');
INSERT INTO comment VALUES (195, 0, 0, 0, 2, '2', '¶Ô©ó¾Ç²ß¼ö¤ß¤½°È');
INSERT INTO comment VALUES (196, 0, 0, 0, 2, '2', '©|ª¾»{¯uÂÔ¨¥·V¦æ');
INSERT INTO comment VALUES (197, 0, 0, 0, 2, '2', '©|ª¾¶Ô¾Ç¦u¤À¦Ûªv');
INSERT INTO comment VALUES (198, 0, 0, 0, 2, '2', '©|ª¾¾Ä«j¶Ô³Ò¨è­W');
INSERT INTO comment VALUES (199, 0, 0, 0, 2, '2', '©|ª¾¶Ô¾ÄªÃ©Ê©¾¸Û');
INSERT INTO comment VALUES (200, 0, 0, 0, 2, '2', '²¤¤í¿n·¥¶Ô»ü¦V¾Ç');
INSERT INTO comment VALUES (201, 0, 0, 0, 2, '2', '¹J¨Æ¼ÖÆ[¨IµÛ§V¤O');
INSERT INTO comment VALUES (202, 0, 0, 0, 2, '2', '¿Wµ½¨ä¨­¦u³W»{¯u');
INSERT INTO comment VALUES (203, 0, 0, 0, 2, '2', '¸ê½è¥­±e¦u¤v¼Ö¸s');
INSERT INTO comment VALUES (204, 0, 0, 0, 2, '2', '¾Ç·~©|¨ÎÁ¾®¥¦³Â§');
INSERT INTO comment VALUES (205, 0, 0, 0, 2, '2', '¦u¯´§Ç·R¾ã¼ä');
INSERT INTO comment VALUES (206, 0, 0, 0, 2, '2', '©|ª¾¦n¾Ç·q¤Í¼Ö¸s');
INSERT INTO comment VALUES (207, 0, 0, 0, 2, '2', '¾Ç·~©|¨Î¼Ö©ó§U¤H');
INSERT INTO comment VALUES (208, 0, 0, 0, 2, '2', '¤£ª¾»{¯u¸ê½è±Ó¼z');
INSERT INTO comment VALUES (209, 0, 0, 0, 2, '2', '¾Ç·~¥­¥­«~©|ºÝ¥¿');
INSERT INTO comment VALUES (210, 0, 0, 0, 2, '2', '¾Ç·~¥ç¨Î«ä·Q¯Â¥¿');
INSERT INTO comment VALUES (211, 0, 0, 0, 2, '2', '¶Ô©ó¾Ç·~©Z¥Õ¸ÛÀµ');
INSERT INTO comment VALUES (212, 0, 0, 0, 2, '2', '¨IÀq¹è¨¥¦u³W¶Ô¾Ç');
INSERT INTO comment VALUES (213, 0, 0, 0, 2, '2', '©|ª¾¾Äµo¶Ô³Ò­t³d');
INSERT INTO comment VALUES (214, 0, 0, 0, 2, '2', '¤¬§U¦X¸s¶Ô¾ë¦u³W');
INSERT INTO comment VALUES (215, 0, 0, 0, 2, '2', '©|ª¾§V¤O¦uªkª¾Â§');
INSERT INTO comment VALUES (216, 0, 0, 0, 2, '2', '«Ý¤H¦³Â§¤Ñ¯u¬¡¼â');
INSERT INTO comment VALUES (217, 0, 0, 0, 2, '2', '¼ÖÆ[¦n°Ê¨D¾Ç¤£¤O');
INSERT INTO comment VALUES (218, 0, 0, 0, 2, '2', '¨IµÛ°í©w©|¯à¦Ûªv');
INSERT INTO comment VALUES (219, 0, 0, 0, 2, '2', '»{¯u¤£°÷³gª±¦n°Ê');
INSERT INTO comment VALUES (220, 0, 0, 0, 2, '2', '»á¦³²z´¼«i©ó§ï¹L');
INSERT INTO comment VALUES (221, 0, 0, 0, 2, '2', '·R¦n¬¡°Ê­èª½¤£ªü');
INSERT INTO comment VALUES (222, 0, 0, 0, 2, '2', 'µy¶û¯B°Ê¼ä¨­¦Û·R');
INSERT INTO comment VALUES (223, 0, 0, 0, 2, '2', '¯Ê¥F¦Ûªv¤ÏÀ³¿ð¶w');
INSERT INTO comment VALUES (224, 0, 0, 0, 2, '2', '©|ª¾¥»¥÷·N§Ó¤£±M');
INSERT INTO comment VALUES (225, 0, 0, 0, 2, '2', '©|ª¾¶i¨ú¤Ñ¯u¬¡¼â');
INSERT INTO comment VALUES (226, 0, 0, 0, 2, '2', '»áª¾¶i¨ú¬¡¼â¦X¸s');
INSERT INTO comment VALUES (227, 0, 0, 0, 2, '2', 'µy¤í¬¡¼â¨IÀR¦n¾Ç');
INSERT INTO comment VALUES (228, 0, 0, 0, 2, '2', '«~¾Ç©|¨Îª¾Â§¦uªk');
INSERT INTO comment VALUES (229, 0, 0, 0, 2, '2', '©|ª¾»{¯u°ß¤Ñ¸êµy®t');
INSERT INTO comment VALUES (230, 0, 0, 0, 2, '2', '«~¼w»P¾Ç·~¸û«e¶i¨B');
INSERT INTO comment VALUES (231, 0, 0, 0, 2, '2', '©Ê±¡²nª½±¤¥¼±M¤ß½Ò·~');
INSERT INTO comment VALUES (232, 0, 0, 0, 2, '2', '¾Ç·~©|¥i±©¤£ª¾·V¨¥');
INSERT INTO comment VALUES (233, 0, 0, 0, 2, '2', '©|ª¾»{¯u¯Ê¥F¦X§@ºë¯«');
INSERT INTO comment VALUES (234, 0, 0, 0, 2, '2', '¾Ç·~©|¨Î±©¦æ°Ê¤£«K');
INSERT INTO comment VALUES (235, 0, 0, 0, 2, '2', '©|ª¾»{¯u±©¤Ñ¸ê¸û®t');
INSERT INTO comment VALUES (236, 0, 0, 0, 2, '2', '§V¤O¤£°÷´¼¼z¦³¾l');
INSERT INTO comment VALUES (237, 0, 0, 0, 2, '2', '±¡ºüÃP¦¢¨IÀq¹è¨¥');
INSERT INTO comment VALUES (238, 0, 0, 0, 2, '2', '©Ê¦n¼^À¸¦Ûª¾ÀËÂI');
INSERT INTO comment VALUES (239, 0, 0, 0, 2, '2', '©|ª¾¾Ä«j©Ê±¡­èª½');
INSERT INTO comment VALUES (240, 0, 0, 0, 2, '3', '¦æ¬°¥¢ÀË¥Í¬¡©ñ¿º');
INSERT INTO comment VALUES (241, 0, 0, 0, 2, '3', '¦æ¬°¥¢ÀË¼Ò½k¤£²M');
INSERT INTO comment VALUES (242, 0, 0, 0, 2, '3', '¤£­t³d¥ô±À½Ó¼Å­l');
INSERT INTO comment VALUES (243, 0, 0, 0, 2, '3', '¦¨ÁZ¤í¨Î¤£ª¾§V¤O');
INSERT INTO comment VALUES (244, 0, 0, 0, 2, '3', '¤£¨D¤W¶i©Ê±¡¹x¦H');
INSERT INTO comment VALUES (245, 0, 0, 0, 2, '3', '¤£ª¾¾ÄµoÃi´kµäÃû');
INSERT INTO comment VALUES (246, 0, 0, 0, 2, '3', '¤£ª¾®¶§@ºë¯«µäÃû');
INSERT INTO comment VALUES (247, 0, 0, 0, 2, '3', 'µ{«×¤´®tÁöª¾§V¤O');
INSERT INTO comment VALUES (248, 0, 0, 0, 2, '3', '¤£ª¾§V¤O¦æ¬°ÀH«K');
INSERT INTO comment VALUES (249, 0, 0, 0, 2, '3', '¤£µ½¥æ¹C¿Wµ½¨ä¨­');
INSERT INTO comment VALUES (250, 0, 0, 0, 2, '3', '¤£¨D¤W¶i·N§ÓÀZ­ð');
INSERT INTO comment VALUES (251, 0, 0, 0, 2, '3', '¤£ª¾§V¤O¸ê½è¥­±e');
INSERT INTO comment VALUES (252, 0, 0, 0, 2, '3', '¦³©lµL²×¦³¤ß¾Ç²ß');
INSERT INTO comment VALUES (253, 0, 0, 0, 2, '3', '¾Ç·~¥­¥­¨­Åé°I®z');
INSERT INTO comment VALUES (254, 0, 0, 0, 2, '3', '¨¥¦æ¤£¤@¤½¨p¤£¤À');
INSERT INTO comment VALUES (255, 0, 0, 0, 2, '3', '¤£ª¾»{¯u§ë¾÷¨ú¥©');
INSERT INTO comment VALUES (256, 0, 0, 0, 2, '3', '¨¥¦æ¤£²Å»{ÃÑ¤£²M');
INSERT INTO comment VALUES (257, 0, 0, 0, 2, '3', '©y¥[ÀËÂI¦æ°ÊÀH«K');
INSERT INTO comment VALUES (258, 0, 0, 0, 2, '3', '¤£µ½«Ý¤H¾ÇÃÑ©|¨Î');
INSERT INTO comment VALUES (259, 0, 0, 0, 2, '3', '¤£ª¾µ½¸s©Ê±¡©t»÷');
INSERT INTO comment VALUES (260, 0, 0, 0, 2, '3', '¾Ç¤£¦w¤ß©Ê±¡¯BÄ¼');
INSERT INTO comment VALUES (261, 0, 0, 0, 2, '3', '¤£ª¾¦X¸s¨¥¦æ²Ê³¥');
INSERT INTO comment VALUES (262, 0, 0, 0, 2, '3', 'µL¤½¼w¤ß¨£§Q§Ñ¸q');
INSERT INTO comment VALUES (263, 0, 0, 0, 2, '3', '¤£ª¾²r¿ô¤p¹L¹ð¥Ç');
INSERT INTO comment VALUES (264, 0, 0, 0, 2, '3', '´dÆ[®ø·¥ªA¸Ë¤£¾ã');
INSERT INTO comment VALUES (265, 0, 0, 0, 2, '3', '¤£Å¥±Ð»£©T°õ¦Û«H');
INSERT INTO comment VALUES (266, 0, 0, 0, 2, '3', 'µL¤ß»{¯uºë¯«µA´²');
INSERT INTO comment VALUES (267, 0, 0, 0, 2, '3', '²@µLªø¶i¶§©^³±¹H');
INSERT INTO comment VALUES (268, 0, 0, 0, 2, '3', '±o¹L¥B¹LÎA¥B°½¦w');
INSERT INTO comment VALUES (269, 0, 0, 0, 2, '3', '¤£ª¾ªø¶iÃi´k¶ú§ª');
INSERT INTO comment VALUES (270, 0, 0, 0, 2, '3', '¤ÏÂÐµL±`·N§Ó¤£°í');
INSERT INTO comment VALUES (271, 0, 0, 0, 2, '3', '²@µL¶i¨Bµ{«×§C¦H');
INSERT INTO comment VALUES (272, 0, 0, 0, 2, '3', 'Â§¸`¤£¶g´²º©ÃP¾Ó');
INSERT INTO comment VALUES (273, 0, 0, 0, 2, '3', 'µL¦P±¡¤ß©¯¨a¼Öº×');
INSERT INTO comment VALUES (274, 0, 0, 0, 2, '3', '¤£ª¾®¶§@¼Ç®ð¨I¨I');
INSERT INTO comment VALUES (275, 0, 0, 0, 2, '3', '¾Ç·~¤í¨Î¹ð¥Ç®Õ³W');
INSERT INTO comment VALUES (276, 0, 0, 0, 2, '3', '¤£ª¾§V¤OÃi´k¯î²¨');
INSERT INTO comment VALUES (277, 0, 0, 0, 2, '3', '®É¾Ç®É½ù¤@¼É¤Q´H');
INSERT INTO comment VALUES (278, 0, 0, 0, 2, '3', '¦æ¬°´²º©·N§ÓÀZ­ð');
INSERT INTO comment VALUES (279, 0, 0, 0, 2, '3', '¤£±M½Ò·~¤ß¦³¥~õ­');
INSERT INTO comment VALUES (280, 0, 0, 0, 2, '3', '¤£ª¾®¶§@«H¤ß³à¥¢');
INSERT INTO comment VALUES (281, 0, 0, 0, 2, '3', 'µL¤½¼w¤ß¦Û¨p¦Û§Q');
INSERT INTO comment VALUES (282, 0, 0, 0, 2, '3', '¤£¨D¤W¶iµäÃû¤£®¶');
INSERT INTO comment VALUES (283, 0, 0, 0, 2, '3', '¤£­t³d¥ôÃi´k¼Å­l');
INSERT INTO comment VALUES (284, 0, 0, 0, 2, '3', '«j¯àÀ³¥I©|ª¾§V¤O');
INSERT INTO comment VALUES (285, 0, 0, 0, 2, '3', 'Âø¶ÃµL³¹Ãi´k¦¨©Ê');
INSERT INTO comment VALUES (286, 0, 0, 0, 2, '3', '¤£³ô±Ð»£«V°d®vªø');
INSERT INTO comment VALUES (287, 0, 0, 0, 2, '3', '½Ò·~¯î²¨·R¬Ý¤p»¡');
INSERT INTO comment VALUES (288, 0, 0, 0, 2, '3', '¤£¨D¶i¯q¦æ¬°¦Û¨p');
INSERT INTO comment VALUES (289, 0, 0, 0, 2, '3', '¤£ª¾®¬§ï¹ð¥Ç¤p¿ù');
INSERT INTO comment VALUES (290, 0, 0, 0, 2, '3', '±o¹L¥B¹L¤£ª¾»{¯u');
INSERT INTO comment VALUES (291, 0, 0, 0, 2, '3', '¾Ç·~°h¨Bºë¯«µäÃû');
INSERT INTO comment VALUES (292, 0, 0, 0, 2, '3', '¤£ª¾§V¤O¦æ¬°ÀH«K');
INSERT INTO comment VALUES (293, 0, 0, 0, 2, '3', '¤£¨D¤W¶iºë¯«¤À´²');
INSERT INTO comment VALUES (294, 0, 0, 0, 2, '3', '¤ß¦³¤G¥Î¤£¦u³W«h');
INSERT INTO comment VALUES (295, 0, 0, 0, 2, '3', '©|«Ý±Òµo¤Ñ¸ê¿ð¶w');
INSERT INTO comment VALUES (296, 0, 0, 0, 2, '3', '¾Ç²ß¤£±Mºë¯«µäÃû');
INSERT INTO comment VALUES (297, 0, 0, 0, 2, '3', '¦³©lµL²×¥Î¤ß¤£±M');
INSERT INTO comment VALUES (298, 0, 0, 0, 2, '3', 'Åª®Ñ¤£±M¤ß¤í¨IÀR');
INSERT INTO comment VALUES (299, 0, 0, 0, 2, '3', '¤£µ½¦X¸s¾Ç·~©|¨Î');
INSERT INTO comment VALUES (300, 0, 0, 0, 2, '3', '¤í¶i¨úºë¯«');
INSERT INTO comment VALUES (301, 0, 0, 0, 2, '3', '¤£¨D¬Æ¸Ñ¥Î¤ß¤£±M');
INSERT INTO comment VALUES (302, 0, 0, 0, 2, '3', '½Ò·~¥­¥­©|¦u³W¯x');
INSERT INTO comment VALUES (303, 0, 0, 0, 2, '3', '¥B¤£»{¯u¤Ñ¸ê²Â©å');
INSERT INTO comment VALUES (304, 0, 0, 0, 2, '3', '¤£¦w©ó¾Ç©Ê±¡¯BÄ¼');
INSERT INTO comment VALUES (305, 0, 0, 0, 2, '3', '¦¨ÁZ¤í¨Î©Ê±¡Ãi´k');
INSERT INTO comment VALUES (306, 0, 0, 0, 2, '3', '¤£ª¾¦ÛÀË­Ó©Ê­Ï±j');
INSERT INTO comment VALUES (307, 0, 0, 0, 2, '3', '½Ò·~¸¨«áªí­±¼Å­l');
INSERT INTO comment VALUES (308, 0, 0, 0, 2, '3', '®É¥Íª§°õ©Ê±¡¹x¥Ö');
INSERT INTO comment VALUES (309, 0, 0, 0, 2, '3', '¤£ª¾»{¯uºë¯«µA´²');
INSERT INTO comment VALUES (310, 0, 0, 0, 2, '3', '¦Û¥Ì¸¨«á¤£ª¾¶Ô¾Ç');
INSERT INTO comment VALUES (311, 0, 0, 0, 2, '3', '¤£»{¯u¦n¼^À¸');
INSERT INTO comment VALUES (312, 0, 0, 0, 2, '3', '©Ê±¡Ãi´k¨¥¤£ÅU¦æ');
INSERT INTO comment VALUES (313, 0, 0, 0, 2, '3', '¤£ª¾¦Û·R¦Û¨p¦Û§Q');
INSERT INTO comment VALUES (314, 0, 0, 0, 2, '3', '©Ê±¡­Ï±jºë¯«µäÃû');
INSERT INTO comment VALUES (315, 0, 0, 0, 2, '3', '¼Å­l°¨ªê«å´kÃi´²');
INSERT INTO comment VALUES (316, 0, 0, 0, 2, '3', '¯BÄ¼µL«í³x±j¦n³Ó');
INSERT INTO comment VALUES (317, 0, 0, 0, 2, '3', '¾Ç¤£±M¤ß¥~°È¤Ó¦h');
INSERT INTO comment VALUES (318, 0, 0, 0, 2, '3', '¶i¨B½wºC©Ê±¡©M¶¶');
INSERT INTO comment VALUES (319, 0, 0, 0, 2, '3', '¤£ª¾ÀË°Q®É¥Ç¤p¿ù');
INSERT INTO comment VALUES (320, 0, 0, 0, 2, '3', '´ý´ý¾µ¾µ©Ê±¡À¶®z');
INSERT INTO comment VALUES (321, 0, 0, 0, 2, '3', '¤£¤Á¹ê»Ú³B¨Æ°¨ªê');
INSERT INTO comment VALUES (322, 0, 0, 0, 2, '3', '¤£«ä¤W¶i®ð½è»´¯B');
INSERT INTO comment VALUES (323, 0, 0, 0, 2, '3', '½Ò·~¸¨«á¹J¨Æ¼Å­l');
INSERT INTO comment VALUES (324, 0, 0, 0, 2, '3', '«å©¿¾Ç·~©Ê±¡²nª½');
INSERT INTO comment VALUES (325, 0, 0, 0, 2, '3', '¶§©^³±¹H¤£Å¥³WÄU');
INSERT INTO comment VALUES (326, 0, 0, 0, 2, '3', '¶ÆºCµLÂ§¤£¨D¤W¶i');
INSERT INTO comment VALUES (327, 0, 0, 0, 2, '3', '¥Í¬¡¤£ÀËµê¯B¤£¹ê');
INSERT INTO comment VALUES (328, 0, 0, 0, 2, '3', 'µL¤ß¦V¾Çµê¯BÀH«K');
INSERT INTO comment VALUES (329, 0, 0, 0, 2, '3', '»´¯B¦n°Ê¦]´`ª±©¿');
INSERT INTO comment VALUES (330, 0, 0, 0, 2, '3', '¹ð»|¤£Ñª¨¥¦æµê°°');
INSERT INTO comment VALUES (331, 0, 0, 0, 2, '3', '¹x¥Ö¦n°Ê­Ó©Ê©t»÷');
INSERT INTO comment VALUES (332, 0, 0, 0, 2, '3', 'ºA«×­Ð¶Æ©Ê±¡²Ê¼É');
INSERT INTO comment VALUES (333, 0, 0, 0, 2, '4', 'Ãø±æ³y´N­Ó©Ê¹x¦H');
INSERT INTO comment VALUES (334, 0, 0, 0, 2, '4', '¯}Ãa¯´§ÇµL²z¨ú¾x');
INSERT INTO comment VALUES (335, 0, 0, 0, 2, '4', '¨¥¦æ¤£²Å¤f¬O¤ß«D');
INSERT INTO comment VALUES (336, 0, 0, 0, 2, '4', '¹ð»|¹ð¥Ç¯}Ãa®Õ³W');
INSERT INTO comment VALUES (337, 0, 0, 0, 2, '4', '¤£¦u¥»¤À¦n«i´þ¨Æ');
INSERT INTO comment VALUES (338, 0, 0, 0, 2, '4', '¦Û¥Ì¼Z¸¨Ãi´²¤£®¶');
INSERT INTO comment VALUES (339, 0, 0, 0, 2, '4', '¾Ç·~¥ç¦H«~¼w¤í¨Î');
INSERT INTO comment VALUES (340, 0, 0, 0, 2, '4', '¤£¦u®Õ³W¼^À¸¦n¾x');
INSERT INTO comment VALUES (341, 0, 0, 0, 2, '4', '¹ð¥Ç¹L¥¢¦æ¬°µLÀË');
INSERT INTO comment VALUES (342, 0, 0, 0, 2, '4', '¹ð»|¤£®©¦]´`«å´k');
INSERT INTO comment VALUES (343, 0, 0, 0, 2, '4', 'ªÈ¥¿¤£§ï«ä·QÂÕ»~');
INSERT INTO comment VALUES (344, 0, 0, 0, 2, '4', '¤£©ú¬O«D­J§@¶Ã¬°');
INSERT INTO comment VALUES (345, 0, 0, 0, 2, '4', '¦n«i°«¬½­Ó©Ê­Ï±j');
INSERT INTO comment VALUES (346, 0, 0, 0, 2, '4', '¤£°È½Ò·~Ãm½Ò¥Ç³W');
INSERT INTO comment VALUES (347, 0, 0, 0, 2, '4', '¶ÆºCµLÂ§©Ê±¡¨Ä»÷');
INSERT INTO comment VALUES (348, 0, 0, 0, 2, '4', 'ÂAª¾ÀËÂI¹x¥Ö¦¨©Ê');
INSERT INTO comment VALUES (349, 0, 0, 0, 2, '4', '¦n¨Æ¥Í­¸¤£ª¾¶Ô¾Ç');
INSERT INTO comment VALUES (350, 0, 0, 0, 2, '4', '¤£ª¾¦Û·R¦æ¬°¥¢ÀË');
INSERT INTO comment VALUES (351, 0, 0, 0, 2, '4', '¤£¦u¯´§Çºë¯«µA´²');
INSERT INTO comment VALUES (352, 0, 0, 0, 2, '4', '¤£¦ÛÀË§ô©ñ¸v»´¯B');
INSERT INTO comment VALUES (353, 0, 0, 0, 2, '4', '¦Û¼É¦Û±ó¨Ä±i¹x¦H');
INSERT INTO comment VALUES (354, 0, 0, 0, 2, '4', '®É¥Ç®Õ³W¤£Å¥°V»£');
INSERT INTO comment VALUES (355, 0, 0, 0, 2, '4', '¹ð»|¤£Ñª¹x¦H¤£¤Æ');
INSERT INTO comment VALUES (356, 0, 0, 0, 2, '4', '¤£ª¾®¬§ï¦æ¬°¨Ä±i');
INSERT INTO comment VALUES (357, 0, 0, 0, 2, '4', '¾Ç¤£±M¤ß¤ß©Ê¯BÄ¼');
INSERT INTO comment VALUES (358, 0, 0, 0, 2, '4', '©Ê¦æ¹x¦H·l¤H§Q¤v');
INSERT INTO comment VALUES (359, 0, 0, 0, 2, '4', '¤£ª¾¤W¶iªÃ©Ê¹x¦H');
INSERT INTO comment VALUES (360, 0, 0, 0, 2, '4', '¤£ª¾»{¯u©Ê±¡¹x¦H');
INSERT INTO comment VALUES (361, 0, 0, 0, 2, '4', '¤£¦uÂ§»öºA«×¶ÆºC');
INSERT INTO comment VALUES (362, 0, 0, 0, 2, '4', '¤£ª¾®¶§@ÀZ­ðÃi´k');
INSERT INTO comment VALUES (363, 0, 0, 0, 2, '4', '¤£¦u®Õ³WÂZ¶Ã¯´§Ç');
INSERT INTO comment VALUES (364, 0, 0, 0, 2, '4', 'ºë¯«µäÃû¥Í¬¡®öº©');
INSERT INTO comment VALUES (365, 0, 0, 0, 2, '4', '¦æ¬°©ñ¿º­Ó©Ê¾îÆZ');
INSERT INTO comment VALUES (366, 0, 0, 0, 2, '4', 'ºë¯«ÀZ¼o¥Í¬¡´²º©');
INSERT INTO comment VALUES (367, 0, 0, 0, 2, '4', '¤£ª¾®¬§ï¨¥¦æ¯îÂÕ');
INSERT INTO comment VALUES (368, 0, 0, 0, 2, '4', '¾Ç·~¯î²¨¨¥¦æ´Û¶B');
INSERT INTO comment VALUES (369, 0, 0, 0, 2, '4', '¦n¶h´c³Ò±jµü¦nÅG');
INSERT INTO comment VALUES (370, 0, 0, 0, 2, '4', '¤£­«¤½¼wºë¯«´²º©');
INSERT INTO comment VALUES (371, 0, 0, 0, 2, '4', '¦h¨¥³gª±ºë¯«¤£®¶');
INSERT INTO comment VALUES (372, 0, 0, 0, 2, '4', '¤£¯à¦X¸s­Ó©Ê©t»÷');
INSERT INTO comment VALUES (373, 0, 0, 0, 2, '4', '¤£ª¾»{¯uÃi´k¦¨©Ê');
INSERT INTO comment VALUES (374, 0, 0, 0, 2, '4', '¯Ê¥F«í¤ßÅª®Ñ¤£±M');
INSERT INTO comment VALUES (375, 0, 0, 0, 2, '4', '¤£Å¥°V»|©Ê±¡­è´p');
INSERT INTO comment VALUES (376, 0, 0, 0, 2, '4', '½Ò·~¤í¨Îºë¯«µäÃû');
INSERT INTO comment VALUES (377, 0, 0, 0, 2, '4', '¥Í¬¡ÃP¾Óºë¯«¯BÄ¼');
INSERT INTO comment VALUES (378, 0, 0, 0, 2, '4', '©Ê±¡¨Ä©Ñ¨­Åéµê®z');
INSERT INTO comment VALUES (379, 0, 0, 0, 2, '4', 'µL¤ßÅª®ÑÁo©ú¦³¾l');
INSERT INTO comment VALUES (380, 0, 0, 0, 2, '4', '¥Í¬¡´²º©¤£©ë¤p¸`');
INSERT INTO comment VALUES (381, 0, 0, 0, 3, '1', '¤ÏÀ³±Ó±¶¡AÁ|¤@¯à¤Ï¤T¡A¯à«Ü§Öªº¶i¤J¦UºØ¾Ç²ß±¡¹Ò¡A°ß¦³®É½k¶î¶·±`±`´£¬Ù¡C');
INSERT INTO comment VALUES (382, 0, 0, 0, 3, '1', '©Ê±¡éw«p¡A½Ò·~¤w¦³¶i¨B¡A¤£¹L¤´»Ý§V¤O¡A¦rÅé¤íºÝ¥¿¡A­n¦n¦n½m²ß¡C');
INSERT INTO comment VALUES (383, 0, 0, 0, 3, '1', '¤Ñ½á¤£¿ù¡A§V¤O¤£°÷¡A¼Æ²z¤è­±ªí²{¸û¨Î¡A»y¤å¸û®t¡A»Ý­n¦n¦n½m²ß¡C');
INSERT INTO comment VALUES (384, 0, 0, 0, 3, '1', '¬¡¼â¦n°Ê¡A°Ê¦p²æ¨ß¡A°ß«ÜÃø¦wÀR¤U¨Ó¡C½Ò·~¤w¦³¶i¨B¡A¦ý¤´¥¼ºÉ¥þ¤O¡A§_«hªí²{·|§ó¦n¡C');
INSERT INTO comment VALUES (385, 0, 0, 0, 3, '1', '­Ó©ÊÄYµÂ¡A¤£ÎA¨¥¯º¡F¤£¹L°µ¨Æ¦³ÂI¨à¯ó²v¡A§V¤O¤£°÷¡A¤´¶·±`±`¤©¥H·þ¾É¡C');
INSERT INTO comment VALUES (386, 0, 0, 0, 3, '1', '¬¡°Ê¤O©ô²±¡A¦³¥Î¤£§¹ªººë¤O¡A«ÜÃø©w¤ß©w©Ê¡A¾Ç²ß¤w¦³¶i¨B¡C');
INSERT INTO comment VALUES (387, 0, 0, 0, 3, '1', '¾Ç²ßº¥¤J¨Î¹Ò¡A¦ý«Ü®e©ö¤À¤ß¡A¶·®É®É¥[¥H·þ¾É¡C');
INSERT INTO comment VALUES (388, 0, 0, 0, 3, '1', '©Ê±¡²v¯u¡B©¾¹ê¡A¤£µ½±»¹¢¡C¨D¾Ç¥¼ºÉ¥þ¤O¡A§_«hÀ³¦³§ó¦nªºªí²{¡C');
INSERT INTO comment VALUES (389, 0, 0, 0, 3, '1', '¤Ñ¥Í¼ÖÆ[¡A¼ö¤ß¤½°È¡A¼Ö©ó§U¤H¡A°ß¨D¾Ç¥¼ºÉ¥þ¤O¡AÀ³¦A§V¤O¡C');
INSERT INTO comment VALUES (390, 0, 0, 0, 3, '1', '¬¡¼â¤j¤è¡A°µ¨Æ²Ê²v¡A½Ò·~ªí²{¨Î¡A¤­¨|§¡¿Åµo®i¤~¬O¨}µ¦¡A©y°ö¾i¨ä¥L¤è­±ªº¿³½ì¡C');
INSERT INTO comment VALUES (391, 0, 0, 0, 3, '1', '­Ó©Êéw«p¡A¤£µ½»P¤Hª§¡A°ß°µ¨Æ¤£°÷¥J²Ó¡A®e©ö¤À¤ß¡A»Ý®É®É¤©¥H·þ¾É¡BÃöÃh¡C');
INSERT INTO comment VALUES (392, 0, 0, 0, 3, '1', '¤ÏÀ³±Ó±¶¡A±`ÃÑÂ×´I¡Aµoªí¯à¤O»á±j¡A¦ý¨D¾ÇºA«×¤£°÷»{¯u¡B¥J²Ó¡A¤Ñ¸êÁö¦n¡A¤´¶·¾a«á¤Ñ§V¤O¡C');
INSERT INTO comment VALUES (393, 0, 0, 0, 3, '1', '­Ó©Ê¬¡¼â¤j¤è¡A«Ü·|µoªí¦Û¤vªº·N¨£¡CªA°È¼ö¤ß¡A¦ý¦³ÂI¨à²Ê¤ß¤j·N¡C');
INSERT INTO comment VALUES (394, 0, 0, 0, 3, '1', '¤Ñ½á¤£¿ù¡A­Y¦A¥[¤W«á¤Ñªº§V¤O¡Aªí²{¤@©w§ó¦n¡F¥u¬O³ã³ã¤£¥ð¡A«Ü·R»¡¸Ü¡A­n±`±`´£¿ô¡C');
INSERT INTO comment VALUES (395, 0, 0, 0, 3, '1', '¬¡¼â¦n°Ê¡A«Ü·R»¡¸Ü¡A»P¤Í¦ñ¬Û³B´r§Ö¡A¤£¹L¼g¦r¤Ó§Ö¡A¥H­P©ó¦³ÂI¨à¯ó²v¡C');
INSERT INTO comment VALUES (396, 0, 0, 0, 3, '1', '¬¡¼â±Ó±¶¡A°µ¨Æ¶Ô§Ö¡A¤â¸}«W¸¨¡A¨D¾Ç»á»{¯u¡A¦rÅé¤]«ÜºÝ¥¿¡C');
INSERT INTO comment VALUES (397, 0, 0, 0, 3, '1', '¤Ñ¸ê¤£±Ó¡A¤S¤£ªÖ¦n¦nªº§V¤O¡A¸ò¤£¤W¦P¾Çªº¶i«×¡F¶·¦b®a¤¤¹ê¬I­Ó§Oªº«ü¾É¡A¤~¯à¸ò¤W¶i«×¡C');
INSERT INTO comment VALUES (398, 0, 0, 0, 3, '1', '¬¡¼â¦n°Ê¡A¬¡°Ê¶q»á¤j¡A¨D¾ÇºA«×¤w¨£¶i¨B¡A¥i¿×º¥¤J¨Î¹Ò¡A°ß¼g¦r¤´µM¤£°÷ºÝ¾ã  ¡C');
INSERT INTO comment VALUES (399, 0, 0, 0, 3, '1', '¤ÏÀ³±Ó±¶¡Aµ½©ó´x´¤±¡¹Ò¡A¨D¾Ç»{¯u¡A¦rÅéºÝ¾ã¡A¦bÃ¸µe¤è­±ªí²{»á¨Î¡C');
INSERT INTO comment VALUES (400, 0, 0, 0, 3, '1', '©Ê±¡éw«p¡B¦Ñ¹ê¡A¦b¤½¦@³õ¦X¤ñ¸û¤£´±µo¨¥¡A¦ý¨p©³¤U¡A«o«Ü®e©ö»P¤H¥´¦¨¤@¤ù¡A»Ý¦h°ö¾iµo¨¥«i®ð¡C');
INSERT INTO comment VALUES (401, 0, 0, 0, 3, '1', '¨D¾Ç»{¯u¡AºA«×»á¨Î¡A°µ¨Æ¥ç¦³³d¥ô·P¡A°ß°£¤FÅª®Ñ¤§¥~¡A©y¦A°ö¾i¨ä¥L¤è­±ªº¿³½ì¡C');
INSERT INTO comment VALUES (402, 0, 0, 0, 3, '1', '©¾«p¦Ñ¹ê¡A¤Q¤Àéw«p¡A¥Í©Ê¼ÖÆ[¡A¼ö¤ß¤½¦@ªA°È¡A¦b½Ò·~¤WÀ³¦h°µ§V¤O¡C');
INSERT INTO comment VALUES (403, 0, 0, 0, 3, '1', '­Ó©Ê¤w¸û¬¡¼â¡B¦n°Ê¡A»P¤Í¦ñ¬Û³B¿Ä¬¢¡A¨D¾ÇºA«×¥ç¨Î¡C');
INSERT INTO comment VALUES (404, 0, 0, 0, 3, '1', '¤Ñ¯uÄêº©°Q¤H³ß¡A°µ¨Æ«Ü±Mª`¡A¤£®e©ö¤À¤ß¡A¦³©l¦³²×¡A¤S«Ü¯à´x´¤±¡¹Ò°µ¾A«×ªºªí²{¡C');
INSERT INTO comment VALUES (405, 0, 0, 0, 3, '1', '­Ó©ÊÀH©M¡A¹ï¦Û¤vªºªF¦è«ÜÀ´±o·R±¤¡F¦b½Ò·~¤W§V¤O¤£°÷¡A©y¦A·þ¾É¡C');
INSERT INTO comment VALUES (406, 0, 0, 0, 3, '1', '­Ó©Ê¶¢´²¡A¤£©ö¶°¤¤ª`·N¤O¡A»Ý±`±`´£¿ô¡B·þ¾É¤~·|¦³©Ò¦¨¡C');
INSERT INTO comment VALUES (407, 0, 0, 0, 3, '1', '­Ó©Ê¬¡¼â¤j¤è¡A°ß§V¤O¤£°÷¡A­Y¯à©w¤ß©ó½Ò·~¤W¡A·í¤£¥u©ó¦¹¡C');
INSERT INTO comment VALUES (408, 0, 0, 0, 3, '1', '¬¡¼â¤j¤è¡A¥æ¹C¼sÁï¡A»P¤H¥æªB¤Í«Ü§Ö¡A½Ò·~»á¦³¶i®i¡A­È±o¹Å³\\¡C');
INSERT INTO comment VALUES (409, 0, 0, 0, 3, '1', 'Äõ¤ß¼z½è¡A¼_¶®·Å¬X¡A»á¦³¤j®a»Ó¨qªº­·½d¡A½Ò·~ªí²{¥ç¨Î¡A¥i¿×«~¾Ç­ÝÀu¡C');
INSERT INTO comment VALUES (410, 0, 0, 0, 3, '1', '¤Ñ½á¤£¿ù¡A¤]»áªÖ§V¤O¡Aªí²{©|¨Î¡A¬°¤H©M¼Ö¡A¼Ö»P¤H¥æ¡A¥B¼ö¤ß§U¤H¡A«Ü±o¤H½t¡C');
INSERT INTO comment VALUES (411, 0, 0, 0, 3, '1', '­Ó©Ê·Å©M¡Bµ½¨}¡A¤Ñ¸ê¤£±Ó¡A»Ý¾a«á¤Ñ¤£Â_ªº§V¤O¡A¤~¥i¯à¦³©Ò¦¨¡A¤£Â_ªºÃöª`¤Î­@¤ßªº»²¾É¤´¬O»Ý­nªº¡C');
INSERT INTO comment VALUES (412, 0, 0, 0, 3, '1', '²Ê¤ß¤j·N¡A±`±`½k¶î¡A¤Ñ¸ê§Y©Î¤£±Ó¡A¶·¾a«á¤Ñªº§V¤O¡B¤£Â_ªºÃöª`©M­Ó§O«ü¾É¡C');
INSERT INTO comment VALUES (413, 0, 0, 0, 3, '1', '¤Ñ½á±Ó±¶¡A«ä¸ô²M´·¡A¦rÅéºÝ¥¿¡A½Ò·~ªí²{¨Î¡A°ß«Ü·RÁ¿¸Ü¡A¶·±`±`´£¿ô¡C');
INSERT INTO comment VALUES (414, 0, 0, 0, 3, '1', '¤ß«ä²Ó±K¡A¦³¤p¤k«Äªº¼b¬X¡A½Ò·~ªí²{¥ç¨Î¡A°ß¦³®É²Ê¤ß¡A¤´­n±`¤©´£¿ô¡C');
INSERT INTO comment VALUES (415, 0, 0, 0, 3, '1', '¹x¥Ö¦n°Ê¡A¤Q¤À²^®ð¡A¬¡°Ê¶q«Ü¤j¡A½Ò·~¥¼ºÉ¥þ¤O¡A¼g¦r¤]«Ü¯ó²v¡C');
INSERT INTO comment VALUES (416, 0, 0, 0, 3, '1', '¾Ç²ß¤wº¥¤J¨Î¹Ò¡A°ß¤W½Ò«Ü®e©ö¤À¤ß¡A¶·±`±`¤©¥H´£¿ô¡C');
INSERT INTO comment VALUES (417, 0, 0, 0, 3, '1', '©Ê±¡·Å©M¡A¤£µ½»P¤Hª§¡A¤£¤ÓÄ@·N¦b¤½²³³õ¦Xªí²{¡A©y¦h¹ªÀy¡C');
INSERT INTO comment VALUES (418, 0, 0, 0, 3, '1', '¤ÏÀ³±Ó±¶¡A½Ò¥~ª¾ÃÑ«ÜÂ×´I¡A¬¡°Ê¶q«Ü¤j¡A°ß¨D¾ÇºA«×¤£°÷»{¯u¡A©y¦A¥[¥H¤Þ¾É¡C');
INSERT INTO comment VALUES (419, 0, 0, 0, 3, '1', '­Ó©Ê·Å¬X¡A¼_¶®µ½¨}¡A°µ¨Æ¥J²Ó¡A¨D¾Ç»{¯u¡A¥i¿×«~¾Ç­ÝÀu¡C');
INSERT INTO comment VALUES (420, 0, 0, 0, 3, '1', '¦wÀR»´¥©¡A¤£µ½¥æ»Ú¡A¾Ç·~¤w¶i¨B¡A­È±o¹Å³\\¡C');
INSERT INTO comment VALUES (421, 0, 0, 0, 3, '1', '¬¡¼â¦n°Ê¡A«Ü¦³ªíºt¼¤¡B»â¾É¼¤¡A°ß¤£°÷¥J²Ó¡A²Ê²v±o¥i·R¡C');
INSERT INTO comment VALUES (422, 0, 0, 0, 3, '1', '«ä¸ô±Ó±¶¡A«Ü¦³¥D°Ê¨Dª¾ªººë¯«¡A°µ¨Æ¥J²Ó¡A¬°¤H©Mµ½¡C');
INSERT INTO comment VALUES (423, 0, 0, 0, 3, '1', '¤­¨|§¡¿Åµo®i¡A¿³½ì¼sªx¡A¥i¿×¯à°Ê¯àÀR¡A·¥°Q¤H³ß·R¡C');
INSERT INTO comment VALUES (424, 0, 0, 0, 3, '1', '©Ê·Å°û¡A¤£¤Ó·R»¡¸Ü¡A¾Ç²ß¤w¦³¶i¨B¡A¦ý¤´¶·§V¤O¡A¤ñ¦P¦~ÄÖªº¤p«Ä¡AªÀ·|©Êªºµo®i¸û¬°¿ð½w¡C');
INSERT INTO comment VALUES (425, 0, 0, 0, 3, '1', '¤ß¦aµ½¨}¡A½Ò·~ªí²{©|¨Î¡AÅã¥Ü¨ä¥Î¤ßµ{«×¡A°ß¿³½ì¤£°÷¼sªx¡A©y¦h°ö¾i¡C');
INSERT INTO comment VALUES (426, 0, 0, 0, 3, '1', '·Å©M¨}µ½¡A­Ó©Ê²Û©ÄÁx¤p¡A¾Ç²ßº¥¦³¶i¨B¡AÄ~Äò§V¤O¡C');
INSERT INTO comment VALUES (427, 0, 0, 0, 3, '1', '©Ê±¡·Å©M¬X¶¶¡A½Ò·~©|¨Î¡A¹ïÃ¸µe«Ü¦³¿³½ì¡AÆ[¹î¤Q¤À²Ó±K¡A­Y¯à¥[¥H°ö¾i¡A¤é«á·í¦³©Ò¦¨¡C');
INSERT INTO comment VALUES (428, 0, 0, 0, 3, '1', '·Å©M¬X¶¶¡B©M®ð¦³Â§¡A§O¤H¤~·|³ßÅw±µªñ§A¡A¦pªG±`´Û«V¦P¾Ç¡A¨º§AªºªB¤Í´N·|·U¨Ó·U¤Ö¤F¡C');
INSERT INTO comment VALUES (429, 0, 0, 0, 3, '1', '¦¬°_¹x¥Ö¤§¤ß¡A±M¤ß©ó¾Ç·~¡A¤W½Ò¶·±M¤ß¡A¤u§@¶·­t³d¡A¤~¤£¶d­t¤÷¥À¤§´Á±æ¡I');
INSERT INTO comment VALUES (430, 0, 0, 0, 3, '1', '¨¯¶Ôªº¯Ñ¯Ð¤§«á¡A¥²¦³ÅwªYªº¦¬Ã¬¡IÁö¥¼¯à¦W¦C«e­T¡A¦ý¦¨ÁZ¤§Ã­©w»PÀu¶V¡A¨¬ÃÒ©p¬O¦³¼ç¤Oªº¡A¦A§V¤O§a¡I¯¬ºÖ©p¤U¦¸¦pÄ@¡C');
INSERT INTO comment VALUES (431, 0, 0, 0, 3, '1', '¤Ñ¯u©Z²v¡A¤u§@¼ö¤ß¡C½Ò·~­±¦p¯à²Ó¤ß¡B¶ÔÅª¡A¥²¥iÀò§ïµ½¡AÄ@©pÅé·|¤O¦æ¤§¡C');
INSERT INTO comment VALUES (432, 0, 0, 0, 3, '1', '¼Æ¾Ç¥u­n¦h¥Î¤ß¡A¦hºâ¡A¬Û«H©p¤]¥i¥H¦Ò±o¦n¡C');
INSERT INTO comment VALUES (433, 0, 0, 0, 3, '1', '°ß¶Ô¤~¯à¸É©å¡A¦hªá¨Ç®É¶¡¡A¬ã¨s¼Æ¾Ç¡A¥²¥i¶i¨B¡C');
INSERT INTO comment VALUES (434, 0, 0, 0, 3, '1', '¤Ñ¯u²vª½¡A¶Ô©óµo°Ý¡A¾Ç·~¬ð­¸²r¶i¡AÄ@¯à«ù¤§¥H«í¡A±N¨Ó¦b¨D¾Ç¤§¸ô¤W¡A¥²¦³¦¨´N¡C');
INSERT INTO comment VALUES (435, 0, 0, 0, 3, '1', 'à±´@¡G¦Ñ®v¦h»ò§Æ±æ©p¥­¤é¦h±µªñ§Ú¡A¦h°Ý§Ú½Ò·~¤Wªº°ÝÃD¡A­nª¾¹D¡A§Ú¬Ý¨ì©pÀRÀRªº¤£·R»¡¸Ü¡A§Ú¬O¦h»òÃø¹L§r¡I');
INSERT INTO comment VALUES (436, 0, 0, 0, 3, '1', '¾á¥ôÁ`°ÈªÑªø¡A¬°¦P¾ÇªA°È¡A¤u§@Áö­W¡A²@µL«è¨¥¡Aºë¯«¥i¹Å¡I¼Æ¾Ç¦¨ÁZº¥¦³¶i¨B¡A Áö¥¼¹F²z·Q¡A¦ý¨¬ÃÒ©p¤w¥I¥X¤F§V¤O¡C');
INSERT INTO comment VALUES (437, 0, 0, 0, 3, '1', '¤åÀR¶Ô«j¡A¤­¨|¬ÒÀu¡C¨C¤é¦Û°ÊÀ°§U¦Ñ®v¾ã²z±Ð«Ç¡A±q¤£¾Ó«å¡A¶Ô³Òºë¯«¡A¥O¦Ñ®v·P°Ê¡I§®®S¡AÁÂÁÂ©p¡C');
INSERT INTO comment VALUES (438, 0, 0, 0, 3, '1', '¬¡¼â©ú?Ô¡Aºë©ó¬ü³Ò¡AÄ@¦hµo´§ªø³B¡A±N¨Ó¤~¯à¦³©Òµo®i¡C');
INSERT INTO comment VALUES (439, 0, 0, 0, 3, '1', '¼ÖÆ[©M¸s¡A­t³d¨Ä¥©¡A½Ò·~¦p¯à¦hªá¤ß«ä¬ã¨s¡A©w·|¶i¨B¡C');
INSERT INTO comment VALUES (440, 0, 0, 0, 3, '1', '¡u¤@¤À¯Ñ¯Ð¡A¤@¤À¦¬Ã¬¡v¡A¦n¦nªº§V¤O¡A\r\n´Á«Ý©¹«á¯à¦³¦nªºªí²{¡C');
INSERT INTO comment VALUES (441, 0, 0, 0, 3, '1', '¬¡¼â¶}®Ô¡A¯àÃö¤ß§O¤H¡A³B³B´À§O¤H³]·Q¡A¸s©Ê¯S¨Î¡C');
INSERT INTO comment VALUES (442, 0, 0, 0, 3, '1', '¨Ä¥©¤åÀR¡A·L¯º±`±¾Áy¤W¡A¿Ë¤Á·P¤H¡A½Ò·~©|ª¾¶Ô«j¡A°ß¼Æ¾Ç¤@¬ì¦³«Ý¥[±j¡C');
INSERT INTO comment VALUES (443, 0, 0, 0, 3, '1', '¦³¤F§V¤O¡A¥²·|¦³¦¬Àò¡A©p¤wºÉ¤F¤O¡A¼Æ¾ÇÁö¥¼¹F²z·Q¡A¦Ñ®v¤S¦ó§Ô©Ç©p©O¡H');
INSERT INTO comment VALUES (444, 0, 0, 0, 3, '1', 'ºë¯«­n®¶§@¡AÅª®Ñ­n¿n·¥¡A¦¨ÁZ¤~·|¦n¡C²QµØ¡G®¶§@°_¨Ó§a¡I¯¬ºÖ©p¡I');
INSERT INTO comment VALUES (445, 0, 0, 0, 3, '1', '©Ê±¡²n®Ô¡A¦ý«o¥¢¤§ÀH«K¡A¦p¯àÂÔ¨¥·V¦æ¡Aµo¼«Åª®Ñ¡A¦¨ÁZ¥²¥iºë¶i¡C');
INSERT INTO comment VALUES (446, 0, 0, 0, 3, '1', '»P¤H¬Û³B¡A¥²¶·¼e«p¡A¤u©ó­p¸û¡AªB¤Í¥²¥¢¡C¤ß¥²¨IÀR¡A¤~¯à¦w¤ß¦V¾Ç¡C');
INSERT INTO comment VALUES (447, 0, 0, 0, 3, '1', '¤Ñ¸ê¤£®t¡A¥Ñ¼Æ¾Ç¦¨ÁZ¥i¬Ý¥X¡A¦p¯àµy¥[¥Î¤ß¡A¨ä¥L¦¨ÁZ¤]¥²·|¶i¨B¡I§V¤O§a¡I¦h¬Ý®Ñ¡A¤Ö»¡¸Ü¡C');
INSERT INTO comment VALUES (448, 0, 0, 0, 3, '1', 'ªÃ©Ê·Å©M¡A«Ý¤H¦³Â§¡A¤u§@¼ö¤ß¡A¶Ô©ó¾Ç²ß¡A¨¬³ô¹Å³¡AÄ@®aªø¥þ¤O°ö´Ó¡A¥H¦¨¤j¾¹¡C');
INSERT INTO comment VALUES (449, 0, 0, 0, 3, '1', '­Ó©Ê­Ï±j¡A¥Ç¿ù¦Ó¤£«äÀË°Q¡A³ß¥H±j¨¥¬¾ÅG¡AºA«×¥¢¤§²z´¼¡AÄ@¯à¦Û§Ú²`«ä¡A¥[¥H§ï¶i¡AÂÔÅ¥®v¨¥¡A§¶§¶¦V¾Ç¡A¤~¬O¾Ç¥ÍÀ³¦³¤§ºA«×¡AÄ@§A¯àÅé·|¡C');
INSERT INTO comment VALUES (450, 0, 0, 0, 3, '1', 'Áo¿o¦Óª¾¶Ô¾Ç¡A¦ý¤Á°O«~¼w¤ñ¾Ç°Ý§ó­«­n¡AÅ¥±q®vªø±Ð»£¡A¤Á°O¤£¥i¨¥»y«_¥¢¡A¥H·S¤H¶û¡C');
INSERT INTO comment VALUES (451, 0, 0, 0, 3, '1', '¾Ç®Õ¬O­Ó¤j®a®x¡A¥²¶·¤H¤H¿í¦u¯´§Ç¡A¤£¥iµL²z¨ú¾x¡A¯}Ãa¯´§Ç¡C¦h¥Î¤ß«ä©ó¾Ç·~¡A¬Û«H¶Ô¥²¯à¸É©å¡C');
INSERT INTO comment VALUES (452, 0, 0, 0, 3, '1', '¸[½áÁo¿o¡A°¸¦³¨ÎÁZ¡A¦ýÅº®ð­«¡A¥¼¯à«ù¤§©ó«í¡C»Pªø½ú¥æ½Í¡A¶·ª`·N¶ÉÅ¥¡A¤£¥i®É±`¦h¨¥¡B´¡¼L¡A¤~¬OÁ¾®¥¤§¹D¡C');
INSERT INTO comment VALUES (453, 0, 0, 0, 3, '1', '°O¨ú¥j¤H¡u¨¥¦h¥²¥¢¡v¤§¨¥¡AºÉ¶q«O«ù¨IÀq¡A¤×¨ä¤W½Ò¤¤¡A¤£¥iÀH«K»¡¸Ü¡A¥H§K·l¤H¤S¤£§Q¤v¡A«Ý¤H¥H¸Û¡A§O¤H¥²¤]³ßÅw§A¡I');
INSERT INTO comment VALUES (454, 0, 0, 0, 3, '1', '¥­¤é¦h¥Î¤ß«ä¦Ò¡A¦h¬ãºâ¡A¬Û«H¼Æ¾Ç¦¨ÁZ¤]¥i©M¨ä¥L¬ì¥Ø¤@¼Ë¦n¡C');
INSERT INTO comment VALUES (742, 1001, 0, 0, 0, '', '');
INSERT INTO comment VALUES (456, 0, 0, 0, 1, '1', '·Å©M¯Â¾ë');
INSERT INTO comment VALUES (457, 0, 0, 0, 1, '1', '©Z¥Õ²nª½');
INSERT INTO comment VALUES (458, 0, 0, 0, 1, '1', '»á¦³§Ó®ð');
INSERT INTO comment VALUES (459, 0, 0, 0, 1, '1', '«i´±ªG¨M');
INSERT INTO comment VALUES (460, 0, 0, 0, 1, '1', '¸Û¹ê¯u¼°');
INSERT INTO comment VALUES (462, 0, 0, 0, 1, '1', '¿w¦æ­@³Ò');
INSERT INTO comment VALUES (463, 0, 0, 0, 1, '1', '¤Ñ¸êÁo¿o');
INSERT INTO comment VALUES (464, 0, 0, 0, 1, '1', 'ºB´n»¨ÁÚ');
INSERT INTO comment VALUES (465, 0, 0, 0, 1, '1', 'ªÃ©Ê¯Â«p');
INSERT INTO comment VALUES (466, 0, 0, 0, 1, '1', 'ºë©ú¯à·F');
INSERT INTO comment VALUES (467, 0, 0, 0, 1, '1', '»â¾É¤O±j');
INSERT INTO comment VALUES (468, 0, 0, 0, 1, '1', '¶Ô³Ò»ü¹²');
INSERT INTO comment VALUES (469, 0, 0, 0, 1, '1', '¿n·¥¾Ä°«');
INSERT INTO comment VALUES (470, 0, 0, 0, 1, '1', '¬¡¼â¤Ñ¯u');
INSERT INTO comment VALUES (471, 0, 0, 0, 1, '1', '¤åÀR¿w¹ê');
INSERT INTO comment VALUES (472, 0, 0, 0, 1, '1', '¼ÖÆ[¶i¨ú');
INSERT INTO comment VALUES (473, 0, 0, 0, 1, '1', '¾Üµ½©T°õ');
INSERT INTO comment VALUES (474, 0, 0, 0, 1, '1', '©|ª¾¾Ä«j');
INSERT INTO comment VALUES (475, 0, 0, 0, 1, '1', '¿w¦u«H¸q');
INSERT INTO comment VALUES (476, 0, 0, 0, 1, '1', '­èª½´ý«p');
INSERT INTO comment VALUES (477, 0, 0, 0, 1, '1', '¸ÛÀµ©Z²v');
INSERT INTO comment VALUES (478, 0, 0, 0, 1, '1', '¬¡¼â²n®Ô');
INSERT INTO comment VALUES (479, 0, 0, 0, 1, '1', '©MÄ§¥i¿Ë');
INSERT INTO comment VALUES (480, 0, 0, 0, 1, '1', '¤£¬á¤£¥ï');
INSERT INTO comment VALUES (481, 0, 0, 0, 1, '1', '°í¼Ý¦³«í');
INSERT INTO comment VALUES (482, 0, 0, 0, 1, '1', '¯ÝÃÌ¶}Áï');
INSERT INTO comment VALUES (483, 0, 0, 0, 1, '1', '´I¶i¨ú¤ß');
INSERT INTO comment VALUES (484, 0, 0, 0, 1, '1', '¤º¦V¦Û«ß');
INSERT INTO comment VALUES (485, 0, 0, 0, 1, '1', '´°«p¸Û¹ê');
INSERT INTO comment VALUES (486, 0, 0, 0, 1, '1', '·N§Ó°í±j');
INSERT INTO comment VALUES (487, 0, 0, 0, 1, '1', '¾÷´¼¨IµÛ');
INSERT INTO comment VALUES (488, 0, 0, 0, 1, '1', '¥ú©ú½U¸¨');
INSERT INTO comment VALUES (489, 0, 0, 0, 1, '1', '·Å¶¶©¾«p');
INSERT INTO comment VALUES (490, 0, 0, 0, 1, '1', 'Åª®Ñ±M¤ß');
INSERT INTO comment VALUES (491, 0, 0, 0, 1, '1', '±M¤ßÅ¥½Ò');
INSERT INTO comment VALUES (492, 0, 0, 0, 1, '1', '²z¸Ñ¤O±j');
INSERT INTO comment VALUES (493, 0, 0, 0, 1, '1', '«~¾Ç­ÝÀu');
INSERT INTO comment VALUES (494, 0, 0, 0, 1, '1', '¾Ç¦³±Mªø');
INSERT INTO comment VALUES (495, 0, 0, 0, 1, '1', '¶Ô¾Ä¦n¾Ç');
INSERT INTO comment VALUES (496, 0, 0, 0, 1, '1', 'ªA±q«ü¾É');
INSERT INTO comment VALUES (497, 0, 0, 0, 1, '1', 'Æ[©À¥¿½T');
INSERT INTO comment VALUES (498, 0, 0, 0, 1, '1', '¨IµÛÂíÀR');
INSERT INTO comment VALUES (499, 0, 0, 0, 1, '1', '¨£¸q«i¬°');
INSERT INTO comment VALUES (500, 0, 0, 0, 1, '1', '®ð«×°ª©|');
INSERT INTO comment VALUES (501, 0, 0, 0, 1, '1', '¨IÀR·Å¨}');
INSERT INTO comment VALUES (502, 0, 0, 0, 1, '1', '¦w¤À¦u¤v');
INSERT INTO comment VALUES (503, 0, 0, 0, 1, '1', '«ä·Q¯Â¥¿');
INSERT INTO comment VALUES (504, 0, 0, 0, 1, '1', 'ªÃ©Ê¯Â«p');
INSERT INTO comment VALUES (505, 0, 0, 0, 1, '1', 'Áo©ú§D«W');
INSERT INTO comment VALUES (506, 0, 0, 0, 1, '1', '¾÷´¼±Ó±¶');
INSERT INTO comment VALUES (507, 0, 0, 0, 1, '1', '³Ð³y¶i¨ú');
INSERT INTO comment VALUES (508, 0, 0, 0, 1, '1', '©|ª¾¶i¨ú');
INSERT INTO comment VALUES (509, 0, 0, 0, 1, '1', '¨Dª¾¼¤±j');
INSERT INTO comment VALUES (510, 0, 0, 0, 1, '1', '¾Ç·~Àu¨}');
INSERT INTO comment VALUES (511, 0, 0, 0, 1, '1', '±Ó¦Ó¦n¾Ç');
INSERT INTO comment VALUES (512, 0, 0, 0, 1, '1', '¾Äµo¦V¤W');
INSERT INTO comment VALUES (513, 0, 0, 0, 1, '1', '¥ß¨D¶i¨ú');
INSERT INTO comment VALUES (514, 0, 0, 0, 1, '1', '¼ôÅª²`«ä');
INSERT INTO comment VALUES (515, 0, 0, 0, 1, '1', '¶Ô©ó¨Dª¾');
INSERT INTO comment VALUES (516, 0, 0, 0, 1, '1', '©|ªÖ»{¯u');
INSERT INTO comment VALUES (517, 0, 0, 0, 1, '1', '±M¤ß­P§Ó');
INSERT INTO comment VALUES (518, 0, 0, 0, 1, '1', '¤é¦³¶i¨B');
INSERT INTO comment VALUES (519, 0, 0, 0, 1, '1', '¤â¤£ÄÀ¨÷');
INSERT INTO comment VALUES (520, 0, 0, 0, 1, '1', '¦sºÃ¨D¯u');
INSERT INTO comment VALUES (521, 0, 0, 0, 1, '1', '¾Ç²ß»{¯u');
INSERT INTO comment VALUES (522, 0, 0, 0, 1, '1', '»áª¾»{¯u');
INSERT INTO comment VALUES (523, 0, 0, 0, 1, '1', '«ä¦Ò¿¦±K');
INSERT INTO comment VALUES (524, 0, 0, 0, 1, '1', '¸`¬ù»ü¬Ù');
INSERT INTO comment VALUES (525, 0, 0, 0, 1, '1', '¼ö¤ß¤½¯q');
INSERT INTO comment VALUES (526, 0, 0, 0, 1, '1', '·R±¤¤½ª«');
INSERT INTO comment VALUES (527, 0, 0, 0, 1, '1', 'ªA°È¶Ô·V');
INSERT INTO comment VALUES (528, 0, 0, 0, 1, '1', '¤ß¥­®ð©M');
INSERT INTO comment VALUES (529, 0, 0, 0, 1, '1', '§JºÉÂ¾³d');
INSERT INTO comment VALUES (530, 0, 0, 0, 1, '1', 'ªA°ÈºÉ³d');
INSERT INTO comment VALUES (531, 0, 0, 0, 1, '1', '©|¦u³W¯x');
INSERT INTO comment VALUES (532, 0, 0, 0, 1, '1', 'Â§»ª©P¨ì');
INSERT INTO comment VALUES (533, 0, 0, 0, 1, '1', '¨è­W­@³Ò');
INSERT INTO comment VALUES (534, 0, 0, 0, 1, '1', '¤Í·R¦P¾Ç');
INSERT INTO comment VALUES (535, 0, 0, 0, 1, '1', '·q®v·ü¤Í');
INSERT INTO comment VALUES (536, 0, 0, 0, 1, '1', '«ÈÆ[¤½¥¿');
INSERT INTO comment VALUES (537, 0, 0, 0, 1, '1', '·Å¤å¦³Â§');
INSERT INTO comment VALUES (538, 0, 0, 0, 1, '1', '¥­©öªñ¤H');
INSERT INTO comment VALUES (539, 0, 0, 0, 1, '1', '¥ô³Ò¥ô«è');
INSERT INTO comment VALUES (540, 0, 0, 0, 1, '1', 'Â²³æ¾ë¯À');
INSERT INTO comment VALUES (541, 0, 0, 0, 1, '1', '´`³WÁÐ¯x');
INSERT INTO comment VALUES (542, 0, 0, 0, 1, '1', 'Á|¤îºÝ²ø');
INSERT INTO comment VALUES (543, 0, 0, 0, 1, '1', '¦X§@µL¶¡');
INSERT INTO comment VALUES (544, 0, 0, 0, 1, '1', '¤u§@§V¤O');
INSERT INTO comment VALUES (545, 0, 0, 0, 1, '1', '»ö®e¾ãµÂ');
INSERT INTO comment VALUES (546, 0, 0, 0, 1, '1', '·RÅ@¹ÎÅé');
INSERT INTO comment VALUES (547, 0, 0, 0, 1, '1', '¦uªk­«¬ö');
INSERT INTO comment VALUES (548, 0, 0, 0, 1, '1', '«Ý¤H¼ö§Õ');
INSERT INTO comment VALUES (549, 0, 0, 0, 1, '1', '¦Û­«¦Û·R');
INSERT INTO comment VALUES (550, 0, 0, 0, 1, '1', '¨è­W¦ÛÀy');
INSERT INTO comment VALUES (551, 0, 0, 0, 1, '1', '§@®§¦³§Ç');
INSERT INTO comment VALUES (552, 0, 0, 0, 1, '1', 'ºA«×¤j¤è');
INSERT INTO comment VALUES (553, 0, 0, 0, 1, '1', '«ù°`ÂÔÄY');
INSERT INTO comment VALUES (554, 0, 0, 0, 1, '1', '¼ö¤ß§U¤H');
INSERT INTO comment VALUES (555, 0, 0, 0, 1, '1', '«i©ó»{¿ù');
INSERT INTO comment VALUES (556, 0, 0, 0, 1, '1', '³B¥@©M¥­');
INSERT INTO comment VALUES (557, 0, 0, 0, 1, '1', '«Ý¤H¸ÛÀµ');
INSERT INTO comment VALUES (558, 0, 0, 0, 1, '1', '¦æ¬°Ã­°·');
INSERT INTO comment VALUES (559, 0, 0, 0, 1, '1', '³d¥ô¤ß­«');
INSERT INTO comment VALUES (560, 0, 0, 0, 1, '1', 'ºÝ²ø¦³Â§');
INSERT INTO comment VALUES (561, 0, 0, 0, 1, '1', '«æ¤½¦n¸q');
INSERT INTO comment VALUES (562, 0, 0, 0, 1, '1', 'Á¾®¥¦³Â§');
INSERT INTO comment VALUES (563, 0, 0, 0, 1, '1', '¼ö¤ß§U¤H');
INSERT INTO comment VALUES (564, 0, 0, 0, 1, '1', '¾ã»ô²M¼ä');
INSERT INTO comment VALUES (565, 0, 0, 0, 1, '1', '¶Ô³Ò»ü¹²');
INSERT INTO comment VALUES (566, 0, 0, 0, 1, '1', '©ú¿ë¿w¦æ');
INSERT INTO comment VALUES (567, 0, 0, 0, 1, '1', '¼ÖÆ[¦X¸s');
INSERT INTO comment VALUES (568, 0, 0, 0, 1, '1', '©|¦u¬ö«ß');
INSERT INTO comment VALUES (569, 0, 0, 0, 1, '1', '½Í¦R¦³«×');
INSERT INTO comment VALUES (570, 0, 0, 0, 1, '1', '¿í¦u¬ö«ß');
INSERT INTO comment VALUES (571, 0, 0, 0, 1, '1', '¬¡¼â´r§Ö');
INSERT INTO comment VALUES (572, 0, 0, 0, 1, '1', 'ºë¯«¹¡º¡');
INSERT INTO comment VALUES (573, 0, 0, 0, 1, '1', '·q·~¼Ö¸s');
INSERT INTO comment VALUES (574, 0, 0, 0, 1, '1', '¼ö¤ß¤½¯q');
INSERT INTO comment VALUES (575, 0, 0, 0, 1, '1', '');
INSERT INTO comment VALUES (576, 0, 0, 0, 1, '1', '²yÃÀºë´ï');
INSERT INTO comment VALUES (577, 0, 0, 0, 1, '1', '§ÞÃÀ¶W¸s');
INSERT INTO comment VALUES (578, 0, 0, 0, 1, '1', 'ºë³q´åªa');
INSERT INTO comment VALUES (579, 0, 0, 0, 1, '1', '¾Õªø¼g§@');
INSERT INTO comment VALUES (580, 0, 0, 0, 1, '1', '¾Õªø°ê³N');
INSERT INTO comment VALUES (581, 0, 0, 0, 1, '1', '¾Õªø®Ñªk');
INSERT INTO comment VALUES (582, 0, 0, 0, 1, '1', '¾Õªø¬ü³N');
INSERT INTO comment VALUES (583, 0, 0, 0, 1, '1', '¾Õªø­µ¼Ö');
INSERT INTO comment VALUES (584, 0, 0, 0, 1, '1', '¾Õªø¤uÃÀ');
INSERT INTO comment VALUES (585, 0, 0, 0, 1, '1', '¾Õªøºt»¡');
INSERT INTO comment VALUES (586, 0, 0, 0, 1, '1', '¾Õªø®a¨Æ');
INSERT INTO comment VALUES (587, 0, 0, 0, 1, '1', '¾Õªø¹B°Ê');
INSERT INTO comment VALUES (588, 0, 0, 0, 1, '1', '¾Õªø¼Æ²z');
INSERT INTO comment VALUES (589, 0, 0, 0, 1, '1', '¾Õªø»RÁÐ');
INSERT INTO comment VALUES (590, 0, 0, 0, 1, '1', '¾ÕªøÀ¸¼@');
INSERT INTO comment VALUES (591, 0, 0, 0, 1, '1', '¾Õªø¤å¥v');
INSERT INTO comment VALUES (592, 0, 0, 0, 1, '1', '³ß©ê¤£¥­');
INSERT INTO comment VALUES (593, 0, 0, 0, 1, '1', '¯ÝÃÌ¯U¤p');
INSERT INTO comment VALUES (594, 0, 0, 0, 1, '1', '­Ó©Ê­Ï±j');
INSERT INTO comment VALUES (595, 0, 0, 0, 1, '1', '©e¦±¨D¥þ');
INSERT INTO comment VALUES (596, 0, 0, 0, 1, '1', '¤ß¯B®ðÄ¼');
INSERT INTO comment VALUES (597, 0, 0, 0, 1, '1', '·N§ÓÁ¡®z');
INSERT INTO comment VALUES (598, 0, 0, 0, 1, '1', '§íÆ{¹èÅw');
INSERT INTO comment VALUES (599, 0, 0, 0, 1, '1', '«æ©Ê¦n°Ê');
INSERT INTO comment VALUES (600, 0, 0, 0, 1, '1', 'Æ[©À°¾»á');
INSERT INTO comment VALUES (601, 0, 0, 0, 1, '4', '¤£°÷¸Û¹ê');
INSERT INTO comment VALUES (602, 0, 0, 0, 1, '1', 'À¶®z¦]´`');
INSERT INTO comment VALUES (603, 0, 0, 0, 1, '1', '¨IÀq¹è¨¥');
INSERT INTO comment VALUES (604, 0, 0, 0, 1, '1', '¤£Å¥°V»|');
INSERT INTO comment VALUES (605, 0, 0, 0, 1, '1', '½Ä°Ê¦n«ã');
INSERT INTO comment VALUES (606, 0, 0, 0, 1, '1', '©Ê±¡¨Ä©Ñ');
INSERT INTO comment VALUES (607, 0, 0, 0, 1, '1', '©Ê±¡­è´p');
INSERT INTO comment VALUES (608, 0, 0, 0, 1, '1', '¦Û¨õ®`²Û');
INSERT INTO comment VALUES (609, 0, 0, 0, 1, '1', '¤£°÷»{¯u');
INSERT INTO comment VALUES (610, 0, 0, 0, 1, '1', '¤£³ß¼Æ²z');
INSERT INTO comment VALUES (611, 0, 0, 0, 1, '1', '¦Û¼É¦Û±ó');
INSERT INTO comment VALUES (612, 0, 0, 0, 1, '1', '½Ò·~¤í¨Î');
INSERT INTO comment VALUES (613, 0, 0, 0, 1, '1', '¼Å­l¯ó²v');
INSERT INTO comment VALUES (614, 0, 0, 0, 1, '1', '¼^¹CµL«×');
INSERT INTO comment VALUES (615, 0, 0, 0, 1, '1', 'Áo¿o³gª±');
INSERT INTO comment VALUES (616, 0, 0, 0, 1, '1', '¤£¨D¤W¶i');
INSERT INTO comment VALUES (617, 0, 0, 0, 1, '2', '¤£¦nÅª®Ñ');
INSERT INTO comment VALUES (618, 0, 0, 0, 1, '2', '³gª±¦n¾Ç');
INSERT INTO comment VALUES (619, 0, 0, 0, 1, '2', '¯Ê¥F«í¤ß');
INSERT INTO comment VALUES (620, 0, 0, 0, 1, '2', '«j¯àÀ³¥I');
INSERT INTO comment VALUES (621, 0, 0, 0, 1, '2', '©|¤í§V¤O');
INSERT INTO comment VALUES (622, 0, 0, 0, 1, '2', 'Ãi©ó¾Ç²ß');
INSERT INTO comment VALUES (623, 0, 0, 0, 1, '2', '¤£©ë¤p¸`');
INSERT INTO comment VALUES (624, 0, 0, 0, 1, '2', '­Ó©Ê©t»÷');
INSERT INTO comment VALUES (625, 0, 0, 0, 1, '2', '¸ê½è¾|¶w');
INSERT INTO comment VALUES (626, 0, 0, 0, 1, '2', '¤ß¤£±M¤@');
INSERT INTO comment VALUES (627, 0, 0, 0, 1, '3', '¤£­t³d¥ô');
INSERT INTO comment VALUES (628, 0, 0, 0, 1, '2', 'À³¦h¦Û«ß');
INSERT INTO comment VALUES (629, 0, 0, 0, 1, '2', '¤£µ½¥æ»Ú');
INSERT INTO comment VALUES (630, 0, 0, 0, 1, '2', '¸X®ð¤Ó­«');
INSERT INTO comment VALUES (631, 0, 0, 0, 1, '3', '´²º©ÃP¾Ó');
INSERT INTO comment VALUES (632, 0, 0, 0, 1, '2', '¹ð»|¤£Ñª');
INSERT INTO comment VALUES (633, 0, 0, 0, 1, '2', '¤ß¯B®ðÀê');
INSERT INTO comment VALUES (634, 0, 0, 0, 1, '3', '¥æ¹C¤£·V');
INSERT INTO comment VALUES (635, 0, 0, 0, 1, '2', 'ºë¯«¤£®¶');
INSERT INTO comment VALUES (636, 0, 0, 0, 1, '3', 'ª±¥@¤£®¥');
INSERT INTO comment VALUES (637, 0, 0, 0, 1, '3', '¨¥¦h¦æ¤Ö');
INSERT INTO comment VALUES (638, 0, 0, 0, 1, '2', '¯ó²v±q¨Æ');
INSERT INTO comment VALUES (639, 0, 0, 0, 1, '3', '²Ê¤ß¤j·N');
INSERT INTO comment VALUES (640, 0, 0, 0, 1, '2', '¾Ç¦Ó¤£±M');
INSERT INTO comment VALUES (641, 0, 0, 0, 1, '2', '¤£¨D¬Æ¸Ñ');
INSERT INTO comment VALUES (642, 0, 0, 0, 1, '2', '®É¾Ç®É°±');
INSERT INTO comment VALUES (643, 0, 0, 0, 1, '2', '§Ú¦æ§Ú¯À');
INSERT INTO comment VALUES (644, 0, 0, 0, 1, '2', '·R¥X­·ÀY');
INSERT INTO comment VALUES (645, 0, 0, 0, 1, '3', '¯BµØ¤£¹ê');
INSERT INTO comment VALUES (646, 0, 0, 0, 1, '2', 'ªA°È¤í¨Î');
INSERT INTO comment VALUES (647, 0, 0, 0, 1, '4', '¼Å­l¯ó²v');
INSERT INTO comment VALUES (648, 0, 0, 0, 1, '3', '±jµü¦nÅG');
INSERT INTO comment VALUES (649, 0, 0, 0, 1, '4', '¾|²õµLÂ§');
INSERT INTO comment VALUES (650, 0, 0, 0, 1, '4', '¦æ¬°¶ÆºC');
INSERT INTO comment VALUES (651, 0, 0, 0, 1, '4', 'Á|¤î»´¨Ù');
INSERT INTO comment VALUES (652, 0, 0, 0, 1, '4', '©Ê±¡²Ê¼É');
INSERT INTO comment VALUES (653, 0, 0, 0, 1, '2', '²`ª¾®¬§ï');
INSERT INTO comment VALUES (654, 0, 0, 0, 1, '3', '·N§Ó®ø¨I');
INSERT INTO comment VALUES (655, 0, 0, 0, 1, '3', '¤£¬Æ¦X¸s');
INSERT INTO comment VALUES (656, 0, 0, 0, 1, '2', '¬X®z¹èÂ_');
INSERT INTO comment VALUES (657, 0, 0, 0, 1, '3', 'ºA«×»´¯B');
INSERT INTO comment VALUES (658, 0, 0, 0, 1, '3', 'Â§»ª¤£¶g');
INSERT INTO comment VALUES (659, 0, 0, 0, 1, '3', '·M¦Ó¦Û¥Î');
INSERT INTO comment VALUES (660, 0, 0, 0, 1, '3', '°¾³R´`¨p');
INSERT INTO comment VALUES (661, 0, 0, 0, 1, '3', '¨¥¦æÀH«K');
INSERT INTO comment VALUES (662, 0, 0, 0, 1, '2', '±o¹L¥B¹L');
INSERT INTO comment VALUES (663, 0, 0, 0, 1, '2', '¨­Åéµê®z');
INSERT INTO comment VALUES (664, 0, 0, 0, 1, '3', '¤£¯à¦X¸s');
INSERT INTO comment VALUES (665, 0, 0, 0, 1, '4', 'Áx¤j¦k¬°');
INSERT INTO comment VALUES (666, 0, 0, 0, 1, '3', '»ö®e¤£¾ã');
INSERT INTO comment VALUES (667, 0, 0, 0, 1, '4', '¦n¶h´c³Ò');
INSERT INTO comment VALUES (668, 0, 0, 0, 1, '4', '¥Í¬¡´²º©');
INSERT INTO comment VALUES (669, 0, 0, 0, 1, '3', '²Ê¤ß¤j·N');
INSERT INTO comment VALUES (670, 0, 0, 0, 1, '3', '¶¢¸Ü¤Ó¦h');
INSERT INTO comment VALUES (671, 0, 0, 0, 1, '4', '¦æ¬°¤£ÀË');
INSERT INTO comment VALUES (672, 0, 0, 0, 1, '4', '¦Û¥ÌÀZ´k');
INSERT INTO comment VALUES (673, 0, 0, 0, 1, '1', '³B¨Æ¥DÆ[');
INSERT INTO comment VALUES (674, 0, 0, 0, 1, '3', 'ºë¯«µäÃû');
INSERT INTO comment VALUES (675, 0, 0, 0, 1, '1', '¯à·F¼ö¤ß');
INSERT INTO comment VALUES (676, 0, 0, 0, 1, '1', '¬¡¼â¦n¾Ç');
INSERT INTO comment VALUES (677, 0, 0, 0, 1, '1', 'Åª®Ñ§V¤O');
INSERT INTO comment VALUES (678, 0, 0, 0, 1, '1', 'Áo©ú¥Î¤ß');
INSERT INTO comment VALUES (679, 0, 0, 0, 1, '1', '»{¯u¦u³W');
INSERT INTO comment VALUES (680, 0, 0, 0, 1, '1', '©|ª¾»{¯u');
INSERT INTO comment VALUES (681, 0, 0, 0, 1, '1', '¦¨ÁZ¶i¨B');
INSERT INTO comment VALUES (682, 0, 0, 0, 1, '1', '¾Ç²ß»{¯u');
INSERT INTO comment VALUES (683, 0, 0, 0, 1, '1', '¤W½Ò¥Î¤ß');
INSERT INTO comment VALUES (684, 0, 0, 0, 1, '1', '»{¯u¦Ñ¹ê');
INSERT INTO comment VALUES (685, 0, 0, 0, 1, '1', 'Áo©ú»{¯u');
INSERT INTO comment VALUES (686, 0, 0, 0, 1, '1', '¶Ô¾Ç¦uªk');
INSERT INTO comment VALUES (687, 0, 0, 0, 1, '1', '»{¯u­t³d');
INSERT INTO comment VALUES (688, 0, 0, 0, 1, '1', '´`³WÁÐ¯x');
INSERT INTO comment VALUES (689, 0, 0, 0, 1, '1', '»{¯u¦n¾Ç');
INSERT INTO comment VALUES (690, 0, 0, 0, 1, '1', '«~¾Ç­ÝÀu');
INSERT INTO comment VALUES (691, 0, 0, 0, 1, '1', '¦³¶i¨ú¤ß');
INSERT INTO comment VALUES (692, 0, 0, 0, 1, '1', 'ºë¤O¥R¨K');
INSERT INTO comment VALUES (693, 0, 0, 0, 1, '1', '©Ê±¡¯Â¨}');
INSERT INTO comment VALUES (694, 0, 0, 0, 1, '1', '¨IÀR¶Ô¾Ä');
INSERT INTO comment VALUES (695, 0, 0, 0, 1, '1', '¦X¸s©M·ü');
INSERT INTO comment VALUES (696, 0, 0, 0, 1, '1', '¤Ñ¸ê¿o¼z');
INSERT INTO comment VALUES (697, 0, 0, 0, 1, '1', '¨IÀq¦u³W');
INSERT INTO comment VALUES (698, 0, 0, 0, 1, '1', '·Å¤å¦³Â§');
INSERT INTO comment VALUES (699, 0, 0, 0, 1, '1', '«~¦æºÝ¥¿');
INSERT INTO comment VALUES (700, 0, 0, 0, 1, '1', '´I¤½¼w¤ß');
INSERT INTO comment VALUES (701, 0, 0, 0, 1, '1', '»{¯uÅ¥¸Ü');
INSERT INTO comment VALUES (702, 0, 0, 0, 1, '1', '¨Ä¥©¥i·R');
INSERT INTO comment VALUES (703, 0, 0, 0, 1, '1', '©MÄ§¿Ë¤Á');
INSERT INTO comment VALUES (704, 0, 0, 0, 1, '1', '¦¨ÁZ»á¨Î');
INSERT INTO comment VALUES (705, 0, 0, 0, 1, '1', '²ß§@»{¯u');
INSERT INTO comment VALUES (706, 0, 0, 0, 1, '1', '©Ê±¡·Å©M');
INSERT INTO comment VALUES (707, 0, 0, 0, 1, '1', 'Åª®Ñ»{¯u');
INSERT INTO comment VALUES (708, 0, 0, 0, 1, '2', '½Ò·~¼ã¯ó');
INSERT INTO comment VALUES (709, 0, 0, 0, 1, '1', '«Ý¤H¼e«p');
INSERT INTO comment VALUES (710, 0, 0, 0, 1, '1', 'ªÃ©Ê¯Â¨}');
INSERT INTO comment VALUES (711, 0, 0, 0, 1, '1', '«Ý¤H¿Ë¤Á');
INSERT INTO comment VALUES (712, 0, 0, 0, 1, '1', '¶Ô³Ò¼ö§Õ');
INSERT INTO comment VALUES (713, 0, 0, 0, 1, '1', '´I¦P±¡¤ß');
INSERT INTO comment VALUES (714, 0, 0, 0, 1, '1', '¤ß¦aµ½¨}');
INSERT INTO comment VALUES (715, 0, 0, 0, 1, '1', '¤f¾¦²M´·');
INSERT INTO comment VALUES (716, 0, 0, 0, 1, '1', '»â®©¤O±j');
INSERT INTO comment VALUES (717, 0, 0, 0, 1, '1', '»â³S¼¤±j');
INSERT INTO comment VALUES (718, 0, 0, 0, 1, '1', '§@®§¦u®É');
INSERT INTO comment VALUES (719, 0, 0, 0, 1, '1', '¨IÀR¶Ô¾Ä');
INSERT INTO comment VALUES (720, 0, 0, 0, 1, '1', 'éw«p¸Û¹ê');
INSERT INTO comment VALUES (721, 0, 0, 0, 1, '1', '©Ê±¡±¬«p');
INSERT INTO comment VALUES (722, 0, 0, 0, 1, '1', 'ªA±q¹è¨¥');
INSERT INTO comment VALUES (723, 0, 0, 0, 1, '1', '´I¶i¨ú¤ß');
INSERT INTO comment VALUES (724, 0, 0, 0, 1, '1', '·Å¬Xª¾Â§');
INSERT INTO comment VALUES (725, 0, 0, 0, 1, '1', '»{¯u­t³d');
INSERT INTO comment VALUES (726, 0, 0, 0, 1, '1', '¾Ç²ß»{¯u');
INSERT INTO comment VALUES (727, 0, 0, 0, 1, '1', '§@·~¤u¾ã');
INSERT INTO comment VALUES (728, 0, 0, 0, 1, '1', '¤Ñ¸êÁo¿o');
INSERT INTO comment VALUES (729, 0, 0, 0, 1, '4', '¤£¼äÃi´²');
INSERT INTO comment VALUES (730, 0, 0, 0, 1, '4', '¦n°Ê¦h¸Ü');
INSERT INTO comment VALUES (731, 0, 0, 0, 1, '3', '¾Ç²ß¤O®t');
INSERT INTO comment VALUES (732, 0, 0, 0, 1, '3', '¤ÏÀ³¿ð¶w');
INSERT INTO comment VALUES (733, 0, 0, 0, 1, '3', '­n¦h§V¤O');
INSERT INTO comment VALUES (734, 0, 0, 0, 1, '3', '²z¸Ñ¤O®t');
INSERT INTO comment VALUES (745, 1001, 0, 0, 3, '1', '©|ª¾¾Ä«j©Ê±¡­èª½¡Aµy¶û¯B°Ê¼ä¨­¦Û·R¡A­Ó©ÊÄYµÂ¡A¤£ÎA\r\n¨¥¯º¡F¤£¹L°µ¨Æ¦³ÂI¨à¯ó²v¡A§V¤O¤£°÷¡A¤´¶·±`±`¤©¥H·þ\r\n¾É¡C©Ê±¡²v¯u¡B©¾¹ê¡A¤£µ½±»¹¢¡C¨D¾Ç¥¼ºÉ¥þ¤O¡A§_«hÀ³\r\n¦³§ó¦nªºªí²{¡C¬¡¼â¤j¤è¡A°µ¨Æ²Ê²v¡A½Ò·~ªí²{¨Î');
INSERT INTO comment VALUES (746, 1001, 0, 0, 1, '5', '¤Ó´Î¤F!');
INSERT INTO comment VALUES (747, 1001, 0, 0, 5, '5', '¤Ó´Î¤F!');
INSERT INTO comment VALUES (753, 1001, 0, 0, 3, '2', '¸Û¹ê¯u¼°¡A»á¦³§Ó®ð¡A¿w¦æ­@³Ò');
# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `comment_kind`
#

CREATE TABLE comment_kind (
  kind_serial tinyint(3) unsigned NOT NULL auto_increment,
  kind_teacher_id int(8) unsigned default NULL,
  kind_name varchar(50) NOT NULL default '',
  PRIMARY KEY  (kind_serial)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `comment_kind`
#

INSERT INTO comment_kind VALUES (1, 0, '¥|¦r½e¨¥');
INSERT INTO comment_kind VALUES (2, 0, '¤K¤åµ´§®');
INSERT INTO comment_kind VALUES (3, 0, 'ªÍµÆ¤§¨¥');
INSERT INTO comment_kind VALUES (5, 1001, 'test3');
# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `comment_level`
#

CREATE TABLE comment_level (
  level_serial tinyint(3) unsigned NOT NULL auto_increment,
  level_teacher_id int(8) unsigned default NULL,
  level_name varchar(50) NOT NULL default '',
  PRIMARY KEY  (level_serial)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `comment_level`
#

INSERT INTO comment_level VALUES (1, 0, '¥Ò');
INSERT INTO comment_level VALUES (2, 0, '¤A');
INSERT INTO comment_level VALUES (3, 0, '¤þ');
INSERT INTO comment_level VALUES (4, 0, '¤B');
# --------------------------------------------------------


#
# ¸ê®Æªí®æ¦¡¡G `file_db`
#

CREATE TABLE file_db (
  FSN mediumint(8) unsigned NOT NULL auto_increment,
  eduer_unit_sn smallint(5) unsigned NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  main_data longblob NOT NULL,
  description text NOT NULL,
  type varchar(128) NOT NULL default '',
  size int(11) NOT NULL default '0',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  category_sn smallint(6) NOT NULL default '0',
  col_name varchar(255) default NULL,
  col_sn mediumint(8) unsigned default '0',
  unit_sn tinyint(3) unsigned NOT NULL default '0',
  enable enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (FSN),
  KEY ESN (eduer_unit_sn),
  KEY msg_id (col_sn,unit_sn),
  FULLTEXT KEY description (description),
  FULLTEXT KEY filename (filename),
  KEY category (category_sn)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `file_db`
#

# --------------------------------------------------------
#
# ¸ê®Æªí®æ¦¡¡G `form_all`
#

CREATE TABLE form_all (
  ofsn smallint(5) unsigned NOT NULL auto_increment,
  of_title varchar(255) NOT NULL default '',
  of_start_date date NOT NULL default '0000-00-00',
  of_dead_line date NOT NULL default '0000-00-00',
  of_text text,
  of_who varchar(255) default NULL,
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  of_communication varchar(255) default NULL,
  of_date datetime NOT NULL default '0000-00-00 00:00:00',
  enable enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (ofsn),
  KEY eduer_unit_sn (teacher_sn)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `form_all`
#

INSERT INTO form_all VALUES (1, 'µL¥DÃD', '2002-12-30', '2002-12-30', '', NULL, 1, NULL, '2002-12-30 13:49:17', '0');
# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `form_col`
#

CREATE TABLE form_col (
  col_sn int(10) unsigned NOT NULL auto_increment,
  ofsn smallint(5) unsigned NOT NULL default '0',
  col_title varchar(255) NOT NULL default '',
  col_text text,
  col_dataType enum('date','varchar','int','bool') NOT NULL default 'date',
  col_value varchar(255) default NULL,
  col_chk enum('1','0') default NULL,
  col_function set('sum','avg','count') default NULL,
  col_sort tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (col_sn),
  KEY ofsn (ofsn)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `form_col`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `form_fill_in`
#

CREATE TABLE form_fill_in (
  schfi_sn int(10) unsigned NOT NULL auto_increment,
  ofsn smallint(5) unsigned NOT NULL default '0',
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  man_name varchar(20) NOT NULL default '',
  tel varchar(10) NOT NULL default '',
  email varchar(50) NOT NULL default '',
  fill_time datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (schfi_sn),
  KEY ofsn (ofsn,teacher_sn),
  KEY SHN (teacher_sn)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `form_fill_in`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `form_value`
#

CREATE TABLE form_value (
  value_sn bigint(20) unsigned NOT NULL auto_increment,
  schfi_sn int(10) unsigned NOT NULL default '0',
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  ofsn smallint(5) unsigned NOT NULL default '0',
  col_sn int(10) unsigned NOT NULL default '0',
  value varchar(255) NOT NULL default '',
  PRIMARY KEY  (value_sn),
  KEY schfi_sn (schfi_sn,ofsn,col_sn),
  KEY SHN (teacher_sn),
  KEY col_sn (col_sn)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `form_value`
#

# --------------------------------------------------------
#
# ¸ê®Æªí®æ¦¡¡G `new_board`
#

CREATE TABLE new_board (
  serial int(10) unsigned NOT NULL auto_increment,
  title varchar(200) NOT NULL default 'µL¥DÃD',
  content text NOT NULL,
  teacher_sn smallint(5) unsigned NOT NULL default '1',
  post_date datetime NOT NULL default '0000-00-00 00:00:00',
  work_date date NOT NULL default '0000-00-00',
  FSN smallint(5) unsigned default NULL,
  image_url varchar(255) default NULL,
  PRIMARY KEY  (serial),
  KEY serial (serial)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `new_board`
#


#
# ¸ê®Æªí®æ¦¡¡G `pro_module`
#

CREATE TABLE pro_module (
  pm_id bigint(11) NOT NULL auto_increment,
  pm_name varchar(30) NOT NULL default '',
  pm_item varchar(40) NOT NULL default '',
  pm_memo varchar(40) NOT NULL default '',
  pm_value varchar(100) NOT NULL default '',
  PRIMARY KEY  (pm_id),
  UNIQUE KEY pm_id (pm_id)
) TYPE=MyISAM COMMENT='µ{¦¡³]©w¸ê®Æ';

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `pro_module`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `pro_module_main`
#

CREATE TABLE pro_module_main (
  pm_name varchar(30) NOT NULL default '',
  m_display_name varchar(60) NOT NULL default '',
  m_group_name varchar(30) NOT NULL default '',
  m_ver  varchar(30) NOT NULL default '',
  m_create_date date NOT NULL default '0000-00-00',
  m_path varchar(60) NOT NULL default '',
  m_update_date datetime NOT NULL default '0000-00-00 00:00:00',  
  PRIMARY KEY  (pm_name),
  UNIQUE KEY pm_name (pm_name)
) TYPE=MyISAM COMMENT='®Õ°È¼Ò²Õ¥DÀÉ';

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `pro_module_main`
#

#
# ¸ê®Æªí®æ¦¡¡G `school_class`
#

CREATE TABLE school_class (
  class_sn mediumint(8) unsigned NOT NULL auto_increment,
  class_id varchar(11) NOT NULL default '',
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  c_year tinyint(2) unsigned NOT NULL default '0',
  c_name varchar(20) default NULL,
  c_kind varchar(30) default '¤@¯ë¯Z',
  c_sort tinyint(3) unsigned NOT NULL default '0',
  enable enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (class_sn),
  KEY year (year,semester)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `score_course`
#

CREATE TABLE score_course (
  course_id mediumint(8) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  class_id varchar(11) NOT NULL default '',
  teacher_sn mediumint(9) NOT NULL default '0',
  class_year tinyint(2) unsigned NOT NULL default '0',
  class_name tinyint(2) unsigned NOT NULL default '0',
  day enum('0','1','2','3','4','5','6','7') default NULL,
  sector tinyint(1) NOT NULL default '0',
  ss_id smallint(5) unsigned NOT NULL default '0',
  room varchar(10) default NULL,
  allow enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (course_id),
  KEY class_name (class_name),
  KEY class_year (class_year),
  KEY year (year,semester)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `new_stud`
#

CREATE TABLE new_stud (
  newstud_sn int(10) NOT NULL auto_increment,
  stud_study_year tinyint(3) unsigned default NULL,
  old_school varchar(100) default NULL,
  stud_person_id varchar(20) default NULL,
  stud_name varchar(20) default NULL,
  stud_sex tinyint(3) unsigned default NULL,
  stud_tel_1 varchar(20) default NULL,
  stud_birthday date default NULL,
  guardian_name varchar(20) default NULL,
  stud_address varchar(200) default NULL,
  sure_study enum('1','0') default NULL,
  stud_id varchar(20) default NULL,
  class_year char(2) default NULL,
  class_sort tinyint(2) unsigned default NULL,
  class_site tinyint(2) unsigned default NULL,
  temp_score1 tinyint(4) NOT NULL default '-100',
  temp_score2 tinyint(4) NOT NULL default '-100',
  temp_score3 tinyint(4) NOT NULL default '-100',
  meno varchar(200) NOT NULL default '',
  UNIQUE KEY newstud_sn (newstud_sn)
) TYPE=MyISAM;


#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `score_course`
#




# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `score_input_col`
#

CREATE TABLE score_input_col (
  col_sn smallint(5) unsigned NOT NULL auto_increment,
  interface_sn tinyint(4) NOT NULL default '0',
  col_text varchar(255) default NULL,
  col_value varchar(255) default NULL,
  col_type varchar(20) NOT NULL default '',
  col_fn varchar(255) default NULL,
  col_ss enum('n','y') NOT NULL default 'n',
  col_comment enum('n','y') NOT NULL default 'n',
  col_check enum('0','1') default '0',
  col_date datetime default NULL,
  enable enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (col_sn),
  KEY interface_sn (interface_sn)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `score_input_col`
#

INSERT INTO score_input_col VALUES (2, 1, '¤é±`¦æ¬°ªí²{', 'ªí²{Àu²§,ªí²{¨}¦n,ªí²{©|¥i,»Ý¦A¥[ªo,¦³«Ý§ï¶i', 'select', '', 'n', 'n', '0', '2002-12-13 14:21:52', '1');
INSERT INTO score_input_col VALUES (3, 1, '¹ÎÅé¬¡°Êªí²{', 'ªí²{Àu²§,ªí²{¨}¦n,ªí²{©|¥i,»Ý¦A¥[ªo,¦³«Ý§ï¶i', 'select', '', 'n', 'n', '0', '2002-12-13 14:21:57', '1');
INSERT INTO score_input_col VALUES (4, 1, '¤½¦@ªA°È', 'ªí²{Àu²§,ªí?{¨}¦n,ªí²{©|¥i,»Ý¦A¥[ªo,¦³«Ý§ï¶i', 'select', '', 'n', 'n', '0', '2002-12-13 14:21:47', '1');
INSERT INTO score_input_col VALUES (5, 1, '®Õ¥~¯S®íªí²{', 'ªí²{Àu²§,ªí²{¨}¦n,ªí²{©|¥i,»Ý¦A¥[ªo,¦³«Ý§ï¶i', 'select', '', 'n', 'n', '0', '2002-12-13 14:22:21', '1');
INSERT INTO score_input_col VALUES (6, 1, '¾É®vµû»y¤Î«ØÄ³', '', 'textarea', '', 'n', 'y', '0', '2002-12-19 10:29:00', '1');
INSERT INTO score_input_col VALUES (7, 1, 'µ¥²Ä', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:23:17', '1');
INSERT INTO score_input_col VALUES (9, 1, '¨Æ°²¸`¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:24:47', '1');
INSERT INTO score_input_col VALUES (10, 1, '¯f°²¸`¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:25:08', '1');
INSERT INTO score_input_col VALUES (11, 1, 'Ãm½Ò¸`¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:25:43', '1');
INSERT INTO score_input_col VALUES (12, 1, '¶°·|¦¸¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:26:01', '1');
INSERT INTO score_input_col VALUES (13, 1, '¤½°²¸`¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:26:38', '1');
INSERT INTO score_input_col VALUES (14, 1, '¨ä¥L¸`¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:26:53', '1');
INSERT INTO score_input_col VALUES (16, 1, '¤j¥\\¦¸¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:27:21', '1');
INSERT INTO score_input_col VALUES (17, 1, '¤p¥\\¦¸¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:27:43', '1');
INSERT INTO score_input_col VALUES (18, 1, '¤p¹L¦¸¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:27:58', '1');
INSERT INTO score_input_col VALUES (19, 1, '¹Å¼ú¦¸¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:28:17', '1');
INSERT INTO score_input_col VALUES (20, 1, '¤j¹L¦¸¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:28:30', '1');
INSERT INTO score_input_col VALUES (21, 1, 'Äµ§i¦¸¼Æ', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:28:43', '1');
INSERT INTO score_input_col VALUES (22, 1, '¨ä¥L', '', 'text', '', 'n', 'n', '0', '2002-12-13 14:29:12', '1');
INSERT INTO score_input_col VALUES (24, 1, '¨C¶g¸`¼Æ', '', 'text', 'get_ss_num', 'y', 'n', '0', '2002-12-17 11:38:07', '1');
INSERT INTO score_input_col VALUES (25, 1, '§V¤Oµ{«×', 'ªí²{Àu²§,ªí²{¨}¦n,ªí²{©|¥i,»Ý¦A¥[ªo,¦³«Ý§ï¶i', 'select', '', 'y', 'n', '0', '2002-12-13 14:30:51', '1');
INSERT INTO score_input_col VALUES (26, 1, '¾Ç²ß¦¨´N', '', 'text', 'get_ss_score', 'y', 'n', '0', '2002-12-17 11:38:51', '1');
INSERT INTO score_input_col VALUES (27, 1, '¾Ç²ß´y­z¤å¦r»¡©ú', '', 'text', '', 'y', 'y', '0', '2002-12-19 10:29:12', '1');
# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `score_input_interface`
#

CREATE TABLE score_input_interface (
  interface_sn tinyint(3) unsigned NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  text text NOT NULL,
  html text NOT NULL,
  sshtml text,
  xml text,
  all_ss enum('n','y') NOT NULL default 'n',
  PRIMARY KEY  (interface_sn)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `score_input_interface`
#

INSERT INTO score_input_interface VALUES (1, '¹w³]¦¨ÁZ³æ', '¦¹¦¨ÁZ³æ¾A¦X¤E¦~¤@³e½Òµ{¡A¦¨ÁZ³æª©­±¨Ï¥Î¤¤°Ï¤»¿¤¥«¤E¦~¤@³e½Òµ{µ¦²¤Áp·ùªº¦¨ÁZµû¶q³qª¾®Ñ®æ¦¡¡C', '<table cellspacing="0" cellpadding="0">\r\n<tr>\r\n<td>\r\n	<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%">\r\n	<tr bgcolor="white">\r\n	<td colspan="13" nowrap>¤@¡B¤é±`¥Í¬¡ªí²{µû¶q</td>\r\n	</tr>\r\n	<tr bgcolor="white">\r\n	<td nowrap>¤é±`¦æ¬°ªí²{</td>\r\n	<td colspan="3">{2_¿é¤JÄæ}</td>\r\n	<td colspan="8" nowrap>¾É®vµû»y¤Î«ØÄ³</td>\r\n	<td nowrap>µ¥²Ä</td>\r\n	</tr>\r\n	<tr bgcolor="white">\r\n	<td nowrap>¹ÎÅé¬¡°Êªí²{</td>\r\n	<td colspan="3">{3_¿é¤JÄæ}</td>\r\n	<td rowspan="3" colspan="8">{6_¿é¤JÄæ}</td>\r\n	<td rowspan="3" colspan="1">{7_¿é¤JÄæ}</td>\r\n	</tr>\r\n	<tr bgcolor="white">\r\n	<td nowrap>¤½¦@ªA°È</td>\r\n	<td colspan="3">{4_¿é¤JÄæ}</td>\r\n	</tr>\r\n	<tr bgcolor="white">\r\n	<td nowrap>®Õ¥~¯S®íªí²{</td>\r\n	<td colspan="3">{5_¿é¤JÄæ}</td>\r\n	</tr>\r\n	<tr bgcolor="white">\r\n	<td nowrap>¾Ç¥Í¯Ê®u±¡ªp<br>\r\n	</td>\r\n	<td nowrap>¨Æ°²<br>¸`¼Æ</td>\r\n	<td>{9_¿é¤JÄæ}</td>\r\n	<td nowrap>¯f°²<br>¸`¼Æ</td>\r\n	<td>{10_¿é¤JÄæ}</td>\r\n	<td nowrap>Ãm½Ò<br>¸`¼Æ</td>\r\n	<td>{11_¿é¤JÄæ}</td>\r\n	<td nowrap>¶°·|<br>¦¸¼Æ</td>\r\n	<td>{12_¿é¤JÄæ}</td>\r\n	<td nowrap>¤½°²<br>¸`¼Æ</td>\r\n	<td>{13_¿é¤JÄæ}</td>\r\n	<td nowrap>¨ä¥L<br>¸`¼Æ</td>\r\n	<td>{14_¿é¤JÄæ}</td>\r\n	</tr>\r\n	<tr bgcolor="white">\r\n	<td nowrap>¼úÃg<br>\r\n	</td>\r\n	<td nowrap>¤j¥\\<br>¦¸¼Æ</td>\r\n	<td>{16_¿é¤JÄæ}</td>\r\n	<td nowrap>¤p¥\\<br>¦¸¼Æ</td>\r\n	<td>{17_¿é¤JÄæ}</td>\r\n	<td nowrap>¹Å¼ú<br>¦¸¼Æ</td>\r\n	<td>{18_¿é¤JÄæ}</td>\r\n	<td nowrap>¤j¹L<br>¦¸¼Æ</td>\r\n	<td>{19_¿é¤JÄæ}</td>\r\n	<td nowrap>¤p¹L<br>¦¸¼Æ</td>\r\n	<td>{20_¿é¤JÄæ}</td>\r\n	<td nowrap>Äµ§i<br>¦¸¼Æ</td>\r\n	<td>{21_¿é¤JÄæ}</td>\r\n	</tr>\r\n	<tr bgcolor="white">\r\n	<td nowrap>¨ä¥L</td>\r\n	<td colspan="12">{22_¿é¤JÄæ}</td>\r\n	</tr>\r\n	</table>\r\n</td></tr>\r\n<tr><td>\r\n	<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%">\r\n	<tr bgcolor="#c4d9ff">\r\n	<td>¬ì¥Ø</td>\r\n	<td align="center">¨C¶g¸`¼Æ</td>\r\n	<td align="center">§V¤Oµ{«×</td>\r\n	<td align="center">¾Ç²ß¦¨´N</td>\r\n	<td align="center">¾Ç²ß´y­z¤å¦r»¡©ú</td>\r\n	</tr>\r\n\r\n	<!--¦¹³B·|¦Û°Ê¥[¤J¤U¤è¡y©M¬ì¥Ø¬ÛÃöÄæ¦ì¡zªº³]©w-->\r\n</table>\r\n</td>\r\n</tr>\r\n</table>\r\n', '<tr bgcolor=\'white\'>\r\n<td>{¬ì¥Ø¦WºÙ}</td>\r\n<td align=\'center\'>{24_¿é¤JÄæ}¸`</td>\r\n<td>{25_¿é¤JÄæ}</td>\r\n<td align=\'center\'>{26_¿é¤JÄæ}</td>\r\n<td>{27_¿é¤JÄæ}</td>\r\n</tr>\r\n', '<table:table-row table:style-name="ss_table.1"><table:table-cell table:style-name="ss_table.A2" table:value-type="string"><text:p text:style-name="P5">{ss_name}</text:p></table:table-cell><table:table-cell table:style-name="ss_table.A2" table:value-type="string"><text:p text:style-name="P5">{24_{ss_sn}}</text:p></table:table-cell><table:table-cell table:style-name="ss_table.A2" table:value-type="string"><text:p text:style-name="P5">{25_{ss_sn}}</text:p></table:table-cell><table:table-cell table:style-name="ss_table.A2" table:value-type="string"><text:p text:style-name="P5">{26_{ss_sn}}</text:p></table:table-cell><table:table-cell table:style-name="ss_table.E2" table:value-type="string"><text:p text:style-name="P10">{27_{ss_sn}}</text:p></table:table-cell></table:table-row>', 'y');




#
# ¸ê®Æªí®æ¦¡¡G `score_input_value`
#

CREATE TABLE score_input_value (
  sc_sn int(10) unsigned NOT NULL auto_increment,
  interface_sn tinyint(4) NOT NULL default '0',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  stud_id varchar(20) NOT NULL default '',
  class_id varchar(11) NOT NULL default '',
  value text NOT NULL,
  sel_year smallint(5) NOT NULL default '0',
  sel_seme enum('1','2') NOT NULL default '1',
  PRIMARY KEY  (sc_sn),
  KEY stud_id (stud_id),
  KEY class_id (class_id)
) TYPE=MyISAM;



# --------------------------------------------------------
#
# ¸ê®Æªí®æ¦¡¡G `score_setup`
#

CREATE TABLE score_setup (
  setup_id smallint(5) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  class_year tinyint(2) unsigned NOT NULL default '0',
  allow_modify enum('false','true') NOT NULL default 'false',
  performance_test_times tinyint(1) unsigned default NULL,
  practice_test_times tinyint(2) unsigned default NULL,
  test_ratio varchar(255) default NULL,
  rule varchar(255) NOT NULL default '',
  score_mode enum('all','severally') NOT NULL default 'all',
  sections tinyint(4) NOT NULL default '8',
  interface_sn tinyint(3) unsigned NOT NULL default '1',
  update_date datetime NOT NULL default '0000-00-00 00:00:00',
  enable enum('1','0','always') NOT NULL default '1',
  PRIMARY KEY  (setup_id),
  KEY year (year,semester)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `score_setup`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `score_ss`
#

CREATE TABLE score_ss (
  ss_id smallint(5) unsigned NOT NULL auto_increment,
  scope_id tinyint(3) unsigned NOT NULL default '0',
  subject_id tinyint(3) unsigned NOT NULL default '0',
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  class_id varchar(11) NOT NULL default '',
  class_year tinyint(4) unsigned NOT NULL default '0',
  enable enum('1','0') NOT NULL default '1',
  need_exam enum('1','0') NOT NULL default '1',
  rate tinyint(3) unsigned NOT NULL default '0',
  sort float default NULL,
  sub_sort tinyint(3) unsigned default NULL,
  print enum('1','0') default NULL,
  link_ss varchar(200) NOT NULL default '',
  PRIMARY KEY  (ss_id),
  KEY scope_id (scope_id,subject_id,year),
  KEY class_year (class_year),
  KEY sort (sort),
  KEY class_id (class_id)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `score_subject`
#

CREATE TABLE score_subject (
  subject_id tinyint(3) unsigned NOT NULL auto_increment,
  subject_name varchar(255) NOT NULL default '',
  subject_school set('0','1','2','3','4','5','6','7','8','9','10','11','12') default NULL,
  subject_kind enum('scope','subject') default NULL,
  enable enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (subject_id)
) TYPE=MyISAM;
#
# ¸ê®Æªí®æ¦¡¡G `course_class_time`
#

CREATE TABLE course_class_time (
  cct int(10) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
  class_time varchar(4) NOT NULL default '',
  class_id varchar(11) NOT NULL default '',
  Cyear tinyint(2) unsigned default NULL,
  PRIMARY KEY  (cct),
  KEY year (year,seme)
) TYPE=MyISAM;

# --------------------------------------------------------
#
# ¸ê®Æªí®æ¦¡¡G `course_ss_num`
#

CREATE TABLE course_ss_num (
  csn int(10) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
  ss_id smallint(5) unsigned NOT NULL default '0',
  num tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (csn),
  KEY year (year,seme)
) TYPE=MyISAM;

# --------------------------------------------------------

CREATE TABLE course_teach_num (
  ctn int(10) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  num tinyint(3) unsigned NOT NULL default '0',
  teach_year varchar(255) default NULL,
  PRIMARY KEY  (ctn),
  KEY year (year,seme)
) TYPE=MyISAM;
# --------------------------------------------------------
#
# ¸ê®Æªí®æ¦¡¡G `course_teacher_ss_num`
#

CREATE TABLE course_teacher_ss_num (
  ctsn int(10) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  class_id varchar(11) NOT NULL default '',
  ss_id smallint(5) unsigned NOT NULL default '0',
  num tinyint(3) unsigned NOT NULL default '0',
  other varchar(20) default NULL,
  PRIMARY KEY  (ctsn),
  KEY year (year,seme)
) TYPE=MyISAM;

# --------------------------------------------------------
#
# ¸ê®Æªí®æ¦¡¡G `course_tmp`
#

CREATE TABLE course_tmp (
  ctmp_sn mediumint(8) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  class_id varchar(11) NOT NULL default '',
  teacher_sn mediumint(9) NOT NULL default '0',
  class_year tinyint(2) unsigned NOT NULL default '0',
  class_name tinyint(2) unsigned NOT NULL default '0',
  day enum('0','1','2','3','4','5','6','7') default NULL,
  sector tinyint(1) NOT NULL default '0',
  ss_id smallint(5) unsigned NOT NULL default '0',
  room varchar(10) default NULL,
  other varchar(255) default NULL,
  PRIMARY KEY  (ctmp_sn),
  KEY class_name (class_name),
  KEY class_year (class_year),
  KEY year (year,semester)
) TYPE=MyISAM;



#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `score_subject`
#

INSERT INTO score_subject VALUES (1, ' »y¤å', '', 'scope', '1');
INSERT INTO score_subject VALUES (2, '°·±d»PÅé¨|', '', 'scope', '1');
INSERT INTO score_subject VALUES (3, '¥Í¬¡', '', 'scope', '1');
INSERT INTO score_subject VALUES (4, '¼Æ¾Ç', '', 'scope', '1');
INSERT INTO score_subject VALUES (5, 'ºî¦X¬¡°Ê', '', 'scope', '1');
INSERT INTO score_subject VALUES (6, '°ê»y', '', 'subject', '1');
INSERT INTO score_subject VALUES (7, '¶m¤g»y¤å', '', 'subject', '1');
INSERT INTO score_subject VALUES (8, '¼u©Ê½Òµ{', '', 'scope', '1');
INSERT INTO score_subject VALUES (9, 'ªÀ·|', '', 'scope', '1');
INSERT INTO score_subject VALUES (10, 'ÃÀ³N»P¤H¤å', '', 'scope', '1');
INSERT INTO score_subject VALUES (11, '¦ÛµM»P¥Í¬¡¬ì§Þ', '', 'scope', '1');
INSERT INTO score_subject VALUES (12, '¦ÛµM', '', 'subject', '1');
INSERT INTO score_subject VALUES (13, '¹q¸£', '', 'subject', '1');
INSERT INTO score_subject VALUES (14, '»²¾É¬¡°Ê', '', 'scope', '1');
# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `sfs_log`
#

CREATE TABLE sfs_log (
  log_id bigint(20) NOT NULL auto_increment,
  log_user varchar(20) NOT NULL default '',
  log_table varchar(40) NOT NULL default '',
  log_time timestamp(14) NOT NULL,
  log_ip varchar(16) NOT NULL default '',
  update_kind enum('insert','update','delete','query') NOT NULL default 'insert',
  chang_id varchar(20) NOT NULL default '',
  PRIMARY KEY  (log_id),
  KEY log_user (log_user)
) TYPE=MyISAM COMMENT='¨t²Î?O¿ý';

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `sfs_log`
#

# --------------------------------------------------------

ALTER TABLE `teacher_base` RENAME `teacher_base1`;

 CREATE TABLE `teacher_base` (
`teach_id` varchar(20) NOT NULL default '',
`teach_person_id` varchar(10) NOT NULL default '',
`name` varchar(20) NOT NULL default '',
`sex` tinyint(3) unsigned NOT NULL default '0',
`age` tinyint(3) unsigned NOT NULL default '0',
`birthday` date NOT NULL default '0000-00-00',
`birth_place` tinyint(3) unsigned NOT NULL default '0',
`marriage` tinyint(3) unsigned default NULL,
`address` varchar(60) default NULL,
`home_phone` varchar(20) default NULL,
`cell_phone` varchar(20) default NULL,
`office_home` varchar(20) default NULL,
`teach_condition` tinyint(3) unsigned NOT NULL default '0',
`teach_memo` varchar(30) default NULL,
`login_pass` varchar(12) NOT NULL default '',
`teach_edu_kind` tinyint(3) unsigned NOT NULL default '0',
`teach_edu_abroad` varchar(4) NOT NULL default '',
`teach_sub_kind` varchar(10) NOT NULL default '',
`teach_check_kind` tinyint(3) unsigned NOT NULL default '0',
`teach_check_word` varchar(30) NOT NULL default '',
`teach_is_cripple` char(2) NOT NULL default '',
`update_time` timestamp(14) NOT NULL,
`update_id` varchar(20) NOT NULL default '',
`teacher_sn` smallint(6) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`teach_id`),
  KEY teacher_sn (`teacher_sn`)
) TYPE=MyISAM;
INSERT INTO `teacher_base` SELECT *,'' FROM `teacher_base1`;

DROP TABLE `teacher_base1`;

#
# Table structure for table `pro_user_state`
#

CREATE TABLE pro_user_state (
  teacher_sn smallint(6) NOT NULL default '0',
  pu_state tinyint(4) NOT NULL default '0',
  pu_time datetime default NULL,
  pu_time_over datetime NOT NULL default '0000-00-00 00:00:00',
  pu_ip varchar(20) NOT NULL default ''
) TYPE=MyISAM COMMENT='¨Ï¥ÎªÌª¬ºA';

CREATE TABLE stud_compile (
  compile_sn int(10) unsigned NOT NULL auto_increment,
  student_sn int(10) unsigned NOT NULL default '0',
  sort tinyint(3) unsigned NOT NULL default '0',
  old_class varchar(11) NOT NULL default '',
  new_class varchar(11) NOT NULL default '',
  site_num tinyint(3) unsigned NOT NULL default '0',
  sex tinyint(1) unsigned NOT NULL default '0',
  stud_birthday date NOT NULL default '0000-00-00',
  update_time datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (compile_sn)
) TYPE=MyISAM;


CREATE TABLE pro_check_new (
  p_id int(10) unsigned NOT NULL auto_increment,
  pro_kind_id smallint(5) unsigned NOT NULL default '0',
  id_kind enum('³B«Ç','±Ð®v','Â¾ºÙ','¾Ç¸¹','¨ä¥L') default '¨ä¥L',
  id_sn int(10) unsigned NOT NULL default '0',
  is_admin enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (p_id),
  KEY pro_kind_id (pro_kind_id)
) TYPE=MyISAM;


#
# ¸ê®Æªí®æ¦¡¡G `stud_addr_zip`
#

CREATE TABLE stud_addr_zip (
  zip char(3) NOT NULL default '',
  country varchar(10) NOT NULL default '',
  town varchar(10) NOT NULL default '',
  area_num varchar(6) NOT NULL default '',
  PRIMARY KEY  (zip)
) TYPE=MyISAM COMMENT='¶l»¼°Ï¸¹';

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_addr_zip`
#

INSERT INTO stud_addr_zip VALUES ('100', '¥x¥_¥«', '¤¤¥¿°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('103', '¥x¥_¥«', '¤j¦P°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('104', '¥x¥_¥«', '¤¤¤s°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('105', '¥x¥_¥«', 'ªQ¤s°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('106', '¥x¥_¥«', '¤j¦w°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('108', '¥x¥_¥«', '¸UµØ°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('110', '¥x¥_¥«', '«H¸q°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('111', '¥x¥_¥«', '¤hªL°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('112', '¥x¥_¥«', '¥_§ë°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('114', '¥x¥_¥«', '¤º´ò°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('115', '¥x¥_¥«', '«n´ä°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('116', '¥x¥_¥«', '¤å¤s°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('117', '¥x¥_¥«', '´º¬ü°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('200', '°ò¶©¥«', '¤¯·R°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('201', '°ò¶©¥«', '«H¸q°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('202', '°ò¶©¥«', '¤¤¥¿°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('203', '°ò¶©¥«', '¤¤¤s°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('204', '°ò¶©¥«', '¦w¼Ö°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('205', '°ò¶©¥«', '·x·x°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('206', '°ò¶©¥«', '¤C°ô°Ï', '02');
INSERT INTO stud_addr_zip VALUES ('207', '¥x¥_¿¤', '¸U¨½¶m', '02');
INSERT INTO stud_addr_zip VALUES ('208', '¥x¥_¿¤', 'ª÷¤s¶m', '02');
INSERT INTO stud_addr_zip VALUES ('209', '³s¦¿¿¤', '°¨¯ª«n', '0836');
INSERT INTO stud_addr_zip VALUES ('210', '³s¦¿¿¤', '°¨¯ª¥_', '0837');
INSERT INTO stud_addr_zip VALUES ('211', '³s¦¿¿¤', '°¨¯ª²÷', '0838');
INSERT INTO stud_addr_zip VALUES ('212', '³s¦¿¿¤', '°¨¯ªªF', '0839');
INSERT INTO stud_addr_zip VALUES ('220', '¥x¥_¿¤', 'ªO¾ô¥«', '02');
INSERT INTO stud_addr_zip VALUES ('221', '¥x¥_¿¤', '¦Á¤î¥«', '02');
INSERT INTO stud_addr_zip VALUES ('222', '¥x¥_¿¤', '²`§|¶m', '02');
INSERT INTO stud_addr_zip VALUES ('223', '¥x¥_¿¤', '¥ÛÞä¶m', '02');
INSERT INTO stud_addr_zip VALUES ('224', '¥x¥_¿¤', '·çªÚÂí', '02');
INSERT INTO stud_addr_zip VALUES ('226', '¥x¥_¿¤', '¥­·Ë¶m', '02');
INSERT INTO stud_addr_zip VALUES ('227', '¥x¥_¿¤', 'Âù·Ë¶m', '02');
INSERT INTO stud_addr_zip VALUES ('228', '¥x¥_¿¤', '°^¼d¶m', '02');
INSERT INTO stud_addr_zip VALUES ('231', '¥x¥_¿¤', '·s©±¥«', '02');
INSERT INTO stud_addr_zip VALUES ('232', '¥x¥_¿¤', '©WªL¶m', '02');
INSERT INTO stud_addr_zip VALUES ('233', '¥x¥_¿¤', '¯Q¨Ó¶m', '02');
INSERT INTO stud_addr_zip VALUES ('234', '¥x¥_¿¤', '¥Ã©M¥«', '02');
INSERT INTO stud_addr_zip VALUES ('235', '¥x¥_¿¤', '¤¤©M¥«', '02');
INSERT INTO stud_addr_zip VALUES ('236', '¥x¥_¿¤', '¤g«°¥«', '02');
INSERT INTO stud_addr_zip VALUES ('237', '¥x¥_¿¤', '¤T®lÂí', '02');
INSERT INTO stud_addr_zip VALUES ('238', '¥x¥_¿¤', '¾ðªL¥«', '02');
INSERT INTO stud_addr_zip VALUES ('239', '¥x¥_¿¤', 'ÅaºqÂí', '02');
INSERT INTO stud_addr_zip VALUES ('241', '¥x¥_¿¤', '¤T­«¥«', '02');
INSERT INTO stud_addr_zip VALUES ('242', '¥x¥_¿¤', '·s²ø¥«', '02');
INSERT INTO stud_addr_zip VALUES ('243', '¥x¥_¿¤', '®õ¤s¶m', '02');
INSERT INTO stud_addr_zip VALUES ('244', '¥x¥_¿¤', 'ªL¤f¶m', '02');
INSERT INTO stud_addr_zip VALUES ('247', '¥x¥_¿¤', 'Äª¬w¥«', '02');
INSERT INTO stud_addr_zip VALUES ('248', '¥x¥_¿¤', '¤­ªÑ¶m', '02');
INSERT INTO stud_addr_zip VALUES ('249', '¥x¥_¿¤', '¤K¨½¶m', '02');
INSERT INTO stud_addr_zip VALUES ('251', '¥x¥_¿¤', '²H¤ôÂí', '02');
INSERT INTO stud_addr_zip VALUES ('252', '¥x¥_¿¤', '¤TªÛ¶m', '02');
INSERT INTO stud_addr_zip VALUES ('253', '¥x¥_¿¤', '¥Ûªù¶m', '02');
INSERT INTO stud_addr_zip VALUES ('260', '©yÄõ¿¤', '©yÄõ¥«', '039');
INSERT INTO stud_addr_zip VALUES ('261', '©yÄõ¿¤', 'ÀY«°Âí', '039');
INSERT INTO stud_addr_zip VALUES ('262', '©yÄõ¿¤', 'ÁG·Ë¶m', '039');
INSERT INTO stud_addr_zip VALUES ('263', '©yÄõ¿¤', '§§³ò¶m', '039');
INSERT INTO stud_addr_zip VALUES ('264', '©yÄõ¿¤', '­û¤s¶m', '039');
INSERT INTO stud_addr_zip VALUES ('265', '©yÄõ¿¤', 'Ã¹ªFÂí', '039');
INSERT INTO stud_addr_zip VALUES ('266', '©yÄõ¿¤', '¤T¬P¶m', '039');
INSERT INTO stud_addr_zip VALUES ('267', '©yÄõ¿¤', '¤j¦P¶m', '039');
INSERT INTO stud_addr_zip VALUES ('268', '©yÄõ¿¤', '¤­µ²¶m', '039');
INSERT INTO stud_addr_zip VALUES ('269', '©yÄõ¿¤', '¥V¤s¶m', '039');
INSERT INTO stud_addr_zip VALUES ('270', '©yÄõ¿¤', 'Ä¬¿DÂí', '039');
INSERT INTO stud_addr_zip VALUES ('272', '©yÄõ¿¤', '«n¿D¶m', '039');
INSERT INTO stud_addr_zip VALUES ('300', '·s¦Ë¥«', '·s¦Ë¥«', '035');
INSERT INTO stud_addr_zip VALUES ('302', '·s¦Ë¿¤', '¦Ë¥_¥«', '035');
INSERT INTO stud_addr_zip VALUES ('303', '·s¦Ë¿¤', '´ò¤f¶m', '035');
INSERT INTO stud_addr_zip VALUES ('304', '·s¦Ë¿¤', '·sÂ×¶m', '035');
INSERT INTO stud_addr_zip VALUES ('305', '·s¦Ë¿¤', '·s®HÂí', '035');
INSERT INTO stud_addr_zip VALUES ('306', '·s¦Ë¿¤', 'Ãö¦èÂí', '035');
INSERT INTO stud_addr_zip VALUES ('307', '·s¦Ë¿¤', 'Ë|ªL¶m', '035');
INSERT INTO stud_addr_zip VALUES ('308', '·s¦Ë¿¤', 'Ä_¤s¶m', '035');
INSERT INTO stud_addr_zip VALUES ('310', '·s¦Ë¿¤', '¦ËªFÂí', '035');
INSERT INTO stud_addr_zip VALUES ('311', '·s¦Ë¿¤', '¤­®p¶m', '035');
INSERT INTO stud_addr_zip VALUES ('312', '·s¦Ë¿¤', '¾î¤s¶m', '035');
INSERT INTO stud_addr_zip VALUES ('313', '·s¦Ë¿¤', '¦y¥Û¶m', '035');
INSERT INTO stud_addr_zip VALUES ('314', '·s¦Ë¿¤', '¥_®H¶m', '035');
INSERT INTO stud_addr_zip VALUES ('315', '·s¦Ë¿¤', '®o¬Ü¶m', '035');
INSERT INTO stud_addr_zip VALUES ('320', '®ç¶é¿¤', '¤¤Ãc¥«', '03');
INSERT INTO stud_addr_zip VALUES ('324', '®ç¶é¿¤', '¥­Âí¥«', '03');
INSERT INTO stud_addr_zip VALUES ('325', '®ç¶é¿¤', 'Às¼æ¶m', '03');
INSERT INTO stud_addr_zip VALUES ('326', '®ç¶é¿¤', '·¨±öÂí', '03');
INSERT INTO stud_addr_zip VALUES ('327', '®ç¶é¿¤', '·s«Î¶m', '03');
INSERT INTO stud_addr_zip VALUES ('328', '®ç¶é¿¤', 'Æ[­µ¶m', '03');
INSERT INTO stud_addr_zip VALUES ('330', '®ç¶é¿¤', '®ç¶é¥«', '03');
INSERT INTO stud_addr_zip VALUES ('333', '®ç¶é¿¤', 'Àt¤s¶m', '03');
INSERT INTO stud_addr_zip VALUES ('334', '®ç¶é¿¤', '¤K¼w¥«', '03');
INSERT INTO stud_addr_zip VALUES ('335', '®ç¶é¿¤', '¤j·ËÂí', '03');
INSERT INTO stud_addr_zip VALUES ('336', '®ç¶é¿¤', '´_¿³¶m', '03');
INSERT INTO stud_addr_zip VALUES ('337', '®ç¶é¿¤', '¤j¶é¶m', '03');
INSERT INTO stud_addr_zip VALUES ('338', '®ç¶é¿¤', 'Äª¦Ë¶m', '03');
INSERT INTO stud_addr_zip VALUES ('350', '­]®ß¿¤', '¦Ë«nÂí', '037');
INSERT INTO stud_addr_zip VALUES ('351', '­]®ß¿¤', 'ÀY¥÷Âí', '037');
INSERT INTO stud_addr_zip VALUES ('352', '­]®ß¿¤', '¤TÆW¶m', '037');
INSERT INTO stud_addr_zip VALUES ('353', '­]®ß¿¤', '«nÉÜ¶m', '037');
INSERT INTO stud_addr_zip VALUES ('354', '­]®ß¿¤', '·à¼æ¶m', '037');
INSERT INTO stud_addr_zip VALUES ('356', '­]®ß¿¤', '«áÀsÂí', '037');
INSERT INTO stud_addr_zip VALUES ('357', '­]®ß¿¤', '³q¾]Âí', '037');
INSERT INTO stud_addr_zip VALUES ('358', '­]®ß¿¤', '­b¸ÌÂí', '037');
INSERT INTO stud_addr_zip VALUES ('360', '­]®ß¿¤', '­]®ß¥«', '037');
INSERT INTO stud_addr_zip VALUES ('361', '­]®ß¿¤', '³y¾ô¶m', '037');
INSERT INTO stud_addr_zip VALUES ('362', '­]®ß¿¤', 'ÀY«Î¶m', '037');
INSERT INTO stud_addr_zip VALUES ('363', '­]®ß¿¤', '¤½À]¶m', '037');
INSERT INTO stud_addr_zip VALUES ('364', '­]®ß¿¤', '¤j´ò¶m', '037');
INSERT INTO stud_addr_zip VALUES ('365', '­]®ß¿¤', '®õ¦w¶m', '037');
INSERT INTO stud_addr_zip VALUES ('366', '­]®ß¿¤', '»ÉÆr¶m', '037');
INSERT INTO stud_addr_zip VALUES ('367', '­]®ß¿¤', '¤T¸q¶m', '037');
INSERT INTO stud_addr_zip VALUES ('368', '­]®ß¿¤', '¦è´ò¶m', '037');
INSERT INTO stud_addr_zip VALUES ('369', '­]®ß¿¤', '¨ôÄõÂí', '04');
INSERT INTO stud_addr_zip VALUES ('400', '¥x¤¤¥«', '¤¤°Ï', '04');
INSERT INTO stud_addr_zip VALUES ('401', '¥x¤¤¥«', 'ªF°Ï', '04');
INSERT INTO stud_addr_zip VALUES ('402', '¥x¤¤¥«', '«n°Ï', '04');
INSERT INTO stud_addr_zip VALUES ('403', '¥x¤¤¥«', '¦è°Ï', '04');
INSERT INTO stud_addr_zip VALUES ('404', '¥x¤¤¥«', '¥_°Ï', '04');
INSERT INTO stud_addr_zip VALUES ('406', '¥x¤¤¥«', '¥_¤Ù', '04');
INSERT INTO stud_addr_zip VALUES ('407', '¥x¤¤¥«', '¦è¤Ù', '04');
INSERT INTO stud_addr_zip VALUES ('408', '¥x¤¤¥«', '«n¤Ù', '04');
INSERT INTO stud_addr_zip VALUES ('411', '¥x¤¤¿¤', '¤Ó¥­¥«', '04');
INSERT INTO stud_addr_zip VALUES ('412', '¥x¤¤¿¤', '¤j¨½¥«', '04');
INSERT INTO stud_addr_zip VALUES ('413', '¥x¤¤¿¤', 'Ãú®p¶m', '04');
INSERT INTO stud_addr_zip VALUES ('414', '¥x¤¤¿¤', '¯Q¤é¶m', '04');
INSERT INTO stud_addr_zip VALUES ('420', '¥x¤¤¿¤', 'Â×­ì¥«', '04');
INSERT INTO stud_addr_zip VALUES ('421', '¥x¤¤¿¤', '¦Z¨½¶m', '04');
INSERT INTO stud_addr_zip VALUES ('422', '¥x¤¤¿¤', '¥Û©£¶m', '04');
INSERT INTO stud_addr_zip VALUES ('423', '¥x¤¤¿¤', 'ªF¶ÕÂí', '04');
INSERT INTO stud_addr_zip VALUES ('424', '¥x¤¤¿¤', '©M¥­¶m', '04');
INSERT INTO stud_addr_zip VALUES ('426', '¥x¤¤¿¤', '·sªÀ¶m', '04');
INSERT INTO stud_addr_zip VALUES ('427', '¥x¤¤¿¤', '¼æ¤l¶m', '04');
INSERT INTO stud_addr_zip VALUES ('428', '¥x¤¤¿¤', '¤j¶®¶m', '04');
INSERT INTO stud_addr_zip VALUES ('429', '¥x¤¤¿¤', '¯«©£¶m', '04');
INSERT INTO stud_addr_zip VALUES ('432', '¥x¤¤¿¤', '¤j¨{¶m', '04');
INSERT INTO stud_addr_zip VALUES ('433', '¥x¤¤¿¤', '¨F³ÀÂí', '04');
INSERT INTO stud_addr_zip VALUES ('434', '¥x¤¤¿¤', 'Às¤«¶m', '04');
INSERT INTO stud_addr_zip VALUES ('435', '¥x¤¤¿¤', '±ï´ÏÂí', '04');
INSERT INTO stud_addr_zip VALUES ('436', '¥x¤¤¿¤', '²M¤ôÂí', '04');
INSERT INTO stud_addr_zip VALUES ('437', '¥x¤¤¿¤', '¤j¥ÒÂí', '04');
INSERT INTO stud_addr_zip VALUES ('438', '¥x¤¤¿¤', '¥~®H¶m', '04');
INSERT INTO stud_addr_zip VALUES ('439', '¥x¤¤¿¤', '¤j¦w¶m', '04');
INSERT INTO stud_addr_zip VALUES ('500', '¹ü¤Æ¿¤', '¹ü¤Æ¥«', '04');
INSERT INTO stud_addr_zip VALUES ('502', '¹ü¤Æ¿¤', 'ªâ¶é¶m', '04');
INSERT INTO stud_addr_zip VALUES ('503', '¹ü¤Æ¿¤', 'ªá¾Â¶m', '04');
INSERT INTO stud_addr_zip VALUES ('504', '¹ü¤Æ¿¤', '¨q¤ô¶m', '04');
INSERT INTO stud_addr_zip VALUES ('505', '¹ü¤Æ¿¤', '³À´äÂí', '04');
INSERT INTO stud_addr_zip VALUES ('506', '¹ü¤Æ¿¤', 'ºÖ¿³¶m', '04');
INSERT INTO stud_addr_zip VALUES ('507', '¹ü¤Æ¿¤', '½u¦è¶m', '04');
INSERT INTO stud_addr_zip VALUES ('508', '¹ü¤Æ¿¤', '©M¬üÂí', '04');
INSERT INTO stud_addr_zip VALUES ('509', '¹ü¤Æ¿¤', '¦ù´ä¶m', '04');
INSERT INTO stud_addr_zip VALUES ('510', '¹ü¤Æ¿¤', '­ûªLÂí', '04');
INSERT INTO stud_addr_zip VALUES ('511', '¹ü¤Æ¿¤', 'ªÀÀY¶m', '04');
INSERT INTO stud_addr_zip VALUES ('512', '¹ü¤Æ¿¤', '¥Ã¹t¶m', '04');
INSERT INTO stud_addr_zip VALUES ('513', '¹ü¤Æ¿¤', '®H¤ß¶m', '04');
INSERT INTO stud_addr_zip VALUES ('514', '¹ü¤Æ¿¤', '·Ë´òÂí', '04');
INSERT INTO stud_addr_zip VALUES ('515', '¹ü¤Æ¿¤', '¤j§ø¶m', '04');
INSERT INTO stud_addr_zip VALUES ('516', '¹ü¤Æ¿¤', '®HÆQ¶m', '04');
INSERT INTO stud_addr_zip VALUES ('520', '¹ü¤Æ¿¤', '¥Ð¤¤Âí', '04');
INSERT INTO stud_addr_zip VALUES ('521', '¹ü¤Æ¿¤', '¥_¤æÂí', '04');
INSERT INTO stud_addr_zip VALUES ('522', '¹ü¤Æ¿¤', '¥Ð§À¶m', '04');
INSERT INTO stud_addr_zip VALUES ('523', '¹ü¤Æ¿¤', '°ñÀY¶m', '04');
INSERT INTO stud_addr_zip VALUES ('524', '¹ü¤Æ¿¤', '·Ë¦{¶m', '04');
INSERT INTO stud_addr_zip VALUES ('525', '¹ü¤Æ¿¤', '¦Ë¶í¶m', '04');
INSERT INTO stud_addr_zip VALUES ('526', '¹ü¤Æ¿¤', '¤GªLÂí', '04');
INSERT INTO stud_addr_zip VALUES ('527', '¹ü¤Æ¿¤', '¤j«°¶m', '04');
INSERT INTO stud_addr_zip VALUES ('528', '¹ü¤Æ¿¤', 'ªÚ­b¶m', '04');
INSERT INTO stud_addr_zip VALUES ('530', '¹ü¤Æ¿¤', '¤G¤ô¶m', '04');
INSERT INTO stud_addr_zip VALUES ('540', '«n§ë¿¤', '«n§ë¥«', '049');
INSERT INTO stud_addr_zip VALUES ('541', '«n§ë¿¤', '¤¤¼d¶m', '049');
INSERT INTO stud_addr_zip VALUES ('542', '«n§ë¿¤', '¯ó¤ÙÂí', '049');
INSERT INTO stud_addr_zip VALUES ('544', '«n§ë¿¤', '°ê©m¶m', '049');
INSERT INTO stud_addr_zip VALUES ('545', '«n§ë¿¤', '®H¨½Âí', '049');
INSERT INTO stud_addr_zip VALUES ('546', '«n§ë¿¤', '¤¯·R¶m', '049');
INSERT INTO stud_addr_zip VALUES ('551', '«n§ë¿¤', '¦W¶¡¶m', '049');
INSERT INTO stud_addr_zip VALUES ('552', '«n§ë¿¤', '¶°¶°Âí', '049');
INSERT INTO stud_addr_zip VALUES ('553', '«n§ë¿¤', '¤ô¨½¶m', '049');
INSERT INTO stud_addr_zip VALUES ('555', '«n§ë¿¤', '³½¦À¶m', '049');
INSERT INTO stud_addr_zip VALUES ('556', '«n§ë¿¤', '«H¸q¶m', '049');
INSERT INTO stud_addr_zip VALUES ('557', '«n§ë¿¤', '¦Ë¤sÂí', '049');
INSERT INTO stud_addr_zip VALUES ('558', '«n§ë¿¤', '³À¨¦¶m', '049');
INSERT INTO stud_addr_zip VALUES ('600', '¹Å¸q¥«', '¦è°Ï¤Í', '05');
INSERT INTO stud_addr_zip VALUES ('602', '¹Å¸q¿¤', 'µf¸ô¶m', '05');
INSERT INTO stud_addr_zip VALUES ('603', '¹Å¸q¿¤', '±ö¤s¶m', '05');
INSERT INTO stud_addr_zip VALUES ('604', '¹Å¸q¿¤', '¦Ë±T¶m', '05');
INSERT INTO stud_addr_zip VALUES ('605', '¹Å¸q¿¤', 'ªü¨½¤s¶m', '05');
INSERT INTO stud_addr_zip VALUES ('606', '¹Å¸q¿¤', '¤¤®H¶m', '05');
INSERT INTO stud_addr_zip VALUES ('607', '¹Å¸q¿¤', '¤j®H¶m', '05');
INSERT INTO stud_addr_zip VALUES ('608', '¹Å¸q¿¤', '¤ô¤W¶m', '05');
INSERT INTO stud_addr_zip VALUES ('611', '¹Å¸q¿¤', '³À¯ó¶m', '05');
INSERT INTO stud_addr_zip VALUES ('612', '¹Å¸q¿¤', '¤Ó«O¥«', '05');
INSERT INTO stud_addr_zip VALUES ('613', '¹Å¸q¿¤', '¦µ¤l¥«', '05');
INSERT INTO stud_addr_zip VALUES ('614', '¹Å¸q¿¤', 'ªF¥Û¶m', '05');
INSERT INTO stud_addr_zip VALUES ('615', '¹Å¸q¿¤', '¤»¸}¶m', '05');
INSERT INTO stud_addr_zip VALUES ('616', '¹Å¸q¿¤', '·s´ä¶m', '05');
INSERT INTO stud_addr_zip VALUES ('621', '¹Å¸q¿¤', '¥Á¶¯¶m', '05');
INSERT INTO stud_addr_zip VALUES ('622', '¹Å¸q¿¤', '¤jªLÂí', '05');
INSERT INTO stud_addr_zip VALUES ('623', '¹Å¸q¿¤', '·Ë¤f¶m', '05');
INSERT INTO stud_addr_zip VALUES ('624', '¹Å¸q¿¤', '¸q¦Ë¶m', '05');
INSERT INTO stud_addr_zip VALUES ('625', '¹Å¸q¿¤', '¥¬³UÂí', '05');
INSERT INTO stud_addr_zip VALUES ('630', '¶³ªL¿¤', '¤æ«nÂí', '05');
INSERT INTO stud_addr_zip VALUES ('631', '¶³ªL¿¤', '¤j°ñ¶m', '05');
INSERT INTO stud_addr_zip VALUES ('632', '¶³ªL¿¤', 'ªê§ÀÂí', '05');
INSERT INTO stud_addr_zip VALUES ('633', '¶³ªL¿¤', '¤g®wÂí', '05');
INSERT INTO stud_addr_zip VALUES ('634', '¶³ªL¿¤', '½Ç©¾¶m', '05');
INSERT INTO stud_addr_zip VALUES ('635', '¶³ªL¿¤', 'ªF¶Õ¶m', '05');
INSERT INTO stud_addr_zip VALUES ('636', '¶³ªL¿¤', '¥x¦è¶m', '05');
INSERT INTO stud_addr_zip VALUES ('637', '¶³ªL¿¤', '±[­I¶m', '05');
INSERT INTO stud_addr_zip VALUES ('638', '¶³ªL¿¤', '³Á¼d¶m', '05');
INSERT INTO stud_addr_zip VALUES ('640', '¶³ªL¿¤', '¤æ¤»¥«', '05');
INSERT INTO stud_addr_zip VALUES ('643', '¶³ªL¿¤', 'ªL¤º¶m', '05');
INSERT INTO stud_addr_zip VALUES ('646', '¶³ªL¿¤', '¥j§|¶m', '05');
INSERT INTO stud_addr_zip VALUES ('647', '¶³ªL¿¤', 'Ûl®ä¶m', '05');
INSERT INTO stud_addr_zip VALUES ('648', '¶³ªL¿¤', '¦èÁ³Âí', '05');
INSERT INTO stud_addr_zip VALUES ('649', '¶³ªL¿¤', '¤G±[¶m', '05');
INSERT INTO stud_addr_zip VALUES ('651', '¶³ªL¿¤', '¥_´äÂí', '05');
INSERT INTO stud_addr_zip VALUES ('652', '¶³ªL¿¤', '¤ôªL¶m', '05');
INSERT INTO stud_addr_zip VALUES ('653', '¶³ªL¿¤', '¤f´ò¶m', '05');
INSERT INTO stud_addr_zip VALUES ('654', '¶³ªL¿¤', '¥|´ò¶m', '05');
INSERT INTO stud_addr_zip VALUES ('655', '¶³ªL¿¤', '¤¸ªø¶m', '05');
INSERT INTO stud_addr_zip VALUES ('700', '¥x«n¥«', '¤¤°Ï', '06');
INSERT INTO stud_addr_zip VALUES ('701', '¥x«n¥«', 'ªF°Ï', '06');
INSERT INTO stud_addr_zip VALUES ('702', '¥x«n¥«', '«n°Ï', '06');
INSERT INTO stud_addr_zip VALUES ('703', '¥x«n¥«', '¦è°Ï', '06');
INSERT INTO stud_addr_zip VALUES ('704', '¥x«n¥«', '¥_°Ï', '06');
INSERT INTO stud_addr_zip VALUES ('708', '¥x«n¥«', '¦w¥­°Ï', '06');
INSERT INTO stud_addr_zip VALUES ('709', '¥x«n¥«', '¦w«n°Ï', '06');
INSERT INTO stud_addr_zip VALUES ('710', '¥x«n¿¤', '¥Ã±d¥«', '06');
INSERT INTO stud_addr_zip VALUES ('711', '¥x«n¿¤', 'Âk¤¯¶m', '06');
INSERT INTO stud_addr_zip VALUES ('712', '¥x«n¿¤', '·s¤ÆÂí', '06');
INSERT INTO stud_addr_zip VALUES ('713', '¥x«n¿¤', '¥ªÂí¶m', '06');
INSERT INTO stud_addr_zip VALUES ('714', '¥x«n¿¤', '¥É¤«¶m', '06');
INSERT INTO stud_addr_zip VALUES ('715', '¥x«n¿¤', '·£¦è¶m', '06');
INSERT INTO stud_addr_zip VALUES ('716', '¥x«n¿¤', '«n¤Æ¶m', '06');
INSERT INTO stud_addr_zip VALUES ('717', '¥x«n¿¤', '¤¯¼w¶m', '06');
INSERT INTO stud_addr_zip VALUES ('718', '¥x«n¿¤', 'Ãö¼q¶m', '06');
INSERT INTO stud_addr_zip VALUES ('719', '¥x«n¿¤', 'Às±T¶m', '06');
INSERT INTO stud_addr_zip VALUES ('720', '¥x«n¿¤', '©x¥Ð¶m', '06');
INSERT INTO stud_addr_zip VALUES ('721', '¥x«n¿¤', '³Â¨§Âí', '06');
INSERT INTO stud_addr_zip VALUES ('722', '¥x«n¿¤', '¨Î¨½Âí', '06');
INSERT INTO stud_addr_zip VALUES ('723', '¥x«n¿¤', '¦è´ä¶m', '06');
INSERT INTO stud_addr_zip VALUES ('724', '¥x«n¿¤', '¤CªÑ¶m', '06');
INSERT INTO stud_addr_zip VALUES ('725', '¥x«n¿¤', '±N­x¶m', '06');
INSERT INTO stud_addr_zip VALUES ('726', '¥x«n¿¤', '¾Ç¥ÒÂí', '06');
INSERT INTO stud_addr_zip VALUES ('727', '¥x«n¿¤', '¥_ªù¶m', '06');
INSERT INTO stud_addr_zip VALUES ('730', '¥x«n¿¤', '·sÀç¥«', '06');
INSERT INTO stud_addr_zip VALUES ('731', '¥x«n¿¤', '«á¾À¶m', '06');
INSERT INTO stud_addr_zip VALUES ('732', '¥x«n¿¤', '¥ÕªeÂí', '06');
INSERT INTO stud_addr_zip VALUES ('733', '¥x«n¿¤', 'ªF¤s¶m', '06');
INSERT INTO stud_addr_zip VALUES ('734', '¥x«n¿¤', '¤»¥Ò¶m', '06');
INSERT INTO stud_addr_zip VALUES ('735', '¥x«n¿¤', '¤UÀç¶m', '06');
INSERT INTO stud_addr_zip VALUES ('736', '¥x«n¿¤', '¬hÀç¶m', '06');
INSERT INTO stud_addr_zip VALUES ('737', '¥x«n¿¤', 'ÆQ¤ôÂí', '06');
INSERT INTO stud_addr_zip VALUES ('741', '¥x«n¿¤', 'µ½¤ÆÂí', '06');
INSERT INTO stud_addr_zip VALUES ('742', '¥x«n¿¤', '¤j¤º¶m', '06');
INSERT INTO stud_addr_zip VALUES ('743', '¥x«n¿¤', '¤s¤W¶m', '06');
INSERT INTO stud_addr_zip VALUES ('744', '¥x«n¿¤', '·s¥«¶m', '06');
INSERT INTO stud_addr_zip VALUES ('745', '¥x«n¿¤', '¦w©w¶m', '06');
INSERT INTO stud_addr_zip VALUES ('800', '°ª¶¯¥«', '·s¿³°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('801', '°ª¶¯¥«', '«eª÷°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('802', '°ª¶¯¥«', '­d¶®°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('803', '°ª¶¯¥«', 'ÆQÑL°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('804', '°ª¶¯¥«', '¹ª¤s°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('805', '°ª¶¯¥«', 'ºX¬z°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('806', '°ª¶¯¥«', '«eÂí°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('807', '°ª¶¯¥«', '¤T¥Á°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('811', '°ª¶¯¥«', '·£±ê°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('812', '°ª¶¯¥«', '¤p´ä°Ï', '07');
INSERT INTO stud_addr_zip VALUES ('813', '°ª¶¯¥«', '¥ªÀç¦Û', '07');
INSERT INTO stud_addr_zip VALUES ('814', '°ª¶¯¿¤', '¤¯ªZ¶m', '07');
INSERT INTO stud_addr_zip VALUES ('815', '°ª¶¯¿¤', '¤jªÀ¶m', '07');
INSERT INTO stud_addr_zip VALUES ('817', '«n®ü½Ñ®q', 'ªF¨F', '0827');
INSERT INTO stud_addr_zip VALUES ('819', '«n®ü½Ñ®q', '«n¨F', '0827');
INSERT INTO stud_addr_zip VALUES ('820', '°ª¶¯¿¤', '©£¤sÂí', '07');
INSERT INTO stud_addr_zip VALUES ('821', '°ª¶¯¿¤', '¸ô¦Ë¶m', '07');
INSERT INTO stud_addr_zip VALUES ('822', '°ª¶¯¿¤', 'ªü½¬¶m', '07');
INSERT INTO stud_addr_zip VALUES ('823', '°ª¶¯¿¤', '¥Ð¼d¶m', '07');
INSERT INTO stud_addr_zip VALUES ('824', '°ª¶¯¿¤', '¿P±_¶m', '07');
INSERT INTO stud_addr_zip VALUES ('825', '°ª¶¯¿¤', '¾ôÀY¶m', '07');
INSERT INTO stud_addr_zip VALUES ('826', '°ª¶¯¿¤', '±ê©x¶m', '07');
INSERT INTO stud_addr_zip VALUES ('827', '°ª¶¯¿¤', 'À±ªû¶m', '07');
INSERT INTO stud_addr_zip VALUES ('828', '°ª¶¯¿¤', '¥Ã¦w¶m', '07');
INSERT INTO stud_addr_zip VALUES ('829', '°ª¶¯¿¤', '´ò¤º¶m', '07');
INSERT INTO stud_addr_zip VALUES ('830', '°ª¶¯¿¤', '»ñ¤s¥«', '07');
INSERT INTO stud_addr_zip VALUES ('831', '°ª¶¯¿¤', '¤j¼d¶m', '07');
INSERT INTO stud_addr_zip VALUES ('832', '°ª¶¯¿¤', 'ªL¶é¶m', '07');
INSERT INTO stud_addr_zip VALUES ('833', '°ª¶¯¿¤', '³¾ªQ¶m', '07');
INSERT INTO stud_addr_zip VALUES ('840', '°ª¶¯¿¤', '¤j¾ð¶m', '07');
INSERT INTO stud_addr_zip VALUES ('842', '°ª¶¯¿¤', 'ºX¤sÂí', '07');
INSERT INTO stud_addr_zip VALUES ('843', '°ª¶¯¿¤', '¬ü¿@Âí', '07');
INSERT INTO stud_addr_zip VALUES ('844', '°ª¶¯¿¤', '¤»Àt¶m', '07');
INSERT INTO stud_addr_zip VALUES ('845', '°ª¶¯¿¤', '¤ºªù¶m', '07');
INSERT INTO stud_addr_zip VALUES ('846', '°ª¶¯¿¤', '§üªL¶m', '07');
INSERT INTO stud_addr_zip VALUES ('847', '°ª¶¯¿¤', '¥Ò¥P¶m', '07');
INSERT INTO stud_addr_zip VALUES ('848', '°ª¶¯¿¤', '®ç·½¶m', '07');
INSERT INTO stud_addr_zip VALUES ('849', '°ª¶¯¿¤', '¤T¥Á¶m', '07');
INSERT INTO stud_addr_zip VALUES ('851', '°ª¶¯¿¤', '­ZªL¶m', '07');
INSERT INTO stud_addr_zip VALUES ('852', '°ª¶¯¿¤', '­XÛ_¶m', '07');
INSERT INTO stud_addr_zip VALUES ('880', '¼ê´ò¿¤', '°¨¤½¥«', '06');
INSERT INTO stud_addr_zip VALUES ('881', '¼ê´ò¿¤', '¦èÀ¬¶m', '06');
INSERT INTO stud_addr_zip VALUES ('882', '¼ê´ò¿¤', '±æ¦w¶m', '06');
INSERT INTO stud_addr_zip VALUES ('883', '¼ê´ò¿¤', '¤C¬ü¶m', '06');
INSERT INTO stud_addr_zip VALUES ('884', '¼ê´ò¿¤', '¥Õ¨F¶m', '06');
INSERT INTO stud_addr_zip VALUES ('885', '¼ê´ò¿¤', '´ò¦è¶m', '06');
INSERT INTO stud_addr_zip VALUES ('890', 'ª÷ªù¿¤', 'ª÷¨FÂí', '0823');
INSERT INTO stud_addr_zip VALUES ('891', 'ª÷ªù¿¤', 'ª÷´òÂí', '0823');
INSERT INTO stud_addr_zip VALUES ('892', 'ª÷ªù¿¤', 'ª÷¹ç¶m', '0823');
INSERT INTO stud_addr_zip VALUES ('893', 'ª÷ªù¿¤', 'ª÷«°Âí', '0823');
INSERT INTO stud_addr_zip VALUES ('894', 'ª÷ªù¿¤', '¯PÀ¬¶m', '0823');
INSERT INTO stud_addr_zip VALUES ('896', 'ª÷ªù¿¤', '¯QËú', '0826');
INSERT INTO stud_addr_zip VALUES ('900', '«ÌªF¿¤', '«ÌªF¥«', '08');
INSERT INTO stud_addr_zip VALUES ('901', '«ÌªF¿¤', '¤T¦a¶m', '08');
INSERT INTO stud_addr_zip VALUES ('902', '«ÌªF¿¤', 'Ãú¥x¶m', '08');
INSERT INTO stud_addr_zip VALUES ('903', '«ÌªF¿¤', 'º¿®a¶m', '08');
INSERT INTO stud_addr_zip VALUES ('904', '«ÌªF¿¤', '¤E¦p¶m', '08');
INSERT INTO stud_addr_zip VALUES ('905', '«ÌªF¿¤', '¨½´ä¶m', '08');
INSERT INTO stud_addr_zip VALUES ('906', '«ÌªF¿¤', '°ª¾ð¶m', '08');
INSERT INTO stud_addr_zip VALUES ('907', '«ÌªF¿¤', 'ÆQ®H¶m', '08');
INSERT INTO stud_addr_zip VALUES ('908', '«ÌªF¿¤', 'ªøªv¶m', '08');
INSERT INTO stud_addr_zip VALUES ('909', '«ÌªF¿¤', 'Åï¬¥¶m', '08');
INSERT INTO stud_addr_zip VALUES ('911', '«ÌªF¿¤', '¦Ë¥Ð¶m', '08');
INSERT INTO stud_addr_zip VALUES ('912', '«ÌªF¿¤', '¤º®H¶m', '08');
INSERT INTO stud_addr_zip VALUES ('913', '«ÌªF¿¤', '¸U¤¦¶m', '08');
INSERT INTO stud_addr_zip VALUES ('920', '«ÌªF¿¤', '¼é¦{Âí', '08');
INSERT INTO stud_addr_zip VALUES ('921', '«ÌªF¿¤', '®õªZ¶m', '08');
INSERT INTO stud_addr_zip VALUES ('922', '«ÌªF¿¤', '¨Ó¸q¶m', '08');
INSERT INTO stud_addr_zip VALUES ('923', '«ÌªF¿¤', '¸UÅr¶m', '08');
INSERT INTO stud_addr_zip VALUES ('924', '«ÌªF¿¤', '®r³»¶m', '08');
INSERT INTO stud_addr_zip VALUES ('925', '«ÌªF¿¤', '·s°ñ¶m', '08');
INSERT INTO stud_addr_zip VALUES ('926', '«ÌªF¿¤', '«n¦{¶m', '08');
INSERT INTO stud_addr_zip VALUES ('927', '«ÌªF¿¤', 'ªLÃä¶m', '08');
INSERT INTO stud_addr_zip VALUES ('928', '«ÌªF¿¤', 'ªF´äÂí', '08');
INSERT INTO stud_addr_zip VALUES ('929', '«ÌªF¿¤', '¯[²y¶m', '08');
INSERT INTO stud_addr_zip VALUES ('931', '«ÌªF¿¤', '¨Î¥V¶m', '08');
INSERT INTO stud_addr_zip VALUES ('932', '«ÌªF¿¤', '·s¶é¶m', '08');
INSERT INTO stud_addr_zip VALUES ('940', '«ÌªF¿¤', 'ªD¼d¶m', '08');
INSERT INTO stud_addr_zip VALUES ('941', '«ÌªF¿¤', 'ªD¤s¶m', '08');
INSERT INTO stud_addr_zip VALUES ('942', '«ÌªF¿¤', '¬K¤é¶m', '08');
INSERT INTO stud_addr_zip VALUES ('943', '«ÌªF¿¤', '·à¤l¶m', '08');
INSERT INTO stud_addr_zip VALUES ('944', '«ÌªF¿¤', '¨®«°¶m', '08');
INSERT INTO stud_addr_zip VALUES ('945', '«ÌªF¿¤', '¨d¤¦¶m', '08');
INSERT INTO stud_addr_zip VALUES ('946', '«ÌªF¿¤', '«í¬KÂí', '08');
INSERT INTO stud_addr_zip VALUES ('947', '«ÌªF¿¤', 'º¡¦{¶m', '08');
INSERT INTO stud_addr_zip VALUES ('950', '¥xªF¿¤', '¥xªF¥«', '089');
INSERT INTO stud_addr_zip VALUES ('951', '¥xªF¿¤', 'ºñ®q¶m', '089');
INSERT INTO stud_addr_zip VALUES ('952', '¥xªF¿¤', 'ÄõÀ¬¶m', '089');
INSERT INTO stud_addr_zip VALUES ('953', '¥xªF¿¤', '©µ¥­¶m', '089');
INSERT INTO stud_addr_zip VALUES ('954', '¥xªF¿¤', '¨õ«n¶m', '089');
INSERT INTO stud_addr_zip VALUES ('955', '¥xªF¿¤', '³À³¥¶m', '089');
INSERT INTO stud_addr_zip VALUES ('956', '¥xªF¿¤', 'Ãö¤sÂí', '089');
INSERT INTO stud_addr_zip VALUES ('957', '¥xªF¿¤', '®üºÝ¶m', '089');
INSERT INTO stud_addr_zip VALUES ('958', '¥xªF¿¤', '¦À¤W¶m', '089');
INSERT INTO stud_addr_zip VALUES ('959', '¥xªF¿¤', 'ªFªe¶m', '089');
INSERT INTO stud_addr_zip VALUES ('961', '¥xªF¿¤', '¦¨¥Âí', '089');
INSERT INTO stud_addr_zip VALUES ('962', '¥xªF¿¤', 'ªøÀØ¶m', '089');
INSERT INTO stud_addr_zip VALUES ('963', '¥xªF¿¤', '¤Ó³Â¨½', '089');
INSERT INTO stud_addr_zip VALUES ('964', '¥xªF¿¤', 'ª÷®p¶m', '089');
INSERT INTO stud_addr_zip VALUES ('965', '¥xªF¿¤', '¤jªZ¶m', '089');
INSERT INTO stud_addr_zip VALUES ('966', '¥xªF¿¤', '¹F¤¯¶m', '089');
INSERT INTO stud_addr_zip VALUES ('970', 'ªá½¬¿¤', 'ªá½¬¥«', '038');
INSERT INTO stud_addr_zip VALUES ('971', 'ªá½¬¿¤', '·s«°¶m', '038');
INSERT INTO stud_addr_zip VALUES ('972', 'ªá½¬¿¤', '¨qªL¶m', '038');
INSERT INTO stud_addr_zip VALUES ('973', 'ªá½¬¿¤', '¦N¦w¶m', '038');
INSERT INTO stud_addr_zip VALUES ('974', 'ªá½¬¿¤', '¹ØÂ×¶m', '038');
INSERT INTO stud_addr_zip VALUES ('975', 'ªá½¬¿¤', '»ñªLÂí', '038');
INSERT INTO stud_addr_zip VALUES ('976', 'ªá½¬¿¤', '¥ú´_¶m', '038');
INSERT INTO stud_addr_zip VALUES ('977', 'ªá½¬¿¤', 'Â×ÀØ¶m', '038');
INSERT INTO stud_addr_zip VALUES ('978', 'ªá½¬¿¤', '·çÁJ¶m', '038');
INSERT INTO stud_addr_zip VALUES ('979', 'ªá½¬¿¤', '¸Uºa¶m', '038');
INSERT INTO stud_addr_zip VALUES ('981', 'ªá½¬¿¤', '¥É¨½Âí', '038');
INSERT INTO stud_addr_zip VALUES ('982', 'ªá½¬¿¤', '¨ô·Ë¶m', '038');
INSERT INTO stud_addr_zip VALUES ('983', 'ªá½¬¿¤', '´I¨½¶m', '038');

#
# ¸ê®Æªí®æ¦¡¡G `sfs_module`
#

CREATE TABLE sfs_module (
  msn smallint(5) unsigned NOT NULL auto_increment,
  showname varchar(100) NOT NULL default '',
  dirname varchar(100) NOT NULL default '',
  sort smallint(5) unsigned NOT NULL default '0',
  isopen tinyint(1) NOT NULL default '0',
  islive tinyint(4) NOT NULL default '1',
  of_group smallint(5) unsigned NOT NULL default '0',
  ver varchar(20) NOT NULL default '',
  icon_image varchar(255) NOT NULL default '',
  author varchar(100) NOT NULL default '',
  creat_date date NOT NULL default '0000-00-00',
  kind enum('¼Ò²Õ','¤ÀÃþ') NOT NULL default '¼Ò²Õ',
  txt varchar(255) NOT NULL default '',
  PRIMARY KEY  (msn),
  KEY sort (sort,of_group)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `sfs_module`
#
INSERT INTO sfs_module VALUES (1,'¨t²ÎºÞ²z','',7,0,1,0,'','administrator_icon.png','','0000-00-00','¤ÀÃþ','');
INSERT INTO sfs_module VALUES (2,'®Õ°È¦æ¬F','',1,1,1,0,'','school_icon.png','','0000-00-00','¤ÀÃþ','');
INSERT INTO sfs_module VALUES (8,'¤å¥ó¸ê®Æ®w','docup',4,1,1,2,'2.0.1','','hami','2002-12-15','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (9,'¤ÈÀ\\­¹ÃÐ¤½§i','lunch',3,1,1,2,'1.0.8','','prolin','2000-09-17','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (7,'¹Ï®ÑºÞ²z¨t²Î','book',2,1,1,2,'2.0.1','','hami','2002-12-15','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (41,'«n¿¤±Ð®vºÞ²z','tnc_teach_class',6,0,1,12,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (11,'®Õ¶é¦æ¨Æ¾ä','school_calendar',1,1,1,2,'1.0','','tad','2003-03-24','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (12,'±Ð°È','',2,0,1,0,'','school_affairs_icon.png','','0000-00-00','¤ÀÃþ','');
INSERT INTO sfs_module VALUES (13,'°V¾É','',3,0,1,0,'','student_counsellor_icon.png','','0000-00-00','¤ÀÃþ','');
INSERT INTO sfs_module VALUES (14,'»²¾É','',4,0,0,0,'','advisory_icon.png','','0000-00-00','¤ÀÃþ','');
INSERT INTO sfs_module VALUES (15,'±ÐÂ¾­û','',6,0,1,0,'','school_teacher_icon.png','','0000-00-00','¤ÀÃþ','');
INSERT INTO sfs_module VALUES (16,'±Ð¾Ç²Õ','',3,0,1,12,'','student_edu_icon.png','','0000-00-00','¤ÀÃþ','');
INSERT INTO sfs_module VALUES (17,'µù¥U²Õ','',4,0,1,12,'','student_reg_icon.png','','0000-00-00','¤ÀÃþ','');
INSERT INTO sfs_module VALUES (18,'¨t²Î³Æ¥÷','backup',2,0,1,1,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (19,'«ü©w·sºôºÞ','chang_root',8,0,1,1,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (20,'¸ê®Æ®wÄæ¦ìºÞ²z','database_info',6,0,1,1,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (21,'¾Ç®Õ³]©w','school_setup',1,0,1,12,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (23,'¦¨ÁZ³æ¤¶­±ºÞ²z','score_input_interface',11,0,0,1,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (24,'¼Ò²ÕÅv­­ºÞ²z','sfs_man2',1,0,1,1,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (26,'¨t²Î¿ï¶µ²M³æ³]©w','sfs_text',7,0,1,1,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (60,'¯ÊÃm½Ò¼úÃgºÞ²z','absent',1,0,1,13,'1.0','','','2003-04-18','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (28,'½u¤W½Õ¬d¨t²Î','online_form',13,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (29,'¾Ç´Áªì³]©w','every_year_setup',2,0,1,12,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (30,'¦æ¨Æ¾ä','calendar',16,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (31,'¯Z¯Å¾ÇÄyºÞ²z','stud_class',1,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (32,'±M¬ì±Ð«Ç¹w¬ù','new_compclass',15,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (33,'¦¨ÁZºÞ²z','score_input',12,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (34,'±Ð®vºÞ²z','teach_class',5,0,1,12,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (35,'¦æ¬F±K½X¬d¸ß','teacher_pass',9,0,1,1,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (36,'¤½§iºÞ²z','new_board',14,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (37,'»s§@¦¨ÁZ³æ','academic_record',11,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (40,'¾Ç®Õ½Òªí¶×¥X¨t²Î','course_paper',1,0,1,16,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (42,'¦¨ÁZ¬d¸ß','score_list',2,0,1,16,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (43,'¦¨ÁZºÞ²z','score_manage',3,0,1,16,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (44,'¾ÇÄyºÞ²z','stud_reg',1,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (45,'¾Ç¥Í²§°Ê','stud_move',2,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (46,'¾Ç¥Í¸ê®Æ¬d¸ß²Î­p','stud_query',3,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (47,'¶×¤J¸ê®Æ','create_data',4,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (48,'½s¯Z§@·~','stud_year',5,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (49,'¾ÇÄy³øªí','stud_report',6,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (50,'¦¨ÁZ¿é¤J','score_input_all',7,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (51,'S§Î¦Û°Ê½s¯Z','stud_compile',8,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (52,'·s¥Í½s¯Z','temp_compile',9,0,1,17,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (53,'²¦·~¥Í§@·~','graduate',10,0,1,17,'','','JRH','2003-03-25','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (54,'§ó§ï±K½X','chpass',21,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (55,'¯Å°ÈºÞ²z','class_things',2,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (56,'¼Æ¦ì¬Û¥»','mig',5,1,1,2,'2.0.1','','hami','2003-03-19','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (57,'±Ð®v³q°T¿ý','teach_report_more',22,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (58,'­Ó¤H¸ê®Æ','teacher_self',20,0,1,15,'','','','0000-00-00','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (59,'¾Ç®Õ½Òªí¬d¸ß¨t²Î','new_course',6,1,1,2,'1.0','','','2003-01-01','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (61,'®Õ°È§G§iÄæ','board',7,1,1,2,'','','hami','2003-04-06','¼Ò²Õ','');
INSERT INTO sfs_module VALUES (62,'®Õ°È§G§iÄæºÞ²zµ{¦¡','board_man',12,0,1,1,'','','hami','2003-04-06','¼Ò²Õ','');


# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `calendar`
#

CREATE TABLE calendar (
  cal_sn smallint(5) unsigned NOT NULL auto_increment,
  year smallint(4) unsigned NOT NULL default '0',
  month tinyint(2) unsigned NOT NULL default '0',
  day tinyint(2) unsigned NOT NULL default '0',
  week enum('0','1','2','3','4','5','6') NOT NULL default '0',
  time time NOT NULL default '00:00:00',
  place varchar(255) NOT NULL default '',
  thing text NOT NULL,
  kind varchar(255) NOT NULL default '',
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  from_teacher_sn smallint(5) unsigned NOT NULL default '0',
  from_cal_sn mediumint(8) unsigned NOT NULL default '0',
  restart enum('0','md','d','w') NOT NULL default '0',
  restart_day date NOT NULL default '0000-00-00',
  restart_end date NOT NULL default '0000-00-00',
  import varchar(255) NOT NULL default '',
  post_time datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (cal_sn),
  KEY time (time),
  KEY teacher_sn (teacher_sn),
  KEY from_cal_sn (from_cal_sn),
  KEY week (week),
  KEY restart_day (restart_day,restart_end)
) TYPE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `course_room`
#

CREATE TABLE course_room (
  crsn int(10) unsigned NOT NULL auto_increment,
  date date NOT NULL default '0000-00-00',
  day enum('0','1','2','3','4','5','6','7') NOT NULL default '0',
  sector tinyint(1) unsigned NOT NULL default '0',
  room varchar(50) NOT NULL default '',
  teacher_sn mediumint(8) unsigned NOT NULL default '0',
  sign_date datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (crsn),
  KEY teacher_sn (teacher_sn),
  KEY sector (sector),
  KEY day (day)
) TYPE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `grad_stud`
#

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
) TYPE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `lunchtb`
#

CREATE TABLE lunchtb (
  pDate date NOT NULL default '0000-00-00',
  pMday tinyint(4) NOT NULL default '0',
  pFood varchar(20) default NULL,
  pMenu varchar(120) default NULL,
  pFruit varchar(20) default NULL,
  pPs varchar(20) default NULL,
  pDesign varchar(20) default NULL,
  PRIMARY KEY  (pDate)
) TYPE=MyISAM;

#
# ¸ê®Æªí®æ¦¡¡G `school_day`
#

CREATE TABLE school_day (
  day_kind varchar(40) NOT NULL default '',
  day date NOT NULL default '0000-00-00',
  year tinyint(2) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
  UNIQUE KEY year_seme (day_kind,year,seme)
) TYPE=MyISAM;


#
# ¸ê®Æªí®æ¦¡¡G `stud_absence`
#

CREATE TABLE stud_absence (
  abs_sn bigint(20) NOT NULL auto_increment,
  date date NOT NULL default '0000-00-00',
  stud_id varchar(20) NOT NULL default '',
  ab1 tinyint(1) default NULL,
  ab2 tinyint(1) default NULL,
  ab3 tinyint(1) default NULL,
  ab4 tinyint(1) default NULL,
  ab5 tinyint(1) default NULL,
  ab6 tinyint(1) default NULL,
  ab7 tinyint(1) default NULL,
  meno varchar(200) default NULL,
  PRIMARY KEY  (abs_sn)
) TYPE=MyISAM;

#
# ¦C¥X¥H¤U¸ê®Æ®wªº¼Æ¾Ú¡G `stud_absence`
#

# --------------------------------------------------------

#
# ¸ê®Æªí®æ¦¡¡G `stud_absent`
#

CREATE TABLE stud_absent (
  sasn int(10) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  class_id varchar(11) NOT NULL default '',
  stud_id varchar(20) NOT NULL default '',
  date date NOT NULL default '0000-00-00',
  absent_kind varchar(20) NOT NULL default '',
  section varchar(10) NOT NULL default '',
  sign_man_sn int(11) NOT NULL default '0',
  sign_man_name varchar(20) NOT NULL default '',
  sign_time datetime NOT NULL default '0000-00-00 00:00:00',
  txt text NOT NULL,
  PRIMARY KEY  (sasn),
  UNIQUE KEY date (stud_id,date,section),
  KEY year (year,semester,class_id,stud_id),
  KEY sign_man_sn (sign_man_sn)
) TYPE=MyISAM;


#
# ¸ê®Æªí®æ¦¡¡G `sfs3_log`
#

CREATE TABLE sfs3_log (
  log_sn int(10) unsigned NOT NULL auto_increment,
  log text NOT NULL,
  mark varchar(255) NOT NULL default '',
  id varchar(20) NOT NULL default '',
  time datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (log_sn)
) TYPE=MyISAM;



# ±Ò¥Î·sªº¼Ò²Õ
#UPDATE pro_kind SET store_path='admin/sfs_man2' WHERE store_path='admin/sfs_man';

   
# §R°£¿ù»~ªº»{ÃÒÃþ§O
#delete from pro_check where pro_kind_id=0;

#§ó¥¿¸ê®Æªí stud_seme

ALTER TABLE `stud_seme` CHANGE `seme_class_s` `seme_class_s` TINYINT UNSIGNED NOT NULL ;
ALTER TABLE `stud_seme` CHANGE `seme_num` `seme_num` TINYINT UNSIGNED NOT NULL;
ALTER TABLE `stud_seme` CHANGE `seme_num_s` `seme_num_s` TINYINT UNSIGNED NOT NULL;

# §ó¥¿ teacher_base

ALTER TABLE `teacher_base` CHANGE `teach_sub_kind` `teach_sub_kind` VARCHAR( 40 ) NOT NULL;

ALTER TABLE `teacher_post` CHANGE `post_kind` `post_kind` INT( 4 ) UNSIGNED DEFAULT '0' NOT NULL; 
ALTER TABLE `teacher_post` CHANGE `post_office` `post_office` VARCHAR( 40 ) NOT NULL ;
ALTER TABLE `teacher_post` CHANGE `post_class` `post_class` VARCHAR( 40 ) NOT NULL;
ALTER TABLE `teacher_post` CHANGE `teach_title_id` `teach_title_id` VARCHAR( 40 ) NOT NULL;
ALTER TABLE `teacher_title` CHANGE `teach_title_id` `teach_title_id` TINYINT UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `teacher_title` CHANGE `title_name` `title_name` VARCHAR( 50 ) NOT NULL  ;

#§ó¥¿³B«Ç¸ê®Æªí
ALTER TABLE `school_room` ADD `enable` ENUM('1','0') DEFAULT '1' NOT NULL;
#§ó¥¿³B«Ç¸ê®Æªí
ALTER TABLE `teacher_title` ADD `enable` ENUM('1','0') DEFAULT '1' NOT NULL;
#§ó§ï ¨t²Î¿ï¶µ³]©wÄæ¦ì
ALTER TABLE `sfs_text` CHANGE `d_id` `d_id` VARCHAR( 20 ) NOT NULL ;
ALTER TABLE `sfs_text` ADD `t_order_id` INT NOT NULL AFTER `t_id` ;
UPDATE `sfs_text` SET t_order_id = d_id;
#§ó§ï »{ÃÒ¸ê®Æªí
ALTER TABLE `pro_check_new` ADD `set_sn` INT UNSIGNED NOT NULL ,
ADD `p_start_date` DATE,
ADD `p_end_date` DATE NOT NULL ,
ADD `oth_set` VARCHAR( 20 ) NOT NULL ;

