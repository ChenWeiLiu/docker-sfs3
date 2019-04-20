<?php
// $Id: $

include "config.php";

sfs_check();

//秀出網頁
head("認證登錄");

echo <<<HERE
<script>
function tagall(item,status) {
  var i =0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name==item) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}


function check_select() {
  var i=0; k=0; answer=true;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].checked) {
		if(document.myform.elements[i].name=='subitem_sn[]') k++;
    }
    i++;
  }
  if(k==0) { alert("尚未選取認證細目！"); answer=false; }
  
  return answer;
}

</script>
HERE;

$item_sn=$_POST[item_sn];
$subitem_sn_arr=$_POST[subitem_sn];
$curr_class_id=$_POST[curr_class_id];
$curr_class_grade=substr($curr_class_id,0,-2);
$student_sn=$_POST[student_sn];
$cancel_sn=$_POST[cancel_sn];
$go_caption='簽認註記';

//橫向選單標籤
echo print_menu($MENU_P);

if($_POST['act']==$go_caption){
	//抓取項目
	$score_array=$_POST['score'];
	$note_array=$_POST['note'];
	$batch_value="";
	foreach($subitem_sn_arr as $key=>$sn){
		$score=$score_array[$sn];
		$note=$note_array[$sn];
		$batch_value.="('$work_year_seme','$sn','$my_sn',curdate(),'$score','$note','$student_sn'),";
	}
	$batch_value=substr($batch_value,0,-1);
	
	//抓取選擇的班級學生
	$sql="INSERT INTO authentication_record(year_seme,sub_item_sn,teacher_sn,date,score,note,student_sn) values $batch_value";
	$res=$CONN->Execute($sql) or user_error("簽認失敗！<br>$sql",256);
}

if($cancel_sn){
	$sql="DELETE FROM authentication_record WHERE sn=$cancel_sn";
	$res=$CONN->Execute($sql) or user_error("刪除失敗！<br>$sql",256);
};

$sql_select="select * from school_class where year=".curr_year()." AND semester=".curr_seme()." order by class_id ";
$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

$class_list="※選擇班級：<select name='curr_class_id' onchange='this.form.submit()'><option></option>";
while(!$recordSet->EOF)
{
	$class_id=sprintf("%d%02d",($recordSet->fields[c_year]),($recordSet->fields[c_sort]));
	$class_name=$class_base[$class_id];
	if($curr_class_id==$class_id){
		$selected='selected';
		$show_student=1;
	} else $selected='';
	$class_list.="<option value='$class_id' $selected>$class_name</option>";
	$recordSet->MoveNext();
}
$class_list.="</select>";

if($show_student)
{
	//取得stud_base中班級學生列表
	$stud_select="SELECT a.student_sn,a.stud_id,a.seme_num,b.stud_name,b.stud_sex FROM stud_seme a,stud_base b WHERE seme_year_seme='$curr_year_seme' and a.seme_class='$curr_class_id' and a.student_sn=b.student_sn ORDER BY seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//以radio呈現
	//<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(\"student_sn[]\",1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(\"student_sn[]\",0);'>
	$studentdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111'><tr bgcolor='#ffcccc'><td>";
	while(list($student_sn,$stud_id,$seme_num,$stud_name,$stud_sex)=$recordSet->FetchRow()) {
			$recno++;
			$bgcolor=($stud_sex==1)?"#DDFFDD":"#FFDDDD";
			$seme_num=sprintf("%02d",$seme_num);
			$checked=($student_sn==$_POST['student_sn'])?'checked':'';
			if($checked) $target_student="($seme_num)$stud_name";
			$studentdata.="<input type='radio' name='student_sn' value='$student_sn' onclick=\"this.form.submit();\" $checked>($seme_num)$stud_name";
			if($recno % 10 ==0) $studentdata.="<br>";
	}
	$studentdata.="</td></tr></table>";	
}

if($_POST['student_sn']){
	//取得認證中項目的下拉選單
	$item_select="<br>※認證項目：<select name='item_sn' onchange='this.form.submit()'>";
	$sql_select="select * from authentication_item WHERE room_id=$my_room_id AND (CURDATE() BETWEEN start_date AND end_date) order by end_date desc";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	if(!$item_sn) $item_sn=$res->fields[sn]; //預設為第一項
	while(!$res->EOF) {
		if($item_sn==$res->fields[sn]) $selected="selected"; else $selected='';
		$item_select.="<option $selected value={$res->fields[sn]}>{$res->fields[nature]}-{$res->fields[code]}-{$res->fields[title]} ({$res->fields[start_date]}~{$res->fields[end_date]})</option>";
		$res->MoveNext();
	}
	$item_select.="</select>";
	
	//取得細目
	$subitem_data="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111'>
	<tr bgcolor='#ccccff' align='center'><td><input type='checkbox' onclick='tagall(\"subitem_sn[]\",this.checked)'></td><td>代號</td><td>細目名稱</td><td>適用年級</td><td>得點</td><td>認證日期</td><td>認證者</td><td>成績</td><td>備註</td></tr>";
	$sql_select="select * from authentication_subitem WHERE item_sn=$item_sn";
	$res=$CONN->Execute($sql_select) or user_error("讀取細目失敗！<br>$sql_select",256);
	while(!$res->EOF) {
		//sn item_sn code title grades bonus cooperate
		$grades=' ,'.$res->fields[grades].',';
		if(strpos($grades,",$curr_class_grade,")){
			$sn=$res->fields[sn];
			$bonus=$res->fields[bonus];
			$title=$res->fields[title];
			$code=$res->fields[code];
			//取得前已認證學生資料
			$sql="select * from authentication_record where sub_item_sn=$sn and student_sn={$_POST['student_sn']}";
			$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
			if($rs->recordcount()){
				$record_sn=$rs->fields['sn'];
				$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaffaa';\" onMouseOut=\"this.style.backgroundColor='#cccccc';\" ondblclick='if(confirm(\"確定要撤除 $target_student 的 ($code)$title 認證簽記？\")) { document.myform.cancel_sn.value=$record_sn; document.myform.submit(); }'";
				$subitem_data.="<tr bgcolor='#cccccc' align='center' $java_script><td>★</td><td>$code</td><td>$title</td><td>{$res->fields[grades]}</td><td>$bonus</td><td>{$rs->fields[date]}</td><td>{$teacher_array[$rs->fields[teacher_sn]]}</td><td>{$rs->fields[score]}</td><td>{$rs->fields[note]}</td></tr>";
			} else $subitem_data.="<tr align='center'><td><input type='checkbox' name='subitem_sn[]' value='$sn'></td><td>$code</td><td>$title</td><td>{$res->fields[grades]}</td><td>$bonus</td><td></td><td></td><td><input type='text' name='score[$sn]' size=5></td>
							<td><input type='text' name='note[$sn]' size=20></td></tr>";
		}
		$res->MoveNext();
	}
	$subitem_data.="<tr align='center'><td colspan=9>
	<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:16px; width:100%;' value='$go_caption' name='act' onclick='return check_select();'>
	</td></tr></table>";
}

echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><input type='hidden' name='cancel_sn' value=''>$class_list $studentdata $item_select $subitem_data</form>";
foot();
?>