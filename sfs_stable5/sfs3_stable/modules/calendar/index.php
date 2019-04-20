<?php

// $Id: index.php 7971 2014-04-01 06:50:58Z smallduh $

/* 取得設定檔 */
include "config.php";

sfs_check();

//檢查是否有管理權, 有管理權才能發佈校務行事曆
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

//2014.04.01 修正, 當啟用並校務時,可一直保持啟用並校務狀態
if ($_POST['with_school_thing']==1) {
	$use_school=$_POST['use_school'];
  $_SESSION[use_school]=$use_school;
}

//$use_school=$_REQUEST['use_school'];
//$_SESSION[use_school]=($use_school=="" and ($_REQUEST['act']=="")) ?0:$_SESSION[use_school];
//$_SESSION[use_school]=($use_school=="on")?1:$_SESSION[use_school];

$month=($_GET[month])?$_GET[month]:$_POST[month];
$year=($_GET[year])?$_GET[year]:$_POST[year];
$day=($_GET[day])?$_GET[day]:$_POST[day];

if(!empty($_GET[this_date]) or !empty($_POST[this_date])){
	$this_date=($_GET[this_date])?$_GET[this_date]:$_POST[this_date];
	$d=explode("-",$this_date);
	$year=$d[0];
	$month=$d[1];
	$day=$d[2];
}

$act=($_GET[act])?$_GET[act]:$_POST[act];

//執行動作判斷
if($act=="getYearView"){
	$main=&getYearView($year);
}elseif($act=="addThingForm"){
	$main=&viewAll($year,$month,$day,"add");
}elseif($act=="存入記事"){
	addOneThing($_POST[data]);
	header("location: $_SERVER[PHP_SELF]?this_date=$_POST[this_date]");
}elseif($act=="貼上行事曆"){
	PasteSchoolThing($_POST[data]);
	header("location: $_SERVER[PHP_SELF]");
}elseif($act=="modifyThing"){
	$main=&viewAll($year,$month,$day,"modify",$_GET[cal_sn]);
}elseif($act=="儲存更新"){
	updateOneThing($_POST[data],$_POST[cal_sn]);
	header("location: $_SERVER[PHP_SELF]?this_date=$_POST[this_date]");
}elseif($act=="delThing"){
	delThing($_GET[cal_sn]);
	header("location: $_SERVER[PHP_SELF]?this_date=$_GET[this_date]");
}elseif($act=="getMonthThingView"){
	$main=&viewAll($year,$month,$day,"viewThing");
}elseif($act=="getMonthThingListView"){
	$main=&viewAll($year,$month,$day,"viewMonthThing");
}elseif($act=="PasteForm"){
	$main=&viewAll($year,$month,$day,"PasteForm");
}else{
	$main=&viewAll($year,$month,$day);
}


//秀出網頁
head("行事曆");
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
	}elseif($mode=="PasteForm"){
		$thing=&PasteForm($year,$month,$day);
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
	$state=($_SESSION[use_school]==1?"(並校務)":"");
	$main="
	<table cellspacing='1' cellpadding='2' bgcolor='#E2ECFC' class='small'>
	<tr bgcolor='#FEFBDA'><td align='center'>
	<a href='$_SERVER[PHP_SELF]?act=getYearView' class='box'><img src='images/month.png' alt='全年日曆' width='16' height='16' hspace='2' border='0' align='absmiddle'>全年日曆</a>
	
	<a href='$_SERVER[PHP_SELF]?act=$act&this_day=$today' class='box'><img src='images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
	</td></tr>
	<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
	<tr><td align='center'>$state</td></tr>
	</table>
	";
	return $main;
}

