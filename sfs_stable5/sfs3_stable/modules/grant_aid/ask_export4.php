<?php
// $Id: ask_export4.php 7132 2013-02-21 07:56:52Z infodaes $

include "config.php";
include "../../include/sfs_case_score.php";
sfs_check();

//學校資訊
$school_id=$SCHOOL_BASE["sch_id"];
$school_tel=$SCHOOL_BASE["sch_phone"];
$school_fax=$SCHOOL_BASE["sch_fax"];
$school_add=$SCHOOL_BASE["sch_addr"];
$school_area=$SCHOOL_BASE["sch_sheng"];

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
$sql_select="select a.student_sn,left(a.class_num,length(a.class_num)-2) as class_id,a.dollar,b.stud_id,b.stud_name,b.stud_person_id,b.stud_sex,b.stud_tel_1,b.stud_addr_1,c.fath_education,c.moth_education,c.fath_occupation,c.moth_occupation from grant_aid a,stud_base b,stud_domicile c where a.year_seme='$work_year_seme' AND a.type='$type' AND a.student_sn=b.student_sn AND a.student_sn = c.student_sn order by class_num";
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

$data="<center><p align='center'><font face='標楷體' size='5'>$school_long_name".$year_seme_arr[$work_year_seme]."清寒優秀學生獎學金申請學生名冊</font></p>
  <p>學校代碼：[ $school_id ] 　　學校名稱：[ $school_long_name ] 　　　申請年度：[ $work_year ]
<table border='1' width='100%' cellspacing='0' cellpadding='0' bordercolordark='#008000' bordercolorlight='#008000'>
<tr bgcolor=$hint_color>
    <td align='center' rowspan=2>編號</td>
    <td align='center' rowspan=2>姓名</td>
    <td align='center' rowspan=2>身份證號碼</td>
    <td align='center' rowspan=2>班級</td>
    <td align='center' rowspan=2>語文</td>
    <td align='center' rowspan=2>數學</td>
    <td align='center' rowspan=2>健康體育</td>
    <td align='center' colspan=3>生活</td>
    <td align='center' rowspan=2>綜合活動</td>
    <td align='center' rowspan=2>領域<br>平均</td>
    <td align='center' rowspan=2>日常<br>生活表現</td>
    <td align='center' rowspan=2>平均成績<br>(領域+日常)</td>
        <td align=center rowspan=2>備　註</td></tr>
    <tr bgcolor=$hint_color><td align='center'>藝術人文</td>
    <td align='center'>自然生活</td>
    <td align='center'>社會</td></tr>";
        $sex=array("-","男","女");
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
	$stud_birthday=$value['stud_birthday'];
	$guardian_name=$value['guardian_name'];

	$clan=$value['clan'];
	$area=$value['area'];
	$dollar=$value['dollar'];
	
	$no=$no+1;
	$num_count++;
        $total=$total+$dollar;
        //取得上學期成績
        $sn_array[0]=$student_sn;
        $sub_score=cal_fin_score($sn_array,$seme_array);
        $nor_score=cal_fin_nor_score($sn_array,$seme_array);
