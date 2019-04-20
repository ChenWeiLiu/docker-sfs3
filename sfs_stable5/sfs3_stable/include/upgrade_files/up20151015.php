<?php
	
//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}



//$tablename資料表不存時建立
$tablename="grad_stud";

if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '$tablename'")) == 0) 
{ 
  $SQL="CREATE TABLE $tablename (
   grad_sn int(10) NOT NULL auto_increment,
   stud_grad_year tinyint(3) unsigned,
   class_year char(2),
   class_sort tinyint(2) unsigned,
   stud_id varchar(20),
   grad_kind tinyint(1) unsigned,
   grad_date date,
   grad_word varchar(20),
   grad_num varchar(20),
   grad_score float unsigned,
   new_school varchar(40),
   student_sn int(11) NOT NULL,
   UNIQUE grad_sn (grad_sn),
   KEY stud_id (stud_id),
   KEY student_sn (student_sn)
   )";
  $rs=$CONN->Execute($SQL);
}
else
{

//判斷$tablename資料表 是否具有student_sn 欄位
$SQL="select count(*) as countsn from information_schema.COLUMNS Where TABLE_SCHEMA = 'sfs3' and TABLE_NAME='$tablename' and COLUMN_NAME='student_sn';" ;
$rs=$CONN->Execute($SQL);
$countsn=$rs->fields["countsn"];
			
    
//$tablename資料表 無student_sn 欄位時
if (empty($countsn) || $countsn==0)
{
$SQL="ALTER TABLE $tablename ADD student_sn int(11) NOT NULL" ; 
$rs=$CONN->Execute($SQL);	
	 
}		

 //判斷$tablename資料表索引student_sn是否存在
 if(mysql_num_rows(mysql_query("SHOW INDEXES FROM $tablename WHERE Key_name = 'student_sn'")) == 0) 
{ 
 $SQL="ALTER TABLE $tablename ADD INDEX(student_sn)";
 $rs=$CONN->Execute($SQL);
 }	

 //判斷$tablename資料表索引stud_id是否存在
 if(mysql_num_rows(mysql_query("SHOW INDEXES FROM $tablename WHERE Key_name = 'stud_id'")) == 0) 
 { 
 $SQL="ALTER TABLE $tablename ADD INDEX(stud_id)";
 $rs=$CONN->Execute($SQL);
 }
	
}	


//校正流水號	
$SQL="select stud_id,stud_grad_year,student_sn from $tablename ";
$rs=$CONN->Execute($SQL);

if(is_object($rs))
{

		while (!$rs->EOF) 
		{
		$grad_stud_id=$rs->fields["stud_id"];
		$grad_stud_grad_year=$rs->fields["stud_grad_year"]-2;
		$grad_student_sn=$rs->fields["student_sn"];
		 $sql_select = "select student_sn,stud_study_year from stud_base where stud_study_year='$grad_stud_grad_year' and stud_id='$grad_stud_id' Limit 1";
         $recordSet=$CONN->Execute($sql_select);
		 $student_sn=$recordSet->fields['student_sn'];
         $stud_study_year=$recordSet->fields['stud_study_year']+2;
		if ($grad_student_sn !=$student_sn)
		{		
	
		$upsql = "UPDATE $tablename SET student_sn='$student_sn' WHERE stud_id='$grad_stud_id' and stud_grad_year='$stud_study_year'";
	    $CONN->Execute($upsql) ;

		}
		$rs->MoveNext();
		}
}
   
	
?>