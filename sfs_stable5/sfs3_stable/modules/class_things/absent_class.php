<?php

// $Id: absent_class.php 6059 2010-08-31 02:48:21Z brucelyc $

/*引入學務系統設定檔*/
include_once "config.php";

//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

if ($is_absent=='n') header("Location: name_form.php");

sfs_check();
$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];

if(!empty($_REQUEST[this_date])){
	$d=explode("-",$_REQUEST[this_date]);
}else{
	$d=explode("-",date("Y-m-d"));
}
$year=(empty($_REQUEST[year]))?$d[0]:$_REQUEST[year];
$month=(empty($_REQUEST[month]))?$d[1]:$_REQUEST[month];
$day=(empty($_REQUEST[day]))?$d[2]:$_REQUEST[day];

$act=$_REQUEST[act];
$One=$_REQUEST[One];
$year_name=$_POST[year_name];
$class_name=$_POST[class_name];
$class_num=$_POST[class_num];

//取得登入老師的id
$teacher_sn=$_SESSION['session_tea_sn'];

//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);
$class_id = $class_name[3];
if ($year_name && $class_name && $class_num) {
	$class_num=($year_name+$IS_JHORES).sprintf("%02d%02d",$class_name,$class_num);
	$sql="select stud_id from stud_base where curr_class_num='$class_num' and stud_study_cond='0'";
	$rs=$CONN->Execute($sql);
	$stud_id=$rs->fields[stud_id];
	if (!empty($stud_id)) $One=$stud_id;
}

if ($One) {
	$sql="select student_sn from stud_base where stud_id='$One' and ($sel_year - stud_study_year between 0 and 9)";
	$rs=$CONN->Execute($sql);
	$student_sn=$rs->fields[student_sn];
	if (!$student_sn) $One="";
}

//執行動作判斷
if($act=="儲存登記"){
	if ($One) {
		add_one($sel_year,$sel_seme,$_POST[class_id],$One,$_POST[s]);
		header("location: $_SERVER[SCRIPT_NAME]?class_id=$_POST[class_id]&One=$_POST[One]&this_date=$_POST[date]");
	} else {
		add_all($sel_year,$sel_seme,$_POST[class_id],$_POST[date],$_POST[s]);
		header("location: $_SERVER[SCRIPT_NAME]?this_date=$_POST[date]&class_id=$_POST[class_id]");
	}
}elseif($act=="clear"){
	clear_data($_GET[this_date],$_GET[stud_id]);
	if ($One)
		header("location: $_SERVER[SCRIPT_NAME]?this_date=$_GET[this_date]&class_id=$_GET[class_id]&One=$One");
	else
		header("location: $_SERVER[SCRIPT_NAME]?this_date=$_GET[this_date]&class_id=$_GET[class_id]");
}else{
	$main=&mainForm($sel_year,$sel_seme,$class_name[3],$_POST[thisOne],$One);
}


//秀出網頁
head("缺曠課紀錄");
print_menu($menu_p);

if(sizeof($_POST[thisOne])>0){
	foreach($_POST[thisOne] as $e_name){
		$js.="
		function disableall_cb".$e_name."() {
		  for (i=0;i<document.myform.cb".$e_name.".length;i++) {
		    document.myform.cb".$e_name."[i].checked=false;
		    document.myform.cb".$e_name."[i].disabled=true;
		  }
		}
		function ableall_cb".$e_name."() {
		  for (i=0;i<document.myform.cb".$e_name.".length;i++) {
		    document.myform.cb".$e_name."[i].disabled=false;
		  }
		}
	";
	}
}elseif(!empty($_REQUEST[One])){
	for ($j=1;$j<=5;$j++)
	$js.="
		function disableall_cb_".$j."() {
		  for (i=0;i<document.myform.cb_".$j.".length;i++) {
		    document.myform.cb_".$j."[i].checked=false;
		    document.myform.cb_".$j."[i].disabled=true;
		  }
		}
		function ableall_cb_".$j."() {
		  for (i=0;i<document.myform.cb_".$j.".length;i++) {
		    document.myform.cb_".$j."[i].disabled=false;
		  }
		}
	";
}else{
	$js="";
}


