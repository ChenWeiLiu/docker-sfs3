<?php

// $Id: auto_course_setup.php 5310 2009-01-10 07:57:56Z hami $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

//取得模組設
$m_arr = &get_sfs_module_set('course_paper');
extract($m_arr, EXTR_OVERWRITE);
if ($midnoon=='') $midnoon=5;

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//log標示
$mark="fix".$sel_year."_".$sel_seme;

//錯誤設定
if($error==1){
	$act="error";
	$error_title="無年級和班級設定";
	$error_main="找不到第 ".$sel_year." 學年度，第 ".$sel_seme." 學期的年級、班級設定，故您無法使用此功能。<ol><li>請先到『<a href='".$SFS_PATH_HTML."school_affairs/every_year_setup/class_year_setup.php'>班級設定</a>』設定年級以及班級資料。<li>以後記得每一學期的學期出都要設定一次喔！</ol>";
}

//執行動作判斷
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="save_teacher_class_num"){
	save_teacher_class_num($sel_year,$sel_seme,$t_class_num,$teach_year);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=teacher_class_num&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="save_ss_class_num"){
	save_ss_class_num($sel_year,$sel_seme,$ss_class_num);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=ss_class_num&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="save_teacher_ss_num"){
	save_teacher_ss_num($sel_year,$sel_seme,$teacher_sn,$class_id,$ss_id,$all_2_teacher,$teacher_class_ss_num);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=teacher_class_ss&class_id=$class_id&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="save_set_class_time"){
	save_set_class_time($sel_year,$sel_seme,$class_id,$class_time,$all_same);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=set_class_time&class_id=$class_id&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="del_al_ss"){
	del_al_ss($ctsn);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=$mode&class_id=$class_id&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="save_same_course"){
	$ctmp_sn=save_same_course($sel_year,$sel_seme,$set_class_id,$set_ctsn);
	$ctmp=implode(",",$ctmp_sn);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=same_course_day_set&ctmp=$ctmp&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="update_same_course"){
	update_same_course($sel_year,$sel_seme,$class_time,$ctmp);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=same_course&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="del_same_course"){
	del_same_course($sel_year,$sel_seme,$class_time,$ctmp_sn);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=$mode&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="save_all"){
	$main=save_all($sel_year,$sel_seme,$mode);
	if($main=="ok")header("location: course_setup.php?sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="fix_class"){
	fix_class($sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=view_tmp&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="add_room"){
	add_room($sel_year,$sel_seme,$sel_class,$ss_id,$room);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=setup_class&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="re_start_go"){
	$mode=re_start_go($sel_year,$sel_seme,$del_null,$del_room,$del_auto_fix,$re_start);
	header("location: {$_SERVER['PHP_SELF']}?act=start&mode=$mode&sel_year=$sel_year&sel_seme=$sel_seme");
}elseif($act=="start"){
	$main=&start_table($sel_year,$sel_seme,$mode);
}elseif($act=="view_log"){
	$main=&view_log($mark);
}else{
	$main=&class_form($sel_year,$sel_seme);
}


//秀出網頁
head("自動排課系統");
echo $main;
foot();


/*
函式區
*/

//基本設定表單
function &class_form($sel_year,$sel_seme){
	global $school_menu_p,$act;
	
	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");

	//說明
	$help_text="
	請選擇一個學年學期以做設定。||
	<span class='like_button'>開始設定</span> 會開始進行全校的自動排課設定。
	";
	$help=&help($help_text);

	$tool_bar=&make_menu($school_menu_p);

	$main="
	<script language='JavaScript'>
	function jumpMenu_seme(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td>請選擇欲設定的學年度：</td><td>$date_select</td></tr>
		<input type='hidden' name='act' value='start'>
		<tr><td colspan='2'><input type='submit' value='開始設定'>
		</td></tr>
		</form>
		</table>
	</td></tr>
	</table>
	<br>
	$help
	";
	return $main;
}

//自動排課基本設定
function &start_table($sel_year,$sel_seme,$mode=""){
	global $CONN,$weekN,$school_menu_p,$act,$class_id,$ctmp,$sel_class;

	//取得學年
	$semester_name=($sel_seme=='2')?"下":"上";
	$date_text="<font color='#607387'>
	<font color='#000000'>$sel_year</font> 學年
	<font color='#000000'>$semester_name</font>學期
	</font>";

	if($mode=="teacher_class_num"){
		$fmain=&teacher_class_num($sel_year,$sel_seme);
	}elseif($mode=="ss_class_num"){
		$fmain=&ss_class_num($sel_year,$sel_seme);
	}elseif($mode=="teacher_class_ss"){
		$fmain=&teacher_class_ss($sel_year,$sel_seme);
	}elseif($mode=="set_class_time"){
		$fmain=&set_class_time($sel_year,$sel_seme,$class_id);
	}elseif($mode=="same_course"){
		$fmain=&same_course($sel_year,$sel_seme);
	}elseif($mode=="same_course_day_set"){
		$fmain=&same_course_day_set($sel_year,$sel_seme,$ctmp);
	}elseif($mode=="start_class"){
		$fmain=start_class($sel_year,$sel_seme);
	}elseif($mode=="setup_class"){
		$fmain=&setup_class($sel_year,$sel_seme,$sel_class);
	}elseif($mode=="view_tmp"){
		$fmain=&view_tmp($sel_year,$sel_seme,$class_id);
	}elseif($mode=="re_start"){
		$fmain=&re_start($sel_year,$sel_seme);
	}

	$tool_bar=&make_menu($school_menu_p);
	$color1=($mode=="teacher_class_num")?"#ccff99":"#ffffff";
	$color2=($mode=="ss_class_num")?"#ccff99":"#ffffff";
	$color3=($mode=="teacher_class_ss")?"#ccff99":"#ffffff";
	$color4=($mode=="set_class_time")?"#ccff99":"#ffffff";
	$color5=($mode=="same_course" or $mode=="same_course_day_set")?"#ccff99":"#ffffff";
	$color6=($mode=="setup_class")?"#ccff99":"#ffffff";
	$color7=($mode=="start_class" or $mode=="view_tmp")?"#ccff99":"#ffffff";
	$color8=($mode=="re_start")?"#ccff99":"#ffffff";
	
	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='4' bgcolor='#C6FF8C'>
	<tr bgcolor='#FFFFFF' class='small'>
	<td bgcolor='$color1'><a href='{$_SERVER['PHP_SELF']}?act=$act&mode=teacher_class_num&sel_year=$sel_year&sel_seme=$sel_seme'>(1)教師授課節數</a></td>
	<td bgcolor='$color2'><a href='{$_SERVER['PHP_SELF']}?act=$act&mode=ss_class_num&sel_year=$sel_year&sel_seme=$sel_seme'>(2)科目節數</a></td>
	<td bgcolor='$color3'><a href='{$_SERVER['PHP_SELF']}?act=$act&mode=teacher_class_ss&sel_year=$sel_year&sel_seme=$sel_seme'>(3) 教師任教科目</a></td>
	<td bgcolor='$color4'><a href='{$_SERVER['PHP_SELF']}?act=$act&mode=set_class_time&sel_year=$sel_year&sel_seme=$sel_seme'>(4)上課時間</a></td>
	<td bgcolor='$color5'><a href='{$_SERVER['PHP_SELF']}?act=$act&mode=same_course&sel_year=$sel_year&sel_seme=$sel_seme'>(5)預設科目</a></td>
	<td bgcolor='$color6'><a href='{$_SERVER['PHP_SELF']}?act=$act&mode=setup_class&sel_year=$sel_year&sel_seme=$sel_seme'>(6)專科教室</a></td>
	<td bgcolor='$color7'><a href='{$_SERVER['PHP_SELF']}?act=$act&mode=start_class&sel_year=$sel_year&sel_seme=$sel_seme'>(7)開始排課</a></td>
	<td bgcolor='$color8'><a href='{$_SERVER['PHP_SELF']}?act=$act&mode=re_start&sel_year=$sel_year&sel_seme=$sel_seme'>(8)重新排課</a></td>
	</tr>
	</table>
	$fmain
	";
	return $main;
}

//教師授課節數設定
function &teacher_class_num($sel_year,$sel_seme){
	global $CONN,$school_kind_name;
	
	//先取得已有資料
	$sql_select = "select teacher_sn,num,teach_year from course_teach_num where year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while (list($teacher_sn,$num,$teach_year) = $recordSet->FetchRow()) {
		$data[$teacher_sn]=$num;
		$teach_data[$teacher_sn]=$teach_year;
	}
	
	$sql_select = "select a.name,a.teacher_sn,b.post_kind,b.class_num from teacher_base as a,teacher_post as b where a.teacher_sn=b.teacher_sn and a.teach_condition='0'";
	$recordSet=$CONN->Execute($sql_select);
	while (list($name,$teacher_sn,$post_kind,$class_num) = $recordSet->FetchRow()) {
		//取得年級陣列
		$cyear_chk="";
		$cyear=get_class_year_array($sel_year,$sel_seme);
		while(list($k,$v)=each($cyear)){
			$all_v=explode(",",$teach_data[$teacher_sn]);
			$checked=(in_array($v,$all_v))?"checked":"";
			$cyear_chk.="<input type='checkbox' name='teach_year[$teacher_sn][]' value='$v' $checked>$school_kind_name[$v]&nbsp;&nbsp;";
		}
		
		$post_kind_name=post_kind();
		$job=(empty($class_num))?$post_kind_name[$post_kind]:class_id2big5($class_num,$sel_year,$sel_seme)."導師";
		
		//教師在該堂的授課次數，若>2表示衝堂。
		$tr.="<tr bgcolor='#FFFFFF'>
		<td>$name <font size='2' color='green'>$job</font></td>
		<td>
		<input type='text' name='t_class_num[$teacher_sn]' value='$data[$teacher_sn]' size='2'>
		</td><td class='small'>
		$cyear_chk
		</td></tr>";
	}

	$main="
	<table cellspacing='1' cellpadding='3' bgcolor='#9EBCDD'>
	<tr bgcolor='#E3F2FB'><td>教師姓名</td><td>節數</td><td>該師教學年級設定〈若沒勾選表示各年級都可以〉</td></tr>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	$tr
	<tr><td colspan='3' align='center'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='act' value='save_teacher_class_num'>
	<input type='submit' value='儲存'></td></tr>
	</form>
	</table>";
	return $main;
}

//新增教師授課節數設定
function save_teacher_class_num($sel_year,$sel_seme,$t_class_num=array(),$teach_year=array()){
	global $CONN;
	
	$sql_delete="delete from course_teach_num where year=$sel_year and seme='$sel_seme'";
	$CONN->Execute($sql_delete)	or trigger_error("SQL語法執行錯誤： $sql_delete", E_USER_ERROR);
	
	if(is_array($t_class_num) and sizeof($t_class_num)>0){
		while(list($tsn,$num)=each($t_class_num)){
			//先處理該教師的年級限制
			$cyear=implode(",",$teach_year[$tsn]);
			$sql_insert = "insert into course_teach_num (year,seme,teacher_sn,num,teach_year) values ($sel_year,'$sel_seme','$tsn','$num','$cyear')";
			$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
		}
	}
	return true;
}

//科目節數設定
function &ss_class_num($sel_year,$sel_seme){
	global $CONN,$SFS_PATH_HTML,$school_kind_name;
	
	//先取得已有資料
	$sql_select = "select ss_id,num from course_ss_num where year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while (list($ss_id,$num) = $recordSet->FetchRow()) {
		$data[$ss_id]=$num;
	}
	
	//$class_year_array=get_class_year_array($sel_year,$sel_seme);
	//列出課程設定中有設定年級與班級
	$yc_array=get_ss_yc($sel_year,$sel_seme);
	
	if(sizeof($yc_array)==0){
		$msg="資料庫中找不到 $sel_year 學年，第 $sel_seme 學期的班級資料。<p>
		請先進行 $sel_year 學年，第 $sel_seme 學期的
		<a href='".$SFS_PATH_HTML."/school_affairs/every_year_setup/class_year_setup.php?act=setup&sel_year=$sel_year&sel_seme=$sel_seme'>
		班級設定</a>，才能繼續進行。</p>";
		trigger_error("無法取得該年級的班級設定， $msg", E_USER_ERROR);
	}
	
	foreach($yc_array as $yc){
		$td="";
		$all="";
		$Cyear=$yc[Cyear];
		if(!empty($yc[class_id])){
			$cd=class_id_2_old($yc[class_id]);
		}
		
		$cy_name=(!empty($yc[class_id]))?$cd[5]:$school_kind_name[$Cyear];
		
		$class=&get_all_ss($sel_year,$sel_seme,$yc[Cyear],$yc[class_id]);
		$n=sizeof($class);
		for($j=0;$j<$n;$j++){
			$ss_id=$class[$j][ss_id];
			$ss_name=&get_ss_name("","","長",$ss_id);
			
			if($j==0){
				$td0="<td class='small'>$ss_name</td>
				<td><input type='text' name='ss_class_num[$ss_id]' value='$data[$ss_id]' size='2' class='border_thin'></td>";
			}else{
				$td.="<tr bgcolor='#FFFFF0'>
				<td class='small'>$ss_name</td>
				<td><input type='text' name='ss_class_num[$ss_id]' value='$data[$ss_id]' 	size='2' class='border_thin'></td></tr>";
			}
			
			$all+=$data[$ss_id];
		}
		//$td.="</table>";
		
		$tr2.="<tr bgcolor='#FFFFFF'>
		<td align='center' rowspan=$n><b>$cy_name</b><p>共 <font color='#008000'>$n</font> 門課<p>已排 <font color='#FF0000'>$all</font> 節</td>$td0</tr>$td";

	}
	
	$main="
	<table cellspacing='1' cellpadding='3' bgcolor='#9EBCDD' class='small'>
	<tr><td colspan='20' align='center'>科目節數設定</td></tr>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	$tr2
	<tr><td colspan='20' align='center'>
	<input type='hidden' name='act' value='save_ss_class_num'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='submit' value='儲存'></td></tr>
	</form>
	</table>";

	return $main;
}

//新增科目節數設定
function save_ss_class_num($sel_year,$sel_seme,$ss_class_num=""){
	global $CONN;
	$sql_delete="delete from course_ss_num where year=$sel_year and seme='$sel_seme'";
	$CONN->Execute($sql_delete)	or trigger_error("SQL語法執行錯誤： $sql_delete", E_USER_ERROR);
	
	while(list($ss_id,$num)=each($ss_class_num)){
		$sql_insert = "insert into course_ss_num (year,seme,ss_id,num) values ($sel_year,'$sel_seme','$ss_id','$num')";
		$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
	}
	return true;
}

//教師任教科目設定
function &teacher_class_ss($sel_year,$sel_seme){
	global $CONN,$act,$mode,$class_id;

	$no_data=false;
	if(!empty($class_id)){
		$c=class_id_2_old($class_id);
	}else{
		$no_data=true;
	}
	
	//取得該班導師姓名：
	$the_teacher_name=get_class_teacher($c[2]);
	
	//製作教師選單
	$sql_select = "select name,teacher_sn from teacher_base where teach_condition='0'";
	$recordSet=$CONN->Execute($sql_select);
	$option="<option value=''>請選擇教師</option>";
	if(!empty($the_teacher_name[sn])){
		$option.="<option value='$the_teacher_name[sn]'>該班導師</option>";
	}
	while (list($name,$teacher_sn) = $recordSet->FetchRow()) {
		//先取得教師預設的授課時數資料
		$tn=get_teacher_num_all($sel_year,$sel_seme,$teacher_sn);
		//分析該教師是否在該年級的教師選單中，若$tn[cyear]是空白表示該教師適用所有年級
		$cy=explode(",",$tn[cyear]);
		if(!empty($tn[cyear]) and !in_array($c[3],$cy))continue;
		if($tn[can] < 1)continue;
		$option.="<option value='$teacher_sn'>$name ".$tn[can]."(".$tn[ok]."/".$tn[all].")</option>\n";
	}

	$select_teacher="
	<select name='teacher_sn'>
	$option
	</select>";

	
	//先取得科目的設定節數
	$sql_select = "select ss_id,num from course_ss_num where year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	
	while (list($ss_id,$num) = $recordSet->FetchRow()) {
		//再取得該班的該科目已經設定的授課時數
		$already_class_ss_num=get_already_class_ss_num($sel_year,$sel_seme,$class_id,$ss_id);
		
		//看看該教師還有幾節課可以用
		if(empty($already_class_ss_num))$already_class_ss_num=0;
		$ss_already_setup_n[$ss_id]=$already_class_ss_num;
		$ss_setup_n[$ss_id]=$num;
		$ss_can_setup_n[$ss_id]=$num-$already_class_ss_num;
		$all_sid_n[$ss_id]=$num;
	}
	
	$cyear_setup_n="";
	$select_ss="";
	//取得該班級的科目
	$sql_select = "select ss_id from score_ss where class_id='$class_id' and year='$sel_year' and semester='$sel_seme' and enable='1'";
	$recordSet=$CONN->Execute($sql_select);
	
	while (list($ss_id) = $recordSet->FetchRow()) {
		$cyear_setup_n+=$ss_setup_n[$ss_id];
		$subject_name=&get_ss_name("","","長",$ss_id);
		$chkbox=(empty($ss_can_setup_n[$ss_id]))?"":"<input type='checkbox' name='ss_id[]' value='$ss_id'>";
		$fcolor=($ss_can_setup_n[$ss_id] > 0)?"#0000FF":"black";
		$select_ss.="<tr bgcolor='#FFFFFF' class='small'><td>
		$chkbox
		$subject_name </td>
		<td align='center'><font face='Verdana' color='$fcolor'>".$ss_can_setup_n[$ss_id]."</font></td>
		<td align='center'><font face='Verdana'>".$ss_already_setup_n[$ss_id]."</font></td>
		<td align='center'><font face='Verdana'>".$ss_setup_n[$ss_id]."</font>
		</td></tr>";
		
	}
	
	//若沒有資料取得該年級的科目
	if(empty($select_ss)){
		$sql_select = "select ss_id from score_ss where class_year='$c[3]' and class_id='' and year='$sel_year' and semester='$sel_seme' and enable='1'";
		$recordSet=$CONN->Execute($sql_select);
		while (list($ss_id) = $recordSet->FetchRow()) {
			$cyear_setup_n+=$ss_setup_n[$ss_id];
			$subject_name=&get_ss_name("","","長",$ss_id);
			$chkbox=(empty($ss_can_setup_n[$ss_id]))?"":"<input type='checkbox' name='ss_id[]' value='$ss_id'>";
			$fcolor=($ss_can_setup_n[$ss_id] > 0)?"#0000FF":"black";
			$select_ss.="<tr bgcolor='#FFFFFF' class='small'><td>
			$chkbox
			$subject_name </td>
			<td align='center'><font face='Verdana' color='$fcolor'>".$ss_can_setup_n[$ss_id]."</font></td>
			<td align='center'><font face='Verdana'>".$ss_already_setup_n[$ss_id]."</font></td>
			<td align='center'><font face='Verdana'>".$ss_setup_n[$ss_id]."</font>
			</td></tr>";
			
		}
	}
	
	$select_ss="<table width='100%' cellspacing='1' cellpadding='2' align='center' bgcolor='#E6E6E6'>
	<tr bgcolor='#E1EAFD' class='small'><td align='center'>科目</td>
	<td align='center'>剩餘<br>時數</td>
	<td align='center'>已設<br>節數</td>
	<td align='center'>應上<br>節數</td></tr>
	$select_ss
	</table>";
	
	//取得該班已經設定好的科目
	$sql_select = "select ctsn,teacher_sn,ss_id,num from course_teacher_ss_num where class_id='$class_id' and year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($ctsn,$tsn,$sid,$n)=$recordSet->FetchRow()){
		$subject_name=&get_ss_name("","","短",$sid);
		$teacher_name=get_teacher_name($tsn);
		$color=($all_sid_n[$sid]!=$n)?"red":"white";
		$data.="<tr bgcolor='$color' class='small'>
		<td>$subject_name</td>
		<td align='center'>$teacher_name</td>
		<td align='center'>$n</td>
		<td>
		<a href='{$_SERVER['PHP_SELF']}?ctsn=$ctsn&act=del_al_ss&mode=$mode&class_id=$class_id&sel_year=$sel_year&sel_seme=$sel_seme'>
		刪除</a>
		</td>		
		</tr>";
		$ss_n++;
		$all_ss_n+=$n;
	}
	
	//如果安排的時數已經超過預設時數，顯示紅色
	$color=($cyear_setup_n < $all_ss_n)?"red":"#CEEECA";
	
	$data="
	<font size='2' color='#800000'>".$c[5]."已設定科目</font>
	<table cellspacing='1' cellpadding='3' bgcolor='#008000'>
	<tr bgcolor='#CEEECA' class='small'><td>科目</td><td>授課教師</td><td>節數</td><td>功能</td></tr>
	$data
	<tr bgcolor='$color' class='small'><td>共 $ss_n 科</td><td colspan='3'>應上 $cyear_setup_n 節，已排 $all_ss_n 節</td></tr>
	</table>";
	
	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu",$class_id);
	
	$other_data=($no_data)?"":"
	<tr bgcolor='#FFFFFF'>
	<td>由 $select_teacher 上
	<font color='#0000FF'>".$c[5]."</font>的 <input type='button' value='所有科目' onClick='javascript:selectall();'><br>$select_ss 
	<p align='center'><input type='checkbox' name='all_2_teacher' value='1' checked>全包，或僅上
	<input type='text' name='teacher_class_ss_num' value='' size='1'>節</p></td>
	</tr>
	<tr><td align='center'>
	<input type='hidden' name='act' value='save_teacher_ss_num'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='submit' value='儲存'></td></tr>
	";
	
	$main="
	<script language=\"JavaScript\">
	function jumpMenu(){
		location=\"{$_SERVER['PHP_SELF']}?act=$act&mode=$mode&sel_year=$sel_year&sel_seme=$sel_seme&class_id=\" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
	}

	function selectall() {
	  for (i=0;i<document.myform.elements.length;i++) {
	    document.myform.elements[i].checked=true;
	  }
	}
	</script>
	<table cellspacing='0' cellpadding='0'><tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#9EBCDD'>
		<tr><td align='center'>教師任教科目設定</td></tr>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
		<tr bgcolor='#FFFFFF'>
		<td>欲設定的班級： $class_select  導師： <font color='#cc0000'>$the_teacher_name[name]</font></td>
		</tr>
		$other_data
		</form>
		</table>
	</td><td width='5'></td><td valign='top'>$data</td></tr></table>
	";
	return $main;
}


