<?php

//$Id: hie.php 5310 2009-01-10 07:57:56Z hami $
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

if($m_arr[is_hie] AND $class_id) {
	
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
if($item_id)
{
		//取得已開列班級學生列表
		$stud_select="select a.*,b.stud_name from charge_record a,stud_base b where item_id=$item_id AND a.student_sn=b.student_sn AND curr_class_num like '$class_id%' order by record_id";
		$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
		$showdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr bgcolor='#CCFF99'>
			<td align='center'>NO.</td>
			<td align='center'>單據號碼</td>
			<td align='center'>班級</td>
			<td align='center'>姓名</td>
			<td align='center'>項目總額</td>
			<td align='center'>減免額</td>
			<td align='center'>應繳額</td>
			<td align='center'>已繳額</td>
			<td align='center'>待繳款</td>
			<td align='center'>繳款日期</td>
			<td align='center'>備註</td>
			</tr>";
		//取得各年級收費列表及計算減免金額  自定函數在 my-fun.php
		$grade_dollars=get_grade_charge($item_id);
		$decrease_dollars=get_charge_decrease($item_id);
		//echo "<PRE>";
		//print_r($grade_dollars);
		//echo "</PRE>";
		
		//echo "<PRE>";
		//print_r($decrease_dollars);
		//echo "</PRE>";
		$counter=0;
		while(!$recordSet->EOF) {
			$grade=substr($recordSet->fields[record_id],4,1);
			$class_id=substr($recordSet->fields[record_id],4,3);
			
			$item_total=array_sum($grade_dollars[$grade]);
			//echo $item_total;

			//echo "=== $grade ==> $class_id <BR>";
			
			$my_decrease=$decrease_dollars[$recordSet->fields[student_sn]][total];
			$my_should_paid=$item_total-$my_decrease;
			$left=$my_should_paid-($recordSet->fields[dollars]);
			if($left<>0){
				$counter+=1;
				if($left>0) $my_bgcolor="#FFFFFF"; else $my_bgcolor="#FFDDDD";
				//列表
				$my_decrease=$decrease_dollars[$recordSet->fields[student_sn]][total];
				
				$showdata.="<tr bgcolor=$my_bgcolor><td align='center'>$counter</td>";
				$showdata.="<td align='center'>".$recordSet->fields[record_id]."</td>";
				$showdata.="<td align='center'>".$class_base[$class_id]."</td>";
				$showdata.="<td align='center'>".$recordSet->fields[stud_name]."</td>";
				$showdata.="<td align='center'>$item_total</td>";
				$showdata.="<td align='center'>$my_decrease</td>";
				$showdata.="<td align='center'>$my_should_paid</td>";			
				$showdata.="<td align='center'>".$recordSet->fields[dollars]."</td>";
				$showdata.="<td align='center'>$left</td>";
				$showdata.="<td align='center'>".$recordSet->fields[paid_date]."</td>";
				$showdata.="<td align='center'>".($left<0?"[溢收]":"").$recordSet->fields[comment]."</td>";
				$showdata.="</td></tr>";
			}
			$recordSet->MoveNext();
		}
	//}
}
$showdata.="</form></table>";

echo $main.$showdata;
} else echo $not_allowed;
foot();
?>