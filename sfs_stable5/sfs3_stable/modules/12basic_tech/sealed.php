<?php

include "config.php";

sfs_check();

//秀出網頁
head("學生資料封存");

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

if($selected_stud AND $_POST['act']=='封存選擇的學生'){
	//抓取選擇的班級學生
	foreach($selected_stud as $key=>$sn)
	{
		$sql="UPDATE 12basic_tech SET editable='0' WHERE academic_year='$academic_year' and student_sn='$sn'";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	}
}

//解除封存學生
if($_POST['student_sn']) {
	$sql="UPDATE 12basic_tech SET editable='1' WHERE academic_year='$academic_year' and student_sn='{$_POST['student_sn']}'";
	$rs=$CONN->Execute($sql) or user_error("錯誤訊息：",$sql,256);
}


if($_POST['act']=='封存本學年所有的學生'){
	$sql="UPDATE 12basic_tech SET editable='0' WHERE academic_year='$work_year'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
};

if($_POST['act']=='全數解除封存'){
	$sql="UPDATE 12basic_tech SET editable='1' WHERE academic_year='$work_year'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
};


//取出班級名稱陣列
$class_base=class_base($work_year_seme);

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
//$seme_list=get_class_seme();
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

//取得指定學年已經開列的學生清單
$listed=get_student_list($work_year);

//已封存代表圖
$sealed_pic="<img src='./images/sealed.png' width=16>";


if($stud_class and $work_year_seme==$curr_year_seme){
	$tool_icon.="<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>　";
	$tool_icon.="<font size=2>　{$sealed_pic}：已封存　</font>";
	$tool_icon.="<input type='submit' value='封存選擇的學生' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";
	$tool_icon.=" <input type='submit' value='封存本學年所有的學生' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";
	$tool_icon.=" <input type='submit' value='全數解除封存' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";
}
$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><input type='hidden' name='student_sn' value=''>$recent_semester $class_list $tool_icon <table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);
	
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//以checkbox呈現
	$col=7; //設定每一列顯示幾人
	
	$studentdata="";
	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year)=$recordSet->FetchRow()) {
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		if($recordSet->currentrow() % $col==1) $studentdata.="<tr align='center'>";
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		if(array_key_exists($student_sn,$listed)) {
			$sealed=array_key_exists($student_sn,$editable_sn_array)?0:1;
			$stud_sex_color=$sealed?$uneditable_bgcolor:$stud_sex_color;
		
			if($work_year==$academic_year and $sealed) {
				$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ff5555';\" onMouseOut=\"this.style.backgroundColor='$uneditable_bgcolor';\" ondblclick='document.myform.student_sn.value=\"$student_sn\"; document.myform.submit();'";
				$studentdata.="<td bgcolor='$uneditable_bgcolor' $java_script>$my_pic ($seme_num)$stud_name $sealed_pic</td>";
			} else {
				$checkable=($curr_year_seme==$work_year_seme)?"<input type='checkbox' name='selected_stud[]' value='$student_sn'>":"";
				$studentdata.="<td bgcolor='$stud_sex_color'>$my_pic $checkable($seme_num)$stud_name</td>";				
			}
		} else {
			$studentdata.="<td bgcolor='#cccccc'>$my_pic ($seme_num)$stud_name</td>";
		}
		if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
	}
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."</form></table>";
foot();?>