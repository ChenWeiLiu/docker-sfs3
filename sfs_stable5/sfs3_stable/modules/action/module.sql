#
# 資料表格式： `actiontb`
#

CREATE TABLE actiontb (
  act_ID int not null auto_increment ,
  act_date DATE not null ,   
  act_name VARCHAR (50) not null ,
  act_info TINYTEXT not null , 
  act_icon VARCHAR (50) , 
  
  act_dir VARCHAR (50) not null ,      
  act_index VARCHAR (50) not null ,    
  act_postdate DATE not null , 

  act_auth VARCHAR (50) ,
  act_view mediumint ,  
  primary key (act_ID)
 ) 