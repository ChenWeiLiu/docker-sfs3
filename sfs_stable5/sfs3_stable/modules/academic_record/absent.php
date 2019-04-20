<?php

// $Id: absent.php 7882 2014-02-20 07:07:53Z smallduh $

// 取得設定檔
include "config.php";
$view=$_GET['view'];

sfs_check();

//程式檔頭
head("觀看勤惰記錄");
$tool_bar=&make_menu($school_menu_p);
echo "$tool_bar<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";

//取得學年學期
$sel_year = curr_year(); //目前學年
$sel_seme = curr_seme(); //目前學期

//找出任教班級
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
$class_name=teacher_sn_to_class_name($teacher_sn);
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();

//儲存資料
/*
if ($_POST[save] && $IS_JHORES==0) {
	while(list($id,$d)=each($_POST[stud_abs])) {
		while(list($k,$v)=each($d)) {
			$CONN->Execute("replace into stud_seme_abs (seme_year_seme,stud_id,abs_kind,abs_days) values ('$seme_year_seme','$id','$k','$v')");
		}
	}
}
*/
if ($_POST[save] && $IS_JHORES==0) {
	foreach($_POST[stud_abs] as $id=>$d){
		foreach($d as $k=>$v){
			$CONN->Execute("replace into stud_seme_abs (seme_year_seme,stud_id,abs_kind,abs_days) values ('$seme_year_seme','$id','$k','$v')");
		}
	}
}

//統計班級現有人數
$sql="select count(student_sn) from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$class_num'";
$res=$CONN->Execute($sql);
$student_number=$res->fields[0];

//取得學生名單
$sql="select student_sn from stud_base where curr_class_num like '$class_name[0]%' and stud_study_cond='0' order by curr_class_num";
$rs=$CONN->Execute($sql) or die($sql);;
while (!$rs->EOF) {
	$stud_sn[]=$rs->fields["student_sn"];
	$rs->MoveNext();
}

if ($view=="One") {
	//取得日期資料
	$today=date("Y-m-d");
	$smday=curr_year_seme_day($sel_year,$sel_seme);
	$dd=explode("-",$smday[start]);
	//取得某班節數
	$all_sections=get_class_cn($class_name[3]);
	for($i=1;$i<=$all_sections;$i++){
		$sections_txt.="<td>第 $i 節</td>";
	}
	//取得學生資料
	$query="select stud_id,stud_name,curr_class_num from stud_base where student_sn='".$_GET['student_sn']."'";
	$res=$CONN->Execute($query) or die($query);
	$stud_name=$res->fields['stud_name'];
	$site_num=intval(substr($res->fields['curr_class_num'],-2,2));

	//取得缺席資料
	if ($_GET['mode']=='all') {
	  $query="select * from stud_absent where stud_id='".$res->fields['stud_id']."' order by date";
  } else {
	  $query="select * from stud_absent where date>='".$smday[start]."' and date<'".$today."' and stud_id='".$res->fields['stud_id']."' order by date";
  }
	$res=$CONN->Execute($query) or die($query);
	$aaa="";
	$data="";
	$total="";
	$lis=0;
	$i=0;
	while(!$res->EOF){
		$the_date=$res->fields['date'];
		$absent_kind=$res->fields['absent_kind'];
		$section=$res->fields['section'];
		if ($the_date != $pre_date) {
			if ($have_data) {
				$data.=show_data($pre_date,$aaa,$all_sections);
				$aaa="";
			}
			$pre_date=$the_date;
			$have_data=1;
			$lis++;
		}
		$aaa[$section]=$absent_kind;
		$total[$absent_kind][$section]++;
		$total[sum][$section]++;
		$i++;
		$res->MoveNext();
	}
	if ($i>0)
		$data.=show_data($the_date,$aaa,$all_sections);
	else
		$data.="<tr bgcolor='#E6F2FF'><td colspan='".($all_sections+4)."' bgcolor='#ffffff' align='center'>無任何請假記錄</td></tr>";

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
		$sum_data.="
			<tr bgcolor='#E6F2FF' align='center'>
			<td>$kind</td>
			<td bgcolor='#FBF8B9'>-</td>
			$section_data
			<td bgcolor='#FFE6D9'>-</td>
			<td bgcolor='#FEFED0'>".$ttotal[$kind]."</td>
			</tr>";
	}

	//計算集會次數
	if ($IS_JHORES!=0) {
		$section_data="";
		for($j=1;$j<=$all_sections;$j++){
			$section_data.="<td bgcolor='#FFFFFF'></td>";
		}
		$ufs=$total['曠課'][uf]+$total['曠課'][allday];
		$dfs=$total['曠課'][df]+$total['曠課'][allday];
		$sum_data="<tr bgcolor='#E6F2FF' align='center'><td>集會</td><td bgcolor='#FBF8B9'>".$ufs."</td>$section_data<td bgcolor='#FFE6D9'>".$dfs."</td><td bgcolor='#FEFED0'>".($ufs+$dfs)."</td></tr>".$sum_data;
	}

	//顯示資料
	$main="
	<table cellspacing='1' cellpadding='3' bgcolor='#000000' class='small'>
	<tr bgcolor='#E6F2FF'><td align='center'>姓名<td colspan='".($all_sections+3)."' bgcolor='#ffffff'>$stud_name</td></tr>
	<tr bgcolor='#E6F2FF'><td align='center'>座號<td colspan='".($all_sections+3)."' bgcolor='#ffffff'>$site_num</td></tr>
	<tr bgcolor='#E6F2FF'>
	<td align='center'>日期</td>
	<td bgcolor='#FBF8B9'>升旗</td>
	$sections_txt
	<td bgcolor='#FFE6D9'>降旗</td>
	<td bgcolor='#FEFED0'>總計</td>
	</tr>
	$data
	<tr bgcolor='#E6F2FF'>
	<td bgcolor='#cccccc' colspan=".($all_sections+4)." align='center'>
	合計
	</tr>
	$sum_data
	</table>";