///刪除已經設定好的班級科目
function del_al_ss($ctsn){
	global $CONN;
	$sql_delete = "delete from course_teacher_ss_num where ctsn='$ctsn'";
	$CONN->Execute($sql_delete) or diE_USER_ERROR("SQL執行失敗","SQL語法如下：<br>$sql_delete");
	return true;
}

//新增教師任教科目設定
function save_teacher_ss_num($sel_year,$sel_seme,$teacher_sn,$class_id,$ss_id,$all_2_teacher,$teacher_class_ss_num){
	global $CONN;
	if(empty($teacher_sn) or empty($ss_id)){
		trigger_error("教師及科目不可空白，請檢查看看，是否是忘了點選教師，或是沒有選擇課程，必須兩者都有才能繼續排課。", E_USER_ERROR);
	}
	
	for($i=0;$i<sizeof($ss_id);$i++){
		//該科全包的話，找出該科目的時數
		if($all_2_teacher=='1'){
			$num=get_one_class_num($sel_year,$sel_seme,$ss_id[$i]);
				
			//取得該班級某科目已經設定的節數
			$al_n=get_already_class_ss_num($sel_year,$sel_seme,$class_id,$ss_id[$i]);
			$n=$num-$al_n;
		}
		
		//假如節數是空白的〈沒有設定〉或是多重選取，那麼以該課程所有節數來算。
		$t_class_ss_num=(empty($teacher_class_ss_num) or sizeof($ss_id) > 1)?$n:$teacher_class_ss_num;
		
		//假如有時數才輸入資料庫
		if($t_class_ss_num > 0){
			//查看該教師還能配課的時數
			$tn=get_teacher_num_all($sel_year,$sel_seme,$teacher_sn);
			
			//假如該教師的時數不夠用
			if($tn[can]<$teacher_class_ss_num){
				$subject_name=&get_ss_name("","","短",$ss_id[$i]);
				$teacher_name=get_teacher_name($teacher_sn);
				trigger_error("該教師節數不足， ".$teacher_name."老師的可排課節數只剩 $tn[can] 節，無法排到 $teacher_class_ss_num 節的".$subject_name."課程。
	<p>該教師的授課節數上限為 $tn[all] 節，已經排了 $tn[ok] 節</p>
	您可以安排 $tn[can] 節的".$subject_name."課給".$teacher_name."老師，或是安排別的教師來上".$subject_name."課。
	<p>當然也可以將該教師在別班或別的科目的課程減少一點，挪出 $teacher_class_ss_num 節來上".$subject_name."課。</p>", E_USER_ERROR);
			}
			
			$sql_insert = "insert into course_teacher_ss_num (year,seme,teacher_sn,class_id,ss_id,num) values ($sel_year,'$sel_seme','$teacher_sn','$class_id','$ss_id[$i]','$t_class_ss_num')";
			$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
		}
	}
	return true;
}

//取得某堂課的預設節數
function get_one_class_num($sel_year,$sel_seme,$ss_id){
	global $CONN,$class_id;
	$sql_select = "select num from course_ss_num where ss_id='$ss_id' and year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	list($num) = $recordSet->FetchRow();

	return $num;
}

//取得某教師已經設定的節數
function get_already_teacher_ss_num($sel_year,$sel_seme,$teacher_sn){
	global $CONN;
	$sql_select = "select sum(num) from course_teacher_ss_num where teacher_sn='$teacher_sn' and year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	list($num) = $recordSet->FetchRow();
	return $num;
}

//取得該班級某科目已經設定的節數
function get_already_class_ss_num($sel_year,$sel_seme,$class_id,$ss_id){
	global $CONN;
	$sql_select = "select sum(num) from course_teacher_ss_num where class_id='$class_id'and ss_id='$ss_id' and year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	list($num) = $recordSet->FetchRow();
	return $num;
}

//取得該班級某科目已經設定到功課表中的節數
function get_ok_class_ss_num($sel_year,$sel_seme,$class_id,$ss_id){
	global $CONN;
	$sql_select = "select count(*) from course_tmp where class_id='$class_id' and ss_id='$ss_id' and year='$sel_year' and  semester='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	list($num) = $recordSet->FetchRow();
	return $num;
}

//取得教師預設的授課時數資料，含總時數，已排課時數，可用時數，適用年級
function get_teacher_num_all($sel_year,$sel_seme,$teacher_sn){
	global $CONN;
	$sql_select = "select num,teach_year from course_teach_num where teacher_sn='$teacher_sn' and year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while (list($num,$teach_year) = $recordSet->FetchRow()) {
		//再取得教師已經設定的授課時數
		$main[ok]=get_already_teacher_ss_num($sel_year,$sel_seme,$teacher_sn);
		
		//看看該教師還有幾節課可以用
		if(empty($main[ok]))$main[ok]=0;
		$main[all]=$num;
		$main[can]=$num-$main[ok];
		$main[cyear]=$teach_year;
	}
	return $main;
}


