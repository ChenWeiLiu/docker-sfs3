<?php

// 引入 SFS3 的函式庫
include "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
//印出檔頭
head("轉入生資料補登");

$tool_bar=make_menu($toxml_menu);
echo $tool_bar;

$selected_type=$_POST['selected_type'];
$selected_student=$_POST['selected_student'];
$selected_student_id=$_POST['selected_student_id'];

if($_POST['go']=='寫入補登的編班資料')
{
	$seme_year_seme=$_POST['study_year_seme'];
	$study_grade=$_POST['study_grade'];
	$study_class_name=$_POST['study_class_name'];
	$study_seme_num=$_POST['study_seme_num'];
	$study_teacher_name=$_POST['study_teacher_name'];
	//var_dump($study_teacher_name);

	$sql="REPLACE INTO stud_seme_import(seme_year_seme,stud_id,seme_class_grade,seme_class_name,seme_num,student_sn,teacher_name) values";
	$count=count($study_year_seme);
	if($count)
	{
		for($i=0;$i<$count;$i++)
		{
			$sql.="('".$seme_year_seme[$i]."','$selected_student_id','".$study_grade[$i]."','".$study_class_name[$i]."','".$study_seme_num[$i]."','$selected_student','".$study_teacher_name[$i]."'),";
		}
		$sql=substr($sql,0,-1);
		$recordSet=$CONN->Execute($sql) or user_error("更新編班紀錄失敗！<br>$sql",256);
		$message="<FONT COLOR='RED' SIZE=2>前次寫入時間：".date("Y-m-d H:i:s")." </FONT> ";
	}
	//echo"<BR><BR>$sql<BR><BR>";
}
//------------------------------------------------------------------------------------------------------------------------------------------------- 
if($_POST['go']=='寫入修改的異動資料')
{
	$move_id=$_POST['move_id'];
	$move_date=$_POST['move_date'];
	$move_kind=$_POST['move_kind'];
	$stud_id=$_POST['stud_id'];
	$move_year_seme=$_POST['move_year_seme'];
	$move_c_unit=$_POST['move_c_unit'];
	$move_c_date=$_POST['move_c_date'];
	$move_c_word=$_POST['move_c_word'];
	$move_c_num=$_POST['move_c_num'];
	$school_id=$_POST['school_id'];
	$school=$_POST['school'];
	$reason=$_POST['reason'];
	$city=$_POST['city'];

	$sql="REPLACE INTO stud_move_import(move_id,move_date,move_kind,stud_id,move_year_seme,move_c_unit,move_c_date,move_c_word,move_c_num,school_id,school,reason,city,student_sn,update_id,update_ip,update_time) values";
	$count=count($move_date);
	if($count)
	{
		//先將原補登記錄砍除
		//$sql_delete="DELETE FROM stud_move_import WHERE student_sn=$selected_student";
		//$res=$CONN->Execute($sql_delete) or user_error("刪除原補登異動記錄紀錄失敗！<br>$sql_delete",256);
		//補上補登的記錄
		for($i=0;$i<$count;$i++)
		{
			if($move_date[$i] and $move_kind[$i])
				$sql.="({$move_id[$i]},'{$move_date[$i]}','{$move_kind[$i]}','$selected_student_id','{$move_year_seme[$i]}','{$move_c_unit[$i]}','{$move_c_date[$i]}','{$move_c_word[$i]}','{$move_c_num[$i]}','{$school_id[$i]}','{$school[$i]}','{$reason[$i]}','{$city[$i]}','$selected_student','{$_SESSION [session_log_id]}','{$_SERVER['REMOTE_ADDR']}',NOW()),";
		}
		$sql=substr($sql,0,-1);
		$recordSet=$CONN->Execute($sql) or user_error("更新異動紀錄失敗！<br>$sql",256);
		$message="<FONT COLOR='RED' SIZE=2>前次修改寫入時間：".date("Y-m-d H:i:s")." </FONT><BR>";
	}
	//echo"<BR><BR>$sql<BR><BR>";
}
//------------------------------------------------------------------------------------------------------------------------------------------------- 
if($_POST['go']=='寫入新增的異動資料')
{
	$move_date=$_POST['a_move_date'];
	$move_kind=$_POST['a_move_kind'];
	$stud_id=$_POST['a_stud_id'];
	$move_year_seme=$_POST['a_move_year_seme'];
	$move_c_unit=$_POST['a_move_c_unit'];
	$move_c_date=$_POST['a_move_c_date'];
	$move_c_word=$_POST['a_move_c_word'];
	$move_c_num=$_POST['a_move_c_num'];
	$school_id=$_POST['a_school_id'];
	$school=$_POST['a_school'];
	$reason=$_POST['a_reason'];
	$city=$_POST['a_city'];

	$sql="INSERT INTO stud_move_import(move_date,move_kind,stud_id,move_year_seme,move_c_unit,move_c_date,move_c_word,move_c_num,school_id,school,reason,city,student_sn,update_id,update_ip,update_time) values";
	if($move_date and $move_kind)
	{
		$sql.="('$move_date','$move_kind','$selected_student_id','$move_year_seme','$move_c_unit','$move_c_date','$move_c_word','$move_c_num','$school_id','$school','$reason','$city','$selected_student','{$_SESSION [session_log_id]}','{$_SERVER['REMOTE_ADDR']}',NOW());";
		$recordSet=$CONN->Execute($sql) or user_error("新增異動紀錄失敗！<br>$sql",256);
	}
	//echo"<BR><BR>$sql<BR><BR>";
}