//        echo"<pre>";
//        print_r($sub_score);
//        echo"</pre>";

        //準備名冊

        //echo $class_id."<BR>";

        $isfive=(substr($class_id,1,1)>2);
            
        /*
            $avg=round(($sub_score[$student_sn][language][avg][score]+
             $sub_score[$student_sn][math][avg][score]+$sub_score[$student_sn][health][avg][score]+
             $sub_score[$student_sn][art][avg][score]+$sub_score[$student_sn][nature][avg][score]+
             $sub_score[$student_sn][social][avg][score]+$sub_score[$student_sn][complex][avg][score]+
             $sub_score[$student_sn][life][avg][score]+
             $nor_score[$student_sn][avg][score])/($isfive?6:8),2);               //$division
             */
             
        $data.="<tr>
        <td align=center>$num_count</td>
        <td align=center>$stud_name</td>
        <td align=center>$stud_person_id</td>
        <td align=center>$class_base[$class_id]</td>
    <td align='center'>".$sub_score[$student_sn][language][avg][score]."</td>
    <td align='center'>".$sub_score[$student_sn][math][avg][score]."</td>
    <td align='center'>".$sub_score[$student_sn][health][avg][score]."</td>";
    
    if($sub_score[$student_sn][succ]==5){ $data.="<td align='center' colspan='3'>".$sub_score[$student_sn][life][avg][score]."</td>";}
        else {
              $data.="<td align='center'>".$sub_score[$student_sn][social][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][art][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][nature][avg][score]."</td>";
                   }
    
    
    //($isfive?"<td align='center'>".$sub_score[$student_sn][life][avg][score]."</td>":
    //"<td align='center'>".$sub_score[$student_sn][art][avg][score]."</td><td align='center'>".$sub_score[$student_sn][nature][avg][score]."</td><td align='center'>".$sub_score[$student_sn][social][avg][score]."</td>").
    $data.="<td align='center'>".$sub_score[$student_sn][complex][avg][score]."</td>
    <td align='center'>".$sub_score[$student_sn][avg][score]."</td>
    <td align='center'>".$nor_score[$student_sn][avg][score]."</td>
    <td align='center'>".(($sub_score[$student_sn][avg][score]+$nor_score[$student_sn][avg][score])/2)."</td>
        <td>　</td></tr>";
        $main.="
<p align='center'><font face='標楷體' size='5'>".$year_seme_arr[$work_year_seme].$school_area."國民中學以上學校清寒優秀學生獎學金申請書</font><br>
</p>
<p align='center'>（請加蓋學校關防，否則無效）<br>
</p>

<table border='1' width='100%' height='103' bordercolordark='#008000' bordercolorlight='#008000' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='12%' height='34' align='center' valign='middle' bgcolor=$hint_color>申請人姓名</td>
    <td width='18%' height='34' align='center' valign='middle'>$stud_name</td>
    <td width='7%' height='34' align='center' valign='middle' bgcolor=$hint_color>性別</td>
    <td width='7%' height='34' align='center' valign='middle'>$sex[$stud_sex]</td>
    <td width='14%' height='34' align='center' valign='middle' bgcolor=$hint_color>身分證字號</td>
    <td width='14%' height='34' align='center' valign='middle'>$stud_person_id</td>
    <td width='14%' height='34' align='center' valign='middle' bgcolor=$hint_color>出生年月日</td>
    <td width='14%' height='34' valign='middle'>
      <p align='center'>$stud_birthday</td>
  </tr>
  <tr>
    <td width='12%' height='57' align='center' valign='middle' bgcolor=$hint_color>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>就讀學校</p>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>(請填全銜)</td>
    <td width='32%' height='57' align='center' colspan='3' valign='middle'>$school_long_name</td>
    <td width='14%' height='57' align='center' valign='middle' bgcolor=$hint_color>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>科(系)別</p>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>(班　級)</td>
    <td width='14%' height='57' align='center' valign='middle'>$class_base[$class_id]</td>
    <td width='28%' colspan='2' height='57' align='center' valign='middle'>
      <p align='left' style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>　□國中組&nbsp;</p>
      <p align='left' style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>　□高中職組<font size='2'>(含專科1、2、3年級)</font></p>
      <p align='left' style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>　□大專組<font size='2'>(含專科4、5年級)</font></td>
  </tr>
</table>
<BR>
<table border='1' width='100%' height='89' bordercolordark='#008000' bordercolorlight='#008000' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='100%' align='center' colspan='9' bgcolor=$hint_color height='1'>前學期($seme_list[$pre_seme])學習成績</td>
  </tr>
  <tr bgcolor=$hint_color>
    <td align='center'>領域科目</td>
    <td align='center'>語文</td>
    <td align='center'>數學</td>
    <td align='center'>健康與體育</td>
    <td align='center'>藝術與人文</td>
    <td align='center'>自然與生活科技</td>
    <td align='center'>社會</td>
    <td align='center'>綜合活動</td>
    <td align='center'>日常生活表現</td>
  </tr>
  <tr>
    <td align='center' bgcolor=$hint_color>成績</td>
    <td align='center'>".$sub_score[$student_sn][language][avg][score]."</td>
    <td align='center'>".$sub_score[$student_sn][math][avg][score]."</td>
    <td align='center'>".$sub_score[$student_sn][health][avg][score]."</td>";
    
        if($sub_score[$student_sn][succ]==5){ $main.="<td align='center' colspan='3'>".$sub_score[$student_sn][life][avg][score]."</td>";}
        else {
              $main.="<td align='center'>".$sub_score[$student_sn][social][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][art][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][nature][avg][score]."</td>";
                   }
    $main.="
    <td align='center'>".$sub_score[$student_sn][complex][avg][score]."</td>
    <td align='center'>".$nor_score[$student_sn][avg][score]."</td>
  </tr>
