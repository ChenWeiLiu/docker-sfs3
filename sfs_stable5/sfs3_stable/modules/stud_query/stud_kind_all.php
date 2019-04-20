<?php
// $Id: stud_kind_all.php 5369 2010-08-25 07:31:36Z wkb $

/*
=====================================================
程式：學生身分別統計表總表
ver1.0 -- wkb
=====================================================
*/

/* 學務系統設定檔 */
include "stud_query_config.php";  

//認證檢查
sfs_check();

//要產生的csv檔名
$char_kind=$_REQUEST[char_kind];
if($char_kind == 1){
  $file_name = curr_year()."_".curr_seme()."_utf8_all.csv";
  $char_value = "UTF-8";
}else{
  $file_name = curr_year()."_".curr_seme()."_big5_all.csv";
  $char_value = "Big5";
}


header("Content-disposition: filename=".$file_name);
header("Content-type: application/octetstream ; Charset=".$char_value."");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
header("Expires: 0");
$test = "";
$test.= '"年","班","班級","座號","姓名","性別",';/**/
//echo $test;
//exit;

//取得學生身分別代號
$stud_kind_arr = stud_kind();


foreach($stud_kind_arr as $k=>$v){
  //echo count($stud_kind_arr)."|".$i."|".$stud_kind_arr[$i]."<BR>\n";
  if(strlen($v)>1 and $k>0){
    $test.= '"'.$v.'",';
  }
}
$test.= "\n";

$query = "select a.*,left(a.curr_class_num,length(a.curr_class_num)-2) as stud_class,right(a.curr_class_num,2) as stud_site from stud_base a where a.stud_study_cond=0 order by a.curr_class_num;";
$rs = $CONN->Execute($query) or die ($CONN->ErrorMsg()."|<BR>\n".$query);
//$k1=0;
//echo $query."|<BR>\n";
//echo $rs -> RecordCount()."|<BR>\n";
while($row = $rs->FetchRow()){
  //echo $k1."|111|<BR>\n";
  $test.=substr($row['stud_class'],0,1).',';
  $test.=substr($row['stud_class'],1,2).',';
  $test.=$class_base[$row['stud_class']].',';
  $test.=$row['stud_site'].',';
  $test.=$row['stud_name'].',';
  $test.=$sex_arr[$row['stud_sex']].',';
  foreach($stud_kind_arr as $k=>$v){
	if (strlen($v)>1 and $k>0){
		//if(strpos($row['stud_kind'],",".$k.",")){
		if(isMatch($row['stud_kind'], $k)>0){
			//echo "Id:".$row['stud_kind']."|".$k."|".$v."|<BR>\n";
			$test.= "1,";
		}else{
			$test.= ",";
		}
    }
  }
  $test.= "\n";
}

echo $test;

//igogo 學生身份別改以RE判斷 原學生身份為 ,3,9 時,無法判斷,3, 為真 strpos() 取得位置為0  
//if(0) {}  永遠為false 
function isMatch($subject,$pattern) {
        //$pattern = "/[,]3[,]/"; 3為低收入戶
        $pattern = sprintf("/[,]%s[,]/",$pattern);
        preg_match($pattern, $subject, $matches);
        return sizeof($matches);
}
exit;
