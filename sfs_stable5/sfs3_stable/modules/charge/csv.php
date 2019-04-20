<?php

include "config.php";
sfs_check();

if($_POST[item_selected]) header("Location:".$_POST[item_selected]);


//秀出網頁
head("收費管理CSV輸出");

//橫向選單標籤
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

$suported_item=array('pay_csv.php'=>'台灣銀行','pay_csv_2.php'=>'中國信託商業銀行','pay_csv_3.php'=>'台中商業銀行','pay_csv_4.php'=>'玉山銀行');

$item_select="<BR><BR><center><form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><B>◎選擇代收金融機構◎</B><BR><BR>";
foreach($suported_item as $key=>$value)
{
	$item_select.="<input type='radio' value='$key' name='item_selected' onclick='this.form.submit();'>$value<BR><BR>";
}
$item_select.="</form></center>";
echo $item_select;

foot();
?>
