<?php

// $Id: absence.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_calendar.php";
require_once "./module-cfg.php";
//引入函數
include "./my_fun.php";

//使用者認證
sfs_check();

$month=($_GET[month])?$_GET[month]:$_POST[month];
$year=($_GET[year])?$_GET[year]:$_POST[year];
$day=($_GET[day])?$_GET[day]:$_POST[day];

$withseme=($_GET[withseme])?$_GET[withseme]:$_POST[withseme];
$sel_year=($_GET[sel_year])?$_GET[sel_year]:$_POST[sel_year];
$sel_seme=($_GET[sel_seme])?$_GET[sel_seme]:$_POST[sel_seme];

if(!empty($_GET[this_date]) or !empty($_POST[this_date])){
	$this_date=($_GET[this_date])?$_GET[this_date]:$_POST[this_date];
	$d=explode("-",$this_date);
	$year=$d[0];
	$month=$d[1];
	$day=$d[2];
}
//echo $year.$month.$day;
if($_GET[act]) $act=$_GET[act];
else $act=$_POST[act];
$ddate=($_GET[ddate])?$_GET[ddate]:$_POST[ddate];
for($i=0;$i<count($_POST[stud_id]);$i++){
	$stud_id[$i]=($_GET[stud_id][$i])?$_GET[stud_id][$i]:$_POST[stud_id][$i];
	$ab1[$i]=($_GET[ab1][$i])?$_GET[ab1][$i]:$_POST[ab1][$i];
	$ab2[$i]=($_GET[ab2][$i])?$_GET[ab2][$i]:$_POST[ab2][$i];
	$ab3[$i]=($_GET[ab3][$i])?$_GET[ab3][$i]:$_POST[ab3][$i];
	$ab4[$i]=($_GET[ab4][$i])?$_GET[ab4][$i]:$_POST[ab4][$i];
	$ab5[$i]=($_GET[ab5][$i])?$_GET[ab5][$i]:$_POST[ab5][$i];
	$ab6[$i]=($_GET[ab6][$i])?$_GET[ab6][$i]:$_POST[ab6][$i];
	$ab7[$i]=($_GET[ab7][$i])?$_GET[ab7][$i]:$_POST[ab7][$i];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);
//秀出網頁
head("班級事務");

echo print_menu($menu_p);
//設定主網頁顯示區的背景顏色
if($_POST[Submit_save]=="儲存"){
	for($i=0;$i<count($stud_id);$i++){
		$ABS=$ab1[$i]+$ab2[$i]+$ab3[$i]+$ab4[$i]+$ab5[$i]+$ab6[$i]+$ab7[$i];
		if($ABS>="1"){
            $sql_select = "select abs_sn from stud_absence where stud_id='$stud_id[$i]' and date='$ddate'";
            $result_s = $CONN->Execute($sql_select) or die($sql_select);
            $abs_sn[$i]=$result_s->fields['abs_sn'];
            if($abs_sn[$i]) $sql_ABS = "UPDATE stud_absence set ab1='$ab1[$i]',ab2='$ab2[$i]',ab3='$ab3[$i]',ab4='$ab4[$i]',ab5='$ab5[$i]',ab6='$ab6[$i]',ab7='$ab7[$i]' where stud_id='$stud_id[$i]' and date='$ddate'";
            else $sql_ABS = "INSERT INTO stud_absence (date,stud_id,ab1,ab2,ab3,ab4,ab5,ab6,ab7) values ('$ddate','$stud_id[$i]','$ab1[$i]','$ab2[$i]','$ab3[$i]','$ab4[$i]','$ab5[$i]','$ab6[$i]','$ab7[$i]')";
			$CONN->Execute($sql_ABS) or die($sql_ABS);
		}
		else{
			$sql_select = "select abs_sn from stud_absence where stud_id='$stud_id[$i]' and date='$ddate'";
			$result_s = $CONN->Execute($sql_select) or die($sql_select);
			$abs_sn[$i]=$result_s->fields['abs_sn'];
			if($abs_sn[$i]) {
				$CONN->Execute("delete from stud_absence where abs_sn='$abs_sn[$i]'");
			}
		}
	}
}
if(empty($year))$year=date("Y");
if(empty($month))$month=date("m");
if(empty($day))$day=date("d");
$w=date ("w", mktime(0,0,0,$month,$day,$year));
$right=&getMonthView($year,$month,$day);

