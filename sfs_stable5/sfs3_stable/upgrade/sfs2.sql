# $Id: sfs2.sql 5311 2009-01-10 08:11:55Z hami $
# phpMyAdmin MySQL-Dump
# version 2.2.0rc3
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# 主機: localhost
# Generation Time: August 6, 2001, 11:30 am
# Server version: 3.22.32
# PHP Version: 4.0.6
# 數據庫 : sfs2
# --------------------------------------------------------

#
# 數據表的結構 'pro_check'
#

DROP TABLE IF EXISTS pro_check;
CREATE TABLE pro_check (
   pc_id bigint(20) DEFAULT '0' NOT NULL auto_increment,
   pro_kind_id smallint(6) DEFAULT '0' NOT NULL,
   post_office tinyint(4) DEFAULT '-1' NOT NULL,
   teach_id varchar(20) DEFAULT 'none' NOT NULL,
   teach_title_id tinyint(4) DEFAULT '-1' NOT NULL,
   is_admin char(1) NOT NULL,
   PRIMARY KEY (pc_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'pro_check_stu'
#

DROP TABLE IF EXISTS pro_check_stu;
CREATE TABLE pro_check_stu (
   pc_id int(11) DEFAULT '0' NOT NULL auto_increment,
   pro_kind_id smallint(6) DEFAULT '0' NOT NULL,
   stud_id varchar(20) NOT NULL,
   teach_id varchar(20) NOT NULL,
   use_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   use_last_date date DEFAULT '0000-00-00' NOT NULL,
   class_num varchar(6) NOT NULL,
   PRIMARY KEY (pc_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'pro_kind'
#

DROP TABLE IF EXISTS pro_kind;
CREATE TABLE pro_kind (
   pro_kind_id smallint(6) DEFAULT '0' NOT NULL auto_increment,
   pro_kind_name varchar(40) NOT NULL,
   pro_kind_order tinyint(4) DEFAULT '0' NOT NULL,
   home_index varchar(30) NOT NULL,
   store_path varchar(200) NOT NULL,
   pro_author text NOT NULL,
   pro_parent smallint(6) DEFAULT '0' NOT NULL,
   PRIMARY KEY (pro_kind_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'school_base'
#
DROP TABLE IF EXISTS school_base;
CREATE TABLE school_base (
  sch_id varchar(6) NOT NULL default '',
  sch_attr_id varchar(6) NOT NULL default '',
  sch_cname varchar(40) NOT NULL default '',
  sch_cname_s varchar(40) NOT NULL default '',
  sch_cname_ss varchar(40) NOT NULL default '',
  sch_ename varchar(40) NOT NULL default '',
  sch_sheng varchar(10) NOT NULL default '',
  sch_cdate date NOT NULL default '0000-00-00',
  sch_mark varchar(8) NOT NULL default '',
  sch_class varchar(8) NOT NULL default '',
  sch_montain char(2) NOT NULL default '',
  sch_area_tol float(10,2) NOT NULL default '0.00',
  sch_area_ext float(10,2) NOT NULL default '0.00',
  sch_area_pin float(10,2) NOT NULL default '0.00',
  sch_money float(10,2) NOT NULL default '0.00',
  sch_money_o float(10,2) NOT NULL default '0.00',
  sch_local_name varchar(10) NOT NULL default '',
  sch_post_num varchar(5) NOT NULL default '',
  sch_addr varchar(60) NOT NULL default '',
  sch_phone varchar(20) default NULL,
  sch_fax varchar(20) default NULL,
  sch_area varchar(20) NOT NULL default '',
  sch_kind varchar(6) NOT NULL default '',
  sch_url varchar(50) NOT NULL default '',
  sch_email varchar(30) NOT NULL default '',
  update_time datetime NOT NULL default '0000-00-00 00:00:00',
  update_id varchar(20) NOT NULL default '',
  update_ip varchar(15) NOT NULL default '',
  PRIMARY KEY  (sch_id)
) TYPE=MyISAM;

#
# Dumping data for table `school_base`
#

INSERT INTO school_base VALUES ('','公立','校園自由軟體交流網','校園自由軟體交流網','校園自由軟體交流網','sfs','台中縣','0000-00-00','正常','一般地區','否','0.00','0.00','0.00','0.00','0.00','','','','','','','','sfs.wpes.tcc.edu.tw','','2001-10-01 21:20:56','','192.168.0.1');

    

#
# 數據表的結構 'school_class_num' 學年度班級數
#

DROP TABLE IF EXISTS school_class_num;
CREATE TABLE school_class_num (
   curr_class_year varchar(5) NOT NULL,
   c_year char(3) DEFAULT '0' NOT NULL,
   c_num tinyint(4) DEFAULT '0' NOT NULL,
   UNIQUE curr_class_year (curr_class_year, c_year)
);



#
# 數據表的結構 'school_room'
#
DROP TABLE IF EXISTS school_room;
CREATE TABLE school_room (
   room_id tinyint(3) unsigned DEFAULT '0' NOT NULL auto_increment,
   room_name varchar(30) NOT NULL,
   room_tel varchar(20) NOT NULL,
   room_fax varchar(20) NOT NULL,
   PRIMARY KEY (room_id)
);


#
# 數據表的結構 'seme_class'
#

DROP TABLE IF EXISTS seme_class;
CREATE TABLE seme_class (
   current_school_year smallint(4) DEFAULT '0' NOT NULL,
   teach_id varchar(20) NOT NULL,
   teach_title_id tinyint(4) DEFAULT '0' NOT NULL,
   class_num varchar(6) NOT NULL,
   subject_id1 tinyint(3) DEFAULT '0' NOT NULL,
   subject_id2 tinyint(3) unsigned DEFAULT '0' NOT NULL,
   subject_id3 tinyint(3) unsigned DEFAULT '0' NOT NULL,
   subject_id4 tinyint(3) DEFAULT '0' NOT NULL,
   subject_id5 tinyint(3) unsigned DEFAULT '0' NOT NULL,
   subject_id6 tinyint(3) unsigned DEFAULT '0' NOT NULL,
   PRIMARY KEY (current_school_year, teach_id)
);
# --------------------------------------------------------

DROP TABLE IF EXISTS stud_addr;
CREATE TABLE stud_addr (
   addr_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   stud_id varchar(20) NOT NULL,
   stud_addr_h_a varchar(6) NOT NULL,
   stud_addr_h_b varchar(10) NOT NULL,
   stud_addr_h_c varchar(6) NOT NULL,
   stud_addr_h_d varchar(6) NOT NULL,
   stud_addr_h_e varchar(20) NOT NULL,
   stud_addr_h_f varchar(4) NOT NULL,
   stud_addr_h_g varchar(8) NOT NULL,
   stud_addr_h_h varchar(6) NOT NULL,
   stud_addr_h_i varchar(6) NOT NULL,
   stud_addr_h_j varchar(6) NOT NULL,
   stud_addr_h_k varchar(5) NOT NULL,
   stud_addr_h_l varchar(5) NOT NULL,
   stud_phone_h varchar(20) NOT NULL,
   stud_handphone_h varchar(20) NOT NULL,
   stud_addr_c_a varchar(6) NOT NULL,
   stud_addr_c_b varchar(10) NOT NULL,
   stud_addr_c_c varchar(6) NOT NULL,
   stud_addr_c_d varchar(6) NOT NULL,
   stud_addr_c_e varchar(20) NOT NULL,
   stud_addr_c_f varchar(4) NOT NULL,
   stud_addr_c_g varchar(8) NOT NULL,
   stud_addr_c_h varchar(6) NOT NULL,
   stud_addr_c_i varchar(6) NOT NULL,
   stud_addr_c_j varchar(6) NOT NULL,
   stud_addr_c_k varchar(5) NOT NULL,
   stud_addr_c_l varchar(5) NOT NULL,
   stud_phone_c varchar(20) NOT NULL,
   stud_handphone_c varchar(20) NOT NULL,
   update_id varchar(20) NOT NULL,
   update_time timestamp(14),
   is_same char(1) DEFAULT '1' NOT NULL,
   PRIMARY KEY (addr_id),
   KEY stud_phone_h (stud_phone_h),
   KEY stud_id (stud_id)
);


# --------------------------------------------------------

#
# 數據表的結構 'stud_base'
#

DROP TABLE IF EXISTS stud_base;
CREATE TABLE stud_base (
   stud_id varchar(20) NOT NULL,
   stud_name varchar(20) NOT NULL,
   stud_person_id varchar(10) NOT NULL,
   stud_country varchar(20) NOT NULL,
   stud_abroad varchar(20) NOT NULL,
   addr_id bigint(20) unsigned DEFAULT '0' NOT NULL,
   stud_birthday date DEFAULT '0000-00-00' NOT NULL,
   stud_sex tinyint(3) unsigned DEFAULT '0' NOT NULL,
   stud_blood_type tinyint(3) unsigned DEFAULT '0' NOT NULL,
   stud_study_cond tinyint(3) unsigned DEFAULT '0',
   stud_study_year int(10) unsigned DEFAULT '0' NOT NULL,
   condition tinyint(3) unsigned DEFAULT '0' NOT NULL,
   stud_row tinyint(3) unsigned DEFAULT '0' NOT NULL,
   sister_brother tinyint(3) unsigned DEFAULT '0' NOT NULL,
   email_pass varchar(12) NOT NULL,
   create_date date DEFAULT '0000-00-00' NOT NULL,
   stud_kind tinyint(4) DEFAULT '0' NOT NULL,
   stud_class_kind tinyint(4) DEFAULT '0' NOT NULL,
   stud_spe_kind tinyint(4) DEFAULT '0' NOT NULL,
   stud_spe_class_kind tinyint(4) DEFAULT '0' NOT NULL,
   stud_preschool_id varchar(6) NOT NULL,
   stud_preschool_name varchar(40) NOT NULL,
   stud_preschool_status tinyint(4) DEFAULT '0' NOT NULL,
   stud_hospital varchar(20) NOT NULL,
   stud_graduate_kind char(2) NOT NULL,
   stud_graduate_date date DEFAULT '0000-00-00' NOT NULL,
   stud_graduate_word varchar(20) NOT NULL,
   stud_graduate_num varchar(14),
   stud_graduate_school varchar(30) NOT NULL,   
   class_num_1 varchar(6) NOT NULL,
   class_num_2 varchar(6) NOT NULL,
   class_num_3 varchar(6) NOT NULL,
   class_num_4 varchar(6) NOT NULL,
   class_num_5 varchar(6) NOT NULL,
   class_num_6 varchar(6) NOT NULL,
   class_num_7 varchar(6) NOT NULL,
   class_num_8 varchar(6) NOT NULL,
   class_num_9 varchar(6) NOT NULL,
   class_num_10 varchar(6) NOT NULL,
   class_num_11 varchar(6) NOT NULL,
   class_num_12 varchar(6) NOT NULL,
   update_id varchar(20) NOT NULL,
   update_time timestamp(14),
   curr_class_num varchar(6),
   PRIMARY KEY (stud_id)
);


# --------------------------------------------------------

#
# 數據表的結構 'stud_behabior'
#

DROP TABLE IF EXISTS stud_behabior;
CREATE TABLE stud_behabior (
   be_id bigint(20) unsigned DEFAULT '0' NOT NULL,
   be_date date DEFAULT '0000-00-00' NOT NULL,
   stud_id varchar(20) NOT NULL,
   be_reason varchar(50) DEFAULT '0' NOT NULL,
   update_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (be_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_brother_sister'
#

DROP TABLE IF EXISTS stud_brother_sister;
CREATE TABLE stud_brother_sister (
   bs_id bigint(20) DEFAULT '0' NOT NULL auto_increment,
   stud_id varchar(20) NOT NULL,
   bs_name varchar(20) DEFAULT '0' NOT NULL,
   bs_calling tinyint(3) unsigned DEFAULT '0' NOT NULL,
   bs_gradu varchar(20) NOT NULL,
   bs_birthyear tinyint(3) unsigned DEFAULT '0' NOT NULL,   
   PRIMARY KEY (bs_id)
);


#
# Table structure for table stud_domicile
#

DROP TABLE IF EXISTS stud_domicile;
CREATE TABLE stud_domicile (
   addr_id bigint(20) DEFAULT '0' NOT NULL,
   stud_id varchar(20) NOT NULL,
   fath_name varchar(20) NOT NULL,
   fath_birthyear varchar(4) NOT NULL,
   fath_alive tinyint(3) unsigned DEFAULT '1' NOT NULL,
   fath_relation varchar(6) NOT NULL,
   fath_country varchar(20) NOT NULL,
   fath_p_id varchar(20) NOT NULL,
   fath_abroad varchar(20),
   fath_education tinyint(3) unsigned DEFAULT '0' NOT NULL,
   fath_occupation varchar(20) NOT NULL,
   fath_unit varchar(20) NOT NULL,
   fath_work_name varchar(20) NOT NULL,
   fath_phone varchar(20) NOT NULL,
   fath_home_phone varchar(20),
   fath_hand_phone varchar(20),
   fath_email varchar(30),
   fath_note tinytext NOT NULL,
   moth_name varchar(20) NOT NULL,
   moth_birthyear varchar(4) NOT NULL,
   moth_alive tinyint(3) unsigned DEFAULT '1' NOT NULL,
   moth_relation varchar(6) NOT NULL,
   moth_country varchar(20) NOT NULL,
   moth_p_id varchar(20) NOT NULL,
   moth_abroad varchar(20),
   moth_education tinyint(3) unsigned DEFAULT '0' NOT NULL,
   moth_occupation varchar(20) NOT NULL,
   moth_unit varchar(20) NOT NULL,
   moth_work_name varchar(20) NOT NULL,
   moth_phone varchar(20) NOT NULL,
   moth_home_phone varchar(20),
   moth_hand_phone varchar(20),
   moth_email varchar(30),
   moth_note tinytext NOT NULL,
   is_same_gua char(1) DEFAULT '0' NOT NULL,
   guardian_name varchar(20) NOT NULL,
   guardian_phone varchar(20) NOT NULL,
   guardian_address varchar(60) NOT NULL,
   guardian_relation varchar(20) NOT NULL,
   guardian_p_id varchar(20),
   guardian_unit varchar(30),
   guardian_work_name varchar(20),
   guardian_hand_phone varchar(20),
   guardian_email varchar(30),
   grandfath_name varchar(20) NOT NULL,
   grandfath_birthyear date DEFAULT '0000-00-00' NOT NULL,
   grandfath_alive tinyint(3) unsigned DEFAULT '1' NOT NULL,
   grandmoth_name varchar(20) NOT NULL,
   grandmoth_birthyear date DEFAULT '0000-00-00' NOT NULL,
   grandmoth_alive tinyint(3) unsigned DEFAULT '1' NOT NULL,
   update_time timestamp(14),
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (addr_id)
);

    



# --------------------------------------------------------

#
# 數據表的結構 'stud_guid_case'
#

DROP TABLE IF EXISTS stud_guid_case;
CREATE TABLE stud_guid_case (
   guid_c_id varchar(10) NOT NULL,
   guid_c_from varchar(10),
   guid_c_bdate date DEFAULT '0000-00-00' NOT NULL,
   guid_c_teacher varchar(20),
   guid_c_kind varchar(20),
   guid_c_behave varchar(20),
   guid_c_reason varchar(20),
   guid_c_isover char(2) DEFAULT '否' NOT NULL,
   guid_c_over_reason varchar(20),
   guid_c_edate date,
   update_time datetime,
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (guid_c_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_guid_case_list'
#

DROP TABLE IF EXISTS stud_guid_case_list;
CREATE TABLE stud_guid_case_list (
   guid_l_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   guid_c_id varchar(10) NOT NULL,
   guid_l_date date DEFAULT '0000-00-00' NOT NULL,
   guid_l_con varchar(40) NOT NULL,
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (guid_l_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_guid_case_u'
#

DROP TABLE IF EXISTS stud_guid_case_u;
CREATE TABLE stud_guid_case_u (
   guid_u_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   guid_c_id varchar(10) NOT NULL,
   PRIMARY KEY (guid_u_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_guidance'
#

DROP TABLE IF EXISTS stud_guidance;
CREATE TABLE stud_guidance (
   seme_year_seme varchar(6) NOT NULL,   
   stud_id varchar(20) NOT NULL,
   guid_p_relation varchar(8) NOT NULL,
   guid_air varchar(6) NOT NULL,
   guid_edu_fath varchar(6) NOT NULL,
   guid_edu_moth varchar(6) NOT NULL,
   guid_live varchar(12) NOT NULL,
   guid_camer varchar(10) NOT NULL,
   guid_sub_like varchar(20),
   guid_sub_diff varchar(20),
   guid_spec varchar(20),
   guid_hobby varchar(20),
   guid_habit varchar(20),
   guid_relation varchar(20),
   guid_behave_o varchar(20),
   guid_behave_i varchar(20),
   guid_behave_edu varchar(20),
   guid_habit_bad varchar(20),
   guid_behave_agi varchar(20),
   guid_temp1 varchar(20),
   guid_temp2 varchar(20),
   update_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (seme_year_seme, stud_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_kinfolk'
#

DROP TABLE IF EXISTS stud_kinfolk;
CREATE TABLE stud_kinfolk (
   kin_id bigint(20) DEFAULT '0' NOT NULL auto_increment,
   stud_id varchar(20) NOT NULL,
   kin_name varchar(20),
   kin_calling varchar(6),
   kin_phone varchar(20),
   kin_hand_phone varchar(20),
   kin_email varchar(40),
   PRIMARY KEY (kin_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_move'
#

DROP TABLE IF EXISTS stud_move;
CREATE TABLE stud_move (
   move_id bigint(20) DEFAULT '0' NOT NULL auto_increment,
   stud_id varchar(20) NOT NULL,
   move_kind varchar(10) NOT NULL,
   move_year_seme varchar(6)  NOT NULL,   
   move_date date DEFAULT '0000-00-00' NOT NULL,
   move_c_unit varchar(30),
   move_c_date date DEFAULT '0000-00-00',
   move_c_word varchar(20),
   move_c_num varchar(14),
   update_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   update_id varchar(20) NOT NULL,
   update_ip varchar(15) NOT NULL,
   PRIMARY KEY (move_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_psy_tests'
#

DROP TABLE IF EXISTS stud_psy_tests;
CREATE TABLE stud_psy_tests (
   psy_id bigint(20) unsigned DEFAULT '0' NOT NULL,
   stud_id varchar(20) NOT NULL,
   psy_num_id varchar(4),
   psy_name_s varchar(20),
   psy_score_id varchar(4),
   psy_resource varchar(30) NOT NULL,
   psy_tran_id varchar(4) NOT NULL,
   psy_name varchar(40) NOT NULL,
   update_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (psy_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_score'
#

DROP TABLE IF EXISTS stud_score;
CREATE TABLE stud_score (
   seme_year_seme varchar(6) NOT NULL,   
   sub_id int(11) DEFAULT '0' NOT NULL,
   sub_attr_id tinyint(4) DEFAULT '0' NOT NULL,
   stud_id varchar(20) NOT NULL,
   sub_num tinyint(3) unsigned DEFAULT '0' NOT NULL,
   sub_percent float(10,2) DEFAULT '0.00' NOT NULL,
   sub_set5 char(2) NOT NULL,
   update_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (seme_year_seme, sub_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_seme'
#

DROP TABLE IF EXISTS stud_seme;
CREATE TABLE stud_seme (
   stud_id varchar(20) NOT NULL,
   seme_year_seme char(6) NOT NULL,   
   seme_class varchar(10) NOT NULL,
   seme_class_name varchar(10) NOT NULL,
   seme_num char(2) NOT NULL,
   seme_class_s varchar(10) NOT NULL,
   seme_num_s char(2) NOT NULL,
   score_total float(10,2) DEFAULT '0.00' NOT NULL,
   score_total_t float(10,2),
   comment varchar(24) NOT NULL,
   seme_cadre varchar(12),
   assist_total float(10,2) unsigned DEFAULT '0.00' NOT NULL,
   absen_thing float(10,2) unsigned,
   absen_sick float(10,2) unsigned,
   absen_none float(10,2) unsigned,
   PRIMARY KEY (stud_id, seme_year_seme)
);
# --------------------------------------------------------

#
# 數據表的結構 'stud_tea_parent'
#

DROP TABLE IF EXISTS stud_tea_parent;
CREATE TABLE stud_tea_parent (
   par_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   stud_id varchar(20) NOT NULL,
   par_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   par_for varchar(20) NOT NULL,
   par_subject varchar(20) NOT NULL,
   par_content varchar(50) NOT NULL,
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (par_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'teacher_base'
#

DROP TABLE IF EXISTS teacher_base;
CREATE TABLE teacher_base (
   teach_id varchar(20) NOT NULL,
   teach_person_id varchar(10) NOT NULL,
   name varchar(20) NOT NULL,
   sex tinyint(3) unsigned DEFAULT '0' NOT NULL,
   age tinyint(3) unsigned DEFAULT '0' NOT NULL,
   birthday date DEFAULT '0000-00-00' NOT NULL,
   birth_place tinyint(3) unsigned DEFAULT '0' NOT NULL,
   marriage tinyint(3) unsigned,
   address varchar(60),
   home_phone varchar(20),
   cell_phone varchar(20),
   office_home varchar(20),
   teach_condition tinyint(3) unsigned DEFAULT '0' NOT NULL,
   teach_memo varchar(30),
   login_pass varchar(12) NOT NULL,
   teach_edu_kind tinyint(3) unsigned DEFAULT '0' NOT NULL,
   teach_edu_abroad varchar(4) NOT NULL,
   teach_sub_kind varchar(10) NOT NULL,
   teach_check_kind tinyint(3) unsigned DEFAULT '0' NOT NULL,
   teach_check_word varchar(30) NOT NULL,
   teach_is_cripple char(2) NOT NULL,
   update_time timestamp(14),
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (teach_id)
);

# --------------------------------------------------------

#
# 數據表的結構 'teacher_connect'
#

DROP TABLE IF EXISTS teacher_connect;
CREATE TABLE teacher_connect (
   teach_id varchar(20) NOT NULL,
   email varchar(50),
   email2 varchar(50),
   email3 varchar(50),
   selfweb varchar(50),
   selfweb2 varchar(50),
   classweb varchar(50),
   classweb2 varchar(50),
   ICQ varchar(20),
   PRIMARY KEY (teach_id)
);
# --------------------------------------------------------

#
# 數據表的結構 'teacher_post'
#

DROP TABLE IF EXISTS teacher_post;
CREATE TABLE teacher_post (
   teach_id varchar(20) NOT NULL,
   post_kind tinyint(3) unsigned DEFAULT '0' NOT NULL,
   post_office tinyint(3) unsigned DEFAULT '0' NOT NULL,
   post_level tinyint(3) unsigned DEFAULT '0' NOT NULL,
   official_level tinyint(3) unsigned,
   post_class tinyint(3) unsigned DEFAULT '0' NOT NULL,
   post_num varchar(20),
   bywork_num varchar(20),   
   salay mediumint(9) DEFAULT '0' NOT NULL,
   appoint_date date DEFAULT '0000-00-00',
   arrive_date date DEFAULT '0000-00-00',
   approve_date date DEFAULT '0000-00-00',
   approve_number varchar(60),
   teach_title_id tinyint(3) unsigned DEFAULT '0' NOT NULL,
   class_num varchar(6) DEFAULT '0' NOT NULL,
   update_time timestamp(14),
   update_id varchar(20) NOT NULL,
   PRIMARY KEY (teach_id)
);

# --------------------------------------------------------

#
# 數據表的結構 'teacher_subject'
#

DROP TABLE IF EXISTS teacher_subject;
CREATE TABLE teacher_subject (
   subject_id tinyint(3) unsigned DEFAULT '0' NOT NULL,
   subject_name varchar(20) NOT NULL,
   subject_year tinyint(4) unsigned,
   PRIMARY KEY (subject_id)
);
# --------------------------------------------------------


#
# 數據表的結構 'teacher_title'
#
DROP TABLE IF EXISTS teacher_title;
CREATE TABLE teacher_title (
   teach_title_id tinyint(4) DEFAULT '0' NOT NULL,
   title_name varchar(20) NOT NULL,
   title_kind tinyint(4) DEFAULT '0' NOT NULL,
   title_short_name varchar(12),
   room_id tinyint(4) DEFAULT '0' NOT NULL,
   title_memo text NOT NULL,
   PRIMARY KEY (teach_title_id)
);


#
# 數據表的結構 'stud_sick_f'
#

DROP TABLE IF EXISTS stud_sick_f;
CREATE TABLE stud_sick_f (
   sick_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   stud_id varchar(20) NOT NULL,
   s_calling varchar(6) NOT NULL,
   sick varchar(100) NOT NULL,
   PRIMARY KEY (sick_id)
);


#
# 數據表的結構 'stud_sick_p'
#

DROP TABLE IF EXISTS stud_sick_p;
CREATE TABLE stud_sick_p (
   stud_id varchar(20) NOT NULL,
   sick varchar(100) NOT NULL,
   PRIMARY KEY (stud_id)
);


#
# 數據表的結構 'school_subject'
#

DROP TABLE IF EXISTS school_subject;
CREATE TABLE school_subject (
   sub_id int(10) unsigned DEFAULT '0' NOT NULL auto_increment,
   seme_year_seme varchar(6) NOT NULL,
   sub_name char(3) NOT NULL,
   sub_course char(1) NOT NULL,
   sub_year char(2) NOT NULL,
   is_exam char(1) NOT NULL,
   sub_num char(2) NOT NULL,
   sub_percent char(3) NOT NULL,
   update_id varchar(20) NOT NULL,
   update_time timestamp(14),
   PRIMARY KEY (sub_id)
);




# phpMyAdmin MySQL-Dump
# version 2.2.0
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# 主機: localhost
# 建立日期: October 1, 2001, 9:41 am
# 資料庫版本: 3.22.32
# PHP版本: 4.0.4pl1
# 資料庫: sfs2test
# --------------------------------------------------------

#
# Table structure for table sfs_text
#

DROP TABLE IF EXISTS sfs_text;
CREATE TABLE sfs_text (
   t_id int(11) DEFAULT '0' NOT NULL auto_increment,
   t_kind varchar(20) NOT NULL,
   g_id tinyint(4) DEFAULT '0' NOT NULL,
   d_id int(11) DEFAULT '0' NOT NULL,
   t_name varchar(50) NOT NULL,
   t_parent varchar(60) NOT NULL,
   p_id int(11) DEFAULT '0' NOT NULL,
   p_dot varchar(20) NOT NULL,
   PRIMARY KEY (t_id)
);

#
# Dumping data for table sfs_text
#

INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '1', 'addr', '1', '0', '住址', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '23', 'addr', '1', '6', '新美里', '1,3,5,', '5', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '22', 'addr', '1', '5', '文武里', '1,3,5,', '5', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '20', 'addr', '1', '4', '江南里', '1,3,5,', '5', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '17', 'addr', '1', '10', '廓子村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '18', 'addr', '1', '9', '土城村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '24', 'addr', '1', '3', '平安里', '1,3,5,', '5', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '12', 'addr', '1', '6', '永豐村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '16', 'addr', '1', '8', '鐵山村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '13', 'addr', '1', '5', '三崁村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '14', 'addr', '1', '7', '水美村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '15', 'addr', '1', '4', '馬鳴村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '21', 'addr', '1', '2', '義和里', '1,3,5,', '5', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '19', 'addr', '1', '1', '岷山里', '1,3,5,', '5', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '10', 'addr', '1', '3', '六分村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '11', 'addr', '1', '2', '中山村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '6', 'addr', '1', '0', '大同村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '5', 'addr', '1', '1', '大甲鎮', '1,3,', '3', '..');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '4', 'addr', '1', '0', '外埔鄉', '1,3,', '3', '..');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '3', 'addr', '1', '0', '台中縣', '1,', '1', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '26', 'addr', '1', '2', '后里鄉', '1,3,', '3', '..');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '25', 'addr', '1', '0', '中山里', '1,3,5,', '5', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '9', 'addr', '1', '1', '大東村', '1,3,4,', '4', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '27', 'addr', '1', '0', '太平村', '1,3,26,', '26', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '28', 'addr', '1', '1', '眉山村', '1,3,26,', '26', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '29', 'addr', '1', '2', '月眉村', '1,3,26,', '26', '...');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '30', 'stud_kind', '1', '0', '學生身份別', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '31', 'stud_spe_kind', '1', '0', '特殊班類別', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '33', 'preschool_status', '1', '0', '入學資格', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '32', 'spe_class_kind', '1', '0', '特殊班班別', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '34', 'post_kind', '2', '0', '職別', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '35', 'preschool_status', '1', '0', '本學區', '33,', '33', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '36', 'preschool_status', '1', '1', '大學區', '33,', '33', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '37', 'preschool_status', '1', '2', '隨父就讀', '33,', '33', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '38', 'preschool_status', '1', '3', '隨母就讀', '33,', '33', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '39', 'post_kind', '2', '1', '校長', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '40', 'post_kind', '2', '2', '教師兼主任', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '41', 'post_kind', '2', '3', '主任', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '42', 'post_kind', '2', '4', '教師兼組長', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '43', 'post_kind', '2', '5', '組長', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '44', 'post_kind', '2', '6', '導師', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '45', 'post_kind', '2', '7', '專任教師', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '46', 'post_kind', '2', '8', '實習教師', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '47', 'post_kind', '2', '9', '試用教師', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '48', 'post_kind', '2', '10', '代理/代課教師', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '49', 'post_kind', '2', '11', '兼任教師', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '50', 'post_kind', '2', '12', '職員', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '51', 'post_kind', '2', '13', '護士', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '52', 'post_kind', '2', '14', '警衛', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '53', 'post_kind', '2', '15', '工友', '34,', '34', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '54', 'stud_kind', '1', '0', '一般學生', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '55', 'stud_kind', '1', '1', '本人殘障', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '56', 'stud_kind', '1', '2', '家長殘障', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '57', 'stud_kind', '1', '3', '低收入戶', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '58', 'stud_kind', '1', '4', '大陸來台依親者', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '59', 'stud_kind', '1', '5', '功\勳子女', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '60', 'stud_kind', '1', '6', '海外僑生', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '61', 'stud_kind', '1', '7', '港澳生', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '62', 'stud_kind', '1', '8', '邊疆生', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '63', 'stud_kind', '1', '9', '原住民', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '64', 'stud_kind', '1', '10', '外籍生', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '65', 'stud_kind', '1', '11', '資優生', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '66', 'stud_kind', '1', '12', '派外人員子女', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '67', 'stud_kind', '1', '13', '體育績優', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '68', 'stud_kind', '1', '14', '顏面傷殘', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '69', 'stud_kind', '1', '15', '教職員子女', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '70', 'stud_kind', '1', '16', '公教遺族(因公)', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '71', 'stud_kind', '1', '17', '公教遺族(因病)', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '72', 'stud_kind', '1', '18', '身心障礙(檢定)', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '73', 'stud_kind', '1', '19', '其他', '30,', '30', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '74', 'stud_spe_kind', '1', '1', '障礙類', '31,', '31', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '75', 'stud_spe_kind', '1', '2', '資優類', '31,', '31', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '76', 'stud_spe_kind', '1', '3', '資源班', '31,', '31', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '77', 'spe_class_kind', '1', '1', '啟智', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '78', 'spe_class_kind', '1', '2', '啟明', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '79', 'spe_class_kind', '1', '3', '啟聰', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '80', 'spe_class_kind', '1', '4', '巡迴輔導', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '81', 'spe_class_kind', '1', '5', '啟學', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '82', 'spe_class_kind', '1', '6', '啟聲', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '83', 'spe_class_kind', '1', '7', '啟健', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '84', 'spe_class_kind', '1', '8', '啟迪', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '85', 'spe_class_kind', '1', '9', '啟仁(肢障)', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '86', 'spe_class_kind', '1', '10', '語障', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '87', 'spe_class_kind', '1', '11', '身心障礙', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '88', 'spe_class_kind', '1', '12', '學習困難', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '89', 'spe_class_kind', '1', '13', '在家教育', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '90', 'spe_class_kind', '1', '14', '多重障礙', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '91', 'spe_class_kind', '1', '15', '一般智賦優異', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '92', 'spe_class_kind', '1', '16', '音樂', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '93', 'spe_class_kind', '1', '17', '美術', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '94', 'spe_class_kind', '1', '18', '舞蹈', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '95', 'spe_class_kind', '1', '19', '體育', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '96', 'spe_class_kind', '1', '20', '其他', '32,', '32', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '97', 'tea_edu_kind', '2', '0', '教師學歷別', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '98', 'tea_edu_kind', '2', '1', '研究所畢業(博士)', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '99', 'tea_edu_kind', '2', '2', '研究所畢業(碩士)', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '100', 'tea_edu_kind', '2', '3', '研究所四十學分班結業', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '101', 'tea_edu_kind', '2', '4', '師大及教育學院畢業', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '102', 'tea_edu_kind', '2', '5', '大學院校一般科系畢業(有修習教育學分)', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '103', 'tea_edu_kind', '2', '6', '大學院校一般科系畢業(無修習教育學分)', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '104', 'tea_edu_kind', '2', '7', '師範專科畢業', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '105', 'tea_edu_kind', '2', '8', '其他專科畢業', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '106', 'tea_edu_kind', '2', '9', '師範學校畢業', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '107', 'tea_edu_kind', '2', '10', '軍事學校畢業', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '108', 'tea_edu_kind', '2', '11', '其他', '97,', '97', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '109', 'tea_check_kind', '2', '0', '教師檢定資格', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '110', 'tea_check_kind', '2', '1', '本科或相關科檢定合格', '109,', '109', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '111', 'tea_check_kind', '2', '2', '實習教師', '109,', '109', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '112', 'tea_check_kind', '2', '3', '試用教師登記', '109,', '109', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '113', 'tea_check_kind', '2', '4', '登計中', '109,', '109', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '114', 'tea_check_kind', '2', '5', '其他', '109,', '109', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '115', 'edu_kind', '1', '0', '家長學歷別', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '116', 'edu_kind', '1', '1', '博士', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '117', 'edu_kind', '1', '2', '碩士', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '118', 'edu_kind', '1', '3', '大學', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '119', 'edu_kind', '1', '4', '專科', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '120', 'edu_kind', '1', '5', '高中', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '121', 'edu_kind', '1', '6', '國中', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '122', 'edu_kind', '1', '7', '國小畢業', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '123', 'edu_kind', '1', '8', '國小肄業', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '124', 'edu_kind', '1', '9', '識字(未就學)', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '125', 'edu_kind', '1', '10', '不識字', '115,', '115', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '126', 'official_level', '2', '0', '官等', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '127', 'official_level', '2', '1', '簡任', '126,', '126', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '128', 'official_level', '2', '2', '薦任', '126,', '126', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '129', 'official_level', '2', '3', '委任', '126,', '126', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '130', 'remove', '2', '0', '教職員在職狀況', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '131', 'remove', '2', '0', '在職', '130,', '130', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '132', 'remove', '2', '1', '調出', '130,', '130', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '133', 'remove', '2', '2', '退休', '130,', '130', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '134', 'remove', '2', '3', '代課期滿', '130,', '130', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '135', 'remove', '2', '4', '資遣', '130,', '130', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '136', 'per_sick_kind', '1', '0', '個人病史', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '137', 'per_sick_kind', '1', '1', '心臟病', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '138', 'per_sick_kind', '1', '2', 'B型肝炎帶原', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '139', 'per_sick_kind', '1', '3', '腮腺炎', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '140', 'per_sick_kind', '1', '4', '癲癇', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '141', 'per_sick_kind', '1', '5', '肺炎', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '142', 'per_sick_kind', '1', '6', '水痘', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '143', 'per_sick_kind', '1', '7', '氣喘', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '144', 'per_sick_kind', '1', '8', '腎臟病', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '145', 'per_sick_kind', '1', '9', '血友病', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '146', 'per_sick_kind', '1', '10', '肺結核', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '147', 'per_sick_kind', '1', '11', '疝氣', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '148', 'per_sick_kind', '1', '12', '特異體質', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '149', 'per_sick_kind', '1', '13', '腦炎', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '150', 'per_sick_kind', '1', '14', '重傷', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '151', 'per_sick_kind', '1', '15', '食品藥物過敏', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '152', 'per_sick_kind', '1', '16', '風濕熱', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '153', 'per_sick_kind', '1', '17', '德國麻疹', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '154', 'per_sick_kind', '1', '18', '小兒麻痺', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '155', 'per_sick_kind', '1', '19', '傷寒', '136,', '136', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '156', 'fam_sick_kind', '1', '0', '家族病史', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '157', 'fam_sick_kind', '1', '1', '高血壓', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '158', 'fam_sick_kind', '1', '2', '糖尿病', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '159', 'fam_sick_kind', '1', '3', 'B型肝炎帶原', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '160', 'fam_sick_kind', '1', '4', '癲癇', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '161', 'fam_sick_kind', '1', '5', '精神疾病', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '162', 'fam_sick_kind', '1', '6', '肺結核', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '163', 'fam_sick_kind', '1', '7', '過敏性疾病', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '164', 'fam_sick_kind', '1', '8', '心臟血管疾病', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '165', 'fam_sick_kind', '1', '9', '內分泌疾病', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '166', 'fam_sick_kind', '1', '10', '腫瘤', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '167', 'fam_sick_kind', '1', '11', '其他', '156,', '156', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '168', 'course9', '3', '0', '學習領域', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '169', 'course9', '3', '1', '語文', '168,', '168', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '170', 'course9', '3', '2', '健康與體育', '168,', '168', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '171', 'course9', '3', '3', '社會', '168,', '168', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '172', 'course9', '3', '4', '藝術與人文', '168,', '168', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '173', 'course9', '3', '5', '自然與科技', '168,', '168', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '174', 'course9', '3', '6', '數學', '168,', '168', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '175', 'course9', '3', '7', '綜合活動', '168,', '168', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '176', 'subject_kind', '3', '0', '科目名稱', '', '0', '');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '177', 'subject_kind', '3', '1', '國語', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '178', 'subject_kind', '3', '2', '數學', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '179', 'subject_kind', '3', '3', '社會', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '180', 'subject_kind', '3', '4', '自然', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '181', 'subject_kind', '3', '5', '道德與健康', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '182', 'subject_kind', '3', '6', '生活與倫理', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '183', 'subject_kind', '3', '7', '體育', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '184', 'subject_kind', '3', '8', '書法', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '185', 'subject_kind', '3', '9', '美勞', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '186', 'subject_kind', '3', '10', '音樂', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '187', 'subject_kind', '3', '11', '美語', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '188', 'subject_kind', '3', '12', '電腦', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '189', 'subject_kind', '3', '13', '鄉土教學', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '190', 'subject_kind', '3', '14', '生活教育', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '191', 'subject_kind', '3', '15', '休閒教育', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '192', 'subject_kind', '3', '16', '社會適應', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '193', 'subject_kind', '3', '17', '實用數學', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '194', 'subject_kind', '3', '18', '實用英文', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '195', 'subject_kind', '3', '19', '彈性應用', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '196', 'subject_kind', '3', '20', '輔導活動', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '197', 'subject_kind', '3', '21', '社團活動', '176,', '176', '.');
INSERT INTO sfs_text (t_id, t_kind, g_id, d_id, t_name, t_parent, p_id, p_dot) VALUES ( '198', 'subject_kind', '3', '22', '職業生活', '176,', '176', '.');   
