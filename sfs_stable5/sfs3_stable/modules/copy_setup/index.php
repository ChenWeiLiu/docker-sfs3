<?php
//$Id: index.php 9116 2017-08-09 06:32:36Z infodaes $
include "config.php";

//認證
sfs_check();

$act=$_REQUEST[act];
$err=$_GET[err];
$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year]; //目前學年
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme]; //目前學期

//主要內容
if ($_POST[copy]) {
	if($act=="go_class_setup"){
		go_class_setup($_POST[ys],$sel_year,$sel_seme);
		header("location: {$_SERVER[PHP_SELF]}?y=$sel_year&s=$sel_seme&act=go_class_setup");
	}elseif($act=="go_score_setup"){
		go_score_setup($_POST[ys],$sel_year,$sel_seme);
		header("location: $_SERVER[PHP_SELF]?y=$sel_year&s=$sel_seme&act=go_score_setup");
	}elseif($act=="go_ss_setup"){
		go_ss_setup($_POST[ys],$sel_year,$sel_seme);
		header("location: $_SERVER[PHP_SELF]?y=$sel_year&s=$sel_seme&act=go_ss_setup");
	}elseif($act=="go_course_setup"){
		go_course_setup($_POST[ys],$sel_year,$sel_seme);
		header("location: $_SERVER[PHP_SELF]?y=$sel_year&s=$sel_seme&act=go_course_setup");
	}
}elseif($_POST[del]){
	$err=del_setup();
	header("location: $_SERVER[PHP_SELF]?err=$err");
}else{
	$main=main_form($sel_year,$sel_seme);
}


//秀出網頁布景標頭
head("設定複製");
echo $main;
//佈景結尾
foot();


