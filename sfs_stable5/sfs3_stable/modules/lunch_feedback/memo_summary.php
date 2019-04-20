<?php

// $Id: memo_summary.php 5310 2009-01-10 07:57:56Z hami $
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

$date_period="日期區間：<input type='text' name='s_date' value='$s_date' size=8>~<input type='text' name='e_date' value='$e_date' size=8><input type='submit' value='列示'>";

//學期別
$work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
//取出班級名稱陣列
$class_base = class_base($work_year_seme);
$unsigned=$class_base;

//排序方式
$sort_method=$_POST[sort_method];
$method_arr=array('班級'=>'class_id','主副食項目'=>'item,class_id','文字意見'=>'memo');
if(!$sort_method) $sort_method=$method_arr[班級];
$sort_method_radio="排序：";
foreach($method_arr as $key=>$value)
{
	$sort_method_radio.="<input type='radio' value='$value' name='sort_method'".($sort_method==$value?' checked':'')." onclick='this.form.submit()'>$key ";
}
$title='<h2><center>['.$s_date.($s_date==$e_date?'':']~['.$e_date).']'.array_search($sort_method,$method_arr).'文字意見匯整</center></h2>';
$main="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'><form name='form_day' method='post' action='$_SERVER[PHP_SELF]'>
	$date_period 　 $sort_method_radio<BR><BR>$title";

$showdata="<tr>
	<td align='center' bgcolor='#CCFF99'>日期</td>	
	<td align='center' bgcolor='#CCFF99'>班級</td>
	<td align='center' bgcolor='#CCFF99'>主副食名稱</td>
	<td align='center' bgcolor='#CCFF99'>文字意見</td>
	<td align='center' bgcolor='#CCFF99'>備註</td>
	</tr>";
	
	//抓取區間已填報文字意見資料
	$sql="select *,WEEKDAY(pdate) as pMday from lunch_feedback where memo<>'' AND (pDate between '$s_date' AND '$e_date') ORDER BY $sort_method";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		if($res->fields[item])
		{
			$showdata.="<tr bgcolor=#FFFFDD><td align='center'>".$res->fields[pdate]."(".$c_day[$res->fields[pMday]+1].")</td>";
			$showdata.="<td align='center'>".$class_base[$res->fields[class_id]]."</td>";
			$showdata.="<td align='center'>".$res->fields[item]."</td>";
			$showdata.="<td width='30%'>".$res->fields[memo]."</td>";
			$showdata.="<td width='20%'></td></td></tr>";
		}
		$res->MoveNext();
	}
	
$showdata.="</form></table>";

echo $main.$showdata;

} else echo "您並非模組管理者, 無法觀視統計資訊!!";

foot();

?>