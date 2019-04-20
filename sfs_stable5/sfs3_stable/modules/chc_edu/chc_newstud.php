<?php
include "config.php";
sfs_check();

download_csv(curr_year());

function download_csv($year){
  global $CONN,$school_sshort_name,$school_long_name;
   $stud_sex_array=array(1=>"男",2=>"女");
   //新生入學年月
   $in_time = $year.".08";

   $data = "學號,姓名,性別,身分證字號,生日,入學時間,入學資格,戶籍地址\r\n";
//   $sql_select="select stud_id,stud_name,stud_sex,stud_person_id,stud_birthday,stud_addr_1 from stud_base where stud_study_year ='".$year."' and (stud_study_cond='0' or stud_study_cond='8') ";
   $sql_select="select stud_id,stud_name,stud_sex,stud_person_id,stud_birthday,stud_addr_1 from stud_base where stud_study_year ='".$year."' and stud_study_cond='0'";
   $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
   while( list($stud_id,$stud_name,$stud_sex,$stud_person_id,$stud_birthday,$stud_addr_1)=$recordSet->FetchRow() ){
     //性別轉換為男女
     $stud_sex = $stud_sex_array[$stud_sex];
      //生日拆成年月日，年3位元，月2位元，日2位元
     $birth_date = explode("-",$stud_birthday);
     //$birth_year = sprintf("%03s",$birth_date[0]-1911);
     $birth_year = $birth_date[0]-1911;
     $birth_mon = sprintf("%02s",$birth_date[1]);
     $birth_day = sprintf("%02s",$birth_date[2]);
     $stud_birthday = $birth_year.".".$birth_mon.".".$birth_day;

     $data.=$stud_id.",".$stud_name.",".$stud_sex.",".$stud_person_id.",".$stud_birthday.",".$in_time.",".$school_long_name.",".$stud_addr_1."\r\n";
   }


   $filename=$year."學年".$school_sshort_name."入學學生名冊.csv";
   $filename = mb_convert_encoding($filename,"utf-8","big5");
   header("Content-disposition: attachment;filename=$filename");
   header("Content-type: text/x-csv ; Charset=utf-8");
   header("Pragma: no-cache");
   header("Expires: 0");
   $data =  mb_convert_encoding($data,"utf-8","big5");
	echo "\xEF\xBB\xBF"; // UTF-8 BOM
   echo $data;
}