//	if ($i==0) $main.="<tr bgcolor='#ffffff'><td align='center' colspan='5'>無獎懲記錄</td></tr>\n";
	$main.="<p class=small><a href={$_SERVER['PHP_SELF']}?view=One&student_sn=".$_GET['student_sn']."&mode=all>列出 [".$stud_name."] 就學期間全部明細</a>&nbsp;&nbsp;<a href={$_SERVER['PHP_SELF']}?view=All>觀看全班記錄</a></p>";

} else {
	//顯示表頭資料
	$main="<table bgcolor=#ffffff border=0 cellpadding=2 cellspacing=1>
		<tr bgcolor='#ffffff'><td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>";
	if ($IS_JHORES==0 and $is_summary_input !='n' ) {
		$main.="<form method='post' action='".$_SERVER['PHP_SELF']."'>";
		$end_form="</form>";
		$end_submit="<input type='submit' name='save' value='確定儲存'> <input type='reset' value='回復預設值'>";
	} else { $main.="<font color='red' size=2>系統管理員並未於本模組的模組變數開放您可以直接輸入學生整學期的缺席統計數據。<BR>此處顯示的資訊僅提供您查閱，要紀錄學生缺席情形請點選連結至<a href='../class_things/absent_class.php'> [級務管理]-[缺曠課紀錄]</a> 逐日登錄！"; }

	$main.="
		<tr bgcolor='#c4d9ff'>
		<td align='center'>座號</td>
		<td align='center'>姓名</td>
		<td align='center'>事假</td>
		<td align='center'>病假</td>
		<td align='center'>曠課</td>
		<td align='center'>集會</td>
		<td align='center'>公假</td>
		<td align='center'>其他</td>
		</tr>
		";

	//顯示成績
	for ($m=0;$m<count($stud_sn);$m++){
		$rs=&$CONN->Execute("select stud_name,stud_id from stud_base where student_sn='$stud_sn[$m]'");

		//取得座號及姓名
		$stud_name=$rs->fields['stud_name'];
		$stud_id[$m]=$rs->fields['stud_id'];
		$site_num=student_sn_to_site_num($stud_sn[$m]);

		$main.="<tr bgcolor='#ffffff'><td>$site_num</td><td><a href={$_SERVER['PHP_SELF']}?view=One&student_sn=$stud_sn[$m]>$stud_name</a></td>";
		$query="select * from stud_seme_abs where seme_year_seme='$seme_year_seme' and stud_id='$stud_id[$m]' order by abs_kind";
		$res=$CONN->Execute($query) or die($query);
		$abs=array();
		if ($res)
			while (!$res->EOF) {
				$abs[$res->fields['abs_kind']]=$res->fields['abs_days'];
				$res->MoveNext();
			}
		for ($i=1;$i<=6;$i++) {
			if ($IS_JHORES==0) {
				$main.="<td><input type='text' size='3' name='stud_abs[".$stud_id[$m]."][$i]' value='".intval($abs[$i])."'></td>";
			} else {
				$main.="<td>".intval($abs[$i])."</td>";
			}
		}
		$main.="</tr>\n";
	}
	$main.="</table>";
}

echo $main;
echo "$end_submit</td></tr>$end_form</table></td></tr></table>";
foot();

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
		<td bgcolor='#FEFED0'>---</td>
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
