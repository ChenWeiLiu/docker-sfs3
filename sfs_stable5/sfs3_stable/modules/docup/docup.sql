CREATE TABLE docup (
   docup_p_id tinyint(4) DEFAULT '0' NOT NULL,
   docup_id int(11) DEFAULT '0' NOT NULL auto_increment,
   docup_name varchar(80) NOT NULL,
   docup_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   docup_owner varchar(12) NOT NULL,
   docup_store varchar(80) NOT NULL,
   docup_share char(3) DEFAULT '0' NOT NULL,
   docup_owerid varchar(6) NOT NULL,
   docup_file_size int(11) DEFAULT '0' NOT NULL,
   PRIMARY KEY (docup_id)
);

CREATE TABLE docup_p (
   doc_kind_id tinyint(4) DEFAULT '0' NOT NULL,
   docup_p_id tinyint(4) DEFAULT '0' NOT NULL auto_increment,
   docup_p_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   docup_p_name varchar(60),
   docup_p_memo text,
   docup_p_owner varchar(12),
   docup_p_ownerid varchar(6) NOT NULL,
   docup_p_count int(11) DEFAULT '0' NOT NULL,
   PRIMARY KEY (docup_p_id)
);