if($act=="edit") $data=&edit_Absence($year,$month,$day);
elseif($act=="statistics") $data=&statistics_Absence($year,$month,$day);
else $data=&view_Absence($year,$month,$day);


//顯示在網頁上的畫面
if($act=="statistics") {
	$monthNames = array("一月", "二月", "三月", "四月", "五月", "六月","七月", "八月", "九月", "十月", "十一月", "十二月");
	if($month!='1') { $upmonth=$month-1; $upyear=$year; }
	else { $upyear=$year-1; $upmonth=12; }
	if($month!='12') { $downmonth=$month+1; $downyear=$year; }
	else { $downyear=$year+1; $downmonth=1; }

	if($month==$SFS_SEME2-1){
		$upmonth=12;
		$upyear=$year-1;
		$downmonth=$month+1;
		$downyear=$year;
		$Mlast_sel_year=$sel_year;
		$Mlast_sel_seme=1;
		$Mnext_sel_year=$sel_year;
		$Mnext_sel_seme=2;
	}
	elseif($month==$SFS_SEME2){
		$upmonth=$month-1;
		$upyear=$year;
		$downmonth=$month+1;
		$downyear=$year;
		$Mlast_sel_year=$sel_year;
		$Mlast_sel_seme=1;
		$Mnext_sel_year=$sel_year;
		$Mnext_sel_seme=2;
	}
	elseif($month==$SFS_SEME1-1){
		$upmonth=$month-1;
		$upyear=$year;
		$downmonth=$month+1;
		$downyear=$year;
		$Mlast_sel_year=$sel_year;
		$Mlast_sel_seme=2;
		$Mnext_sel_year=$sel_year+1;
		$Mnext_sel_seme=1;
	}
	elseif($month==$SFS_SEME1){
		$upmonth=$month-1;
		$upyear=$year;
		$downmonth=$month+1;
		$downyear=$year;
		$Mlast_sel_year=$sel_year-1;
		$Mlast_sel_seme=2;
		$Mnext_sel_year=$sel_year;
		$Mnext_sel_seme=1;
	}
	elseif($month=='12'){
		$upmonth=$month-1;
		$upyear=$year;
		$downmonth=1;
		$downyear=$year+1;
		$Mlast_sel_year=$sel_year;
		$Mlast_sel_seme=1;
		$Mnext_sel_year=$sel_year;
		$Mnext_sel_seme=1;
	}
	else{
		$upmonth=$month-1;
		$upyear=$year;
		$downmonth=$month+1;
		$downyear=$year;
		$Mlast_sel_year=$sel_year;
		$Mlast_sel_seme=$sel_month;
		$Mnext_sel_year=$sel_year;
		$Mnext_sel_seme=$sel_month;
	}

	if($month-6 >= 1) { $upsememonth=$month-6; $upsemeyear=$year; }
	else { $upsememonth=$month+6; $upsemeyear=$year-1; }
	if($month+6 <= 12) { $downsememonth=$month+6; $downsemeyear=$year; }
	else { $downsememonth=$month-6; $downsemeyear=$year+1; }

	if($sel_seme== 1) { $last_sel_seme=2 ;$last_sel_year=$sel_year-1; $next_sel_seme=2; $next_sel_year=$sel_year; }
	else { $last_sel_seme=1 ;$last_sel_year=$sel_year; $next_sel_seme=1; $next_sel_year=$sel_year+1; }
	
	if($withseme) $tell=$sel_year."學年度第".$sel_seme."學期統計表";
	else $tell=$year."年".$monthNames[intval($month)-1]."份統計表";

	$ctrl_bar="<a href='$_SERVER[PHP_SELF]?act=edit&this_date=$this_date' class='box'>編輯</a>
		<br><a href='$_SERVER[PHP_SELF]?act=statistics&month=$upmonth&year=$upyear&sel_year=$Mlast_sel_year&sel_seme=$Mlast_sel_seme'><img src='images/left.png' style='border: 0px solid ;' align='top'></a><a href='$_SERVER[PHP_SELF]?act=statistics&this_date=$today'>本月份</a><a href='$_SERVER[PHP_SELF]?act=statistics&month=$downmonth&year=$downyear&sel_year=$Mnext_sel_year&sel_seme=$Mnext_sel_seme'><img src='images/right.png' style='border: 0px solid ;' align='top'></a>|<a href='$_SERVER[PHP_SELF]?act=statistics&withseme=1&month=$upsememonth&year=$upsemeyear&sel_year=$last_sel_year&sel_seme=$last_sel_seme'><img src='images/left.png' style='border: 0px solid ;' align='top'></a><a href='$_SERVER[PHP_SELF]?act=statistics&withseme=1&this_date=$today'>本學期</a><a href='$_SERVER[PHP_SELF]?act=statistics&withseme=1&month=$downsememonth&year=$downsemeyear&sel_year=$next_sel_year&sel_seme=$next_sel_seme'><img src='images/right.png' style='border: 0px solid ;' align='top'></a>
		$tell";
}
elseif($act=="edit") {
	echo "<form name='form_save' method='post' action='{$_SERVER['PHP_SELF']}'>";
	$ctrl_bar="<a href='$_SERVER[PHP_SELF]?act=edit&this_date=$this_date' class='box'>編輯</a>
		<input type='submit' name='Submit_save' value='儲存'>
		<a href='$_SERVER[PHP_SELF]?act=statistics&this_date=$this_date' class='box'>統計</a>";

}
else{
	$ctrl_bar="<a href='$_SERVER[PHP_SELF]?act=edit&this_date=$this_date' class='box'>編輯</a>
		<a href='$_SERVER[PHP_SELF]?act=statistics&this_date=$this_date' class='box'>統計</a>";

}
$main="
<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#000000' class='small' valign='top'>
<tr bgcolor='#FEFBDA'>
<td colspan='9'>
<font class='dateStyle'>$year</font>
年
<font class='dateStyle'>$month</font>
月
<font class='dateStyle'>$day</font>（星期".$week_array[$w]."）<font class='dateStyle'>".$class_name[1]."</font>出缺席紀錄簿
<a href='$_SERVER[PHP_SELF]?act=&this_date=$today' class='box'><img src='images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
".$ctrl_bar."
</td>
</tr>
<tr bgcolor='#EAECEE'>
<td nowrap rowspan='2'>姓名</td><td nowrap  rowspan='2'>座號</td><td nowrap  rowspan='2'>遲到</td><td nowrap  rowspan='2'>早退</td><td nowrap colspan='5' align='center'>缺席</td>
</tr>
<tr bgcolor='#FAF799'>
<td nowrap>曠課</td><td nowrap>事假</td><td nowrap>病假</td><td nowrap>喪假</td><td nowrap>不可抗力</td>
</tr>
$data
</table>";


