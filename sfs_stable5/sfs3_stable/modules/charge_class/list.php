<?php

// $Id: list.php 9240 2018-05-04 03:25:04Z igogo $
include "config.php";
include "my_fun.php";

sfs_check();

//�q�X����
head("���O�޲z(�ɮv��)");
//��V������
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

if($m_arr[is_list] AND $class_id) {
	
echo <<<HERE
<script>
function tagall(status) {
  var i =0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='selected_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

//�Ǵ��O
$work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$item_id=$_REQUEST[item_id];
$stud_class=$_POST[stud_class];
$selected_stud=$_POST[selected_stud];
//print_r($selected_stud);
//���o�ثe�Z��id
$grade+=$class_data[2];
//���X�Z�ŦW�ٰ}�C
$class_base = class_base($work_year_seme);

if($selected_stud AND $_POST['act']=='�}�C��ܪ��ǥ�'){
	if($item_id<>'' AND $class_id<>'')
	{
		//�����ܪ��Z�žǥ�
		$batch_value="";
		foreach($selected_stud as $stud_datas)
		{
			$stud_data=explode(',',$stud_datas);
			$sn=$stud_data[0];
			$record_id=$work_year_seme.$stud_data[1];
			$batch_value.="('$record_id',$sn,$item_id),";
		}
		$batch_value=substr($batch_value,0,-1);
		//echo "===================<BR>$batch_value<BR>===================";
		$sql_select="REPLACE INTO charge_record(record_id,student_sn,item_id) values $batch_value";
		$res=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	} else echo "<script language=\"Javascript\"> alert (\"��T����, �L�k�����O�妸�s�W�I\")</script>";
};

if($_POST['act']=='�M�����Z�ť�ú�ڪ��W��'){
	$sql_select="delete from charge_record where item_id=$item_id AND record_id like '$work_year_seme$class_id%' AND dollars=0";
	$res=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
};
$main="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#AAAAAA' width='100%'><form name='myform' method='post' action='$_SERVER[PHP_SELF]'>
	<select name='item_id' onchange='this.form.submit()'><option></option>";


//���o�~�׶���
$sql_select="select * from charge_item where cooperate=1 AND year_seme='$work_year_seme' AND (curdate() between start_date AND end_date) order by end_date desc";
$res=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
while(!$res->EOF) {
	$main.="<option ".($item_id==$res->fields[item_id]?"selected":"")." value=".$res->fields[item_id].">".$res->fields[item]."(".$res->fields[start_date]."~".$res->fields[end_date].")</option>";
	$res->MoveNext();
}

$main.="</select>";
if($item_id)
{
	if($class_id)
	{
		//���o�e�w�}�C�ǥ͸��
		$sql_select="select * from charge_record where item_id=$item_id AND record_id like '$work_year_seme$class_id%' order by record_id";
		$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
		$listed=array();
		while(!$recordSet->EOF)
		{
			$listed[$recordSet->fields[student_sn]]=$recordSet->fields[dollars];
			$recordSet->MoveNext();
		}

		//���ostud_base���Z�žǥͦC���þڥH�P�esql��ӫ����
		$stud_select="SELECT student_sn,curr_class_num,right(curr_class_num,2) as class_no,stud_name,stud_sex FROM stud_base WHERE stud_study_cond=0 AND curr_class_num like '$class_id%' ORDER BY curr_class_num";
		$recordSet=$CONN->Execute($stud_select) or user_error("Ū�����ѡI<br>$stud_select",256);
		//�Hcheckbox�e�{
		$col=7; //�]�w�C�@�C��ܴX�H
		$studentdata="";
		while(list($student_sn,$curr_class_num,$class_no,$stud_name,$stud_sex)=$recordSet->FetchRow()) {
			if($recordSet->currentrow() % $col==1) $studentdata.="<tr>";
			if (array_key_exists($student_sn,$listed)) {
    				$studentdata.="<td bgcolor=".($listed[$recordSet->fields[student_sn]-1]?"#CCCCCC":"#FFFFDD").">��($class_no)$stud_name</td>";
			} else {
				$studentdata.="<td bgcolor=".($stud_sex==1?"#CCFFCC":"#FFCCCC")."><input type='checkbox' name='selected_stud[]' value='$student_sn,$curr_class_num' id='stud_selected'>($class_no)$stud_name</td>";
			}
			if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
			//echo "<BR>$curr_class_num === $stud_name";
		}
		$studentdata.="<tr height='50'><td align='right' colspan=$col>���G�w�}�C�@�@<input type='button' name='all_stud' value='����' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='������' onClick='javascript:tagall(0);'>�@";
		$studentdata.="<input type='submit' value='�}�C��ܪ��ǥ�' name='act'>";
		$studentdata.="�@<input type='submit' value='�M�����Z�ť�ú�ڪ��W��' name='act' onclick='return confirm(\"�T�w�n\"+this.value+\"?\\n\\nPS.ú�O������|�@�ֳQ�R��\")'></td></tr>";
	}
}
echo $main.$studentdata."</form></table>";
} else echo $not_allowed;
foot();
?>