echo "<style type=\"text/css\">
<!--
.calendarTr {font-size:12px; font-weight: bolder; color: #006600}
.calendarHeader {font-size:12px; font-weight: bolder; color: #cc0000}
.calendarToday {font-size:12px; background-color: #ffcc66}
.calendarTheday {font-size:12px; background-color: #ccffcc}
.calendar {font-size:11px;font-family: Arial, Helvetica, sans-serif;}
.dateStyle {font-size:15px;font-family: Arial; color: #cc0066; font-weight: bolder}
-->
</style>
<script language=\"JavaScript\">
	$js
</script>
";
echo $main;
foot();

//主要輸入畫面
function &mainForm($sel_year,$sel_seme,$class_id="",$thisOne=array(),$One=""){
	global $year,$month,$day,$SFS_PATH_HTML,$CONN;
	
	if (!empty($One) && empty($class_id)) {
		$sql="select curr_class_num from stud_base where stud_id='$One' and ($sel_year - stud_study_year between 0 and 9)";
		$rs=$CONN->Execute($sql);
		$curr_class_num=$rs->fields[curr_class_num];
		$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($curr_class_num,0,-4),substr($curr_class_num,-4,2));
	}
	
	//取得該班及學生名單，以及填寫表格
//	if(!empty($class_id)){
		$signForm=&signForm($sel_year,$sel_seme,$class_id,$thisOne,$One);
//	}
//	if(!empty($class_id)){
		$cal = new MyCalendar;
		if ($One)
			$cal->linkStr="&class_id=$class_id&One=$One";
		else
			$cal->linkStr="&class_id=$class_id";
		$cal->setStartDay(1);
		$cal->getDateLink();
		$mc=$cal->getMonthView($month,$year,$day);
		$the_cal="
		<table cellspacing='1' cellpadding='2' bgcolor='#E2ECFC' class='small'>
		<tr bgcolor='#FEFBDA'>
		<td align='center'>		
		<a href='$_SERVER[SCRIPT_NAME]?act=$_REQUEST[act]&this_day=$today&class_id=$class_id' class='box'><img src='".$SFS_PATH_HTML."images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
		</td></tr>
		<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
		</table>
		";
//	}
	
	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<form action='$_SERVER[SCRIPT_NAME]' method='post'>
	<tr bgcolor='#FFFFFF'><td>
	紀錄日期： <font color='blue'>$year</font> 年 <font color='blue'>$month</font> 月 <font color='blue'>$day</font> 日</td></tr>
	</form>
<!--
	<form action='$_SERVER[SCRIPT_NAME]' method='post'>
	<tr bgcolor='#ffffff'>
	<td>或直接輸入學號：<input type='text' size='10' maxsize='10' name='One'><input type='hidden' name='this_date' value='$year-$month-$day'>
	</tr>
	</form>
	<form action='$_SERVER[SCRIPT_NAME]' method='post'>
	<tr bgcolor='#ffffff'>
	<td>或直接輸入班級座號：<input type='text' size='2' maxsize='2' name='year_name'> 年級 <input type='text' size='2' maxsize='2' name='class_name'> 班 <input type='text' size='2' maxsize='2' name='class_num'> 號 <input type='submit' value='確定'><input type='hidden' name='this_date' value='$year-$month-$day'>
	</tr>
	</form>
-->	
	</table>
	<table cellspacing='1' cellpadding='3'>
	<tr>
	<td valign='top'>$signForm</td>
	<td valign='top'>$the_cal</td>
	</tr>
	</table>
	";
	return $main;
}

//取得某一筆資料
function getOneDaydata($stud_id,$year,$month,$day){
	global $CONN;
	$sql_select="select section, absent_kind from stud_absent where stud_id='$stud_id' and date='$year-$month-$day'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($section,$kind)=$recordSet->FetchRow()){
		$theData[$section]=$kind;
	}
	return $theData;
}

//取得該班及學生名單，以及填寫表格
function &signForm($sel_year,$sel_seme,$class_id,$thisOne=array(),$One=""){
	global $year,$month,$day,$CONN,$weekN;
	//取得某班學生陣列
	$c=class_id_2_old($class_id);
	
	//取得該班有幾節課
	$sql_select = "select sections from score_setup where year = '$c[0]' and semester='$c[1]' and class_year='$c[3]'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("SQL語法錯誤： $sql_select", E_USER_ERROR);
	list($all_sections) = $recordSet->FetchRow();
		for($i=1;$i<=$all_sections;$i++){
			$sections_txt.="<td>$i 節</td>";
		}		
	
	//取得缺曠課類別
	$absent_kind_array= SFS_TEXT("缺曠課類別");
	
	$option="
	<option value=''></option>";
	foreach($absent_kind_array as $k){
		$option.="<option value='$k'>$k</option>\n";
	}
	

	//取得學生陣列
	$stud=get_stud_array($c[0],$c[1],$c[3],$c[4],"id","name");
	//座號
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$c[3].sprintf("%02d",$c[4]);
	$sql_num="select stud_id,seme_num from stud_seme where seme_class='$seme_class' and seme_year_seme='$seme_year_seme'";
	$rs_num=$CONN->Execute($sql_num);
	while (!$rs_num->EOF) {
		$stud_id=$rs_num->fields['stud_id'];
		$num[$stud_id]=$rs_num->fields['seme_num'];
		$rs_num->MoveNext();
	}

	//若是整天請假則一些欄位要合併起來
	$coln=$all_sections+3;

	if (empty($One)) {
	
	foreach($stud as $id=>$name){

		//取得該學生資料
		$aaa=getOneDaydata($id,$year,$month,$day);
		
		//各一節資料
		$blank="";
		for($i=1;$i<=$all_sections;$i++){
			$blank.="<td>$aaa[$i]</td>";
		}
		
		//編輯模式
		if(in_array($id,$thisOne) or $id==$One){
			$e_name="cb".$id;
			
			//曠課種類
			$select="<select name='s[$id][kind]' id='tool'>$option</select>";
			
			$checked="checked";
			
			//找出每一節課
			if(empty($aaa[allday])){
				$sections_data="";
				$close_allday=false;
				for($i=1;$i<=$all_sections;$i++){
					$sv=(!empty($aaa[$i]))?$aaa[$i]:"<input type='checkbox' id='$e_name' name='s[$id][section][]' value='$i'>";
					$sections_data.="<td>$sv</td>\n";
					//只要有紀錄任何一節曠課，就不給使用「整天」的功能
					if(!empty($aaa[$i]))$close_allday=true;
				}
			}else{
				$sections_data="";
			}
			
			//升旗
			$ufv=(!empty($aaa[uf]))?$aaa[uf]:"<input type='checkbox' id='$e_name' name='s[$id][section][]' value='uf'>";
			$uf=(!empty($aaa[allday]))?"<td bgcolor='#FFFFFF' colspan=$coln>$aaa[allday]</td>":"<td bgcolor='#FBF8B9'>$ufv</td>";
			
			//降旗
			$dfv=(!empty($aaa[df]))?$aaa[df]:"<input type='checkbox' id='$e_name' name='s[$id][section][]' value='df'>";
			$df=(!empty($aaa[allday]))?"":"<td bgcolor='#FFE6D9'>$dfv</td>";
			
			//整天
			//看是否要關閉「整天」功能
			$disabled=($close_allday or !empty($aaa[uf]) or !empty($aaa[df]))?"disabled":"";
			$allday=(!empty($aaa[allday]))?$aaa[allday]:"<input type='checkbox' id='cb_all' $disabled name='s[$id][section][]' value='allday' onClick=\"if (this.checked==false){javascript:ableall_$e_name() } else { javascript:disableall_$e_name()}\">";
			
			$all_day=(!empty($aaa[allday]))?"":"<td bgcolor='#E8F9C8'>$allday</td>";
			
			
			
			$select_col="<td bgcolor='#ECff8F9'>$select</td>";
			$tool="缺曠課種類";
		}else{
			//觀看模式
			$sections_data=(!empty($aaa[allday]))?"":$blank;
			$checked="";
			$uf=(!empty($aaa[allday]))?"<td bgcolor='#FFFFFF' colspan=$coln align='center'>$aaa[allday]</td>":"<td bgcolor='#FBF8B9'>$aaa[uf]</td>";
			$df=(!empty($aaa[allday]))?"":"<td bgcolor='#FFE6D9'>$aaa[df]</td>";
			$all_day=(!empty($aaa[allday]))?"":"<td bgcolor='#E8F9C8'>$aaa[allday]</td>";
			$tool="功能";
			$select_col="<td bgcolor='#ECff8F9' align='center'><a href='$_SERVER[SCRIPT_NAME]?class_id=$class_id&One=$id&this_date=$year-$month-$day'>編輯</a>|<a href='$_SERVER[SCRIPT_NAME]?act=clear&class_id=$class_id&stud_id=$id&this_date=$year-$month-$day'>清除</a></td>";
		}
		
		//勾選盒
		$chkBox=(sizeof($thisOne)>0 or !empty($One))?"":"<input type='checkbox' name='thisOne[]' value='$id' $checked>";
		
		//每一列資料
		//整天沒來
		$data.="
		<tr bgcolor='#FFFFFF'>
		<td>$id</td>
		<td>".$num[$id]."</td>
		<td>$chkBox<a href='$_SERVER[SCRIPT_NAME]?class_id=$class_id&One=$id&this_date=$year-$month-$day'>$name</a></td>		
		$uf
		$sections_data
		$df		
		$all_day
		$select_col
		</tr>";
	}
	} else {
		$date_title="<td align='center'>日期</td>";
		$tool="缺曠課種類";
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$sql="select a.stud_name,b.seme_num from stud_base a,stud_seme b where a.stud_id='$One' and a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme'";
		$rs=$CONN->Execute($sql);
		$stud_name=$rs->fields[stud_name];
		$seme_num=$rs->fields[seme_num];
		$fday=mktime(0,0,0,$month,$day,$year);
		$dd=getdate($fday);
		$fday-=($dd[wday]-1)*86400;
		for ($j=0;$j<=4;$j++) {
			//取得該學生資料
			$smkt=$fday+$j*86400;
			$syear=date("Y",$smkt);
			$smonth=date("m",$smkt);
			$sday=date("d",$smkt);
			$dd=getdate($smkt);
			$did=date("Y-m-d",$smkt);
			$e_name="cb_".$dd[wday];
			$aaa=getOneDaydata($One,$syear,$smonth,$sday);
			//曠課種類
			$select="<select name='s[$did][kind]' id='tool'>$option</select>";
			$checked="checked";

			//找出每一節課
			if(empty($aaa[allday])){
				$sections_data="";
				$close_allday=false;
				for($i=1;$i<=$all_sections;$i++){
					$sv=(!empty($aaa[$i]))?$aaa[$i]:"<input type='checkbox' id='$e_name' name='s[$did][section][]' value='$i'>";
					$sections_data.="<td>$sv</td>\n";
					//只要有紀錄任何一節曠課，就不給使用「整天」的功能
					if(!empty($aaa[$i]))$close_allday=true;
				}
			}else{
				$sections_data="";
			}
			
			//升旗
			$ufv=(!empty($aaa[uf]))?$aaa[uf]:"<input type='checkbox' id='$e_name' name='s[$did][section][]' value='uf'>";
			$uf=(!empty($aaa[allday]))?"<td bgcolor='#FFFFFF' colspan=$coln align='center'>$aaa[allday]</td>":"<td bgcolor='#FBF8B9'>$ufv</td>";
			
			//降旗
			$dfv=(!empty($aaa[df]))?$aaa[df]:"<input type='checkbox' id='$e_name' name='s[$did][section][]' value='df'>";
			$df=(!empty($aaa[allday]))?"":"<td bgcolor='#FFE6D9'>$dfv</td>";
			
			//整天
			//看是否要關閉「整天」功能
			$disabled=($close_allday or !empty($aaa[uf]) or !empty($aaa[df]))?"disabled":"";
			$allday=(!empty($aaa[allday]))?$aaa[allday]:"<input type='checkbox' id='cb_all' $disabled name='s[$did][section][]' value='allday' onClick=\"if (this.checked==false){javascript:ableall_$e_name() } else { javascript:disableall_$e_name()}\">";
			
			$all_day=(!empty($aaa[allday]))?"":"<td bgcolor='#E8F9C8'>$allday</td>";
			
			
			
			$tool="缺曠課種類";
			if ($j==0)
				$data.="
				<tr bgcolor='#FFFFFF'>
				<td rowspan='5'>$One</td>
				<td rowspan='5' align='center'>$seme_num</td>
				<td rowspan='5'>$stud_name</td>";
			else
				$data.="<tr bgcolor='#FFFFFF'>";
			$data.="
			<td align='center'><a href='$_SERVER[SCRIPT_NAME]?class_id=$class_id&this_date=$did'>".$did."<br>(".$weekN[$dd[wday]-1].")</a></td>	
			$uf
			$sections_data
			$df		
			$all_day
			<td bgcolor='#ECff8F9'>
			$select
			<a href='$_SERVER[SCRIPT_NAME]?act=clear&class_id=$class_id&stud_id=$One&this_date=$did&One=$One'><img src='images/del.png' border='0' alt='刪除這一天($did)所有記錄'></a></td>
			</tr>";
		}
	}
	$submitTxt=(sizeof($thisOne)>0 or $One!="")?"儲存登記":"勾選編輯";
	
	$main="
	<table cellspacing='0' cellpadding='0'0class='small'>
	<tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2' class='small'>
		<tr bgcolor='#E6F2FF'>
		<td align='center'>學號</td>
		<td align='center'>座號</td>
		<td align='center'>姓名</td>
		$date_title
		<td bgcolor='#FBF8B9'>升旗</td>
		$sections_txt
		<td bgcolor='#FFE6D9'>降旗</td>
		<td bgcolor='#E8F9C8'>整天</td>
		<td bgcolor='#ECff8F9'>$tool</td>
		</tr>
		<form action='$_SERVER[SCRIPT_NAME]' method='post' name='myform'>
		$data	
		</table>
	</td><td valign='top'>
		<input type='hidden' name='sel_year' value='$sel_year'>
		<input type='hidden' name='sel_seme' value='$sel_seme'>
		<input type='hidden' name='class_id' value='$class_id'>
		<input type='hidden' name='this_date' value='$year-$month-$day'>
		<input type='hidden' name='date' value='$year-$month-$day'>
		<input type='submit' name='act' value='$submitTxt'>";
	if (!empty($One)) $main.="<input type='hidden' name='One' value='$One'>";
	$main.="
		</form>
	</td></tr>
	</table>
	";
	return $main;
}


//新增資料
function add_all($sel_year,$sel_seme,$class_id="",$date="",$data=array()){
/*
s[091005][uf]
s[091005][section]
s[091005][df]
s[091005][allday]
s[091005][kind]
s[091005][date]
*/
	foreach($data as $id =>$v){
		foreach($v[section] as $section){
			if(empty($v['kind']))continue;
			add($sel_year,$sel_seme,$id,$class_id,$date,$section,$v['kind']);
		}
	}
	return;
}

//新增一人資料
function add_one($sel_year,$sel_seme,$class_id="",$stud_id="",$data=array()){
	foreach($data as $id =>$v){
		foreach($v[section] as $section){
			if(empty($v['kind']))continue;
			add($sel_year,$sel_seme,$stud_id,$class_id,$id,$section,$v['kind']);
		}
	}
	return;
}

//新增單一筆資料
function add($sel_year,$sel_seme,$stud_id,$class_id="",$date,$section,$kind){
	global $CONN;
	$sql_insert = "insert into stud_absent (year,semester,class_id,stud_id,date,absent_kind,section,sign_man_sn,sign_man_name,sign_time) values ('$sel_year','$sel_seme','$class_id','$stud_id','$date','$kind','$section','$_SESSION[session_tea_sn]','$_SESSION[session_tea_name]',now())";
	$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
	return;
}

//刪除某人某日的資料
function clear_data($this_date,$stud_id){
	global $CONN;
	$sql_delete = "delete from stud_absent where stud_id='$stud_id' and date='$this_date'";
	$CONN->Execute($sql_delete) or user_error("刪除失敗！<br>$sql_delete",256);
	return true;
}

?>
