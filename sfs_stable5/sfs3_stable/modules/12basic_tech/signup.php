<?php

include "config.php";

sfs_check();

//�q�X����
head("���W�Ǯ�");

//�Ǵ��O
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

$stud_class=$_REQUEST['stud_class'];
$edit_sn=$_POST['edit_sn'];

$show_zero=$_POST['show_zero']?'checked':'';

if($_POST['act']=='����') { $edit_sn=0;	$_POST['batch']=''; }

if($_POST['act']=='�T�w�ק�'){
	$sql="UPDATE 12basic_tech SET signup_north='{$_POST['edit_signup']['north']}',signup_central='{$_POST['edit_signup']['central']}',signup_south='{$_POST['edit_signup']['south']}',signup_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$edit_sn=0;
}

if($_POST['act']=='�妸��s'){
	foreach($_POST[batch] as $student_sn=>$data) {
		$sql="UPDATE 12basic_tech SET signup_north='{$data['edit_signup']['north']}',signup_central='{$data['edit_signup']['central']}',signup_south='{$data['edit_signup']['south']}',signup_memo='{$data['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$student_sn AND editable='1'";
		$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	}
	$edit_sn=0;
	$_POST['batch']='';
}


//��V������
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//���o�~�׻P�Ǵ����U�Կ��
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//��ܯZ��
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

$tool_icon="<input type='checkbox' name='show_zero' value=1 $show_zero onclick=\"this.form.submit();\"><font size=2 color='green'>��ܡu000 (�����)�v</font>";
$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><input type='hidden' name='batch' value=''><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon
	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//���o���w�Ǧ~�w�g�}�C���ǥͲM��
	$listed=get_student_list($work_year);
	
	//�ˬd�O�_���i�ק�������ѻP�K�վǥ�
	$editable_sn_array=get_editable_sn($work_year);
	
	//���o���w�Ǧ~�w�g�}�C���ǥͳ��W�ǮեN�X	
	$signup_array=get_student_signup($work_year);
	
	//���ostud_base���Z�žǥͦC���þڥH�P�esql��ӫ����
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("Ū�����ѡI<br>$stud_select",256);
	
	if($work_year==$academic_year and !$_POST['batch']) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ff8888';\" ondblclick='document.myform.batch.value=\"1\"; document.myform.submit();'";
	$studentdata="<tr align='center' bgcolor='#ff8888' $java_script><td width=80>�Ǹ�</td><td width=50>�y��</td><td width=120>�m�W</td><td width=$pic_width>�j�Y��</td><td>�_��</td><td>����</td><td>�n��</td><td>�Ƶ�</td>";
/*
echo '<pre>';
print_r($signup_array);
echo '</pre>';
*/
	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year)=$recordSet->FetchRow()) {
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		$signup='';
		foreach($signup_array[$student_sn]['item'] as $key=>$value){
			$td_data=$value.' '.$schools[$key][$value];
			if($value=='000' && !$_POST['show_zero']) $td_data='';
			$signup.="<td align='left'>$td_data</td>";		
		}
		$memo=$signup_array[$student_sn]['memo'];
		
		
		
		$action='';
		$java_script='';
	
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		
		//�妸�s��
		if($_POST['batch']){
			if(array_key_exists($student_sn,$editable_sn_array) and array_key_exists($student_sn,$listed)){
				$signup='';
				foreach($schools as $key=>$data){
					$signup.="<td><select name='batch[$student_sn][edit_signup][$key]'>";
					foreach($data as $id=>$value){
						$selected=($id==$signup_array[$student_sn]['item'][$key])?'selected':'';
						$signup.="<option value='$id' $selected>$value</option>";
					}
					$signup.="</select></td>";
				}				
				$memo="<input type='text' name='batch[$student_sn][edit_memo]' size=20 value='$memo'>";				
			}			
		} else {
			if($student_sn==$edit_sn){
				$stud_sex_color='#ffffaa';
				$signup='';
				foreach($schools as $key=>$data){
					$signup.="<td><select name='edit_signup[$key]'>";
					foreach($data as $id=>$value){
						$selected=($id==$signup_array[$student_sn]['item'][$key])?'selected':'';
						$signup.="<option value='$id' $selected>$value</option>";
					}
					$signup.="</select></td>";
				}
				
				$memo="<input type='text' name='edit_memo' size=20 value='$memo'>";
				
				
				//�ʧ@���s
				$action="<input type='submit' name='act' value='�T�w�ק�' onclick='return confirm(\"�T�w�n�ק� $stud_name ����L��Ƕ��ج���?\")'> <input type='submit' name='act' value='����' onclick='document.myform.edit_sn.value=0;'>";		
			} else {
				if(array_key_exists($student_sn,$listed)){
					$editable=array_key_exists($student_sn,$editable_sn_array)?1:0;
					$stud_sex_color=$editable?$stud_sex_color:$uneditable_bgcolor;
					$java_script=($work_year==$academic_year and $editable)?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'":'';
				} else { $stud_sex_color='#aaaaaa'; }
			}
		}
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$my_pic</td>$signup<td align='left'>$memo $action</td></tr>";
	}
	if($_POST['batch']) $studentdata.="<tr align='center'><td colspan=10><input type='submit' name='act' value='�妸��s' onclick='return confirm(\"�T�w�n�ק糧�Z�ǥͩҦ��������Ш|�ǵ{���Z?\")'> <input type='submit' name='act' value='����'></td></tr>";
}

//��ܫʦs���A��T
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."</table></form>";
foot();
?>