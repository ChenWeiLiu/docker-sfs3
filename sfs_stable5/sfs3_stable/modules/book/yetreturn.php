<?php

// $Id: yetreturn.php 5310 2009-01-10 07:57:56Z hami $

include "book_config.php";
include "header.php";
$class_year= year_base();
$class_name =class_base();
$query = "SELECT book.book_id, book.book_name, book.book_author, date_format(borrow.out_date,'%Y-%m-%d')as out_d ,to_days(curdate())-to_days(borrow.out_date)-".($yetdate-1)." as yet,borrow.stud_id  from book,borrow where book.book_id=borrow.book_id and borrow.out_date < '".date("Y-m-d",mktime(0,0,0,date("m") ,date("d")- $yetdate,date("Y")))."' and borrow.in_date =0 and borrow.curr_class_num <> 0 order by borrow.curr_class_num,borrow.out_date ";
//echo $query;
//exit;

$result = mysql_query($query,$conID) or die ($query);
$tolnum = mysql_num_rows($result);
echo  "<center><BR><H3>逾期歸還書籍計 $tolnum 冊：統計時間：".date("Y-m-d")."</H3></center>";
echo  "<table border=1 width=95% align=center>";
echo  "<tr><td bgcolor=\"#8080FF\" width=20% align=center><strong>書號</strong></td>";
echo  "<td bgcolor=\"#8080FF\" width=50% align=center><strong>書名</strong></td>";
echo  "<td bgcolor=\"#8080FF\" width=15% align=center><strong>借閱人</strong></td>";
echo  "<td bgcolor=\"#8080FF\" width=20% align=center><strong>借閱<br>日期</strong></td>";
echo  "<td bgcolor=\"#8080FF\" width=10% align=center nowrap><strong>逾期<br>日數</strong></td>";
echo  "</tr>";
while($row = mysql_fetch_array($result)){
        $query2 ="select stud_name,curr_class_num,stud_study_cond from stud_base where stud_id ='".$row["stud_id"]."'";
        $result2 = mysql_query($query2,$conID) or die ($query2);
        $row2 = mysql_fetch_array($result2);
        $cyear = $row2["curr_class_num"];
        $memo = "";
        if ($row2["stud_study_cond"]==5){
                $memo ="(已畢業)";
        }
        echo sprintf("<tr><td>%s</td><td>%s</td><td nowrap>%s--%s %s</td><td nowrap>%s</td><td align=right><b><font color=red>%s</font></b></td></tr>",
        $row["book_id"],
        $row["book_name"],
//        $class_year[substr($cyear,0,1)],
        $class_name[substr($cyear,0,3)],
        $row2["stud_name"],
        $memo,
        $row["out_d"],
        $row["yet"]
        );
}
echo "</table>";
echo "</center>";
include "footer.php";
?>