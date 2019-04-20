<?php
// $Id: detail.php 6114 2010-09-09 08:19:54Z infodaes $

include "config.php";

sfs_check();

//秀出網頁
head("收費管理");


//學期別
$work_year_seme=$_REQUEST[work_year_seme];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());

$item_id=$_REQUEST[item_id];
$detail_id=$_REQUEST[detail_id];

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

// $_SESSION[session_tea_name]  取得教師姓名


if($_POST['act']=='新增'){
	$sql_select="INSERT INTO charge_detail(item_id,detail_type,detail_sort,detail,dollars) values ('$item_id','$_POST[a_detail_type]','$_POST[a_detail_sort]','$_POST[a_detail]','$_POST[a_dollars]')";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
};

if($_POST['act']=='修改'){
	$sql_select="update charge_detail set item_id=$item_id,detail_type='$_POST[detail_type]',detail_sort='$_POST[detail_sort]',detail='$_POST[detail]',dollars='$_POST[dollars]' where detail_id=$detail_id;";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$detail_id=0;
};

if($_POST['act']=='刪除'){
	$sql_select="delete from charge_detail where detail_id=$detail_id";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
};



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

//顯示指定項目詳細資料
$sql_select="select * from charge_detail where item_id='$item_id' order by detail_type,detail_sort";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

$main.="　<img border=0 src='images/modify.gif' alt='編修選定細目'><select name='detail_id' onchange='this.form.submit()'><option></option>";
while(!$res->EOF) {
	$main.="<option ".($detail_id==$res->fields[detail_id]?"selected":"")." value=".$res->fields[detail_id].">".$res->fields[detail]."</option>";
	$res->MoveNext();
}
$main.="</select></table>";


$showdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
	<tr>
	<td align='center' bgcolor='#CCFF99' size=5>NO.</td>
	<td align='center' bgcolor='#CCFF99' size=5>收歸帳戶</td>
	<td align='center' bgcolor='#CCFF99' size=5>排序</td>	
	<td align='center' bgcolor='#CCFF99' size=20>細目名稱</td>
	<td align='center' bgcolor='#CCFF99' size=30>應收金額(請以,分隔各年級)</td>
	<td align='center' bgcolor='#CCFF99'>功能操作</td>
	</tr>";
$res->MoveFirst();
while(!$res->EOF) {
	$detail_type=$res->fields[detail_type]?$res->fields[detail_type]:0;
	$detail_type_radio='';
	foreach($detail_types as $key=>$value)
	{
		$detail_type_radio.="<input type='radio' value='$key' name='detail_type'".(($key==$detail_type)?' checked':'').">$value ";	
		$a_detail_type_radio.="<input type='radio' value='$key' name='a_detail_type'>$value ";
	}

	if($detail_id==$res->fields[detail_id]){
		$showdata.="<tr bgcolor=#AAFFCC><td align='center'>".($res->CurrentRow()+1)."</td>";
		$showdata.="<td align='center'>$detail_type_radio</td>";
		$showdata.="<td align='center'><input type='text' name='detail_sort' value='".$res->fields[detail_sort]."' size=5></td>";
		$showdata.="<td align='center'><input type='text' name='detail' value='".$res->fields[detail]."' size=30></td>";
		$showdata.="<td align='center'><input type='text' name='dollars' value='".$res->fields[dollars]."' size=30></td>";
		$showdata.="<td align='center'><input type='submit' value='修改' name='act' onclick='return confirm(\"確定要更改[".$res->fields[detail]."]?\")'>　<input type='submit' value='刪除' name='act' onclick='return confirm(\"真的要刪除[".$res->fields[detail]."]?\")'></td></tr>";
	} else {	
		$showdata.="<tr bgcolor=#FFFFDD><td align='center'>".($res->CurrentRow()+1)."</td>";
		$detail_type=$detail_types[$detail_type]; 
		$showdata.="<td align='center'>".$detail_type."</td>";
		$showdata.="<td align='center'>".$res->fields[detail_sort]."</td>";
		$showdata.="<td>".$res->fields[detail]."</td>";
		$showdata.="<td align='center'>".$res->fields[dollars]."</td>";
		$showdata.="<td></td>";

		//功能連結
		//$showdata.="<td align='center'>";
		//$showdata.="<a href='detail.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/modify.gif' alt='設定細目'> </a>";
		//$showdata.="<a href='record.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/sxw.gif' alt='印收費單'> </a>";
		//$showdata.="<a href='statistics.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/sigma.gif' alt='收費統計'> </a>";
		//$showdata.="<a href='item.php?act=delete&item_id=".$res->fields[item_id]."'><img border=0 src='images/delete.gif' alt='刪除' onclick='return confirm(\"真的要刪除 $stud_name ?\")'></a>";
		$showdata.="</td></tr>";
	}
	$res->MoveNext();
}
if($work_year_seme==$curr_year_seme and $item_id>0)
{
	//新增細目
	$a_detail_type_radio='';
	foreach($detail_types as $key=>$value)
	{
		$a_detail_type_radio.="<input type='radio' value='$key' name='a_detail_type'>$value ";
	}
	$showdata.="<tr></tr><tr bgcolor='#FFCCCC' height=60><td align='center'><img border=0 src='images/add.gif' alt='開列新項目'></td>";
	$showdata.="<td align='center'>$a_detail_type_radio</td>";
	$showdata.="<td align='center'><input type='text' name='a_detail_sort' size=5></td>";
	$showdata.="<td align='center'><input type='text' name='a_detail' size=20></td>";
	$showdata.="<td align='center'><input type='text' name='a_dollars' size=24></td>";
	$showdata.="<td align='center'><input type='submit' value='新增' name='act'><input type='reset' value='重新設定'></td></tr><tr></tr>";
}

$showdata.="</form></table>";

echo $main.$showdata;


foot();
?>