<?php
// $Id: ask_export3.php 7780 2013-11-21 05:13:36Z infodaes $

include "config.php";
include "../../include/sfs_case_score.php";
sfs_check();

//學校資訊
$school_id=$SCHOOL_BASE["sch_id"];
$school_tel=$SCHOOL_BASE["sch_phone"];
$school_fax=$SCHOOL_BASE["sch_fax"];

//依教育部代碼判定年制別
$school_yg=$SCHOOL_BASE["sch_id"][3];
switch ($school_yg) {
  case 0: $school_yg='四'; break;
  case 1: $school_yg='四'; break;
  case 2: $school_yg='二'; break;
  case 3: $school_yg='三'; break;
  case 4: $school_yg='三'; break;
  case 5: $school_yg='三'; break;
  case 6: $school_yg='六'; break;
  case 7: $school_yg='六'; break;
  case 8: $school_yg='六'; break;
  default: $school_yg='';
}

//$school_area=$SCHOOL_BASE["sch_sheng"]."政府";

//今天的日期
$today=(date("Y")-1911).date("年m月d日");

//學期別
$work_year_seme= ($_POST[work_year_seme])?$_POST[work_year_seme]:$_GET[work_year_seme];
if($work_year_seme=='')        $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$work_year=substr($work_year_seme,0,3)+0;

//取得前一學期的代號
$seme_list=get_class_seme();
$seme_key_list=array_keys($seme_list);
$pre_seme=$seme_key_list[(array_search($work_year_seme,$seme_key_list))+1];
$seme_array=array($pre_seme);
$sn_array=array();

//列高與列數
$height= ($_POST[height])?$_POST[height]:$_GET[height];
if($height=="") $height=33;

$rows= ($_POST[rows])?$_POST[rows]:$_GET[rows];
if($rows=="") $rows=20;

//換頁html碼
$newpage="<P STYLE='page-break-before: always;'>";


//簽章列
$sign="承辦人：　　　　　出納： 　　　　　會計： 　　　　主任：　　　　　　校長：";

// 取出班級陣列
$class_base = class_base($work_year_seme);
//$class_teacher=get_class_teacher();

//取得學年學期陣列
$year_seme_arr = get_class_seme();

//取得學生基本資料
$sql_select="select a.student_sn,left(a.class_num,length(a.class_num)-2) as class_id,a.dollar,b.stud_id,b.stud_name,b.stud_person_id,b.stud_sex,b.stud_tel_1,b.stud_addr_1,b.stud_birthday,c.guardian_name,c.fath_education,c.moth_education,c.fath_occupation,c.moth_occupation,c.guardian_p_id,c.guardian_address from grant_aid a,stud_base b,stud_domicile c where a.year_seme='$work_year_seme' AND a.type='$type' AND a.student_sn=b.student_sn AND a.student_sn = c.student_sn order by class_num";
$res=$CONN->Execute($sql_select) or user_error("身分別紀錄讀取失敗！<br>$sql_select",256);
$student_arr=array();
while(!$res->EOF) {
	$student_sn=$res->fields['student_sn'];
	
	$student_arr[$student_sn]['class_id']=$res->fields['class_id'];
	$student_arr[$student_sn]['stud_id']=$res->fields['stud_id'];
	$student_arr[$student_sn]['stud_name']=$res->fields['stud_name'];
	$student_arr[$student_sn]['stud_person_id']=$res->fields['stud_person_id'];
	$student_arr[$student_sn]['dollar']=$res->fields['dollar'];
	$student_arr[$student_sn]['stud_sex']=$res->fields['stud_sex'];
	$student_arr[$student_sn]['stud_tel_1']=$res->fields['stud_tel_1'];
	$student_arr[$student_sn]['stud_addr_1']=$res->fields['stud_addr_1'];
	$student_arr[$student_sn]['fath_education']=$res->fields['fath_education'];
	$student_arr[$student_sn]['moth_education']=$res->fields['moth_education'];
	$student_arr[$student_sn]['fath_occupation']=$res->fields['fath_occupation'];
	$student_arr[$student_sn]['moth_occupation']=$res->fields['moth_occupation'];
	$student_arr[$student_sn]['stud_birthday']=$res->fields['stud_birthday'];
	$student_arr[$student_sn]['guardian_name']=$res->fields['guardian_name'];
	$student_arr[$student_sn]['guardian_p_id']=$res->fields['guardian_p_id'];
	$student_arr[$student_sn]['guardian_address']=$res->fields['guardian_address'];


	$res->MoveNext();
}

