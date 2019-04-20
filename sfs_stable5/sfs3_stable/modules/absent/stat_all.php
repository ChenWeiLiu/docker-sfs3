<?php

// $Id: stat_all.php 9011 2016-11-21 17:48:30Z smallduh $

/* 取得設定檔 */
include_once "config.php";

sfs_check();
$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];

//default start_date and end_date
//取得開學日
$start_day=curr_year_seme_day($sel_year,$sel_seme);
$start_date=($_POST['start_date']=='')?$start_day[st_start]:$_POST['start_date'];
$end_date=($_POST['end_date']=='')?date("Y-m-d"):$_POST['end_date'];


if(!empty($_REQUEST[this_date])){
	$d=explode("-",$_REQUEST[this_date]);
}else{
	$d=explode("-",date("Y-m-d"));
}
$year=(empty($_REQUEST[year]))?$d[0]:$_REQUEST[year];
$month=(empty($_REQUEST[month]))?$d[1]:$_REQUEST[month];
$day=(empty($_REQUEST[day]))?$d[2]:$_REQUEST[day];
	
$act=$_REQUEST[act];



//執行動作判斷
if($act=="儲存登記"){
	add_all($sel_year,$sel_seme,$_POST[class_id],$_POST[date],$_POST[s]);
	header("location: $_SERVER[PHP_SELF]?this_date=$_POST[date]&class_id=$_POST[class_id]");
}elseif($act=="print"){
	$main=statForm($sel_year,$sel_seme,$_GET[class_id],"print");
	echo $main;
	exit;
}elseif($act=="取消返回"){
	header("location: $_SERVER[PHP_SELF]?this_date=$_POST[date]&class_id=$_POST[class_id]");
}else{
	$main=&mainForm($sel_year,$sel_seme,$_REQUEST[class_id]);
}


//秀出網頁
head("缺曠課統計");

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
<script type=\"text/javascript\" src=\"./JSCal/src/js/jscal2.js\"></script>
<script type=\"text/javascript\" src=\"./JSCal/src/js/lang/b5.js\"></script>
<link type=\"text/css\" rel=\"stylesheet\" href=\"./JSCal/src/css/jscal2.css\">
";
echo $main;
foot();

