<?php

include "config.php";

sfs_check();

//秀出網頁
head("參與免試學生");

print_menu($menu_p);
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


//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

$stud_class=$_REQUEST['stud_class'];
$selected_stud=$_POST['selected_stud'];

//取出班級名稱陣列
$class_base=class_base($work_year_seme);

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

if($selected_stud AND $_POST['act']=='開列選擇的學生'){
	//抓取選擇的班級學生
	$batch_value="";
	foreach($selected_stud as $key=>$sn)
	{
		$batch_value.="('$academic_year',$sn,$session_tea_sn),";
	}
	$batch_value=substr($batch_value,0,-1);
	$sql="REPLACE INTO 12basic_ptc(academic_year,student_sn,update_sn) values $batch_value";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
}


//刪除參加學生
if($_POST['student_sn']) {
	$sql="delete from 12basic_ptc WHERE academic_year='$academic_year' and student_sn=".$_POST['student_sn'];
	$rs=$CONN->Execute($sql) or user_error("錯誤訊息：",$sql,256);
}


if($_POST['act']=='開列本學年所有的學生'){
	//抓班級學生
	$sql_select="SELECT a.student_sn FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND b.stud_study_cond=0 AND a.seme_class like '$graduate_year%'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	
	$batch_value="";
	while(!$recordSet->EOF)
	{
		$sn=$recordSet->fields['student_sn'];
		$batch_value.="('$academic_year',$sn,$session_tea_sn),";
		$recordSet->MoveNext();
	}
	$batch_value=substr($batch_value,0,-1);
	
	$sql="INSERT INTO 12basic_ptc(academic_year,student_sn,update_sn) values $batch_value";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
};

if($_POST['act']=='撤除') {
	$stud_select="SELECT a.student_sn FROM 12basic_ptc a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year' AND b.stud_study_cond NOT IN (0,5,15)";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	$sn_list='';
	while(! $recordSet->EOF) {
		$sn_list.=$recordSet->fields[0].",";
		$recordSet->MoveNext();
	}
	$sn_list=substr($sn_list,0,-1);
	if($sn_list) $CONN->Execute("DELETE FROM 12basic_ptc WHERE student_sn IN ($sn_list)") or user_error("讀取失敗！<br>$stud_select",256);
}


//取得年度與學期的下拉選單
//$seme_list=get_class_seme();
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

//取得指定學年已經開列的學生清單
$listed=get_student_list($work_year);

//參與圖示
$ok_pic="<img src='./images/ok.png' width=14>";


//顯示非在籍卻有參與免試的學生
$stud_select="SELECT a.student_sn,b.curr_class_num,b.stud_name,b.stud_sex,b.stud_study_cond FROM 12basic_ptc a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year' AND b.stud_study_cond not in (0,5,15) ORDER BY b.curr_class_num";
$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
if($recordSet->RecordCount()) {
	//學籍狀態
	$study_cond=study_cond();
	
	$abnormal="<br><div style='width:95%; box-shadow:2px 2px 3px grey; float:left; border:1px #cccccc solid; padding:10px 10px 10px 10px; background-color: #aaaacc; color: white; border-radius: 10px;'>
		系統提示：下列為設定參與本年度免試入學但已經不在籍也非畢業的學生！";
	while(list($student_sn,$curr_class_num,$stud_name,$stud_sex,$stud_study_cond)=$recordSet->FetchRow()) {
			$abnormal.="<li>$curr_class_num $stud_name {$study_cond[$stud_study_cond]}</li>";
	}
	$abnormal.="<input type='submit' value='撤除' name='act' onclick='return confirm(\"確定要撤除表列所有學生的設定？\")'></div>";
}

if($stud_class and $work_year_seme==$curr_year_seme){
	$tool_icon.="<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>　";
	$tool_icon.="<font size=2>　{$ok_pic}：已開列(快按兩下可撤除)　　</font>";
	$tool_icon.="<input type='submit' value='開列選擇的學生' name='act'>";
	if(!$listed) $tool_icon.="<input type='submit' value='開列本學年所有的學生' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";
	if($abnormal) $tool_icon.=$abnormal;
}
$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><input type='hidden' name='student_sn' value=''>$recent_semester $class_list $tool_icon <table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year,b.stud_study_cond FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//以checkbox呈現
	$col=7; //設定每一列顯示幾人

	$studentdata="";
	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year,$stud_study_cond)=$recordSet->FetchRow()) {
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		if($recordSet->currentrow() % $col==1) $studentdata.="<tr align='center'>";
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		if(array_key_exists($student_sn,$listed)) {
			$dbl_data=$remove_alarm?"if(confirm(\"確定要撤銷 ($seme_num)$stud_name ？\")) { document.myform.student_sn.value=\"$student_sn\"; document.myform.submit(); }":"document.myform.student_sn.value=\"$student_sn\"; document.myform.submit();";
			//if(confirm(\"確定要撤銷 $stud_name ？\") { document.myform.student_sn.value=\"$student_sn\"; }":"document.myform.student_sn.value=\"$student_sn\"; document.myform.submit();";
			$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ff5555';\" onMouseOut=\"this.style.backgroundColor='#FFFFDD';\" ondblclick='$dbl_data'";
			//$stud_cond=$stud_study_cond?"({$study_cond[$stud_study_cond]})":"-";
			$studentdata.="<td bgcolor='#FFFFDD' $java_script>$my_pic $ok_pic ($seme_num)$stud_name</td>";
		} else {
			$checkable=($curr_year_seme==$work_year_seme)?"<input type='checkbox' name='selected_stud[]' value='$student_sn'>":"";
			$studentdata.="<td bgcolor='$stud_sex_color'>$my_pic $checkable($seme_num)$stud_name</td>";
		}
		if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
	}
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';


echo "$main $studentdata  </table></form>";



foot();?>