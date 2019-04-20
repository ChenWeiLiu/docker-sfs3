<?php

// $Id: score_nor.php 5310 2009-01-10 07:57:56Z hami $

// 取得設定檔
include "config.php";

sfs_check();

//取得學期
$year_seme=($_GET['year_seme'])?$_GET['year_seme']:$_POST['year_seme'];
$year_name=($_GET['year_name'])?$_GET['year_name']:$_POST['year_name'];
$me=($_GET['me'])?$_GET['me']:$_POST['me'];
$default=$_POST['default'];
if (!$default) $stud_score=$_POST['stud_score'];

//程式檔頭
head("日常成績總表");
print_menu($school_menu_p);
$m_arr = get_module_setup("score_nor");
extract($m_arr, EXTR_OVERWRITE);

echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";

$sel_year = curr_year(); //目前學年
$sel_seme = curr_seme(); //目前學期
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id

//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
$delta_year=curr_year()-$sel_year;
$class_id= sprintf ("%03d_%1d_%02d_%02d", curr_year(),curr_seme(),$year_name+$delta_year,$me);

//取得學生名單
$class_num= $year_name.sprintf("%02d",$me);
$i=0;
$sql="select a.student_sn,a.seme_num from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_name[0]' and a.student_sn=b.student_sn and b.stud_study_cond=0 order by a.seme_num";
$rs=$CONN->Execute($sql) or die($sql);;
while (!$rs->EOF) {
	$sn=$rs->fields["student_sn"];
	$stud_sn[]=$sn;
	$stud_num[$sn]=$rs->fields["seme_num"];
	$i++;
	$rs->MoveNext();
}
$student_number=$i;

