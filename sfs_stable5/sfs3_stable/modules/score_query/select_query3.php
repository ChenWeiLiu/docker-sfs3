<?php
// $Id: select_query3.php 5310 2009-01-10 07:57:56Z hami $
/*引入設定檔*/
include "config.php";

//使用者認證
sfs_check();

$use_rate=$_REQUEST['use_rate'];
$show_avg=$_REQUEST['show_avg'];
$year_seme=$_REQUEST['year_seme'];
$year_name=$_REQUEST['year_name'];
$me=$_REQUEST['me'];
$stage=$_REQUEST['stage'];
$kind=$_REQUEST['kind'];
$go=$_POST['go'];
$friendly_print=$_GET['friendly_print'];

$yorn=findyorn();

if ($friendly_print==0) {
	$border="0";
	$bgcolor1="#FDC3F5";
	$bgcolor2="#B8FF91";
	$bgcolor3="#CFFFC4";
	$bgcolor4="#B4BED3";
	$bgcolor5="#CBD6ED";
	$bgcolor6="#D8E4FD";
} else {
	$border="1";
	$bgcolor1="#FFFFFF";
	$bgcolor2="#FFFFFF";
	$bgcolor3="#FFFFFF";
	$bgcolor4="#FFFFFF";
	$bgcolor5="#FFFFFF";
	$bgcolor6="#FFFFFF";
}
//秀出網頁
if ($friendly_print != 1) head("成績綜合查詢");

//列出橫向的連結選單模組
if ($friendly_print != 1) print_menu($menu_p);

//設定主網頁顯示區的背景顏色
if ($friendly_print != 1) echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc><tr><td>";
if ($year_seme) {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
}
$score_semester="score_semester_".$sel_year."_".$sel_seme;
$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_year_menu=class_year_menu($sel_year,$sel_seme,$year_name);

if($year_name)	$class_year_name_menu=class_name_menu($sel_year,$sel_seme,$year_name,$me);

if($year_name && $me)	$stage_menu=stage_menu($sel_year,$sel_seme,$year_name,$me,$stage);

if ($year_name && $me && $stage) {
	if ($stage=='255') {
		$choice_kind[0]="全學期";
		$chart_kind="學期成績";
	} else {
		$kind_menu=kind_menu($sel_year,$sel_seme,$year_name,$me,$stage,$kind);
		if ($kind=="1") {	
			$choice_kind[0]="定期評量";
			$chart_kind="定期考查";
		} elseif ($kind=="2") {
			$choice_kind[0]="平時成績";
			$chart_kind="平時成績";
		} else {
			$choice_kind[1]="定期評量";
			$choice_kind[2]="平時成績";
			$chart_kind="";
		}
	}
	$rate_checked=($use_rate)?"checked":"";
	$avg_checked=($show_avg)?"checked":"";
	$asign_checked=($print_asign)?"checked":"";
	$rate_menu="<input type='checkbox' name='use_rate' $rate_checked onclick='this.form.submit()';>加權";
	$avg_menu="<input type='checkbox' name='show_avg' $avg_checked onclick='this.form.submit()';>顯示各科平均";
	$icon_menu="<input type='submit' name='go' value='查詢'>";
}

$menu="<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	<table>
	<tr>
	<td>$year_seme_menu</td><td>$class_year_menu</td><td>$class_year_name_menu</td><td>$stage_menu</td><td>$kind_menu</td><td>$rate_menu</td><td>$avg_menu</td><td>$icon_menu</td>
	</tr>
	</table></form>";
if ($friendly_print != 1) echo $menu;

//以上為選單bar

