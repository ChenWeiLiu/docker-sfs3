<?php
// $Id: quick_input_m.php 8418 2015-05-12 02:10:21Z smallduh $
/*引入學務系統設定檔*/
include "config.php";
include_once "../../include/sfs_case_subjectscore.php";

//引入函數
include "./my_fun.php";

//顯示欄數
$col_num = 5;
$signBtn = "登錄成績";

//使用者認證
sfs_check();

$edit=$_GET['edit'];
$teacher_course=$_GET['teacher_course'];
$class_id=$_GET['class_id'];
$ss_id=($_GET['ss_id']!="")?$_GET['ss_id']:$_POST['ss_id'];
$curr_sort=($_GET['curr_sort']!="")?$_GET['curr_sort']:$_POST['curr_sort'];
$score_semester=$_POST['score_semester'];
$test_kind=$_POST['test_kind'];

//全學期平時成績取第一階段
if ($curr_sort==254) $curr_sort=1;


//平時成績處理
/***************************************************************************************/
//  將teacher_id 轉成 teacher_sn 再確定老師的平時成績紀錄資料表，命名規則是score_nor_加上teacher_sn
$teacher_id=$_SESSION['session_log_id'];
$query="select teacher_sn from teacher_base where teach_id='$teacher_id'";
$res=$CONN->Execute($query);
$teacher_sn=$res->fields["teacher_sn"];
$score_nor="score_nor_".$teacher_sn;

//取得正確的任教科目
$course_arr_all=get_teacher_course(curr_year(),curr_seme(),$teacher_sn,$is_allow);