//取得該成績單和成績無關的欄位欄位資料
	$i=0;
	$comm_num=0;
	$text_table="
		<td colspan='6' align='center'>勤惰加減分</td>
		<td colspan='8' align='center'>日常加減分</td>
		<td colspan='7' align='center'>獎懲加減分</td>
		<td rowspan='3' valign='bottom' width='30' align='center'>總分</td>
		<td rowspan='3' valign='bottom' width='300'>學習描述文字說明</td>";
	$text_table2="
	<tr bgcolor='#bbd0ff'>
	<td align='center' valign='bottom' width='15' rowspan='2'>全勤</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>曠課</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>病假</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>事假</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>集會</td>
	<td align='center' valign='bottom' width='30' rowspan='2'>小計</td>
	<td align='center' valign='bottom' width='30' rowspan='2'>導師評分<br><font color='#ff0000'>(±5)</font></td>
	<td colspan='4' align='center'>團體活動成績</td>
	<td rowspan='2' valign='bottom' width='30'>公共服務<br><font color='#ff0000'>(±5)</font></td>
	<td rowspan='2' valign='bottom' width='30'>校外特殊表現<br><font color='#ff0000'>(+5)</font></td>
	<td rowspan='2' valign='bottom' width='30' align='center'>小計</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>大功</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>小功</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>嘉獎</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>大過</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>小過</td>
	<td align='center' valign='bottom' width='15' rowspan='2'>警告</td>
	<td align='center' valign='bottom' width='30' rowspan='2'>小計</td>
	</tr>
	<tr>
	<td align='center' width='30'>班級活動<br><font color='#ff0000'>(±5)</font></td>
	<td align='center' width='30'>社團活動<br><font color='#ff0000'>(±5)</font></td>
	<td align='center' width='30'>自治活動<br><font color='#ff0000'>(±5)</font></td>
	<td align='center' width='30'>例行活動<br><font color='#ff0000'>(±5)</font></td>
	</tr>";
 	
	//設定加減成績資料
	$score_sn=array("0"=>"1","1"=>"1","2"=>"1","3"=>"1","4"=>"1","5"=>"1","6"=>"0");

	$main="	<table bgcolor=#ffffff border=0 cellpadding=2 cellspacing=1>
		<tr bgcolor='#ffffff'><td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'>
		<td align='center' valign='bottom' rowspan='3'>座號</td>
		<td align='center' valign='bottom' rowspan='3'>姓名</td>
		$text_table
		</tr>
		$text_table2
		";
		
	//顯示成績
	for ($m=0;$m<count($stud_sn);$m++){
		$sn=$stud_sn[$m];
		$rs=&$CONN->Execute("select stud_name,stud_id from stud_base where student_sn='$sn'");

		//取得座號及姓名
		$stud_name=$rs->fields['stud_name'];
		$id=$rs->fields['stud_id'];
		$stud_id[$m]=$id;
		$site_num=$stud_num[$sn];

		$abs_score="";
		$nor_score="";
		$rew_score="";
		$table_temp="";
		
		//取得導師加減分
		$rs_nor=&$CONN->Execute("select * from seme_score_nor where seme_year_seme='$seme_year_seme' and stud_id='$id'");
		$stud_score[$id][0]=$rs_nor->fields['score1'];
		$stud_score[$id][1]=$rs_nor->fields['score2'];
		$stud_score[$id][2]=$rs_nor->fields['score3'];
		$stud_score[$id][3]=$rs_nor->fields['score4'];
		$stud_score[$id][4]=$rs_nor->fields['score5'];
		$stud_score[$id][5]=$rs_nor->fields['score6'];
		$stud_score[$id][6]=$rs_nor->fields['score7'];
		$stud_score[$id][7]=round($stud_score[$id][0]+($stud_score[$id][1]+$stud_score[$id][2]+$stud_score[$id][3]+$stud_score[$id][4])/4+$stud_score[$id][5]+$stud_score[$id][6]);
		if ($stud_score[$id][7]=="") $stud_score[$id][7]="0";
		for ($j=0;$j<=6;$j++) {
			$score_value=$stud_score[$id][$j];
			$nor_score.="<td align='center'>$score_value</td>";
		}
		//計算全勤
		$rs_abs=$CONN->Execute("select * from school_day where year='$sel_year' and seme='$sel_seme'");
		if ($rs_abs) {
			while (!$rs_abs->EOF) {
				$school_day[$rs_abs->fields['day_kind']]=$rs_abs->fields['day'];
				$rs_abs->MoveNext();
			}
		}
		$abs_kind="'事假','病假','曠課','集會'";
		$abs_month=array();
		$sql_abs="select distinct month from stud_absent where year='$sel_year' and semester='$sel_seme' and stud_id='$id' and absent_kind in ($abs_kind) order by month";
		$rs_abs=$CONN->Execute($sql_abs) or die($sql_abs);
		if ($rs_abs->recordcount()>0) {
			while (!$rs_abs->EOF) {
				$abs_month[$rs_abs->fields['month']]=1;
				$rs_abs->MoveNext();
			}
		}
		$st=explode("-",$school_day[start]);
		$se=explode("-",$school_day[end]);
		$month_start=intval($st[1]);
		$month_end=intval($se[1]);
		if ($month_end<$month_start) $month_end+=12;
		$months=$month_end-$month_start+1;
		$abs_all=$months;
		for ($i=$month_start;$i<=$month_end;$i++) {
			$j=$i;
			if ($i>12) $j=$i-12;
			if ($abs_month[$j]==1) $abs_all--;
		}
		if ($a_score!="1") $abs_all=($abs_all==$months)?"5":"0";
		//取得出缺席值
		if (intval($cl_days)==0) $cl_days=30;
		if (intval($sl_days)==0) $sl_days=80;
		$rs_abs=$CONN->Execute("select * from stud_seme_abs where seme_year_seme='$seme_year_seme' and stud_id='$id' order by abs_kind");
		if ($rs_abs->recordcount()>0)
			while (!$rs_abs->EOF) {
				$stud_abs[$id][$rs_abs->fields['abs_kind']]=$rs_abs->fields['abs_days'];
				$rs_abs->MoveNext();
			}
		for ($i=1;$i<=6;$i++) if ($stud_abs[$id][$i]=="") $stud_abs[$id][$i]=0;
		$stud_abs[$id][0]=$abs_all-floor($stud_abs[$id][3]/2+$stud_abs[$id][2]/$sl_days+$stud_abs[$id][1]/$cl_days+$stud_abs[$id][4]/4);
		$abs_score.="<td align='center'>".$abs_all."</td>";
		$abs_score.="<td align='center'>".$stud_abs[$id][3]."</td>";
		$abs_score.="<td align='center'>".$stud_abs[$id][2]."</td>";
		$abs_score.="<td align='center'>".$stud_abs[$id][1]."</td>";
		$abs_score.="<td align='center'>".$stud_abs[$id][4]."</td>";
		$abs_score.="<td align='center' bgcolor='#e6fbff'>".$stud_abs[$id][0]."</td>";
		//取得獎懲值
		$rs_rew=$CONN->Execute("select * from stud_seme_rew where seme_year_seme='$seme_year_seme' and stud_id='$id' order by sr_kind_id");
		if ($rs_rew) {
			while (!$rs_rew->EOF) {
				$stud_rew[$id][$rs_rew->fields['sr_kind_id']]=$rs_rew->fields['sr_num'];
				$rs_rew->MoveNext();
			}
		}
		for ($i=1;$i<=6;$i++) {
			if ($stud_rew[$id][$i]=="") $stud_rew[$id][$i]=0;
			$rew_score.="<td align='center'>".$stud_rew[$id][$i]."</td>";
		}
		$stud_rew[$id][0]=$stud_rew[$id][1]*9+$stud_rew[$id][2]*3+$stud_rew[$id][3]-$stud_rew[$id][4]*7;
		if ($f_w=="") $f_w=0;
		if ($stud_rew[$id][6] > 0) $stud_rew[$id][0]-=$stud_rew[$id][6]-1+$f_w;
		if ($stud_rew[$id][5] > 2)
			$stud_rew[$id][0]-=($stud_rew[$id][5]-2)*3+4;
		else
			$stud_rew[$id][0]-=$stud_rew[$id][5]*2;
		$rew_score.="<td align='center' bgcolor='#e6fbff'>".$stud_rew[$id][0]."</td>";
		
		$rs=&$CONN->Execute("select student_sn,ss_score_memo from stud_seme_score_nor where student_sn='$stud_sn[$m]' and ss_id='0' and seme_year_seme='$seme_year_seme'");
		$ss_memo=addslashes($rs->fields['ss_score_memo']);
		if ($stud_score[$id][$j]=="") $stud_score[$id][$j]=addslashes($rs->fields['ss_score_memo']);
		$nor_total=80+$stud_abs[$id][0]+$stud_score[$id][7]+$stud_rew[$id][0];
		$main.="
			<tr bgcolor='#ffffff'>
			<td align='right'>$site_num &nbsp;</td>
               		<td>&nbsp; $stud_name &nbsp;</td>
               		$abs_score
               		$nor_score
               		<td align='center' bgcolor='#e6fbff'>".$stud_score[$id][7]."</td>
               		$rew_score
               		<td align='center'>$nor_total</td>
               		<td>".stripslashes($ss_memo)."</td>
               		</tr>
               		";
		$ss_score_memo=$stud_score[$id][$j];
	}
	$main.="</table>";


