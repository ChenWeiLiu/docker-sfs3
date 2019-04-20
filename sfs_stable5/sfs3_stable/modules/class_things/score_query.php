<?php
// $Id: score_query.php 5682 2009-10-13 07:13:13Z yjtzeng $
/*入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";

 //取得模組參數的類別設定
$m_arr = get_module_setup("score_manage_new");
extract($m_arr,EXTR_OVERWRITE);
$m_arr = get_module_setup("class_things");
extract($m_arr,EXTR_OVERWRITE);


//引入函數
include "../score_manage_new/my_fun.php";
include "./module-cfg.php";


//使用者認證
sfs_check();

$year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$use_rate=$_REQUEST['use_rate'];
$show_avg=$_REQUEST['show_avg'];
$show_tol_avg=$_REQUEST['show_tol_avg'];
$year_name=$_REQUEST['year_name'];
$me=$_REQUEST['me'];
if ($me && strlen($year_name)==1) $year_name.=sprintf("%02d",$me);
$stage=$_REQUEST['stage'];
$kind=$_REQUEST['kind'];
$friendly_print=$_REQUEST['friendly_print'];
$single_export=$_REQUEST['single_export'];
$h_count=$_REQUEST['h_count']?$_REQUEST['h_count']:2;

$print_asign=$_REQUEST['print_asign'];
$yorn=findyorn();
$save_csv=$_POST['save_csv'];
$sort_num=$_REQUEST['sort_num'];
$move_out=$_REQUEST['move_out'];
$print_special=$_REQUEST['print_special'];
$chk=$_REQUEST[chk];

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
if (empty($friendly_print) && empty($save_csv) && empty($single_export)) head("階段成績列表");

//列出橫向的連結選單模組
if (empty($friendly_print) && empty($save_csv) && empty($single_export)) print_menu($menu_p);

//設定主網頁顯示區的背景顏色
if (empty($friendly_print) && empty($save_csv) && empty($single_export)) echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc><tr><td>";

//取得學年學期陣列
$year_seme_arr = get_class_seme();
//新增一個下拉選單實例
$ss1 = new drop_select();
//下拉選單名稱
		//$ss1->s_name = "year_seme";
//提示字串
		//$ss1->top_option = "選擇學期";
//下拉選單預設值
	//$ss1->id = $year_seme;
//下拉選單陣列
		//$ss1->arr = $year_seme_arr;
//自動送出
		//$ss1->is_submit = true;
//傳回下拉選單字串
		//$year_seme_menu = $ss1->get_select();


$sel_year=substr($year_seme,0,3);
$sel_seme=substr($year_seme,-1);

$score_semester="score_semester_".intval($sel_year)."_".$sel_seme;
$teacher_sn=$_SESSION['session_tea_sn'];  //取得登入老師的id
//$class_year_menu=class_year_menu($sel_year,$sel_seme,$year_name);

/*
if($year_seme){
	$show_class_year = class_base($year_seme);
	$ss1->s_name ="year_name";
	$ss1->top_option = "選擇班級";
	$ss1->id = $year_name;
	$ss1->arr = $show_class_year;
	$ss1->is_submit = true;
	$class_year_menu =$ss1->get_select();
}
*/

//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);

$year_name=$class_name[0];

$c_year = substr($year_name,0,-2);
$c_name = substr($year_name,-2);

if($year_name)	$stage_menu=stage_menu($sel_year,$sel_seme,$c_year,$c_name,$stage);

