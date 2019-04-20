<?php
// $Id: ask_export2.php 7132 2013-02-21 07:56:52Z infodaes $

include "config.php";
include "../../include/sfs_case_score.php";
//include "../../include/sfs_case_dataarray.php";

sfs_check();

//學校資訊
$school_id=$SCHOOL_BASE["sch_id"];
$school_tel=$SCHOOL_BASE["sch_phone"];
$school_fax=$SCHOOL_BASE["sch_fax"];

//取得學歷參照表
$edu_list=edu_kind();

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


$data="<center><p align='center'><font face='標楷體' size='5'>$school_long_name".$year_seme_arr[$work_year_seme]."原住民學生獎學金申請學生名冊</font></p>
  <p>學校代碼：[ $school_id ] 　　學校名稱：[ $school_long_name ] 　　　申請年度：[ $work_year ]
<table border='1' width='100%' cellspacing='0' cellpadding='0' bordercolordark='#008000' bordercolorlight='#008000'>
<tr bgcolor=$hint_color><td align=center>編號</td>
        <td align=center>姓名</td>
        <td align=center>身份證號碼</td>
        <td align=center>班級</td>
    <td align='center'>語文(國)</td>
    <td align='center'>語文(英)</td>
    <td align='center'>數學</td>
    <td align='center'>健康體育</td>
    <td align='center'>藝術人文</td>
    <td align='center'>自然生活</td>
    <td align='center'>社會</td>
    <td align='center'>綜合活動</td>
    <td align='center'>日常表現</td>
    <td align='center'>平均成績</td>
        <td align=center>備　　註</td></tr>";
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
        //準備名冊

//print_r($sub_score);
        //不加權的平均成績「平均成績」一欄的換算方式為各領域成績的總和除以總領域數，即國中除以9，國小除以8，請不要以加權方式計算之。
        $division=($sub_score[$student_sn][succ]==5)?6:9;
        $avg=round(($sub_score[$student_sn][chinese][avg][score]+$sub_score[$student_sn][english][avg][score]+
             $sub_score[$student_sn][math][avg][score]+$sub_score[$student_sn][health][avg][score]+
             $sub_score[$student_sn][art][avg][score]+$sub_score[$student_sn][nature][avg][score]+
             $sub_score[$student_sn][social][avg][score]+$sub_score[$student_sn][complex][avg][score]+
             $sub_score[$student_sn][life][avg][score]+ $nor_score[$student_sn][avg][score])/$division,2);
        $data.="<tr>
        <td align=center>$num_count</td>
        <td align=center>$stud_name</td>
        <td align=center>$stud_person_id</td>
        <td align=center>$class_base[$class_id]</td>
    <td align='center'>".$sub_score[$student_sn][chinese][avg][score]."</td>
    <td align='center'>".$sub_score[$student_sn][english][avg][score]."</td>
    <td align='center'>".$sub_score[$student_sn][math][avg][score]."</td>
    <td align='center'>".$sub_score[$student_sn][health][avg][score]."</td>";

    if($sub_score[$student_sn][succ]==5){ $data.="<td align='center' colspan='3'>".$sub_score[$student_sn][life][avg][score]."</td>";}
        else {
              $data.="<td align='center'>".$sub_score[$student_sn][social][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][art][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][nature][avg][score]."</td>";
                    }
    $data.="
    <td align='center'>".$sub_score[$student_sn][complex][avg][score]."</td>
    <td align='center'>".$nor_score[$student_sn][avg][score]."</td>
    <td align='center'>$avg</td>
        <td>　</td></tr>";
        $main.="