function main_form($sel_year,$sel_seme){
	global $school_menu_p,$err,$act;

	//選項
	if ($act=="") $act="go_class_setup";
	switch($act) {
		case "go_class_setup":
			//班級設定
			$array=get_class_setup_ys($sel_year,$sel_seme);
			$to_class_setup=to_ys("to_y","to_s",$array);
			$g=0;
			break;
		case "go_score_setup":
			//成績設定
			$array=get_score_setup_ys($sel_year,$sel_seme);
			$to_score_setup=to_ys("to_y","to_s",$array);
			$g=1;
			break;
		case "go_ss_setup":
			//課程設定
			$array=get_ss_setup_ys($sel_year,$sel_seme);
			$to_ss_setup=to_ys("to_y","to_s",$array);
			$g=2;
			break;
		case "go_course_setup":
			//課表設定
			$array=get_course_setup_ys($sel_year,$sel_seme);
			$to_course_setup=to_ys("to_y","to_s",$array);
			$g=3;
			break;
	}
	if ($g=="") $g=0;
	$checked[$g]="checked";
	$setup[$g]=make_option($array,"ys");

	//檢查現有資料
	$chk[0]=(chk_have_ys("school_class",$sel_year,$sel_seme))?"true":"false";
	$chk[1]=(chk_have_ys("score_setup",$sel_year,$sel_seme))?"true":"false";
	$chk[2]=(chk_have_ys("score_ss",$sel_year,$sel_seme))?"true":"false";
	$chk[3]=(chk_have_ys("score_course",$sel_year,$sel_seme))?"true":"false";
	for($i=0;$i<=3;$i++) {
		$err_msg[$i]=($chk[$i]=="true")?"<br><font color='red'>(本學期已有資料)</font>":"";
	}

	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	$main="$tool_bar
	<table cellspacing='1' cellpadding='6' bgcolor='#C0C0C0' class='small'>
	<input type='hidden' name='data[sp_sn]' value='$DBV[sp_sn]'>
	<tr class='title_mbody' align='center'><td>
	項目
	</td><td>
	從哪一學期（資料筆數）
	</td><td>
	複製到哪一學期
	</td><td>
	執行
	</td><td>
	刪除
	</td></tr>
	<form name='setform' action='$_SERVER[PHP_SELF]' method='post'>
	<tr bgcolor='#FFFFFF'><td>
        <input type='radio' name='act' value='go_class_setup' $checked[0] OnChange='this.form.submit();'>班級設定 $err_msg[0]
	</td><td rowspan='4' align='center'>".
	$setup[$g]."
	</td><td rowspan='4' align='center'>
	$sel_year 學年度第 $sel_seme 學期
	</td><td rowspan='4' align='center'>
	<input type=\"submit\" value=\"開始複製\" class=\"b1\" name=\"copy\">
	</td><td rowspan='4' align=\"center\">
	<input type='submit' value='刪除本學期已存在資料' class='b1' name=\"del\" OnClick=\"return confirm('確定刪除".$sel_year."學年度第".$sel_seme."學期「班級設定」、「成績設定」、「課程設定」、「課表設定」等資料?');\"";
	if ($err==1)
		$main.=" disabled=\"true\"><br><font color=\"red\">(本學期已有成績不允許刪除設定)</font>";
	else
		$main.=">";
	$main.="
	</td></tr>
	<tr bgcolor='#FFFFFF'><td>
	<input type='radio' name='act' value='go_score_setup' $checked[1] OnChange='this.form.submit();'>成績設定 $err_msg[1]
	</td></tr>
	<tr bgcolor='#FFFFFF'><td>
	<input type='radio' name='act' value='go_ss_setup' $checked[2] OnChange='this.form.submit();'>課程設定 $err_msg[2]
	</td></tr>
	<tr bgcolor='#FFFFFF'><td>
	<input type='radio' name='act' value='go_course_setup' $checked[3] OnChange='this.form.submit();'>課表設定 $err_msg[3]
	</td></tr>
	</form>
	</table>
	<p>
	<table cellspacing='1' cellpadding='6' bgcolor='#C0C0C0' class='small'>
	<tr bgcolor='#FFFFFF'><td style='line-height:2'>
	此工具威力強大，可以在彈指間替您複製各種學期初設定。但是，相對的，若誤用的話，造成的損害也大，因此請依照以下方法來進行事前準備，以避免造成無可挽救的情況：
	<ol>
	<li>在使用之前，請備份 copy_log、school_class、score_setup、score_ss、score_course 等資料表，以避免出問題。
	<li>請由「上」而「下」，依照順序複製，盡量勿跳著複製，因為有些資料是有相依性的。
	<p style='color:blue'>例：若欲複製課表，「課程設定」務必先複製，因為「課程設定」的資料會被「課表設定」所使用。</p>
	<li>若欲修改其中任何項目，請於所有複製動作做完再進行，以策安全。
	<p style='color:blue'>例：若欲複製課表，必須複製完「課程設定」後，緊接著進行「課表設定」的複製。千萬不要複製完「課程設定」後，就去調整「課程設定」，然後又來進行「課表設定」的複製，這樣將有可能出問題。</p>
	<li>此工具建議用在上下學期的設定複製，較不建議用在新學年度上，因為學年度間的差異比較大。
	<p style='color:blue'>例：以一年級班級數來說，92年下學期的一年級班級數，不得見會和 93 年上學期的班級數一樣，所以不建議用複製的方式。</p>
	</ol>
	</td></tr>
	</table>
	";
	return $main;
}

//取得現有班級設定的學年度學期
function get_class_setup_ys($sel_year,$sel_seme){
	global $CONN;
	$sql_select="select count(*), year , semester FROM school_class WHERE enable='1' group by year , semester order by year desc,semester desc";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($count,$year,$semester)=$recordSet->FetchRow()) {
		$k=sprintf("%03d%01d", $year, $semester);
		$main[$k]=$count;
	}
	return $main;
}

//取得現有成績設定的學年度學期
function get_score_setup_ys($sel_year,$sel_seme){
	global $CONN;
	$sql_select="select count(*), year , semester FROM score_setup WHERE enable='1' group by year , semester order by year desc,semester desc";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($count,$year,$semester)=$recordSet->FetchRow()) {
		$k=sprintf("%03d%01d", $year, $semester);
		$main[$k]=$count;
	}
	return $main;
}

