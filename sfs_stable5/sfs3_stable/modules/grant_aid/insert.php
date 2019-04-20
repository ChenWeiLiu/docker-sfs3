<?php
// $Id: insert.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
sfs_check();

//取得前一頁資訊
$curr_year_seme=($_POST[curr_year_seme]);
$dollar=($_POST[dollar]);
$sel_stud=($_POST[sel_stud])?$_POST[sel_stud]:$_GET[sel_stud];
$type=($_POST[type])?$_POST[type]:$_GET[type];


//學期別
$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

//秀出網頁
head("獎助學金");
echo $menu;

//取得陣列長度
$count = count($sel_stud);

if($count<1) { echo "<center><BR><BR><BR><BR><H1><a href='add.php?type=$type'><img border='0' src='images/back.gif'> 您並未勾選任何學生喔!!</a></center>"; }
else {
        //將要寫入的值合併成一字串
        for($i=0; $i<$count; $i++)
        {
                $values.="('".$type."','".$curr_year_seme."',".$sel_stud[$i].",".$dollar.")";
                if($i<$count-1) $values.=","; else $values.=";";
        }

        //連接資料庫送出sql
        $values="insert into grant_aid(type,year_seme,student_sn,class_num,dollar) values ".$values;
//        echo $values;

        $recordSet=$CONN->Execute($values) or user_error("讀取失敗！<br>$values",256);

        //送出寫入幾筆的訊息和連結回到列表頁面
        echo "<center><BR><BR><BR><BR><H1><a href='index.php?type=$type'><img border='0' src='images/back.gif'> 已新增[$count]筆獎助紀錄!<BR>請按此回列示頁面檢查</a></center>";
}
foot();

?>