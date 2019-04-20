<?php
// $Id: old_psy_2_csv.php 7711 2013-10-23 13:07:37Z smallduh $

include "config.php";

sfs_check();

$sql="SELECT LEFT(a.seme_year_seme,3) as year,RIGHT(a.seme_year_seme,1) as semester,b.student_sn,a.st_numb,a.st_name,a.st_score_numb,a.st_data_from,a.st_chang_numb,a.st_name_long,a.teacher_sn FROM stud_seme_test a,stud_base b WHERE a.stud_id=b.stud_id ORDER BY seme_year_seme";
$res=$CONN->Execute($sql) or user_error("擷取舊心裡測驗資料失敗！<br>$sql",256);

$filename = "學生舊心裡測驗資料列表.csv";
$Str='"學年","學期","學生編號","心理測驗編號","測驗中文簡稱","成績編號","資料來源","轉換表編號","測驗中文全名","教師編號"'."\r\n";
while(!$res->EOF)
{
	$record_str="";
	for($i=0;$i<$res->FieldCount();$i++){
		$record_str.='"'.$res->fields[$i].'",';
	}
	$record_str=substr($record_str,0,-1);
	$Str.="$record_str\r\n";
	$res->MoveNext();
}
header("Content-disposition: attachment; filename=$filename");
header("Content-type: text/x-csv; Charset=Big5");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
header("Expires: 0");
echo $Str;
?>