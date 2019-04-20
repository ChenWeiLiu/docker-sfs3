

CREATE TABLE stud_guid (
  guid_c_id int(10) unsigned NOT NULL auto_increment,
  st_sn varchar(10) NOT NULL default '',
  guid_c_from varchar(30) NOT NULL default '',
  begin_date date NOT NULL default '0000-00-00',
  guid_tea_sn varchar(20) NOT NULL default '',
  guid_c_kind varchar(20) NOT NULL default '',
  guid_c_behave varchar(20) NOT NULL default '',
  about_man varchar(20) NOT NULL default '',
  relation varchar(20) NOT NULL default '',
  ab_man_addr varchar(50) NOT NULL default '',
  ab_man_tel varchar(20) NOT NULL default '',
  guid_c_isover tinyint(3) NOT NULL default '0',
  update_id varchar(20) NOT NULL default '',
  end_date date NOT NULL default '0000-00-00',
  update_time datetime NOT NULL default '0000-00-00 00:00:00',
  guid_begin_why text NOT NULL,
  guid_c_reason text NOT NULL,
  about_home text NOT NULL,
  about_school text NOT NULL,
  person_specific text NOT NULL,
  about_oth text NOT NULL,
  st_analysis text NOT NULL,
  guidance_method text NOT NULL,
  st_oth_record text NOT NULL,
  guid_over_reason text NOT NULL,
  PRIMARY KEY  (guid_c_id)
) ENGINE=MyISAM;
        

CREATE TABLE stud_guid_event (
  guid_l_id bigint(20) unsigned NOT NULL auto_increment,
  guid_c_id int(10) NOT NULL default '0',
  guid_l_date datetime NOT NULL default '0000-00-00 00:00:00',
  guid_kind varchar(20) NOT NULL default '',
  tutor varchar(20) NOT NULL default '',
  guid_l_con text NOT NULL,
  update_id varchar(20) NOT NULL default '',
  PRIMARY KEY  (guid_l_id)
) ENGINE=MyISAM;
        
