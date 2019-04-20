#
# 資料表格式： `grad_stud`
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
# 資料表格式： `school_day`
#

CREATE TABLE school_day (
  day_kind varchar(40) NOT NULL default '',
  day date NOT NULL default '0000-00-00',
  year tinyint(2) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
  UNIQUE KEY year_seme (day_kind,year,seme)
) TYPE=MyISAM;

#
# 列出以下資料庫的數據： `school_day`
#

#
# 資料表格式： `sfs_module`
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
  kind enum('模組','分類') NOT NULL default '模組',
  txt varchar(255) NOT NULL default '',
  PRIMARY KEY  (msn),
  KEY sort (sort,of_group)
) TYPE=MyISAM;

#
# 列出以下資料庫的數據： `sfs_module`
#

INSERT INTO sfs_module VALUES (1, '系統管理', '', 7, 0, 1, 0, '', 'administrator_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (2, '校務行政', '', 1, 1, 1, 0, '', 'school_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (8, '文件資料庫', 'docup', 4, 1, 1, 2, '2.0.1', '', 'hami', '2002-12-15', '模組', '');
INSERT INTO sfs_module VALUES (9, '午餐\食譜公告', 'lunch', 3, 1, 1, 2, '1.0.8', '', 'prolin', '2000-09-17', '模組', '');
INSERT INTO sfs_module VALUES (7, '圖書管理系統', 'book', 2, 1, 1, 2, '2.0.1', '', 'hami', '2002-12-15', '模組', '');
INSERT INTO sfs_module VALUES (41, '南縣教師管理', 'tnc_teach_class', 6, 0, 1, 12, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (11, '校園行事曆', 'school_calendar', 1, 1, 1, 2, '1.0', '', 'tad', '2003-03-24', '模組', '');
INSERT INTO sfs_module VALUES (12, '教務', '', 2, 0, 1, 0, '', 'school_affairs_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (13, '訓導', '', 3, 0, 1, 0, '', 'student_counsellor_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (14, '輔導', '', 4, 0, 0, 0, '', 'advisory_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (15, '教職員', '', 6, 0, 1, 0, '', 'school_teacher_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (16, '教學組', '', 3, 0, 1, 12, '', 'student_edu_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (17, '註冊組', '', 4, 0, 1, 12, '', 'student_reg_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (18, '系統備份', 'backup', 2, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (19, '指定新網管', 'chang_root', 8, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (20, '資料庫欄位管理', 'database_info', 6, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (21, '學校設定', 'school_setup', 1, 0, 1, 12, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (23, '成績單介面管理', 'score_input_interface', 11, 0, 0, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (24, '模組權限管理', 'sfs_man2', 1, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (26, '系統選項清單設定', 'sfs_text', 7, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (60, '缺曠課獎懲管理', 'absent', 1, 0, 1, 13, '1.0', '', '', '2003-04-18', '模組', '');
INSERT INTO sfs_module VALUES (28, '線上調查系統', 'online_form', 13, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (29, '學期初設定', 'every_year_setup', 2, 0, 1, 12, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (30, '行事曆', 'calendar', 16, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (31, '班級學籍管理', 'stud_class', 1, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (32, '專科教室預約', 'new_compclass', 15, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (33, '成績管理', 'score_input', 12, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (34, '教師管理', 'teach_class', 5, 0, 1, 12, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (35, '行政密碼查詢', 'teacher_pass', 9, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (36, '公告管理', 'new_board', 14, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (37, '製作成績單', 'academic_record', 11, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (40, '學校課表匯出系統', 'course_paper', 1, 0, 1, 16, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (42, '成績查詢', 'score_list', 2, 0, 1, 16, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (43, '成績管理', 'score_manage', 3, 0, 1, 16, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (44, '學籍管理', 'stud_reg', 1, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (45, '學生異動', 'stud_move', 2, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (46, '學生資料查詢統計', 'stud_query', 3, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (47, '匯入資料', 'create_data', 4, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (48, '編班作業', 'stud_year', 5, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (49, '學籍報表', 'stud_report', 6, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (50, '成績輸入', 'score_input_all', 7, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (51, 'S形自動編班', 'stud_compile', 8, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (52, '新生編班', 'temp_compile', 9, 0, 1, 17, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (53, '畢業生作業', 'graduate', 10, 0, 1, 17, '', '', 'JRH', '2003-03-25', '模組', '');
INSERT INTO sfs_module VALUES (54, '更改密碼', 'chpass', 21, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (55, '級務管理', 'class_things', 2, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (56, '數位相本', 'mig', 5, 1, 1, 2, '2.0.1', '', 'hami', '2003-03-19', '模組', '');
INSERT INTO sfs_module VALUES (57, '教師通訊錄', 'teach_report_more', 22, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (58, '個人資料', 'teacher_self', 20, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (59, '學校課表查詢系統', 'new_course', 6, 1, 1, 2, '1.0', '', '', '2003-01-01', '模組', '');
INSERT INTO sfs_module VALUES (61, '校務佈告欄', 'board', 7, 1, 1, 2, '', '', 'hami', '2003-04-06', '模組', '');
INSERT INTO sfs_module VALUES (62, '校務佈告欄管理程式', 'board_man', 12, 0, 1, 1, '', '', 'hami', '2003-04-06', '模組', '');
# --------------------------------------------------------

#
# 資料表格式： `stud_absence`
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
# 列出以下資料庫的數據： `stud_absence`
#

# --------------------------------------------------------

#
# 資料表格式： `stud_absent`
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
# 列出以下資料庫的數據： `stud_absent`
#
#
# 資料表格式： `stud_addr_zip`
#

CREATE TABLE stud_addr_zip (
  zip char(3) NOT NULL default '',
  country varchar(10) NOT NULL default '',
  town varchar(10) NOT NULL default '',
  area_num varchar(6) NOT NULL default '',
  PRIMARY KEY  (zip)
) TYPE=MyISAM COMMENT='郵遞區號';

#
# 列出以下資料庫的數據： `stud_addr_zip`
#

INSERT INTO stud_addr_zip VALUES ('100', '台北市', '中正區', '02');
INSERT INTO stud_addr_zip VALUES ('103', '台北市', '大同區', '02');
INSERT INTO stud_addr_zip VALUES ('104', '台北市', '中山區', '02');
INSERT INTO stud_addr_zip VALUES ('105', '台北市', '松山區', '02');
INSERT INTO stud_addr_zip VALUES ('106', '台北市', '大安區', '02');
INSERT INTO stud_addr_zip VALUES ('108', '台北市', '萬華區', '02');
INSERT INTO stud_addr_zip VALUES ('110', '台北市', '信義區', '02');
INSERT INTO stud_addr_zip VALUES ('111', '台北市', '士林區', '02');
INSERT INTO stud_addr_zip VALUES ('112', '台北市', '北投區', '02');
INSERT INTO stud_addr_zip VALUES ('114', '台北市', '內湖區', '02');
INSERT INTO stud_addr_zip VALUES ('115', '台北市', '南港區', '02');
INSERT INTO stud_addr_zip VALUES ('116', '台北市', '文山區', '02');
INSERT INTO stud_addr_zip VALUES ('117', '台北市', '景美區', '02');
INSERT INTO stud_addr_zip VALUES ('200', '基隆市', '仁愛區', '02');
INSERT INTO stud_addr_zip VALUES ('201', '基隆市', '信義區', '02');
INSERT INTO stud_addr_zip VALUES ('202', '基隆市', '中正區', '02');
INSERT INTO stud_addr_zip VALUES ('203', '基隆市', '中山區', '02');
INSERT INTO stud_addr_zip VALUES ('204', '基隆市', '安樂區', '02');
INSERT INTO stud_addr_zip VALUES ('205', '基隆市', '暖暖區', '02');
INSERT INTO stud_addr_zip VALUES ('206', '基隆市', '七堵區', '02');
INSERT INTO stud_addr_zip VALUES ('207', '台北縣', '萬里鄉', '02');
INSERT INTO stud_addr_zip VALUES ('208', '台北縣', '金山鄉', '02');
INSERT INTO stud_addr_zip VALUES ('209', '連江縣', '馬祖南', '0836');
INSERT INTO stud_addr_zip VALUES ('210', '連江縣', '馬祖北', '0837');
INSERT INTO stud_addr_zip VALUES ('211', '連江縣', '馬祖莒', '0838');
INSERT INTO stud_addr_zip VALUES ('212', '連江縣', '馬祖東', '0839');
INSERT INTO stud_addr_zip VALUES ('220', '台北縣', '板橋市', '02');
INSERT INTO stud_addr_zip VALUES ('221', '台北縣', '汐止市', '02');
INSERT INTO stud_addr_zip VALUES ('222', '台北縣', '深坑鄉', '02');
INSERT INTO stud_addr_zip VALUES ('223', '台北縣', '石碇鄉', '02');
INSERT INTO stud_addr_zip VALUES ('224', '台北縣', '瑞芳鎮', '02');
INSERT INTO stud_addr_zip VALUES ('226', '台北縣', '平溪鄉', '02');
INSERT INTO stud_addr_zip VALUES ('227', '台北縣', '雙溪鄉', '02');
INSERT INTO stud_addr_zip VALUES ('228', '台北縣', '貢寮鄉', '02');
INSERT INTO stud_addr_zip VALUES ('231', '台北縣', '新店市', '02');
INSERT INTO stud_addr_zip VALUES ('232', '台北縣', '坪林鄉', '02');
INSERT INTO stud_addr_zip VALUES ('233', '台北縣', '烏來鄉', '02');
INSERT INTO stud_addr_zip VALUES ('234', '台北縣', '永和市', '02');
INSERT INTO stud_addr_zip VALUES ('235', '台北縣', '中和市', '02');
INSERT INTO stud_addr_zip VALUES ('236', '台北縣', '土城市', '02');
INSERT INTO stud_addr_zip VALUES ('237', '台北縣', '三峽鎮', '02');
INSERT INTO stud_addr_zip VALUES ('238', '台北縣', '樹林市', '02');
INSERT INTO stud_addr_zip VALUES ('239', '台北縣', '鶯歌鎮', '02');
INSERT INTO stud_addr_zip VALUES ('241', '台北縣', '三重市', '02');
INSERT INTO stud_addr_zip VALUES ('242', '台北縣', '新莊市', '02');
INSERT INTO stud_addr_zip VALUES ('243', '台北縣', '泰山鄉', '02');
INSERT INTO stud_addr_zip VALUES ('244', '台北縣', '林口鄉', '02');
INSERT INTO stud_addr_zip VALUES ('247', '台北縣', '蘆洲市', '02');
INSERT INTO stud_addr_zip VALUES ('248', '台北縣', '五股鄉', '02');
INSERT INTO stud_addr_zip VALUES ('249', '台北縣', '八里鄉', '02');
INSERT INTO stud_addr_zip VALUES ('251', '台北縣', '淡水鎮', '02');
INSERT INTO stud_addr_zip VALUES ('252', '台北縣', '三芝鄉', '02');
INSERT INTO stud_addr_zip VALUES ('253', '台北縣', '石門鄉', '02');
INSERT INTO stud_addr_zip VALUES ('260', '宜蘭縣', '宜蘭市', '039');
INSERT INTO stud_addr_zip VALUES ('261', '宜蘭縣', '頭城鎮', '039');
INSERT INTO stud_addr_zip VALUES ('262', '宜蘭縣', '礁溪鄉', '039');
INSERT INTO stud_addr_zip VALUES ('263', '宜蘭縣', '壯圍鄉', '039');
INSERT INTO stud_addr_zip VALUES ('264', '宜蘭縣', '員山鄉', '039');
INSERT INTO stud_addr_zip VALUES ('265', '宜蘭縣', '羅東鎮', '039');
INSERT INTO stud_addr_zip VALUES ('266', '宜蘭縣', '三星鄉', '039');
INSERT INTO stud_addr_zip VALUES ('267', '宜蘭縣', '大同鄉', '039');
INSERT INTO stud_addr_zip VALUES ('268', '宜蘭縣', '五結鄉', '039');
INSERT INTO stud_addr_zip VALUES ('269', '宜蘭縣', '冬山鄉', '039');
INSERT INTO stud_addr_zip VALUES ('270', '宜蘭縣', '蘇澳鎮', '039');
INSERT INTO stud_addr_zip VALUES ('272', '宜蘭縣', '南澳鄉', '039');
INSERT INTO stud_addr_zip VALUES ('300', '新竹市', '新竹市', '035');
INSERT INTO stud_addr_zip VALUES ('302', '新竹縣', '竹北市', '035');
INSERT INTO stud_addr_zip VALUES ('303', '新竹縣', '湖口鄉', '035');
INSERT INTO stud_addr_zip VALUES ('304', '新竹縣', '新豐鄉', '035');
INSERT INTO stud_addr_zip VALUES ('305', '新竹縣', '新埔鎮', '035');
INSERT INTO stud_addr_zip VALUES ('306', '新竹縣', '關西鎮', '035');
INSERT INTO stud_addr_zip VALUES ('307', '新竹縣', '芎林鄉', '035');
INSERT INTO stud_addr_zip VALUES ('308', '新竹縣', '寶山鄉', '035');
INSERT INTO stud_addr_zip VALUES ('310', '新竹縣', '竹東鎮', '035');
INSERT INTO stud_addr_zip VALUES ('311', '新竹縣', '五峰鄉', '035');
INSERT INTO stud_addr_zip VALUES ('312', '新竹縣', '橫山鄉', '035');
INSERT INTO stud_addr_zip VALUES ('313', '新竹縣', '尖石鄉', '035');
INSERT INTO stud_addr_zip VALUES ('314', '新竹縣', '北埔鄉', '035');
INSERT INTO stud_addr_zip VALUES ('315', '新竹縣', '峨眉鄉', '035');
INSERT INTO stud_addr_zip VALUES ('320', '桃園縣', '中壢市', '03');
INSERT INTO stud_addr_zip VALUES ('324', '桃園縣', '平鎮市', '03');
INSERT INTO stud_addr_zip VALUES ('325', '桃園縣', '龍潭鄉', '03');
INSERT INTO stud_addr_zip VALUES ('326', '桃園縣', '楊梅鎮', '03');
INSERT INTO stud_addr_zip VALUES ('327', '桃園縣', '新屋鄉', '03');
INSERT INTO stud_addr_zip VALUES ('328', '桃園縣', '觀音鄉', '03');
INSERT INTO stud_addr_zip VALUES ('330', '桃園縣', '桃園市', '03');
INSERT INTO stud_addr_zip VALUES ('333', '桃園縣', '龜山鄉', '03');
INSERT INTO stud_addr_zip VALUES ('334', '桃園縣', '八德市', '03');
INSERT INTO stud_addr_zip VALUES ('335', '桃園縣', '大溪鎮', '03');
INSERT INTO stud_addr_zip VALUES ('336', '桃園縣', '復興鄉', '03');
INSERT INTO stud_addr_zip VALUES ('337', '桃園縣', '大園鄉', '03');
INSERT INTO stud_addr_zip VALUES ('338', '桃園縣', '蘆竹鄉', '03');
INSERT INTO stud_addr_zip VALUES ('350', '苗栗縣', '竹南鎮', '037');
INSERT INTO stud_addr_zip VALUES ('351', '苗栗縣', '頭份鎮', '037');
INSERT INTO stud_addr_zip VALUES ('352', '苗栗縣', '三灣鄉', '037');
INSERT INTO stud_addr_zip VALUES ('353', '苗栗縣', '南庄鄉', '037');
INSERT INTO stud_addr_zip VALUES ('354', '苗栗縣', '獅潭鄉', '037');
INSERT INTO stud_addr_zip VALUES ('356', '苗栗縣', '後龍鎮', '037');
INSERT INTO stud_addr_zip VALUES ('357', '苗栗縣', '通霄鎮', '037');
INSERT INTO stud_addr_zip VALUES ('358', '苗栗縣', '苑裡鎮', '037');
INSERT INTO stud_addr_zip VALUES ('360', '苗栗縣', '苗栗市', '037');
INSERT INTO stud_addr_zip VALUES ('361', '苗栗縣', '造橋鄉', '037');
INSERT INTO stud_addr_zip VALUES ('362', '苗栗縣', '頭屋鄉', '037');
INSERT INTO stud_addr_zip VALUES ('363', '苗栗縣', '公館鄉', '037');
INSERT INTO stud_addr_zip VALUES ('364', '苗栗縣', '大湖鄉', '037');
INSERT INTO stud_addr_zip VALUES ('365', '苗栗縣', '泰安鄉', '037');
INSERT INTO stud_addr_zip VALUES ('366', '苗栗縣', '銅鑼鄉', '037');
INSERT INTO stud_addr_zip VALUES ('367', '苗栗縣', '三義鄉', '037');
INSERT INTO stud_addr_zip VALUES ('368', '苗栗縣', '西湖鄉', '037');
INSERT INTO stud_addr_zip VALUES ('369', '苗栗縣', '卓蘭鎮', '04');
INSERT INTO stud_addr_zip VALUES ('400', '台中市', '中區', '04');
INSERT INTO stud_addr_zip VALUES ('401', '台中市', '東區', '04');
INSERT INTO stud_addr_zip VALUES ('402', '台中市', '南區', '04');
INSERT INTO stud_addr_zip VALUES ('403', '台中市', '西區', '04');
INSERT INTO stud_addr_zip VALUES ('404', '台中市', '北區', '04');
INSERT INTO stud_addr_zip VALUES ('406', '台中市', '北屯', '04');
INSERT INTO stud_addr_zip VALUES ('407', '台中市', '西屯', '04');
INSERT INTO stud_addr_zip VALUES ('408', '台中市', '南屯', '04');
INSERT INTO stud_addr_zip VALUES ('411', '台中縣', '太平市', '04');
INSERT INTO stud_addr_zip VALUES ('412', '台中縣', '大里市', '04');
INSERT INTO stud_addr_zip VALUES ('413', '台中縣', '霧峰鄉', '04');
INSERT INTO stud_addr_zip VALUES ('414', '台中縣', '烏日鄉', '04');
INSERT INTO stud_addr_zip VALUES ('420', '台中縣', '豐原市', '04');
INSERT INTO stud_addr_zip VALUES ('421', '台中縣', '后里鄉', '04');
INSERT INTO stud_addr_zip VALUES ('422', '台中縣', '石岡鄉', '04');
INSERT INTO stud_addr_zip VALUES ('423', '台中縣', '東勢鎮', '04');
INSERT INTO stud_addr_zip VALUES ('424', '台中縣', '和平鄉', '04');
INSERT INTO stud_addr_zip VALUES ('426', '台中縣', '新社鄉', '04');
INSERT INTO stud_addr_zip VALUES ('427', '台中縣', '潭子鄉', '04');
INSERT INTO stud_addr_zip VALUES ('428', '台中縣', '大雅鄉', '04');
INSERT INTO stud_addr_zip VALUES ('429', '台中縣', '神岡鄉', '04');
INSERT INTO stud_addr_zip VALUES ('432', '台中縣', '大肚鄉', '04');
INSERT INTO stud_addr_zip VALUES ('433', '台中縣', '沙鹿鎮', '04');
INSERT INTO stud_addr_zip VALUES ('434', '台中縣', '龍井鄉', '04');
INSERT INTO stud_addr_zip VALUES ('435', '台中縣', '梧棲鎮', '04');
INSERT INTO stud_addr_zip VALUES ('436', '台中縣', '清水鎮', '04');
INSERT INTO stud_addr_zip VALUES ('437', '台中縣', '大甲鎮', '04');
INSERT INTO stud_addr_zip VALUES ('438', '台中縣', '外埔鄉', '04');
INSERT INTO stud_addr_zip VALUES ('439', '台中縣', '大安鄉', '04');
INSERT INTO stud_addr_zip VALUES ('500', '彰化縣', '彰化市', '04');
INSERT INTO stud_addr_zip VALUES ('502', '彰化縣', '芬園鄉', '04');
INSERT INTO stud_addr_zip VALUES ('503', '彰化縣', '花壇鄉', '04');
INSERT INTO stud_addr_zip VALUES ('504', '彰化縣', '秀水鄉', '04');
INSERT INTO stud_addr_zip VALUES ('505', '彰化縣', '鹿港鎮', '04');
INSERT INTO stud_addr_zip VALUES ('506', '彰化縣', '福興鄉', '04');
INSERT INTO stud_addr_zip VALUES ('507', '彰化縣', '線西鄉', '04');
INSERT INTO stud_addr_zip VALUES ('508', '彰化縣', '和美鎮', '04');
INSERT INTO stud_addr_zip VALUES ('509', '彰化縣', '伸港鄉', '04');
INSERT INTO stud_addr_zip VALUES ('510', '彰化縣', '員林鎮', '04');
INSERT INTO stud_addr_zip VALUES ('511', '彰化縣', '社頭鄉', '04');
INSERT INTO stud_addr_zip VALUES ('512', '彰化縣', '永靖鄉', '04');
INSERT INTO stud_addr_zip VALUES ('513', '彰化縣', '埔心鄉', '04');
INSERT INTO stud_addr_zip VALUES ('514', '彰化縣', '溪湖鎮', '04');
INSERT INTO stud_addr_zip VALUES ('515', '彰化縣', '大村鄉', '04');
INSERT INTO stud_addr_zip VALUES ('516', '彰化縣', '埔鹽鄉', '04');
INSERT INTO stud_addr_zip VALUES ('520', '彰化縣', '田中鎮', '04');
INSERT INTO stud_addr_zip VALUES ('521', '彰化縣', '北斗鎮', '04');
INSERT INTO stud_addr_zip VALUES ('522', '彰化縣', '田尾鄉', '04');
INSERT INTO stud_addr_zip VALUES ('523', '彰化縣', '埤頭鄉', '04');
INSERT INTO stud_addr_zip VALUES ('524', '彰化縣', '溪州鄉', '04');
INSERT INTO stud_addr_zip VALUES ('525', '彰化縣', '竹塘鄉', '04');
INSERT INTO stud_addr_zip VALUES ('526', '彰化縣', '二林鎮', '04');
INSERT INTO stud_addr_zip VALUES ('527', '彰化縣', '大城鄉', '04');
INSERT INTO stud_addr_zip VALUES ('528', '彰化縣', '芳苑鄉', '04');
INSERT INTO stud_addr_zip VALUES ('530', '彰化縣', '二水鄉', '04');
INSERT INTO stud_addr_zip VALUES ('540', '南投縣', '南投市', '049');
INSERT INTO stud_addr_zip VALUES ('541', '南投縣', '中寮鄉', '049');
INSERT INTO stud_addr_zip VALUES ('542', '南投縣', '草屯鎮', '049');
INSERT INTO stud_addr_zip VALUES ('544', '南投縣', '國姓鄉', '049');
INSERT INTO stud_addr_zip VALUES ('545', '南投縣', '埔里鎮', '049');
INSERT INTO stud_addr_zip VALUES ('546', '南投縣', '仁愛鄉', '049');
INSERT INTO stud_addr_zip VALUES ('551', '南投縣', '名間鄉', '049');
INSERT INTO stud_addr_zip VALUES ('552', '南投縣', '集集鎮', '049');
INSERT INTO stud_addr_zip VALUES ('553', '南投縣', '水里鄉', '049');
INSERT INTO stud_addr_zip VALUES ('555', '南投縣', '魚池鄉', '049');
INSERT INTO stud_addr_zip VALUES ('556', '南投縣', '信義鄉', '049');
INSERT INTO stud_addr_zip VALUES ('557', '南投縣', '竹山鎮', '049');
INSERT INTO stud_addr_zip VALUES ('558', '南投縣', '鹿谷鄉', '049');
INSERT INTO stud_addr_zip VALUES ('600', '嘉義市', '西區友', '05');
INSERT INTO stud_addr_zip VALUES ('602', '嘉義縣', '番路鄉', '05');
INSERT INTO stud_addr_zip VALUES ('603', '嘉義縣', '梅山鄉', '05');
INSERT INTO stud_addr_zip VALUES ('604', '嘉義縣', '竹崎鄉', '05');
INSERT INTO stud_addr_zip VALUES ('605', '嘉義縣', '阿里山鄉', '05');
INSERT INTO stud_addr_zip VALUES ('606', '嘉義縣', '中埔鄉', '05');
INSERT INTO stud_addr_zip VALUES ('607', '嘉義縣', '大埔鄉', '05');
INSERT INTO stud_addr_zip VALUES ('608', '嘉義縣', '水上鄉', '05');
INSERT INTO stud_addr_zip VALUES ('611', '嘉義縣', '鹿草鄉', '05');
INSERT INTO stud_addr_zip VALUES ('612', '嘉義縣', '太保市', '05');
INSERT INTO stud_addr_zip VALUES ('613', '嘉義縣', '朴子市', '05');
INSERT INTO stud_addr_zip VALUES ('614', '嘉義縣', '東石鄉', '05');
INSERT INTO stud_addr_zip VALUES ('615', '嘉義縣', '六腳鄉', '05');
INSERT INTO stud_addr_zip VALUES ('616', '嘉義縣', '新港鄉', '05');
INSERT INTO stud_addr_zip VALUES ('621', '嘉義縣', '民雄鄉', '05');
INSERT INTO stud_addr_zip VALUES ('622', '嘉義縣', '大林鎮', '05');
INSERT INTO stud_addr_zip VALUES ('623', '嘉義縣', '溪口鄉', '05');
INSERT INTO stud_addr_zip VALUES ('624', '嘉義縣', '義竹鄉', '05');
INSERT INTO stud_addr_zip VALUES ('625', '嘉義縣', '布袋鎮', '05');
INSERT INTO stud_addr_zip VALUES ('630', '雲林縣', '斗南鎮', '05');
INSERT INTO stud_addr_zip VALUES ('631', '雲林縣', '大埤鄉', '05');
INSERT INTO stud_addr_zip VALUES ('632', '雲林縣', '虎尾鎮', '05');
INSERT INTO stud_addr_zip VALUES ('633', '雲林縣', '土庫鎮', '05');
INSERT INTO stud_addr_zip VALUES ('634', '雲林縣', '褒忠鄉', '05');
INSERT INTO stud_addr_zip VALUES ('635', '雲林縣', '東勢鄉', '05');
INSERT INTO stud_addr_zip VALUES ('636', '雲林縣', '台西鄉', '05');
INSERT INTO stud_addr_zip VALUES ('637', '雲林縣', '崙背鄉', '05');
INSERT INTO stud_addr_zip VALUES ('638', '雲林縣', '麥寮鄉', '05');
INSERT INTO stud_addr_zip VALUES ('640', '雲林縣', '斗六市', '05');
INSERT INTO stud_addr_zip VALUES ('643', '雲林縣', '林內鄉', '05');
INSERT INTO stud_addr_zip VALUES ('646', '雲林縣', '古坑鄉', '05');
INSERT INTO stud_addr_zip VALUES ('647', '雲林縣', '莿桐鄉', '05');
INSERT INTO stud_addr_zip VALUES ('648', '雲林縣', '西螺鎮', '05');
INSERT INTO stud_addr_zip VALUES ('649', '雲林縣', '二崙鄉', '05');
INSERT INTO stud_addr_zip VALUES ('651', '雲林縣', '北港鎮', '05');
INSERT INTO stud_addr_zip VALUES ('652', '雲林縣', '水林鄉', '05');
INSERT INTO stud_addr_zip VALUES ('653', '雲林縣', '口湖鄉', '05');
INSERT INTO stud_addr_zip VALUES ('654', '雲林縣', '四湖鄉', '05');
INSERT INTO stud_addr_zip VALUES ('655', '雲林縣', '元長鄉', '05');
INSERT INTO stud_addr_zip VALUES ('700', '台南市', '中區', '06');
INSERT INTO stud_addr_zip VALUES ('701', '台南市', '東區', '06');
INSERT INTO stud_addr_zip VALUES ('702', '台南市', '南區', '06');
INSERT INTO stud_addr_zip VALUES ('703', '台南市', '西區', '06');
INSERT INTO stud_addr_zip VALUES ('704', '台南市', '北區', '06');
INSERT INTO stud_addr_zip VALUES ('708', '台南市', '安平區', '06');
INSERT INTO stud_addr_zip VALUES ('709', '台南市', '安南區', '06');
INSERT INTO stud_addr_zip VALUES ('710', '台南縣', '永康市', '06');
INSERT INTO stud_addr_zip VALUES ('711', '台南縣', '歸仁鄉', '06');
INSERT INTO stud_addr_zip VALUES ('712', '台南縣', '新化鎮', '06');
INSERT INTO stud_addr_zip VALUES ('713', '台南縣', '左鎮鄉', '06');
INSERT INTO stud_addr_zip VALUES ('714', '台南縣', '玉井鄉', '06');
INSERT INTO stud_addr_zip VALUES ('715', '台南縣', '楠西鄉', '06');
INSERT INTO stud_addr_zip VALUES ('716', '台南縣', '南化鄉', '06');
INSERT INTO stud_addr_zip VALUES ('717', '台南縣', '仁德鄉', '06');
INSERT INTO stud_addr_zip VALUES ('718', '台南縣', '關廟鄉', '06');
INSERT INTO stud_addr_zip VALUES ('719', '台南縣', '龍崎鄉', '06');
INSERT INTO stud_addr_zip VALUES ('720', '台南縣', '官田鄉', '06');
INSERT INTO stud_addr_zip VALUES ('721', '台南縣', '麻豆鎮', '06');
INSERT INTO stud_addr_zip VALUES ('722', '台南縣', '佳里鎮', '06');
INSERT INTO stud_addr_zip VALUES ('723', '台南縣', '西港鄉', '06');
INSERT INTO stud_addr_zip VALUES ('724', '台南縣', '七股鄉', '06');
INSERT INTO stud_addr_zip VALUES ('725', '台南縣', '將軍鄉', '06');
INSERT INTO stud_addr_zip VALUES ('726', '台南縣', '學甲鎮', '06');
INSERT INTO stud_addr_zip VALUES ('727', '台南縣', '北門鄉', '06');
INSERT INTO stud_addr_zip VALUES ('730', '台南縣', '新營市', '06');
INSERT INTO stud_addr_zip VALUES ('731', '台南縣', '後壁鄉', '06');
INSERT INTO stud_addr_zip VALUES ('732', '台南縣', '白河鎮', '06');
INSERT INTO stud_addr_zip VALUES ('733', '台南縣', '東山鄉', '06');
INSERT INTO stud_addr_zip VALUES ('734', '台南縣', '六甲鄉', '06');
INSERT INTO stud_addr_zip VALUES ('735', '台南縣', '下營鄉', '06');
INSERT INTO stud_addr_zip VALUES ('736', '台南縣', '柳營鄉', '06');
INSERT INTO stud_addr_zip VALUES ('737', '台南縣', '鹽水鎮', '06');
INSERT INTO stud_addr_zip VALUES ('741', '台南縣', '善化鎮', '06');
INSERT INTO stud_addr_zip VALUES ('742', '台南縣', '大內鄉', '06');
INSERT INTO stud_addr_zip VALUES ('743', '台南縣', '山上鄉', '06');
INSERT INTO stud_addr_zip VALUES ('744', '台南縣', '新市鄉', '06');
INSERT INTO stud_addr_zip VALUES ('745', '台南縣', '安定鄉', '06');
INSERT INTO stud_addr_zip VALUES ('800', '高雄市', '新興區', '07');
INSERT INTO stud_addr_zip VALUES ('801', '高雄市', '前金區', '07');
INSERT INTO stud_addr_zip VALUES ('802', '高雄市', '苓雅區', '07');
INSERT INTO stud_addr_zip VALUES ('803', '高雄市', '鹽埕區', '07');
INSERT INTO stud_addr_zip VALUES ('804', '高雄市', '鼓山區', '07');
INSERT INTO stud_addr_zip VALUES ('805', '高雄市', '旗津區', '07');
INSERT INTO stud_addr_zip VALUES ('806', '高雄市', '前鎮區', '07');
INSERT INTO stud_addr_zip VALUES ('807', '高雄市', '三民區', '07');
INSERT INTO stud_addr_zip VALUES ('811', '高雄市', '楠梓區', '07');
INSERT INTO stud_addr_zip VALUES ('812', '高雄市', '小港區', '07');
INSERT INTO stud_addr_zip VALUES ('813', '高雄市', '左營自', '07');
INSERT INTO stud_addr_zip VALUES ('814', '高雄縣', '仁武鄉', '07');
INSERT INTO stud_addr_zip VALUES ('815', '高雄縣', '大社鄉', '07');
INSERT INTO stud_addr_zip VALUES ('817', '南海諸島', '東沙', '0827');
INSERT INTO stud_addr_zip VALUES ('819', '南海諸島', '南沙', '0827');
INSERT INTO stud_addr_zip VALUES ('820', '高雄縣', '岡山鎮', '07');
INSERT INTO stud_addr_zip VALUES ('821', '高雄縣', '路竹鄉', '07');
INSERT INTO stud_addr_zip VALUES ('822', '高雄縣', '阿蓮鄉', '07');
INSERT INTO stud_addr_zip VALUES ('823', '高雄縣', '田寮鄉', '07');
INSERT INTO stud_addr_zip VALUES ('824', '高雄縣', '燕巢鄉', '07');
INSERT INTO stud_addr_zip VALUES ('825', '高雄縣', '橋頭鄉', '07');
INSERT INTO stud_addr_zip VALUES ('826', '高雄縣', '梓官鄉', '07');
INSERT INTO stud_addr_zip VALUES ('827', '高雄縣', '彌陀鄉', '07');
INSERT INTO stud_addr_zip VALUES ('828', '高雄縣', '永安鄉', '07');
INSERT INTO stud_addr_zip VALUES ('829', '高雄縣', '湖內鄉', '07');
INSERT INTO stud_addr_zip VALUES ('830', '高雄縣', '鳳山市', '07');
INSERT INTO stud_addr_zip VALUES ('831', '高雄縣', '大寮鄉', '07');
INSERT INTO stud_addr_zip VALUES ('832', '高雄縣', '林園鄉', '07');
INSERT INTO stud_addr_zip VALUES ('833', '高雄縣', '鳥松鄉', '07');
INSERT INTO stud_addr_zip VALUES ('840', '高雄縣', '大樹鄉', '07');
INSERT INTO stud_addr_zip VALUES ('842', '高雄縣', '旗山鎮', '07');
INSERT INTO stud_addr_zip VALUES ('843', '高雄縣', '美濃鎮', '07');
INSERT INTO stud_addr_zip VALUES ('844', '高雄縣', '六龜鄉', '07');
INSERT INTO stud_addr_zip VALUES ('845', '高雄縣', '內門鄉', '07');
INSERT INTO stud_addr_zip VALUES ('846', '高雄縣', '杉林鄉', '07');
INSERT INTO stud_addr_zip VALUES ('847', '高雄縣', '甲仙鄉', '07');
INSERT INTO stud_addr_zip VALUES ('848', '高雄縣', '桃源鄉', '07');
INSERT INTO stud_addr_zip VALUES ('849', '高雄縣', '三民鄉', '07');
INSERT INTO stud_addr_zip VALUES ('851', '高雄縣', '茂林鄉', '07');
INSERT INTO stud_addr_zip VALUES ('852', '高雄縣', '茄萣鄉', '07');
INSERT INTO stud_addr_zip VALUES ('880', '澎湖縣', '馬公市', '06');
INSERT INTO stud_addr_zip VALUES ('881', '澎湖縣', '西嶼鄉', '06');
INSERT INTO stud_addr_zip VALUES ('882', '澎湖縣', '望安鄉', '06');
INSERT INTO stud_addr_zip VALUES ('883', '澎湖縣', '七美鄉', '06');
INSERT INTO stud_addr_zip VALUES ('884', '澎湖縣', '白沙鄉', '06');
INSERT INTO stud_addr_zip VALUES ('885', '澎湖縣', '湖西鄉', '06');
INSERT INTO stud_addr_zip VALUES ('890', '金門縣', '金沙鎮', '0823');
INSERT INTO stud_addr_zip VALUES ('891', '金門縣', '金湖鎮', '0823');
INSERT INTO stud_addr_zip VALUES ('892', '金門縣', '金寧鄉', '0823');
INSERT INTO stud_addr_zip VALUES ('893', '金門縣', '金城鎮', '0823');
INSERT INTO stud_addr_zip VALUES ('894', '金門縣', '烈嶼鄉', '0823');
INSERT INTO stud_addr_zip VALUES ('896', '金門縣', '烏坵', '0826');
INSERT INTO stud_addr_zip VALUES ('900', '屏東縣', '屏東市', '08');
INSERT INTO stud_addr_zip VALUES ('901', '屏東縣', '三地鄉', '08');
INSERT INTO stud_addr_zip VALUES ('902', '屏東縣', '霧台鄉', '08');
INSERT INTO stud_addr_zip VALUES ('903', '屏東縣', '瑪家鄉', '08');
INSERT INTO stud_addr_zip VALUES ('904', '屏東縣', '九如鄉', '08');
INSERT INTO stud_addr_zip VALUES ('905', '屏東縣', '里港鄉', '08');
INSERT INTO stud_addr_zip VALUES ('906', '屏東縣', '高樹鄉', '08');
INSERT INTO stud_addr_zip VALUES ('907', '屏東縣', '鹽埔鄉', '08');
INSERT INTO stud_addr_zip VALUES ('908', '屏東縣', '長治鄉', '08');
INSERT INTO stud_addr_zip VALUES ('909', '屏東縣', '麟洛鄉', '08');
INSERT INTO stud_addr_zip VALUES ('911', '屏東縣', '竹田鄉', '08');
INSERT INTO stud_addr_zip VALUES ('912', '屏東縣', '內埔鄉', '08');
INSERT INTO stud_addr_zip VALUES ('913', '屏東縣', '萬丹鄉', '08');
INSERT INTO stud_addr_zip VALUES ('920', '屏東縣', '潮州鎮', '08');
INSERT INTO stud_addr_zip VALUES ('921', '屏東縣', '泰武鄉', '08');
INSERT INTO stud_addr_zip VALUES ('922', '屏東縣', '來義鄉', '08');
INSERT INTO stud_addr_zip VALUES ('923', '屏東縣', '萬巒鄉', '08');
INSERT INTO stud_addr_zip VALUES ('924', '屏東縣', '崁頂鄉', '08');
INSERT INTO stud_addr_zip VALUES ('925', '屏東縣', '新埤鄉', '08');
INSERT INTO stud_addr_zip VALUES ('926', '屏東縣', '南州鄉', '08');
INSERT INTO stud_addr_zip VALUES ('927', '屏東縣', '林邊鄉', '08');
INSERT INTO stud_addr_zip VALUES ('928', '屏東縣', '東港鎮', '08');
INSERT INTO stud_addr_zip VALUES ('929', '屏東縣', '琉球鄉', '08');
INSERT INTO stud_addr_zip VALUES ('931', '屏東縣', '佳冬鄉', '08');
INSERT INTO stud_addr_zip VALUES ('932', '屏東縣', '新園鄉', '08');
INSERT INTO stud_addr_zip VALUES ('940', '屏東縣', '枋寮鄉', '08');
INSERT INTO stud_addr_zip VALUES ('941', '屏東縣', '枋山鄉', '08');
INSERT INTO stud_addr_zip VALUES ('942', '屏東縣', '春日鄉', '08');
INSERT INTO stud_addr_zip VALUES ('943', '屏東縣', '獅子鄉', '08');
INSERT INTO stud_addr_zip VALUES ('944', '屏東縣', '車城鄉', '08');
INSERT INTO stud_addr_zip VALUES ('945', '屏東縣', '牡丹鄉', '08');
INSERT INTO stud_addr_zip VALUES ('946', '屏東縣', '恆春鎮', '08');
INSERT INTO stud_addr_zip VALUES ('947', '屏東縣', '滿州鄉', '08');
INSERT INTO stud_addr_zip VALUES ('950', '台東縣', '台東市', '089');
INSERT INTO stud_addr_zip VALUES ('951', '台東縣', '綠島鄉', '089');
INSERT INTO stud_addr_zip VALUES ('952', '台東縣', '蘭嶼鄉', '089');
INSERT INTO stud_addr_zip VALUES ('953', '台東縣', '延平鄉', '089');
INSERT INTO stud_addr_zip VALUES ('954', '台東縣', '卑南鄉', '089');
INSERT INTO stud_addr_zip VALUES ('955', '台東縣', '鹿野鄉', '089');
INSERT INTO stud_addr_zip VALUES ('956', '台東縣', '關山鎮', '089');
INSERT INTO stud_addr_zip VALUES ('957', '台東縣', '海端鄉', '089');
INSERT INTO stud_addr_zip VALUES ('958', '台東縣', '池上鄉', '089');
INSERT INTO stud_addr_zip VALUES ('959', '台東縣', '東河鄉', '089');
# --------------------------------------------------------

ALTER TABLE `board_p` CHANGE `b_url` `b_url` VARCHAR( 150 ) NOT NULL ;
ALTER TABLE `docup_p` CHANGE `docup_p_id` `docup_p_id` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `docup_p` CHANGE `doc_kind_id` `doc_kind_id` INT DEFAULT '0' NOT NULL ;
ALTER TABLE `docup` CHANGE `docup_p_id` `docup_p_id` INT NOT NULL ;
ALTER TABLE `score_setup` ADD `allow_modify` ENUM( 'false', 'true' ) NOT NULL ;
ALTER TABLE `score_ss` ADD `class_id` VARCHAR( 11 ) NOT NULL ,ADD `link_ss` VARCHAR( 200 ) NOT NULL ;
ALTER TABLE `sfs_text` ADD `t_order_id` INT NOT NULL ;
ALTER TABLE `sfs_text` CHANGE `d_id` `d_id` VARCHAR( 20 ) DEFAULT '0' NOT NULL ;
ALTER TABLE `board_p` ADD `teacher_sn` SMALLINT UNSIGNED NOT NULL ;
ALTER TABLE `board_check` ADD `teacher_sn` SMALLINT UNSIGNED NOT NULL ;
ALTER TABLE `docup` ADD `teacher_sn` SMALLINT UNSIGNED NOT NULL ;
ALTER TABLE `docup_p` ADD `teacher_sn` SMALLINT UNSIGNED NOT NULL ;

