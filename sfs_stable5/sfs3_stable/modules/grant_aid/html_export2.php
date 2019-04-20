<?php
// $Id $

include "config.php";
sfs_check();


//學校資訊
$school_id=$SCHOOL_BASE["sch_id"];
$school_tel=$SCHOOL_BASE["sch_phone"];
$school_fax=$SCHOOL_BASE["sch_fax"];
$school_area=$SCHOOL_BASE["sch_sheng"];

//今天的日期
$today=(date("Y")-1911).date("年m月d日");

//學期別
$work_year_seme= ($_POST[work_year_seme])?$_POST[work_year_seme]:$_GET[work_year_seme];
if($work_year_seme=='')        $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$work_year=substr($work_year_seme,0,3)+0;

//列高與列數
$height= ($_POST[height])?$_POST[height]:$_GET[height];
if($height=="") $height=35;

$rows= ($_POST[rows])?$_POST[rows]:$_GET[rows];
if($rows=="") $rows=10;

//換頁html碼
$newpage="<P STYLE='page-break-before: always;'>";


//簽章列
$sign="承辦人：　　　　　　　　出納：　　　　 　　　　會計：　　　 　　　　主任：　　　　　　　　　校長：　　　　　";

// 取出班級陣列
$class_base = class_base($work_year_seme);

//取得學年學期陣列
$year_seme_arr = get_class_seme();

//取得全校學生人數
$year_select="select count(*) from stud_base where stud_study_cond=0";
$recordSet=$CONN->Execute($year_select) or user_error("讀取失敗！<br>$year_select",256);
$student_total=$recordSet->FetchRow();

//取得原住民學生人數    d_id=9
$type_select="SELECT count(*) FROM stud_base WHERE stud_study_cond=0 and stud_kind like '%,$type_id,%'";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
$yuanzhumin_total=$recordSet->FetchRow();



//取得紀錄資料
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
        <td align='center'>$stud_name</td>
        <td align='center'>$stud_person_id</td>
        <td align='center'>$clan</td>
        <td align='center'>$area</td>
        <td align='center'></td>
        <td align='center'>ˇ</td>
        <td align='center'></td>
        <td align='right'>$dollar &nbsp;</td>
        <td></td></tr>";

        //分頁輸出  $num_count==20  代表每頁印20人
        if(($num_count % $rows==0) or $no==$student_arr_len) {
                $page=$page+1;
                $alldollar=$alldollar+$total;
                $allnum=$allnum+$num_count;
//                 <td>全校學生人數：_____<br>全校原住民學生人數：___________<br>全校遴送人數：___________</td>
//                <td>電話：$school_tel <br>傳真：$school_fax </td>
                $main.="<font face='標楷體'><CENTER><H2>$school_area".$year_seme_arr[$work_year_seme]."申請國民中小學原住民學生獎學金資格證明暨印領清冊</H2>[ 請加蓋關防，以資具有法律的效力 ]<BR><BR><BR>
                學校名稱：[ $school_short_name ]　　學校代號：[ $school_id ]　　填報日期：[ $today ]
                <font face='新細明體'><table border='1' style='border-collapse: collapse' bordercolor='#006600' width='96%' cellspacing='1' cellpadding='3'>
  <tr bgcolor=$hint_color>
    <td width='3%'  align='center' rowspan='2'>NO</td>
    <td width='10%'  align='center' rowspan='2'>班級</td>
    <td width='9%'  align='center' rowspan='2'>姓名</td>
    <td width='12%'  align='center' rowspan='2'>身分證字號</td>
    <td width='10%'  align='center' rowspan='2'>族籍</td>
    <td width='8%'  align='center' rowspan='2'>地域別</font></td>
    <td width='24%' height='15' colspan='3' align='center'><font size='2'>申請依據 (請在適合選項中打ˇ)</font></td>
    <td width='6%'  align='center' rowspan='2'>金額</td>
    <td width='10%'  align='center' rowspan='2'>學生簽章</td>
  </tr>
  <tr bgcolor=$hint_color>
    <td width='8%' height='16' align='center'><font size='1'>特殊優良表現</font></td>
    <td width='7%' height='16' align='center'><font size='1'>前學期成績</font></td>
    <td width='6%' height='16' align='center'><font size='1'>學校遴薦</font></td>
  </tr>
                $data
                <tr bgcolor=$hint_color>
    <td align='left' colspan='11'>
      <font size='2'>&nbsp;※經查上表所列學生確實具有原住民族籍，於前學期就讀期間表現優異，並皆未領有其他政府機關或公營事業單位之獎學金，特此證明。</font>
    </td>
  </tr>
                </CENTER><tr><td colspan=11>※ 本頁小計：人數： $num_count 人，金額： $total 元整。　　　※ 累計原住民學生數： $allnum 人，申請補助金額：新台幣 $alldollar 元整。</td></tr></table><font face='標楷體'><BR>$sign ";
                if($no<>$student_arr_len) $main.=$newpage;

                $num_count=0;
                $total=0;
                $data="";
	}
}


$main="<font face='標楷體'><CENTER><BR><BR><H1>$school_area".$year_seme_arr[$work_year_seme]."國民中小學<BR><BR>《原住民學生獎學金》<BR><BR>資格證明暨印領清冊封面</H1><BR>
        <table border='1' width='60%' cellspacing='0' cellpadding='0' bordercolordark='#008000' bordercolorlight='#008000'>
        <tr><td align=center height=30 width=30% bgcolor=$hint_color>學校代號</td><td align=center>$school_id</tr>
        <tr><td  align=center height=30 bgcolor=$hint_color>學校名稱</td><td align=center>$school_long_name</tr>
        <tr><td  align=center height=30 bgcolor=$hint_color>聯絡電話</td><td align=center>$school_tel</tr>
        <tr><td  align=center height=30 bgcolor=$hint_color>傳真號碼</td><td align=center>$school_fax</tr>
        <tr><td  align=center height=30 bgcolor=$hint_color>學校類型</td><td align=center>□原住民重點國中小　□一般國中小</td></tr>
        <tr><td  align=center height=30 bgcolor=$hint_color>全校學生人數</td><td align=center>$student_total[0]</tr>
        <tr><td  align=center height=30 bgcolor=$hint_color>原住民學生數</td><td align=center>$yuanzhumin_total[0]</tr>
        <tr><td align=center height=30 bgcolor=$hint_color>全校遴送人數</td><td align=center>$allnum</tr>
        <tr><td align=center height=30 bgcolor=$hint_color>申請金額總計</td><td align=center>$alldollar</tr>
        <tr><td align=center height=30 bgcolor=$hint_color>填報日期</td><td align=center>$today</table><BR><BR>$sign".$newpage.$main;
        echo $main;


echo "<script language=\"Javascript\"> alert (\"本報表預設印表格式為A4橫印，印表前請記得設定喔！\")</script>";




?>