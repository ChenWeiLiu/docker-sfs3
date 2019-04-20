<?php
// $Id: csv_export.php 7711 2013-10-23 13:07:37Z smallduh $

include "config.php";
include "../../include/sfs_case_dataarray.php";
sfs_check();

$semester=$_REQUEST['semester'];
$item=$_REQUEST['item'];
$key=$_REQUEST['key'];
$value=$_REQUEST['value'];

// 取出班級陣列
$class_base = class_base($semester);


$status_arr=array();
$status_arr['父母關係']="SELECT b.* FROM stud_base b,stud_seme_eduh a WHERE a.stud_id=b.stud_id AND a.seme_year_seme='$semester' AND a.sse_relation=$key ORDER BY b.curr_class_num";
$status_arr['家庭類型']="SELECT b.* FROM stud_base b,stud_seme_eduh a WHERE a.stud_id=b.stud_id AND a.seme_year_seme='$semester' AND a.sse_family_kind=$key ORDER BY b.curr_class_num";
$status_arr['家庭氣氛']="SELECT b.* FROM stud_base b,stud_seme_eduh a WHERE a.stud_id=b.stud_id AND a.seme_year_seme='$semester' AND a.sse_family_air=$key ORDER BY b.curr_class_num";
$status_arr['父管教方式']="SELECT b.* FROM stud_base b,stud_seme_eduh a WHERE a.stud_id=b.stud_id AND a.seme_year_seme='$semester' AND a.sse_farther=$key ORDER BY b.curr_class_num";
$status_arr['母管教方式']="SELECT b.* FROM stud_base b,stud_seme_eduh a WHERE a.stud_id=b.stud_id AND a.seme_year_seme='$semester' AND a.sse_mother=$key ORDER BY b.curr_class_num";
$status_arr['居住情形']="SELECT b.* FROM stud_base b,stud_seme_eduh a WHERE a.stud_id=b.stud_id AND a.seme_year_seme='$semester' AND a.sse_live_state=$key ORDER BY b.curr_class_num";
$status_arr['經濟狀況']="SELECT b.* FROM stud_base b,stud_seme_eduh a WHERE a.stud_id=b.stud_id AND a.seme_year_seme='$semester' AND a.sse_rich_state=$key ORDER BY b.curr_class_num";

$sql=$status_arr[$item];
$res=$CONN->Execute($sql) or user_error("執行列表sql失敗！<br>$sql",256);

################################    輸出 CSV    ##################################
$filename = $school_id.$school_short_name." $semester $item [ $value ] 學生名單清冊.csv";
$Str="班級名稱,座號,學號,姓名,性別,出生年月日,身分證字號,通訊地址,戶籍電話,通訊電話\r\n";
while(!$res->EOF)
{
    
	$class_number=substr($res->fields['curr_class_num'],-2);
	$class_id=substr($res->fields['curr_class_num'],0,3);
    $class_name=$class_base[$class_id];

    $Str.=$class_name.',';
    $Str.=$class_number.',';
    $Str.=$res->fields['stud_id'].',';
    $Str.=$res->fields['stud_name'].',';
    $Str.=($res->fields['stud_sex']==1?'男':'女').',';
    $Str.=$res->fields['stud_birthday'].',';
    $Str.=$res->fields['stud_person_id'].',';
    $Str.=$res->fields['stud_addr_2'].',';
	$Str.=$res->fields['stud_tel_1'].',';
	$Str.=$res->fields['stud_tel_2']."\r\n";
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
