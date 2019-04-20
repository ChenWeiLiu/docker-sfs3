<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("認證登錄");



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

$item_sn=$_POST[item_sn];
$sn=$_POST[sn];
$curr_class_id=$_POST[curr_class_id];
$selected_stud=$_POST[selected_stud];
$cancel_sn=$_POST[cancel_sn];


//橫向選單標籤
echo print_menu($MENU_P);

if($selected_stud AND $_POST['act']=='簽認註記'){
	$score_array=$_POST['score'];
	$note_array=$_POST['note'];
	//抓取選擇的班級學生
	$batch_value="";
	foreach($selected_stud as $studdent_sn)
	{
		$score=$score_array[$studdent_sn];
		$note=$note_array[$studdent_sn];
		$batch_value.="('$work_year_seme','$sn','$my_sn',curdate(),'$score','$note','$studdent_sn'),";
	}
	$batch_value=substr($batch_value,0,-1);
	
	$sql_select="INSERT INTO authentication_record(year_seme,sub_item_sn,teacher_sn,date,score,note,student_sn) values $batch_value";
	$res=$CONN->Execute($sql_select) or user_error("簽認失敗！<br>$sql_select",256);
};


if($cancel_sn AND $_POST['act']=='取消簽認'){
	foreach($cancel_sn as $csn) $batch_value.="$csn,";
	$batch_value=substr($batch_value,0,-1);
	$sql_select="DELETE FROM authentication_record WHERE sn IN ($batch_value)";
	$res=$CONN->Execute($sql_select) or user_error("刪除失敗！<br>$sql_select",256);
};

//取得認證中項目的下拉選單
$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>※認證項目：<select name='item_sn' onchange='this.form.submit()'>";
$sql_select="select * from authentication_item WHERE (CURDATE() BETWEEN start_date AND end_date) order by end_date desc";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
if(! $item_sn) $item_sn=$res->fields[sn]; //預設為第一項
while(!$res->EOF) {
	if($item_sn==$res->fields[sn]) $selected="selected"; else $selected='';
	$main.="<option $selected value={$res->fields[sn]}>{$res->fields[nature]}-{$res->fields[code]}-{$res->fields[title]} ({$res->fields[start_date]}~{$res->fields[end_date]})</option>";
	$res->MoveNext();
}
$main.="</select>";

//取得細目
if($item_sn){
	$main.="<BR>※認證細目：<select name='sn' onchange='this.form.submit()'>";
	$sql_select="select * from authentication_subitem WHERE item_sn=$item_sn";
	$res=$CONN->Execute($sql_select) or user_error("讀取細目失敗！<br>$sql_select",256);
	$grades=$res->fields[grades];
	if(! $sn) $sn=$res->fields[sn];    //預設為第一項
	while(!$res->EOF) {
		if($sn==$res->fields[sn]){
			$grades=$res->fields[grades];
			$selected="selected";
		} else $selected='';
		$main.="<option $selected value={$res->fields[sn]}>{$res->fields[code]}-{$res->fields[title]}-({$res->fields[bonus]})-({$res->fields[grades]})</option>";
		$res->MoveNext();
	}
	$main.="</select>";
}

//顯示可認證班級
if($grades)
{
	$sql_select="select * from school_class where year=".curr_year()." AND semester=".curr_seme()." AND (c_year IN ($grades)) order by class_id ";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

	$class_list="<select name='curr_class_id' onchange='this.form.submit()'><option></option>";
	while(!$recordSet->EOF)
	{
		$class_id=sprintf("%d%02d",($recordSet->fields[c_year]),($recordSet->fields[c_sort]));
		$class_name=$class_base[$class_id];
		if($curr_class_id==$class_id){
				$selected='selected';
				$show_student=1;
			} else $selected='';
			
		//導師加入班級過濾
		if($class_id==$my_class_id)	$class_list.="<option value='$class_id' $selected>$class_name</option>";
		//增加檢查被授權的項目
		$sql_empowered="select class_id,subitem_sn from authentication_empower WHERE empowered_sn=$my_sn AND year_seme='$curr_year_seme' AND subitem_sn=$sn AND class_id='$class_id'";
		$res_empowered=$CONN->Execute($sql_empowered) or user_error("讀取失敗！<br>$sql_empowered",256);
		if($res_empowered->RecordCount()) $class_list.="<option value='$class_id' $selected>$class_name</option>";
		
		$recordSet->MoveNext();
	}
	$class_list.="</select>";

	//取得前已認證學生資料
	$sql_select="select * from authentication_record where sub_item_sn=$sn order by student_sn";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$authenticated=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields[student_sn];
		$authenticated[$student_sn]['sn']=$recordSet->fields['sn'];
		$authenticated[$student_sn]['year_seme']=$recordSet->fields['year_seme'];
		$authenticated[$student_sn]['date']=$recordSet->fields['date'];
		$authenticated[$student_sn]['teacher_sn']=$recordSet->fields['teacher_sn'];
		$authenticated[$student_sn]['score']=$recordSet->fields['score'];
		$authenticated[$student_sn]['note']=$recordSet->fields['note'];
		$recordSet->MoveNext();
	}	
	$authenticated_count=count($authenticated);
	
	$main.=" ($authenticated_count)<BR>※選擇班級：$class_list <input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>";
}

if($show_student)
{
	//取得stud_base中班級學生列表
	//$stud_select="SELECT student_sn,curr_class_num,right(curr_class_num,2) as class_no,stud_name,stud_sex FROM stud_base WHERE stud_study_cond=0 AND curr_class_num like '$class_id%' ORDER BY curr_class_num";
	$stud_select="SELECT a.student_sn,a.stud_id,a.seme_num,b.stud_name,b.stud_sex FROM stud_seme a,stud_base b WHERE seme_year_seme='$curr_year_seme' and a.seme_class='$curr_class_id' and a.student_sn=b.student_sn ORDER BY seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//以checkbox呈現

	$studentdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>";
	$studentdata.="<tr bgcolor='#FFCCCC' align='center'><td width=50>座號</td><td width=120>姓名</td><td width=80>認證學期</td><td width=90><input type='submit' value='簽認註記' name='act'></td><td width=50>成績</td><td width=100>備註</td><td width=100><input type='submit' value='取消簽認' name='act'></td></tr>";
	while(list($student_sn,$stud_id,$seme_num,$stud_name,$stud_sex)=$recordSet->FetchRow()) {
		if (array_key_exists($student_sn,$authenticated)) {
			$studentdata.="<tr bgcolor='#CCCCCC' align='center'><td>$seme_num</td><td>$stud_name</td><td>{$authenticated[$student_sn]['year_seme']}</td><td>{$authenticated[$student_sn]['date']}</td><td>{$authenticated[$student_sn]['score']}</td><td>{$authenticated[$student_sn]['note']}</td><td><input type='checkbox' name='cancel_sn[]' value='{$authenticated[$student_sn]['sn']}'></td>";
		} else {
			$bgcolor=($stud_sex==1)?"#DDFFDD":"#FFDDDD";	
			$studentdata.="<tr bgcolor='$bgcolor' align='center'>
							<td>$seme_num</td><td>$stud_name</td>
							<td>$work_year_seme</td>
							<td><input type='checkbox' name='selected_stud[]' value='$student_sn'></td>
							<td><input type='text' name='score[$student_sn]' size=5></td>
							<td><input type='text' name='note[$student_sn]' size=10></td><td></td>";
		}
		$studentdata.="</tr>";
	}
	$studentdata.="</td></tr></table>";
	
}
echo $main.$studentdata."</form>";
foot();
?>