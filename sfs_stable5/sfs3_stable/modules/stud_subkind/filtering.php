<?php
// $Id: filtering.php 5596 2009-08-21 01:33:21Z infodaes $

include_once "config.php";
sfs_check();
$group_selected=$_POST['group_selected'];
$group_xor_selected=$_POST['group_xor_selected'];

$new_description=$_POST['new_description'];
$filter_mode=$_POST['filter_mode']?$_POST['filter_mode']:'or';

$stud_data_mode=$_POST['stud_data_mode']?$_POST['stud_data_mode']:'simple';
$aid=($stud_data_mode=='aid')?'checked':'';
$simple=($stud_data_mode=='simple')?'checked':'';
$book=($stud_data_mode=='book')?'checked':'';

$sorted=$_POST['sorted']?$_POST['sorted']:'by_class';
$by_kind=($sorted=='by_kind')?'checked':'';
$by_class=($sorted=='by_class')?'checked':'';


if($_POST['go']=='儲存修改'){
	if($group_selected) {
		$kind_array=$_POST['kind'];
		foreach($kind_array as $value) $kind_list.="$value,";
		$kind_list=','.$kind_list;
		$update_sql="UPDATE stud_kind_group SET description='$new_description',kind_list='$kind_list' WHERE sn=$group_selected";
		$recordSet=$CONN->Execute($update_sql) or user_error("讀取失敗！<br>$update_sql",256);
	}
}


//秀出網頁
head("群組名單篩選");