//設定班級上課時間
function &set_class_time($sel_year,$sel_seme,$class_id){
	global $CONN,$weekN,$midnoon;
	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center'>星期".$weekN[$i-1]."</td>";
	}
	
	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu",$class_id);
	
	if(!empty($class_id)){
		$c=class_id_2_old($class_id);
		
		//取得已有資料
		$class_times=get_set_class_time($sel_year,$sel_seme,$class_id);
		
		//取得考試所有設定
		$sm=&get_all_setup("",$sel_year,$sel_seme,$c[3]);
		$sections=$sm[sections];
	
		//取得課表
		for ($j=1;$j<=$sections;$j++){

			if ($j==$midnoon){
				$all_class.= "<tr bgcolor='white' class='small'><td colspan='$dayn' align='center'>午休</td></tr>\n";
			}
			$all_class.="<tr bgcolor='#E1ECFF' class='small'><td align='center'>$j</td>";
			//列印出各節
			for ($i=1;$i<=count($weekN); $i++) {
				$k2=$i."_".$j;
				if(!empty($class_times) and is_array($class_times)){
					$checked=(in_array($k2,$class_times))?"checked":"";
				}else{
					$checked="checked";
				}
				//每一格
				$all_class.="<td align='center'>
				<input type='checkbox' name='class_time[]' value='$k2' $checked>
				</td>\n";
			}
			$all_class.= "</tr>\n" ;
		}
		//該班課表
		$main_class_list="
		<table cellspacing='1' cellpadding='2' bgcolor='#9EBCDD'>
		<tr class='small' bgcolor='#C1C1FF'><td colspan='$dayn'>$class_select 
		<input type='radio' name='all_same' value='year'><font color='#800080'>同年級</font>
		<input type='radio' name='all_same' value='all'><font color='#800080'>全校</font> 均採同樣設定
		</td></tr>
		<tr bgcolor='#E1ECFF' class='small'><td align='center'>節</td>$main_a</tr>
		$all_class
		<tr bgcolor='#E1ECFF'><td colspan='6' align='center'>
		<input type='hidden' name='sel_year' value='$sel_year'>
		<input type='hidden' name='sel_seme' value='$sel_seme'>
		<input type='hidden' name='act' value='save_set_class_time'>
		<input type='submit' value='設定'>
		</td></tr>
		</table>
		";
	}else{
		$main_class_list=$class_select;
	}
	
	$main="
	<script language=\"JavaScript\">
	function jumpMenu(){
		location=\"{$_SERVER['PHP_SELF']}?act=start&mode=set_class_time&sel_year=$sel_year&sel_seme=$sel_seme&class_id=\" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
	}
	</script>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	$main_class_list
	</form>
	";
	return $main;
	
}