//取得現有科目設定的學年度學期
function get_ss_setup_ys($sel_year,$sel_seme){
	global $CONN;
	$sql_select="select count(*), year , semester FROM score_ss WHERE enable='1' group by year , semester order by year desc,semester desc";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($count,$year,$semester)=$recordSet->FetchRow()) {
		$k=sprintf("%03d%01d", $year, $semester);
		$main[$k]=$count;
	}
	return $main;
}

//取得現有課表設定的學年度學期
function get_course_setup_ys($sel_year,$sel_seme){
	global $CONN;
	$sql_select="select count(*), year , semester FROM score_course group by year , semester order by year desc,semester desc";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($count,$year,$semester)=$recordSet->FetchRow()) {
		$k=sprintf("%03d%01d", $year, $semester);
		$main[$k]=$count;
	}
	return $main;
}

//製作下拉選單的選項
function make_option($array=array(),$name){
	$main="<select name='$name'>";
	foreach($array as $k=>$v){
		$y=substr($k,0,3)*1;
		$s=substr($k,-1)*1;
		$show=$y." 學年度第 ".$s."學期";
		$main.="<option value='$k'>".$show."（".$v."）</option>";
	}
	$main.="</select>";
	return $main;
}

//製作欲複製到學年的表單
function to_ys($y_name,$s_name,$array=array()){
	global $sel_year,$sel_seme;

	foreach($array as $k=>$v){
		$y=substr($k,0,3)*1;
		$s=substr($k,-1)*1;
		break;
	}

	if($s=='1'){
		$yy=$y;
		$ss=2;
	}elseif($s=='2'){
		$yy=$y+1;
		$ss=1;
	}

	$selected1=($ss==1)?"selected":"";
	$selected2=($ss==2)?"selected":"";
	$main="<input value='$yy' size='3' name='$y_name' type='text'> 學年度第	<select name='$s_name'><option value='1' $selected1>1</option><option value='2' $selected2>2</option></select>學期";
	return $main;
}


//複製的log
function cp_log($log=array(),$tbl_name="",$record=array(),$year="",$semester=""){
	global $CONN;

	foreach($log as $sn){
		$in[]="('$sn','$tbl_name',now(),'$record[$sn]','$year','$semester')";
	}
	$all_in=implode(",",$in);

	$sql_insert = "insert into copy_log (sn,tbl_name,date,record,year,semester) values $all_in";
	$CONN->Execute($sql_insert);
}

//取得log中的紀錄對應值
function get_cp_log($tbl_name=""){
	global $CONN;
	$sql_select = "select sn,record FROM copy_log where tbl_name='$tbl_name'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($sn,$record)=$recordSet->FetchRow()){
		$main[$record]=$sn;
	}
	return $main;
}

//檢查是該資料表的學年學期狀況
function chk_have_ys($tbl="",$year="",$semester=""){
	global $CONN;
	$sql_select="select count(*)  FROM $tbl WHERE year='$year' and semester='$semester'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	list($count)=$recordSet->FetchRow();
	return $count;
}

//開始複製班級設定
function go_class_setup($ys="",$to_y="",$to_s=""){
	global $CONN;
	if(empty($ys) or empty($to_y) or empty($to_s))return;

	$y=substr($ys,0,3)*1;
	$s=substr($ys,-1)*1;

	//檢查是否已有資料
	$count=chk_have_ys("school_class",$to_y,$to_s);
	if($count)user_error("已有第".$to_y."學年度第".$to_s."學期的班級資料，複製停止。",256);

	$sql_select="select class_id,c_year,c_name,c_kind,c_sort,teacher_1,teacher_2 FROM school_class WHERE enable='1' and year='$y' and semester='$s'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($class_id,$c_year,$c_name,$c_kind,$c_sort,$teacher_1,$teacher_2)=$recordSet->FetchRow()) {
		$new_class_id=sprintf("%03d_%01d", $to_y, $to_s).substr($class_id,-6);
		$teacher_1 = addslashes($teacher_1);
		$teacher_2 = addslashes($teacher_2);
		$sql_insert = "insert into school_class (class_id,year,semester,c_year,c_name,c_kind,c_sort,enable,teacher_1,teacher_2) values ('$new_class_id','$to_y','$to_s','$c_year','$c_name','$c_kind','$c_sort','1','$teacher_1','$teacher_2')";
		$CONN->Execute($sql_insert) or user_error("班級複製失敗！<br>$sql_insert",256);
		$sn=mysql_insert_id();
		$log[]=$sn;
		$record[$sn]=$class_id;
	}
	cp_log($log,"school_class",$record,$to_y,$to_s);
	return ;
}