if ($year_name && $stage) {
	if ($stage=='255') {
		$choice_kind[0]="全學期";
		$chart_kind="學期成績";
	} elseif ($yorn=='n') {
		if ($stage=="254") {	
			$kind="2";
			$choice_kind[0]="平時成績";
			$chart_kind="平時成績";
			$stage=1;
		} else {
			$kind="1";
			$choice_kind[0]="定期評量";
			$chart_kind="定期考查";
		}
	} else {
		$kind_menu=kind_menu($sel_year,$sel_seme,$c_year,$c_name,$stage,$kind);
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
	$tol_avg_checked=($show_tol_avg)?"checked":"";
	$asign_checked=($print_asign)?"checked":"";
	$special_checked=($print_special)?"checked":"";
	$move_checked=($move_out)?"checked":"";
	$sort_checked=($sort_num)?"checked":"";
	$snum=($sort_num)?".75":"1.5";
	$rate_menu="<input type='checkbox' name='use_rate' $rate_checked onclick='this.form.submit()';>加權";
	$avg_menu="<input type='checkbox' name='show_avg' $avg_checked onclick='this.form.submit()';>顯示各科平均";
	$tol_avg_menu="<input type='checkbox' name='show_tol_avg' $tol_avg_checked onclick='this.form.submit()';>顯示個人平均";
	$move_menu="<input type='checkbox' name='move_out' $move_checked onclick='this.form.submit()';>顯示調出及畢業學生";
	$asign_menu="<input type='checkbox' name='print_asign' $asign_checked onclick='this.form.submit()';>列印老師簽章欄";
	if ($sort_enable) $sort_menu="<input type='checkbox' name='sort_num' $sort_checked onclick='this.form.submit()';>列印名次";
	$special_menu="<input type='checkbox' name='print_special' $special_checked onclick='this.form.submit()';>套用排除名單";
}

$menu="<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	<table>
	<tr>
	<td>$year_seme_menu</td><td>$class_year_menu</td><td>$stage_menu</td><td>$kind_menu</td>
	</tr>
	</table>
	<table>
	<td>$rate_menu</td><td>$avg_menu</td><td>$tol_avg_menu</td><td>$move_menu</td><td>$asign_menu</td><td>$sort_menu</td><td>$special_menu</td>
	</table>";
if (empty($friendly_print) && empty($save_csv) && empty($single_export)) echo $menu;

//以上為選單bar

/******************************************************************************************/
if($year_seme && $year_name  && (($stage<250 && $kind) || $stage==255)){
    //取出本學年本學期的學校成績共通設定
	$sql="select * from score_setup where class_year=$c_year and year='$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql);
	$score_mode= $rs->fields['score_mode'];
	$test_ratio=explode("-",$rs->fields['test_ratio']);
	$sratio=$test_ratio[0];
	$nratio=$test_ratio[1];
	$pers=1;
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$class_id = sprintf("%03s_%s_%02s_%02s",$sel_year,$sel_seme,$c_year,$c_name);		
	//取得全班學生資料
	$cond=($move_out)?"":"and b.stud_study_cond='0'";

//----取得排除列表 student_sn (98.12.20)

	$all_stu_sn=array();
	$query="select * from score_manage_out where year='$sel_year' and semester='$sel_seme'";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$all_stu_sn[]=$res->fields['student_sn'];
		$res->MoveNext();
	}

	if (count($all_stu_sn)>0 && $print_special){
		$SpeAddSQL="and a.student_sn not in ('".implode("','",$all_stu_sn)."')";
	}else{
		$SpeAddSQL="";
	}
	unset($all_stu_sn);

//----end of 取得排除列表 student_sn (98.12.20)

	if ($_GET[ele]=="yes")
		$sql="select a.student_sn, b.stud_name, b.stud_id from elective_stu a, stud_base b where a.student_sn=b.student_sn and a.group_id='$me' $cond $SpeAddSQL order by b.curr_class_num";
	else
		$sql="select a.student_sn, b.stud_name, a.seme_num, b.stud_id from stud_seme a, stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$seme_year_seme' and a.seme_class='$year_name' $cond $SpeAddSQL order by a.seme_num";
	$rs=$CONN->Execute($sql);
	$all_sn="";
	$i=0;
	while (!$rs->EOF) {
		$sn=$rs->fields['student_sn'];
		$student_sn[$i]=$sn;
		$student_name[$sn]=$rs->fields['stud_name'];
		$student_sitenum[$sn]=($rs->fields['seme_num']>0)?$rs->fields['seme_num']:($i+1);
		$student_id[$sn] = $rs->fields['stud_id'];
		$all_sn.=$sn.",";
		$i++;
		$rs->MoveNext();
	}
	$all_sn=substr($all_sn,0,-1);
	//取得科目資料
	$query="select distinct b.ss_id from $score_semester a,score_ss b where a.test_sort='$stage' and a.ss_id=b.ss_id and a.student_sn in ($all_sn) and b.enable='1' order by b.sort,b.sub_sort";
	$res=$CONN->Execute($query);
	$i=0;
	while(!$res->EOF) {
		$ss_id[$i]=$res->fields[ss_id];
		$i++;
		$res->MoveNext();
	}
	//取得全班成績資料
	while (list($k,$v)=each($choice_kind)) {
		$sql="select a.*,b.rate from $score_semester a,score_ss b where a.test_sort='$stage' and a.test_kind='$v' and a.ss_id=b.ss_id and a.student_sn in ($all_sn) and b.enable='1' order by b.sort,b.sub_sort";
		//echo $sql."<br>";
		$rs=$CONN->Execute($sql);
		$ossid="";
		while(!$rs->EOF){
			$sn=$rs->fields["student_sn"];
			$ssid=$rs->fields["ss_id"];
			if ($ssid!=$ossid) {
				$i++;
				$ossid=$ssid;
				$rate[$ssid]=($use_rate)?$rs->fields["rate"]:1;
				if ($rate[$ssid]==100 && $use_rate) $pers=100;
			}
			$score=$rs->fields["score"];
			if($score==-100) $score="";
			$Sscore[$sn][$ssid][$rs->fields["test_kind"]]=$score;
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
       	if($kind=="4"){
       		$raw='rowspan="2"';
       		$col='colspan="2"';
		$subject_list2 = "<tr>";
		for($i=0;$i<count($ss_id);$i++){
			if ($friendly_print)
				$subject_list2.=($chk[$ss_id[$i]])?"<td width=33 style='mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top: 1.5pt solid windowtext; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm; text-align:center' height=\"39\" align=\"center\"><font size=\"2\" face=\"Dotum\">定期</font></td><td  width=33 style='mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top: 1.5pt solid windowtext; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm; text-align:center' height=\"39\" align=\"center\"><font size=\"2\" face=\"Dotum\">平時</font></td>":"";
			else
				$subject_list2.="<td  bgcolor='#FEE2FA' align='center'>定期</td><td bgcolor='#FEE2FA' align='center'>平時</td>";	
		}
		$subject_list2 .= "</tr>";	       		
       		//為解決定期；平時列消失問題，多一個變數來儲存
                //原始的$subject_list2在稍後的處理會在後方多加科目名稱
                //會造成顥示錯誤
                $subject_list4 = $subject_list2;
       	}	
       	for($i=0;$i<count($ss_id);$i++) $subs+=($chk[$ss_id[$i]])?1:0;
       	if ($subs==0) {
	       	for($i=0;$i<count($ss_id);$i++) $chk[$ss_id[$i]]=1;
       	}
       	for($i=0;$i<count($ss_id);$i++){
		$subject_name[$i]=ss_id_to_subject_name($ss_id[$i]);
		$rate_string=($use_rate)?"<br>*".$rate[$ss_id[$i]]:"";
		$chked=($chk[$ss_id[$i]])?"checked":"";
		if ($friendly_print)
			$subject_list.=($chked)?"<td $col width=33 style='mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top: 1.5pt solid windowtext; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm; text-align:center' height=\"39\" align=\"center\"><font size=\"2\" face=\"Dotum\">$subject_name[$i]".$rate_string."</font></td>":"";
		else if ($save_csv){
			if($kind=="4")
				$subject_list.=($chked)?$subject_name[$i]."定期,".$subject_name[$i]."平時,":"";
			else
				$subject_list.=($chked)?$subject_name[$i].",":"";
		} else {
			$subject_list.="<td $col align='center'><input type='checkbox' name='chk[".$ss_id[$i]."]' $chked onclick='this.form.submit()'><br>$subject_name[$i]".$rate_string."</td>";
			$subject_list2.="<td align='center'>$subject_name[$i]".$rate_string."</td>";
		}

		$rate_all+=($chked)?$rate[$ss_id[$i]]:0;
		for($j=0;$j<count($student_sn);$j++){
			$SS1[$j]=$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[0]];
			$SSav[$j]=number_format($SS1[$j],($SS1[$j]!=(int)$SS1[$j]));
			if ($friendly_print){
				if($kind=="4") 
					$score_list[$j].=($chked)?"<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">".number_format($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]],($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]]!=(int)$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]]))."</font></td><td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">".number_format($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]],($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]]!=(int)$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]]))."</font></td>":"";
				else		  		
					$score_list[$j].=($chked)?"<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">$SSav[$j]</font></td>":"";
			}
			else if ($save_csv){
				if($kind=="4") 
					$score_list[$j].=($chked)?number_format($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]],2).",".number_format($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]],($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]]!=(int)$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]])).",":"";
				else
					$score_list[$j].=($chked)?$SSav[$j].",":"";				
			}
			else{
				$bgcos=($chked)?"#ffffff":"#888888";
				if($kind=="4") 
					$score_list[$j].="<td align='right' bgcolor='$bgcos'>".number_format($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]],($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]]!=(int)$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]]))."</td><td align='right' bgcolor='$bgcos'>".number_format($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]],($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]]!=(int)$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]]))."</td>";
					
				else {
					$score_list[$j].="<td align='right' bgcolor='$bgcos'>$SSav[$j] &nbsp;</td>";
				}
			}
			$one_student_total[$j]+=($chked)?$SSav[$j]*$rate[$ss_id[$i]]:0;
			$statistics_average[$i]+=$SSav[$j];
			if($kind=="4"){
				$statistics_average1[$i]+=($chked)?number_format($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]],($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]]!=(int)$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[1]])):0;
				$statistics_average2[$i]+=($chked)?number_format($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]],($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]]!=(int)$Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[2]])):0;
			}	
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
		$statistics_total_average+=($chked)?$statistics_average[$i]*$rate[$ss_id[$i]]:0;
		if($kind=="4"){
			$statistics_average1[$i] = number_format($statistics_average1[$i]/count($student_sn),2);
			$statistics_average2[$i] = number_format($statistics_average2[$i]/count($student_sn),2);
			$statistics_total_average[1]+=$statistics_average1[$i]*$rate[$ss_id[$i]];
			$statistics_total_average[2]+=$statistics_average2[$i]*$rate[$ss_id[$i]];
		}		
		
		$standard_deviation[$i]=0;
		for ($j=0;$j<count($student_sn);$j++){	
			$standard_deviation[$i]+=pow(($Sscore[$student_sn[$j]][$ss_id[$i]][$choice_kind[0]]-$statistics_average[$i]),2);
		}
		$standard_deviation[$i]=number_format((sqrt($standard_deviation[$i]/count($student_sn))),2);
		if ($friendly_print) {
			if($kind=="4")
				$statistics_list_average.="<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">".$statistics_average1[$i]."</font></td><td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">".$statistics_average2[$i]."</font></td>";
			else				
				$statistics_list_average.=($chk[$ss_id[$i]])?"<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">$statistics_average[$i]</font></td>":"";
			$standard_deviation_list.=($chk[$ss_id[$i]])?"<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font size=\"2\" face=\"Dotum\">$standard_deviation[$i]</font></td>":"";
			if ($print_asign)
				$asign_col.=($chk[$ss_id[$i]])?"<td $col width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"></td>":"";
			
		}
		else if ($save_csv){
			if($kind=="4")
				$statistics_list_average.= $statistics_average1[$i].",".$statistics_average2[$i].",";
			else
				$statistics_list_average.= $statistics_average[$i].",";		
		}else {
			if($kind=="4")
				$statistics_list_average.="<td align='right'>".$statistics_average1[$i]."</td><td align='right'>".$statistics_average2[$i]."</td>";
			else
				$statistics_list_average.="<td align='right'>$statistics_average[$i]</td>";
			
			$standard_deviation_list.="<td align='right'>$standard_deviation[$i]</td>";
		}
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
	$statistics_total=number_format($statistics_total_average/$pers,2);
	$statistics_total_average=number_format($statistics_total_average/$rate_all,2);
	$many_ss=count($ss_id);
	
	$personal_score_list=array();
    for($i=0;$i<count($student_sn);$i++){
		$one_student_average[$i]=number_format(($one_student_total[$i]/$rate_all),2);
		$seniority[$i]=how_big($one_student_total[$i],$one_student_total);
		if ($friendly_print) {
			$student_and_score_list.="
				<tr>
				<td width=14 style='border-top:.75pt solid #000000; mso-border-top-alt: solid windowtext .75pt; border-left: 1.5pt solid windowtext; border-right: .75pt solid #000000; border-bottom: .75pt solid #000000; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font size=\"2\" face=\"Dotum\">".$student_sitenum[$student_sn[$i]]."</font></td>
				<td nowrap width=60 style='border-top:.75pt solid #000000; mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid #000000; border-bottom: .75pt solid #000000; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"center\"><font size=\"2\" face=\"Dotum\">".$student_name[$student_sn[$i]]."</font></td>
				$score_list[$i]
				<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: ".$snum."pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">".($one_student_total[$i]/$pers)."</font></td>";
			if ($show_tol_avg) $student_and_score_list.="<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: ".$snum."pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">$one_student_average[$i]</font></td>";
			if ($sort_num) $student_and_score_list.="<td width=14 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: 1.5pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">$seniority[$i]</font></td>";
			$student_and_score_list.="</tr>";
		} else if ($save_csv)
			$student_and_score_list.= $c_year."年".$c_name."班,".$student_id[$student_sn[$i]].",".$student_sitenum[$student_sn[$i]].",".$student_name[$student_sn[$i]].",".$score_list[$i].($one_student_total[$i]/$pers).",".$one_student_average[$i].",".$seniority[$i]."\n";		
		else {
			$student_no=$student_sitenum[$student_sn[$i]];			
			$personal_temp="
				<tr bgcolor=#ffffff>
				<td bgcolor=$bgcolor2 align='right'>$student_no &nbsp;</td>
				<td bgcolor=$bgcolor3 align='left'>&nbsp; ".$student_name[$student_sn[$i]]."</td>
				$score_list[$i]
				<td bgcolor=$bgcolor4 align='right'>".($one_student_total[$i]/$pers)." &nbsp;&nbsp;</td>";
			if ($show_tol_avg) $personal_temp.="<td bgcolor=$bgcolor5 align='right'>$one_student_average[$i]</td>";
			if(empty($single_export)) $personal_temp.="<td bgcolor=$bgcolor6 align='right'>$seniority[$i] &nbsp;&nbsp;</td></tr>";
			
			$student_and_score_list.=$personal_temp;
			//新開一個陣列，儲存個人成績表
			$personal_score_list[$student_no]=$personal_temp;	
	
		}
	}


	$print_msg=($stage)?"<input type='submit' name='friendly_print' value='友善列印'> <input type='submit' name='save_csv' value='匯出csv檔'>":""; 
	$print_msg.=($stage and $kind!='4')?" [<font size=2> 橫向人數:<input type='text' name='h_count' value='$h_count' size=1></font><input type='submit' name='single_export' value='列印個人成績單'>]":""; 
		
	
