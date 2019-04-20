<?php

// $Id: analysis.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

sfs_check();

//秀出網頁
head("午餐意見調查");

//橫向選單標籤
echo print_menu($MENU_P);

if(checkid($_SERVER['SCRIPT_FILENAME'],1))
{
//統計的日期
$s_date=$_POST[s_date];
if(!$s_date) $s_date=date("Y-m-d");
$e_date=$_POST[e_date];
if(!$e_date) $e_date=date("Y-m-d");

$date_period="日期區間：<input type='text' name='s_date' value='$s_date' size=8>～<input type='text' name='e_date' value='$e_date' size=8><input type='submit' value='列示'>";

//學期別
$work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
//取出班級名稱陣列
$class_base = class_base($work_year_seme);
$unsigned=$class_base;

//群組方式
$group_method=$_POST[group_method];
$method_arr=array('班級'=>'class_id','主副食項目'=>'item','用餐日期'=>'pdate','教師'=>'teacher_sn','填報日期'=>'update_date');
if(!$group_method) $group_method=$method_arr[班級];
$group_method_radio="分析依據：";
foreach($method_arr as $key=>$value)
{
	$group_method_radio.="<input type='radio' value='$value' name='group_method'".($group_method==$value?' checked':'')." onclick='this.form.submit()'>$key ";
}

//開始分析
$analysis_arr=array();
$analysis_items=array('數量滿意度'=>'quantity','色香味滿意度'=>'taste','衛生安全滿意度'=>'hygiene');
foreach($analysis_items as $key=>$value)
{
	$field_show_title.="<td align='center'>$key</td>";
	
	//開始查詢
	$sql="select $group_method,$value,count(*) as counter from lunch_feedback where (pDate between '$s_date' AND '$e_date') GROUP BY $group_method,$value";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);

	while(!$res->EOF)
	{
		$analysis_arr[$res->fields[$group_method]][$key][$res->fields[$value]]+=$res->fields[counter];
		$res->MoveNext();
	}

}
//將結果陣列排序後顯示

//echo "<pre>";
//print_r($analysis_arr);
//echo "</pre>";
$title='<h2><center>['.$s_date.($s_date==$e_date?'':']~['.$e_date).']'.array_search($group_method,$method_arr).'滿意度分析</center></h2>';
$main="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'><form name='form_day' method='post' action='$_SERVER[PHP_SELF]'>
	$date_period 　 $group_method_radio<BR><BR>$title";

$showdata="<tr bgcolor='#CCFF99'>
	<td align='center'>分析群組項目</td>$field_show_title<td align='center'>備註</td></tr>";
	foreach($analysis_arr as $analysis_item=>$satisfy_items)
	{
		if ($group_method=='class_id') { $analysis_item=$class_base[$analysis_item]; }
		if ($group_method=='teacher_sn')
		{
			//取出教師陣列
			$teacher_base=teacher_base();
			$analysis_item=$teacher_base[$analysis_item];
		}
		$showdata.="<tr bgcolor=#FFFFDD><td>$analysis_item</td>";
		foreach($satisfy_items as $satisfy_item)
		{			
			$showdata.="<td>";
			foreach($satisfy_item as $key=>$value)
			{
				$showdata.="$key($value) ";
			}
		}
		$showdata.="</td><td width='20%'></td></tr>";
	}

$showdata.="</form></table>";

echo $main.$showdata;


} else echo "您並非模組管理者, 無法觀視統計資訊!!";

foot();

?>