echo "<table width='100%'><tr><td width='70%' valign='top'>".$main."</td><td align='right' valign='top'>".$right."</td></tr></table>";
if($act=="edit") {
	$this_date=($this_date)?$this_date:$today;
	echo "<input type='hidden' name='this_date' value='$this_date'>";
	echo "<input type='hidden' name='ddate' value='$this_date'>";
	echo "</form>";
}

//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";
//程式檔尾
foot();

//取得月行事曆
function &getMonthView($year="",$month="",$day="",$mode=""){
	global $today;
	$cal = new MyCalendar;
	$cal->setStartDay(1);
	$mc=($mode=="viewThing")?$cal->getMonthThingView($month,$year,$day):$cal->getMonthView($month,$year,$day);
	$main="
	<table cellspacing='1' cellpadding='2' bgcolor='#000000' class='small'>
	<tr bgcolor='#FEFBDA'><td align='center'>
	<a href='$_SERVER[PHP_SELF]?act=$act&this_date=$today' class='box'><img src='images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
	</td></tr>
	<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
	</table>
	";
	return $main;
}

function &view_Absence($year="",$month="",$day="",$mode=""){
	global $today,$act,$class_name,$CONN;
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
	$sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
	//echo $sql;
	$rs=$CONN->Execute($sql);
    $m=0;
	while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name from stud_base where stud_id='$stud_id[$m]'");
        $stud_name[$m] = $rs_name->fields["stud_name"];
		$t_date=date ("Y-m-d", mktime(0,0,0,$month,$day,$year));
		$sql_abs="select * from stud_absence where stud_id='$stud_id[$m]' and date='$t_date'";
		$rs_abs=$CONN->Execute($sql_abs);
		$ab1[$m] = $rs_abs->fields["ab1"];
		$ab2[$m] = $rs_abs->fields["ab2"];
		$ab3[$m] = $rs_abs->fields["ab3"];
		$ab4[$m] = $rs_abs->fields["ab4"];
		$ab5[$m] = $rs_abs->fields["ab5"];
		$ab6[$m] = $rs_abs->fields["ab6"];
		$ab7[$m] = $rs_abs->fields["ab7"];
		if($ab1[$m]) $ab1_icon[$m]="<img src='images/no.png'>";
		if($ab2[$m]) $ab2_icon[$m]="<img src='images/no.png'>";
		if($ab3[$m]) $ab3_icon[$m]="<img src='images/no.png'>";
		if($ab4[$m]) $ab4_icon[$m]="<img src='images/no.png'>";
		if($ab5[$m]) $ab5_icon[$m]="<img src='images/no.png'>";
		if($ab6[$m]) $ab6_icon[$m]="<img src='images/no.png'>";
		if($ab7[$m]) $ab7_icon[$m]="<img src='images/no.png'>";
		$data.="<tr bgcolor='#FFFFFF'>
					<td align='center' bgcolor='#BBDFAB'>$stud_name[$m]</td>
					<td align='center' bgcolor='#E4FBD0'>$site_num[$m]</td>
					<td align='center'>$ab1_icon[$m]</td>
					<td align='center'>$ab2_icon[$m]</td>
					<td align='center'>$ab3_icon[$m]</td>
					<td align='center'>$ab4_icon[$m]</td>
					<td align='center'>$ab5_icon[$m]</td>
					<td align='center'>$ab6_icon[$m]</td>
					<td align='center'>$ab7_icon[$m]</td>
			   	</tr>";
		$m++;
        $rs->MoveNext();
    }
	return $data;
}