//加入類別屬性資料
$sql_select="select student_sn,clan,area from stud_subkind where type_id='".$target_id[$type]."'";
$res=$CONN->Execute($sql_select) or user_error("身分屬性讀取失敗！<br>$sql_select",256);
while(!$res->EOF) {
	$student_sn=$res->fields['student_sn'];
	if(array_key_exists($student_sn,$student_arr)){	
		$student_arr[$student_sn]['clan']=$res->fields['clan'];
		$student_arr[$student_sn]['area']=$res->fields['area'];
	}
	$res->MoveNext();
}

//echo '<PRE>';
//print_r($student_arr);
//echo '</PRE>';

$student_arr_len=count($student_arr);

$data="<center><font size='5' face='標楷體'>[表二]    財團法人臺灣學產基金會設置清寒學生助學金學生名冊</font><BR><BR>
  <p>學校代碼：[ $school_id ] 　　學校名稱：[ $school_long_name ] 　　　申請年度：[ $work_year ]
<table border='1' width='90%' cellspacing='0' cellpadding='0' bordercolordark='#008000' bordercolorlight='#008000'>
<tr><td align=center bgcolor=$hint_color>編號</td>
        <td align=center bgcolor=$hint_color>姓名</td>
        <td align=center bgcolor=$hint_color>身分證統一編號</td>
        <td align=center bgcolor=$hint_color>年制</td>
        <td align=center bgcolor=$hint_color>班級</td>
        <td align=center bgcolor=$hint_color>學習領域</td>
        <td align=center bgcolor=$hint_color>備　　　　　　　註</td></tr>";