//橫向選單標籤
echo print_menu($MENU_P,$linkstr);
if(checkid($_SERVER['SCRIPT_FILENAME'],1)) {
//取得學生身份列表
$kind_ref_array=SFS_TEXT(stud_kind);

//取得群組名單
$group_sql="SELECT * FROM stud_kind_group ORDER BY description";
$recordSet=$CONN->Execute($group_sql) or user_error("讀取失敗！<br>$group_sql",256);
$group_select="<select name='group_selected' onchange='this.form.submit();'><option value='-'>---------選擇群組---------</option>";
$group_xor_select="<select name='group_xor_selected' onchange='this.form.submit();'><option value='-'>---------選擇互斥群組---------</option>";

while(!$recordSet->EOF){
	$sn=$recordSet->fields['sn'];
	$description=$recordSet->fields['description'];
	$selected='';
	
	if($group_selected==$sn) {
		$selected='selected';
		$kind_list='';
		$kind_list_array=explode(',',$recordSet->fields['kind_list']);
		foreach($kind_list_array as $value) if($value) $kind_list.="[$kind_ref_array[$value]]";
	}
	$group_select.="<option $selected value=$sn>$description</option>";
	
	$selected='';
	if($group_xor_selected==$sn) {
		$selected='selected';
		$kind_xor_list='';
		$kind_xor_list_array=explode(',',$recordSet->fields['kind_list']);
		foreach($kind_xor_list_array as $value) if($value) $kind_xor_list.="[$kind_ref_array[$value]]";
	}
	$group_xor_select.="<option $selected value=$sn>$description</option>";

	$recordSet->MoveNext();
}
$group_select.="</select>";
$group_xor_select.="</select>";

//進行互斥處理
foreach($kind_xor_list_array as $value) {
	$key_result=array_search($value,$kind_list_array);
	if($key_result) unset($kind_list_array[$key_result]);
}

//篩選學生
$filtered_student=array();
$sn_list='';
$all_kind='';
foreach($kind_list_array as $kind) {
	if($kind) {
		$all_kind.="[$kind]";
		$group_sql="SELECT a.student_sn,a.curr_class_num,a.stud_id,a.stud_name,a.stud_sex,a.stud_addr_1,a.stud_addr_2,a.stud_tel_1,a.stud_tel_2,b.guardian_name FROM stud_base a LEFT JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE stud_study_cond=0 AND INSTR(a.stud_kind,',$kind,') ORDER BY a.curr_class_num;";
		$recordSet=$CONN->Execute($group_sql) or user_error("讀取失敗！<br>$group_sql",256);
		while(!$recordSet->EOF){
			$student_sn=$recordSet->fields['student_sn'];
			$curr_class_num=$recordSet->fields['curr_class_num'];
			$primary_key=$curr_class_num."_".$student_sn;  //避免學校班級學生座號重複以至於名單被後者覆蓋
			if(!array_key_exists($student_sn,$filtered_student)) {
				$filtered_student[$primary_key]['student_sn']=$recordSet->fields['student_sn'];
				$filtered_student[$primary_key]['curr_class_num']=$recordSet->fields['curr_class_num'];
				$filtered_student[$primary_key]['stud_id']=$recordSet->fields['stud_id'];
				$filtered_student[$primary_key]['stud_name']=$recordSet->fields['stud_name'];
				$filtered_student[$primary_key]['stud_sex']=$recordSet->fields['stud_sex'];
				$filtered_student[$primary_key]['stud_addr_1']=$recordSet->fields['stud_addr_1'];
				$filtered_student[$primary_key]['stud_addr_2']=$recordSet->fields['stud_addr_2'];
				$filtered_student[$primary_key]['stud_tel_1']=$recordSet->fields['stud_tel_1'];
				$filtered_student[$primary_key]['stud_tel_2']=$recordSet->fields['stud_tel_2'];
				$filtered_student[$primary_key]['guardian_name']=$recordSet->fields['guardian_name'];				 
			}
			$filtered_student[$primary_key]['kind'].="[$kind]";
			$filtered_student[$primary_key]['kind_description'].="[$kind_ref_array[$kind]]";
			$recordSet->MoveNext();
		}
	}
}

//進行and 篩選
if($_POST['filter_mode']=='and'){
	foreach($filtered_student as $curr_class_num=>$data){
		if($data['kind']<>$all_kind) unset($filtered_student[$curr_class_num]);
	}
}
foreach($kind_list_array as $value) if($value) $kind_result_list.="[$kind_ref_array[$value]]";

//進行陣列排序
if($by_class) ksort($filtered_student);

//echo "<PRE>";
//print_r($filtered_student);
//echo "</PRE>";

// 取出班級陣列
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$class_base = class_base($curr_year_seme);
$sex_array=array('1'=>'男','2'=>'女');

//抓取學年班級導師陣列
$class_teacher_arr=array();
$semester_template=sprintf('%03d_%d_',curr_year(),curr_seme());
$sql="select class_id,teacher_1 from school_class where class_id like '$semester_template%'";
$res=$CONN->Execute($sql) or user_error("讀取school_class資料失敗！<br>$sql",256);
while(!$res->EOF) {
	$teacher_class_id=$res->fields['class_id'];
	$class_teacher_arr[$teacher_class_id]=$res->fields['teacher_1'];
	$res->MoveNext();
}

//學生資料列表
$serial_no=0;
foreach($filtered_student as $curr_class_num=>$data){
	$stud_sex=$data['stud_sex'];
	$stud_class=$class_base[substr($data['curr_class_num'],0,3)];
	$stud_no=substr($data['curr_class_num'],-2);
	
	$teacher_class_id=sprintf('%02d_%02d',substr($data['curr_class_num'],0,1),substr($data['curr_class_num'],1,2));
	$teacher_class_id=$semester_template.$teacher_class_id;

	$bg_color=$stud_sex==1?'#CCCCFF':'#FFCCCC';
	$serial_no++;
	switch($stud_data_mode){
	case 'simple':
		$stud_data.="<tr bgcolor='$bg_color'>
					<td align='center'>$serial_no</td>
					<td>".$data['stud_id']."</td>
					<td>$stud_class</td>
					<td>$stud_no</td>
					<td>".$data['stud_name']."</td>
					<td>".$sex_array[$stud_sex]."</td>
					<td>".$data['kind_description']."</td>
					</tr>";		
		break;
	case 'aid':
		$stud_data.="<tr bgcolor='$bg_color'>
					<td align='center'>$serial_no</td>
					<td>".$SCHOOL_BASE["sch_sheng"]."</td>
					<td>".$SCHOOL_BASE["sch_local_name"]."</td>
					<td>".$SCHOOL_BASE["sch_cname_ss"]."</td>
					<td>$stud_class</td>
					<td>".$class_teacher_arr[$teacher_class_id]."</td>
					<td>".$data['stud_name']."</td>
					<td>".$data['guardian_name']."</td>
					<td>".($data['stud_tel_2']?$data['stud_tel_2']:$data['stud_tel_1'])."</td>
					<td>".($data['stud_addr_2']?$data['stud_addr_2']:$data['stud_addr_1'])."</td>
					<td>".$data['kind_description']."</td>
					</tr>";		
		break;	
	case 'book':
		$stud_data.="<tr bgcolor='$bg_color'>
					<td align='center'>$serial_no</td>
					<td>".$SCHOOL_BASE["sch_local_name"]."</td>
					<td>".$SCHOOL_BASE["sch_cname_ss"]."</td>
					<td>".$data['stud_id']."</td>
					<td>$stud_class</td>
					<td>".$data['stud_name']."</td>					
					<td>".$data['kind_description']."</td>
					</tr>";
		break;	
	}
	
	
}

switch($stud_data_mode){
case 'simple':
	$cols_count=7;
	$title_data="<tr align=center><td>NO.</td><td>學號</td><td>就讀班級</td><td>座號</td><td>姓名</td><td>性別</td><td>符合的身分類別</td></tr>";
	break;
case 'aid':
	$cols_count=11;
	$title_data="<tr align=center><td>項次</td><td>縣市</td><td>鄉鎮區</td><td>校名</td><td>班級</td><td>導師姓名</td><td>學生姓名</td><td>家長姓名</td><td>聯絡電話</td><td>聯絡地址</td><td>無力繳交代收代辦費原因</td></tr>";
	break;	
case 'book':
	$cols_count=7;
	$title_data="<tr align=center><td>編號</td><td>鄉鎮區</td><td>校名</td><td>學號</td><td>班級</td><td>學生姓名</td><td>符合類別</td></tr>";
	break;
}

$listdata="<table width='100%' cellspacing='1' cellpadding='3'>
             <form name='my_form' method='post' action='$_SERVER[PHP_SELF]'>
			 <tr bgcolor=#FFCFAA><td colspan=$cols_count><img border='0' src='images/pin.gif'>身份別群組列表：$group_select <font color=red size=2>$kind_list</font></td></tr>
			 <tr bgcolor=#FFCFAA><td colspan=$cols_count><img border='0' src='images/pin.gif'>群組身份別排除：$group_xor_select  <font color=red size=2>$kind_xor_list</font></td></tr>
			 <tr bgcolor=#FFCFAA><td colspan=$cols_count><img border='0' src='images/pin.gif'>排除結果：<font color=blue>$kind_result_list</font></td></tr>
			 <tr bgcolor=#FFCFAA><td colspan=$cols_count><img border='0' src='images/pin.gif'>篩選邏輯：
			 <input type='radio' value='or' name='filter_mode' ".(($filter_mode=='or')?'checked':'')." onclick='this.form.submit();'>具身分類別之ㄧ
			 <input type='radio' value='and' name='filter_mode' ".(($filter_mode=='and')?'checked':'')." onclick='this.form.submit();'>符合全部的身分類別
			 </td></tr>
			 
			 <tr bgcolor=#FFCFAA><td colspan=$cols_count><img border='0' src='images/pin.gif'>篩選結果：共有 ".count($filtered_student)." 位符合條件</td></tr>";

if($group_selected and count($filtered_student)) {
	$listdata.="<tr bgcolor=#FFCFAA><td colspan=$cols_count><img border='0' src='images/pin.gif'>排序方式：
				<input type='radio' value='by_kind' name='sorted' $by_kind onclick='this.form.submit()'>以身分類別 
				<input type='radio' value='by_class' name='sorted' $by_class onclick='this.form.submit()'>以班級座號
				</td></tr>";	
	$listdata.="<tr bgcolor=#FFCFAA><td colspan=$cols_count><img border='0' src='images/pin.gif'>學生資料列表：
				<input type='radio' value='simple' name='stud_data_mode' $simple onclick='this.form.submit()'>簡易式 
				<input type='radio' value='aid' name='stud_data_mode' $aid onclick='this.form.submit()'>代收代辦費補助 
				<input type='radio' value='book' name='stud_data_mode' $book onclick='this.form.submit()'>教科書補助費
				</td></tr>";	
	
}

$listdata.="$title_data $stud_data</form></table>";
echo $listdata;
} else { echo "<h2><center><BR><BR><font color=#FF0000>您並非模組管理員，無法使用本功能!</font></center></h2>"; } 
foot();
?>
