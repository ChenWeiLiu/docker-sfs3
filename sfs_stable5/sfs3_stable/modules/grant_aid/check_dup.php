<?php
// $Id: check_dup.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
sfs_check();

//獎助類別
$type=($_REQUEST[type]);

//學期別
$work_year_seme= ($_REQUEST[work_year_seme]);
if($work_year_seme=='')        $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

//秀出網頁
head("獎助學金");
echo $menu;

// 取出班級陣列
$class_base = class_base($work_year_seme);
//print_r($class_base );

//取得學年學期陣列
$year_seme_arr = get_class_seme();

//取得紀錄資料
$sql_select="select a.student_sn,left(a.class_num,length(a.class_num)-2) as class_id,b.stud_id,b.stud_name,count(*) as count,sum(dollar) as dollar from grant_aid a,stud_base b where a.type='$type' and a.year_seme='$work_year_seme' and a.student_sn=b.student_sn group by student_sn,class_id,stud_id,b.stud_name having count>1";

$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
//print_r($recordSet->FetchRow());

while (list($student_sn,$class_id,$stud_id,$stud_name,$count,$dollar)=$recordSet->FetchRow()) {
$data.="<tr bgcolor='#FFFFFF'><td>$student_sn</td><td>$class_base[$class_id]</td><td>$stud_id</td><td>$stud_name</td><td>$count</td><td>$dollar</td></tr>";
}
        $main="<table width='96%' cellspacing='1' cellpadding='3' bgcolor='$hint_color'>
        <tr><td colspan=5><center><img border='0' src='images/pin.gif'>檢查的學年(期)別：$year_seme_arr[$work_year_seme]　　　　　　　　　　<a href='index.php?type=$type&work_year_seme=$work_year_seme'><img border='0' src='images/back.gif'>回上一頁</a></center></td></tr>
        <tr bgcolor='#CCCCFF'><td>學籍流水號</td><td>班級</td><td>學號</td><td>姓名</td><td>次數統計</td><td>金額統計</td></tr>
        $data
        </table>";
echo $main;
foot();
?>