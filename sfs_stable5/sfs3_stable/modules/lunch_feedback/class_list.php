<?php

// $Id: class_list.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

sfs_check();

//秀出網頁
head("午餐意見調查");

//橫向選單標籤
echo print_menu($MENU_P);

if(checkid($_SERVER['SCRIPT_FILENAME'],1))
{
//抓取最近天數
$list_period=$m_arr[list_period];

$sql="select pDate,pMday from lunchtb where TO_DAYS(curdate())-TO_DAYS(pDate) between 0 AND $list_period ORDER BY pDate DESC";
$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
$days_list=$res->GetRows();

$list_date=$_POST[list_date];
if(!$list_date) $list_date=date("Y-m-d");
$list_class=$_POST[list_class];
if(!$list_class) $list_class=$class_id;

//產生日期選單
$date_combo="<select name='list_date' onchange='this.form.submit()'>";
foreach($days_list as $value)
{
	if($list_date==$value[pDate]) $is_this_date='selected'; else $is_this_date='';
	$date_combo.="<option $is_this_date value='$value[pDate]'>".$value[pDate]."(".$c_day[$value[pMday]].")</option>";
}
$date_combo.="</select>";

//學期別
$work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
//取出班級名稱陣列
$class_base = class_base($work_year_seme);
$unsigned=$class_base;

//顯示全年級
$show_grade=$_POST[show_grade];
$show_grade_checkbox="<input type='checkbox' name='show_grade'".($show_grade?' checked':'')." onclick='this.form.submit()'>顯示全年級";
$class_filter=$show_grade?substr($list_class,0,1):$list_class;

//排序方式
$sort_method=$_POST[sort_method];
$method_arr=array('班級'=>'class_id','主副食項目'=>'item,class_id',);
if(!$sort_method) $sort_method=$method_arr[班級];
$sort_method_radio="排序：";
foreach($method_arr as $key=>$value)
{
	$sort_method_radio.="<input type='radio' value='$value' name='sort_method'".($sort_method==$value?' checked':'')." onclick='this.form.submit()'>$key ";
}
//產生班級選單
$class_combo="<select name='list_class' onchange='this.form.submit()'><option></option>";
//列示填報班級
$sql="select DISTINCT class_id from lunch_feedback where pDate='$list_date' ORDER BY class_id";
$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
$signed_class=$res->recordcount();
while(!$res->EOF)
{
	if($list_class==$res->fields[class_id]) $is_this_class='selected'; else	$is_this_class='';
	$class_combo.="<option $is_this_class value='".$res->fields[class_id]."'>".$class_base[$res->fields[class_id]]."</option>";	
	
	//將此班級自未填報名單剔除
	unset($unsigned[$res->fields[class_id]]);

	$res->MoveNext();
}

$class_combo.="</select>";

$unsigned_class="<BR>※已填報的班級數：$signed_class<BR><font color='#FF0000'>※尚未填報的班級(".count($unsigned)."班)：";
	
foreach($unsigned as $key=>$value)
{
	$unsigned_class.=$value."，";
}
$unsigned_class=substr($unsigned_class,0,-2)."。";

$main="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'><form name='form_day' method='post' action='$_SERVER[PHP_SELF]'>
	$date_combo $class_combo 　 $show_grade_checkbox 　　 $sort_method_radio";

$showdata="<tr>
	<td align='center' bgcolor='#CCFF99' rowspan=2>班級</td>
	<td align='center' bgcolor='#CCFF99' rowspan=2>主副食名稱</td>
	<td align='center' bgcolor='#CCFF99' colspan=3>滿意度調查</td>
	<td align='center' bgcolor='#CCFF99' rowspan=2>文字意見</td>
	</tr>
	<tr>
	<td align='center' bgcolor='#CCFF99'>數量</td>
	<td align='center' bgcolor='#CCFF99'>色香味</td>
	<td align='center' bgcolor='#CCFF99'>衛生安全</td></tr>";
	//抓取班級前已填報資料
	$sql="select * from lunch_feedback where pDate='$list_date' and class_id like '$class_filter%' ORDER BY $sort_method";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		if($res->fields[item])
		{
			$showdata.="<tr bgcolor=#FFFFDD><td align='center'>".$class_base[$res->fields[class_id]]."</td>";
			$showdata.="<td align='center'>".$res->fields[item]."</td>";
			$showdata.="<td align='center'>".$res->fields[quantity]."</td>";
			$showdata.="<td align='center'>".$res->fields[taste]."</td>";
			$showdata.="<td align='center'>".$res->fields[hygiene]."</td>";		
			$showdata.="<td>".$res->fields[memo]."</td>";
			$showdata.="</td></tr>";
		}
		$res->MoveNext();
	}
if($m_arr['warning']<>'Y') $unsigned_class='';
	
$showdata.="</form></table>$unsigned_class";

echo $main.$showdata;

} else echo "您並非模組管理者, 無法觀視統計資訊!!";

foot();

?>