//儲存時間設定
function save_set_class_time($sel_year,$sel_seme,$class_id,$class_time,$all_same){
	global $CONN;
	
	$c=class_id_2_old($class_id);
	$Cyear=$c[3];
	
	//若是全部相同則把該年級資料全移除了，否則僅移除該班
	if($all_same=='all'){
		$kind="";
	}elseif($all_same=='year'){
		$kind="and Cyear='$Cyear'";
	}else{
		$kind="and class_id='$class_id'";
	}

	$sql_delete="delete from course_class_time where year=$sel_year and seme='$sel_seme' $kind";
	$CONN->Execute($sql_delete)	or trigger_error("SQL語法執行錯誤： $sql_delete", E_USER_ERROR);
	
	if(!empty($all_same)){
		//列出該年級選單
		$and_where=($all_same=="all")?"":"and c_year='$Cyear'";	
		$sql_select = "select class_id from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' $and_where";
	
		$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
		while(list($classid) = $recordSet->FetchRow()){
			reset($class_time);
			$c="";
			$c=class_id_2_old($classid);
			for($i=0;$i<sizeof($class_time);$i++){
				$sql_insert = "insert into course_class_time (year,seme,class_time,class_id,Cyear) values ($sel_year,'$sel_seme','$class_time[$i]','$classid','$c[3]')";
				$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
			}
		}
	
	}else{
		for($i=0;$i<sizeof($class_time);$i++){
			$sql_insert = "insert into course_class_time (year,seme,class_time,class_id,Cyear) values ($sel_year,'$sel_seme','$class_time[$i]','$class_id','$Cyear')";
			$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
		}
	}
	return true;
}

//取得某一班的時間設定
function get_set_class_time($sel_year,$sel_seme,$class_id=""){
	global $CONN;
	$sql_select = "select class_time from course_class_time where year='$sel_year' and seme='$sel_seme' and class_id='$class_id'";
	$recordSet=$CONN->Execute($sql_select)	or trigger_error("SQL語法執行錯誤： $sql_select", E_USER_ERROR);
	while(list($class_time) = $recordSet->FetchRow()){
		$main[]=$class_time;
	}
	return $main;
}


//預設科目
function &same_course($sel_year,$sel_seme){
	global $CONN,$school_kind_name,$weekN;
	//班級選單
	$sql_select = "select class_id,c_year,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' order by c_year,c_sort";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
	while(list($class_id,$c_year,$c_name) = $recordSet->FetchRow()){
		//取得該班級設定好的科目
		$sql_select = "select ctsn,teacher_sn,ss_id,num from course_teacher_ss_num where class_id='$class_id' and year='$sel_year' and seme='$sel_seme'";
		$recordSet2=$CONN->Execute($sql_select);
		$option="<option value=''>請選擇科目</option>";
		while (list($ctsn,$teacher_sn,$ss_id,$num) = $recordSet2->FetchRow()) {
			//取得該課程原本應有的節數
			$set_ss_n=get_already_class_ss_num($sel_year,$sel_seme,$class_id,$ss_id);
			
			//並找出已設定到課表中的節數
			$ok_ss_n=get_ok_class_ss_num($sel_year,$sel_seme,$class_id,$ss_id);
			
			$n=$set_ss_n-$ok_ss_n;
			
			if($n==0)continue;
			
			$subject_name=&get_ss_name("","","短",$ss_id);
			$teacher_name=get_teacher_name($teacher_sn);
			$option.="<option value='$ctsn'>$n $subject_name (".$teacher_name.")</option>\n";
		}
		//製作科目選單
		$select_ss="
		<select name='set_ctsn[$class_id]'>
		$option
		</select>";
		
		$class_name_chk.="<input type='checkbox' name='set_class_id[]' value='$class_id'>
		".$school_kind_name[$c_year]."".$c_name."班 $select_ss
		<br>";
	}
	
	
	//製作課表
	$main_class_list=&make_preview_tbl($sel_year,$sel_seme);
	
	//set_class_id
	$main="
	<script language=\"JavaScript\">
	function selectall() {
	  for (i=0;i<document.myform.elements.length;i++) {
	    document.myform.elements[i].checked=true;
	  }
	}
	</script>
	<table cellspacing='1' cellpadding='4' bgcolor='#C9DD9F'>
	<tr><td>預設科目設定<input type='button' value='全部勾選' onClick='javascript:selectall();'></td></td></tr>
	<tr bgcolor='#FFFFFF' class='small'>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	<td>$class_name_chk<br>
		
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='mode' value='same_course_day_set'>
	<input type='hidden' name='act' value='save_same_course'>
	<input type='submit' value='確定'></td>
	</form>
	</tr>
	</table>
	
	<p>$main_class_list</p>
	";
	return $main;
}


//找出全校總課表
function &make_preview_tbl($sel_year,$sel_seme){
	global $CONN,$school_kind_name,$weekN,$mode,$midnoon;
	//找出今年度每日課堂最多的節數
	$sections=get_most_class($sel_year,$sel_seme);
	
	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center'>".$weekN[$i-1]."</td>";
	}
	
	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	//取得課表
	for ($j=1;$j<=$sections;$j++){
		if ($j==$midnoon){
			$all_class.= "<tr bgcolor='white' class='small'><td colspan='$dayn' align='center'>午休</td></tr>\n";
		}
		$all_class.="<tr bgcolor='#E1ECFF' class='small'><td align='center'>$j</td>";
		//列印出各節
		for ($i=1;$i<=count($weekN); $i++) {
			$k2=$i."_".$j;
			
			//找出該時間所有的課程
			$have_data=get_ok_class_ss($sel_year,$sel_seme,$i,$j);
			$show="";
			if(!empty($have_data) and is_array($have_data) and sizeof($have_data)!=0){
				
				for($k=0;$k<sizeof($have_data);$k++){
					$d=explode(",",$have_data[$k]);
					//$class_id=$d[0];
					//$teacher_sn=$d[1];
					//$ss_id=$d[2];
					//$ctmp_sn=$d[3];
					$tc=class_id_2_old($d[0]);
					$subject_name=&get_ss_name("","","短",$d[2]);
					$show.="<br><a href='{$_SERVER['PHP_SELF']}?act=del_same_course&mode=$mode&ctmp_sn=$d[3]&sel_year=$sel_year&sel_seme=$sel_seme'>刪</a>：".$tc[5]."-".$subject_name;
				}
			}
			$tool=(!empty($show))?"<a href='{$_SERVER['PHP_SELF']}?act=del_same_course&mode=$mode&class_time=$k2&sel_year=$sel_year&sel_seme=$sel_seme'>刪除此節所有課程</a>":"";
			//每一格
			$all_class.="<td align='center' bgcolor='white'>
			$tool
			$show
			</td>\n";
		}
		$all_class.= "</tr>\n" ;
	}
	
	//該班課表
	$main_class_list="
	<table cellspacing='1' cellpadding='2' bgcolor='#9EBCDD'>
	<tr bgcolor='#E1ECFF' class='small'><td align='center'>節</td>$main_a</tr>
	$all_class
	</table>
	";
	return $main_class_list;
}


//儲存預設科目設定
function save_same_course($sel_year,$sel_seme,$set_class_id,$set_ctsn){
	global $CONN,$weekN;
	
	for($i=0;$i<sizeof($set_class_id);$i++){
		$class_id=$set_class_id[$i];
		$c=class_id_2_old($class_id);
		$ctsn=$set_ctsn[$class_id];
		$ctsn_data=get_ctsn_data($ctsn);
		$sql_insert = "insert into course_tmp (year,semester,class_id,teacher_sn,class_year,class_name,ss_id) values 
	($sel_year,'$sel_seme','$class_id','$ctsn_data[teacher_sn]','$c[3]','$c[4]','$ctsn_data[ss_id]')";
		$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
		$ctmp_sn[]=mysql_insert_id();
	}
	return $ctmp_sn;
}