//2013/02/27 by smallduh
//增加一個社團記錄
if ($_POST['club_act']=='club_add') {
 $query="insert into association (student_sn,seme_year_seme,association_name,score,description,update_sn,update_time) values ('".$selected_student."','".$_POST['year_seme']."','".$_POST['association_name']."','".$_POST['score']."','".$_POST['description']."','".$_SESSION['session_tea_sn']."',NOW())";
 mysql_query($query);
}
//刪除一個社團記錄
if ($_POST['club_act']=='club_delete') {
 $query="delete from association where sn='".$_POST['club_option']."'";
 mysql_query($query);
}

//-------------------------------------------------------------------------------------------------------------------------------------------------
/*
if($selected_student)
{
	$data_types=array('學期編班'=>'','異動紀錄'=>'','學期成績'=>'../score_input_all_new/person_seme_input.php');
	$data_type_radio="※選擇補登項目：";
	foreach($data_types as $key=>$value)
	{
		if($key==$selected_type) $action_target=$value;
		$data_type_radio.="<input type='radio' value='$key' name='selected_type'".(($key==$selected_type)?' checked':'')." onclick='this.form.submit()'>$key ";	
	}
	if(! $action_target) $action_target=$_SERVER['SCRIPT_NAME'];
}
*/
//取得年度與學期的下拉選單
$work_year_seme=$_REQUEST[work_year_seme];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$move_year_seme = intval(substr($work_year_seme,0,-1)).substr($work_year_seme,-1,1);

$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());

$seme_list=get_class_seme();
$main="<hr><form name='myform' method='post' action='{$_SERVER['SCRIPT_NAME']}'>
	※選擇轉入的學期：<select name='work_year_seme' onchange='document.myform.submit()'>";
foreach($seme_list as $key=>$value){
	$main.="<option ".($key==$work_year_seme?"selected":"")." value=$key>$value</option>";
}
$main.="</select><hr>";


//取得項目
//if($selected_student)
//{
	$data_types=array('1'=>'學期編班','2'=>'異動紀錄','3'=>'社團記錄','4'=>'學期成績');
	$data_type_radio="※選擇補登項目：";
	foreach($data_types as $key=>$value)
	{
		$data_type_radio.="<input type='radio' value='$key' name='selected_type'".(($key==$selected_type)?' checked':'')." onclick='this.form.submit()'>$value ";	
	}
	$data_type_radio.="<hr>";
//}

