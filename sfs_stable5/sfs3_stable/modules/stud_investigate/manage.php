<?php

// $Id: $

/*�ޤJ�ǰȨt�γ]�w��*/
include "config.php";

//�ϥΪ̻{��
sfs_check();

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
*/

$_POST['title']=$_POST['title']?$_POST['title']: 'X���R�W���� '.date('Y-m-d H:i:s');

if($_POST['go']=='�s�W')
{
$sql="INSERT INTO investigate SET room='$my_room',title='{$_POST['title']}',fields='{$_POST['fields']}',selections='{$_POST['selections']}',memo='{$_POST['memo']}',visible='{$_POST['visible']}',start='{$_POST['start']}',end='{$_POST['end']}',update_name='$my_title$my_name';";
	$rs=$CONN->Execute($sql) or die("�L�k�s�W�s������<br>$sql");
}

if($_POST['go']=='�T�w�ק�')
{
	$sql="UPDATE investigate SET room='$my_room',title='{$_POST['title']}',fields='{$_POST['fields']}',selections='{$_POST['selections']}',memo='{$_POST['memo']}',visible='{$_POST['visible']}',start='{$_POST['start']}',end='{$_POST['end']}',update_name='$my_title$my_name' WHERE sn={$_POST['target_sn']};";
	$rs=$CONN->Execute($sql) or die("�L�k�ק�쪺����<br>$sql");
}

if($_POST['target_sn'] and $_POST['act']=='del')
{
	//echo "�i�J�@�R���@�B�z�{��!!   {$_POST['target_sn']}";
	$sql="DELETE FROM investigate WHERE sn={$_POST['target_sn']} AND room='$my_room';";
	$rs=$CONN->Execute($sql) or die("�L�k�R�����w������!<br>$sql");
}

//�q�X����
head("�լd���غ޲z");
print_menu($MENU_P);

 
if($_POST['target_sn'] and $_POST['act']=='modify')
{
	$sql="SELECT * FROM investigate WHERE sn={$_POST['target_sn']}";
	$rs=$CONN->Execute($sql) or die("�L�k���o�w�g�}�C�����ظ��!<br>$sql");

	$visible_array=array('Y'=>'�O','N'=>'�_');
	foreach($visible_array as $key=>$value)
	{
		$checked=($key==$rs->fields['visible'])?'checked':'';
		$visible_radio.="<input type='radio' value='$key' name='visible' $checked>$value";
	}
	
	$new_format="<table STYLE='font-size: x-small' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr align='center' bgcolor='#CCFFCC'><td>�լd���</td><td>���ﶵ</td><td width='30%'>�Ƶ�</td><td>�ާ@</td></tr>
			<tr>
				<td><textarea name='fields' style='border-width:1px; width:100%;' rows=15>{$rs->fields['fields']}</textarea></td>
				<td><textarea name='selections' style='border-width:1px; width:100%;' rows=15>{$rs->fields['selections']}</textarea></td>
				<td><textarea name='memo' style='border-width:1px; width:100%;' rows=15>{$rs->fields['memo']}</textarea></td>
				<td valign='top'><br>���A�γB�ǡG<font color='blue'>$my_room</font>
					<br><br>���լd���ئW�١G<input type='text' size=50 name='title' value='{$rs->fields['title']}'>
					<br><br>���ɮv�i�ݨ쥻���ءG $visible_radio
					<br><br>���ɮv��������G <input type='text' size=10 name='start' value='{$rs->fields['start']}'> ~ <input type='text' size=10 name='end' value='{$rs->fields['end']}'>
					<br><br><hr>
					<p align='center'>
						<input type='submit' name='go' value='�T�w�ק�' onclick='return confirm(\"�u���n�ק�?\")'>
						<input type='button' name='clear' value='�M�����]' onclick=\"document.myform.field_selected.value='';\" >
					</p>
				</td>
			</tr>
			</table>";
} else {
	$start=date('Y-m-d',strtotime("+1 day"));
	$end=date('Y-m-d',strtotime("+1 week +1 day"));
	$new_format="<table STYLE='font-size: x-small' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr align='center' bgcolor='#FFCCCC'><td>�լd���</td><td>���ﶵ</td><td width='30%'>�Ƶ�</td><td>�ާ@</td></tr>
			<tr>
				<td><textarea name='fields' style='border-width:1px; width:100%;' rows=15></textarea></td>
				<td><textarea name='selections' style='border-width:1px; width:100%;' rows=15></textarea></td>
				<td><textarea name='memo' style='border-width:1px; width:100%;' rows=15></textarea></td>
				<td valign='top'><br>���A�γB�ǡG<font color='blue'>$my_room</font>
					<br><br>���լd���ئW�١G<input type='text' size=50 name='title' value='' placeholder='�O�o�b����J���ئW��'>
					<br><br>���ɮv�i�ݨ쥻���ءG<input type='radio' name='visible' value='Y' checked>�O <input type='radio' name='visible' value='N'>�_
					<br><br>���ɮv��������G <input type='text' size=10 name='start' value='$start'> ~ <input type='text' size=10 name='end' value='$end'>
					<br><br><hr>
					<p align='center'>
						<input type='submit' name='go' value='�s�W' onclick='return confirm(\"�u���n�s�W?\")'>
						<input type='reset' value='�M�����]'>
					</p>
				</td>
			</tr>
			</table>";
				
}  //����ơG<input type='text' name='columns' size=1 maxlength=1 value='1'>�@�@
			
//����w�g�}�C���˦����
$saved_format="<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
				<tr bgcolor='#CCFCCF'>
					<td align='center'>���ئW��</td>
					<td align='center'>�լd���C��</td>
					<td align='center'>�ﶵ</td>
					<td align='center'>�ɮv�i��</td>
					<td align='center'>�������</td>
					<td align='center'>�]�w��</td>
					<td align='center'>��s�ɨ�</td>
					<td align='center'>���@<input type='hidden' name='target_sn' value='{$_POST['target_sn']}'><input type='hidden' name='act' value=''></td>
				</tr>";
$sql="select * from investigate where room='$my_room' order by update_datetime desc;";
$rs=$CONN->Execute($sql) or die("�L�k���o�w�g�}�C�����ظ��!<br>$sql");
while(!$rs->EOF) {
	$target_sn=$rs->fields['sn'];
	$saved_format.="<tr>
					<td align='center'>{$rs->fields['title']}</td><td>{$rs->fields['fields']}</td><td>{$rs->fields['selections']}</td><td align='center'>{$rs->fields['visible']}</td><td align='center'>{$rs->fields['start']} ~ {$rs->fields['end']}</td><td align='center'>{$rs->fields['update_name']}</td><td align='center'>{$rs->fields['update_datetime']}</td><td align='center'>
						<input type='button' value='�ק�' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"modify\"; this.form.submit();'>
						<input type='button' value='�R��' onclick='if(confirm(\"�u���n�R��?\")) { this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"del\"; this.form.submit(); }'>
					</td></tr>";
	$rs->MoveNext();
}
$saved_format.='</table>';

			
echo "<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>$nature_radio<br>$new_format<br>$saved_format</form>";
foot();
?>
