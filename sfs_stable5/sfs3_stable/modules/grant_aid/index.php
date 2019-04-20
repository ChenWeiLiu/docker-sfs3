<?php
// $Id: index.php 7132 2013-02-21 07:56:52Z infodaes $

include "config.php";
include "../../include/sfs_case_score.php";

sfs_check();
//秀出網頁
head("獎助學金");
echo $menu;

//學期別
$work_year_seme= ($_POST[work_year_seme])?$_POST[work_year_seme]:$_GET[work_year_seme];
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$work_year_seme=$work_year_seme?$work_year_seme:$curr_year_seme;

//取得前一學期的代號
$seme_list=get_class_seme();
$seme_key_list=array_keys($seme_list);
$pre_seme=$seme_key_list[(array_search($work_year_seme,$seme_key_list))+1];
$seme_array=array($pre_seme);
$sn_array=array();

// 取出班級陣列
$class_base = class_base($work_year_seme);

//取得學年學期陣列
$year_seme_arr = get_class_seme();

//取得年度資料
$year_select="select distinct year_seme from grant_aid where type='$type'";
$recordSetYear=$CONN->Execute($year_select) or user_error("讀取失敗！<br>$year_select",256);
while (list($year_seme)=$recordSetYear->FetchRow()) {
        if ($work_year_seme==$year_seme)
                $yeardata.="<option value='$year_seme' selected >$year_seme_arr[$year_seme]</option>";
        else
                $yeardata.="<option value='$year_seme'>$year_seme_arr[$year_seme]</option>";
}

//取得符合資格資料
$subkind_array=array();
$sql="SELECT * FROM stud_subkind WHERE type_id=$type_id ORDER BY student_sn";
$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>本模組需要先安裝[學生身份子類別]模組,請安裝後再試!<br><br>$sql",256);
while(!$rs->EOF){
	$student_sn=$rs->fields['student_sn'];
	$subkind_array[$student_sn]['clan']=$rs->fields['clan'];
	$subkind_array[$student_sn]['area']=$rs->fields['area'];
	$subkind_array[$student_sn]['memo']=$rs->fields['memo'];
	$subkind_array[$student_sn]['note']=$rs->fields['note'];
	$rs->MoveNext();
}

//取得紀錄資料
//$sql_select="SELECT a.sn,a.year_seme,left(a.class_num,length(a.class_num)-2) as class_id,a.student_sn,a.class_num,b.stud_id,b.stud_name,a.dollar,b.stud_birthday,b.stud_person_id,c.clan,c.area FROM grant_aid a,stud_base b,stud_subkind c WHERE a.student_sn=c.student_sn AND a.year_seme='$work_year_seme' AND a.type='$type' AND a.student_sn=b.student_sn AND c.type_id=$type_id ORDER BY class_num";
$sql_select="SELECT a.sn,a.year_seme,left(a.class_num,length(a.class_num)-2) as class_id,a.student_sn,a.class_num,b.stud_id,b.stud_name,a.dollar,b.stud_birthday,b.stud_person_id FROM grant_aid a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.year_seme='$work_year_seme' AND a.type='$type' ORDER BY class_num";
$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