foreach($student_arr as $key=>$value){
	$class_id=$value['class_id'];
	$stud_name=$value['stud_name'];
	$stud_id=$value['stud_id'];
	$stud_person_id=$value['stud_person_id'];
	$stud_sex=$value['stud_sex'];
	$stud_tel_1=$value['stud_tel_1'];
	$stud_addr_1=$value['stud_addr_1'];
	$fath_education=$value['fath_education'];
	$moth_education=$value['moth_education'];
	$fath_occupation=$value['fath_occupation'];
	$moth_occupation=$value['moth_occupation'];
	//$stud_birthday=$value['stud_birthday'];
	$stud_birthday=$value['stud_birthday'];
	//.'年'.date('m',$value['stud_birthday']).'月'.date('d',$value['stud_birthday']).'日';
	$guardian_name=$value['guardian_name'];
	$guardian_p_id=$value['guardian_p_id'];
	$guardian_address=$value['guardian_address'];

	$clan=$value['clan'];
	$area=$value['area'];
	$dollar=$value['dollar'];
	$student_sn=$key;
	$no=$no+1;
	$num_count++;
        $total=$total+$dollar;
        //取得上學期成績
        $sn_array[0]=$student_sn;
        $sub_score=cal_fin_score($sn_array,$seme_array);
        $nor_score=cal_fin_nor_score($sn_array,$seme_array);
        //準備名冊
        $data.="<tr>
        <td align=center>$num_count</td>
        <td align=center>$stud_name</td>
        <td align=center>$stud_person_id</td>
        <td align=center>$school_yg</td>
        <td align=center>$class_base[$class_id]</td>
        <td align=center>{$sub_score[$student_sn][avg][score]}</td>
        <td>[ A ]</td>
        </tr>";
$main.="<style>
<!--
 table.MsoNormalTable
	{mso-style-parent:'';
	font-size:10.0pt;
	font-family:'Times New Roman';
	}
 p.MsoNormal
	{mso-style-parent:'';
	margin-bottom:.0001pt;
	font-size:12.0pt;
	font-family:'Times New Roman';
	margin-left:0cm; margin-right:0cm; margin-top:0cm}
-->
</style>
<table class='MsoNormalTable' border='1' cellspacing='0' cellpadding='0' style='border-collapse: collapse; border: medium none' id='table1'>
	<tr style='page-break-inside: avoid; height: 1.0cm'>
		<td width='611' colspan='9' style='width: 457.95pt; height: 1.0cm; border-left: 1.0pt solid windowtext; border-right: medium none; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' style='text-align: justify; text-justify: inter-ideograph; line-height: 16.0pt; layout-grid-mode: char'>
		<span lang='EN-US' style='font-size: 14.0pt; font-family: 細明體'>[</span><span style='font-size: 14.0pt; font-family: 細明體'>表一</span><span lang='EN-US' style='font-size: 14.0pt'>]&nbsp;&nbsp;&nbsp;
		</span><span style='font-size: 14.0pt; font-family: 新細明體'>教育部學產基金</span><span style='font-size: 14.0pt; font-family: 細明體'>低收入</span><span style='font-size: 14.0pt; font-family: 新細明體'>戶學生助學金</span><span style='font-size: 14.0pt; font-family: 細明體'>申請書</span></td>
		<td width='324' colspan='4' style='width: 242.85pt; height: 1.0cm; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-family: 細明體'>※申請日期：</span>$today</td>
	</tr>
	<tr style='page-break-inside: avoid; height: 14.25pt'>
		<td width='34' rowspan='2' style='width: 25.4pt; height: 14.25pt; border-left: 1.0pt solid windowtext; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>就</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>讀</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>學</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>校</span></td>
		<td width='278' colspan='3' style='width: 208.15pt; height: 14.25pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center; margin-left: 36.0pt; margin-right: 36.0pt; margin-top: 0cm; margin-bottom: .0001pt'>
		<span lang='EN-US' style='font-size: 10.0pt; font-family: 細明體'>(</span><span style='font-size: 10.0pt; font-family: 細明體'>學校全銜<span lang='EN-US'>)</span></span></td>
		<td width='58' style='width: 43.85pt; height: 14.25pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>年制別</span></td>
		<td style='width: 136px; height: 14.25pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' colspan='2'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>年級/科系</span></td>
		<td width='24' rowspan='4' style='width: 18.15pt; height: 14.25pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>前</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>學</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>期</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>成</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>績</span></td>
		<td width='210' colspan='2' rowspan='2' style='width: 157.5pt; height: 14.25pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>學業</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span lang='EN-US' style='font-size: 8.0pt; font-family: 細明體'>(</span><span style='font-size: 8.0pt; font-family: 細明體'>學習領域<span lang='EN-US'>)</span></span></td>
		<td width='30' rowspan='2' style='width: 22.35pt; height: 14.25pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>校</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>長</span></td>
		<td width='147' rowspan='2' style='width: 109.9pt; height: 14.25pt; border-left: medium none; border-right: medium none; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' style='text-align: justify; text-justify: inter-ideograph'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='18' rowspan='2' style='width: 13.5pt; height: 14.25pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='right' style='text-align: right'>
		<span style='font-size: 8.0pt; font-family: 細明體'>簽章</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 36.4pt'>
		<td width='278' colspan='3' style='width: 208.15pt; height: 36.4pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span lang='en-us'>{$school_long_name}</span></td>
		<td width='58' style='width: 43.85pt; height: 36.4pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center'>
		<span lang='EN-US'>{$school_yg}</span></td>
		<td style='width: 136px; height: 36.4pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' colspan='2'>
		<p align='center' class='MsoNormal'>{$class_base[$class_id]}</td>
	</tr>
	<tr style='page-break-inside: avoid; height: 16.9pt'>
		<td width='34' rowspan='2' style='width: 25.4pt; height: 16.9pt; border-left: 1.0pt solid windowtext; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>申</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>請</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>人</span></td>
		<td width='88' style='width: 66.0pt; height: 16.9pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>姓名</span></td>
		<td width='96' style='width: 72.0pt; height: 16.9pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 9.0pt; font-family: 細明體; color: red'>身分證統一編號</span></td>
		<td width='94' style='width: 70.15pt; height: 16.9pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>出生年月日</span></td>
		<td width='98' colspan='2' style='width: 73.85pt; height: 16.9pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>電話</span></td>
		<td width='96' style='width: 72.0pt; height: 16.9pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>學生簽章</span></td>
		<td width='210' colspan='2' rowspan='2' style='width: 157.5pt; height: 16.9pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span lang='EN-US'>{$sub_score[$student_sn][avg][score]}</span></td>
		<td width='30' rowspan='2' style='width: 22.35pt; height: 16.9pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>承辦單位</span></p>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>主管</span></td>
		<td width='147' rowspan='2' style='width: 109.9pt; height: 16.9pt; border-left: medium none; border-right: medium none; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' style='text-align: justify; text-justify: inter-ideograph'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='18' rowspan='2' style='width: 13.5pt; height: 16.9pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='right' style='text-align: right'>
		<span style='font-size: 8.0pt; font-family: 細明體'>簽章</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 42.15pt'>
		<td width='88' style='width: 66.0pt; height: 42.15pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span lang='EN-US'>{$stud_name}</span></td>
		<td width='96' style='width: 72.0pt; height: 42.15pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span lang='EN-US'>{$stud_person_id}</span></td>
		<td width='94' style='width: 70.15pt; height: 42.15pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span lang='EN-US'>{$stud_birthday}</span></td>
		<td width='98' colspan='2' style='width: 73.85pt; height: 42.15pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span lang='EN-US'>{$stud_tel_1}</span></td>
		<td width='96' style='width: 72.0pt; height: 42.15pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align: center'>
		<span lang='EN-US'>&nbsp;</span></td>
	</tr>
</table>
<p class='MsoNormal' align='center'>　</p>
<table class='MsoNormalTable' border='0' cellspacing='0' cellpadding='0' style='border-collapse: collapse' id='table2' height='401'>
	<tr style='page-break-inside: avoid; height: 16.5pt'>
		<td width='58' colspan='2' style='width: 43.4pt; height: 16.5pt; border: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>低收入</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>戶長姓名</span></td>
		<td width='160' colspan='3' style='width: 120.0pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>{$guardian_name}</span></td>
		<td width='72' colspan='2' style='width: 54.0pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span style='font-size: 8.0pt; font-family: 細明體; color: red'>戶長身分證統一編號</span></td>
		<td width='120' colspan='2' style='width: 90.0pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>{$guardian_p_id}</span></td>
		<td width='32' colspan='2' rowspan='2' style='width: 24.0pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>居住</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>現況</span></td>
		<td width='64' rowspan='2' style='width: 48.0pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span style='font-size: 8.0pt; font-family: 細明體'>□租屋</span></p>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span style='font-size: 8.0pt; font-family: 細明體'>□自有房屋</span></td>
		<td width='24' rowspan='10' style='width: 18.15pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>學校審查意見</span></td>
		<td width='210' rowspan='10' valign='top' style='width: 157.5pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span style='font-size: 8.0pt; font-family: 細明體'>一、清寒條件：</span></p>
		<p class='MsoNormal' style='text-align: justify; text-justify: inter-ideograph; text-indent: -36.0pt; margin-left: 60.0pt'>
		<span lang='EN-US' style='font-size: 8.0pt'>（A）<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</span></span><span style='font-size: 8.0pt; font-family: 細明體'>
		□持低收入戶證明者。</span></p>
		<p class='MsoNormal' style='margin-left:30.05pt;text-align:justify;text-justify:
  inter-ideograph;text-indent:-.15pt'>
		<span lang='EN-US' style='font-size: 8.0pt; font-family: 細明體'>[</span><span style='font-size: 8.0pt; font-family: 細明體'>本證明文件須與本申請書一同寄送到承辦學校<span lang='EN-US'>]</span></span></p>
		<p class='MsoNormal' style='text-align: justify; text-justify: inter-ideograph; text-indent: -36.0pt; margin-left: 60.0pt'>
		<span lang='EN-US' style='font-size: 8.0pt'>（B）<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</span></span><span style='font-size: 8.0pt; font-family: 細明體'>
		□同時具有原住民身份。</span></p>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span lang='EN-US' style='font-size: 8.0pt; font-family: 細明體'>&nbsp;</span></p>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span style='font-size: 8.0pt; font-family: 細明體'>二、</span><b><u><span style='font-size:10.0pt;font-family:標楷體;color:red'>未申請政府發給之其它獎助學金及學雜費減免，不包含政府發給之低收入學生學雜費減免，若有偽造不實情事，願負法律責任並繳回助學金。</span></u></b></p>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span lang='EN-US' style='font-size: 8.0pt'>&nbsp;</span></p>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span lang='EN-US' style='font-size: 8.0pt'>&nbsp;</span></p>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span style='font-size: 8.0pt; font-family: 細明體'>三、學校初審小組審查決議：</span></p>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span lang='EN-US' style='font-size: 8.0pt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
		<span style='font-size: 8.0pt; font-family: 細明體'>□合格</span></p>
		<p class='MsoNormal' style='text-align:justify;text-justify:inter-ideograph'>
		<span lang='EN-US' style='font-size: 8.0pt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
		<span style='font-size: 8.0pt; font-family: 細明體'>□不合格</span></td>
		<td width='15' rowspan='10' style='width: 11.15pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>承</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US' style='font-size: 8.0pt; font-family: 細明體'>&nbsp;</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US' style='font-size: 8.0pt; font-family: 細明體'>&nbsp;</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US' style='font-size: 8.0pt; font-family: 細明體'>&nbsp;</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US' style='font-size: 8.0pt; font-family: 細明體'>&nbsp;</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>辦</span></td>
		<td width='15' rowspan='3' style='width: 11.2pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' align='center'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>單</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>位</span></td>
		<td width='147' rowspan='3' style='width: 109.9pt; height: 16.5pt; border-left: medium none; border-right: medium none; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='18' rowspan='3' style='width: 13.5pt; height: 16.5pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='right' style='text-align:right'>
		<span style='font-size: 8.0pt; font-family: 細明體'>處室</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 26.25pt'>
		<td width='58' colspan='2' style='width: 43.4pt; height: 26.25pt; border-left: 1.0pt solid windowtext; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>聯絡地址</span></td>
		<td width='352' colspan='7' style='width: 264.0pt; height: 26.25pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>{$guardian_address}</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 17.75pt'>
		<td width='34' rowspan='8' style='width: 25.45pt; height: 17.75pt; border-left: 1.0pt solid windowtext; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: medium none; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>家</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>庭</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>狀</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>況</span></td>
		<td width='40' colspan='2' rowspan='2' style='width: 29.95pt; height: 17.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>親屬</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>稱謂</span></td>
		<td width='96' rowspan='2' style='width: 72.0pt; height: 17.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>姓名</span></td>
		<td width='48' rowspan='2' style='width: 36.0pt; height: 17.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>存歿</span></td>
		<td width='40' rowspan='2' style='width: 30.0pt; height: 17.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>年齡</span></td>
		<td width='248' colspan='6' style='width: 186.0pt; height: 17.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>健康狀況</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 17.3pt'>
		<td width='80' colspan='2' style='width: 60.0pt; height: 17.3pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>正常</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 17.3pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>疾病</span></td>
		<td width='88' colspan='2' style='width: 66.0pt; height: 17.3pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體; color: red'>身心障礙</span></td>
		<td width='15' rowspan='4' style='width: 11.2pt; height: 17.3pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: medium none; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' align='center'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>人</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>員</span></td>
		<td width='147' rowspan='4' style='width: 109.9pt; height: 17.3pt; border: medium none; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='18' rowspan='4' style='width: 13.5pt; height: 17.3pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: medium none; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>簽章</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 23.05pt'>
		<td width='40' colspan='2' style='width: 29.95pt; height: 23.05pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='96' style='width: 72.0pt; height: 23.05pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='48' style='width: 36.0pt; height: 23.05pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='40' style='width: 30.0pt; height: 23.05pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 23.05pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 23.05pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='88' colspan='2' style='width: 66.0pt; height: 23.05pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 25.95pt'>
		<td width='40' colspan='2' style='width: 29.95pt; height: 25.95pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='96' style='width: 72.0pt; height: 25.95pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='48' style='width: 36.0pt; height: 25.95pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='40' style='width: 30.0pt; height: 25.95pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 25.95pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 25.95pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='88' colspan='2' style='width: 66.0pt; height: 25.95pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 21.75pt'>
		<td width='40' colspan='2' style='width: 29.95pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='96' style='width: 72.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='48' style='width: 36.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='40' style='width: 30.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='88' colspan='2' style='width: 66.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 21.75pt'>
		<td width='40' colspan='2' style='width: 29.95pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='96' style='width: 72.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='48' style='width: 36.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='40' style='width: 30.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='88' colspan='2' style='width: 66.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='15' rowspan='3' style='width: 11.2pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm' align='center'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>聯</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>絡</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 8.0pt; font-family: 細明體'>電話</span></td>
		<td width='165' colspan='2' rowspan='3' style='width: 123.4pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: 1.0pt solid windowtext; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 21.75pt'>
		<td width='40' colspan='2' style='width: 29.95pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='96' style='width: 72.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='48' style='width: 36.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='40' style='width: 30.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 21.75pt; border-left: medium none; border-right: medium none; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='88' colspan='2' style='width: 66.0pt; height: 21.75pt; border-left: 1.0pt solid windowtext; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 21.75pt'>
		<td width='40' colspan='2' style='width: 29.95pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='96' style='width: 72.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='48' style='width: 36.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='40' style='width: 30.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 21.75pt; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='80' colspan='2' style='width: 60.0pt; height: 21.75pt; border-left: medium none; border-right: medium none; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
		<td width='88' colspan='2' style='width: 66.0pt; height: 21.75pt; border-left: 1.0pt solid windowtext; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span lang='EN-US'>&nbsp;</span></td>
	</tr>
	<tr style='page-break-inside: avoid; height: 94.45pt'>
		<td width='34' style='width: 25.45pt; height: 114px; border: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm; background: silver'>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>注</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>意</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>事</span></p>
		<p class='MsoNormal' align='center' style='text-align:center'>
		<span style='font-size: 10.0pt; font-family: 細明體'>項</span></td>
		<td width='900' colspan='17' style='width: 675.35pt; height: 113px; border-left: medium none; border-right: 1.0pt solid windowtext; border-top: medium none; border-bottom: 1.0pt solid windowtext; padding-left: 1.4pt; padding-right: 1.4pt; padding-top: 0cm; padding-bottom: 0cm'>
		<p class='MsoNormal' style='margin-top:0cm;margin-right:3.0pt;margin-bottom:
  0cm;margin-left:24.95pt;margin-bottom:.0001pt;text-align:justify;text-justify:
  inter-ideograph;text-indent:-20.5pt'>
		<span style='font-size: 10.0pt; font-family: 細明體'>一、上表各欄，辦理手續不完備者概不受理，<span style='color:red'>申請者不得異議</span>，家庭狀況之親屬如不足填載可加浮籤。</span></p>
		<p class='MsoNormal' style='margin-top:0cm;margin-right:3.0pt;margin-bottom:
  0cm;margin-left:24.95pt;margin-bottom:.0001pt;text-align:justify;text-justify:
  inter-ideograph;text-indent:-20.5pt'>
		<span style='font-size: 10.0pt; font-family: 細明體'>二、申請條件：本</span><span lang='EN-US' style='font-size: 10.0pt; color: blue'>({$work_year}</span><span lang='EN-US' style='font-size: 10.0pt'>)</span><span style='font-size: 10.0pt; font-family: 細明體'>學年度第</span><span lang='EN-US' style='font-size: 10.0pt'>1</span><span style='font-size: 10.0pt; font-family: 細明體'>學期，<span style='color:red'>僅</span>限低收入戶</span><span lang='EN-US' style='font-size: 10.0pt'>(</span><span style='font-size: 10.0pt; font-family: 細明體'>不包括中低收入戶</span><span lang='EN-US' style='font-size: 10.0pt'>)</span><span style='font-size: 10.0pt; font-family: 細明體'>身分，且前學期成績高中職以上學業成績<span style='color:red'>總平均六十分</span>以上（且德行評量無小過以上之處分）。國民中學：學習領域評量成績總計平均六十分以上，且日常生活表現評量無小過以上之處分。國民小學：學習領域評量成績總計平均六十分以上。一年級新生上學期免審核成績。</span></p>
		<p class='MsoNormal' style='margin-top:0cm;margin-right:3.0pt;margin-bottom:
  0cm;margin-left:24.95pt;margin-bottom:.0001pt;text-align:justify;text-justify:
  inter-ideograph;text-indent:-20.5pt'>
		<span style='font-size: 10.0pt; font-family: 細明體'>三、申請方式：每學期開學初，依就讀學校公<span style='color:red'>布申請</span>期限，詳填申請書並檢附有效之收入戶證明，向學校提出申請。</span></p>
		<p class='MsoNormal' style='margin-top:0cm;margin-right:3.0pt;margin-bottom:
  0cm;margin-left:24.95pt;margin-bottom:.0001pt;text-align:justify;text-justify:
  inter-ideograph;text-indent:-20.5pt'>
		<span style='font-size: 10.0pt; font-family: 細明體'>
		四、低收入戶證明（若為影本請加蓋學校承辦人員印章）中若未列出申請學生資料時，請提供戶口名簿或戶籍謄本。</span></p>
		<p class='MsoNormal' style='margin-top:0cm;margin-right:3.0pt;margin-bottom:
  0cm;margin-left:24.95pt;margin-bottom:.0001pt;text-align:justify;text-justify:
  inter-ideograph;text-indent:-20.5pt'>
		<span style='font-size: 10.0pt; font-family: 細明體'>
		五、審查結果經核定發給助學金者，如於學期結束前尚未被通知領取，請洽各校承辦人員查詢。</span></td>
	</tr>
</table>
";

       if($no<>$student_arr_len) $main.=$newpage;
}
//全校學生數
$sql_select="select count(*) from stud_base where stud_study_cond in (0,15)";
$res=$CONN->Execute($sql_select) or user_error("全校學生數讀取失敗！<br>$sql_select",256);
$total=$res->fields[0];
$data.="</table><BR>全校學生數：[$total]人。選送人數  低收入戶：[ $num_count ]人。　　　　　申請日期：[ $today ]</CENTER>";
$main.=$newpage.$data."";

echo $main;
echo "\n<script language=\"Javascript\"> alert (\"本報表預設印表格式為A4橫印，印表前請記得設定喔！\")</script>";
?>
