# 資料庫： 此為本模組所使用的資料表指令, 當安裝此模組時, SFS3 系統會一併執行這裡的 MySQL 指令,建立資料表.
#					 若本模組不需建立資料表, 則留空白即可.
#

#補考學期別設定
#用於設定目前正在進行的補考學期
# resit_year_seme 目前學期
# start_time	考試開始時間 (用於一次領全部考卷模式)
# end_time		考試結束時間 (用於一次領全部考卷模式)
# paper_mode	領卷模式 
#							0 表示依考卷本身設定, 一次領一科
#							1 考試開始時間到就開放所有領卷考取供領卷
#												
CREATE TABLE IF NOT EXISTS `resit_seme_setup` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `now_year_seme` varchar(4) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `paper_mode` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`sn`)
) ENGINE=MyISAM;

#試卷設定
#用於記錄每份領域試卷的設定
# sn 流水號
# seme_year_seme 學期
# class_year 年級 (1~9)
# scope 領域別
# start_time 開始時間 (當 paper_mode 設為 0 時, 本份考卷領卷開始時間依此設定)
# end_time 結束時間 (當 paper_mode 設為 0 時, 本份考卷考試領卷結束時間依此設定)
# timer_mode 計時模式(0個別, 每個人都給 timer 設定的秒數倒數計時；1同時,每個人的結束時間都是 end_time 設定值)
# timer 考試時間計時長度 (分)
# items 考試時隨機出幾題
# relay_answer 交卷後是否立即回饋答案?
# double_papers 是否可重覆領卷, 學生可能會斷線再進來領, 或同時兩個人登入同一帳號領卷
CREATE TABLE IF NOT EXISTS `resit_paper_setup` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `seme_year_seme` varchar(4) NOT NULL,
  `class_year` tinyint(1) not null,
	`scope` varchar(15) not null,
	`start_time` datetime not null,
	`end_time` datetime not null,
	`timer_mode` tinyint(1) not null,
	`timer` int(5) not null,
	`items` int(3) not null,
	`relay_answer` tinyint(1) not null,
	`double_papers` tinyint(1) not null,
  PRIMARY KEY  (`sn`)
) ENGINE=MyISAM;

#試題庫
# sn 流水號
# paper_sn 本試題對應於 resit_paper_setup.sn 值 
# sort 試題號
# question 題幹
# cha 選項a
# chb 選項b
# chc 選項c 
# chd 選項d
# cha 選項a
# fig_q 題幹的附圖 sn (對應 resit_images.sn 值 , 保留空值不附圖)
# fig_a 選項a的附圖 sn (對應 resit_images.sn 值 , 保留空值不附圖)
# fig_b 選項b的附圖 sn (對應 resit_images.sn 值 , 保留空值不附圖)
# fig_c 選項c的附圖 sn (對應 resit_images.sn 值 , 保留空值不附圖)
# fig_d 選項d的附圖 sn (對應 resit_images.sn 值 , 保留空值不附圖)
# answer 解答
#
CREATE TABLE IF NOT EXISTS `resit_exam_items` (
  `sn` int(10) unsigned NOT NULL auto_increment,
	`paper_sn` int(10) not null,
	`sort` int(3) not null,
	`question` text not null,
	`cha` text not null,
	`chb` text not null,
	`chc` text not null,
	`chd` text not null,
	`fig_q`	int(6) null,
	`fig_a` int(6) null,	
	`fig_b` int(6) null,	
	`fig_c` int(6) null,	
	`fig_d` int(6) null,
	`answer` text not null,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM;

#成績及作答記錄 (利用serialize指令記錄作答陣列, 並利用unserilize解開)
# sn 流水號
# student_sn 學生的流水號
# paper_sn 試卷的流水號
# score 得分
# items 學生考的題目 (利用serialize指令記錄作答陣列再儲存, 讀取後利用unserilize解開)
# answer 學生的作答明細 (利用serialize指令記錄作答陣列再儲存, 讀取後利用unserilize解開)
# entrance 是否已領卷
# entrance_time 領卷時間
# complete 是否完成考試
# complete_time 完成時間
# update_time 記錄時間
#
CREATE TABLE IF NOT EXISTS `resit_exam_score` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `student_sn` int(10) NOT NULL,
	`paper_sn` int(10) not null,
	`org_score` float unsigned null,
	`score` float unsigned null,
	`items` text not null,
	`answers` text not null,
	`entrance` tinyint(1) not null,
	`entrance_time` datetime not null,
	`complete` tinyint(1) not null,
	`complete_time` datetime not null,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`)
) ENGINE=MyISAM;

#圖庫
create table `resit_images` (
	sn int(6) not null auto_increment,
	filetype char(50) not null,
	xx int(4) not null,
	yy int(4) not null,
	content longblob null,
	primary key (sn)
) ENGINE=MyISAM;

	