<p align='center'><font face='標楷體' size='5'>$school_long_name
原住民學生".$year_seme_arr[$work_year_seme]."獎學金申請書</font></p>
<p align='right'><font face='標楷體'>申請日期</font>：[<font face='標楷體'>
$today ]&nbsp;&nbsp;&nbsp; #$num_count</font></p>
<table border='1' width='100%' height='240' cellpadding='4' bordercolordark='#008000' bordercolorlight='#008000' cellspacing='0'>
  <tr>
    <td width='6%' rowspan='2' height='60' align='center' bgcolor=$hint_color>申請人</td>
    <td width='14%' height='16' align='center' bgcolor=$hint_color>姓名</td>
    <td width='20%' height='16' align='center' bgcolor=$hint_color>身分證號碼</td>
    <td width='10%' height='16' align='center' colspan='2' bgcolor=$hint_color>性別</td>
    <td width='15%' height='16' align='center' colspan='3' bgcolor=$hint_color>班級</td>
    <td width='17%' height='16' align='center' colspan='3' bgcolor=$hint_color>族籍</td>
    <td width='18%' height='54' align='center' colspan='3' rowspan='2'>[$area]原住民</td>
  </tr>
  <tr>
    <td width='14%' height='38' align='center'>$stud_name</td>
    <td width='20%' height='38' align='center'>$stud_person_id</td>
    <td width='10%' height='38' align='center' colspan='2'>$sex[$stud_sex]</td>
    <td width='15%' height='38' align='center' colspan='3'>$class_base[$class_id]</td>
    <td width='17%' height='38' align='center' colspan='3'>$clan</td>
  </tr>
  <tr>
    <td width='6%' height='55' align='center' bgcolor=$hint_color>住址</td>
    <td width='44%' height='55' align='center' colspan='4'>$stud_addr_1</td>
    <td width='5%' height='55' align='center' bgcolor=$hint_color>電話</td>
    <td width='15%' height='55' align='center' colspan='3'>$stud_tel_1</td>
    <td width='12%' height='55' align='center' colspan='2' bgcolor=$hint_color>學生簽章</td>
    <td width='18%' height='55' align='center' colspan='3'>　</td>
  </tr>
  <tr>
    <td width='20%' height='16' align='left' colspan='2'>父親職業：$fath_occupation</td>
    <td width='20%' height='16' align='left'>父親學歷：$edu_list[$fath_education]</td>
    <td width='48%' height='16' align='left' colspan='9'>家庭狀況(可複選)：□低收入戶　□單親　□隔代教養</td>
    <td width='12%' height='32' align='center' colspan='2' rowspan='2' bgcolor=#FFCCCC>
      <p align='left'><font size='2'>不影響獲獎與否的結果，請據實回答。</font></td>
  </tr>
  <tr>
    <td width='20%' height='16' align='left' colspan='2'>母親職業：$moth_occupation</td>
    <td width='20%' height='16' align='left'>母親學歷：$edu_list[$moth_education]</td>
    <td width='48%' height='16' align='left' colspan='9'>家庭經濟(單選)：□困苦　□普通　□小康　□不錯</td>
  </tr>
  <tr>
    <td width='40%' height='51' align='center' colspan='3'>
      <p align='left' style='line-height: 100%; margin-top: 0; margin-bottom: 0'><font size='3'>是否領有其它政府機關或公營事業單位之獎學金？</font><p align='center' style='line-height: 100%; margin-top: 0; margin-bottom: 0'><font size='3'>□是　□否</font></td>
    <td width='4%' height='77' align='center' rowspan='2' bgcolor=$hint_color>前學期成績</td>
    <td width='6%' height='51' align='center' bgcolor=$hint_color>語文(國)</td>
    <td width='5%' height='51' align='center' bgcolor=$hint_color>語文(英)</td>
    <td width='5%' height='51' align='center' bgcolor=$hint_color>數學</td>
    <td width='5%' height='51' align='center' bgcolor=$hint_color>健康體育</td>
    <td width='5%' height='51' align='center' bgcolor=$hint_color>藝術人文</td>
    <td width='6%' height='51' align='center' bgcolor=$hint_color>自然生活</td>
    <td width='6%' height='51' align='center' bgcolor=$hint_color>社會</td>
    <td width='6%' height='51' align='center' bgcolor=$hint_color>綜合活動</td>
    <td width='6%' height='51' align='center' bgcolor=$hint_color>日常表現</td>
    <td width='6%' height='51' align='center' bgcolor=$hint_color>平均成績</td>
  </tr>
  <tr>
    <td width='6%' height='26' align='center' bgcolor=$hint_color>申請資格條件依據</td>
    <td width='34%' height='26' align='center' colspan='2'>
          <p align='left' style='line-height: 100%; margin-top: 0; margin-bottom: 0'><font size='3'>□以團體或個人方式參加全國性比賽前三名的特殊優良表現為申請依據(請附證明)</font>
          <p align='left' style='line-height: 100%; margin-top: 0; margin-bottom: 0'><font size='3'>□以前一學期總成績在70分(含)以上為申請依據(請填成績分數)。</font>
          <p align='left' style='line-height: 100%; margin-top: 0; margin-bottom: 0'><font size='3'>□無成績依據，由學校遴薦之(一年級新生第一學期才適用)。</font>
    </td>
    <td width='6%' height='26' align='center'>".$sub_score[$student_sn][chinese][avg][score]."</td>
    <td width='5%' height='26' align='center'>".$sub_score[$student_sn][english][avg][score]."</td>
    <td width='5%' height='26' align='center'>".$sub_score[$student_sn][math][avg][score]."</td>
    <td width='5%' height='26' align='center'>".$sub_score[$student_sn][health][avg][score]."</td>";

    if($sub_score[$student_sn][succ]==5){ $main.="<td align='center' colspan='3'>".$sub_score[$student_sn][life][avg][score]."</td>";}
        else {
              $main.="<td align='center'>".$sub_score[$student_sn][social][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][art][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][nature][avg][score]."</td>";
                    }
    $main.="
    <td width='6%' height='26' align='center'>".$sub_score[$student_sn][complex][avg][score]."</td>
    <td width='6%' height='26' align='center'>".$nor_score[$student_sn][avg][score]."</td>
    <td width='6%' height='26' align='center'>$avg</td>
  </tr>
  <tr>
    <td width='100%' height='1' align='center' colspan='14' bgcolor='#FFCCCC'>
      <p align='left'><font size='2'>註：一、「平均成績」一欄的換算方式為各領域成績的總和除以總領域數，即國中除以9，國小除以8，請不要以加權方式計算之。<br>
     二、是否領有其它政府機關或公營事業單位之獎學金及申請資格條件依據系申請必具條件，必須勾選。</font></td>
  </tr>