function &edit_Absence($year="",$month="",$day="",$mode=""){
	global $today,$act,$class_name,$CONN;
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
	$sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
	//echo $sql;
	$rs=$CONN->Execute($sql);
    $m=0;
	while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name from stud_base where stud_id='$stud_id[$m]'");
        $stud_name[$m] = $rs_name->fields["stud_name"];
		$t_date=date ("Y-m-d", mktime(0,0,0,$month,$day,$year));
		$sql_abs="select * from stud_absence where stud_id='$stud_id[$m]' and date='$t_date'";
		$rs_abs=$CONN->Execute($sql_abs);
		$ab1[$m] = $rs_abs->fields["ab1"];
		$ab2[$m] = $rs_abs->fields["ab2"];
		$ab3[$m] = $rs_abs->fields["ab3"];
		$ab4[$m] = $rs_abs->fields["ab4"];
		$ab5[$m] = $rs_abs->fields["ab5"];
		$ab6[$m] = $rs_abs->fields["ab6"];
		$ab7[$m] = $rs_abs->fields["ab7"];
		if($ab1[$m]) $ab1_check[$m]="checked";
		if($ab2[$m]) $ab2_check[$m]="checked";
		if($ab3[$m]) $ab3_check[$m]="checked";
		if($ab4[$m]) $ab4_check[$m]="checked";
		if($ab5[$m]) $ab5_check[$m]="checked";
		if($ab6[$m]) $ab6_check[$m]="checked";
		if($ab7[$m]) $ab7_check[$m]="checked";
		$data.="<tr bgcolor='#FFFFFF'>
					<input type='hidden' name='stud_id[$m]' value='$stud_id[$m]'>
					<td align='center' bgcolor='#BBDFAB'>$stud_name[$m]</td>
					<td align='center' bgcolor='#E4FBD0'>$site_num[$m]</td>
					<td align='center'><input type='checkbox' name='ab1[$m]' $ab1_check[$m] value='1'></td>
					<td align='center'><input type='checkbox' name='ab2[$m]' $ab2_check[$m] value='1'></td>
					<td align='center'><input type='checkbox' name='ab3[$m]' $ab3_check[$m] value='1'></td>
					<td align='center'><input type='checkbox' name='ab4[$m]' $ab4_check[$m] value='1'></td>
					<td align='center'><input type='checkbox' name='ab5[$m]' $ab5_check[$m] value='1'></td>
					<td align='center'><input type='checkbox' name='ab6[$m]' $ab6_check[$m] value='1'></td>
					<td align='center'><input type='checkbox' name='ab7[$m]' $ab7_check[$m] value='1'></td>
			   	</tr>";
		$m++;
        $rs->MoveNext();
    }
	return $data;
}

