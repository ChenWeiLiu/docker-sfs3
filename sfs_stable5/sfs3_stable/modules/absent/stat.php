<?php

// $Id: stat.php 7726 2013-10-28 08:15:30Z smallduh $

/* 取得設定檔 */
include_once "config.php";

sfs_check();
$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];

//判斷計算迄那一日
if(!empty($_REQUEST[this_date])){
	$d=explode("-",$_REQUEST[this_date]);
}else{
	$d=explode("-",date("Y-m-d"));
}
$tyear=$_REQUEST[year];
if (!empty($tyear) && $tyear<1911) $tyear+=1911;
$tmonth=$_REQUEST[month];
if (!empty($tmonth) && (intval($tmonth)<1 || intval($tmonth)>12)) $tmonth="";
$tday=$_REQUEST[day];
if (!empty($tday) && (intval($tday)<1 || intval($tday)>31)) $tday="";
$year=(empty($tyear))?$d[0]:$tyear;
$month=(empty($tmonth))?$d[1]:$tmonth;
$day=(empty($tday))?$d[2]:$tday;
$this_date=sprintf("%04d-%02d-%02d",$year,$month,$day);

//判斷計算由那一日起
//該日期學年
$the_year=curr_year($this_date,"-");
//該日期學期
$the_seme=curr_seme($this_date,"-");
if (empty($_REQUEST[start_date])) {
	//找出該學期的開學日
	$smday=curr_year_seme_day($the_year,$the_seme);
	$d=explode("-",$smday[st_start]);
} else {
	$d=explode("-",$_REQUEST[start_date]);
}
$tyear=$_REQUEST[start_year];
if (!empty($tyear) && $tyear<1911) $tyear+=1911;
$tmonth=$_REQUEST[start_month];
if (!empty($tmonth) && (intval($tmonth)<1 || intval($tmonth)>12)) $tmonth="";
$tday=$_REQUEST[start_day];
if (!empty($tday) && (intval($tday)<1 || intval($tday)>31)) $tday="";
$start_year=(empty($tyear))?$d[0]:$tyear;
$start_month=(empty($tmonth))?$d[1]:$tmonth;
$start_day=(empty($tday))?$d[2]:$tday;
$start_date=sprintf("%04d-%02d-%02d",$start_year,$start_month,$start_day);

$act=$_REQUEST[act];
$stud_id=$_REQUEST[stud_id];
$class_id=$_REQUEST[class_id];
$year_name=$_POST[year_name];
$class_name=$_POST[class_name];
$class_num=$_POST[class_num];
if ($year_name && $class_name && $class_num) {
	$class_num=($year_name+$IS_JHORES).sprintf("%02d%02d",$class_name,$class_num);
	$sql="select stud_id from stud_base where curr_class_num='$class_num' and stud_study_cond='0'";
	$rs=$CONN->Execute($sql);
	$sid=$rs->fields[stud_id];
	$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$year_name+$IS_JHORES,$class_name);
	if (!empty($sid)) $stud_id=$sid;
}
if ($stud_id) {
	$sql="select student_sn from stud_base where stud_id='$stud_id'";
	$rs=$CONN->Execute($sql);
	$student_sn=$rs->fields[student_sn];
	if (!$student_sn) $stud_id="";
}
//執行動作判斷
if(count($_POST[chg_date])>0) {
	if($_POST[kind]) {
		reset($_POST[chg_date]);
		while(list($k,$v)=each($_POST[chg_date])) {
			$query="update stud_absent set absent_kind='$_POST[kind]' where date='$k' and stud_id='$_POST[stud_id]'";
			$CONN->Execute($query) or die($query);
		}
		sum_abs($sel_year,$sel_seme,$_POST[stud_id]);
	}
}
if($act=="儲存登記"){
	add_all($sel_year,$sel_seme,$class_id,$_POST[date],$_POST[s]);
	header("location: $_SERVER[PHP_SELF]?this_date=$_POST[date]&class_id=$class_id");
}elseif($act=="view_one"||$act=="print"){
	$main=&mainForm($sel_year,$sel_seme,$class_id,$stud_id);
}else{
	$main=&mainForm($sel_year,$sel_seme,$class_id);
}


//秀出網頁
if ($act!="print") head("缺曠課明細");

