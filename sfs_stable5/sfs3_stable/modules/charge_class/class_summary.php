<?php

//$Id: class_summary.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "my_fun.php";

sfs_check();
//秀出網頁
head("收費管理(導師版)");

//學期別

$work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$item_id=$_REQUEST[item_id];
$record_id=$_POST[record_id];

// 取出班級名稱陣列
$class_base = class_base($work_year_seme);
//橫向選單標籤
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

if($m_arr[is_class_summary] AND $class_id) {

$main="<table><form name='form_item' method='post' action='$_SERVER[PHP_SELF]'>
	<select name='item_id' onchange='this.form.submit()'><option></option>";

//取得年度項目
$sql_select="select * from charge_item where cooperate=1 AND year_seme='$work_year_seme' AND (curdate() between start_date AND end_date) order by end_date desc";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

while(!$res->EOF) {
	$main.="<option ".($item_id==$res->fields[item_id]?"selected":"")." value=".$res->fields[item_id].">".$res->fields[item]."(".$res->fields[start_date]."~".$res->fields[end_date].")</option>";
	$res->MoveNext();
}
$main.="</select>";
if($item_id>0)
{
	//取得已開列班級學生列表
	$stud_select="select * from charge_record where item_id=$item_id order by record_id";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);

	//取得各年級收費列表及計算減免金額  自定函數在 my-fun.php
	$grade_dollars=get_grade_charge($item_id);
	$decrease_dollars=get_charge_decrease($item_id);
	$class_summary=array();
	while(!$recordSet->EOF) {
		$grade=substr($recordSet->fields[record_id],4,1);
		$res_class_id=substr($recordSet->fields[record_id],4,3);
		$item_total=array_sum($grade_dollars[$grade]); //年級學生項目金額
		$my_decrease=$decrease_dollars[$recordSet->fields[student_sn]][total];
		$my_should_paid=$item_total-$my_decrease;
		$left=$my_should_paid-($recordSet->fields[dollars]);
		
		$class_summary[$res_class_id][members]+=1;
		$class_summary[$res_class_id][total]+=$item_total;
		$class_summary[$res_class_id][decrease]+=$my_decrease;
		$class_summary[$res_class_id][should_paid]+=$my_should_paid;
		$class_summary[$res_class_id][dollars]+=$recordSet->fields[dollars];
		$class_summary[$res_class_id][left]+=$left;
		if($recordSet->fields[dollars]==0) $class_summary[$res_class_id][zero]+=1; 
			elseif($left==0) $class_summary[$res_class_id][cleared]+=1;
			elseif($left<0) $class_summary[$res_class_id][over]+=1;
			else $class_summary[$res_class_id][others]+=1;

			$recordSet->MoveNext();
		}
}
	//計算加總
foreach($class_summary as $res_class_id=>$class_data)
{
	foreach($class_data as $key=>$value)
	{
		$class_summary['TOTAL'][$key]+=$class_summary[$res_class_id][$key];
	}
}

//將陣列轉表格列示
	//顯示班級學生的收費統計
	$showdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
		<tr bgcolor='#CCFF99'>
		<td align='center'>NO.</td>
		<td align='center'>班級</td>
		<td align='center'>項目<br>金額</td>
		<td align='center'>應收<br>人數</td>
		<td align='center'>項目<br>總額</td>
		<td align='center'>減免<br>總額</td>
		<td align='center'>應收<br>總額</td>
		<td align='center'>已收<br>總額</td>
		<td align='center'>待收<br>總額</td>
		<td align='center'>繳清<br>人次</td>
		<td align='center'>未繳<br>人次</td>
		<td align='center'>溢繳<br>人次</td>
		<td align='center'>其他<br>人次</td>
		</tr>";
	$counter=0;
	foreach($class_summary as $key=>$value)
	{
		$counter+=1;
		$showdata.="<tr bgcolor=".($key==$class_id?"#FFDDDD":"#FDFDDF")."><td align='center'>$counter</td>";
		$showdata.="<td align='center'>".($class_base[$key]?$class_base[$key]:"<合　計>")."</td>";
		$showdata.="<td align='center'>".($class_base[$key]?($value[total]/$value[members]):"---")."</td>";
		$showdata.="<td align='center'>".$value[members]."</td>";
		$showdata.="<td align='center'>".$value[total]."</td>";
		$showdata.="<td align='center'>".$value[decrease]."</td>";
		$showdata.="<td align='center'>".$value[should_paid]."</td>";
		$showdata.="<td align='center'>".$value[dollars]."</td>";
		$showdata.="<td align='center'>".$value[left]."</td>";
		$showdata.="<td align='center'>".$value[cleared]."</td>";
		$showdata.="<td align='center'>".$value[zero]."</td>";
		$showdata.="<td align='center'>".$value[over]."</td>";
		$showdata.="<td align='center'>".$value[others]."</td>";
		$showdata.="</tr>";
	}

$showdata.="</form></table>";

echo $main.$showdata;
} else echo $not_allowed;
foot();
?>