//開始複製成績設定
function go_score_setup($ys="",$to_y="",$to_s=""){
	global $CONN;
	if(empty($ys) or empty($to_y) or empty($to_s))return;

	$y=substr($ys,0,3)*1;
	$s=substr($ys,-1)*1;


	//檢查是否已有資料
	$count=chk_have_ys("score_setup",$to_y,$to_s);
	if($count)user_error("已有第".$to_y."學年度第".$to_s."學期的成績設定資料，複製停止。",256);

	//檢查是否已有班級資料
	$count=chk_have_ys("school_class",$to_y,$to_s);
	if(empty($count))user_error("沒有第".$to_y."學年度第".$to_s."學期的班級設定資料，因此無法進行課程複製。",256);

	$sql_select="select setup_id,class_year,allow_modify,performance_test_times,practice_test_times,test_ratio,rule,score_mode,sections,interface_sn FROM score_setup WHERE enable='1' and year='$y' and semester='$s'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($setup_id,$class_year,$allow_modify,$performance_test_times,$practice_test_times,$test_ratio,$rule,$score_mode,$sections,$interface_sn)=$recordSet->FetchRow()) {
		$sql_insert = "insert into score_setup (year,semester,class_year,allow_modify,performance_test_times,practice_test_times,test_ratio,rule,score_mode,sections,interface_sn,update_date,enable) values ('$to_y','$to_s','$class_year','$allow_modify','$performance_test_times','$practice_test_times','$test_ratio','$rule','$score_mode','$sections','$interface_sn',now(),'1')";
		$CONN->Execute($sql_insert) or user_error("成績設定複製失敗！<br>$sql_insert",256);
		$sn=mysql_insert_id();
		$log[]=$sn;
		$record[$sn]=$setup_id;
	}
	cp_log($log,"score_setup",$record,$to_y,$to_s);
	return;
}

//開始複製課程設定
function go_ss_setup($ys="",$to_y="",$to_s=""){
	global $CONN;
	if(empty($ys) or empty($to_y) or empty($to_s))return;

	$y=substr($ys,0,3)*1;
	$s=substr($ys,-1)*1;

	//檢查是否已有資料
	$count=chk_have_ys("score_ss",$to_y,$to_s);
	if($count)user_error("已有第".$to_y."學年度第".$to_s."學期的課程資料，複製停止。",256);

	//檢查是否已有班級資料
	$count=chk_have_ys("school_class",$to_y,$to_s);
	if(empty($count))user_error("沒有第".$to_y."學年度第".$to_s."學期的班級設定資料，因此無法進行課程複製。",256);
	// 複製所有課程(含班級課程 )by hami  2012-2-24
	$sql_select="select ss_id,scope_id,subject_id,class_id,class_year,need_exam,rate,sort,sub_sort,print,link_ss,nor_item_kind,sections,k12ea_category,k12ea_area,k12ea_subject,k12ea_language,k12ea_frequency FROM score_ss WHERE  enable='1' and year='$y' and semester='$s'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($ss_id,$scope_id,$subject_id,$class_id,$class_year,$need_exam,$rate,$sort,$sub_sort,$print,$link_ss,$nor_item_kind,$sections,$k12ea_category,$k12ea_area,$k12ea_subject,$k12ea_language,$k12ea_frequency)=$recordSet->FetchRow()) {
		if ($class_id) {
			$new_class_id= sprintf("%03d_%01d", $to_y, $to_s).substr($class_id,-6);
			$sql_insert = "insert into score_ss (scope_id,subject_id,year,semester,class_year,enable,need_exam,rate,sort,sub_sort,print,link_ss,nor_item_kind,class_id,sections,k12ea_category,k12ea_area,k12ea_subject,k12ea_language,k12ea_frequency) values ('$scope_id','$subject_id','$to_y','$to_s','$class_year','1','$need_exam','$rate','$sort','$sub_sort','$print','$link_ss','$nor_item_kind','$new_class_id','$sections','$k12ea_category','$k12ea_area','$k12ea_subject','$k12ea_language','$k12ea_frequency')";
		}
		else
			$sql_insert = "insert into score_ss (scope_id,subject_id,year,semester,class_year,enable,need_exam,rate,sort,sub_sort,print,link_ss,nor_item_kind,sections,k12ea_category,k12ea_area,k12ea_subject,k12ea_language,k12ea_frequency) values ('$scope_id','$subject_id','$to_y','$to_s','$class_year','1','$need_exam','$rate','$sort','$sub_sort','$print','$link_ss','$nor_item_kind','$sections','$k12ea_category','$k12ea_area','$k12ea_subject','$k12ea_language','$k12ea_frequency')";

		$CONN->Execute($sql_insert) or user_error("課程設定複製失敗！<br>$sql_insert",256);
		$sn=mysql_insert_id();
		$log[]=$sn;
		$record[$sn]=$ss_id;
	}
	cp_log($log,"score_ss",$record,$to_y,$to_s);
	return;
}


