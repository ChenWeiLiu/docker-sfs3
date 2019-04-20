<?php
// $Id: statistics.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
sfs_check();

//學校ID
$school_id=$SCHOOL_BASE["sch_id"];

//獎助類別
$type=($_REQUEST[type]);

//秀出網頁
head("獎助學金");
echo $menu;


//取得學年學期陣列
$year_seme_arr = get_class_seme();

//取得紀錄資料
$sql_select="select year_seme,count(*) as count,sum(dollar) as dollar from grant_aid where type='$type' group by year_seme";

$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
//print_r($recordSet->FetchRow());

while (list($year_seme,$count,$dollar)=$recordSet->FetchRow()) {
$data.="<tr bgcolor='#FFFFFF'><td align=center>$year_seme_arr[$year_seme]</td><td align=center>$count</td><td align=center>$dollar</td></tr>";
}
        $main="<table width='100%' cellspacing='1' cellpadding='3' bgcolor='$hint_color'><tr><td>
        <center><br><br><H2><font face='標楷體'>※ $MODULE_PRO_KIND_NAME ※</H2><H3>[$type]申領統計表</H3>
        <br>學校名稱：$school_long_name 　　　學校代號：$school_id
        <table width='70%' cellspacing='1' cellpadding='3' bgcolor='#C0C0C0'>
        <tr bgcolor='#FFFFCC'>
        <td align=center>學年(期)別</td><td align=center>獎助人數</td><td align=center>金額</td></tr>$data</table><br>
        <a href='index.php?type=$type'><img border='0' src='images/back.gif'> 回上一頁</a><br><br></center>
        </td></tr></table>";
echo $main;
foot();
?>