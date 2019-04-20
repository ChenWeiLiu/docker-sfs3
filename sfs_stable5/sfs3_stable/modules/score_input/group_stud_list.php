<?php
// $Id: normal.php 5836 2010-01-25 15:03:54Z chiming $
/*引入學務系統設定檔*/
include "config.php";
include "./module-upgrade.php";
require_once "../../include/sfs_case_score.php";


//使用者認證
sfs_check();


//變數設定
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//教師代號
$teacher_sn = $_SESSION[session_tea_sn];
$teacher_name = $_SESSION[session_tea_name];

//取得班級名稱陣列
$class_name_arr = class_base();

//設定可以引用的學生資料
$student_fields_array=array('curr_class_num'=>'班級座號','stud_name'=>'學生姓名','stud_sex'=>'性別','stud_id'=>'學號','stud_tel_2'=>'聯絡電話','email'=>'電子郵件','stud_mschool_name'=>'國小就讀學校','enroll_school'=>'入學學校');

if($_POST['go']=='HTML輸出')
{
	$page_title=$_POST['page_title'];
	$page_memo=$_POST['page_memo'];
	$added_field=$_POST['added_field'];	
	$selected_class=$_POST['selected_class'];
	$preload_field=$_POST['preload_field'];
	$groups_title=$_POST['groups_title'];
	$cols_title=$_POST['cols_title'];

	$sex_array=array('1'=>'男','2'=>'女');

	$page_break ="<P style='page-break-after:always'>&nbsp;</P>";
	
	//抓取類項與子項
	$cols=$_POST['cols'];
	$groups=$_POST['groups'];
	switch($groups_title)
	{
		case '1': $groups_title_array=explode(',','　,　,　,　,　,　,　,　,　,　,　'); break;
		case '2': $groups_title_array=explode(',','1,2,3,4,5,6,7,8,9,10'); break;
		case '3': $groups_title_array=explode(',','一,二,三,四,五,六,七,八,九,十'); break;
		case '9': $groups_title_array=explode(',',$_POST['my_groups_title']); break;
	}
	for($i=0;$i<$groups;$i++) $groups_list.="<td colspan='$cols' align='center'>{$groups_title_array[$i]}</td>";
	
	
	switch($cols_title)
	{
		case '1': $cols_title_array=explode(',','　,　,　,　,　,　,　,　,　,　,　'); break;
		case '2': $cols_title_array=explode(',','1,2,3,4,5,6,7,8,9,10'); break;
		case '3': $cols_title_array=explode(',','一,二,三,四,五,六,七,八,九,十'); break;
		case '9': $cols_title_array=explode(',',$_POST['my_cols_title']); break;
	}
	for($k=0;$k<$groups;$k++) for($i=0;$i<$cols;$i++)
	{
		$cols_list.="<td align='center'>{$cols_title_array[$i]}</td>"; 
		$cols_list_empty.="<td ></td>"; 
	}
	
	//抓取前置欄位名稱
	foreach($preload_field as $value) $table_header.="<td rowspan='2' align='center'>{$student_fields_array[$value]}</td>";

	//抓取後置欄位名稱
	$added_field_array=explode(',',$_POST['added_field']);
	foreach($added_field_array as $value)
	{
		$added_field_list.="<td rowspan=2 align='center'>$value</td>";
		$added_field_list_empty.="<td></td>";
	}
	
	//列出表格資料
	$class_count=count($selected_class)-1;
	foreach($selected_class as $key=>$value)
	{
		$group_data=explode(' ',$value);
		$group_id=$group_data[0];
		//抓取分組課程學生清單
		$sql="select student_sn from elective_stu where group_id=$group_id";
		$rs=$CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
		$group_sn='';
		while (!$rs->EOF) {
			$group_sn.=$rs->fields['student_sn'].',';
			$rs->MoveNext();
		}
		$group_sn=substr($group_sn,0,-1);
		
		
		//抓取班級學生資料
		$stud_data="";
		$sql="select * from stud_base where student_sn in ($group_sn) AND stud_study_cond=0 order by curr_class_num";
		$rs=$CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
		while(!$rs->EOF) {
			foreach($preload_field as $value)
			{
				$field_data=$rs->fields[$value];
				if($value=='curr_class_num') $field_data=substr($field_data,0,-2).'-'.substr($field_data,-2); else
					if($value=='stud_sex') $field_data=$sex_array[$field_data]; 
		
				$stud_data.="<td align='center'>$field_data</td>";
			}
			$stud_data="<tr>$stud_data $cols_list_empty $added_field_list_empty</tr>";
			$rs->MoveNext();
		}

		
		$main="<center><font size=4>$page_title</font></center>
			<p>◎學期別：$sel_year - $sel_seme 　◎教師：$teacher_name 　◎任教組別與科目：{$group_data[1]}　{$group_data[2]}</p>
			<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr bgcolor='#FFEECC'>$table_header $groups_list $added_field_list</tr><tr>$cols_list</tr>$stud_data</table><font size=2>$page_memo</font>";
		if($key<$class_count) $main.=$page_break;
		echo $main;
	}
} else {
	head('分組學生名冊列印');
	print_menu($menu_p);
	echo "<script>
		function tagall(status) {
		  var i =0;
		  while (i < document.myform.elements.length)  {
			if (document.myform.elements[i].name=='selected_class[]') {
			  document.myform.elements[i].checked=status;
			}
			i++;
		  }
		}
		</script>";

	$subject_checkbox_list="";
	//取得正確任教課程
	$sql="SELECT a.*,b.* FROM elective_tea a,score_ss b WHERE a.ss_id=b.ss_id AND b.year=$sel_year AND b.semester=$sel_seme AND a.teacher_sn=$teacher_sn ORDER BY group_id";
	$rs=$CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
	while (!$rs->EOF) {
		$course_id[$i] = $rs->fields["course_id"];
		$class_year = $rs->fields["class_year"];
		$group_id= $rs->fields["group_id"];
		$group_name = $rs->fields["group_name"];
		$ss_id= $rs->fields["ss_id"];
		$subject_name=ss_id_to_subject_name($ss_id).'-'.$group_name;
		$subject_checkbox_list.="<input type='checkbox' name='selected_class[]' value='$group_id $subject_name' checked>($group_id)$subject_name<br>";
		$rs->MoveNext();
	}

	$page_title="◎報表標題：<input type='text' name='page_title' size=70 value='$school_long_name 成績紀錄表'>";
	
	//設定可以獲取的學生資料
	$lead_field="◎前置欄位：";
	foreach($student_fields_array as $key=>$value)
	{
		$c++;
		$checked=($c<=3)?'checked':'';
		$lead_field.="<input type='checkbox' name='preload_field[]' value='$key' $checked>$value";
	}
	

	$groups="　※階段次數：<input type='radio' value='1' name='groups'>1 <input type='radio' value='2' name='groups'>2 <input type='radio' value='3' name='groups' checked>3 <input type='radio' value='4' name='groups'>4";
	$groups_title="　　■標題：<input type='radio' value='1' name='groups_title'>空白 <input type='radio' value='2' name='groups_title'>1,2,3,.. <input type='radio' value='3' name='groups_title' checked>一,二,三,.. <input type='radio' value='9' name='groups_title'>自定義(以,分隔)：<input type='text' name='my_groups_title' size=25 maxlength=25 value='第一階段,第二階段,第三階段'>";
	$cols="　※每階段格數：<input type='radio' value='3' name='cols'>3 <input type='radio' value='5' name='cols' checked>5 <input type='radio' value='7' name='cols'>7 <input type='radio' value='10' name='cols'>10 ";
	$cols_title="　　■標題：<input type='radio' value='1' name='cols_title'>空白 <input type='radio' value='2' name='cols_title' checked>1,2,3,.. <input type='radio' value='3' name='cols_title'>一,二,三,.. <input type='radio' value='9' name='cols_title'>自定義(以,分隔)：<input type='text' name='my_cols_title' size=25 maxlength=25 value='定期,平時,平均'>";
	
	$added_field="◎後置欄位(以,分隔)：<input type='text' name='added_field' size=30 maxlength=30 value='總分,平均,備註'>";
	
	$page_memo="◎報表備註：<input type='text' name='page_memo' size=70 value=''>";
	
	echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]' target='_BLANK'>
			<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr><td align='center' width='200' bgcolor='#FFCCCC'>◎任教的科目◎ 　　　<input type='checkbox' name='tag' checked onclick='javascript:tagall(this.checked);'>全選</td>
			<td rowspan=2 valign='top'><BR>$page_title<BR><BR>$lead_field<BR><BR> $groups<BR> $groups_title<BR><BR> $cols<BR> $cols_title<BR><BR>$added_field<BR><BR>$page_memo
			<BR><BR><p align='center'><input type='submit' name='go' value='HTML輸出'></p></td></tr>
			<tr><td>$subject_checkbox_list</td></tr>
			</table></form>";
	foot();
}
?>
