<?php

// $Id: report.php 7742 2013-10-31 06:37:05Z smallduh $

/* 取得設定檔 */
include "config.php";
if($stud_view_self_absent) require_once "../absent/config.php";


// 認證檢查
sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}



//印出檔頭
head("學生資料自建");

//只限當學期
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();

//取得登入學生的學號和流水號
$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='".$_SESSION['session_log_id']."'";
$res=$CONN->Execute($query);
$student_sn=$res->fields['student_sn'];
if ($student_sn) {
	$query="select * from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query);
	if ($res->fields['stud_study_cond']!="0") {
		$student_sn="";
	} else {
		$stud_study_year=$res->fields['stud_study_year'];
	}
}



if(!empty($_REQUEST[this_date])){
	$d=explode("-",$_REQUEST[this_date]);
}else{
	$d=explode("-",date("Y-m-d"));
}


$sel_year=curr_year();
$sel_seme=curr_seme();

//取得週次
$weeks_array=get_week_arr($sel_year,$sel_seme,$today);
$start_day=curr_year_seme_day($sel_year,$sel_seme);

if ($_REQUEST[week_num]) {
	$week_num=$_REQUEST[week_num];
	$weeks_array[0]=$week_num;
	if ($start_day[st_start]) {
		$this_date=$weeks_array[$week_num];
		$d=explode("-",$this_date);
	}
}

if (empty($week_num)) $week_num=$weeks_array[0];

$year=(empty($_REQUEST[year]))?$d[0]:$_REQUEST[year];
$month=(empty($_REQUEST[month]))?$d[1]:$_REQUEST[month];
$day=(empty($_REQUEST[day]))?$d[2]:$_REQUEST[day];


$main=&mainForm($sel_year,$sel_seme,$week_num);

//秀出網頁
head("缺曠課明細");

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
foot();

//主要輸入畫面
function &mainForm($sel_year,$sel_seme,$week_num=""){
	global $school_menu_p,$year,$month,$day,$SFS_PATH_HTML,$CONN,$today,$start_day,$weeks_array;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	
	//週報表
	if ($week_num) $weekForm=wform($sel_year,$sel_seme,$week_num);
	
	//週選單
	$week_select="";
	if (!$start_day[st_start])
		$week_select="開學日沒有設定";
	else {
		reset($weeks_array);
		while(list($k,$v)=each($weeks_array)) {
			if ($k==0) continue;
			$weeks[$k]="第".$k."週 ($v ~ ".date("Y-m-d",(strtotime($v)+86400*6)).")";
		}
		$ds=new drop_select();
		$ds->s_name = "week_num"; //選單名稱
		$ds->id = $week_num; //索引ID
		$ds->arr = $weeks; //內容陣列
		$ds->has_empty = true; //先列出空白
		$ds->top_option = "請選擇週次";
		$ds->bgcolor = "#FFFFFF";
		$ds->font_style = "font-size:12px";
		$ds->is_submit = true; //更動時送出查詢
		$week_select=$ds->get_select();
	}
		
	//日曆的連結字串
//	$linkStr=(!empty($week_num))?"&week_num=$week_num":"";
	
	if(!empty($week_num)){
		$cal = new MyCalendar;
		$cal->linkStr=$linkStr;
		$cal->setStartDay(1);
		$cal->getDateLink();
		$mc=$cal->getMonthView($month,$year,$day);
		$the_cal="
		<table cellspacing='1' cellpadding='2' bgcolor='#E2ECFC' class='small'>
		<tr bgcolor='#FEFBDA'>
		<td align='center'>		
		<a href='$_SERVER[SCRIPT_NAME]?act=$_REQUEST[act]&this_day=$today' class='box'><img src='".$SFS_PATH_HTML."images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
		</td></tr>
		<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
		</table>
		";
	}

	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<tr bgcolor='#FFFFFF'><td>
	<form action='$_SERVER[SCRIPT_NAME]' method='post'>
	<font color='blue'>$sel_year</font>學年度第<font color='blue'>$sel_seme</font>學期
	$week_select
	<input type='hidden' name='act' value='view'>
	<input type='hidden' name='this_date' value='$year-$month-$day'>
	</td></form></tr>
	</table>
	<table cellspacing='1' cellpadding='3'>
	<tr>
	<td valign='top'>$weekForm</td>
	<td valign='top'>$the_cal</td>
	</tr>
	</table>
	";
	return $main;
}

