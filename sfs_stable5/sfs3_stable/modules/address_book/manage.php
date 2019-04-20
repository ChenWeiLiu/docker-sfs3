<?php

// $Id: $

/*引入學務系統設定檔*/
include "config.php";

//使用者認證
sfs_check();

if($_POST['go']=='新增')
{
	//避免sql injection
	$_POST['field_selected']=str_replace(';','',$_POST['field_selected']);
	$_POST['field_selected']=str_replace('delete','',$_POST['field_selected']);
	$_POST['field_selected']=str_replace('DELETE','',$_POST['field_selected']);
	$_POST['field_selected']=str_replace('drop','',$_POST['field_selected']);
	$_POST['field_selected']=str_replace('DROP','',$_POST['field_selected']);
	
	$_POST['new_format_name']=str_replace(';','',$_POST['new_format_name']);
	$_POST['new_format_name']=str_replace('delete','',$_POST['new_format_name']);
	$_POST['new_format_name']=str_replace('DELETE','',$_POST['new_format_name']);
	$_POST['new_format_name']=str_replace('drop','',$_POST['new_format_name']);
	$_POST['new_format_name']=str_replace('DROP','',$_POST['new_format_name']);
	
	$_POST['columns']=trim($_POST['columns']);
	$sql="INSERT INTO address_book SET nature='{$_POST['nature']}',room='{$_POST['room']}',title='{$_POST['new_format_name']}',fields='{$_POST['field_selected']}',header='{$_POST['header']}',footer='{$_POST['footer']}',columns='{$_POST['columns']}',creater='$my_name',update_time=now();";
	$rs=$CONN->Execute($sql) or die("無法新增新的格式<br>$sql");
}

if($_POST['go']=='確定修改')
{
	//避免sql injection
	$_POST['field_selected']=str_replace(';','',$_POST['field_selected']);
	$_POST['field_selected']=str_replace('delete','',$_POST['field_selected']);
	$_POST['field_selected']=str_replace('DELETE','',$_POST['field_selected']);
	$_POST['field_selected']=str_replace('drop','',$_POST['field_selected']);
	$_POST['field_selected']=str_replace('DROP','',$_POST['field_selected']);
	
	$_POST['new_format_name']=str_replace(';','',$_POST['new_format_name']);
	$_POST['new_format_name']=str_replace('delete','',$_POST['new_format_name']);
	$_POST['new_format_name']=str_replace('DELETE','',$_POST['new_format_name']);
	$_POST['new_format_name']=str_replace('drop','',$_POST['new_format_name']);
	$_POST['new_format_name']=str_replace('DROP','',$_POST['new_format_name']);
	
	$sql="UPDATE address_book SET nature='{$_POST['nature']}',room='{$_POST['room']}',title='{$_POST['new_format_name']}',fields='{$_POST['field_selected']}',header='{$_POST['header']}',footer='{$_POST['footer']}',columns='{$_POST['columns']}',creater='$my_name',update_time=now() WHERE sn={$_POST['target_sn']};";
	$rs=$CONN->Execute($sql) or die("無法修改原的格式<br>$sql");
}

if($_POST['target_sn'] and $_POST['act']=='del')
{
	//echo "進入　刪除　處理程序!!   {$_POST['target_sn']}";
	$sql="DELETE FROM address_book WHERE sn={$_POST['target_sn']};";
	$rs=$CONN->Execute($sql) or die("無法刪除指定的樣式!<br>$sql");
}

//秀出網頁
head("通訊錄樣式管理");
print_menu($menu_p);