//設定預設科目的時間
function &same_course_day_set($sel_year,$sel_seme,$ctmp){
	global $CONN,$weekN,$midnnon;
	$ctmp_sn=explode(",",$ctmp);
	for($i=0;$i<sizeof($ctmp_sn);$i++){
		//取得暫存的排課表中某一筆的詳細資料
		$ctmp_data=get_ctmp_data($ctmp_sn[$i]);
		$class_id=$ctmp_data[class_id];
		$time=get_set_class_time($sel_year,$sel_seme,$class_id);
		if($i==0){
			//母樣本
			$no1_time=$time;
		}else{
			//假如是第二次以後，均需和母樣本做比較
			for($i=0;$i<sizeof($time);$i++){
				if(in_array($time[$i],$no1_time))$ok_time[]=$time[$i];
			}
			//接著把結果當作母樣本
			$no1_time=$ok_time;
		}
	}
	
	//找出今年度每日課堂最多的節數
	$sections=get_most_class($sel_year,$sel_seme);
	
	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center'>".$weekN[$i-1]."</td>";
	}
	
	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	//取得課表
	for ($j=1;$j<=$sections;$j++){
		if ($j==$midnoon){
			$all_class.= "<tr bgcolor='white' class='small'><td colspan='$dayn' align='center'>午休</td></tr>\n";
		}
		$all_class.="<tr bgcolor='#E1ECFF' class='small'><td align='center'>$j</td>";
		//列印出各節
		for ($i=1;$i<=count($weekN); $i++) {
			$k2=$i."_".$j;
			
			//找出該時間所有的課程
			$have_data=get_ok_class_ss($sel_year,$sel_seme,$i,$j);
			$show="";
			if(!empty($have_data) and is_array($have_data) and sizeof($have_data)!=0){
				
				for($k=0;$k<sizeof($have_data);$k++){
					$d=explode(",",$have_data[$k]);
					//$class_id=$d[0];
					//$teacher_sn=$d[1];
					//$ss_id=$d[2];
					$tc=class_id_2_old($d[0]);
					$subject_name=&get_ss_name("","","短",$d[2]);
					$teacher_name=get_teacher_name($d[1]);
					$show.="<br>".$tc[5]."-".$subject_name."(".$teacher_name.")";
				}
			}
			$chk_box=(in_array($k2,$no1_time))?"<input type='radio' name='class_time' value='$k2'>":"";
			//每一格
			$all_class.="<td align='center' bgcolor='$color'>
			$chk_box
			$show
			</td>\n";
		}
		$all_class.= "</tr>\n" ;
	}
	//該班課表
	$main_class_list="
	<table cellspacing='1' cellpadding='2' bgcolor='#9EBCDD'>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<tr bgcolor='#E1ECFF' class='small'><td align='center'>節</td>$main_a</tr>
	$all_class
	<tr bgcolor='#E1ECFF'><td colspan='6' align='center'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='ctmp' value='$ctmp'>
	<input type='hidden' name='act' value='update_same_course'>
	<input type='submit' value='設定'>
	</td></tr>
	</form>
	</table>
	";
	
	
	return $main_class_list;
}


//更新預設科目設定中的日期設定
function update_same_course($sel_year,$sel_seme,$class_time,$ctmp){
	global $CONN;
	$t=explode("_",$class_time);
	$ctmp_sn=explode(",",$ctmp);
	for($i=0;$i<sizeof($ctmp_sn);$i++){
		$sql_update = "update course_tmp set day='$t[0]',sector='$t[1]' where ctmp_sn=$ctmp_sn[$i]";
		$CONN->Execute($sql_update)	or trigger_error("SQL語法執行錯誤： $sql_update", E_USER_ERROR);
	}
	return true;
}

//刪除預設科目設定中的某一節
function del_same_course($sel_year,$sel_seme,$class_time="",$ctmp_sn=""){
	global $CONN;
	if(empty($ctmp_sn)){
		$t=explode("_",$class_time);
		$kind="day='$t[0]' and sector='$t[1]'";
	}else{
		$kind="ctmp_sn='$ctmp_sn'";
	}
	$sql_delete = "delete from course_tmp where $kind and year='$sel_year' and semester='$sel_seme'";
	$CONN->Execute($sql_delete)	or trigger_error("SQL語法執行錯誤： $sql_delete", E_USER_ERROR);
	
	return true;
}


//取得某一設定好的科目詳細資料
function get_ctsn_data($ctsn){
	global $CONN;
	$sql_select = "select * from course_teacher_ss_num where ctsn='$ctsn'";
	$recordSet=$CONN->Execute($sql_select);
	$array=$recordSet->FetchRow();
	return $array;
}

//取得暫存的排課表中某一筆的詳細資料
function get_ctmp_data($ctmp_sn){
	global $CONN;
	$sql_select = "select * from course_tmp where ctmp_sn='$ctmp_sn'";
	$recordSet=$CONN->Execute($sql_select);
	$array=$recordSet->FetchRow();
	return $array;
}


//找出某一節功課表中已設定的所有課程
function get_ok_class_ss($sel_year,$sel_seme,$day,$sector){
	global $CONN;
	$sql_select = "select ctmp_sn,class_id,teacher_sn,ss_id from course_tmp where day='$day' and sector='$sector' and year='$sel_year' and  semester='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($ctmp_sn,$class_id,$teacher_sn,$ss_id) = $recordSet->FetchRow()){
		$main[]=$class_id.",".$teacher_sn.",".$ss_id.",".$ctmp_sn;
	}
	return $main;
}

//設定專科教室
function &setup_class($sel_year,$sel_seme,$sel_class=""){
	global $CONN,$school_kind_name,$weekN,$act,$mode;
	$all_year=array();
	//班級選單
	$sql_select = "select class_id,c_year,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' order by c_year,c_sort";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
	while(list($class_id,$c_year,$c_name) = $recordSet->FetchRow()){
		//製作年級陣列
		if(in_array($c_year,$all_year)){
			continue;
		}else{
			$selected=($c_year==$sel_class)?"selected":"";
			$school_sel_1.="<option value='$c_year' $selected>所有".$school_kind_name[$c_year]."級</option>\n";
			$all_year[]=$c_year;
		}
		//製作班級選單
		$selected=($class_id==$sel_class)?"selected":"";
		$school_sel_2.="<option value='$class_id' $selected>".$school_kind_name[$c_year]."".$c_name."班\n";
	}
	
	//製作全校的年級與班級下拉選單
	$selected_all=($sel_class=="all")?"selected":"";
	$school_sel="
	<select name='sel_class' size='1' onChange='jumpMenu()'>
	<option value=''>請選擇設定範圍</option>
	<option value='all' $selected_all>全校</option>
	$school_sel_1
	$school_sel_2
	</select>";
	
	
	if(!empty($sel_class)){
		if($sel_class=="all"){
			$what_class="";
		}elseif(strlen($sel_class) <= 3){
			$y=(strlen($sel_year)==2)?"0".$sel_year:$sel_year;
			$cy=(strlen($sel_class)==1)?"0".$sel_class:$sel_class;
			$what_class="and left(class_id,8)='".$y."_".$sel_seme."_".$cy."'";
		}else{
			$what_class="and class_id='$sel_class'";
		}
		//取得該班級設定好的科目
		$sql_select = "select ss_id from course_teacher_ss_num where year='$sel_year' and seme='$sel_seme' $what_class";
		$recordSet2=$CONN->Execute($sql_select);
		$option="<option value=''>請選擇科目</option>";
		$ss_name=array();
		$same_ss_name=array();
		
		while (list($ss_id) = $recordSet2->FetchRow()) {
			
			$subject_name=&get_ss_name("","","短",$ss_id);

			//判斷是否要秀出該選項
			if(show_this_subject_name($sel_year,$sel_seme,$sel_class,$ss_id))continue;			
	
			//為避免全校時，有重複的科目名稱，故如果是全校的，且在$ss_name中出現過的，且還沒加入重複科目陣列$same_ss_name的科目，就將之加入
			if($sel_class=="all" and in_array($subject_name,$ss_name) and !in_array($subject_name,$same_ss_name)){
				//重複科目陣列
				$same_ss_name[]=$subject_name;
			}
			//所有科目陣列
			$ss_name[$ss_id]=$subject_name;
		}

		//若選的是全校
		if($sel_class=="all"){
			while(list($ss_id,$subject_name)=each($ss_name)){
				//若該科目是重複科目陣列中的科目
				if(in_array($subject_name,$same_ss_name)){
					//把同一個名稱的索引〈ss_id〉取出
					$the_ss_id=array_keys ($ss_name, $subject_name);
					$ss_id=implode(",",$the_ss_id);
				}
				$all_ss_name[$ss_id]=$subject_name;
			}
		}else{
			$all_ss_name=$ss_name;
		}
		
		while(list($ss_id,$subject_name)=each($all_ss_name)){
			$option.="<option value='$ss_id'>$subject_name</option>\n";
		}
		
		//製作科目選單
		$select_ss="
		<select name='ss_id'>
		$option
		</select>";
	}else{
		$select_ss="哪些科目";
	}
	
	//$room_setup=now_room_setup($sel_year,$sel_seme);
	$main_class_list=&make_preview_tbl($sel_year,$sel_seme);
	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		if(document.myform.sel_class.options[document.myform.sel_class.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&mode=$mode&sel_year=$sel_year&sel_seme=$sel_seme&sel_class=\" + document.myform.sel_class.options[document.myform.sel_class.selectedIndex].value;
		}
	}
	</script>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	$school_sel
	的
	$select_ss
	都在
	<input type='text' name='room' size='10'>
	上課
	<input type='hidden' name='act' value='add_room'>
	<input type='submit' value='新增'>
	</form>
	$main_class_list";
	return $main;
}

