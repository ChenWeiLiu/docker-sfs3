# $Id: sfs_module.sql 8152 2014-09-30 01:15:55Z smallduh $

# phpMyAdmin MySQL-Dump
# version 2.4.0
# http://www.phpmyadmin.net/ (download page)
#
# 主機: localhost
# 建立日期: Apr 01, 2003 at 11:23 AM
# 伺服器版本: 3.23.56
# PHP 版本: 4.3.1
# 資料庫: `sfs3`
# --------------------------------------------------------

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
) ENGINE=MyISAM;

#
# 列出以下資料庫的數據： `sfs_module`
#

INSERT INTO sfs_module VALUES (1, '系統管理', '', 7, 0, 1, 0, '', 'administrator_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (2, '校務行政', '', 1, 0, 1, 0, '', 'school_icon.png', '', '0000-00-00', '分類', '');
INSERT INTO sfs_module VALUES (8, '文件資料庫', 'docup', 4, 0, 1, 2, '2.0.1', '', 'hami', '2002-12-15', '模組', '');
INSERT INTO sfs_module VALUES (9, '午餐\食譜公告', 'lunch', 3, 0, 1, 2, '1.0.8', '', 'prolin', '2000-09-17', '模組', '');
INSERT INTO sfs_module VALUES (7, '圖書管理系統', 'book', 2, 0, 1, 2, '2.0.1', '', 'hami', '2002-12-15', '模組', '');
INSERT INTO sfs_module VALUES (41, '南縣教師管理', 'tnc_teach_class', 6, 0, 1, 12, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (11, '校園行事曆', 'school_calendar', 1, 0, 1, 2, '1.0', '', 'tad', '2003-03-24', '模組', '');
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
INSERT INTO sfs_module VALUES (22, '新增移除模組', 'install_module', 4, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (23, '成績單介面管理', 'score_input_interface', 11, 0, 0, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (24, '模組權限管理', 'sfs_man2', 1, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (25, '模組參數管理', 'sfs_module', 3, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (26, '系統選項清單設定', 'sfs_text', 7, 0, 1, 1, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (27, '缺曠課獎懲管理', 'absent', 1, 0, 1, 13, '', '', '', '0000-00-00', '模組', '');
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
INSERT INTO sfs_module VALUES (56, '數位相本', 'mig', 5, 0, 1, 2, '2.0.1', '', 'hami', '2003-03-19', '模組', '');
INSERT INTO sfs_module VALUES (57, '教師通訊錄', 'teach_report_more', 22, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');
INSERT INTO sfs_module VALUES (58, '個人資料', 'teacher_self', 20, 0, 1, 15, '', '', '', '0000-00-00', '模組', '');