echo $main;
echo "</tr></table></tr></table>";
$help_text="如果勤惰及獎懲內容不正確，可能是學務處尚未統計，建議您先請學務處統計日常成績。";
echo help($help_text);

foot();
?>

<script language="JavaScript">
<!-- Begin
function jumpMenu0(){
	var str, classstr ;
 if (document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value!="") {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value;
	}
}

function jumpMenu1(){
	var str, classstr ;
 if ((document.form1.year_name.value!="") & (document.form1.year_name.options[document.form1.year_name.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form1.year_seme.value + "&year_name=" + document.form1.year_name.options[document.form1.year_name.selectedIndex].value;
	}
}

function jumpMenu2(){
	var str, classstr ;
 if ((document.form2.year_name.value!="") & (document.form2.me.options[document.form2.me.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form2.year_seme.value + "&year_name=" + document.form2.year_name.value + "&me=" + document.form2.me.options[document.form2.me.selectedIndex].value;
	}
}

function jumpMenu3(){
	var str, classstr ;
 if ((document.form3.year_name.value!="") & (document.form3.me.value!="") & (document.form3.stud_id.options[document.form3.stud_id.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form3.year_seme.value + "&year_name=" + document.form3.year_name.value + "&me=" +document.form3.me.value + "&stud_id=" + document.form3.stud_id.options[document.form3.stud_id.selectedIndex].value;
	}
}
//  End -->
</script>