//新增專科教室的設定
function add_room($sel_year,$sel_seme,$sel_class,$ss_id,$room){
	global $CONN;
	
	//分解某一科的科目代號
	$ss_id_array=explode(",",$ss_id);
	
	//找出這些課程所佔的總節數
	$all_num=get_ssid_all_num($sel_year,$sel_seme,$ss_id_array);

	//找出今年度每日課堂最多的節數
	$max_num=get_most_class($sel_year,$sel_seme);
	
	//假如，所有所需的時間比(最大節數-1)*5天-星期三的四節課，還小
	$no=($all_num-(($max_num-1)*5-4)<=0)?"0":array(1);
	
	for($j=0;$j<sizeof($ss_id_array);$j++){
		$sid=$ss_id_array[$j];
		
		//取得指定課程
		$sql_select = "select ctsn,teacher_sn,class_id,num from course_teacher_ss_num where ss_id=$sid and year='$sel_year' and seme='$sel_seme' order by rand()";
		
		$recordSet=$CONN->Execute($sql_select);
		while(list($ctsn,$teacher_sn,$class_id,$num)=$recordSet->FetchRow()){
			$c=class_id_2_old($class_id);
			
			for($i=0;$i<$num;$i++){
				//找出該科目已經設定的節數
				$n=get_ok_class_ss_num($sel_year,$sel_seme,$class_id,$sid);
				//假如該科目已經比預設的節數還大或一樣大，那跳過
				if($n >= $num)continue;
			
				//找出該教室可以排課的時間
				$time=get_one_class_time($sel_year,$sel_seme,$class_id,$teacher_sn,$room,$no);
				$day=$time[day];
				$sector=$time[sector];
				//開始排
				$sql_insert = "insert into course_tmp (year,semester,class_id,teacher_sn,class_year,class_name,day,sector,ss_id,room,other) values 
			($sel_year,'$sel_seme','$class_id','$teacher_sn','$c[3]','$c[4]','$day','$sector','$sid','$room','room')";
				$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
			}
		}
	}
	return;
}


//找出專科教室的這些課程所佔的總節數
function get_ssid_all_num($sel_year,$sel_seme,$ss_id_array){
	global $CONN,$school_kind_name;
	$all_num=0;
	for($j=0;$j<sizeof($ss_id_array);$j++){
		$sid=$ss_id_array[$j];
		
		//取得指定課程
		$sql_select = "select num from course_teacher_ss_num where ss_id=$sid and year='$sel_year' and seme='$sel_seme'";
		
		$recordSet=$CONN->Execute($sql_select);
		list($num)=$recordSet->FetchRow();
		$all_num+=$num;		
	}
	return $all_num;
}


//判斷是否要秀出該選項，true=有了，不能再出現
function show_this_subject_name($sel_year,$sel_seme,$sel_class,$ss_id){
	global $CONN,$school_kind_name;
	return false;
	if($sel_class=="all"){
		//全校的情況，只要有出現過的ss_id就擋
		$all_ss_id=get_room_setup($sel_year,$sel_seme,$cr_sn,$ss_id,"");
		while(list($cr_sn,$ssid)=each($all_ss_id)){
			if(substr_count($ssid,",")){
				$ss_id_array=explode(",",$ssid);
			}
		}
		
	}elseif(strlen($sel_class)<=3){
		//年級的情況，該年級，該年級底下的某班，全校有出現的就擋
	}else{
		//班級的情況，該班級，該年級，全校有出現的就擋
	}
}

//開始排課
function start_class($sel_year,$sel_seme,$mode=""){
	global $CONN,$weekN,$act;
	if(have_tmp_course($sel_year,$sel_seme)){
		header("location: {$_SERVER['PHP_SELF']}?act=$act&mode=view_tmp&sel_year=$sel_year&sel_seme=$sel_seme");
	}
	//取得所有課程
	$sql_select = "select ctsn,teacher_sn,class_id,ss_id,num from course_teacher_ss_num where year='$sel_year' and seme='$sel_seme' order by rand()";
	$recordSet=$CONN->Execute($sql_select);
	while(list($ctsn,$teacher_sn,$class_id,$ss_id,$num)=$recordSet->FetchRow()){
		
		$c=class_id_2_old($class_id);
		//取得該班導師：
		$the_teacher=get_class_teacher($c[2]);
		
		//第一次跑排課，不排導師
		if($mode==""){
			if($the_teacher[sn]==$teacher_sn)continue;
		}else{
			if($the_teacher[sn]!=$teacher_sn)continue;
		}
		
		for($i=0;$i<$num;$i++){
			//找出該科目已經設定的節數
			$n=get_ok_class_ss_num($sel_year,$sel_seme,$class_id,$ss_id);
			//假如該科目已經比預設的節數還大或一樣大，那跳過
			if($n >= $num)continue;
			
			//找出該班級可以排課的時間
			$time=get_one_class_time($sel_year,$sel_seme,$class_id,$teacher_sn);
						
			$day=$time[day];
			$sector=$time[sector];
			
			//開始排
			$sql_insert = "insert into course_tmp (year,semester,class_id,teacher_sn,class_year,class_name,day,sector,ss_id,other) values 
		($sel_year,'$sel_seme','$class_id','$teacher_sn','$c[3]','$c[4]','$day','$sector','$ss_id','auto')";
			$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
			
		}
	}
	
	//第一次排課後，再排一次導師的課
	if($mode==""){
		start_class($sel_year,$sel_seme,"go");
	}else{
		header("location: {$_SERVER['PHP_SELF']}?act=$act&mode=view_tmp&sel_year=$sel_year&sel_seme=$sel_seme");
	}
	
	return;
}


//找出某一節，可以排課的班級
function get_ok_class_time($sel_year,$sel_seme,$time){
	global $CONN;
	//找出該班有上課的時間
	$sql_select = "select class_id from course_class_time where year='$sel_year' and seme='$sel_seme' and class_time='$time'";
	$recordSet=$CONN->Execute($sql_select)	or trigger_error("SQL語法執行錯誤： $sql_select", E_USER_ERROR);
	while(list($class_id) = $recordSet->FetchRow()){
		$data[]=$class_id;
	}
	return $data;
}

//跑第二次排課，把衝堂的課調開
function fix_class($sel_year,$sel_seme){
	global $CONN,$weekN,$mark;
	//找出今年度每日課堂最多的節數
	$sections=get_most_class($sel_year,$sel_seme);
	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	//取得課表
	for ($j=1;$j<=$sections;$j++){
		//各節
		for ($i=1;$i<=count($weekN); $i++) {
			$k2=$i."_".$j;
			//找出該時間可以排課的班級
			$class_array=get_ok_class_time($sel_year,$sel_seme,$k2);
			
			for($n=0;$n<sizeof($class_array);$n++){
				$class_id=$class_array[$n];
				if(!is_have_class($sel_year,$sel_seme,$class_id,$i,$j)){
					//把應排課卻未排課的時間作成陣列
					$time_no_class[$class_id][]=$i."_".$j;
				}
			}
		}
	}
	
	
	//取得該班尚未設定好的科目
	$sql_select = "select ctmp_sn,class_id,teacher_sn,ss_id from course_tmp where day='' and sector=0 and year='$sel_year' and  semester='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($ctmp_sn,$class_id,$teacher_sn,$ss_id)=$recordSet->FetchRow()){
		$time=$time_no_class[$class_id][0];
		$t=explode("_",$time);
		
		
		$C=class_id_2_old($class_id);
		$SN=&get_ss_name("","","短",$ss_id);
		$TN=get_teacher_name($teacher_sn);
		$log.="<strong>$C[5] 由 $TN 上的 $SN 課( $ctmp_sn )尚未設定好。系統將把該課程安排到星期 $t[0] 第 $t[1] 節</strong><br>";
		
		array_shift ($time_no_class[$class_id]);
		
		//因為衝堂而未排入的教師稱為A教師，應排班級A班，應排時間A節
		//衝堂班級B班，衝堂B節
		//調課對象教師C教師，原任課時間C節
		
		//找出A教師在A節，B班是哪一班，以及哪一節課
		$other=get_teacher_time($sel_year,$sel_seme,$time,$class_id,$teacher_sn);
		
		$other_class_id=$other[class_id];
		$other_ctmp_sn=$other[ctmp_sn];
		
		$OC=class_id_2_old($other_class_id);
		$log.="<font color='#FF0000'>衝堂原因： $TN 在 $OC[5] 的星期 $t[0] 第 $t[1] 節有課，所以衝堂。</font><br>";
		
		//調課，把B班的A師與C師互調，$ctmp_sn指的是B節課
		$logtmp=&chang_class($sel_year,$sel_seme,$time,$class_id,$other_class_id,$teacher_sn,$other_ctmp_sn);
		
		$log.=$logtmp;
		
		$log.="<font color='#0000FF'>系統把".$C[5].$TN."的".$SN."課( $ctmp_sn ) 安排到星期 $t[0] 第 $t[1] 節。</font><br>";
		//把A節排給A教師
		$sql_update = "update course_tmp set day='$t[0]',sector='$t[1]',other='fix' where ctmp_sn='$ctmp_sn'";
		$CONN->Execute($sql_update) or trigger_error("SQL語法錯誤： $sql_update", E_USER_ERROR);
	}
	add_log($log,$mark);
	return;
}


