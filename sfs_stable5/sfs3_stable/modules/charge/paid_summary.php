<?php

// $Id:

include "config.php";
include "my_fun.php";
sfs_check();

//秀出網頁
head("收費管理");

//學期別
$work_year_seme=$_REQUEST[work_year_seme];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$item_id=$_REQUEST[item_id];
$record_id=$_POST[record_id];

// 取出班級名稱陣列
//$class_base = class_base($work_year_seme);

//橫向選單標籤

$linkstr="work_year_seme=$work_year_seme&item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$seme_list=get_class_seme();
$main="<table><form name='form_item' method='post' action='$_SERVER[PHP_SELF]'>
	<select name='work_year_seme' onchange='this.form.submit()'>";
foreach($seme_list as $key=>$value){
	$main.="<option ".($key==$work_year_seme?"selected":"")." value=$key>$value</option>";
}
$main.="</select><select name='item_id' onchange='this.form.submit()'><option></option>";

//取得年度項目
$sql_select="select * from charge_item where year_seme='$work_year_seme' order by end_date desc";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
while(!$res->EOF) {
	$main.="<option ".($item_id==$res->fields[item_id]?"selected":"")." value=".$res->fields[item_id].">".$res->fields[item]."(".$res->fields[start_date]."~".$res->fields[end_date].")</option>";
	$res->MoveNext();
}
$main.="</select>";

if($item_id)
{
	//取得各年級收費列表及計算減免金額  自定函數在 my-fun.php
	$m_arr['is_sort']='Y';  //修正陣列索引受變數影響以致無法統計的錯誤
	$grade_dollars=get_grade_charge($item_id);

	//取得減免學生減免列表	
	$decrease_dollars=get_charge_decrease($item_id);
	
	//取得收費細目列表
	$detail_list=get_item_detail_list($item_id);
	
	
	//取得已"繳費"班級學生列表
	$stud_select="select * from charge_record where item_id=$item_id AND dollars>0 order by paid_date";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	
	$detail_summary=array();
	$error_sn="";
	while(!$recordSet->EOF) {
		$student_sn=$recordSet->fields[student_sn];
		$paid_date=$recordSet->fields[paid_date];
		$grade=substr($recordSet->fields[record_id],4,1);
		//$class_id=substr($recordSet->fields[record_id],4,3);
		$item_total=array_sum($grade_dollars[$grade]); //年級學生應繳總額
		$my_decrease=$decrease_dollars[$student_sn][total];
		$my_should_paid=$item_total-$my_decrease;
		//只統計繳費金額與應收金額MATCH者
		if($my_should_paid==$recordSet->fields[dollars]) {
			foreach($grade_dollars[$grade] as $detail_key=>$detail_value) {
				$detail_decrease=$decrease_dollars[$student_sn][$detail_key][dollars];
				$detail_summary[$paid_date][$detail_key]+=$detail_value-$detail_decrease;		
			}
		} else {
			$sql_select="select curr_class_num,stud_name from stud_base where student_sn=$student_sn";
			$rs=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
			$error_sn.="<li>".$recordSet->fields[paid_date]."--".$rs->fields[curr_class_num].$rs->fields[stud_name]."( 應收：$item_total  收款登錄： ".$recordSet->fields[dollars]." )</li>";
		
		}
		$recordSet->MoveNext();
	}
	if($error_sn) $error_sn="<BR><font size=2 color='red'>下面列表的學生，因為應繳金額與收款金額不一致，並未被列入統計中，請檢查！<BR> $error_sn </font>";
}		
//echo "<PRE>";
//print_r($detail_summary);
//echo "</PRE>";	


	//將陣列轉表格列示
	$showdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
		<tr bgcolor='#CCFF99'>
		<td align='center'>日期</td>";
	foreach($detail_list as $key=>$value) $showdata.="<td align='center'>$value</td>";
	$showdata.="<td align='center'>小計</td></tr>";

	foreach($detail_summary as $date_key=>$date_value){
		$date_total=0;
		$showdata.="<tr><td align='center' bgcolor='#FFFFCC'>$date_key</td>";
		foreach($detail_list as $key=>$value){
			$showdata.="<td  align='center'>$date_value[$value]</td>";
			$date_total+=$date_value[$value];
		}
		$showdata.="<td align='center' bgcolor='#FFFFCC'>$date_total</td></tr>";	
	}
$showdata.="</form></table>";
echo $main.$showdata.$error_sn;
foot();

?>