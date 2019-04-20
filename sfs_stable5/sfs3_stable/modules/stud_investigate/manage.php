<?php

// $Id: $

/*引入學務系統設定檔*/
include "config.php";

//使用者認證
sfs_check();

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
*/

$_POST['title']=$_POST['title']?$_POST['title']: 'X未命名項目 '.date('Y-m-d H:i:s');

if($_POST['go']=='新增')
{
$sql="INSERT INTO investigate SET room='$my_room',title='{$_POST['title']}',fields='{$_POST['fields']}',selections='{$_POST['selections']}',memo='{$_POST['memo']}',visible='{$_POST['visible']}',start='{$_POST['start']}',end='{$_POST['end']}',update_name='$my_title$my_name';";
	$rs=$CONN->Execute($sql) or die("無法新增新的項目<br>$sql");
}

if($_POST['go']=='確定修改')
{
	$sql="UPDATE investigate SET room='$my_room',title='{$_POST['title']}',fields='{$_POST['fields']}',selections='{$_POST['selections']}',memo='{$_POST['memo']}',visible='{$_POST['visible']}',start='{$_POST['start']}',end='{$_POST['end']}',update_name='$my_title$my_name' WHERE sn={$_POST['target_sn']};";
	$rs=$CONN->Execute($sql) or die("無法修改原的項目<br>$sql");
}

if($_POST['target_sn'] and $_POST['act']=='del')
{
	//echo "進入　刪除　處理程序!!   {$_POST['target_sn']}";
	$sql="DELETE FROM investigate WHERE sn={$_POST['target_sn']} AND room='$my_room';";
	$rs=$CONN->Execute($sql) or die("無法刪除指定的項目!<br>$sql");
}

//秀出網頁
head("調查項目管理");
print_menu($MENU_P);

 
if($_POST['target_sn'] and $_POST['act']=='modify')
{
	$sql="SELECT * FROM investigate WHERE sn={$_POST['target_sn']}";
	$rs=$CONN->Execute($sql) or die("無法取得已經開列的項目資料!<br>$sql");

	$visible_array=array('Y'=>'是','N'=>'否');
	foreach($visible_array as $key=>$value)
	{
		$checked=($key==$rs->fields['visible'])?'checked':'';
		$visible_radio.="<input type='radio' value='$key' name='visible' $checked>$value";
	}
	
	$new_format="<table STYLE='font-size: x-small' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr align='center' bgcolor='#CCFFCC'><td>調查欄位</td><td>欄位選項</td><td width='30%'>備註</td><td>操作</td></tr>
			<tr>
				<td><textarea name='fields' style='border-width:1px; width:100%;' rows=15>{$rs->fields['fields']}</textarea></td>
				<td><textarea name='selections' style='border-width:1px; width:100%;' rows=15>{$rs->fields['selections']}</textarea></td>
				<td><textarea name='memo' style='border-width:1px; width:100%;' rows=15>{$rs->fields['memo']}</textarea></td>
				<td valign='top'><br>◎適用處室：<font color='blue'>$my_room</font>
					<br><br>◎調查項目名稱：<input type='text' size=50 name='title' value='{$rs->fields['title']}'>
					<br><br>◎導師可看到本項目： $visible_radio
					<br><br>◎導師填報期間： <input type='text' size=10 name='start' value='{$rs->fields['start']}'> ~ <input type='text' size=10 name='end' value='{$rs->fields['end']}'>
					<br><br><hr>
					<p align='center'>
						<input type='submit' name='go' value='確定修改' onclick='return confirm(\"真的要修改?\")'>
						<input type='button' name='clear' value='清除重設' onclick=\"document.myform.field_selected.value='';\" >
					</p>
				</td>
			</tr>
			</table>";
} else {
	$start=date('Y-m-d',strtotime("+1 day"));
	$end=date('Y-m-d',strtotime("+1 week +1 day"));
	$new_format="<table STYLE='font-size: x-small' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr align='center' bgcolor='#FFCCCC'><td>調查欄位</td><td>欄位選項</td><td width='30%'>備註</td><td>操作</td></tr>
			<tr>
				<td><textarea name='fields' style='border-width:1px; width:100%;' rows=15></textarea></td>
				<td><textarea name='selections' style='border-width:1px; width:100%;' rows=15></textarea></td>
				<td><textarea name='memo' style='border-width:1px; width:100%;' rows=15></textarea></td>
				<td valign='top'><br>◎適用處室：<font color='blue'>$my_room</font>
					<br><br>◎調查項目名稱：<input type='text' size=50 name='title' value='' placeholder='記得在此輸入項目名稱'>
					<br><br>◎導師可看到本項目：<input type='radio' name='visible' value='Y' checked>是 <input type='radio' name='visible' value='N'>否
					<br><br>◎導師填報期間： <input type='text' size=10 name='start' value='$start'> ~ <input type='text' size=10 name='end' value='$end'>
					<br><br><hr>
					<p align='center'>
						<input type='submit' name='go' value='新增' onclick='return confirm(\"真的要新增?\")'>
						<input type='reset' value='清除重設'>
					</p>
				</td>
			</tr>
			</table>";
				
}  //◎欄數：<input type='text' name='columns' size=1 maxlength=1 value='1'>　　
			
//抓取已經開列的樣式資料
$saved_format="<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
				<tr bgcolor='#CCFCCF'>
					<td align='center'>項目名稱</td>
					<td align='center'>調查欄位列表</td>
					<td align='center'>選項</td>
					<td align='center'>導師可視</td>
					<td align='center'>填報期間</td>
					<td align='center'>設定者</td>
					<td align='center'>更新時刻</td>
					<td align='center'>維護<input type='hidden' name='target_sn' value='{$_POST['target_sn']}'><input type='hidden' name='act' value=''></td>
				</tr>";
$sql="select * from investigate where room='$my_room' order by update_datetime desc;";
$rs=$CONN->Execute($sql) or die("無法取得已經開列的項目資料!<br>$sql");
while(!$rs->EOF) {
	$target_sn=$rs->fields['sn'];
	$saved_format.="<tr>
					<td align='center'>{$rs->fields['title']}</td><td>{$rs->fields['fields']}</td><td>{$rs->fields['selections']}</td><td align='center'>{$rs->fields['visible']}</td><td align='center'>{$rs->fields['start']} ~ {$rs->fields['end']}</td><td align='center'>{$rs->fields['update_name']}</td><td align='center'>{$rs->fields['update_datetime']}</td><td align='center'>
						<input type='button' value='修改' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"modify\"; this.form.submit();'>
						<input type='button' value='刪除' onclick='if(confirm(\"真的要刪除?\")) { this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"del\"; this.form.submit(); }'>
					</td></tr>";
	$rs->MoveNext();
}
$saved_format.='</table>';

			
echo "<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>$nature_radio<br>$new_format<br>$saved_format</form>";
foot();
?>