</table>
<BR>
<table border='1' width='100%' height='280' bordercolordark='#008000' bordercolorlight='#008000' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='60%' align='center' colspan='5' valign='middle' height='1' bgcolor=$hint_color>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='center'>　應檢附之證件請學校初審無誤者並請打ˇ</td>
    <td width='40%' align='center' colspan='2' height='4' valign='top' rowspan='2'>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>　</p>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>□未領受其他政府獎學金</p>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='center'><font size='2'>(
      請就讀學校承辦單位主管核章證明 )</font></td>
  </tr>
  <tr>
    <td width='90%' align='center' colspan='5' valign='middle' height='16'>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='left'><font size='2'>　 □戶籍謄本或戶口名單簿影本。</font></p>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='left'><font size='2'>　     □低收入戶證明：以申請人或其父母設籍並居住本縣所在地之鄉鎮市公所開立低收入戶　證明。</font></p>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='left'><font size='2'>　
      </font><font size='2'>□前學期成績證明：就讀學校統一開立之前學期成績證明，國中新生第一學期以前所就讀國民小</font></p>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='left'><font size='2'>　　</font><font size='2'>學開立之六年級最後一學期成績證明。</font></p>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='left'><font size='2'>　  □在學證明：學生證正反面影本。</font></td>
  </tr>
  <tr>
    <td width='13%' align='center' height='20' bgcolor=$hint_color>學校地址</td>
    <td width='77%' align='center' height='5' colspan='4'>$school_add</td>
    <td width='11%' align='center' height='26' rowspan='2' bgcolor=$hint_color>學生簽章</td>
    <td width='18%' align='center' height='26' rowspan='2'>　　</td>
  </tr>
  <tr>
    <td width='13%' align='center' height='20' bgcolor=$hint_color>聯絡電話</td>
    <td width='20%' align='center' height='20'>$school_tel</td>
    <td width='13%' align='center' height='20' bgcolor=$hint_color>傳真號碼</td>
    <td width='27%' align='center' height='20' colspan='2'>$school_fax</td>
  </tr>
  <tr>
    <td width='30%' align='center' height='20' bgcolor=$hint_color colspan='2'>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0'>學校初審結果　</p>
    </td>
    <td width='28%' align='center' height='20' colspan='2' bgcolor=$hint_color>教育局覆核結果</td>
    <td width='42%' align='center' height='20' colspan='3' bgcolor=$hint_color>
      <p align='center'>※ 不符合原因 ※</td>
  </tr>
  <tr>
    <td width='48%' align='center' height='100' colspan='2' valign='middle'>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='center'>□符合且證件齊全　　　</p><BR>
      <p style='word-spacing: 0; line-height: 100%; margin-top: 0; margin-bottom: 0' align='center'>□不符合(含證件不齊全)</td>
    <td width='8%' align='center' height='100' colspan='2' valign='middle'>□符合　　□不符合&nbsp;
    </td>
    <td width='53%' align='center' height='100' colspan='3' valign='middle'>　　</td>
  </tr>
</table>
<BR>　　承辦人：　　　　　　　　　　　　　　業務主管：　　　　　　　　　　　　　　校長：</BR>
";

         if($no<>$student_arr_len) $main.=$newpage;
}
$data.="</table><BR>※ 全校申請人數：[$num_count]，累計金額：新台幣[ $total ]元整。　　　　　申請日期：[ $today ]</CENTER>";
$main=$data.$newpage.$main;

echo $main;
echo "\n<script language=\"Javascript\"> alert (\"本報表預設印表格式為A4橫印，印表前請記得設定喔！\")</script>";
?>