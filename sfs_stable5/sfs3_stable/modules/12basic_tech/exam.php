<?php
include "config.php";

sfs_check();

//秀出網頁
head("參與免試學生");

print_menu($menu_p);

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

$stud_class=$_REQUEST['stud_class'];
$edit_sn=$_POST['edit_sn'];

if($_POST['act']=='確定修改'){
	//$_POST['edit_score_exam_c']=min($_POST['edit_score_exam_c'],$exam_score_well);
	//$_POST['edit_score_exam_m']=min($_POST['edit_score_exam_m'],$exam_score_well);
	//$_POST['edit_score_exam_e']=min($_POST['edit_score_exam_e'],$exam_score_well);
	//$_POST['edit_score_exam_s']=min($_POST['edit_score_exam_s'],$exam_score_well);
	//$_POST['edit_score_exam_n']=min($_POST['edit_score_exam_n'],$exam_score_well);
	//$_POST['edit_score_exam_w']=min($_POST['edit_score_exam_w'],$exam_score_well);
	
	$sql="UPDATE 12basic_tech SET score_exam_c='{$_POST['edit_score_exam_c']}',score_exam_m='{$_POST['edit_score_exam_m']}',score_exam_e='{$_POST['edit_score_exam_e']}',score_exam_s='{$_POST['edit_score_exam_s']}',score_exam_n='{$_POST['edit_score_exam_n']}',score_exam_w='{$_POST['edit_score_exam_w']}',exam_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
//echo $sql;	
	$edit_sn=0;
}


if($_POST['act']=='清除所有教育會考成績'){
	$sql="UPDATE 12basic_tech SET score_exam_c=NULL,score_exam_m=NULL,score_exam_e=NULL,score_exam_s=NULL,score_exam_n=NULL,exam_memo=NULL WHERE academic_year=$work_year AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("清除失敗！<br>$sql",256);
	$edit_sn=0;
}

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//2015.05.05 by smallduh

if($_POST['act']=='傳送准考證號碼'){
	$data_array=explode("\n",$_POST['arr_data']);
 	
 $i=0;	
 foreach ($data_array as $a) {
 	$data_arr=explode("\t",$a);
 	
 	$seme_class="9".$data_arr[1];
 	$seme_num=$data_arr[2];
 	$seme_num=$seme_num*1;
 	$acad_exam_reg_num=trim($data_arr[4]);
 	
 	$sql="select student_sn from stud_seme where seme_year_seme='$curr_year_seme' and seme_class='$seme_class' and seme_num=$seme_num";
 	$res=$CONN->Execute($sql) or die("error! sql=".$sql);
  
  
  if ($res->recordcount()) {
    $student_sn=$res->fields['student_sn'];
    $sql_select="select * from 12basic_tech where academic_year='$academic_year' and student_sn='$student_sn'";
    $res_select=$CONN->Execute($sql_select);
    if ($res_select->recordcount()) {
     $sql_update="update 12basic_tech set acad_exam_reg_num='$acad_exam_reg_num' where academic_year='$academic_year' and student_sn='$student_sn'";
     $res_update=$CONN->Execute($sql_update) or die("更新准考證發生錯誤 ! sql=".$sql_update);
     echo $seme_class."班 ".$seme_num." ->".$student_sn." 已更新!<br>";
     $i++;
    }
  }
   
    
 } // end foreach
 echo "<br>本次共更新".$i."學生的准考證號碼";
 exit();
}

if($_POST['act']=='匯入會考准考證'){
  //顯示封存狀態資訊
  echo get_sealed_status($work_year).'<br>';
  echo "<font color=blue size=3><img src='./images/on.png' height=12>匯入准考證號碼<br>";
  echo "<img src='./images/on.png' height=12>比對班級座號目標：".$work_year."學年的三年級學生</font><br>";
  ?>
  <form name='myform' method='post' action='<?php echo $_SERVER[PHP_SELF];?>'>
   <textarea name="arr_data" cols="70" rows="10"></textarea><br>
   <input type="submit" value="傳送准考證號碼" name="act">
   <input type="button" value="取消" onclick="window.location='exam.php'"><br>
   <font size="2">
   ※說明:<br>
   1.學校替學生報名會考後，會考中心會提供各校一個包括:報名序號、班級、座號、考生姓名、准考證號碼等欄位的 Excel 檔。<br>
   2.請開啟該檔，然後選擇要匯入准考證號碼的學生，如圖所示，接著按右鍵「複製」。<br>
   <img src="./images/up_acad_exam_reg_num.png"><br>
   3.貼上該資料後，按下【傳送准考證號碼】。<br>
   </font>
  </form>
  <?php
  foot();
  exit();
}



//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