//當月事件總覽
function &getMonththing($year,$month,$day){
	global $CONN,$import_array,$kind_array,$restart_array,$week_array,$today,$kind_color_array,$import_color_array;
	global $module_manager;
	
	if ($module_manager) {
	  $paste="
	  <a href='$_SERVER[PHP_SELF]?act=PasteForm' class='box'>
	  <img src='images/appointment.png' alt='快貼校務行事曆' width='16' height='16' hspace='2' border='0' align='absmiddle'>快貼校務行事曆</a>";
	  
	}
	
	$mounth_num=date("t",mktime(0,0,0,$month,$day,$year));
	$data="";
	for($i=1;$i<=$mounth_num;$i++){
		$data.=getOneDayThing($year,$month,$i,"show_date");
	}
	$state=($_SESSION[use_school]==1?"(並校務)":"");
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
	<a href='$_SERVER[PHP_SELF]?act=addThingForm&this_date=$this_date' class='box'>
	<img src='images/appointment.png' alt='新增事件' width='16' height='16' hspace='2' border='0' align='absmiddle'>新增事件</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingListView&this_date=$this_date' class='box'>
	<img src='images/list.png' alt='當月事件總覽' width='16' height='16'  hspace='2' border='0' align='absmiddle'>當月事件總覽</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingView&this_date=$this_date' class='box'>
	<img src='images/1day.png' alt='當月行事曆' width='16' height='16'  hspace='2' border='0' align='absmiddle'>當月行事曆</a>
	$paste
	$state
	</td>
	</tr>
	<tr bgcolor='#EAECEE'>
	<td nowrap>日期</td><td nowrap>星期</td><td nowrap>時間</td><td nowrap>地點</td><td>事件</td><td nowrap>種類</td><td nowrap>紀錄者</td><td nowrap>循環</td><td nowrap>重要性</td><td nowrap>功能</td>
	</tr>
	$data
	</table>";
	return $main;
}

//取得某日事件，只有<tr></tr>，沒有<table></table>
function getOneDayThing($year,$month,$day,$mode=""){
	global $CONN,$import_array,$kind_array,$restart_array,$week_array,$today,$kind_color_array,$import_color_array,$MODULE_TABLE_NAME;
	global $coop_edit;

	//星期
	$w=date ("w", mktime(0,0,0,$month,$day,$year));
	
	$this_date="$year-$month-$day";
	$this_date_tt=mktime (0,0,0,$month,$day,$year);
	
	$sql_select = "
	select * from $MODULE_TABLE_NAME[0]
	where 
	(
		(year='$year' and month='$month' and day='$day') or
		(
			(restart='md' and month='$month' and day='$day') or 
			(restart='d' and day='$day') or 
			(restart='w' and week='$w')
		) 
	) and (teacher_sn=$_SESSION[session_tea_sn]";
	$sql_select .= ($_SESSION[use_school]==1)?" or kind=0)":")";	
	$sql_select .= " order by time";
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
		$modify_tool=(($_SESSION[session_tea_sn]==$c[from_teacher_sn]) or ($kind==0 and $coop_edit==1))?"<a href='$_SERVER[PHP_SELF]?act=modifyThing&cal_sn=$c[cal_sn]&this_date=$this_date'>修改</a>":"";	
		$del_tool=(($_SESSION[session_tea_sn]==$c[from_teacher_sn]) or ($kind==0 and $coop_edit==1))?"| <a href='$_SERVER[PHP_SELF]?act=delThing&cal_sn=$c[cal_sn]&this_date=$this_date'>刪除</a>":"";
		
		$show_date=($mode=="show_date")?"<td>$this_date</td><td>$week_array[$w]</td>":"";
		
		$data.="
		<tr bgcolor='#FFFFFF'>
		$show_date
		<td nowrap>$time</td>
		<td nowrap>$c[place]</td>
		<td>$thing</td>
		<td bgcolor='$kind_color_array[$kind]' nowrap>$kind_array[$kind]</td>
		<td nowrap>$name</td>
		<td nowrap>$restart_txt</td>
		<td nowrap><font color='$import_color_array[$import]'>$import_array[$import]</font></td>
		<td nowrap>
		$modify_tool
		$del_tool</td>
		</tr>
		";
	}
	
	if(empty($data))$data=($mode=="show_date")?"<tr bgcolor='#FFFFFF'><td>$this_date</td><td>$week_array[$w]</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>":"<tr bgcolor='#FFFFFF'><td colspan='8' align='center' nowrap>今日無大事！</td></tr>";
	
	return $data;
}