function wform($sel_year,$sel_seme,$week_num) {
	global $CONN,$weekN,$class_year,$start_day,$weeks_array,$student_sn;

	//取得該班有幾節課
	$sql = "select sections,class_year from score_setup where year = '$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
	while (!$rs->EOF) {
		$i=$rs->fields['class_year'];
		$all_sections[$i] = $rs->fields['sections'];
		$rs->MoveNext();
	}
	$sql="select c_name,c_sort from school_class where year='$sel_year' and semester='$sel_seme' and enable=1 order by c_sort";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$class_cname[$rs->fields['c_sort']]=$rs->fields['c_name'];
		$rs->MoveNext();
	}
	$bgcolor_arr=array("1"=>"#FEFED7","2"=>"#FEFEC4","3"=>"#FEFEB1","4"=>"#FEFE9E","5"=>"#FEFE8B");
	$d=explode("-",$weeks_array[$week_num]);
	$wmt=mktime(0,0,0,$d[1],$d[2],$d[0]);
	$temp="
		<table cellspacing='0' cellpadding='0'0class='small'>
		<tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2' class='small'>
		<tr bgcolor='#E6F2FF'>
		<td align='center' rowspan='3'>年級</td>
		<td align='center' rowspan='3'>班級</td>
		<td align='center' rowspan='3'>座號</td>
		<td align='center' rowspan='3'>姓名</td>
		";
	
	$w_days=0;
	for ($i=1;$i<=6;$i++) { //2013.10.29 $i 由 5改為6
		$dd=getdate($wmt+86400*$i);
		$wd[$i]=sprintf("%04d-%02d-%02d",($dd[year]),$dd[mon],$dd[mday]);
		if ($DAY[$wd[$i]]=='1') $w_days++; //2013.10.29 統計本週上課日數
		$dw[$wd[$i]]=$i;
		$temp.="
		<td align='center' colspan='5'>".$dd[mon]."月".$dd[mday]."日</td>
		";
	}
	$temp.="<td align='center' rowspan='2' colspan='5'>本週合計</td><td align='center' rowspan='2' colspan='5'>至本週累計</td></tr><tr bgcolor='#E6F2FF'>";
	for ($i=1;$i<=6;$i++) $temp.="<td align='center' colspan='5'>星期".$weekN[$i-1]."</td>"; //2013.10.29 $i 由 5改為6
	$temp.="</tr><tr>";
	for ($i=1;$i<=8;$i++) {  //2013.10.29 $i 由 7改為8
		$temp.="
		<td bgcolor='".$bgcolor_arr[1]."'>事<br>假</td>
		<td bgcolor='".$bgcolor_arr[2]."'>病<br>假</td>
		<td bgcolor='".$bgcolor_arr[3]."'>曠<br>課</td>
		<td bgcolor='".$bgcolor_arr[4]."'>升<br>降<br>旗</td>
		<td bgcolor='".$bgcolor_arr[5]."'>其<br>他</td>";
	}
	$temp.="</tr>";
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	//2013.10.29  sql $wd[5] 由 5改為6
	$sql="select a.*,b.seme_num,c.stud_name from stud_absent a, stud_seme b, stud_base c where a.date >= '$start_day[st_start]' and a.date <= '$wd[6]' and a.stud_id=b.stud_id and b.student_sn=c.student_sn and c.student_sn='$student_sn' and b.seme_year_seme='$seme_year_seme' order by a.class_id,b.seme_num,a.date,a.section";
	$rs=$CONN->Execute($sql);
	if ($rs->recordcount() > 0) {
		$m=0;
		while (!$rs->EOF) {
			$ad=$rs->fields['date'];
			$id=$rs->fields['stud_id'];
			if ($stud_id[$m]!=$id) {
				$m++;
				$stud_id[$m]=$id;
				$stud_name[$m]=addslashes($rs->fields['stud_name']);
			} 
			$class_id=explode("_",$rs->fields['class_id']);
			$class[$m][year]=intval($class_id[2]);
			$class[$m][name]=intval($class_id[3]);
			$class[$m][num]=intval($rs->fields['seme_num']);
			switch ($rs->fields['absent_kind']) {
				case '事假':
					$abskind=1;
					break;
				case '病假':
					$abskind=2;
					break;
				case '曠課':
					$abskind=3;
					break;
				default:
					$abskind=5;
					break;
			}
			$section=$rs->fields['section'];
			if ($section=='uf' || $section=='df') {
				//如果是曠課的升降旗才處理
				if ($abskind==3) {
					if ($wd[1] <= $ad) {
						$enable[$m]=1;
						$absent[$m][$dw[$ad]][4]++;
						$absent[$m][7][4]++; //2013.10.29 [$m][7] 由 6改為7
					}
					$absent_total[$id][4]++;
				}
			} elseif ($section=="allday") {
				if ($wd[1] <= $ad) {
					$enable[$m]=1;
					//如果是曠課, 升降旗各加一
					if ($abskind==3) {
						$absent[$m][$dw[$ad]][4]+=2;
						$absent[$m][7][4]+=2; //2013.10.29 [$m][7] 由 6改為7
					}
					$absent[$m][$dw[$ad]][$abskind]+=$all_sections[$class[$m][year]];
					$absent[$m][7][$abskind]+=$all_sections[$class[$m][year]]; //2013.10.29 [$m][7] 由 6改為7
				}
				if ($abskind==3) $absent_total[$id][4]+=2;
				$absent_total[$id][$abskind]+=$all_sections[$class[$m][year]];
			} else {
				if ($wd[1] <= $ad) {
					$enable[$m]=1;
					$absent[$m][$dw[$ad]][$abskind]++;
					$absent[$m][7][$abskind]++; //2013.10.29 [$m][7] 由 6改為7
				}
				$absent_total[$id][$abskind]++;
			}
			$rs->MoveNext();
		}
	}
	reset($enable);
	while(list($i,$v)=each($enable)) {
		$temp.="<tr bgcolor='#E6F2FF'><td>".substr($class_year[$class[$i][year]],0,2)."<td>".$class_cname[$class[$i][name]]."<td align='right'>".$class[$i][num]."<td>".stripslashes($stud_name[$i]);
		for ($j=1;$j<=7;$j++) {  //2013.10.29 $j 由 6改為7
			for ($m=1;$m<=5;$m++) {	
				$temp.="<td bgcolor='".$bgcolor_arr[$m]."'>".$absent[$i][$j][$m];
			}
		}
		for ($m=1;$m<=5;$m++) {
			$temp.="<td bgcolor='".$bgcolor_arr[$m]."'>".$absent_total[$stud_id[$i]][$m];
		}
		$temp.="</tr>";
		$have_data=1;
	}
	if (!$have_data==1) $temp.="<tr bgcolor='#E6F2FF'><td colspan='39' align='center'>本週無資料</td></tr>";
	$temp.="</table>
		</td><td valign='top'>
		</td></tr>
		</table>
		";
	return $temp;
}
?>
