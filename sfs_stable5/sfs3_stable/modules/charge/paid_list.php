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
$class_base = class_base($work_year_seme);

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

	//取得所有學生的收費資料陣列
	$all_datas=get_item_all_stud_list($item_id);	

	//取得收費細目列表
	$detail_list=get_item_detail_list($item_id);


	//將陣列轉表格列示
	$showdata="<table style='font-size:10pt;' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
		<tr bgcolor='#CCFF99'>
		<td align='center'>NO.</td>
		<td align='center'>班級</td>
		<td align='center'>座號</td>
		<td align='center'>姓名</td>
		<td align='center'>收費單號碼</td>
		<td align='center'>繳款金額</td>
		<td align='center'>繳費日期</td>";
	foreach($detail_list as $key=>$value) $showdata.="<td align='center'>$value</td>";
	$showdata.="</tr>";
	
	//取得已"繳費"班級學生列表
	$stud_select="select a.*,b.stud_name,right(b.curr_class_num,2) as class_num from charge_record a left join stud_base b on a.student_sn=b.student_sn where item_id=$item_id AND dollars>0 order by a.record_id";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	
	$detail_summary=array();
	$error_sn="";
	$counter=0;
	while(!$recordSet->EOF) {
		$student_sn=$recordSet->fields[student_sn];
		$paid_date=$recordSet->fields[paid_date];
		$grade=substr($recordSet->fields[record_id],4,1);
		$class_id=substr($recordSet->fields[record_id],4,3);
		$my_should_paid=$all_datas[$student_sn][total];
		
		//只統計繳費金額與應收金額MATCH者
		if($my_should_paid==$recordSet->fields[dollars]) {
			//人員清冊
			$counter++;
			$showdata.="<tr>
			<td align='center'>$counter</td>
			<td align='center'>".$class_base[$class_id]."</td>
			<td align='center'>".$recordSet->fields[class_num]."</td>
			<td align='center'>".$recordSet->fields[stud_name]."</td>
			<td align='center'>".$recordSet->fields[record_id]."</td>
			<td align='center'>".$recordSet->fields[dollars]."</td>
			<td align='center' bgcolor='#FFFFCC'>".$recordSet->fields[paid_date]."</td>";
			//列示各收費項目
			foreach($detail_list as $key=>$value){
				$detail_original=$all_datas[$student_sn][detail][$value][original];
				$detail_decrease=$all_datas[$student_sn][detail][$value][decrease_dollars];
				if($detail_decrease) $detail_bgcolor='bgcolor=#FFCCCC'; else $detail_bgcolor='';
				$detail_paid=$detail_original-$detail_decrease;
				$showdata.="<td align='center' $detail_bgcolor>$detail_paid</td>";
			}			
			$showdata.="</tr>";	
		} else {
			$sql_select="select curr_class_num,stud_name from stud_base where student_sn=$student_sn";
			$rs=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
			$error_sn.="<li>".$recordSet->fields[paid_date]."--".$rs->fields[curr_class_num].$rs->fields[stud_name]."( 應收：$my_should_paid  收款登錄： ".$recordSet->fields[dollars]." )</li>";
		
		}
		$recordSet->MoveNext();
	}
	if($error_sn) $error_sn="<BR><font size=2 color='red'>下面列表的學生，因為應繳金額與收款金額不一致，並未被列入統計中，請檢查！<BR> $error_sn </font>";
}		
//echo "<PRE>";
//print_r($detail_summary);
//echo "</PRE>";	


$showdata.="</form></table>";
echo $main.$error_sn.$showdata;
foot();

?>