</table>
<table border='1' width='100%' height='81' bordercolordark='#008000' bordercolorlight='#008000'>
  <tr>
    <td width='6%' rowspan='3' align='center' valign='middle' height='75' bgcolor=$hint_color>隨附
      <p>文件</p>
    </td>
    <td width='26%' align='center' valign='middle' height='19' bgcolor=$hint_color>附件名稱</td>
    <td width='14%' align='center' valign='middle' height='19' bgcolor=$hint_color>審查結果</td>
    <td width='9%' rowspan='3' align='center' valign='middle' height='75' bgcolor=$hint_color>審核
      <p>結果</p>
    </td>
    <td width='45%' rowspan='3' align='center' valign='middle' height='75'>　</td>
  </tr>
  <tr>
    <td width='26%' align='center' valign='middle' height='30' bgcolor='#D9ECFF'>原住民身分證明文件影本</td>
    <td width='14%' align='center' valign='middle' height='30'>□有　□無</td>
  </tr>
  <tr>
    <td width='26%' align='center' valign='middle' height='32' bgcolor='#D9ECFF'>其它文件影本</td>
    <td width='14%' align='center' valign='middle' height='32'>□有　□無</td>
  </tr>
</table><br>
<p>級任導師蓋章：　　　　　　承辦人蓋章：　　　　　　電話：[$school_tel]　　　　教務主任蓋章：　　　　                校長蓋章：</p>
";

      if($no<>$student_arr_len) $main.=$newpage;
}
$data.="</table><BR>※ 申請人數合計：[$num_count]，金額合計：新台幣[ $total ]元整。　　　　　申請日期：[ $today ]</CENTER>";
$main=$data.$newpage.$main;

echo $main;
echo "\n<script language=\"Javascript\"> alert (\"本報表預設印表格式為A4橫印，印表前請記得設定喔！\")</script>";
?>