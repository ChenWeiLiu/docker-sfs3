<?php
// $Id: deleteall.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
sfs_check();

//獎助類別
$type=($_REQUEST[type]);

//學期別
$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme_tag= sprintf("%3d學年度第%d學期",curr_year(),curr_seme());
$act=($_POST[act]);


if($act=="否") { header("location: index.php?type=$type"); }
else
{
//秀出網頁
head("獎助學金");
echo $menu;
echo "<table width='100%' cellspacing='1' cellpadding='3' bgcolor='$hint_color'><tr><td>";
if($act=="是")
{
        $values="delete from grant_aid where type='$type' and year_seme='$curr_year_seme'";
        $recordSet=$CONN->Execute($values) or user_error("刪除失敗！<br>$values",256);
        echo "<center><BR><BR><BR><H2><font face='標楷體'><a href='index.php?type=$type'>已刪除本學期<$curr_year_seme_tag>所有的[$type]獎助紀錄!<br><br>請按此回列示頁面檢查<BR><BR></a></center>";
} else
        echo "<center><form name='delete' method='post' action='$_SERVER[PHP_SELF]'><H2><BR><BR><BR><font face='標楷體'>您真的要刪除< $curr_year_seme_tag >的[$type]開列紀錄嗎?</font><BR><BR><input type='hidden' name='type' value='$type'><input type='radio' value='是' name='act'>是　<input type='radio' value='否' checked name='act'>否　<BR><BR><input type='submit' value='　確　定　' name='go' style='font-size: 16pt; font-family: 標楷體; font-weight: bold'></form></center>";
}
echo "</td></tr></table>";
foot();

?>