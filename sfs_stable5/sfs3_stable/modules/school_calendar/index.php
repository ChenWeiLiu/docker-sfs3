<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include "config.php";

$month=($_GET[month])?$_GET[month]:$_POST[month];
$year=($_GET[year])?$_GET[year]:$_POST[year];
$day=($_GET[day])?$_GET[day]:$_POST[day];

if(!empty($_GET[this_date]) or !empty($_POST[this_date])){
	$this_date=($_GET[this_date])?$_GET[this_date]:$_POST[this_date];
	$d=explode("-",$_GET[this_date]);
	$year=$d[0];
	$month=$d[1];
	$day=$d[2];
}

$act=($_GET[act])?$_GET[act]:$_POST[act];

//執行動作判斷
if($act=="getYearView"){
	$main=&getYearView($year);
}elseif($act=="getMonthThingView"){
	$main=&viewAll($year,$month,$day,"viewThing");
}elseif($act=="getMonthThingListView"){
	$main=&viewAll($year,$month,$day,"viewMonthThing");
}else{
	$main=&viewAll($year,$month,$day);
}


//秀出網頁
head("校務行事曆");
?>
<style type="text/css">
<!--
.calendarTr {font-size:12px; font-weight: bolder; color: #006600}
.calendarHeader {font-size:12px; font-weight: bolder; color: #cc0000}
.calendarToday {font-size:12px; background-color: #ffcc66}
.calendarTheday {font-size:12px; background-color: #ccffcc}
.calendar {font-size:11px;font-family: Arial, Helvetica, sans-serif;}
.dateStyle {font-size:15px;font-family: Arial; color: #cc0066; font-weight: bolder}
-->
</style>
<?php
echo $main;
foot();

function &viewAll($year="",$month="",$day="",$mode="",$cal_sn=""){
	global $school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	//假如沒有日期，指定今天
	
	if(empty($year))$year=date("Y");
	if(empty($month))$month=date("m");
	if(empty($day))$day=date("d");
	
	//行事曆
	$cal=&getMonthView($year,$month,$day,$mode);
	
	//事件列表
	if($mode=="add"){
		$thing=&addThingForm($year,$month,$day);
	}elseif($mode=="modify"){
		$thing=&addThingForm($year,$month,$day,$cal_sn);
	}elseif($mode=="viewThing"){
		$thing="";
	}elseif($mode=="viewMonthThing"){
		$thing=&getMonththing($year,$month,$day);
	}else{
		$thing=&getthing($year,$month,$day);
	}
	
	$main="
	$tool_bar
	<table width='96%' cellspacing='0' cellpadding='0' bgcolor='#C0C0C0'><tr bgcolor='#FFFFFF'>
	<td valign='top'>$thing</td>
	<td width='5'></td>
	<td valign='top'>$cal</td>
	</tr></table>";
	return $main;
}


//取得月行事曆
function &getMonthView($year="",$month="",$day="",$mode=""){
	global $today,$act;
	$cal = new MyCalendar;
	$cal->setStartDay(1);
	$mc=($mode=="viewThing")?$cal->getMonthThingView($month,$year,$day):$cal->getMonthView($month,$year,$day);

	if($act!="addThingForm")$act="";
	
	$main="
	<table cellspacing='1' cellpadding='2' bgcolor='#E2ECFC' class='small'>
	<tr bgcolor='#FEFBDA'><td align='center'>
	<a href='$_SERVER[PHP_SELF]?act=getYearView' class='box'><img src='images/month.png' alt='全年日曆' width='16' height='16' hspace='2' border='0' align='absmiddle'>全年日曆</a>
	
	<a href='$_SERVER[PHP_SELF]?act=$act&this_day=$today' class='box'><img src='images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
	</td></tr>
	<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
	</table>
	";
	return $main;
}

//當月事件總覽
function &getMonththing($year,$month,$day){
	global $CONN,$import_array,$kind_array,$restart_array,$week_array,$today,$kind_color_array,$import_color_array,$mNames;
	
	$mounth_num=date("t",mktime(0,0,0,$month,$day,$year));
	$data="";
	for($i=1;$i<=$mounth_num;$i++){
		$data.=getOneDayThing($year,$month,$i,"show_date");
	}
	
	$m=$month*1;
	$cmName=$mNames[$m];

	$main="	
	<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#DFE8F2' class='small'>
	<tr bgcolor='#FEFBDA'>
	<td colspan='10'>
	<font class='dateStyle'>$year</font>
	年
	<font class='dateStyle'>$month</font>
	月
	<font class='dateStyle'>$day</font>（星期".$week_array[$w]."）的行事曆：
	<a href='$_SERVER[PHP_SELF]?act=$act&this_day=$today' class='box'><img src='images/today.png' alt='回行事曆' width='16' height='16' hspace='2' border='0' align='absmiddle'>回行事曆</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingListView&this_date=$this_date' class='box'>
	<img src='images/list.png' alt='".$cmName."事件總覽' width='16' height='16'  hspace='2' border='0' align='absmiddle'>".$cmName."事件總覽</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingView&this_date=$this_date' class='box'>
	<img src='images/1day.png' alt='".$cmName."行事曆' width='16' height='16'  hspace='2' border='0' align='absmiddle'>".$cmName."行事曆</a>
	</td>
	</tr>
	<tr bgcolor='#EAECEE'>
	<td nowrap>日期</td><td nowrap>星期</td><td nowrap>時間</td><td nowrap>地點</td><td>事件</td><td nowrap>處室</td><td nowrap>紀錄者</td><td nowrap>循環</td><td nowrap>重要性</td>
	</tr>
	$data
	</table>";
	return $main;
}

//取得某日事件，只有<tr></tr>，沒有<table></table>
function getOneDayThing($year,$month,$day,$mode=""){
	global $CONN,$import_array,$kind_array,$restart_array,$week_array,$today,$kind_color_array,$import_color_array,$MODULE_TABLE_NAME;

	//處室代碼
	$office=room_kind();
	//星期
	$w=date ("w", mktime(0,0,0,$month,$day,$year));
	
	$this_date="$year-$month-$day";
	$this_date_tt=mktime (0,0,0,$month,$day,$year);
	
	$sql_select = "
	select * from calendar
	where 
	(
		(year='$year' and month='$month' and day='$day') or
		(
			(restart='md' and month='$month' and day='$day') or 
			(restart='d' and day='$day') or 
			(restart='w' and week='$w')
		) 
	)		
	and kind='0'
	order by time";
	$recordSet = $CONN->Execute($sql_select) or user_error("$sql_select",256);
	while ($c=$recordSet->FetchRow()) {
		
		$te=get_teacher_post_data($c[teacher_sn]);
		$teacher_post=$te[post_office];
		
		if($c[restart_day]!="0000-00-00"){
			$rd=explode("-",$c[restart_day]);
			if($this_date_tt < mktime (0,0,0,$rd[1],$rd[2],$rd[0])) {
				continue;
			}
		}
		if($c[restart_end]!="0000-00-00"){
			$re=explode("-",$c[restart_end]);
			if($this_date_tt > mktime (0,0,0,$re[1],$re[2],$re[0])) {
				continue;
			}
		}
		
		$name=get_teacher_name($c[from_teacher_sn]);
		$kind=$c[kind];
		$import=$c[import];
		$thing=nl2br($c[thing]);
		$time=substr($c[time],0,5);
		
		if($c[restart]=="w"){
			$restart_txt="每星期".$week_array[$w]."";
		}elseif($c[restart]=="d"){
			$restart_txt="每月的".$day."日";
		}elseif($c[restart]=="md"){
			$restart_txt="每年的".$month."月".$day."日";
		}else{
			$restart_txt="";
		}
		
		$show_date=($mode=="show_date")?"<td>$this_date</td><td>$week_array[$w]</td>":"";
		
		$data.="
		<tr bgcolor='#FFFFFF'>
		$show_date
		<td nowrap>$time</td>
		<td nowrap>$c[place]</td>
		<td>$thing</td>
		<td bgcolor='$kind_color_array[$kind]' nowrap>$office[$teacher_post]</td>
		<td nowrap>$name</td>
		<td nowrap>$restart_txt</td>
		<td nowrap><font color='$import_color_array[$import]'>$import_array[$import]</font></td>
		</tr>
		";
	}
	
	if(empty($data))$data=($mode=="show_date")?"<tr bgcolor='#FFFFFF'><td>$this_date</td><td>$week_array[$w]</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>":"<tr bgcolor='#FFFFFF'><td colspan='8' align='center' nowrap>今日無大事！</td></tr>";
	
	return $data;
}

//取得某日事件
function &getthing($year,$month,$day){
	global $CONN,$import_array,$kind_array,$restart_array,$week_array,$today,$kind_color_array,$import_color_array,$mNames;
	
	$w=date ("w", mktime(0,0,0,$month,$day,$year));
	$this_date="$year-$month-$day";
	$data=getOneDayThing($year,$month,$day);
	
	$m=$month*1;
	$cmName=$mNames[$m];

	$main="	
	<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#DFE8F2' class='small'>
	<tr bgcolor='#FEFBDA'>
	<td colspan='8'>
	<font class='dateStyle'>$year</font>
	年
	<font class='dateStyle'>$month</font>
	月
	<font class='dateStyle'>$day</font>（星期".$week_array[$w]."）的行事曆：
	<a href='$_SERVER[PHP_SELF]?act=$act&this_day=$today' class='box'><img src='images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingListView&this_date=$this_date' class='box'>
	<img src='images/list.png' alt='".$cmName."事件總覽' width='16' height='16'  hspace='2' border='0' align='absmiddle'>".$cmName."事件總覽</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingView&this_date=$this_date' class='box'>
	<img src='images/1day.png' alt='".$cmName."行事曆' width='16' height='16'  hspace='2' border='0' align='absmiddle'>".$cmName."行事曆</a>
	</td>
	</tr>
	<tr bgcolor='#EAECEE'>
	<td nowrap>時間</td><td nowrap>地點</td><td>事件</td><td nowrap>種類</td><td nowrap>紀錄者</td><td nowrap>循環</td><td nowrap>重要性</td>
	</tr>
	$data
	</table>";
	return $main;
}

//取得某日簡易事件
function getSimpleThing($year,$month,$day){
	global $CONN,$import_array,$kind_array,$restart_array,$week_array,$today,$MODULE_TABLE_NAME;
	
	//星期
	$w=date ("w", mktime(0,0,0,$month,$day,$year));
	
	$this_date="$year-$month-$day";
	$this_date_tt=mktime (0,0,0,$month,$day,$year);
	
	$sql_select = "
	select * from calendar
	where 
	(
		(year='$year' and month='$month' and day='$day') or
		(
			(restart='md' and month='$month' and day='$day') or 
			(restart='d' and day='$day') or 
			(restart='w' and week='$w')
		) 
	)		
	and kind='0'
	order by time";
	$recordSet = $CONN->Execute($sql_select) or user_error("$sql_select",256);
	while ($c=$recordSet->FetchRow()) {
		if($c[restart_day]!="0000-00-00"){
			$rd=explode("-",$c[restart_day]);
			if($this_date_tt < mktime (0,0,0,$rd[1],$rd[2],$rd[0])) {
				continue;
			}
		}
		if($c[restart_end]!="0000-00-00"){
			$re=explode("-",$c[restart_end]);
			if($this_date_tt > mktime (0,0,0,$re[1],$re[2],$re[0])) {
				continue;
			}
		}
		
		$name=get_teacher_name($c[from_teacher_sn]);
		
		$dot=(strlen($c[thing])>12)?"...":"";
		$thing=substr(nl2br($c[thing]),0,12).$dot;
		
		$time=substr($c[time],0,5);
		
		$data.="<li>$thing</li>
		";
	}
	
	return $data;
}

//取得一個事件的資料
function get_one_cal($cal_sn){
	global $CONN,$MODULE_TABLE_NAME;
	$sql_select = "select * from calendar where cal_sn=$cal_sn";
	$recordSet = $CONN->Execute($sql_select) or user_error("$sql_select",256);
	$c=$recordSet->FetchRow();
	return $c;
}


//年度行事曆
function &getYearView($year=""){
	global $school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	$d = getdate(time());
	
	if ($year == ""){
	    $year = $d["year"];
	}
	
	$cal = new MyCalendar;
	//$cal->setStartMonth(4);
	$yearCal=$cal->getYearView($year);
	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='4' bgcolor='#DFE8F2' class='small'>
	<tr bgcolor='#FEFBDA'><td><a href='$_SERVER[PHP_SELF]?act=$act&this_day=$today' class='box'><img src='images/1day.png' alt='回行事曆' width='16' height='16' hspace='2' border='0' align='absmiddle'>回行事曆</a></td></tr>
	<tr bgcolor='#FFFFFF'><td>$yearCal</td>
	</tr></table>
	";
	return $main;
}


?>
