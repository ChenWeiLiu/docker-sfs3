<?php
// $Id: check_score_error.php 5310 2009-01-10 07:57:56Z hami $
//載入設定檔
include "stud_check_config.php";

//認證檢查
sfs_check();

if ($_GET[sel]='delete'){
	$query = "delete from stud_seme_score where seme_year_seme='$_GET[seme_year_seme]' and ss_id='$_GET[ss_id]'";
	$CONN->Execute($query);
}
	
head("學籍資料檢查");
print_menu($menu_p);

$query = "select ss_id,year,semester from score_ss where enable=1 ";
$res = $CONN->Execute($query);
while(!$res->EOF){
	$seme_year_seme = sprintf("%03d%d",$res->fields[year],$res->fields[semester]);
	$temp_arr[$seme_year_seme][$res->fields[ss_id]]=1;
	$res->MoveNext();
}
$query = "select seme_year_seme,ss_id from stud_seme_score group by seme_year_seme,ss_id order by ss_id";
$res = $CONN->Execute($query);
echo "<h3>本項作業將檢查學期成績,批次匯入時,由於科目代號錯誤,導致成績資料錯置!!</h3>";
$temp_str ='';
while(!$res->EOF){
	$seme_year_seme = $res->fields[seme_year_seme];
	$ss_id = $res->fields[ss_id];
	if ($temp_arr[$seme_year_seme][$ss_id]<>1){
			$query = "select count(*) from stud_seme_score where seme_year_seme='$seme_year_seme' and ss_id='$ss_id'";
			$res2 = $CONN->Execute($query);
			$cc = $res2->fields[0];
			$temp_str .= "檢查出第 $seme_year_seme 學期錯誤科目代號 -- $ss_id 為錯誤資料,共有 $cc 筆資料,是否刪除? <a href=\"$_SERVER[PHP_SELF]?sel=delete&seme_year_seme=$seme_year_seme&ss_id=$ss_id\">刪除錯誤資料</a><BR>";
			
	}
	$res->MoveNext();
}
if ($temp_str=='')
	echo "<br>未檢查出錯誤成績資料!!";
else
	echo $temp_str;

foot();

?>

