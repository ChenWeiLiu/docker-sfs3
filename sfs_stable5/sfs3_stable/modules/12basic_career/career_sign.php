<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

if($_POST['go']){
	foreach($_POST['content'] as $student_sn=>$data){
		$data=serialize($data);
		$sql_data.="('$student_sn','9-9','$data'),";
	}
	$sql_data=substr($sql_data,0,-1);
	$sql="REPLACE INTO career_self_ponder(student_sn,id,content) VALUES $sql_data";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
}

//秀出網頁
head("審閱紀錄");
echo <<<HERE
<script>

function setdata(n,y,s,name) {
  var i =0;
  var nys=n+"-"+y+"-"+s;
  var t=nys+"-teacher";
  var d=nys+"-date";
  var m=nys+"-memo";
  var now = new Date();
  if(document.getElementById('na').checked){
	document.getElementById(t).value=name;
	document.getElementById(d).value=now.toLocaleString();
	document.getElementById(m).value=document.getElementById('dm').value;
  }
}

</script>
HERE;

//模組選單
print_menu($menu_p,$linkstr);

$menu=$_POST['menu'];


if($c_id){
	//抓取既有紀錄
	$content_arr=array();
	$query="select * from career_self_ponder where student_sn in ( select student_sn from stud_seme where seme_year_seme='$curr_year_seme' and seme_class='{$c_id}') and id='9-9'";
	$res=$CONN->Execute($query) or die("SQL錯誤：<br>$query");
	while(!$res->EOF){
		$student_sn=$res->fields['student_sn'];
		$content_arr[$student_sn]=unserialize($res->fields['content']);		
		$res->MoveNext();
	}
	
	$student_select="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'><tr align='center' bgcolor='#ffffcc'><td>座號</td><td>姓名</td>";
	for($i=$min;$i<=$max;$i++) for($j=1;$j<=2;$j++){
		$student_select.="<td $java_script>$i-$j</td>";
	}
	$student_select.="</tr>";
	
	//產生學生名單
	$query="select a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_study_cond from `stud_seme` a inner join stud_base b on a.student_sn=b.student_sn where seme_year_seme='$curr_year_seme' and seme_class='{$c_id}' order by a.seme_num";
	$res=$CONN->Execute($query) or die("SQL錯誤：<br>$query");
	while(!$res->EOF){
		$student_sn=$res->fields['student_sn'];
		$color=($res->fields['stud_sex']==1)?'#ccffcc':'#ffcccc';
		$color=($res->fields['stud_study_cond'])?'#aaaaaa':$color;
		$student_select.="<tr bgcolor='$color' align='center'><td>{$res->fields['seme_num']}</td><td>{$res->fields['stud_name']}</td>";
		for($i=$min;$i<=$max;$i++) for($j=1;$j<=2;$j++){
			$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ccccff';\" onMouseOut=\"this.style.backgroundColor='$color';\" ondblclick='setdata($student_sn,$i,$j,\"".$_SESSION['session_tea_name']."\");'";
			//$student_select.="<td>$i-$j</td>";
			$seme_key="$i-$j";
			$student_select.="<td align='left' $java_script>
				<img src='./images/t.png' alt='簽記教師'> <input type='text' size=16 name='content[$student_sn][$seme_key][teacher]' id='$student_sn-$seme_key-teacher' value='{$content_arr[$student_sn][$seme_key]['teacher']}' style='background-color:#ffeeff;' readonly><br>
				<img src='./images/d.png' alt='簽記時刻'> <input type='datetime' size=16 name='content[$student_sn][$seme_key][date]'  id='$student_sn-$seme_key-date' value='{$content_arr[$student_sn][$seme_key]['date']}' style='background-color:#ffeeff;' readonly><br>
				<img src='./images/m.png' alt='備註'> <input type='text' size=16 name='content[$student_sn][$seme_key][memo]'  id='$student_sn-$seme_key-memo' value='{$content_arr[$student_sn][$seme_key]['memo']}' style='background-color:#ffeeff;' readonly>
				</td>";
		}
		$student_select.="</tr>";
	
		$res->MoveNext();
	}
	$student_select.="</table>";

}

$checked=$_POST['dm']?'checked':'';
$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>$class_select ◎預設備註：<input type='text' name='dm' id='dm' value='{$_POST['dm']}'> <input type='checkbox' id= 'na' name='na' value=1 $checked>允許雙擊寫入資料 <input type='submit' name='go' value='寫入簽記資料'>
<br>$student_select $showdata<br>$act</form></font>";

echo $main;

foot();

?>