//主要輸入畫面
function &mainForm($sel_year,$sel_seme,$class_id=""){
	global $school_menu_p,$year,$month,$day,$SFS_PATH_HTML,$school_menu_p,$start_day,$start_date,$end_date;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	

	//取得該班及學生名單，以及填寫表格
	if(!empty($class_id)){
		$signForm=&statForm($sel_year,$sel_seme,$class_id);
	}
	//年級與班級選單
	$class_select=&classSelect($sel_year,$sel_seme,"","class_id",$class_id,false);
	
	if(!empty($class_id)){
		$cal = new MyCalendar;
		$cal->linkStr="&class_id=$class_id";
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


	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<form action='$_SERVER[PHP_SELF]' method='post'>
	<tr bgcolor='#FFFFFF'><td>
	<font color='blue'>$sel_year</font>學年度第<font color='blue'>$sel_seme</font>學期
	自
	<input type=\"text\" name=\"start_date\" id=\"start_date\" size=\"10\" value=\"".$start_date."\">
 		<script type=\"text/javascript\">
		new Calendar({
  		    inputField: \"start_date\",
   		    dateFormat: \"%Y-%m-%d\",
    	    trigger: \"start_date\",
 	        min:\"".$start_day[st_start]."\",
    		max: \"".date("Y-m-d")."\",
    	    bottomBar: false,
    	    weekNumbers: false,
    	    showTime: false,
    	    onSelect: function() {this.hide();}
		    });
		</script>
	至
	<input type=\"text\" name=\"end_date\" id=\"end_date\" size=\"10\" value=\"".$end_date."\">
			<script type = \"text/javascript\" >
			new Calendar({
				inputField:\"end_date\",
				dateFormat: \"%Y-%m-%d\",
				trigger: \"end_date\",
				min: \"".$start_day[st_start]."\",
				max: \"".date("Y-m-d")."\",
				bottomBar: false,
				weekNumbers: false,
				showTime: false,
				onSelect: function() {this.hide();}
				});
			</script>
	止

	$class_select

	缺曠課統計統計結果
	<input type='submit' value='列出'>
	</td></tr>
	</form>
	</table>
	<a href='$_SERVER[PHP_SELF]?act=print&class_id=$class_id&end_date=$end_date&start_date=$start_date' target='_blank'>列印</a>
	<table cellspacing='1' cellpadding='3'>
	<tr>
	<td valign='top'>$signForm</td>
	<td valign='top'>$the_cal</td>
	</tr>
	</table>
	";
	return $main;
}



//取得該班及學生名單，以及填寫表格
function &statForm($sel_year,$sel_seme,$class_id,$mode){
	global $year,$month,$day,$CONN,$start_date,$end_date;
	
	//取得缺曠課類別
	$absent_kind_array= SFS_TEXT("缺曠課類別");
	
	//增加集會這個類別
	$abkind_TXT="<td>集會</td>";
	
	//製作標題
	foreach($absent_kind_array as $abkind){
		$abkind_TXT.="<td>$abkind</td>";
	}
		
	//轉換班級代號
	$c=class_id_2_old($class_id);
	
	//取得學生陣列
	$stud=get_stud_array($c[0],$c[1],$c[3],$c[4],"id","name");

	//列出表頭
	$s=get_school_base();
	if ($mode=="print") {
		$start_date=$_GET['start_date'];
		$end_date=$_GET['end_date'];

		$print_str="	<html><head><title>$s[sch_cname]".$sel_year."學年度第".$sel_seme."學期".$c[3]."年".$c[4]."班缺曠課統計表</title></head>\n
			<body>
			<p align='center'><font face='標楷體' size='4'>$s[sch_cname]".$sel_year."學年度第".$sel_seme."學期</font><br><font face='標楷體' size='5'>".$c[3]."年".$c[4]."班缺曠課統計表</font></p>\n
			<p align='center'><font face='標楷體' size='3'>統計區間：$start_date 至 $end_date</font></p>\n
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"610\">\n
			<tr><td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"50\">學號</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"50\">座號</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓名</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">集會</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">曠課</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">事假</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">病假</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">喪假</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">公假</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">不可抗力</td></tr>\n";
	}

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

	//取得開學日
	$start_day=curr_year_seme_day($sel_year,$sel_seme);
	$i=1;
	foreach($stud as $id=>$name){

		//取得該學生資料
		//$aaa=getOneMdata($id,$sel_year,$sel_seme,"$year-$month-$day","種類",$start_day[st_start]);
		$aaa=getOneMdata($id,$sel_year,$sel_seme,$end_date,"種類",$start_date);

		//各種缺曠課數
		if ($mode=="print") {
			$d_b=($i%5==0 || $i==count($stud))?"1.5pt":"0.75pt";
			$sections_data="<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt $d_b 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$aaa[f]</font></td>\n";
		} else {
			$sections_data="<td>$aaa[f]</td>";
		}
		foreach($absent_kind_array as $abkind){
			if ($mode=="print") {
				$r_b=($abkind=="不可抗力")?"1.5pt":"0.75pt";
				$sections_data.="<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt $r_b $d_b 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$aaa[$abkind]</font></td>\n";
			} else {
				$sections_data.="<td>$aaa[$abkind]</td>";
			}
		}

		if ($mode=="print") {
			$print_str.="<tr><td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt $d_b 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$id</font></td>\n
					<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt $d_b 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$num[$id]."</font></td>\n
					<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt $d_b 0.75pt; padding: 0cm 1.4pt;\">$name</td>\n
					$sections_data
					</tr>\n";
		} else {
			//每一列資料
			//整天沒來
			$data.="
			<tr bgcolor='#FFFFFF' align='center'>
			<td>$id</td>
			<td>".$num[$id]."</td>
			<td>$name</td>		
			$sections_data	
			</tr>";
		}
		$i++;
	}
	
	//說明
	$help_text="
	「集會」只統計曠課的升旗或降旗。(因為只有曠課的升旗或降旗才予以扣減日常生活表現分數)
	";
	$help=help($help_text);
	$main="
	<table cellspacing='0' cellpadding='0' class='small' >
	<tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#000000' class='small'>
		<tr bgcolor='#E6F2FF'>
		<td>學號</td>
		<td>座號</td>
		<td>姓名</td>		
		$abkind_TXT
		</tr>
		<form action='$_SERVER[PHP_SELF]' method='post' name='myform'>
		$data	
		</table>
	</td><td valign='top'>

		</form>
	</td></tr>
	</table>
	$help
	";
	if ($mode=="print") {
		$print_str.="</table>
		<p style='font-size:14pt;font-family:標楷體'>核章處</p>
		";

		return $print_str;
	}	else {
		return $main;
	}

}
?>