if($work_year==$academic_year) $tool_icon=" <input type='submit' value='清除所有教育會考成績' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'><input type='submit' value='匯入會考准考證' name='act'>";
//<font size=1 color='red'>※請注意：本模組的教育會考原始成績抓取生涯輔導紀錄的教育會考成績紀錄！</font><br>
$main.="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon
<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//取得前已開列學生資料
	$student_list_array=get_student_list($work_year);
	
	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);
	
	//取得會考積分
	$exam_data=get_exam_data($work_year);
	
	//取得會考成績
	//$exam_=get_exam_score($work_year);
	
	if(!$_POST['edit_write'] and $work_year==$academic_year) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ff8888';\" ondblclick='document.myform.edit_write.value=1; document.myform.submit();'";
	elseif($_POST['edit_write']) $ok="<input type='submit' name='act' value='確定修改'  onclick='return confirm(\"確定要修改寫作測驗級分?\")'>";
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);	
	$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td width=$pic_width>大頭照</td><td>准考證號</td><td>國文</td><td>數學</td><td>英語</td><td>社會</td><td>自然</td><td>級分統計</td><td>寫作測驗</td><td>備註</td>";
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$seme_num=$recordSet->fields['seme_num'];
		$stud_name=$recordSet->fields['stud_name'];
		$stud_sex=$recordSet->fields['stud_sex'];
		$stud_id=$recordSet->fields['stud_id'];
		$stud_study_year=$recordSet->fields['stud_study_year'];
		
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		$stud_sex_color=array_key_exists($student_sn,$student_list_array)?$stud_sex_color:'#aaaaaa';
		
		//准考證號碼
		$acad_exam_reg_num=$exam_data[$student_sn]['acad_exam_reg_num'];
		
		$c=$exam_data[$student_sn]['score_exam_c']; $score_exam_c=$c?"$exam_level_description[$c] ($exam_level[$c])":'';
		$m=$exam_data[$student_sn]['score_exam_m']; $score_exam_m=$m?"$exam_level_description[$m] ($exam_level[$m])":'';
		$e=$exam_data[$student_sn]['score_exam_e']; $score_exam_e=$e?"$exam_level_description[$e] ($exam_level[$e])":'';
		$s=$exam_data[$student_sn]['score_exam_s']; $score_exam_s=$c?"$exam_level_description[$s] ($exam_level[$s])":'';
		$n=$exam_data[$student_sn]['score_exam_n']; $score_exam_n=$n?"$exam_level_description[$n] ($exam_level[$n])":'';
		
		$w=$exam_data[$student_sn]['score_exam_w']; $score_exam_w=$w?$w."級分":'';
		//備註
		$memo=$exam_data[$student_sn]['exam_memo'];
		//級分統計
		$score=$exam_data[$student_sn]['bonus'];
		//最大值限定
		$score=min($score,$exam_score_max);
		
		$java_script="";
		$action='';
		if($student_sn==$edit_sn){			
			//教育會考
			//$score_exam_c="<input type='text' name='edit_score_exam_c' size=5 value='$score_exam_c'>";
			$score_exam_c="<select name='edit_score_exam_c'><option value=''>";
			foreach($exam_level as $key=>$value){
				$selected=($key==$c)?'selected':'';
				$description=$exam_level_description[$key];
				$score_exam_c.="<option value='$key'$selected>$description ($value)";
			}
			$score_exam_c.="</select>";				

			//$score_exam_m="<input type='text' name='edit_score_exam_m' size=5 value='$score_exam_m'>";
			$score_exam_m="<select name='edit_score_exam_m'><option value=''>";
			foreach($exam_level as $key=>$value){
				$selected=($key==$m)?'selected':'';
				$description=$exam_level_description[$key];
				$score_exam_m.="<option value='$key'$selected>$description ($value)";
			}
			$score_exam_m.="</select>";

			//$score_exam_e="<input type='text' name='edit_score_exam_e' size=5 value='$score_exam_e'>";
			$score_exam_e="<select name='edit_score_exam_e'><option value=''>";
			foreach($exam_level as $key=>$value){
				$selected=($key==$e)?'selected':'';
				$description=$exam_level_description[$key];
				$score_exam_e.="<option value='$key'$selected>$description ($value)";
			}
			$score_exam_e.="</select>";

			//$score_exam_s="<input type='text' name='edit_score_exam_s' size=5 value='$score_exam_s'>";
			$score_exam_s="<select name='edit_score_exam_s'><option value=''>";
			foreach($exam_level as $key=>$value){
				$selected=($key==$s)?'selected':'';
				$description=$exam_level_description[$key];
				$score_exam_s.="<option value='$key'$selected>$description ($value)";
			}
			$score_exam_s.="</select>";

			//$score_exam_n="<input type='text' name='edit_score_exam_n' size=5 value='$score_exam_n'>";
			$score_exam_n="<select name='edit_score_exam_n'><option value=''>";
			foreach($exam_level as $key=>$value){
				$selected=($key==$n)?'selected':'';
				$description=$exam_level_description[$key];
				$score_exam_n.="<option value='$key'$selected>$description ($value)";
			}
			$score_exam_n.="</select>";
			
			//$score_exam_w="<input type='text' name='edit_score_exam_w' size=5 value='$score_exam_w'>";
			$score_exam_w="<select name='edit_score_exam_w'><option value=''>";
			for($i=1;$i<=$exam_score_write;$i++){
				$selected=($i==$w)?'selected':'';
				$description=$i."級分";
				$score_exam_w.="<option value='$i'$selected>$description";
			}
			$score_exam_n.="</select>";			
			
			$memo="<input type='text' name='edit_memo' size=20 value='$memo'>";
			$stud_sex_color='#ffffaa';
			$score='';
			//動作按鈕
			$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的教育會考級分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
		} else {
			if(array_key_exists($student_sn,$student_list_array)){
				$editable=array_key_exists($student_sn,$editable_sn_array)?1:0;
				$stud_sex_color=$editable?$stud_sex_color:$uneditable_bgcolor;
				$java_script=($work_year==$academic_year and $editable)?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'":'';
			} else { $stud_sex_color='#aaaaaa'; }
		}		
		
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$my_pic</td><td>$acad_exam_reg_num</td><td>$score_exam_c</td><td>$score_exam_m</td>
		<td>$score_exam_e</td><td>$score_exam_s</td><td>$score_exam_n</td><td><b>$score</b></td><td>$score_exam_w</td><td>$memo $action</td></tr>";
		
		$recordSet->MoveNext();
	}
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."<input type='hidden' name='edit_write' value=0></form></table>";
foot();
?>