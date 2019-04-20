
# 資料庫： `contest` 結構說明
#

#查資料比賽題庫
CREATE TABLE contest_itembank (
  id int(5) not null auto_increment,
  ibsn varchar(15) not null,
  question text not null,
  ans text not null,
  ans_url text null,
  primary key (id)
) ENGINE=MyISAM ;

#試題組, 每次競賽從題庫抓題目
#tsn 唯一代碼, tsort出題順序
create table contest_ibgroup (
id int(5) not null auto_increment,
tsn int(5) not null,
tsort int(3) not null,
ibsn varchar(15) not null,
question text not null,
ans text not null,
ans_url text null,
primary key (id)
) ENGINE=MyISAM;

#查資料作答記錄, 
create table contest_record1 (
id int(5) not null auto_increment,
tsn int(5) not null,
student_sn int(10) not null,
ibsn varchar(15) not null,
myans varchar(200) not null,
lurl varchar(200) not null,
anstime datetime not null,
chk tinyint(1) not null default '0',
primary key (id)
) ENGINE=MyISAM ;

#繪圖, 簡報等作答記錄 (檔案上傳)
create table contest_record2 (
id int(5) not null auto_increment,
tsn int(5) not null,
student_sn int(10) not null,
filename varchar(40) not null,
anstime datetime not null,
primary key (id)
) ENGINE=MyISAM;

#競賽相關設定
create table contest_setup (
tsn int(5) not null auto_increment,
year_seme varchar(4) not null,
title varchar(200) not null,
qtext varchar(200) not null,
sttime datetime not null,
endtime datetime not null,
memo blob not null,
active tinyint(1) not null,
open_judge tinyint(1) not null,
open_review tinyint(1) not null,
primary key (tsn)
) ENGINE=MyISAM;

#每個競賽的評分細項
create table contest_score_setup (
id int(5) not null auto_increment,
tsn int(5) not null,
sco_sn varchar(15) not null,
sco_text varchar(40) not null,
primary key (id)
) ENGINE=MyISAM;

#每個競賽的學生細項評分成績
create table contest_score_user (
id int(5) not null auto_increment,
student_sn int(10) not null,
teacher_sn int(10) not null,
sco_sn varchar(15) not null,
sco_num int(2) not null,
primary key (id)
) ENGINE=MyISAM;

#繪圖, 簡報等總分與評語
create table contest_score_record2 (
id int(5) not null auto_increment,
tsn int(5) not null,
student_sn int(10) not null,
teacher_sn int(10) not null,
score decimal(5,2) not null,
prize_memo text null,
primary key (id)
) ENGINE=MyISAM;

#考生名單帳號
create table contest_user (
id int(5) not null auto_increment,
tsn int(5) not null,
student_sn int(10) not null,
lastlogin datetime not null,
logintimes int(3) not null,
prize_id int(2) null,
prize_text varchar(32) null,
ifgroup varchar(5) not null,
primary key (id)
) ENGINE=MyISAM;

#評審員帳號 , 競賽結束後30日內有效
create table contest_judge_user (
id int(5) not null auto_increment,
teacher_sn int(10) not null,
tsn int(5) not null,
lastlogin datetime not null,
logintimes int(3) not null,
primary key (id)
) ENGINE=MyISAM;

#最新消息設定
create table contest_news (
nsn int(5) not null auto_increment,
title varchar(200) not null,
sttime datetime not null,
endtime datetime not null,
memo blob not null,
updatetime datetime not null,
htmlcode tinyint(1) not null,
primary key (nsn)
) ENGINE=MyISAM;

#檔案下載設定
create table contest_files (
fsn int(5) not null auto_increment,
nsn int(5) not null,
ftext varchar(200) not null,
filename varchar(36) not null,
primary key (fsn)
) ENGINE=MyISAM;