//取得某日事件
function &getthing($year,$month,$day){
	global $CONN,$import_array,$kind_array,$restart_array,$week_array,$today,$kind_color_array,$import_color_array;
	global $module_manager;
	
	if ($module_manager) {
	  $paste="
	  <a href='$_SERVER[PHP_SELF]?act=PasteForm' class='box'>
	  <img src='images/appointment.png' alt='快貼校務行事曆' width='16' height='16' hspace='2' border='0' align='absmiddle'>快貼校務行事曆</a>";
	  
	}
	$w=date ("w", mktime(0,0,0,$month,$day,$year));
	$this_date="$year-$month-$day";
	$data=getOneDayThing($year,$month,$day);
	
	$use_checked=($_SESSION[use_school]==1)?"checked":"";

	$main="
	<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">	
	<input type='hidden' name='with_school_thing' value=0>
	<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#DFE8F2' class='small'>
	<tr bgcolor='#FEFBDA'>
	<td colspan='8'>
	<font class='dateStyle'>$year</font>
	年
	<font class='dateStyle'>$month</font>
	月
	<font class='dateStyle'>$day</font>（星期".$week_array[$w]."）的行事曆：
	<a href='$_SERVER[PHP_SELF]?act=$act&this_day=$today' class='box'><img src='images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
	<a href='$_SERVER[PHP_SELF]?act=addThingForm&this_date=$this_date' class='box'>
	<img src='images/appointment.png' alt='新增事件' width='16' height='16' hspace='2' border='0' align='absmiddle'>新增事件</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingListView&this_date=$this_date' class='box'>
	<img src='images/list.png' alt='當月事件總覽' width='16' height='16'  hspace='2' border='0' align='absmiddle'>當月事件總覽</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingView&this_date=$this_date' class='box'>
	<img src='images/1day.png' alt='當月行事曆' width='16' height='16'  hspace='2' border='0' align='absmiddle'>當月行事曆</a>
	$paste
	<input type='checkbox' name='use_school' value='1' $use_checked onclick='this.form.with_school_thing.value=\"1\";this.form.submit()';>並校務
	<input name='act' type='hidden' id='act' value=''>
	<input name='this_date' type='hidden' id='this_date' value='$this_date'>
	</td>
	</tr>
	<tr bgcolor='#EAECEE'>
	<td nowrap>時間</td><td nowrap>地點</td><td>事件</td><td nowrap>種類</td><td nowrap>紀錄者</td><td nowrap>循環</td><td nowrap>重要性</td><td nowrap>功能</td>
	</tr>
	$data
	</table>
	</form>	";
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
	select * from $MODULE_TABLE_NAME[0]
	where 
	(
		(year='$year' and month='$month' and day='$day') or
		(
			(restart='md' and month='$month' and day='$day') or 
			(restart='d' and day='$day') or 
			(restart='w' and week='$w')
		) 
	) and (teacher_sn=$_SESSION[session_tea_sn]";
	$sql_select .= ($_SESSION[use_school]==1)?" or kind=0)":")";	
	$sql_select .= " order by time";
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

//新增事件表單
function &addThingForm($year="",$month="",$day="",$cal_sn=""){
	global $import_array,$kind_array,$hour_array,$restart_array,$week_array;
	global $module_manager,$manager_kind_array,$nor_kind_array;
	
	if(!empty($cal_sn)){
		$cal_data=get_one_cal($cal_sn);
	}
	
	$is_v=(!empty($cal_sn))?$cal_data[import]:2;
	$is=new drop_select;
	$is->s_name="data[import]";
	$is->arr=$import_array;
	$is->id=$is_v;
	$is->font_style = "font-size:12px";
	$import_select=$is->get_select();
	
	$ks_v=(!empty($cal_sn))?$cal_data[kind]:2;
	$ks=new drop_select;
	$ks->s_name="data[kind]";
	$ks->arr=($module_manager)?$manager_kind_array:$nor_kind_array;
	$ks->id=$ks_v;
	$ks->font_style = "font-size:12px";
	//$ks->use_val_as_key=true;
	$kind_select=$ks->get_select();
	
	$rs_v=(!empty($cal_sn))?$cal_data[restart]:"不循環";
	$rs=new drop_select;
	$rs->s_name="data[restart]";
	$rs->arr=$restart_array;
	$rs->id=$rs_v;
	$rs->font_style = "font-size:12px";
	$restartselect=$rs->get_select();
	
	if(!empty($cal_sn)){
		$t=explode(":",$cal_data[time]);
	}
	$ts_v=(!empty($cal_sn))?$t[0]:date("H");
	$hs=new drop_select;
	$hs->s_name="data[h]";
	$hs->arr=$hour_array;
	$hs->has_empty=false;
	$hs->id=$ts_v;
	$hs->font_style = "font-size:12px";
	$hour_select=$hs->get_select();
	
	
	//教師選單
	$to_tsn_arr=(!empty($cal_sn))?get_cal_to_who($cal_sn):"";
	$teacher_array=teacher_array();
	$teacher_array[all]="給全校";
	$ts=new drop_select;
	$ts->s_name="data[to_teacher_sn][]";
	$ts->arr=$teacher_array;
	$ts->unvisible_arr=array($_SESSION[session_tea_sn]);
	$ts->top_option="不公開";
	$ts->font_style = "font-size:12px";
	$ts->multiple=true;
	$ts->multiple_id=$to_tsn_arr;
	$ts->size=9;
	$teacher_select=$ts->get_select();
	
	$this_date="$year-$month-$day";
	$w=date ("w", mktime(0,0,0,$month,$day,$year));
	
	$restart_day=(!empty($cal_sn))?$cal_data[restart_day]:$this_date;
	$submit=(!empty($cal_sn))?"儲存更新":"存入記事";
	
	$main="
	
	<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#DFE8F2' class='small'>
	<tr bgcolor='#FEFBDA'><td colspan='3'>
	<font class='dateStyle'>$year</font>
	年
	<font class='dateStyle'>$month</font>
	月
	<font class='dateStyle'>$day</font>
	（星期".$week_array[$w]."）的行事曆：
	<a href='$_SERVER[PHP_SELF]?this_date=$this_date' class='box'>
	<img src='images/1day.png' alt='回行事曆列表' width='16' height='16' hspace='2' border='0' align='absmiddle'>回行事曆列表</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingView&this_date=$this_date' class='box'><img src='images/list.png' alt='當月行事曆' width='16' height='16' hspace='2' border='0' align='absmiddle'>當月行事曆</a></td></tr>
	<form action='$_SERVER[PHP_SELF]' method='post'>
	<input type='hidden' name='data[year]' value='$year'>
	<input type='hidden' name='data[month]' value='$month'>
	<input type='hidden' name='data[day]' value='$day'>
	<input type='hidden' name='data[week]' value='$w'>
	<tr bgcolor='#FFFFFF'>
	<td colspan='2' nowrap>
	時間： $hour_select<input type='text' name='data[min]' value='$t[1]' size='2' maxlength='2'> 分，
	地點：<input type='text' name='data[place]' value='$cal_data[place]'>，
	請將事件詳述於下：<br>
	<textarea cols='50' rows='5' name='data[thing]' style='width:100%' class='small'>$cal_data[thing]</textarea></td>
</tr>
	<tr bgcolor='#FFFFFF'>
	<td nowrap>種類： $kind_select
	</td>
	<td rowspan='5'>
		<table class='small'><tr><td valign='top'>
		公開給：<br>
		$teacher_select
		</td><td valign='top'>
		<ol style='line-height: 1.5;'>
		<li><font color='#8000FF'><strong>定為學校行事曆：</strong></font>在「種類」中，選「校務」會把該事件視為學校行事曆的事件。</li>
		<li><font color='#8000FF'><strong>把事件複製給其他教師：</strong></font>在「公開給：」中點選教師名稱即可。按住 ctrl 然後點選，可以不連續複選。按住 shift 然後點選下，可以連續複選。若選了「給全校」，則會複製給全校教職員。</li>
		<li><font color='#8000FF'><strong>固定重複出現的事件：</strong></font>如生日，可以選擇「每年該日」。如果是每個月的某一天，那麼選「每月該日」，或是固定每週的某一天（以星期幾作為主要依據），那麼選「每週該日」。</li>
		</ol>
		</td></tr></table>
	</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<td nowrap>
	重要性： $import_select
	</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<td nowrap>
	循環事件？
	$restartselect
	</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<td nowrap>
	循環事件起始日
	<input type='text' name='data[restart_day]' value='$restart_day' size='10'>
	</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<td nowrap>
	循環事件結束日
	<input type='text' name='data[restart_end]' value='$cal_data[restart_end]' size='10'>
	</td>
	</tr>
	</table>
	<input type='hidden' name='this_date' value='$this_date'>
	<input type='hidden' name='cal_sn' value='$cal_sn'>
	<div align='center'><input type='submit' name='act' value='$submit'></div>
	</form>";
	return $main;
}


//快貼校務行事曆
function &PasteForm($year="",$month="",$day=""){
	global $import_array,$kind_array,$hour_array,$restart_array,$week_array;
	global $module_manager,$manager_kind_array,$nor_kind_array;
	
	if ($module_manager==0) {
		echo "抱歉! 您沒有發佈校務行事曆的權限!";
		exit();
	}
	$submit="貼上行事曆";
	$main="
	<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#DFE8F2' class='small'>
	<tr bgcolor='#FEFBDA'>
	<td colspan='10'>
	<font class='dateStyle'>$year</font>
	年
	<font class='dateStyle'>$month</font>
	月
	<font class='dateStyle'>$day</font>（星期".$week_array[$w]."）：
	<a href='$_SERVER[PHP_SELF]?act=$act&this_day=$today' class='box'><img src='images/today.png' alt='回行事曆' width='16' height='16' hspace='2' border='0' align='absmiddle'>回行事曆</a>
	<a href='$_SERVER[PHP_SELF]?act=addThingForm&this_date=$this_date' class='box'>
	<img src='images/appointment.png' alt='新增事件' width='16' height='16' hspace='2' border='0' align='absmiddle'>新增事件</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingListView&this_date=$this_date' class='box'>
	<img src='images/list.png' alt='當月事件總覽' width='16' height='16'  hspace='2' border='0' align='absmiddle'>當月事件總覽</a>
	<a href='$_SERVER[PHP_SELF]?act=getMonthThingView&this_date=$this_date' class='box'>
	<img src='images/1day.png' alt='當月行事曆' width='16' height='16'  hspace='2' border='0' align='absmiddle'>當月行事曆</a>
	</td>
	</tr>
	</table>
	<br>
		<form action='$_SERVER[PHP_SELF]' method='post'>
      <font color=blue>◎快貼校務行事曆</font> - 請利用 Excel 依格式整理好後整批貼上<br>
      <textarea cols='80' rows='10' name='data'></textarea><br>
      <input type='submit' name='act' value='$submit'>
		</form>	
		<table border='0'>
		<tr>
			<td>
			※說明：如圖所示，僅選擇內容部分，複製並貼上即可。<<a href='./images/demo.xls'>下載範例檔</a>><br>
			<img src='./images/demo.png' border='0'><br>
			<font color='red'>※注意！已存在系統中的事件，請勿重覆貼上！</font>
			</td>
		</tr>
		</table>
	";
	
	
	
	return $main;
}





//取得一個事件的資料
function get_one_cal($cal_sn){
	global $CONN,$MODULE_TABLE_NAME;
	$sql_select = "select * from $MODULE_TABLE_NAME[0] where cal_sn=$cal_sn";
	$recordSet = $CONN->Execute($sql_select) or user_error("$sql_select",256);
	$c=$recordSet->FetchRow();
	return $c;
}

//取得一個事件的資料公開給哪些教師
function get_cal_to_who($cal_sn){
	global $CONN,$MODULE_TABLE_NAME;
	$sql_select = "select teacher_sn from $MODULE_TABLE_NAME[0] where from_cal_sn=$cal_sn";
	$recordSet = $CONN->Execute($sql_select) or user_error("$sql_select",256);
	while(list($teacher_sn)=$recordSet->FetchRow()){
		$tsn[]=$teacher_sn;
	}
	return $tsn;
}

//新增一個事件
function addOneThing($data){
	global $CONN,$MODULE_TABLE_NAME;
	$min=(empty($data[min]))?"00":$data[min];
	$time=$data[h].":".$min;

	$sql_insert = "insert into $MODULE_TABLE_NAME[0] (year,month,day,week,time,place,thing,kind,teacher_sn,from_teacher_sn,from_cal_sn,restart,restart_day,restart_end,import,post_time) values ($data[year],$data[month],$data[day],'$data[week]','$time','$data[place]','$data[thing]','$data[kind]',$_SESSION[session_tea_sn],$_SESSION[session_tea_sn],'0','$data[restart]','$data[restart_day]','$data[restart_end]','$data[import]',now())";
	$CONN->Execute($sql_insert) or user_error("新增事件失敗！<br>$sql_insert",256);
	$from_cal_sn=mysql_insert_id();
	
	if($data[kind]!="0"){
		//若是給全校則取得全校教師編號
		if(in_array("all",$data[to_teacher_sn])){
			$teacher_array=teacher_array();
			$all_tsn=array_keys($teacher_array);
		}else{
			$all_tsn=$data[to_teacher_sn];
		}
		
		if(empty($data[restart])){
			$data[restart_day]="";
			$data[restart_end]="";
		}
		
		//除了自己以外，給應該給的人
		foreach($all_tsn as $to_ts){
			if($to_ts==$_SESSION[session_tea_sn])continue;
			$sql_insert = "insert into $MODULE_TABLE_NAME[0] (year,month,day,week,time,place,thing,kind,teacher_sn,from_teacher_sn,from_cal_sn,restart,restart_day,restart_end,import,post_time) values ($data[year],$data[month],$data[day],'$data[week]','$time','$data[place]','$data[thing]','$data[kind]','$to_ts',$_SESSION[session_tea_sn],'$from_cal_sn','$data[restart]','$data[restart_day]','$data[restart_end]','$data[import]',now())";
			$CONN->Execute($sql_insert) or user_error("新增事件失敗！<br>$sql_insert",256);
		}
	}
	
	return true;
}

//新增校務事件
function PasteSchoolThing($DATA) {
	global $CONN,$MODULE_TABLE_NAME;
	
	 $data=explode("\n",$DATA);
	 
   //開始存入 每筆資料有6欄 (年,月,日,時,地,事件,重要性)
   foreach ($data as $a) {
    $data_array=explode("\t",$a);
       $year=trim($data_array[0]);
       $month=trim($data_array[1]);
       $day=trim($data_array[2]);
       $time=trim($data_array[3]);
       $place=trim($data_array[4]);
       $thing=trim($data_array[5]);
       $import=trim($data_array[6]);

    if ($year!="" and $month!="" and $day!="" and $time!="" and $place!="" and $thing!="") {
       $week=date ("w", mktime(0,0,0,$month,$day,$year));
			 $sql_insert = "insert into $MODULE_TABLE_NAME[0] (year,month,day,week,time,place,thing,kind,teacher_sn,from_teacher_sn,from_cal_sn,restart,restart_day,restart_end,import,post_time)	values ('$year','$month','$day','$week','$time','$place','$thing','0',".$_SESSION['session_tea_sn'].",".$_SESSION['session_tea_sn'].",'0','0','0000-00-00','0000-00-00',".$import.",now())";
			 $CONN->Execute($sql_insert) or user_error("新增事件失敗！<br>$sql_insert",256);
    
    } //欄位完整
	 } // end foreach	
	 
	return true;
}

//更新一個事件
function updateOneThing($data,$cal_sn){
	global $CONN,$MODULE_TABLE_NAME;
	$min=(empty($data[min]))?"00":$data[min];
	$time=$data[h].":".$min;
	
	if(empty($data[restart])){
		$data[restart_day]="";
		$data[restart_end]="";
	}
	
	$sql_update="update $MODULE_TABLE_NAME[0] set
	time='$time',
	place='$data[place]',
	thing='$data[thing]',
	kind='$data[kind]',
	restart='$data[restart]',
	restart_day='$data[restart_day]',
	restart_end='$data[restart_end]',
	import='$data[import]',
	post_time=now()
	where cal_sn='$cal_sn'
	";

	$CONN->Execute($sql_update) or user_error("更新事件失敗！<br>$sql_update",256);
	
	
	//若是給全校則取得全校教師編號
	
	if(in_array("all",$data[to_teacher_sn])){
		$teacher_array=teacher_array();
		$all_tsn=array_keys($teacher_array);
	}else{
		$all_tsn=$data[to_teacher_sn];
	}

		//除了自己以外，給應該給的人
	foreach($all_tsn as $to_ts){
		if($to_ts==$_SESSION[session_tea_sn])continue;
		$sql_update = "
		update $MODULE_TABLE_NAME[0] set
		time='$time',
		place='$data[place]',
		thing='$data[thing]',
		kind='$data[kind]',
		restart='$data[restart]',
		restart_day='$data[restart_day]',
		restart_end='$data[restart_end]',
		import='$data[import]',
		post_time=now()
		where from_cal_sn=$cal_sn
		";
	
		$CONN->Execute($sql_update) or user_error("更新事件失敗！<br>$sql_update",256);
	}
	
	
	
	return true;
}

//刪除事件
function delThing($cal_sn){
	global $CONN,$MODULE_TABLE_NAME;
	$sql_delete = "delete from $MODULE_TABLE_NAME[0] where cal_sn=$cal_sn or from_cal_sn=$cal_sn";
	$CONN->Execute($sql_delete) or user_error("刪除事件失敗！<br>$sql_delete",256);
	return true;
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
