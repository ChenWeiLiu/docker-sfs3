<?php
// $Id: html_export3.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
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
$sql_select="select a.student_sn,left(a.class_num,length(a.class_num)-2) as class_id,b.stud_id,b.stud_name,b.stud_person_id,a.dollar from grant_aid a,stud_base b where a.year_seme='$work_year_seme' and a.type='$type' and a.student_sn=b.student_sn order by class_num";
$res=$CONN->Execute($sql_select) or user_error("身分別紀錄讀取失敗！<br>$sql_select",256);
$student_arr=array();
while(!$res->EOF) {
	$student_sn=$res->fields['student_sn'];
	
	$student_arr[$student_sn]['class_id']=$res->fields['class_id'];
	$student_arr[$student_sn]['stud_id']=$res->fields['stud_id'];
	$student_arr[$student_sn]['stud_name']=$res->fields['stud_name'];
	$student_arr[$student_sn]['stud_person_id']=$res->fields['stud_person_id'];
	$student_arr[$student_sn]['dollar']=$res->fields['dollar'];
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

foreach($student_arr as $key=>$value){
	$class_id=$class_base[$value['class_id']];
	$stud_name=$value['stud_name'];
	$stud_id=$value['stud_id'];
	$stud_person_id=$value['stud_person_id'];
	$clan=$value['clan'];
	$area=$value['area'];
	$dollar=$value['dollar'];
	
	$no=$no+1;
	$num_count++;
        $total=$total+$dollar;
        $data.="<tr bgcolor='#FFFFFF' height=$height>
        <td align='center'>$num_count</td>
        <td align='center'>$class_id</td>
        <td align='center'>$stud_id</td>
        <td align='center'>$stud_name</td>
        <td align='center'>$stud_person_id</td>
        <td align='center'>$school_yg</td>
        <td align='right'>$dollar &nbsp;</td>
        <td></td></tr>";

        //分頁輸出  $num_count==20  代表每頁印20人
        if(($num_count % $rows==0) or $no==$student_arr_len) {
                $page=$page+1;
                $alldollar=$alldollar+$total;
                $allnum=$allnum+$num_count;

                $main.="<font face='標楷體'><CENTER><H2>財團法人臺灣學產基金會<BR>清寒助學金印領清冊</H2>[ 請加蓋關防，否則無效 ]<BR><BR><BR>學校名稱：$school_short_name 　　　學校代號：$school_id 　　　　　填報日期：$today<BR>
                <font face='新細明體'><table border='1' style='border-collapse: collapse' bordercolor='#006600' width='96%' cellspacing='1' cellpadding='3'>
                <tr bgcolor=$hint_color height=20>
                <td align='center'>編號</td>
                <td align='center'>班級</td>
                <td align='center'>學號</td>
                <td align='center'>姓名</td>
                <td align='center'>身分證字號</td>
                <td align='center'>年制</td>
                <td align='center'>助學金(元)</td>
                <td align='center'>學生簽章</td></tr>
                $data
                </CENTER><tr><td colspan=8>※ 本頁小計：人數： $num_count 人，金額： $total 元整。<BR>※ 累計申請補助學生數： $allnum 人，申請補助金額：新台幣 $alldollar 元整。</td></tr></table><font face='標楷體'><BR>$sign ";
                if($no<>$student_arr_len) $main.=$newpage;

                $num_count=0;
                $total=0;
                $data="";
	}
}
$main="<font face='標楷體'><CENTER><BR><BR><BR><BR><BR><BR><H1> $work_year 學年度財團法人臺灣學產基金會<BR>《清寒學生助學金》<BR><BR>印領清冊封面</H1><BR><BR><BR>
        <table border='1' width='50%' cellspacing='0' cellpadding='0' bordercolordark='#008000' bordercolorlight='#008000'>
        <tr><td align=center height=45 bgcolor=$hint_color>學校代號</td><td align=center>$school_id</tr>
        <tr><td align=center height=45 bgcolor=$hint_color>學校名稱</td><td align=center>$school_short_name</tr>
        <tr><td align=center height=45 bgcolor=$hint_color>聯絡電話</td><td align=center>$school_tel</tr>
        <tr><td align=center height=45 bgcolor=$hint_color>傳真號碼</td><td align=center>$school_fax</tr>
        <tr><td align=center height=45 bgcolor=$hint_color>人數統計</td><td align=center>$allnum</tr>
        <tr><td align=center height=45 bgcolor=$hint_color>金額總計</td><td align=center>$alldollar</tr>
        <tr><td align=center height=45 bgcolor=$hint_color>填報日期</td><td align=center>$today
        </table><BR><BR><BR><BR><BR><BR><BR>$sign".$newpage.$main;

$main="<font face='標楷體'><CENTER><BR><BR><BR><BR><H1> $work_year 學年度財團法人臺灣學產基金會<BR>《清寒學生助學金領款收據》<BR></H1><BR>
<table border='1' width='90%' cellspacing='0' cellpadding='0' bordercolordark='#008000' bordercolorlight='#008000' height='165'>
  <tr bgcolor=$hint_color>
    <td width='15%' colspan='2' align='center' height='24'>
      <p align='center'>編號</p>
    </td>
    <td width='67%' colspan='5' height='24'>
      <p align='center'>科目</td>
    <td width='33%' colspan='6' height='24'>
      <p align='center'>金　　　額</td>
  </tr>
  <tr bgcolor=$hint_color>
    <td width='7%' align='center' height='18'>
      <p align='center'>字</p>
    </td>
    <td width='6%' align='center' height='18'>
      <p align='center'>號</p>
    </td>
    <td width='67%' colspan='5' rowspan='2' height='48'>
      <p align='center'>代辦經費：學產基金助學金</p>
    </td>
    <td width='5%' height='18'>
      <p align='center'>十萬</p>
    </td>
    <td width='5%' height='18'>
      <p align='center'>萬</p>
    </td>
    <td width='5%' height='18'>
      <p align='center'>千</p>
    </td>
    <td width='5%' height='18'>
      <p align='center'>百</p>
    </td>
    <td width='5%' height='18'>
      <p align='center'>十</p>
    </td>
    <td width='5%' height='18'>
      <p align='center'>元</p>
    </td>
  </tr>
  <tr>
    <td width='7%' height='28'>
      <p align='center'>　</td>
    <td width='6%' height='28'>
      <p align='center'>　</td>
    <td width='5%' height='28'>
      <p align='center'>　</td>
    <td width='5%' height='28'>
      <p align='center'>　</td>
    <td width='5%' height='28'>
      <p align='center'>　</td>
    <td width='5%' height='28'>
      <p align='center'>　</td>
    <td width='5%' height='28'>
      <p align='center'>　</td>
    <td width='5%' height='28'>
      <p align='center'>　</td>
  </tr>
  <tr bgcolor=$hint_color>
    <td width='20%' colspan='2' align='center' height='32'>
      <p align='center'>校長</p>
    </td>
    <td width='15%' align='center' height='32'>
      <p align='center'>會計主任</p>
    </td>
    <td width='10%' align='center' height='32'>
      <p align='center'>審核</p>
    </td>
    <td width='10%' align='center' height='32'>
      <p align='center'>承辦室主任</p>
    </td>
    <td width='10%' align='center' height='32'>
      <p align='center'>承辦組組長</p>
    </td>
    <td width='10%' align='center' height='32'>
      <p align='center'>承辦人</p>
    </td>
    <td width='30%' colspan='6' align='center' height='32'>
      <p align='center'>備考</p>
    </td>
  </tr>
  <tr>
    <td width='20%' colspan='2' height='53'>
      <p align='center'>　</td>
    <td width='15%' height='53'>
      <p align='center'>　</td>
    <td width='10%' height='53'>
      <p align='center'>　</td>
    <td width='10%' height='53'>
      <p align='center'>　</td>
    <td width='10%' height='53'>
      <p align='center'>　</td>
    <td width='10%' height='53'>
      <p align='center'>　</td>
    <td width='30%' colspan='6' height='53'>
      <p align='center'>　</td>
  </tr>
  </table><BR>[本欄以上由各區承辦學校或主辦學校核銷填寫，各校請勿用印及填寫]<BR>
  <BR><BR><H3>財團法人台灣學產基金會 $work_year 年學產基金清寒學生獎助金
  <BR><BR>計新台幣：NT$ $alldollar 元整，共計 $allnum 人。</H3>
  <BR><BR><BR><BR>[校印加蓋處]<BR><BR><BR><BR>
  <table border='1' width='50%' cellspacing='0' cellpadding='0' bordercolordark='#008000' bordercolorlight='#008000' height=100>
  <tr><td height='30' width='90' bgcolor=$hint_color>　學校代碼</td><td>　$school_id</td></tr>
  <tr><td height='30' width='90' bgcolor=$hint_color>　學校名稱</td><td>　$school_short_name</td></tr>
  <tr><td height='30' width='90' bgcolor=$hint_color>　銀行代號</td><td>　</td></tr>
  <tr><td height='30' width='90' bgcolor=$hint_color>　銀行名稱</td><td>　</td></tr>
  <tr><td height='30' width='90' bgcolor=$hint_color>　銀行帳號</td><td>　</td></tr>
  <tr><td height='30' width='90' bgcolor=$hint_color>　帳號戶名</td><td>　</td></tr></table>
  <BR><BR>$sign".$newpage.$main;

  echo $main;

?>