//產生選項
foreach($fields_array as $key=>$value)
{
	$field_radio.="<input type='radio' value='$key' name='field_radio' onclick='this.form.field_selected.value=this.form.field_selected.value+\"$value,\";'>$value ";
}
if($_POST['target_sn'] and $_POST['act']=='modify')
{
	$sql="SELECT * FROM address_book WHERE sn={$_POST['target_sn']}";
	$rs=$CONN->Execute($sql) or die("無法取得已經開列的樣式資料!<br>$sql");

	$new_format="<table STYLE='font-size: x-small' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr><td bgcolor='#88FFFF' rowspan=3><font size=4> 修 <br> 改 <br> 原 <br> 格 <br> 式 </font></td><td bgcolor='#CCFCCF'>$field_radio</td></tr>
			<tr><td bgcolor='#FFFFCC'>			
			◎您所選取的欄位：<input type='text' size=100 maxlength=200 name='field_selected' value='{$rs->fields['fields']}'>
			<input type='button' name='clear' value='清除重設' onclick=\"document.myform.field_selected.value='';\" >
			<br>◎頁首宣告：<input type='text' size=117 maxlength=200 name='header' value='{$rs->fields['header']}'>
			<br>◎頁尾說明：<input type='text' size=117 maxlength=200 name='footer' value='{$rs->fields['footer']}'>
			<br>◎適用處室：<font color='blue'>$my_room</font><input type='hidden'name='room' value='$my_room'>　　
			◎欄數：<input type='text' name='columns' size=1 maxlength=1 value='{$rs->fields['columns']}'>　　
			◎將上列的欄位列表儲存為：<input type='text' size=50 name='new_format_name' value='{$rs->fields['title']}'>
			<input type='submit' name='go' value='確定修改' onclick='return confirm(\"真的要修改?\")'></td></tr>
			</table>";
} else {
	$new_format="<table STYLE='font-size: x-small' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr><td bgcolor='#FFCCCC' rowspan=3> 創 <br> 建 <br> 新 <br> 格 <br> 式 </td><td bgcolor='#CCFCCF'>$field_radio</td></tr>
			<tr><td bgcolor='#FFFFCC'>
			◎您所選取的欄位：<input type='text' size=100 maxlength=200 name='field_selected' value=''>
			<input type='button' name='clear' value='清除重設' onclick=\"document.myform.field_selected.value='';\" >
			<br>◎頁首宣告：<input type='text' size=117 maxlength=200 name='header' value='{$rs->fields['header']}'>
			<br>◎頁尾說明：<input type='text' size=117 maxlength=200 name='footer' value='{$rs->fields['footer']}'>
			<br>◎適用處室：<font color='blue'>$my_room</font><input type='hidden'name='room' value='$my_room'>　　
			◎將上列的欄位列表儲存為：<input type='text' size=50 name='new_format_name' value=''>
			<input type='submit' name='go' value='新增' onclick='return confirm(\"真的要新增?\")'></td></tr></table>";
}  //◎欄數：<input type='text' name='columns' size=1 maxlength=1 value='1'>　　
			
//抓取已經開列的樣式資料
$saved_format="<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'><tr bgcolor='#CCFCCF'><td align='center'>標題</td><td align='center' width='40%'>欄位列表</td><td align='center'>欄數</td><td align='center'>設定者</td><td align='center'>更新日期</td><td align='center'>維護<input type='hidden' name='target_sn' value='{$_POST['target_sn']}'><input type='hidden' name='act' value=''></td></tr>";
$sql="select * from address_book where room='$my_room' and nature='$nature' order by update_time desc;";
$rs=$CONN->Execute($sql) or die("無法取得已經開列的樣式資料!<br>$sql");
while(!$rs->EOF) {
	$target_sn=$rs->fields['sn'];
	$saved_format.="<tr><td align='center'>{$rs->fields['title']}</td><td>{$rs->fields['fields']}</td><td align='center'>{$rs->fields['columns']}</td><td align='center'>{$rs->fields['creater']}</td><td align='center'>{$rs->fields['update_time']}</td><td align='center'>
					<input type='button' value='修改' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"modify\"; this.form.submit();'>
					<input type='button' value='刪除' onclick='if(confirm(\"真的要刪除?\")) { this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"del\"; this.form.submit(); }'></td></tr>";
	$rs->MoveNext();
}
$saved_format.='</table>';

			
echo "<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>$nature_radio<br>$new_format<br>$saved_format</form>";
foot();
?>