while (list($sn,$year_seme,$class_id,$student_sn,$class_num,$stud_id,$stud_name,$dollar,$stud_birthday,$stud_person_id)=$recordSet->FetchRow()) {
$no++;
//取得學期成績
$sn_array[0]=$student_sn;
$sub_score=cal_fin_score($sn_array,$seme_array);
$nor_score=cal_fin_nor_score($sn_array,$seme_array);

//顏色 60以下紅色   60-70 淺粉紅
switch (substr($sub_score[$student_sn][avg][score],0,1)) {
case 0:
    $row_color="#777777";
    break;
case 1:
    $row_color="#888888";
    break;
case 2:
    $row_color="#999999";
    break;
case 3:
    $row_color="#AAAAAA";
    break;
case 4:
    $row_color="#BBBBBB";
    break;
case 5:
    $row_color="#CCCCCC";
    break;
case 6:
    $row_color="#DDDDDD";
    break;
case 7:
    $row_color="#EEEEEE";
    break;
default:
    $row_color="#FFFFFF";
    break;
}

	
$clan=$subkind_array[$student_sn]['clan']?$subkind_array[$student_sn]['clan']:"<center><a href='../stud_subkind/setsubkind.php?type_id=$type_id'><img border=0 src='./images/set.gif'></a></center>";

$data.="<tr bgcolor='$row_color'>
         <td>$no</td>
         <td>$class_base[$class_id]</td>
         <td>$stud_id</td>
         <td>$stud_name</td>
         <td>$dollar</td>
         <td>$clan</td>
         <td>{$subkind_array[$student_sn]['area']}</td>
         <td><a href='modify.php?act=modify&sn=$sn'><img border='0' src='images/modify.png' alt='修改'></a> | <a href='modify.php?act=del&sn=$sn&type=$type' onclick='return confirm(\"真的要刪除 $stud_name ?\")'><img border='0' src='images/delete.png' alt='刪除[ $stud_name ]'></a></td>
         <td>".$sub_score[$sn_array[0]][language][$seme_array[0]][score]."</td>
         <td>".$sub_score[$sn_array[0]][math][$seme_array[0]][score]."</td>
         <td>".$sub_score[$sn_array[0]][health][$seme_array[0]][score]."</td>";

    if($sub_score[$student_sn][succ]==5){ $data.="<td align='center' colspan='3'>".$sub_score[$student_sn][life][avg][score]."</td>";}
        else {
              $data.="<td align='center'>".$sub_score[$student_sn][social][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][art][avg][score]."</td>
                  <td align='center'>".$sub_score[$student_sn][nature][avg][score]."</td>";
                    }
    $data.="<td>".$sub_score[$sn_array[0]][complex][$seme_array[0]][score]."</td>
         <td align='center'>".$sub_score[$student_sn][avg][score]."</td>
         <td>".$nor_score[$sn_array[0]][$seme_array[0]][score]."</td></tr>";
}
        $main="
        <table width='100%' cellspacing='1' cellpadding='3' bgcolor='$hint_color'><tr><td><form name=\"year_form\" method=\"post\" action=\"$_SERVER[PHP_SELF]\"><img border='0' src='images/pin.gif'>學年(期)別：<select name='work_year_seme' onchange='this.form.submit()'><option value=''></option>$yeardata</select></td>
                <td><input type='hidden' name='type' value='$type'>
        　<a href='batchadd.php?type=$type'><img border='0' src='images/batchadd.gif' alt='($curr_year_seme)'>身份類別填報</a>
        　<a href='add.php?type=$type'><img border='0' src='images/add.gif' alt='($curr_year_seme)'>個人填報</a>
        　<a href='check_dup.php?type=$type&work_year_seme=$work_year_seme'><img border='0' src='images/check.png' alt='($work_year_seme)'>檢查重複名單</a>
        　<a href='deleteall.php?type=$type'><img border='0' src='images/trash.gif' alt='($curr_year_seme)'>清空本學期名單</a>
        　<a href='statistics.php?type=$type'><img border='0' src='images/sigma.gif'>統計</a></form></td></tr></table>
        <table cellspacing='1' cellpadding='3' bgcolor='#C0C0C0'>
        <tr bgcolor='#E6E9F9'>
        <td>編號</td>
        <td>班級</td>
        <td>學號</td>
        <td>姓名</td>
        <td>金額</td>
        <td><a href='../stud_subkind/setsubkind.php?type_id=$type_id'>$clan_title</a></td>
        <td>$area_title</td>
        <td>編|刪</td>
        <td>語文</td>
        <td>數學</td>
        <td>健體</td>
        <td>藝術</td>
        <td>自然</td>
        <td>社會</td>
        <td>綜合</td>
        <td>領域平均</td>
        <td>日常表現</td>
        </tr>
        $data
        </table><P align='center'>
        <a href='".$menudata[$menu_id][2]."?type=$type&work_year_seme=$work_year_seme&rows=&height=' target='_blank'><img border='0' src='images/htm.gif?type=$type'> HTML印領清冊</a>　　
        <a href='".$menudata[$menu_id][3]."?type=$type&work_year_seme=$work_year_seme&rows=&height=' target='_blank'><img border='0' src='images/htm.gif?type=$type'> HTML申請表</a>　　
        <a href='".$menudata[$menu_id][4]."?type=$type&work_year_seme=$work_year_seme'><img border='0' src='images/csv.png'> CSV檔輸出</a>
        　　　　　　 PS.成績列示學期：$seme_list[$pre_seme]</P>";

echo $main;

foot();
?>