echo "<style type=\"text/css\">
<!--
.calendarTr {font-size:12px; font-weight: bolder; color: #006600}
.calendarHeader {font-size:12px; font-weight: bolder; color: #cc0000}
.calendarToday {font-size:12px; background-color: #ffcc66}
.calendarTheday {font-size:12px; background-color: #ccffcc}
.calendar {font-size:11px;font-family: Arial}
.dateStyle {font-size:15px;font-family: Arial; color: #cc0066; font-weight: bolder}
-->
</style>
";
echo $main;
if ($act!="print") foot();

//主要輸入畫面
function &mainForm($sel_year,$sel_seme,$class_id="",$stud_id=""){
	global $school_menu_p,$year,$month,$day,$start_year,$start_month,$start_day,$SFS_PATH_HTML,$school_menu_p,$CONN,$IS_JHORES,$act;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	
	if ($stud_id) {
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$sql="select seme_class,seme_num from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$stud_id'";
		$rs=$CONN->Execute($sql);
		$seme_class=$rs->fields['seme_class'];
		$class_num=intval($rs->fields['seme_num']);
		$year_name=intval(substr($seme_class,0,-2));
		$class_name=intval(substr($seme_class,-2,2));
		$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$year_name,$class_name);
		$year_name-=$IS_JHORES;
	}

	//取得該班及學生名單，以及填寫表格
	if(!empty($class_id) and !empty($stud_id)){
		$signForm=&stud_statForm($sel_year,$sel_seme,"$year-$month-$day",$class_id,$stud_id);
	}elseif(!empty($class_id)){
		$signForm=&statForm($sel_year,$sel_seme,$class_id);
	}

	//年級與班級選單
	$class_select=&classSelect($sel_year,$sel_seme,"","class_id",$class_id);

	//日曆的連結字串
	$linkStr=(!empty($class_id) and !empty($stud_id))?"&start_date=$start_year-$start_month-$start_day&act=view_one&class_id=$class_id&stud_id=$stud_id":"&start_date=$start_year-$start_month-$start_day&class_id=$class_id";

	if(!empty($class_id)){
		$cal = new MyCalendar;
		$cal->linkStr=$linkStr;
		$cal->setStartDay(1);
		$cal->getDateLink();
		$mc=$cal->getMonthView($month,$year,$day);
		$the_cal="
		<table cellspacing='1' cellpadding='2' bgcolor='#E2ECFC' class='small'>
		<tr bgcolor='#FEFBDA'>
		<td align='center'>		
		<a href='$_SERVER[PHP_SELF]?act=$_REQUEST[act]&this_day=$today&class_id=$class_id' class='box'><img src='".$SFS_PATH_HTML."images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
		</td></tr>
		<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
		</table>
		";
	}
	
	if(!empty($class_id) and !empty($stud_id)){
		$c=class_id_2_old($class_id);
			
		//學生下拉選單
	    $sel1 = new drop_select(); //選單類別
	    $sel1->s_name = "stud_id"; //選單名稱
		$sel1->id = $stud_id;
		$sel1->is_submit=true;
		$sel1->arr = get_stud_array($c[0],$c[1],$c[3],$c[4],"id","name"); //內容陣列
		$stu_sel=$sel1->get_select();
	}else{
		$stu_sel="";
	}
	
	$main=($act!="print")?"
	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<form action='$_SERVER[PHP_SELF]' method='post'>
	<tr bgcolor='#FFFFFF'><td>
	$class_select $stu_sel
	統計時間：由<input type='text' maxsize='4' size='4' name='start_year' value='$start_year'>年<input type='text' maxsize='2' size='2' name='start_month' value='$start_month'>月<input type='text' maxsize='2' size='2' name='start_day' value='$start_day'>日至<input type='text' maxsize='4' size='4' name='year' value='$year'>年<input type='text' maxsize='2' size='2' name='month' value='$month'>月<input type='text' maxsize='2' size='2' name='day' value='$day'>日止<input type='submit' name='change_date' value='更換日期'>
	<input type='hidden' name='act' value='view_one'>
	<input type='hidden' name='this_date' value='$year-$month-$day'>
	</td></tr></form>
	<form action='$_SERVER[PHP_SELF]' method='post'>
	<tr bgcolor='#ffffff'>
	<td>或直接輸入學號：<input type='text' size='10' maxsize='10' name='stud_id' value='$stud_id'>
	<input type='hidden' name='act' value='view_one'>
	<input type='hidden' name='start_date' value='$start_year-$start_month-$start_day'>
	<input type='hidden' name='this_date' value='$year-$month-$day'>
	</tr>
	</form>
	<form action='$_SERVER[PHP_SELF]' method='post'>
	<tr bgcolor='#ffffff'>
	<td>或直接輸入班級座號：<input type='text' size='2' maxsize='2' name='year_name' value='$year_name'> 年級 <input type='text' size='2' maxsize='2' name='class_name' value='$class_name'> 班 <input type='text' size='2' maxsize='2' name='class_num' value='$class_num'> 號 <input type='submit' value='確定'>
	<input type='hidden' name='act' value='view_one'>
	<input type='hidden' name='start_date' value='$start_year-$start_month-$start_day'>
	<input type='hidden' name='this_date' value='$year-$month-$day'>
	</tr>
	</form>
	</table>
	<table cellspacing='1' cellpadding='3'>
	<tr>
	<td valign='top'>$signForm</td>
	<td valign='top'>$the_cal</td>
	</tr>
	</table>
	":"
	<center><table cellspacing='1' cellpadding='3' align='center'>
	<tr>
	<td valign='top'>$signForm</td>
	</tr>
	</table>
	</center>
	";
	return $main;
}


//取得該班及學生名單，以及填寫表格
function &statForm($sel_year,$sel_seme,$class_id){
	global $year,$month,$day,$start_date,$CONN;
	//取得某班學生陣列
	$c=class_id_2_old($class_id);
	
	$all_sections=get_class_cn($class_id);
	
	for($i=1;$i<=$all_sections;$i++){
		$sections_txt.="<td>第 $i 節</td>";
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
	
	foreach($stud as $id=>$name){

		//取得該學生資料
		$aaa=getOneMdata($id,$sel_year,$sel_seme,"$year-$month-$day");
		
		//各一節資料
		$blank="";
		for($i=1;$i<=$all_sections;$i++){
			$blank.="<td>$aaa[$i]</td>";
		}	
		
		
		//觀看模式
		$sections_data=$blank;

		$uf="<td bgcolor='#FBF8B9'>$aaa[uf]</td>";
		$df="<td bgcolor='#FFE6D9'>$aaa[df]</td>";
		$all_day="<td bgcolor='#E8F9C8'>$aaa[allday]</td>";
		
		//每一列資料
		//整天沒來
		$data.="
		<tr bgcolor='#FFFFFF' align='center'>
		<td>$id</td>
		<td>".$num[$id]."</td>
		<td><a href='$_SERVER[PHP_SELF]?act=view_one&class_id=$class_id&stud_id=$id&this_date=$year-$month-$day&start_date=$start_date'>$name</a></td>		
		$uf
		$sections_data
		$df
		$all_day
		</tr>";
	}
	
	$main="
	<table cellspacing='0' cellpadding='0' class='small' >
	<tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#000000' class='small'>
		<tr bgcolor='#E6F2FF'>
		<td>學號</td>
		<td>座號</td>
		<td>姓名</td>		
		<td bgcolor='#FBF8B9'>升旗</td>
		$sections_txt
		<td bgcolor='#FFE6D9'>降旗</td>
		<td bgcolor='#E8F9C8'>整天</td>
		</tr>
		<form action='$_SERVER[PHP_SELF]' method='post' name='myform'>
		$data	
		</table>
	</td><td valign='top'>

		</form>
	</td></tr>
	</table>
	";
	return $main;
}

//單一學生的缺況課明細
function &stud_statForm($sel_year,$sel_seme,$this_date,$class_id,$stud_id){
	global $CONN,$start_date,$this_date,$act,$IS_JHORES,$class_name_kind_1,$_POST;
	//取得某班節數
	$all_sections=get_class_cn($class_id);
	
	for($i=1;$i<=$all_sections;$i++){
		if ($act=="print")
			$sections_txt.="<td>".$i."</td>";
		else
			$sections_txt.="<td>第 $i 節</td>";
	}

	$sql="select date,absent_kind,section from stud_absent where (date>='$start_date') and (date<='$this_date') and stud_id='$stud_id' order by date,section";
	$rs=$CONN->Execute($sql);
	$aaa="";
	$data="";
	$total=array();
	$lis=0;
	while(!$rs->EOF){
		$the_date=$rs->fields['date'];
		$absent_kind=$rs->fields['absent_kind'];
		$section=$rs->fields['section'];
		if ($the_date != $pre_date) {
			if ($have_data) {
				$data.=show_data($pre_date,$aaa,$all_sections);
				$aaa="";
			}
			$pre_date=$the_date;
			$have_data=1;
			if ($lis!=0 && ($lis%5)==0 && $act=="print") $data.="<tr><td colspan=".($all_sections+11)." align='center'><hr size='1'></tr>";
			$lis++;
		}
		$aaa[$section]=$absent_kind;
		$total[$absent_kind][$section]++;
		$total[sum][$section]++;
		$rs->MoveNext();
	}
	$data.=show_data($the_date,$aaa,$all_sections);

	//取得缺曠課類別
	$absent_kind_array= SFS_TEXT("缺曠課類別");
	$sum_data="";
	for ($i=0;$i<count($absent_kind_array);$i++) {
		$section_data="";
		$kind=$absent_kind_array[$i];
		for($j=1;$j<=$all_sections;$j++){
			$k=($IS_JHORES!=0)?$total[$kind][$j]+$total[$kind][allday]:$total[$kind][$j];
			if ($k==0) $k="";
			$section_data.="<td bgcolor='#FFFFFF'>".$k."</td>";
			$ttotal[$kind]+=$total[$kind][$j];
		}
		$ttotal[$kind]+=($IS_JHORES==0)?$total[$kind][allday]:$total[$kind][allday]*$all_sections;
		$sum_data.=($act!="print")?"
			<tr bgcolor='#E6F2FF' align='center'>
			<td>$kind</td>
			<td bgcolor='#FBF8B9'>-</td>
			$section_data
			<td bgcolor='#FFE6D9'>-</td>
			<td bgcolor='#E8F9C8'><font color='#FF0000'>".$total[$kind][allday]."</font></td>
			<td bgcolor='#FEFED0'>".$ttotal[$kind]."</td>
			</tr>":"
			<td>".$ttotal[$kind]."</td>";
	}
	if ($IS_JHORES!=0) {
		$section_data="";
		for($j=1;$j<=$all_sections;$j++){
			$section_data.="<td bgcolor='#FFFFFF'></td>";
		}
		$ufs=$total[曠課][uf]+$total[曠課][allday];
		$dfs=$total[曠課][df]+$total[曠課][allday];
		$sum_data=($act!="print")?"<tr bgcolor='#E6F2FF' align='center'><td>集會</td><td bgcolor='#FBF8B9'>".$ufs."</td>$section_data<td bgcolor='#FFE6D9'>".$dfs."</td><td bgcolor='#E8F9C8'></td><td bgcolor='#FEFED0'>".($ufs+$dfs)."</td></tr>".$sum_data:
		"$sum_data<td>".($ufs+$dfs)."</td>";
	}
	if ($act=="print") {
		$query="select * from school_base";
		$res=$CONN->Execute($query);
		$school_name=$res->fields[sch_cname];
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$query="select a.stud_name,b.seme_num from stud_base a,stud_seme b where b.seme_year_seme='$seme_year_seme' and a.student_sn=b.student_sn and a.stud_id='$stud_id'";
		$res=$CONN->Execute($query);
		$stud_name=$res->fields[stud_name];
		$seme_num=$res->fields[seme_num];
		$query="select * from school_class where class_id='$class_id'";
		$res=$CONN->Execute($query);
		$c_name=$res->fields[c_name];
		$c_year=$res->fields[c_year];
		$today=date("Y-m-d");
	}
	//取得缺曠課類別
	$absent_kind_array= SFS_TEXT("缺曠課類別");
	$option="<select name='kind'><option value=''></option>\n";
	foreach($absent_kind_array as $k){
		$option.="<option value='$k'>$k</option>\n";
	}
	$option.="</select><input type='submit' value='修改'>";
	$main=($act!="print")?"
	<form action='$_SERVER[PHP_SELF]' method='post'><small><input type=\"button\" value=\"友善列印\" OnClick=\"this.form.act.value='print';this.form.submit();\"><input type='hidden' name='class_id' value='$class_id'>　將選取假別修改成：$option</small>
	<table cellspacing='1' cellpadding='3' bgcolor='#000000' class='small'>
	<tr bgcolor='#E6F2FF'>
	<td align='center'>日期＼節次</td>		
	<td bgcolor='#FBF8B9'>升旗</td>
	$sections_txt
	<td bgcolor='#FFE6D9'>降旗</td>
	<td bgcolor='#E8F9C8'>整天</td>
	<td bgcolor='#FEFED0'>選取</td>
	</tr>
	$data
	<tr bgcolor='#E6F2FF'>
	<td bgcolor='#cccccc' colspan=".($all_sections+5)." align='center'>
	合計
	</tr>
	$sum_data
	<tr bgcolor='#E6F2FF'>
	<td align='center'>假別／節次</td>		
	<td bgcolor='#FBF8B9'>升旗</td>
	$sections_txt
	<td bgcolor='#FFE6D9'>降旗</td>
	<td bgcolor='#E8F9C8'>整天</td>
	<td bgcolor='#FEFED0'>總計</td>
	</tr>
	<input type='hidden' name='stud_id' value='$stud_id'>
	<input type='hidden' name='act' value='view_one'>
	<input type='hidden' name='start_date' value='$start_date'>
	<input type='hidden' name='this_date' value='$this_date'>
	</form></table>":"
	<center><h3>$school_name $sel_year 學年度　第 $sel_seme 學期　學生個人勤惰明細表</h3>
	".$class_name_kind_1[intval($c_year)-$IS_JHORES]."年".$c_name."班　座號：".$seme_num."　學號：".$stud_id."　姓名：".$stud_name." 　　　<small>列印日期：$today</small><br><br><small>統計時間：".$_POST['start_date']."～".$_POST['this_date']."</small><br><br>
	<table cellspacing='1' cellpadding='3' class='small'>
	<tr align='center'>
	<td>缺席日期</td>		
	<td>星期</td>		
	<td>升</td>
	$sections_txt
	<td>降</td><td>曠</td><td>事</td><td>病</td><td>喪</td><td>公</td><td>不</td><td>旗</td>
	</tr>
	<tr>
	<td colspan=".($all_sections+11)." align='center'><hr size='2'></td>
	</tr>
	$data
	<tr>
	<td colspan=".($all_sections+11)." align='center'><hr size='2'></td>
	</tr>
	<tr align='center'>
	<td>缺席日期</td>		
	<td>星期</td>		
	<td>升</td>
	$sections_txt
	<td>降</td><td>曠</td><td>事</td><td>病</td><td>喪</td><td>公</td><td>不</td><td>旗</td>
	</tr>
	<tr align='center'>
	<td>累計</td><td colspan=".($all_sections+3)."></td>$sum_data
	</tr>
	</table></center>
	";
	if ($IS_JHORES!=0&&$act!="print") $main.=help("集會只統計曠課的升降旗");
	return $main;
}

function show_data($the_date,$a,$all_sections) {
	global $IS_JHORES,$class_name_kind_1,$act;
	//各一節資料
	$w=explode("-",$the_date);
	$ww=date("w", mktime (0,0,0,$w[1],$w[2],$w[0]));
	$section_data="";
	$k="";
	$ak=array("曠課"=>0,"事假"=>0,"病假"=>0,"喪假"=>0,"公假"=>0,"不可抗力"=>0,"旗"=>0);
	if ($IS_JHORES!=0 && !empty($a[allday])) {
		$k=$a[allday];
		$a[uf]=$k;
		$a[df]=$k;
	}
	for($j=1;$j<=$all_sections;$j++){
		if ($k) $a[$j]=$k;
		if ($act=="print") {
			$section_data.="<td>".substr($a[$j],0,2)."</td>";
			if ($a[$j]) $ak[$a[$j]]++;
		} else
			$section_data.="<td bgcolor='#FFFFFF'>$a[$j]</td>";
	}
	$data=($act!="print")?"
		<tr bgcolor='#E6F2FF' align='center'>
		<td>$the_date(".$class_name_kind_1[$ww].")</td>
		<td bgcolor='#FBF8B9'>$a[uf]</td>
		$section_data
		<td bgcolor='#FFE6D9'>$a[df]</td>
		<td bgcolor='#E8F9C8'><font color='#FF0000'>$a[allday]</font></td>
		<td bgcolor='#FEFED0' align='center'><input type='checkbox' name='chg_date[".$the_date."]'></td>
		</tr>":"
		<tr align='center'>
		<td>$the_date</td>
		<td>".$class_name_kind_1[$ww]."
		<td>".substr($a[uf],0,2)."</td>
		$section_data
		<td>".substr($a[df],0,2)."</td>
		";
	if ($act=="print") {
		if ($a[uf]=="曠課") $ak["旗"]++;
		if ($a[df]=="曠課") $ak["旗"]++;
		while (list($x,$y)=each($ak)) {
			$data.="<td>".intval($y)."</td>";
		}
		$data.="</tr>";
	}
	return $data;
}
?>
