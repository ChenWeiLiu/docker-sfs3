<?php
// $Id: rent_summary.php 6548 2011-09-23 08:08:11Z infodaes $

include "config.php";
sfs_check();
//秀出網頁
head("場地出租管理");
echo print_menu($MENU_P);

$purpose=$_POST[purpose];
//統計項目
$purpose_item=array("年度統計","月份統計","日別統計","上午時段統計","下午時段統計","晚間時段統計","申請者統計","場地統計","類別統計","管理維護費統計","水電補貼費統計","保證金統計","備註統計");

$purpose_select="<select name='purpose' onchange='this.form.submit()'>’";
foreach($purpose_item as $key=>$value)
{
	$is_key=($key==$purpose?'selected':'');
	$purpose_select.="<option $is_key value='$key'>$value</option>";
}
$purpose_select.=".</select>";


//顯示結果
$proved="(NOT ISNULL(prove_id))";
switch ($purpose) {
case 0:
	$sql="SELECT year(rent_date) as year,count(*) FROM rent_record GROUP BY year";
    break;
case 1:
	$sql="SELECT month(rent_date) as month,count(*) FROM rent_record GROUP BY month";
    break;
case 2:
	$sql="SELECT DAY(rent_date) as day,count(*) FROM rent_record GROUP BY day";
    break;
case 3:
	$sql="SELECT '上午',count(*) as count FROM rent_record WHERE morning=true GROUP BY morning";
    break;
case 4:
	$sql="SELECT '下午',count(*) as count FROM rent_record WHERE afternoon=true GROUP BY afternoon";
    break;
case 5:
	$sql="SELECT '晚間',count(*) as count FROM rent_record WHERE evening=true GROUP BY evening";
    break;
case 6:
	$sql="SELECT borrower,count(*) as count FROM rent_record GROUP BY borrower";
    break;
case 7:
	$sql="SELECT rent_place,count(*) as count FROM rent_record GROUP BY rent_place";
	break;
case 8:
	$sql="SELECT borrower_type,count(*) as count FROM rent_record GROUP BY borrower_type";
	break;
case 9:
	$sql="SELECT '管理維護費',sum(rent) as total FROM rent_record";
	break;
case 10:
	$sql="SELECT '水電補貼費',sum(clean) as total FROM rent_record";
	break;
case 11:
	$sql="SELECT '保證金',sum(prove) as total FROM rent_record";
	break;
case 12:
	$sql="SELECT reply,count(*) as count FROM rent_record GROUP BY reply";
    break;
}

$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
//$data_arr=$res->getrows();

//echo "<pre>";
//print_r($data_arr);
//echo "</pre>";


$field_count=$res->FieldCount();
$data="<tr bgcolor='#FFAAAA'><td align='center'>統計項目</td><td align='center'>數額</td></tr>";
while(!$res->EOF)
{
	$data.="<tr>";
		for($i=0;$i<$field_count;$i++)
			$data.="<td align='center'>".$res->fields[$i]."</td>";
	$data.="</tr>";
	$res->MoveNext();
}


$main="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#008DFF' width='70%'><form name='myform' method='post' action='$_SERVER[PHP_SELF]'>";
$main.=$year_seme_select.$purpose_select.$data;

$main.="</form></table>";
echo $main;

foot();
?>