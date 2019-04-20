<?php
// $Id:$
require "config.php";

sfs_check();

$grade=$_POST['grade']?$_POST['grade']:($IS_JHORES+1);
$ss_id=$_POST['ss_id'];

$curr_year = curr_year();
$curr_seme = curr_seme();

if($_POST['act']){
	$imported_datas=$_POST['imported'];
	$error_list='';
	foreach($imported_datas as $group_id=>$students){
		$value_list='';		
		$student_array=explode("\r\n",$students);
		foreach($student_array as $key=>$value)
		{
			if($value){
				switch($_POST['import_radio']){
					case 'id':
						$sql="SELECT student_sn,curr_class_num,stud_name FROM stud_base WHERE stud_study_cond=0 and stud_id='$value'";			
						break;
					case 'no':
						$sql="SELECT student_sn,curr_class_num,stud_name FROM stud_base WHERE stud_study_cond=0 and curr_class_num='$value'";			
						break;
				}
				$res=$CONN->Execute($sql) or trigger_error("錯誤訊息： $sql", E_USER_ERROR);
				$student_sn=$res->fields[0];
				$stud_grade=substr($res->fields[1],0,-4);
				$stud_name=$res->fields[2];
				if($student_sn){
					if($stud_grade==$grade)	$value_list.="('$group_id','$student_sn'),";
				} else $error_list.="<br>NO.$group_id - <font color='blue' size=5>$value</font> $stud_name";
			}
		}
		if($value_list){
			$value_list=substr($value_list,0,-1);
			$sql="REPLACE INTO elective_stu(group_id,student_sn) values $value_list";
			$rs=$CONN->Execute($sql) or trigger_error($sql,256);
			echo $sql."<br>";
		}
	}
	if($error_list) echo "◎無法解析匯入的資料：$error_list";
	exit;
}


head("分組課程學生名單匯入");
print_menu($menu_p);

//年級選單
$sql="select distinct c_year from school_class where year='$curr_year' and semester='$curr_seme' and enable='1' order by c_year";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);
while(!$rs->EOF){
	$c_year=$rs->fields['c_year'];
	$selected=($grade==$c_year)?" selected":"";	
	$option_grade.="<option value='$c_year'$selected>{$school_kind_name[$c_year]}</option>\n";
	$rs->MoveNext();
}
$class_selecter="<select name='grade' onchange=\"this.form.target=''; this.form.submit();\">$option_grade</select>";

//年級科目選單
$class=array($curr_year,$curr_seme,"",$grade);
$ss_name_arr=&get_ss_name_arr($class,$mode="長");

foreach($ss_name_arr as $kk => $vv){
	if($ss_id==$kk) $selected1=" selected";
	else $selected1="";
	$option_ss_id.="<option value='$kk'$selected1>$vv</option>\n";
}
if($option_ss_id=="") $ss_selecter="<br><br><font size=5 color='red'>本學期的課程尚未設定！</font>";
else $ss_selecter="<select name='ss_id' onchange=\"this.form.target='$_PHP[SCRIPT_NAME]'; this.form.submit();\"><option value='0'>選擇課程</option>\n$option_ss_id</select>";

//分組名稱選單
if($ss_id){
	$group_name_arr=&get_group_name_arr($ss_id);
	$group_count=count($group_name_arr);
	if($group_count){
		$imported_data="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#AAAAAA'>";
		
		$ss_id_no="<tr align='center' bgcolor='#ccccff'>";
		$ss_id_header="<tr align='center' bgcolor='#ffcccc'>";
		$ss_id_students="<tr align='center'>";
		foreach($group_name_arr as $kkk => $vvv){
			$ss_id_no.="<td>NO.$kkk</td>";
			$ss_id_header.="<td>{$vvv[0]}<br>".get_teacher_name($vvv[1])."</td>";
			$ss_id_students.="<td><textarea rows=15 name='imported[$kkk]' cols=10></textarea></td>";
	}
	$ss_id_no.="</tr>"; $ss_id_header.="</tr>"; $ss_id_students.="</tr>";
	
	$import_radios="<tr align='center'><td colspan=$group_count>◎輸入資料類型：
					<input type='radio' name='import_radio' value='no' checked>班級+座號 
					<input type='radio' name='import_radio' value='id'>學號 
					</td></tr><tr align='center'><td colspan=$group_count>
					<font size=2 color='red'>*僅限定匯入同年級在學的學生*</font><br>
					<input type='submit' name='act' value='解析匯入' style='border-width:2px; cursor:hand; color:black; width:100%; background:#ccffcc; font-size:20px;' onclick='this.form.target=\"$ss_id\";'></td></tr>";
	$imported_data.="$ss_id_no $ss_id_header $ss_id_students $import_radios</table>";
	} else $imported_data.="<br><br><font size=5 color='red'>尚未開設分組！</font>";
	
}
$main="<form name='myform' method='POST'>$class_selecter $ss_selecter $imported_data</form>";

echo $main;

foot();

?>