//檢查是否為任教科目
if ($course_arr_all[course][$teacher_course]) {
?>
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<head>
<title>成績輸入</title>
</head>
<body onLoad="setTimeout('set_default();',500);">
<form name="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
<div id="mytitle"></div>
<?php
if ($edit=='s1'){
	$test_kind ="定期評量";
	$test_kind_name ="定期評量";
} elseif (($edit=='s2')&&($curr_sort!=255)){
	$test_kind ="平時成績";
	$test_kind_name ="平時成績";
} else {
	$test_kind ="全學期";
	$test_kind_name ="全學期";
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$score_semester="score_semester_".$sel_year."_".$sel_seme;

//取得排除名單
$student_out=get_manage_out($sel_year,$sel_seme);

//顯示成績輸入頁面
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
if(strstr($teacher_course,'g')){
	$query = "select class_year from score_ss where ss_id='$ss_id'";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	$class_year= $res->fields['class_year'];
	$teacher_course_arr=explode("g",$teacher_course);
	$query="select a.*,b.stud_name,b.curr_class_num,b.stud_study_year,b.stud_id from elective_stu a,stud_base b where a.student_sn=b.student_sn and a.group_id='$teacher_course_arr[0]' and b.stud_study_cond in ($in_study) order by b.curr_class_num";
}else{
	if ($class_id) $class_arr=class_id_2_old($class_id);
	$class_year=$class_arr[3];
	$query="select a.*,b.stud_name,b.curr_class_num,b.stud_study_year,b.stud_id from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_arr[2]' and b.stud_study_cond in ($in_study) order by a.seme_num";
}
$rs=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
echo "<table bgcolor=\"#000000\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
$ii =0;
while (!$rs->EOF){
	$sn = $rs->fields[student_sn];
	$sit_num = (strstr($teacher_course,'g'))?intval(substr($rs->fields[curr_class_num],-4,2))."-".intval(substr($rs->fields[curr_class_num],-2)):substr($rs->fields[curr_class_num],-2);
	$student_sn = $rs->fields[student_sn];
	$stud_name = $rs->fields[stud_name];
	//排除名單加註*
  $stud_name.=($student_out[$student_sn])?"<font color=red>*</font>":"";

	$stud_id=$rs->fields[stud_id];
	$stud_study_year=$rs->fields[stud_study_year];
	
	$query="select * from $score_semester where student_sn='$sn' and ss_id='$ss_id' and test_sort='$curr_sort' and test_kind='$test_kind'";
	$rs2=$CONN->Execute($query);
	$test_score = $rs2->fields[score];
	if($test_score == -100 or $test_score =='0')
	$test_score='';
	if($ii % $col_num == 0)	echo "<tr bgcolor=\"white\">";
	if($pic_checked) {
		//印出照片
		$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$stud_id; 
		$img_link=$UPLOAD_URL."photo/student/".$stud_study_year."/".$stud_id;			
		if (file_exists($img)) $pic_data="<img src='$img_link' width=$pic_width>"; else $pic_data="---";
		echo "<td bgcolor=#e3f9ef>$pic_data</td>";
		echo "<td bgcolor=#ffcbfb align='center'>$sit_num.<br>$stud_name<br><input type=\"text\" name=\"s_$sn\" id=\"sc_$ii\" size=6 maxlength=5 value=\"$test_score\" onFocus=\"set_ower(this,$ii)\" onBlur=\"unset_ower(this)\"></td>";
		echo "<td></td>";
	} else {
		echo "<td bgcolor=#e3f9ef>$sit_num</td>";
		echo "<td bgcolor=#ffcbfb>$stud_name</td>";
		echo "<td><br><input type=\"text\" name=\"s_$sn\" id=\"sc_$ii\" size=6 maxlength=5 value=\"$test_score\" onFocus=\"set_ower(this,$ii)\" onBlur=\"unset_ower(this)\"></td>";
	}
	if($ii++ % $col_num == ($col_num-1)) echo "</tr>\n";
	$rs->MoveNext();
}
if ($ii % $col_num != 0) echo "<td bgcolor=\"#F0F0F0\" colspan=\"".(($col_num - ($ii % $col_num))*3)."\">&nbsp;</td></tr>";
echo "</table>";

} else {
	echo "非任教科目<br>";
}
?>
<input type="button" name="do_key" value="<?php echo $signBtn ?>" onClick="apply_sc();self.parent.closeThickbox();">
<input type="button" name="go_away" value="放棄" onClick="self.parent.closeThickbox();">
<input type="button" name="reset_allBtn" value="清空" onClick="reset_all()">
<input type="hidden" name="ss_id" value="<?php echo $ss_id ?>">
<input type="hidden" name="curr_sort" value="<?php echo $curr_sort ?>">
<input type="hidden" name="test_kind" value="<?php echo $test_kind ?>">
<input type="hidden" name="teacher_course" value="<?php echo $teacher_course ?>">
</form>

<script>
var ss=0;
var is_change = false;
self.parent.document.getElementById('TB_closeAjaxWindow').innerHTML="";
function set_default(){
	document.getElementById('sc_0').focus();
}

function check_change(){
	if(is_change){
		if (confirm('您已經更改資料是否要離開 ?'))
			window.close();
	} else
		window.close();
	}

function set_ower(thetext,ower) {
	ss=ower;
	thetext.style.background = '#FFFF00';
	//thetext.select();
	return true;
}

function unset_ower(thetext) {
	if(thetext.value>100){ thetext.style.background = '#FF0000'; alert("輸入成績高於100分");}
	else if(thetext.value<0){ thetext.style.background = '#AA5555'; alert("輸入成績為負數"); }
	else { thetext.style.background = '#FFFFFF'; }
	return true;
}

function apply_sc() {
	var i =0;
	var b;

	while (i < document.myform.elements.length)  {
		a=document.myform.elements[i].id.substring(0,3);
		if (a=='sc_') {
			b=document.myform.elements[i].name;
			self.parent.document.getElementById(b).value=document.myform.elements[i].value;
		}
		i++;
	}
}

function reset_all() {
	for (var i=0;i<document.myform.elements.length;i++) {
		var e = document.myform.elements[i];
		if (e.type == 'text') e.value = '';
	}
	document.myform.elements[0].focus();
}

// handle keyboard events
if (navigator.appName == "Mozilla")
	document.addEventListener("keyup",keypress,true);
else if (navigator.appName == "Netscape")
	document.captureEvents(Event.KEYPRESS);
if (navigator.appName != "Mozilla")
	document.onkeypress=keypress;

function keypress(e) {
	if (navigator.appName == "Microsoft Internet Explorer")
		tmp = window.event.keyCode;
	else if (navigator.appName == "Navigator")
		tmp = e.which;
	else if (navigator.appName == "Navigator" || navigator.appName == "Netscape")
		tmp = e.keyCode;
	if(document.myform.elements[ss].type != 'text')
		return true;
	else if (tmp == 13){
		var tt = parseFloat(document.myform.elements[ss].value);
		if (isNaN(tt) || tt >100 || tt < 0 ){
			alert('錯誤的分數!');
			document.myform.elements[ss].value ='';
			return false;
		} else {
			ss++;
			document.myform.elements[ss].focus();
			is_change = true;
			return true;
		}
	} else 
	return true;
}
</script>
</body>
</html>
