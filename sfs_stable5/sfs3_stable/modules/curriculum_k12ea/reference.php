<?php
// $Id: output_xml.php 6036 2010-08-26 05:39:46Z infodaes $

require "config.php";
require_once "../../include/sfs_case_dataarray.php";

sfs_check();

$this_yeae_seme = $_POST['year_seme']? $_POST['year_seme'] : curr_year().'_'.curr_seme();
$ys = explode('_',$this_yeae_seme);


if($this_yeae_seme) {
	
	//國教署課程對應ARRAY
	$k12ea_category_array = k12ea_category();
	$k12ea_area_array = k12ea_area();
	$k12ea_subject_array = k12ea_subject();
	$k12ea_language_array = k12ea_language();
	
	//抓取科目名稱
	$subject_arr=array();
	$sql="SELECT subject_id,subject_name FROM score_subject WHERE enable=1";
	$res=$CONN->Execute($sql) or user_error("讀取課表科目名稱資料失敗！<br>$sql",256);
	while(!$res->EOF){
		$subject_id=$res->fields['subject_id'];
		$subject_arr[$subject_id]=$res->fields['subject_name'];
		$res->MoveNext();
	}
	
	//抓取有效課程資料
	$n = $res->fields['class_id'] ? $res->fields['class_id'] : '年級課程';
	
	$data = "<tr align='center' bgcolor='#ccffcc'>
		<td rowspan=2>年級</td>
		<td rowspan=2>適用班級</td>
		<td rowspan=2>排序</td>
		<td rowspan=2>領域</td>
		<td rowspan=2>科目</td>
		<td rowspan=2>九年一貫對應</td>
		<td rowspan=2>節數</td>
		<td rowspan=2>加權</td>
		<td colspan=4 bgcolor='#ffeeee'>國教署人力資源網課程對應</td>
		</tr>
		<tr align='center' bgcolor='#ffeeee'>
		<td>類別</td>
		<td>領域</td>
		<td>科目</td>
		<td>語言別</td>
		</tr>";
	$sql="SELECT * FROM score_ss WHERE enable=1 AND year={$ys[0]} AND semester={$ys[1]} ORDER BY class_year,class_id,sort,sub_sort";
	$res=$CONN->Execute($sql) or user_error("讀取課程設定資料失敗！<br>$sql",256);
	while(!$res->EOF) {
		//學校科目名稱
		$scope_id = $res->fields['scope_id'];
		$subject_id = $res->fields['subject_id'];
		$scope=$subject_arr[$scope_id];
		$subject=$subject_arr[$subject_id] ? $subject_arr[$subject_id] : $subject_arr[$scope_id];
		
		$bgcolor = in_array($subject,$k12ea_subject_array) ? '#ffffff' : '#eeeeee';
		
		$data .= "<tr bgcolor='$bgcolor'>
		<td align='center'>{$res->fields['class_year']}</td>
		<td align='center'>$n</td>
		<td align='center'>{$res->fields['sort']}-{$res->fields['sub_sort']}</td>
		<td>$scope</td>
		<td>$subject</td>
		<td>{$res->fields['link_ss']}</td>
		<td align='center'>{$res->fields['sections']}</td>
		<td align='center'>{$res->fields['rate']}</td>		
		<td>{$k12ea_category_array[$res->fields['k12ea_category']]}</td>
		<td>{$k12ea_area_array[$res->fields['k12ea_area']]}</td>
		<td>{$k12ea_subject_array[$res->fields['k12ea_subject']]}</td>
		<td>{$k12ea_language_array[$res->fields['k12ea_language']]}</td>
		</tr>";		
		
		$res->MoveNext();
	}
	
}


head('課程參照總表');
print_menu($menu_p);


//抓取有課表學期，提供選單之用
$sql="SELECT distinct year,semester FROM score_course ORDER BY year desc,semester desc";
$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);

$main.="<form name='myform' method='post'>
		<table border=2 cellpadding=10 cellspacing=0 style='border-collapse: collapse; font-size=12pt;' bordercolor='#ffcfcf' width='100%'>
		<tr align='center' bgcolor='#ffffaa'><td>選擇學期</td><td>對照設定總表</td></tr><tr><td valign='top'>";
while(!$res->EOF) {
	if(curr_year()-$res->fields[year]<5) {
		$year_seme=$res->fields[year].'_'.$res->fields[semester];
		$year_seme_name=$res->fields[year].'學年度第'.$res->fields[semester].'學期';
		//$this_yeae_seme=curr_year().'_'.curr_seme();
		$checked=$this_yeae_seme==$year_seme?'checked':''; 
		$main.="<input type='radio' name='year_seme' value='$year_seme' $checked onclick='this.form.submit();'>$year_seme_name<br>";
	}
	$res->MoveNext();
}


$main.="</td><td valign='top'>
<table border=2 cellpadding=10 cellspacing=0 style='border-collapse: collapse;' width='100%'>$data</table>
</td></tr></table></form>";

echo $main;

foot();


?>