function &statistics_Absence($year="",$month="",$day="",$mode=""){
	global $today,$act,$class_name,$CONN,$withseme,$sel_year,$sel_seme;
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
	$sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
	//echo $sql;
	$rs=$CONN->Execute($sql);
    $m=0;
	while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name from stud_base where stud_id='$stud_id[$m]'");
        $stud_name[$m] = $rs_name->fields["stud_name"];
		
		
		$staryear=1912+$sel_year-($sel_seme%2);
		$endyear=($sel_seme=='1')?$staryear+1:$staryear;
		
		
		$startmonth=($sel_seme=='1')?8:2;
		$endmonth=($sel_seme=='1')?1:7;
		$start_seme_date=date ("Y-m-d", mktime(0,0,0,$startmonth,1,$staryear));
		$end_seme_date=date ("Y-m-d", mktime(0,0,0,$endmonth,31,$endyear));
		$t_date=date ("Y-m", mktime(0,0,0,$month,$day,$year));
		if($withseme) $sql_abs="select * from stud_absence where stud_id='$stud_id[$m]' and date >='$start_seme_date' and date<='$end_seme_date'";
		else $sql_abs="select * from stud_absence where stud_id='$stud_id[$m]' and date like '$t_date%' ";
		$rs_abs=$CONN->Execute($sql_abs);
		$n=0;
		while(!$rs_abs->EOF){
			$ab1[$m][$n] = $rs_abs->fields["ab1"];
			$ab2[$m][$n] = $rs_abs->fields["ab2"];
			$ab3[$m][$n] = $rs_abs->fields["ab3"];
			$ab4[$m][$n] = $rs_abs->fields["ab4"];
			$ab5[$m][$n] = $rs_abs->fields["ab5"];
			$ab6[$m][$n] = $rs_abs->fields["ab6"];
			$ab7[$m][$n] = $rs_abs->fields["ab7"];
			$ab1_sta[$m]=$ab1_sta[$m]+$ab1[$m][$n];
			$ab2_sta[$m]=$ab2_sta[$m]+$ab2[$m][$n];
			$ab3_sta[$m]=$ab3_sta[$m]+$ab3[$m][$n];
			$ab4_sta[$m]=$ab4_sta[$m]+$ab4[$m][$n];
			$ab5_sta[$m]=$ab5_sta[$m]+$ab5[$m][$n];
			$ab6_sta[$m]=$ab6_sta[$m]+$ab6[$m][$n];
			$ab7_sta[$m]=$ab7_sta[$m]+$ab7[$m][$n];
			$n++;
			$rs_abs->MoveNext();
		}
		if($ab1_sta[$m]=="" || $ab1_sta[$m]=="0") $ab1_sta[$m]="<font color='#CECECE'>0</font>";
		if($ab2_sta[$m]=="" || $ab2_sta[$m]=="0") $ab2_sta[$m]="<font color='#CECECE'>0</font>";
		if($ab3_sta[$m]=="" || $ab3_sta[$m]=="0") $ab3_sta[$m]="<font color='#CECECE'>0</font>";
		if($ab4_sta[$m]=="" || $ab4_sta[$m]=="0") $ab4_sta[$m]="<font color='#CECECE'>0</font>";
		if($ab5_sta[$m]=="" || $ab5_sta[$m]=="0") $ab5_sta[$m]="<font color='#CECECE'>0</font>";
		if($ab6_sta[$m]=="" || $ab6_sta[$m]=="0") $ab6_sta[$m]="<font color='#CECECE'>0</font>";
		if($ab7_sta[$m]=="" || $ab7_sta[$m]=="0") $ab7_sta[$m]="<font color='#CECECE'>0</font>";
		
		$data.="<tr bgcolor='#FFFFFF'>
					<input type='hidden' name='stud_id[$m]' value='$stud_id[$m]'>
					<td align='center' bgcolor='#BBDFAB'>$stud_name[$m]</td>
					<td align='center' bgcolor='#E4FBD0'>$site_num[$m]</td>
					<td align='center'>$ab1_sta[$m]</td>
					<td align='center'>$ab2_sta[$m]</td>
					<td align='center'>$ab3_sta[$m]</td>
					<td align='center'>$ab4_sta[$m]</td>
					<td align='center'>$ab5_sta[$m]</td>
					<td align='center'>$ab6_sta[$m]</td>
					<td align='center'>$ab7_sta[$m]</td>
			   	</tr>";
		$m++;
        $rs->MoveNext();
    }
	return $data;
}

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
