#
# 列出以下資料庫的數據： 
#
# jshow_set  某類別的設定
# 
# 欄位說明：
#						id 編號(自動)  
#						idtext 中文說明
#						memo 顯示給使用者的說明
#						max_width 上傳圖片時,允許的最大width (自動縮圖)
#						max_height 上傳圖片時,允許的最大 height (自動縮圖)
#						init_pic_set 未指定圖時，預設要顯示那張圖
#						day_pic_set	 那些日期(只供月-日),分別秀那些圖(二維陣列,以 serialize 編碼)
#						display_mode 展圖模式 (0 資料庫內所有圖片依序列出,1 資料庫內所有圖片亂數列出, 2 依日期 )
#						update_time	更新日期(自動)
#
CREATE TABLE jshow_setup (
  `kind_id` int(5) NOT NULL auto_increment,
	`id_name` varchar(30) not null,
	`memo` text,
	`max_width` int(5) not null,
	`max_height` int(5) not null,
	`init_pic_set` int(5) not null,
	`display_mode` tinyint(1) not null,
  `day_pic_set` text,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (kind_id)
) Engine=MyISAM;

# jshow_check  某類別的授權設定
# 
# 欄位說明：
#
CREATE TABLE jshow_check (
  `id` int(5) NOT NULL auto_increment,
  `kind_id` int(5) NOT NULL default '0',
  `post_office` tinyint(4) NOT NULL default '-1',
  `teach_id` varchar(20) NOT NULL default 'none',
  `teach_title_id` tinyint(4) NOT NULL default '-1',
  `is_admin` char(1) NOT NULL default '',
  `teacher_sn` int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) Engine=MyISAM;


#
# jshow_data 圖片資料
# 
# 欄位說明：id 編號(自動)
#						kind_id 所屬類別 (jshow_set的kind_id) 
#						sub 圖片主題
#						memo 內文說明
#						filename 檔名
#						display 是否展示 0關閉 1打開
#						upload_day  上傳時間
#						teacher_sn	上傳者
#
CREATE TABLE jshow_pic(
 `id` int( 5 ) NOT NULL AUTO_INCREMENT ,
 `kind_id` int(5) NOT NULL default '0', 
 `sub` varchar( 128 ) NOT NULL default '',
 `memo` text NOT NULL ,
 `filename` text NOT NULL ,
 `display` tinyint(1) NOT NULL default '0', 
 `display_sub` tinyint(1) NOT NULL default '0',
 `display_memo` tinyint(1) NOT NULL default '0',
 `upload_day` datetime NOT NULL ,
 `teacher_sn` int( 6 ) NOT NULL ,
 `sort` int(3) not null default '100',
 PRIMARY KEY ( id ) 
) Engine=MyISAM;