/******************************************************************************************/
if($go || $friendly_print==1){
    //取出本學年本學期的學校成績共通設定
	$sql="select * from score_setup where class_year=$year_name and year='$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql);
	$score_mode= $rs->fields['score_mode'];
	$test_ratio=explode("-",$rs->fields['test_ratio']);
	$sratio=$test_ratio[0];
	$nratio=$test_ratio[1];

	$pers=1;
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$class_id=sprintf ("%03d_%d_%02d_%02d", $sel_year,$sel_seme,$year_name, $me);
	$seme_class=$year_name.sprintf("%02d",$me);
	$sql="select a.student_sn,b.stud_name,a.seme_num,b.stud_study_cond from stud_seme a,stud_base b where a.stud_id=b.stud_id and a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' order by a.seme_num";
	$rs=$CONN->Execute($sql);
	$all_sn="";
	$i=0;
	while (!$rs->EOF) {
		$sn=$rs->fields['student_sn'];
		$student_sn[$i]=$sn;
		$student_name[$sn]=$rs->fields['stud_name'];
		$student_sitenum[$sn]=$rs->fields['seme_num'];
		$study_cond[$sn]=$rs->fields['stud_study_cond'];
		$all_sn.=$sn.",";
		$i++;
		$rs->MoveNext();
	}
	$all_sn=substr($all_sn,0,-1);
	while (list($k,$v)=each($choice_kind)) {
		$sql="select a.*,b.rate from $score_semester a,score_ss b where a.class_id='$class_id' and a.test_sort='$stage' and a.test_kind='$v' and a.ss_id=b.ss_id and a.student_sn in ($all_sn) and b.enable='1' order by b.ss_id,b.sort,b.sub_sort";
		$rs=$CONN->Execute($sql);
		$i=-1;
		$ossid="";
		while(!$rs->EOF){
			$sn=$rs->fields["student_sn"];
			$ssid=$rs->fields["ss_id"];
			if ($ssid!=$ossid) {
				$i++;
				$ossid=$ssid;
				while (!empty($ss_id[$i]) && $ss_id[$i]!=$ssid) {$i++;} 
				$ss_id[$i]=$ssid;
				$test_kind[$i]=$rs->fields["test_kind"];
				$rate[$ssid]=($use_rate)?$rs->fields["rate"]:1;
				if ($rate[$ssid]==100 && $use_rate) $pers=100;
			}
			$score=$rs->fields["score"];
			if($score==-100) $score="";
			$Sscore[$sn][$ssid][$test_kind[$i]]=$score;
			$rs->MoveNext();
		}
	}
	//處理定期+平時
	if (count($choice_kind) > 1) {
		$choice_kind[0]="階段成績";
		while (list($k,$v)=each($student_sn)) {
			reset ($ss_id);
			while (list($i,$j)=each($ss_id)) {
				$Sscore[$v][$j][$choice_kind[0]]=($Sscore[$v][$j][$choice_kind[1]]*$sratio+$Sscore[$v][$j][$choice_kind[2]]*$nratio)/($sratio+$nratio);
			}
		}
	}
       	$rate_all=0;
       	$statistics_total_average=0;
       	for($i=0;$i<count($ss_id);$i++){
		$subject_name[$i]=ss_id_to_subject_name($ss_id[$i]);
		$rate_string=($use_rate)?"<br>x".$rate[$ss_id[$i]]."%":"";
		if ($friendly_print=='1')
			$subject_list.="<td width='48' valign='middle'><p align='center'><span style='font-size:10pt;'>$subject_name[$i]".$rate_string."</span></p></td>";
		else
			$subject_list.="<td width='40' align='center'><small>$subject_name[$i]".$rate_string."</small></td>";
		$rate_all+=$rate[$ss_id[$i]];
		for($j=0;$j<count($student_sn);$j++){
			$SS1[$j]=$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[0]];
			$SSav[$j]=number_format($SS1[$j],0);
		  	if ($friendly_print=='1')
				$score_list[$j].="<td width='48' valign='middle'><p align='right'><span style='font-size:10pt;'>$SSav[$j] &nbsp;&nbsp;</span></p></td>";
			else
				$score_list[$j].="<td align='right'>$SSav[$j] &nbsp;&nbsp;</td>";
			$one_student_total[$j]=$one_student_total[$j]+$SSav[$j]*$rate[$ss_id[$i]];
			$statistics_average[$i]=$statistics_average[$i]+$SSav[$j];
			if($SSav[$j]==100) $statistics_100[$i]++;
			elseif(($SSav[$j]<100)&&($SSav[$j]>=90)) $statistics_90[$i]++;
			elseif(($SSav[$j]<90)&&($SSav[$j]>=80)) $statistics_80[$i]++;
			elseif(($SSav[$j]<80)&&($SSav[$j]>=70)) $statistics_70[$i]++;
			elseif(($SSav[$j]<70)&&($SSav[$j]>=60)) $statistics_60[$i]++;
			elseif(($SSav[$j]<60)&&($SSav[$j]>=50)) $statistics_50[$i]++;
			elseif(($SSav[$j]<50)&&($SSav[$j]>=40)) $statistics_40[$i]++;
			elseif(($SSav[$j]<40)&&($SSav[$j]>=30)) $statistics_30[$i]++;
			elseif(($SSav[$j]<30)&&($SSav[$j]>=20)) $statistics_20[$i]++;
			elseif(($SSav[$j]<20)&&($SSav[$j]>=10)) $statistics_10[$i]++;
			else $statistics_0[$i]++;
            	}
		$statistics_average[$i]=number_format($statistics_average[$i]/count($student_sn),2);
		$statistics_total_average+=$statistics_average[$i]*$rate[$ss_id[$i]];
		if ($friendly_print=='1') {
			$statistics_list_average.="<td width='48' valign='middle'><p align='right'><span style='font-size:10pt;'>$statistics_average[$i] &nbsp;&nbsp;</span></p></td>";
			if ($print_asign) $asign_col.="<td width='48' valign='middle'><p align='right'><span style='font-size:10pt;'>&nbsp;&nbsp;</span></p></td>";
		} else
			$statistics_list_average.="<td align='right'>$statistics_average[$i]</td>";
		$statistics_list_100.="<td bgcolor='#FFFFFF' align='right'>$statistics_100[$i] &nbsp;&nbsp;</td>";
		$statistics_list_90.="<td bgcolor='#FFFFFF' align='right'>$statistics_90[$i] &nbsp;&nbsp;</td>";
		$statistics_list_80.="<td bgcolor='#FFFFFF' align='right'>$statistics_80[$i] &nbsp;&nbsp;</td>";
		$statistics_list_70.="<td bgcolor='#FFFFFF' align='right'>$statistics_70[$i] &nbsp;&nbsp;</td>";
		$statistics_list_60.="<td bgcolor='#FFFFFF' align='right'>$statistics_60[$i] &nbsp;&nbsp;</td>";
		$statistics_list_50.="<td bgcolor='#FFFFFF' align='right'>$statistics_50[$i] &nbsp;&nbsp;</td>";
		$statistics_list_40.="<td bgcolor='#FFFFFF' align='right'>$statistics_40[$i] &nbsp;&nbsp;</td>";
		$statistics_list_30.="<td bgcolor='#FFFFFF' align='right'>$statistics_30[$i] &nbsp;&nbsp;</td>";
		$statistics_list_20.="<td bgcolor='#FFFFFF' align='right'>$statistics_20[$i] &nbsp;&nbsp;</td>";
		$statistics_list_10.="<td bgcolor='#FFFFFF' align='right'>$statistics_10[$i] &nbsp;&nbsp;</td>";
		$statistics_list_0.="<td bgcolor='#FFFFFF' align='right'>$statistics_0[$i] &nbsp;&nbsp;</td>";
	}
	$statistics_total=number_format($statistics_total_average/$pers,0);
	$statistics_total_average=number_format($statistics_total_average/$rate_all,2);
	$many_ss=count($ss_id);
       	for($i=0;$i<count($student_sn);$i++){
//		if ($one_student_total[$i]==0 && $study_cond[$student_sn[$i]]!=0) break; 
		$one_student_average[$i]=number_format(($one_student_total[$i]/$rate_all),2);
		$seniority[$i]=how_big($one_student_total[$i],$one_student_total);
		if ($friendly_print=='1')
			$student_and_score_list.="
				<tr>
				<td width='32' valign='middle'><p align='right'><span style='font-size:10pt;'>".$student_sitenum[$student_sn[$i]]." &nbsp;</span></p></td>
				<td width='60' valign='middle'><p align='left'><span style='font-size:10pt;'>&nbsp; ".$student_name[$student_sn[$i]]."</span></p></td>
				$score_list[$i]
				<td width='50' valign='middle'><p align='right'><span style='font-size:10pt;'>".($one_student_total[$i]/$pers)." &nbsp;&nbsp;</span></p></td>
				<td width='48' valign='middle'><p align='right'><span style='font-size:10pt;'>$one_student_average[$i] &nbsp;&nbsp;</span></p></td>
				<td width='32' valign='middle'><p align='right'><span style='font-size:10pt;'>$seniority[$i] &nbsp;&nbsp;</span></p></td>
				</tr>";
		else
			$student_and_score_list.="
				<tr bgcolor=#ffffff>
		    		<td bgcolor=$bgcolor2 align='right'>".$student_sitenum[$student_sn[$i]]." &nbsp;</td>
		    		<td bgcolor=$bgcolor3 align='left'>&nbsp; ".$student_name[$student_sn[$i]]."</td>
		    		$score_list[$i]
		    		<td bgcolor=$bgcolor4 align='right'>".($one_student_total[$i]/$pers)." &nbsp;&nbsp;</td>
		    		<td bgcolor=$bgcolor5 align='right'>$one_student_average[$i]</td>
		    		<td bgcolor=$bgcolor6 align='right'>$seniority[$i] &nbsp;&nbsp;</td>
				</tr>";
	}
	$print_msg=($stage)?"<a href='{$_SERVER['PHP_SELF']}?year_seme=$year_seme&year_name=$year_name&me=$me&stage=$stage&kind=$kind&friendly_print=1&use_rate=$use_rate&show_avg=$show_avg&print_asign=$print_asign' target='new'><b><small>友善列印</small></b></a><br>":""; 
	if ($print_asign) $asign_col="<tr><td colspan='2' valign='middle'><p align='center'><span style='font-size:10pt;'>各科老師簽章</span></p></td>".$asign_col."<td><p align='center'>---</p></td><td><p align='center'>---</p></td><td><p align='center'>---</p></td></tr>";
	if ($friendly_print==1) {
		$main="
			<table border='1' cellspacing='0' height='12' bordercolordark='white' bordercolorlight='black'>
			<tr>
			<td width='32' valign='middle'><p align='center'><span style='font-size:10pt;'>座號</span></p></td>
			<td width='60' valign='middle'><p align='center'><span style='font-size:10pt;'>姓名</span></p></td>
			$subject_list
			<td width='50' valign='middle'><p align='center'><span style='font-size:10pt;'>總分</span></p></td>
			<td width='48' valign='middle'><p align='center'><span style='font-size:10pt;'>平均</span></p></td>
			<td width='32' valign='middle'><p align='center'><span style='font-size:10pt;'>名次</span></p></td>
			</tr>
			$student_and_score_list";
			if ($show_avg) $main.="
			<tr>
			<td colspan='2' valign='middle'><p align='center'><span style='font-size:10pt;'>各科平均</span></p></td>
			$statistics_list_average
			<td valign='middle'><p align='right'><span style='font-size:10pt;'>$statistics_total &nbsp;&nbsp;</span></p></td>
			<td valign='middle'><p align='right'><span style='font-size:10pt;'>$statistics_total_average &nbsp;&nbsp;</span></p></td>
			<td>&nbsp;</td>
			</tr>";
		if ($print_asign) $main.=$asign_col;

	} else {
		$main="
			<table bgcolor=#0000ff border=$border cellpadding='6' cellspacing='1'>
			<tr bgcolor=$bgcolor1>
			<td width='30' align='center'><small>座號</small></td>
			<td width='80' align='center'><small>姓名</small></td>
			$subject_list
			<td width='50' align='center'><small>總分</small></td>
			<td width='40' align='center'><small>平均</small></td>
			<td width='40' align='center'><small>名次</small></td>
			</tr>
			$student_and_score_list";
		if ($show_avg) $main.="
			<tr bgcolor=$bgcolor1>
			<td colspan='2' align='center'><small>各科平均</small></td>
			$statistics_list_average
			<td width='50' align='right'>$statistics_total &nbsp;&nbsp;</td>
			<td width='40'>&nbsp;$statistics_total_average</td>
			<td width='40'>&nbsp;</td>
			</tr>";
	}
	if ($friendly_print==1) {
		$school_title=score_head($sel_year,$sel_seme,$year_name,$me,$stage,$chart_kind);
		echo "<center><b>".$school_title."</b><br></center><br>".$main."</table>";
	}
	$main="
		".$print_msg.$main."
		<tr>
		<td colspan=2><font color=#FFFFFF>成績分佈表</font></td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>100分 &nbsp;&nbsp;</td>
		$statistics_list_100
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>90~100分 &nbsp;&nbsp;</td>
		$statistics_list_90
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>80~ 90分 &nbsp;&nbsp;</td>
		$statistics_list_80
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>70~ 80分 &nbsp;&nbsp;</td>
		$statistics_list_70
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>60~ 70分 &nbsp;&nbsp;</td>
		$statistics_list_60
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>50~ 60分 &nbsp;&nbsp;</td>
		$statistics_list_50
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>40~ 50分 &nbsp;&nbsp;</td>
		$statistics_list_40
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>30~ 40分 &nbsp;&nbsp;</td>
		$statistics_list_30
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>20~ 30分 &nbsp;&nbsp;</td>
		$statistics_list_20
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>10~ 20分 &nbsp;&nbsp;</td>
		$statistics_list_10
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>0~ 10分 &nbsp;&nbsp;</td>
		$statistics_list_0
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor5>&nbsp;</td>
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		</table>";

	if ($friendly_print != 1) echo $main;
}

//結束主網頁顯示區
if ($friendly_print != 1) echo "</td></tr></table>";

//程式檔尾
if ($friendly_print != 1) foot();

?>
