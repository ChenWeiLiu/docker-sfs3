
CREATE TABLE sch_doc1 (
   doc1_id varchar(10) NOT NULL,
   doc1_year_limit tinyint(3) unsigned DEFAULT '0' NOT NULL,
   doc1_kind tinyint(4) DEFAULT '0' NOT NULL,
   doc1_date date DEFAULT '0000-00-00' NOT NULL,
   doc1_date_sign datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   doc1_unit varchar(60) NOT NULL,
   doc1_word varchar(60) NOT NULL,
   doc1_main tinytext NOT NULL,
   doc1_unit_num1 tinyint(4) DEFAULT '0' NOT NULL,
   doc1_unit_num2 varchar(6) NOT NULL,
   teach_id varchar(20) NOT NULL,
   doc1_k_id tinyint(1) DEFAULT '0' NOT NULL,
   doc_stat char(1) DEFAULT '1' NOT NULL,
   doc1_end_date date DEFAULT '0000-00-00' NOT NULL,
   doc1_infile_date date DEFAULT '0000-00-00' NOT NULL,
   do_teacher varchar(20) NOT NULL,
   PRIMARY KEY (doc1_id)
);


#
# Table structure for table 'sch_doc1_unit'
# ©Ó¿ì³æ¦ì 

CREATE TABLE sch_doc1_unit (
   doc1_unit_num1 tinyint(4) NOT NULL auto_increment,
   doc1_unit_name varchar(20) NOT NULL,
   PRIMARY KEY (doc1_unit_num1)
);
