<?php
// $Id: batchinsert.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
sfs_check();

//取得前一頁資訊
$curr_year_seme=($_POST[curr_year_seme]);
$dollar=($_POST[dollar]);
$sel_stud=($_POST[sel_stud]);


//學期別
$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

//秀出網頁
head("獎助學金");
echo $menu;

//取得陣列長度
$count = count($sel_stud);

if($count<1) { echo "<center><BR><BR><BR><BR><H1><a href='batchadd.php'><img border='0' src='images/back.gif'> 您並未勾選任何學生類別喔!!</a></center>"; }
else {
        //將選取類別的學生資料取出並寫入grant_aid資料庫
        for($i=0; $i<$count; $i++)
        {
                $sql_select="select curr_class_num,student_sn from stud_base where (stud_kind like '%,$sel_stud[$i],%') and (stud_study_cond=0) order by curr_class_num";
                $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
                $reccount=$recordSet->recordcount();
                if($reccount>0)
                {
                $values="";

                while(list($curr_class_num,$student_sn)=$recordSet->FetchRow()) {
                        $values.="('".$type."','".$curr_year_seme."',".$student_sn.",".$curr_class_num.",".$dollar.")";
                        if($recordSet->CurrentRow()<$reccount) $values.=","; else $values.=";";
                $total=$total+1;
                }
                //連接資料庫送出sql
                $values="insert into grant_aid(type,year_seme,student_sn,class_num,dollar) values ".$values;
                $recordSet=$CONN->Execute($values) or user_error("讀取失敗！<br>$values",256);
                }
        }
        //送出寫入幾筆的訊息和連結回到列表頁面
        if($total>0) echo "<center><BR><BR><BR><BR><H1><a href='index.php?type=$type'><img border='0' src='images/back.gif'> 已新增[$count]類學生,共[$total]筆獎助紀錄!<BR>請按此回列示頁面檢查</a></center>";
        else echo "<center><BR><BR><BR><BR><H1><a href='batchadd.php?type=$type'><img border='0' src='images/back.gif'> 您選定的類別，並未含有任何的學生紀錄!<BR>請按此回類別填報頁面</a></center>";
}

foot();

?>