//開始複製課表設定
function go_course_setup($ys="",$to_y="",$to_s=""){
	global $CONN;
	if(empty($ys) or empty($to_y) or empty($to_s))return;

	$y=substr($ys,0,3)*1;
	$s=substr($ys,-1)*1;

	//檢查是否已有資料
	$count=chk_have_ys("score_course",$to_y,$to_s);
	if($count)user_error("已有第".$to_y."學年度第".$to_s."學期的課表設定資料，複製停止。",256);

	//檢查是否已課程資料
	$count=chk_have_ys("copy_log",$to_y,$to_s);
	if(empty($count))user_error("您沒有進行第".$to_y."學年度第".$to_s."學期的課程設定資料複製，因此無法進行課表複製。",256);

	$new_ss_id=get_cp_log("score_ss");

	$sql_select="select course_id,class_id,teacher_sn,class_year,class_name,day,sector,ss_id,room,allow,c_kind FROM score_course WHERE year='$y' and semester='$s'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($course_id,$class_id,$teacher_sn,$class_year,$class_name,$day,$sector,$ss_id,$room,$allow,$c_kind)=$recordSet->FetchRow()) {
		$new_class_id=sprintf("%03d_%01d", $to_y, $to_s).substr($class_id,-6);
		$sql_insert = "insert into score_course (year,semester,class_id,teacher_sn,class_year,class_name,day,sector,ss_id,room,allow,c_kind) values ('$to_y','$to_s','$new_class_id','$teacher_sn','$class_year','$class_name','$day','$sector','$new_ss_id[$ss_id]','$room','$allow','$c_kind')";
		$CONN->Execute($sql_insert) or user_error("課表設定複製失敗！<br>$sql_insert",256);
		$sn=mysql_insert_id();
		$log[]=$sn;
		$record[$sn]=$course_id;
	}
	cp_log($log,"score_course",$record,$to_y,$to_s);
	return;
}

//刪除設定
function del_setup(){
	global $CONN,$sel_year,$sel_seme;

	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	$query="select * from $score_semester where 1=0";
	if (!$CONN->Execute($query)) {
		$query="select count(score) from $score_semester where score>'0'";
		$res=$CONN->Execute($query);
		if ($res->fields[0]>0) return 1;
	}
	$CONN->Execute("delete from school_class where year='$sel_year' and semester='$sel_seme'");
	$CONN->Execute("delete from score_setup where year='$sel_year' and semester='$sel_seme'");
	$CONN->Execute("delete from score_ss where year='$sel_year' and semester='$sel_seme'");
	$CONN->Execute("delete from score_course where year='$sel_year' and semester='$sel_seme'");
	$CONN->Execute("delete from copy_log where year='$sel_year' and semester='$sel_seme'");
	return 0;
}
?>