//調課
function &chang_class($sel_year,$sel_seme,$old_time,$class_id,$other_class_id,$teacher_sn,$other_ctmp_sn){
	global $CONN;
	
	//取得原班級的所有時間〈A班〉
	$class_all_time=get_set_class_time($sel_year,$sel_seme,$class_id);
	
	//A節
	$ot=explode("_",$old_time);
	
	//找出該教師以外的教師，C教師
	$sql_select = "select teacher_sn from course_tmp where class_id='$other_class_id' and teacher_sn!='$teacher_sn' and year='$sel_year' and  semester='$sel_seme' group by teacher_sn";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("SQL語法錯誤： $sql_select", E_USER_ERROR);
	while(list($tsn) = $recordSet->FetchRow()){
		$TN=get_teacher_name($teacher_sn);
		$OC=class_id_2_old($other_class_id);
		$OTN=get_teacher_name($tsn);
		$log="系統在".$OC[5]."找到".$OTN."，看該師是否有時間可以調課。<br>";
		
		//找出該教師在該班任教的某一節課〈C節〉
		$class_time_array=get_teacher_class($sel_year,$sel_seme,$other_class_id,$tsn);
		while(list($time,$ctmp_sn)=each($class_time_array)){
			
			$t=explode("_",$time);
			$log.="查看".$OTN."老師星期 $t[0] 第 $t[1] 節是否可以調？";
			//分析每一節，若A師也沒課就OK
			$At=teacher_have_class($sel_year,$sel_seme,$t[0],$t[1],$teacher_sn);
			if($At){
				$ok_at=false;
				$log.="&nbsp;&nbsp;&nbsp;&nbsp;<font color='#808000'>".$TN."不行！</font>";
			}else{
				$ok_at=true;
				$log.="&nbsp;&nbsp;&nbsp;&nbsp;<font color='#9900CC'>".$TN."可以！</font>";
			}
			
			//分析每一節，若C師也沒課就OK
			$Ct=teacher_have_class($sel_year,$sel_seme,$ot[0],$ot[1],$tsn);
			if($Ct){
				$ok_ct=false;
				$log.="&nbsp;&nbsp;&nbsp;&nbsp;<font color='#808000'>但".$OTN."不行！</font><br>";
			}else{
				$ok_ct=true;
				$log.="&nbsp;&nbsp;&nbsp;&nbsp;<font color='#9900CC'>".$OTN."可以！</font><br>";
			}
			
			//分析該節是否有專科教室上的衝突，若沒有就OK
			$OCr=room_have_class($sel_year,$sel_seme,$t[0],$t[1],$other_ctmp_sn);
			if($OCr){
				$ok_ocr=false;
				$log.="&nbsp;&nbsp;&nbsp;&nbsp;<font color='#808000'>但因教室衝突不行！</font><br>";
			}else{
				$ok_ocr=true;
				$log.="&nbsp;&nbsp;&nbsp;&nbsp;<font color='#9900CC'>教室也可以！</font><br>";
			}
			
			$Cr=room_have_class($sel_year,$sel_seme,$ot[0],$ot[1],$ctmp_sn);
			if($Cr){
				$ok_cr=false;
				$log.="&nbsp;&nbsp;&nbsp;&nbsp;<font color='#808000'>但因教室衝突不行！</font><br>";
			}else{
				$ok_cr=true;
				$log.="&nbsp;&nbsp;&nbsp;&nbsp;<font color='#9900CC'>教室也可以！</font><br>";
			}
			
			if($ok_ct and $ok_at and $ok_cr and $ok_ocr){
				$log.="<font color='#0000FF'>系統把".$OC[5]."的".$OTN."老師於星期 $t[0] 第 $t[1] 節上的課調到星期 $ot[0] 第 $ot[1] 節。</font><br>";
				//把找到的C教師上的課，C節換成B節
				$sql_update = "update course_tmp set day='$ot[0]',sector='$ot[1]',other='fix' where ctmp_sn='$ctmp_sn'";
				$CONN->Execute($sql_update) or trigger_error("SQL語法錯誤： $sql_update");
				
				$log.="<font color='#0000FF'>系統把".$OC[5]."的".$TN."老師於星期 $ot[0] 第 $ot[1] 節上的課調到星期 $t[0] 第 $t[1] 節。</font><br>";
				//把原來空白的日期填上後來找到的時間
				$sql_update = "update course_tmp set day='$t[0]',sector='$t[1]',other='fix' where ctmp_sn='$other_ctmp_sn'";
				$CONN->Execute($sql_update) or trigger_error("SQL語法錯誤： $sql_update", E_USER_ERROR);
				return $log;
			}
		}
		$log.="找不到".$OC[5]."可以調課的教師及時間。<br>";
	}
	return $log;
}



//找出某個教師在某個時間是在哪一班上課
function get_teacher_time($sel_year,$sel_seme,$time,$class_id,$teacher_sn){
	global $CONN;
	$t=explode("_",$time);
	$day=$t[0];
	$sector=$t[1];
	$sql_select = "select ctmp_sn,class_id from course_tmp where class_id!='$class_id' and day='$day' and sector='$sector' and teacher_sn='$teacher_sn' and year='$sel_year' and  semester='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($ctmp_sn,$class_id) = $recordSet->FetchRow()){
		$main[ctmp_sn]=$ctmp_sn;
		$main[class_id]=$class_id;
		return $main;
	}
	return;
}

//找出該教師在該班任教的課
function get_teacher_class($sel_year,$sel_seme,$class_id,$teacher_sn){
	global $CONN;
	//找出該教師以外的教師
	$sql_select = "select ctmp_sn,day,sector,ss_id from course_tmp where class_id='$class_id' and teacher_sn='$teacher_sn' and year='$sel_year' and  semester='$sel_seme' order by rand()";
	$recordSet=$CONN->Execute($sql_select);
	while(list($ctmp_sn,$day,$sector,$ss_id) = $recordSet->FetchRow()){
		$time=$day."_".$sector;
		$class[$time]=$ctmp_sn;
	}
	return $class;
}


//查看暫存課表是否已經排過課程
function have_tmp_course($sel_year,$sel_seme){
	global $CONN;
	$sql_select = "select count(*) from course_tmp where other='auto' and year='$sel_year' and  semester='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($n) = $recordSet->FetchRow()){
		if($n > 0)return true;
	}
	return false;
}

//查看某班某課暫存課表是否已經存在
function have_class_in_tmp_course($sel_year,$sel_seme,$ss_id,$teacher_sn,$class_id){
	global $CONN;
	$sql_select = "select count(*) from course_tmp where ss_id='$ss_id' and teacher_sn='$teacher_sn' and class_id='$class_id' and year='$sel_year' and  semester='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($n) = $recordSet->FetchRow()){
		if($n > 0)return true;
	}
	return false;
}

//找出該班級可以排課的時間
function get_one_class_time($sel_year,$sel_seme,$class_id,$teacher_sn,$room="",$no=array(1)){
	global $CONN;
	//找出該班有上課的時間
	$sql_select = "select class_time from course_class_time where year='$sel_year' and seme='$sel_seme' and class_id='$class_id' order by rand()";
	$recordSet=$CONN->Execute($sql_select)	or trigger_error("SQL語法執行錯誤： $sql_select", E_USER_ERROR);
	while(list($class_time) = $recordSet->FetchRow()){
		$t=explode("_",$class_time);
		$day=$t[0];
		$sector=$t[1];
		
		if($room!=""){
			//排除第一節
			if(!empty($no) and is_array($no) and in_array($sector,$no)) continue;
			
			//判斷該節該教室是否已經有課
			if(is_room_have_class($sel_year,$sel_seme,$room,$day,$sector)) continue;
		}
		//判斷某一節該班是否已經排好課了。
		if(is_have_class($sel_year,$sel_seme,$class_id,$day,$sector)) continue;
		
		//看看該時間該老師是否已經有上課
		if(teacher_have_class($sel_year,$sel_seme,$day,$sector,$teacher_sn)) continue;
		
		$time[day]=$day;
		$time[sector]=$sector;
		return $time;	
	}
	return false;
}

//找出某一節課某老師是否已經有課了
function teacher_have_class($sel_year,$sel_seme,$day,$sector,$teacher_sn){
	global $CONN;
	$sql_select = "select count(*) from course_tmp where teacher_sn='$teacher_sn' and year='$sel_year' and  semester='$sel_seme' and day='$day' and sector='$sector'";
	$recordSet=$CONN->Execute($sql_select)	or trigger_error("SQL語法執行錯誤： $sql_select", E_USER_ERROR);
	list($n) = $recordSet->FetchRow();
	if($n > 0)return true;
	return false;
}


//判斷某一節某一班是否已經排好課了。tmp
function is_have_class($sel_year,$sel_seme,$class_id,$day,$sector){
	global $CONN;
	$sql_select = "select ss_id from course_tmp where day='$day' and sector='$sector' and year='$sel_year' and  semester='$sel_seme' and class_id='$class_id'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($ss_id) = $recordSet->FetchRow()){
		if(!empty($ss_id))return true;
	}
	return false;
}

//判斷該節該教室是否已經有課
function is_room_have_class($sel_year,$sel_seme,$room,$day,$sector){
	global $CONN;
	$sql_select = "select ss_id from course_tmp where day='$day' and sector='$sector' and year='$sel_year' and  semester='$sel_seme' and room='$room'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($ss_id) = $recordSet->FetchRow()){
		if(!empty($ss_id))return true;
	}
	return false;
}

//判斷該課程該教室是否已經有課
function room_have_class($sel_year,$sel_seme,$day,$sector,$ctmp_sn){
	global $CONN;
	//先分析ctmp_sn這堂課是否會用到專科教室
	$sql_select = "select room from course_tmp where ctmp_sn='$ctmp_sn'";
	$recordSet=$CONN->Execute($sql_select);
	list($room) = $recordSet->FetchRow();
	if(empty($room))return false;
	
	$sql_select = "select room from course_tmp where day='$day' and sector='$sector' and year='$sel_year' and  semester='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($have_room) = $recordSet->FetchRow()){
		if(!empty($have_room) and $have_room==$room)return true;
	}
	return false;
}