//	$print_msg=($stage)?"<input type='submit' name='friendly_print' value='友善列印'>&nbsp;&nbsp;&nbsp;<a href='{$_SERVER['PHP_SELF']}?year_seme=$year_seme&year_name=$year_name&stage=$stage&kind=$kind&use_rate=$use_rate&show_avg=$show_avg&print_asign=$print_asign&save_csv=1&chk=$chk'><b>匯出csv檔</b></a><br>":""; 
	if ($print_asign) {
		$asign_col="<tr style='height:30.0pt;mso-row-margin-left:1.4pt;mso-row-margin-right:1.4pt'>
				<td width=74 style='mso-border-top-alt: solid windowtext .75pt; border-left: 1.5pt solid windowtext; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\" colspan=\"2\"><font size=\"2\" face=\"Dotum\">各科老師<br>簽章</font></td>
				$asign_col";
		if ($show_tol_avg) $asign_col.="<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font size=\"2\" face=\"Dotum\">---</font></td>";
		$asign_col.="<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: ".$snum."pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font size=\"2\" face=\"Dotum\">---</font></td>";
		if ($sort_num) $asign_col.="<td width=14 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: 1.5pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font size=\"2\" face=\"Dotum\">-</font></td>";
		$asign_col.="</tr>";
	}
	if ($friendly_print) {
		$main="<table border=0 cellspacing=0 cellpadding=0 style='border-collapse: collapse; mso-padding-alt: 0cm 1.4pt 0cm 1.4pt; text-align:center' height=\"627\" width=\"617\">
			<tr style='height:30.0pt;mso-row-margin-left:1.4pt;mso-row-margin-right:1.4pt'>
			<td $raw width=14 style='border-left: 1.5pt solid windowtext; border-right: .75pt solid windowtext; border-top: 1.5pt solid windowtext; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"53\" align=\"center\"><font size=\"2\" face=\"Dotum\">座號</font></td>
			<td $raw width=60 style='mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: .75pt solid windowtext; border-top: 1.5pt solid windowtext; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"53\" align=\"center\"><p align=\"right\"><font size=\"2\" face=\"Dotum\">科目</font></p><p align=\"left\"><font size=\"2\" face=\"Dotum\">姓名</font></td>
			$subject_list
			<td $raw width=33 style='mso-border-left-alt: solid windowtext .75pt; border-left-width: medium; border-right: ".$snum."px solid #000000; border-top: 1.5pt solid #000000; border-bottom: .75pt solid #000000; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"39\" align=\"center\"><font size=\"2\" face=\"Dotum\">總分</font></td>";
		if ($show_tol_avg) $main.="<td $raw width=33 style='mso-border-left-alt: solid windowtext .75pt; border-left-width: medium; border-right: ".$snum."px solid #000000; border-top: 1.5pt solid #000000; border-bottom: .75pt solid #000000; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"39\" align=\"center\"><font size=\"2\" face=\"Dotum\">平均</font></td>";
		if ($sort_num) $main.="<td $raw width=14 style='mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: 1.5pt solid windowtext; border-top: 1.5pt solid windowtext; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"39\" align=\"center\"><font size=\"2\" face=\"Dotum\">名次</font></td>";
		$main.="</tr>"
			.$subject_list2.$student_and_score_list;
			if ($show_avg) $main.="
			<tr>
			<td width=74 style='border-top:.75pt solid #000000; mso-border-top-alt: solid windowtext .75pt; border-left: 1.5pt solid windowtext; border-right: .75pt solid #000000; border-bottom: .75pt solid #000000; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\" colspan=\"2\"><font size=\"2\" face=\"Dotum\">各科平均</font></td>
			$statistics_list_average
			<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: ".$snum."pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">$statistics_total</font></td>";
			if ($show_tol_avg) $main.="<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: ".$snum."pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">$statistics_total_average</font></td>";
		if ($show_avg && $sort_num) $main.="<td width=14 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: 1.5pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: .75pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font face=\"Dotum\" size=\"2\">-</font></td>";
		$main.="</tr>";
		if($kind!="4")	{
			$class_summary.="<table border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
			<tr style='height:30.0pt;mso-row-margin-left:1.4pt;mso-row-margin-right:1.4pt'>
			<td width=74 style='mso-border-top-alt: solid windowtext .75pt; border-left: 1.5pt solid windowtext; border-right: .75pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\" colspan=\"2\"><font size=\"2\" face=\"Dotum\">標準差</font></td>
			$standard_deviation_list
			<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: ".$snum."pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font size=\"2\" face=\"Dotum\">---</font></td>";
			if ($show_tol_avg) $class_summary.="<td width=33 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: ".$snum."pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font size=\"2\" face=\"Dotum\">---</font></td>";
			if ($sort_num) $class_summary.="<td width=14 style='mso-border-top-alt: solid windowtext .75pt; mso-border-left-alt: solid windowtext .75pt; border-left-style: none; border-left-width: medium; border-right: 1.5pt solid windowtext; border-top-style: none; border-top-width: medium; border-bottom: 1.5pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' height=\"15\" align=\"right\"><font size=\"2\" face=\"Dotum\">-</font></td>";
			$class_summary.="</tr></table>";
		}
		if ($print_asign) $main.=$asign_col;

	}else if($save_csv){
		$main = "班級,學號,座號,姓名,".$subject_list."總分,平均,名次\n".$student_and_score_list."\n";
	}	 
	else {
		$title="<tr bgcolor=$bgcolor3>
			<td colspan=2  align='center'>科目</td>
			$subject_list2
			<td width='50' align='center'>總分</td>";
			
		$personal_title="<tr bgcolor=$bgcolor3>
			<td width='35' $raw align='center'>座號</td>
			<td width='60' $raw align='center'>姓名</td>
			$subject_list2
			<td width='50' align='center'>總分</td>";
			
		$main="<table border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
			<tr bgcolor=$bgcolor1>
			<td width='35' $raw align='center'>座號</td>
			<td width='60' $raw align='center'>姓名</td>
			$subject_list
			<td width='50' $raw align='center'>總分</td>";
			
		//判斷是否顯示個人平均
		if ($show_tol_avg){ 
			$main.="<td width='50' $raw align='center'>平均</td>
				<td width='40' $raw align='center'>名次</td>";
				$title.="<td width='50' $raw align='center'>平均</td>";
				$personal_title.="<td width='50' $raw align='center'>平均</td>";
				}
                //加入$subject_list4變數以解決缺少定期平時列
		$main.="</tr>".$subject_list4.$student_and_score_list;
		if ($show_avg ) {
			$class_summary="
			<tr bgcolor=$bgcolor1>
			<td colspan='2' align='center'>班級平均</td>
			$statistics_list_average
			<td width='50' align='right'>$statistics_total &nbsp;&nbsp;</td>";
			//判斷是否顯示個人平均
			if ($show_tol_avg) $class_summary.="<td width='40'>&nbsp;$statistics_total_average</td>";
			$main.="</tr>";
		}
		if($kind!="4") {
			$class_summary.="		
			<tr bgcolor=$bgcolor1>
			<td colspan='2' align='center'>班級標準差</td>
			$standard_deviation_list
			<td width='50' align='right'>&nbsp;</td>";
			//if ($show_tol_avg) $class_summary.="<td width='40'>&nbsp;</td>";
			$class_summary.="<td width='40'>&nbsp;</td></tr>";
		}
	}
	$school_title=score_head($sel_year,$sel_seme,$c_year,$c_name,$stage,$chart_kind);
	if ($friendly_print) {
		$today=date("Y-m-d",mktime (0,0,0,date("m"),date("d"),date("Y")));
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\"><title>成績單</title></head>
			<SCRIPT LANGUAGE=\"JavaScript\">
			<!--
			function pp() {   
				if (window.confirm('開始列印？')){
				self.print();}
			}
			//-->
			</SCRIPT>			
			<body onload=\"pp();return true;\">
			<table border=0 cellspacing=0 cellpadding=0 style='border-collapse: collapse; mso-padding-alt: 0cm 1.4pt 0cm 1.4pt' width=\"618\">
			<tr>
			<td width=612 valign=top style='padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
			<p class=MsoNormal align=center style='text-align:center'><b>".$school_title."</b><span style=\"font-family: 新細明體; mso-ascii-font-family: Times New Roman; mso-hansi-font-family: Times New Roman\">&nbsp;&nbsp;&nbsp; </span></p>
			<p class=MsoNormal align=right><span style=\"font-family: 新細明體; mso-ascii-font-family: Times New Roman; mso-hansi-font-family: Times New Roman\">
			<font size=\"1\">列印日期：$today</font></span></p>".$main."</table></td></tr></table></body></html>";
	}else if($save_csv){
		$filename = $year_seme."_".$c_year."_".$c_name."_stagescore.csv";
   		
		header("Content-type: text/x-csv ; Charset=Big5");
		header("Content-disposition:attachment ; filename=$filename");
		//header("Pragma: no-cache");
						//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

		header("Expires: 0");
		echo $school_title."\n".$main;
	}	
	
	$main=$print_msg.$main;
	if($kind!="4") {
		//$main.="
		$spread="<table border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
		{{column_title}} $class_summary		
		<tr>
		<td colspan='2' align='center'>成績分佈表</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>100分 &nbsp;&nbsp;</td>
		$statistics_list_100
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>90~99分 &nbsp;&nbsp;</td>
		$statistics_list_90
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>80~ 89分 &nbsp;&nbsp;</td>
		$statistics_list_80
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>70~ 79分 &nbsp;&nbsp;</td>
		$statistics_list_70
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>60~ 69分 &nbsp;&nbsp;</td>
		$statistics_list_60
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>50~ 59分 &nbsp;&nbsp;</td>
		$statistics_list_50
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>40~ 49分 &nbsp;&nbsp;</td>
		$statistics_list_40
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>30~ 39分 &nbsp;&nbsp;</td>
		$statistics_list_30
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>20~ 29分 &nbsp;&nbsp;</td>
		$statistics_list_20
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>10~ 19分 &nbsp;&nbsp;</td>
		$statistics_list_10
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="
		<td width='40' bgcolor=$bgcolor6>&nbsp;</td>
		</tr>
		<tr>
		<td colspan='2' bgcolor=$bgcolor3 align='right'>0~ 9分 &nbsp;&nbsp;</td>
		$statistics_list_0
		<td width='40' bgcolor=$bgcolor4>&nbsp;</td>";
		//if ($show_tol_avg) $spread.="<td width='40' bgcolor=$bgcolor5>&nbsp;</td>";
		$spread.="</tr></table>";
		
		$spread_personal=$spread;
		$spread_personal=str_replace("{{column_title}}","$personal_title {{personal_score_list}}",$spread_personal);
		
		$spread=str_replace("{{column_title}}",$title,$spread);
		
		
		$main.="$class_summary</tr></table>";
	}

	//換頁html碼
	if($single_export){
		$newpage="<P STYLE='page-break-before: always;'>";	
		//$h_count=3;
		$h_status=1;
		$school_title_fixed=str_replace($school_long_name,$school_long_name."<BR>",$school_title);
		foreach($personal_score_list as $key=>$value) {
			$mydata=$spread_personal;
			$mydata=str_replace("{{personal_score_list}}",$value,$mydata);
			//表格化包裝
			$single_data="<table><tr><td align='center'>$school_title_fixed</td></tr><tr><td align='center'>$mydata</td></tr></table>";
			
			$h_data.="<td>$single_data</td><td></td><td></td><td></td>";
	
			if($h_status==$h_count) {
				$h_data="<table><tr>$h_data</tr></table>";
				echo "$h_data $newpage";
				$h_data='';
				$h_status=0;
			}
			$h_status++;
		}
		if($h_data) echo "<table><tr>$h_data</tr></table>";
		
	} else if (empty($friendly_print) && empty($save_csv) && empty($single_export)) echo "<table><tr valign='top'><td>$main</td><td>$spread</td></tr></table>";
}

//結束主網頁顯示區
if (empty($friendly_print) && empty($save_csv) && empty($single_export)) echo "</td></tr></table></form></tr></table>";

//程式檔尾
if (empty($friendly_print) && empty($save_csv) && empty($single_export)) foot();
?>
