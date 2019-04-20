# $Id: module.sql 5311 2009-01-10 08:11:55Z hami $
#
# 資料表格式： `photoviewtb`
#

CREATE TABLE photoviewtb (
  act_ID int not null auto_increment ,
  act_date DATE not null ,   
  act_name VARCHAR (50) not null ,
  act_info TEXT not null , 
  act_dir VARCHAR (50) not null ,      
  act_postdate DATE not null , 
  act_auth VARCHAR (50) ,
  act_view mediumint ,  
  primary key (act_ID)
 ) 