//觀看暫排課表
function &view_tmp($sel_year,$sel_seme,$class_id){
	global $act,$mode;

	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu",$class_id);

	//取得年度與學期的下拉選單
	$date_select="$sel_year 學年第 $sel_seme 學期";

	$list_class_table=&search_class_tmp_table($sel_year,$sel_seme,$class_id,"view");

	$main="
	<script language=\"JavaScript\">
	function jumpMenu(){
		location=\"{$_SERVER['PHP_SELF']}?act=$act&mode=$mode&sel_year=$sel_year&sel_seme=$sel_seme&class_id=\" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
	}
	</script>
	<table cellspacing='1' cellpadding='2'  bgcolor=#9EBCDD>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	<tr bgcolor='#F7F7F7' class='small'>
	<td>$date_select $class_select
	<a href='{$_SERVER['PHP_SELF']}?act=fix_class&sel_year=$sel_year&sel_seme=$sel_seme'>修正衝堂</a>，
	<a href='{$_SERVER['PHP_SELF']}?act=save_all&sel_year=$sel_year&sel_seme=$sel_seme'>全部匯入正式課表中</a>
	</td>
	</tr>
	</form>
	</table>
	$list_class_table
	";
	return $main;
}

//列出某個班級的暫存課表
function &search_class_tmp_table($sel_year="",$sel_seme="",$class_id="",$tsn=""){
	global $CONN,$class_year,$conID,$weekN,$school_menu_p,$midnoon;

	if(empty($class_id)){
		//取得任教班級代號
		$class_num=get_teach_class();
		$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);
	}

	//取得班級資料
	$the_class=get_class_all($class_id);

	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	$sql_select = "select teacher_sn,day,sector,ss_id,other from course_tmp where class_id='$class_id' order by day,sector";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($teacher_sn,$day,$sector,$ss_id,$other)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$r[$k]=$other;
	}

	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >星期".$weekN[$i-1]."</td>";
	}

	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	$sections=$sm[sections];

	if(!empty($class_id)){

		//取得課表
		for ($j=1;$j<=$sections;$j++){

			if ($j==$midnoon){
				$all_class.= "<tr bgcolor='white' class='small'><td colspan='$dayn' align='center'>午休</td></tr>\n";
			}

			$all_class.="<tr bgcolor='#E1ECFF' class='small'><td align='center'>$j</td>";

			//列印出各節
			for ($i=1;$i<=count($weekN); $i++) {
				$k2=$i."_".$j;
				
				$teacher_search_mode=(!empty($tsn) and $tsn==$b[$k2])?true:false;

				//科目的下拉選單
				$subject_sel="".get_ss_name("","","短",$a[$k2])."";
				
				//教師的下拉選單
				$teacher_sel="<font color='#996699'>".get_teacher_name($b[$k2])."</font>";
				
				$align="align='center'";
				$color=($r[$k2]=="fix")?"#ccffcc":"white";

				//每一格
				$all_class.="<td $align bgcolor='$color' width='90'>
				$subject_sel<br>
				$teacher_sel
				</td>\n";
				$sidn=$a[$k2];
				$ss_num[$sidn]++;
			}
			
			$all_class.= "</tr>\n" ;
		}
		
		if(!empty($tsn))$class_name="<tr bgcolor='#B9C5FF' class='small'><td colspan=6>$the_class[name] 課程表</td></tr>";

		//該班課表
		$main_class_list="
		$class_name
		<tr bgcolor='#E1ECFF'><td align='center'>節</td>$main_a</tr>
		$all_class
		";
	}else{
		$main_class_list="";
	}
	
	//取得該班已經設定好的科目
	$sql_select = "select teacher_sn,ss_id,num from course_teacher_ss_num where class_id='$class_id' and year='$sel_year' and seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($tsn,$sid,$n)=$recordSet->FetchRow()){
		$ok_n=$ss_num[$sid];
		$subject_name=&get_ss_name("","","短",$sid);
		$teacher_name=get_teacher_name($tsn);
		$color=($ok_n!=$n)?"red":"white";
		$data.="<tr bgcolor='$color' class='small'>
		<td>$subject_name</td>
		<td align='center'>$teacher_name</td>
		<td align='center'>$n</td>
		<td align='center'>$ok_n</td>
		</tr>";
	}
	$ss_setup="<table cellspacing='1' cellpadding='2' bgcolor='#008000'>
	<tr align='center' bgcolor='#C7DB9D' class='small'>
	<td>科目</td><td>教師</td><td>預設<br>節數</td><td>已排<br>節數</td></tr>
	$data</table>";

	$main="
	<table cellspacing='0' cellpadding='0'><tr><td valign='top'>
		<table border='0' cellspacing='1' cellpadding='4' bgcolor='#9EBCDD'>
		$main_class_list
		</table>
	</td><td width='10'></td><td valign='top'>$ss_setup</td></tr></table>
	";
	return  $main;
}

//把所有排出的課表匯入正式課表中
function save_all($sel_year,$sel_seme,$mode=""){
	global $CONN,$act;
	
	if($mode=="replace"){
		$sql_delete="delete from score_course where year='$sel_year' and semester='$sel_seme'";
		$CONN->Execute($sql_delete) or trigger_error("錯誤訊息： $sql_delete", E_USER_ERROR);
	}elseif($mode=="no"){
		header("location: {$_SERVER['PHP_SELF']}?act=start&mode=view_tmp&sel_year=$sel_year&sel_seme=$sel_seme");
		return ;
	}else{
		$sql_select = "select count(*) from score_course where year='$sel_year' and semester='$sel_seme'";
		$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
		list($n)= $recordSet->FetchRow();
		if($n > 0){
			$msg="<form action='{$_SERVER['PHP_SELF']}' method='post'>
			第 $sel_year 學年度第 $sel_seme 學期已經有課表資料，是否要覆蓋舊資料？
			或是取消目前執行的動作？
			<p><input type='radio' name='mode' value='replace' checked>
			把原有舊資料全部覆蓋掉，換上新的排課資料。</p>
			<p><input type='radio' name='mode' value='no'>
			取消目前的動作，不對原有的課表資料作任何更變。</p>
			<input type='hidden' name='act' value='$act'>
			<div align='center'><input type='submit' value='執行'></div>
			</form>";
			
			$main=&error_tbl("第 $sel_year 學年度第 $sel_seme 學期已經有課表資料",$msg);
			return $main;
		}
	}
	
	$sql_select = "select year,semester,class_id,teacher_sn,class_year,class_name,day,sector,ss_id,room from course_tmp where year='$sel_year' and  semester='$sel_seme' order by class_id,day,sector";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($year,$semester,$class_id,$teacher_sn,$class_year,$class_name,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		save_to_course($year,$semester,$class_id,$teacher_sn,$class_year,$class_name,$day,$sector,$ss_id,$room);
	}
	return "ok";
}

//存入正式課表
function save_to_course($year,$semester,$class_id,$teacher_sn,$class_year,$class_name,$day,$sector,$ss_id,$room){
	global $CONN;
	$sql_insert = "insert into score_course 
	(year,semester,class_id,teacher_sn,class_year,class_name,day,sector,ss_id,room) values 
	('$year','$semester','$class_id','$teacher_sn','$class_year','$class_name','$day','$sector','$ss_id','$room')";
	$CONN->Execute($sql_insert)	or trigger_error("SQL語法執行錯誤： $sql_insert", E_USER_ERROR);
	return true;
}

//重新排課
function &re_start($sel_year,$sel_seme){
	global $CONN;
	$main="
	<table>
	<tr><td>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<input type='checkbox' name='del_null' value=true>清除「預設科目」的設定以及所排的課程<br>
	<input type='checkbox' name='del_room' value=true>清除「專科教室」設定以及所排的課程<br>
	<input type='checkbox' name='del_auto_fix' value=true checked>清除自動排的課程<br>
	<input type='checkbox' name='re_start' value=true checked>重排課程
	<font size='2' color='#0000FF'>〈選此項會強制執行「清除自動排的課程」〉</font><br>
	<input type='hidden' name='act' value='re_start_go'><br>
	<input type='submit' value='執行'>
	</form>
	</td></tr>
	</table>
	";
	return $main;
}

//執行重新排課命令
function re_start_go($sel_year,$sel_seme,$del_null,$del_room,$del_auto_fix,$re_start){
	global $CONN;
	
	$mode="re_start";
	
	if($del_null){
		$sql_delete="delete FROM course_tmp WHERE other='' and year=$sel_year and semester='$sel_seme'";
		$CONN->Execute($sql_delete)	or trigger_error("SQL語法執行錯誤： $sql_delete", E_USER_ERROR);
		$mode="same_course";
	}
	if($del_room){
		$sql_delete="delete FROM course_tmp WHERE other='room' and year=$sel_year and semester='$sel_seme'";
		$CONN->Execute($sql_delete)	or trigger_error("SQL語法執行錯誤： $sql_delete", E_USER_ERROR);
		$mode="setup_class";
	}
	if($del_auto_fix or $re_start){
		$sql_delete="delete FROM course_tmp WHERE (other='auto' or other='fix') and year=$sel_year and semester='$sel_seme'";
		$CONN->Execute($sql_delete)	or trigger_error("SQL語法執行錯誤： $sql_delete", E_USER_ERROR);
		$mode="setup_class";
	}
	if($re_start){
		$mode="start_class";
	}
	return $mode;
}

?>