if($selected_type)
{
//取得該學期轉入學生清單
$sql="SELECT a.*,b.stud_id,b.stud_name,b.stud_sex,b.stud_study_year FROM stud_move a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.move_kind in (2,3,14) AND move_year_seme='$move_year_seme' ORDER BY move_date DESC";
$recordSet=$CONN->Execute($sql) or user_error("讀取stud_move、stud_base資料失敗！<br>$sql",256);

$col=3; //設定每一列顯示幾人
$studentdata="※選擇欲補登的學生：<table>";
$student_radio='';
while(!$recordSet->EOF)
{
	$currentrow=$recordSet->currentrow()+1;
	if($currentrow % $col==1) $studentdata.="<tr>";
	$student_sn=$recordSet->fields['student_sn'];
	$stud_id=$recordSet->fields['stud_id'];
	$stud_name=$recordSet->fields['stud_name'];
	$stud_move_date=$recordSet->fields['move_date'];
	if($recordSet->fields['stud_sex']=='1') $color='#CCFFCC'; else  $color='#FFCCCC';
	if($student_sn==$selected_student) {
		$color='#FFFFAA';
		$stud_study_year=$recordSet->fields['stud_study_year'];
		$selected_student_id=$stud_id;
	}
	
	$student_radio="<input type='radio' value='$student_sn' name='selected_student'".(($student_sn==$selected_student)?' checked':'')." onclick='document.myform.submit()'>( $student_sn - $stud_id ) $stud_name - $stud_move_date";	
	
	$studentdata.="<td bgcolor='$color' align='center'> $student_radio </td>";

	if( $currentrow % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
	$recordSet->movenext();
}
$studentdata.='</table><hr>';

//依據選項顯示該生目前狀況
if($selected_student)
switch($selected_type)
{
	case '1': //編班資料
		//推算應就學學期
		$counter=$IS_JHORES?3:6;
		$seme_class_cond=array();
		for($grade=1;$grade<=$counter;$grade++)
		{
			for($semester=1;$semester<=2;$semester++)
			{
				$real_grade=$grade+$IS_JHORES;
				$study_year=$stud_study_year+$grade-1;
				$seme_year_seme=sprintf('%03d%d',$study_year,$semester);
				$seme_class_cond[$seme_year_seme]['grade']=$real_grade;
				$seme_class_cond[$seme_year_seme]['data_source']='0';
			}
		}
	
		//抓取stud_seme_import的紀錄
		$sql="SELECT seme_year_seme,seme_class_grade,seme_class_name,seme_num,teacher_name FROM stud_seme_import WHERE student_sn=$selected_student ORDER BY seme_year_seme";
		$recordSet=$CONN->Execute($sql) or user_error("讀取stud_seme_import資料失敗！<br>$sql",256);
		while(!$recordSet->EOF)
		{
			$seme_year_seme=$recordSet->fields['seme_year_seme'];
			
			$seme_class_cond[$seme_year_seme]['seme_class']=$recordSet->fields['seme_class_grade'];
			$seme_class_cond[$seme_year_seme]['seme_class_name']=$recordSet->fields['seme_class_name'];
			$seme_class_cond[$seme_year_seme]['seme_num']=$recordSet->fields['seme_num'];
			$seme_class_cond[$seme_year_seme]['teacher_name']=$recordSet->fields['teacher_name'];
			
			$seme_class_cond[$seme_year_seme]['data_source']='2';
	
			$recordSet->movenext();
		}
			
		//抓取stud_seme的紀錄
		$sql="SELECT seme_year_seme,seme_class,seme_class_name,seme_num FROM stud_seme WHERE student_sn=$selected_student ORDER BY seme_year_seme";
		$recordSet=$CONN->Execute($sql) or user_error("讀取stud_seme資料失敗！<br>$sql",256);
		while(!$recordSet->EOF)
		{
			$seme_year_seme=$recordSet->fields['seme_year_seme'];
			
			$seme_class_cond[$seme_year_seme]['seme_class']=$recordSet->fields['seme_class'];
			$seme_class_cond[$seme_year_seme]['seme_class_name']=$recordSet->fields['seme_class_name'];
			$seme_class_cond[$seme_year_seme]['seme_num']=$recordSet->fields['seme_num'];
			$seme_class_cond[$seme_year_seme]['data_source']='1';
			$recordSet->movenext();
		}
	
		//echo "<pre>";
		//print_r($seme_class_cond);
		//echo "</pre>";		
		
		//顯示並可直接修正
		$show_data="<table width='80%' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>";
		$show_data.="<tr bgcolor='#FFFFAA'><td align='center'>學期別</td><td align='center'>年級</td><td align='center'>班級</td><td align='center'>座號</td><td align='center'>導師姓名</td><td align='center'>資料來源</td></tr>";
		foreach($seme_class_cond as $seme_year_seme=>$value)
		{
			$grade=substr($value['grade'],0,1);
			//echo "<BR>".substr($value['grade'],0,1)."---<BR>".$value['grade']."<BR>";
			$class_name=$value['seme_class_name'];
			$seme_num=$value['seme_num'];
			$teacher_name=$value['teacher_name'];
			switch($value['data_source'])
			{
				case '0': $data_source=''; break;
				case '1': $data_source='本校就讀紀錄(stud_seme)'; break;
				case '2': $data_source='補登紀錄(stud_seme_import)'; break;			
			}
			if($curr_year_seme>=$seme_year_seme)
			{
				if($value['data_source']=='1') $show_data.="<tr><td align='center'>$seme_year_seme</td><td align='center'>$grade</td><td align='center'>$class_name</td><td align='center'>$seme_num</td><td align='center'>$teacher_name</td><td>$data_source</td></tr>";
				else  $show_data.="<tr><td align='center'>$seme_year_seme<input type='hidden' name='study_year_seme[]' value='$seme_year_seme'></td><td align='center'><input type='text' name='study_grade[]' value='$grade' size=2 maxlength=2></td><td align='center'><input type='text' name='study_class_name[]' value='$class_name' size=8 maxlength=8></td><td align='center'><input type='text' name='study_seme_num[]' value='$seme_num' size=2 maxlength=2></td><td align='center'><input type='text' name='study_teacher_name[]' value='$teacher_name' maxlength=20></td><td>$data_source</td></tr>";
			} //else $show_data.="<tr><td align='center'>$seme_year_seme</td><td align='center'>$grade</td><td align='center'>--</td><td align='center'>--</td><td align='center'>--</td><td align='center'>--</td></tr>";
		}
		$show_data.="<tr bgcolor='#FFFFAA'><td colspan=4 align='center'>$message</td><td colspan=2 align='center'><input type='hidden' name='selected_student_id' value='$selected_student_id'><input type='submit' name='go' value='寫入補登的編班資料'></td></tr></table>";
		break;
		
	case '2': //異動紀錄
		$counter=$IS_JHORES?3:6;
		$seme_move_cond=array();
	
		//抓取stud_seme_import的紀錄
		$sql="SELECT * FROM stud_move_import WHERE student_sn=$selected_student ORDER BY move_date";
		$recordSet=$CONN->Execute($sql) or user_error("讀取stud_move_import資料失敗！<br>$sql",256);
		while(!$recordSet->EOF)
		{
			$move_date=$recordSet->fields['move_date'];
			$seme_move_cond[$move_date]['move_id']=$recordSet->fields['move_id'];
			$seme_move_cond[$move_date]['data_source']='2';
			$seme_move_cond[$move_date]['move_year_seme']=$recordSet->fields['move_year_seme'];
			$seme_move_cond[$move_date]['stud_id']=$recordSet->fields['stud_id'];
			$seme_move_cond[$move_date]['move_kind']=$recordSet->fields['move_kind'];
			$seme_move_cond[$move_date]['school_move_num']=$recordSet->fields['school_move_num'];
			$seme_move_cond[$move_date]['move_c_unit']=$recordSet->fields['move_c_unit'];
			$seme_move_cond[$move_date]['move_c_date']=$recordSet->fields['move_c_date'];
			$seme_move_cond[$move_date]['move_c_word']=$recordSet->fields['move_c_word'];
			$seme_move_cond[$move_date]['move_c_num']=$recordSet->fields['move_c_num'];
			$seme_move_cond[$move_date]['update_time']=$recordSet->fields['update_time'];
			$seme_move_cond[$move_date]['update_id']=$recordSet->fields['update_id'];
			$seme_move_cond[$move_date]['update_ip']=$recordSet->fields['update_ip'];
			$seme_move_cond[$move_date]['school']=$recordSet->fields['school'];
			$seme_move_cond[$move_date]['school_id']=$recordSet->fields['school_id'];
			$seme_move_cond[$move_date]['student_sn']=$recordSet->fields['student_sn'];
			$seme_move_cond[$move_date]['reason']=$recordSet->fields['reason'];
			$seme_move_cond[$move_date]['city']=$recordSet->fields['city'];

			$recordSet->movenext();
		}
		//抓取stud_seme的紀錄
		$sql="SELECT * FROM stud_move WHERE student_sn=$selected_student ORDER BY move_date";
		$recordSet=$CONN->Execute($sql) or user_error("讀取stud_move_import資料失敗！<br>$sql",256);
		while(!$recordSet->EOF)
		{
			$move_date=$recordSet->fields['move_date'];
			$seme_move_cond[$move_date]['move_id']=$recordSet->fields['move_id'];
			$seme_move_cond[$move_date]['data_source']='1';
			$seme_move_cond[$move_date]['move_year_seme']=$recordSet->fields['move_year_seme'];
			$seme_move_cond[$move_date]['stud_id']=$recordSet->fields['stud_id'];
			$seme_move_cond[$move_date]['move_kind']=$recordSet->fields['move_kind'];
			$seme_move_cond[$move_date]['school_move_num']=$recordSet->fields['school_move_num'];
			$seme_move_cond[$move_date]['move_c_unit']=$recordSet->fields['move_c_unit'];
			$seme_move_cond[$move_date]['move_c_date']=$recordSet->fields['move_c_date'];
			$seme_move_cond[$move_date]['move_c_word']=$recordSet->fields['move_c_word'];
			$seme_move_cond[$move_date]['move_c_num']=$recordSet->fields['move_c_num'];
			$seme_move_cond[$move_date]['update_time']=$recordSet->fields['update_time'];
			$seme_move_cond[$move_date]['update_id']=$recordSet->fields['update_id'];
			$seme_move_cond[$move_date]['update_ip']=$recordSet->fields['update_ip'];
			$seme_move_cond[$move_date]['school']=$recordSet->fields['school'];
			$seme_move_cond[$move_date]['school_id']=$recordSet->fields['school_id'];
			$seme_move_cond[$move_date]['student_sn']=$recordSet->fields['student_sn'];
			$seme_move_cond[$move_date]['reason']=$recordSet->fields['reason'];
			$seme_move_cond[$move_date]['city']=$recordSet->fields['city'];

			$recordSet->movenext();
		}
	
		//echo "<pre>";
		//print_r($seme_move_cond);
		//echo "</pre>";
		//依日期排序
		ksort($seme_move_cond);
		//顯示並可直接修正
		$show_data="<table width='100%' border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse ;font-size:10pt;' bordercolor='#111111' width='100%'>";
		$show_data.="<tr bgcolor='#FFCCAA'><td align='center'>異動日期</td><td align='center'>類別</td><td align='center'>學號</td><td align='center'>學年學期</td><td align='center'>核准單位</td><td align='center'>核准日期</td><td align='center'>核准字號</td><td align='center'>轉出入學校代號及名稱</td><td align='center'>異動原因</td><td align='center'>原就讀學校縣市</td><td align='center'>紀錄來源</td></tr>";
		$edit_count=0;
		foreach($seme_move_cond as $move_date=>$value)
		{
			$move_id=$value['move_id'];
			$move_kind=$value['move_kind'];
			$move_year_seme=$value['move_year_seme'];
			$school_move_num=$value['school_move_num'];
			
			$move_c_unit=$value['move_c_unit'];
			$move_c_date=$value['move_c_date'];
			$move_c_word=$value['move_c_word'];
			$move_c_num=$value['move_c_num'];
			$school=$value['school'];
			$school_id=$value['school_id'];
			$stud_id=$value['stud_id'];
			$reason=$value['reason'];
			$city=$value['city'];

			switch($value['data_source'])
			{
				case '1': $data_source='本校異動紀錄(stud_move)'; break;
				case '2': $data_source='補登他校紀錄(stud_move_import)'; $edit_count++; break;			
			}
			if($value['data_source']=='1')
				$show_data.="<tr><td align='center'>$move_date</td><td align='center'>$move_kind</td><td align='center'>$stud_id</td><td align='center'>$move_year_seme</td><td align='center'>$move_c_unit</td>
					<td align='center'>$move_c_date</td><td align='center'>$move_c_word $move_c_num</td><td align='center'>$school $school_id</td><td align='center'>$reason</td><td align='center'>$city</td><td>$data_source</td></tr>";
			else $show_data.="<tr><input type='hidden' name='move_id[]' value='$move_id'><td align='center'><input type='text' name='move_date[]' value='$move_date' size=8></td><td align='center'><input type='text' name='move_kind[]' value='$move_kind' size=2></td><td><input type='text' name='stud_id[]' value='$stud_id' size=6></td>
					<td align='center'><input type='text' name='move_year_seme[]' value='$move_year_seme' size=4></td><td align='center'><input type='text' name='move_c_unit[]' value='$move_c_unit' size=8></td>
					<td align='center'><input type='text' name='move_c_date[]' value='$move_c_date' size=8></td><td align='center'><input type='text' name='move_c_word[]' value='$move_c_word' size=6> <input type='text' name='move_c_num[]' value='$move_c_num' size=8></td><td align='center'><input type='text' name='school_id[]' value='$school_id' size=6> <input type='text' name='school[]' value='$school' size=10></td> 
					<td align='center'><input type='text' name='reason[]' value='$reason' size=6></td><td align='center'><input type='text' name='city[]' value='$city' size=6></td><td>$data_source</td></tr>";
		}
		$message.="※類別代號說明：2:轉入 3:中輟復學 4:休學復學 5:畢業 6:休學 7:出國 8:調校(轉出) 9:升級 10:降級 11:死亡 12:中輟 13:新生入學 14:轉學復學 15:在家自學"; 
		//加列空白
		$append_data="<tr bgcolor='#FFCCAA'><td align='center'><input type='text' name='a_move_date' value='' size=8></td><td align='center'><input type='text' name='a_move_kind' value='' size=2></td><td><input type='text' name='a_stud_id' value='' size=6></td>
					<td align='center'><input type='text' name='a_move_year_seme' value='' size=4></td><td align='center'><input type='text' name='a_move_c_unit' value='' size=8></td>
					<td align='center'><input type='text' name='a_move_c_date' value='' size=8></td><td align='center'><input type='text' name='a_move_c_word' value='' size=6> <input type='text' name='a_move_c_num' value='' size=8></td><td align='center'><input type='text' name='a_school_id' value='' size=6> <input type='text' name='a_school' value='' size=10></td> 
					<td align='center'><input type='text' name='a_reason' value='' size=6></td><td align='center'><input type='text' name='a_city' value='' size=6></td><td><input type='submit' name='go' value='寫入新增的異動資料'></td></tr>";
		
		$show_data.="<tr bgcolor='#CAFACF'><td colspan=10 align='center'>$message</td><td align='center'><input type='hidden' name='selected_student_id' value='$selected_student_id'>".($edit_count?"<input type='submit' name='go' value='寫入修改的異動資料'>":"")."</td></tr><tr></tr>$append_data</table>";
	
		break;
	case '4':
		$show_data="<br><br><center><input type='hidden' name='stud_id' value='$selected_student_id'><input type='hidden' name='student_sn' value='$selected_student'><input type='button' name='score' value='請按此連結至 [成績補登修改 ] 模組進行補登' onclick=\"document.myform.action='../score_input_all_new/person_seme_input.php'; document.myform.submit();\"></center>";
		break;
	case '3': //社團記錄
		
		$show_club_form="
		
		 <br>
		 
		  <input type='hidden' name='club_act' value=''>
		  <input type='hidden' name='club_option' value=''>
		  
		  <input type='hidden' name='selected_student_id' value='$selected_student_id'>
		  <font color='#800000'>※補登社團記錄</font>
		  <table border='1' style='border-collapse:collapse' bordercolor='#800000'>
		    <tr bgcolor='#FFCCFF'>
		     <td align='center'>學期</td>
		     <td align='center'>社團名稱</td>
		     <td align='center'>成績(0-100分)</td>
		     <td align='center'>指導老師評語</td>
		     <td align='center'>&nbsp;</td>
			  </tr>
			  ";
			$query="select * from association where student_sn='$selected_student' order by seme_year_seme";
			$res=mysql_query($query);
			while ($row=mysql_fetch_array($res,1)) {
			 $del_mode=($row['club_sn']>0)?"<font size=2 color=red><i>校內社團</i></font>":"<input type='button' value='刪除' onclick=\"if(confirm('您確定要刪除該生的\社團:「".$row['association_name']."」記錄?')) { document.myform.club_option.value='".$row['sn']."';document.myform.club_act.value='club_delete';document.myform.submit(); } \">";
			 $dd="
		    <tr>
		     <td align='center'>".$row['seme_year_seme']."</td>
		     <td align='center'>".$row['association_name']."</td>
		     <td align='center'>".$row['score']."</td>
		     <td>".$row['description']."</td>
		     <td align='center'>".$del_mode."</td></tr>";
			   $show_club_form.=$dd;
			}  			  
			$show_club_form.="  
		    <tr>
		     <td align='center'><input type='text' name='year_seme' size='5'></td>
		     <td align='center'><input type='text' name='association_name' size='20'></td>
		     <td align='center'><input type='text' name='score' size='5'></td>
		     <td><input type='text' name='description' size='50'></td>
		     <td><input type='button' value='新增社團資料' onclick=\"if( document.myform.association_name.value!='') { document.myform.club_act.value='club_add';document.myform.submit(); } \">
			  </tr>
		  </table>
      ※說明:<br>
      1.學期請輸入學年+學期別, 如: 99學年第1學期, 則輸入 991 。<br>
      2.以此模組所補登的資料, 在社團模組內無法查得, 但成績單內可正常輸出.
   		 
		 
		";
		
		$show_data.=$show_club_form;
		
		break;
} // end switch
}
echo $main.$data_type_radio.$studentdata.$show_data."</form>";

foot();
?>
