<?php

// $Id: report.php 6764 2012-05-21 05:16:09Z infodaes $

/* 取得設定檔 */
include "config.php";

sfs_check();

//取得學年學期
$year_seme=$_REQUEST[year_seme];
if ($year_seme) {
	$sel_year=intval(substr($year_seme,0,3));
	$sel_seme=substr($year_seme,3,1);
} else {
	$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
	$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];
}

//取得週次
$weeks_array=get_week_arr($sel_year,$sel_seme,$today);

if ($_REQUEST[week_num]) {
	$week_num=$_REQUEST[week_num];
	$weeks_array[0]=$week_num;
}

if (empty($week_num)) $week_num=$weeks_array[0];

$act=$_POST[act];

if ($act=="列印獎勵公告"){
	$oo_path="report";
	$kd="and a.reward_kind > '0'";
	include ("trans_main.php");
} elseif ($act=="列印懲戒公告") {
	$oo_path="report";
	$kd="and a.reward_kind < '0'";
	include ("trans_main.php");
} elseif ($act=="列印今日獎勵") {
	$oo_path="report";
	$kd="and a.reward_kind > '0'";
	$dt="and a.reward_date = '".date("Y-m-d")."'";
	include ("trans_main.php");
} elseif ($act=="列印今日懲戒") {
	$oo_path="report";
	$kd="and a.reward_kind < '0'";
	$dt="and a.reward_date = '".date("Y-m-d")."'";
	include ("trans_main.php");
}

$main=&mainForm($sel_year,$sel_seme,$week_num);

//秀出網頁
head("週獎懲明細");
echo $main;
echo "</tr></table>";
foot();

//主要輸入畫面
function &mainForm($sel_year,$sel_seme,$week_num=""){
	global $student_menu_p,$SFS_PATH_HTML,$CONN,$today,$weeks_array;
	//相關功能表
	$tool_bar=&make_menu($student_menu_p);

	//週選單
	$start_day=curr_year_seme_day($sel_year,$sel_seme);
	$week_select="";
	if (!$start_day[start])
		$week_select="開學日沒有設定";
	else {
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

	$reward_list=reward_data($sel_year,$sel_seme);

	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<form action='$_SERVER[SCRIP_NAME]' method='post'>
	<tr bgcolor='#FFFFFF'><td>
	<font color='blue'>$sel_year</font>學年度第<font color='blue'>$sel_seme</font>學期
	$week_select
	<input type='hidden' name='act' value='view'>
	獎懲公告
	<input type='submit' name='act' value='列印獎勵公告'>
	<input type='submit' name='act' value='列印懲戒公告'>
	<input type='submit' name='act' value='列印今日獎勵'>
	<input type='submit' name='act' value='列印今日懲戒'>
	</td></tr></form>
	</table>
	<table cellspacing='1' cellpadding='3'>
	<tr>
	<td valign='top'>$reward_list</td>
	</tr>
	</table>
	";
	return $main;
}

function reward_data($sel_year,$sel_seme) {
	global $CONN,$weeks_array,$reward_arr,$class_year;

	//取得學生陣列
	$reward_year_seme=$sel_year.$sel_seme;
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$all_sn="";
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' order by seme_class,seme_num";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$stud_id=$res->fields[stud_id];
		$student_sn=$res->fields[student_sn];
		$seme_class[$stud_id]=$res->fields[seme_class];
		$seme_num[$stud_id]=$res->fields[seme_num];
		$all_sn.="'".$student_sn."',";
		$res->MoveNext();
	}
	$all_sn=substr($all_sn,0,-1);
	$query="select stud_id,stud_name from stud_base where student_sn in ($all_sn)";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$stud_id=$res->fields[stud_id];
		$stud_name[$stud_id]=addslashes($res->fields[stud_name]);
		$res->MoveNext();
	}

	//取得班級陣列
	$query="select class_id,c_name from school_class where year='$sel_year' and semester='$sel_seme' order by class_id";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$class_id=$res->fields[class_id];
		$c=explode("_",$class_id);
		$c_year=intval($c[2]);
		$class_name[$c_year.$c[3]]=$class_year[$c_year].$res->fields[c_name];
		$res->MoveNext();
	}

	$sw1=$weeks_array[0];
	$sw2=$sw1+1;
	$last_str=($sw2<count($weeks_array))?"and a.reward_date<'$weeks_array[$sw2]'":"";
	$temp_str="
		<table cellspacing='1' cellpadding='3' bgcolor='#9ebcdd' class='small'>
		<tr class='title_sbody2'>
		<td align='left'>年級</td>
		<td align='left'>班級</td>
		<td align='left'>座號</td>
		<td align='left'>姓名</td>
		<td align='left'>獎懲日期</td>
		<td align='left'>獎懲事由</td>
		<td align='left'>獎懲依據</td>
		<td align='left'>獎懲類別</td>
		<td align='left'>備註</td>
		</tr>";
	$query="select a.* from reward a left join stud_seme b on a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme' where a.reward_year_seme='$reward_year_seme' and a.reward_date>='$weeks_array[$sw1]' $last_str and dep_id <> 0 order by b.seme_class,b.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$stud_id=$res->fields[stud_id];
		$reward_kind=$res->fields[reward_kind];
		$bgcolor=($reward_kind>0)?"#FFE6D9":"#E6F2FF";
		$c=explode("年",$class_name[$seme_class[$stud_id]]);
		$temp_str.="
		<tr bgcolor=$bgcolor>
		<td>".$c[0]."
		<td>".$c[1]."
		<td>".$seme_num[$stud_id]."
		<td>".addslashes($stud_name[$stud_id])."
		<td width='100'>".addslashes($res->fields[reward_date])."
		<td width='150'>".addslashes($res->fields[reward_reason])."
		<td width='150'>".addslashes($res->fields[reward_base])."
		<td>".addslashes($reward_arr[$reward_kind])."
		<td></td>
		</tr>\n";
		
		$res->MoveNext();
	}
	
	return $temp_str;
}
?>
