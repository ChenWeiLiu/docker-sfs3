<?php
//$Id: nor_index.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

//秀出網頁布景標頭
head("國小日常生活表現期末成績查詢");
$tool_bar=make_menu($menu_p);
echo $tool_bar;
$year_seme = ($_POST[seme_chi])?$_POST[seme_chi]:$_GET[seme_chi];
if($year_seme=='')
$year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$year_name = $_POST[year_name];
if($year_name=='')
$year_name = $_GET[year_name];

($_POST[class_chi]) ? $class_chi=$_POST[class_chi]:$class_chi=get_teach_class();

//主要內容 $main=""; echo $main;
echo "<table width='100%' cellspacing='1' cellpadding='1' border=0 align='center' bgcolor='Silver'>
<FORM METHOD=POST ACTION='$PHP_SELF'>
<tr bgcolor='white' style='color:#800000;font-size:14px;'>
<TD colspan=4>■學年度<INPUT TYPE='text' NAME='seme_chi' value='$year_seme' size=5>
<BR>■班　級<INPUT TYPE='text' NAME='class_chi' value='$class_chi' size=5>
<INPUT TYPE='submit'></TD><TD><FONT COLOR='blue'>92學年度第1學期用0921表示,92學年度第2學期用0922表示。<BR>
2年3班用203表示，5年15班用515表示。</FONT>
</FORM></TD></tr>";



// echo get_teach_class();
echo "
<tr bgcolor='white' style='color:#800000;font-size:14px;'>
<TD width=8% nowarp>座號</TD>
<TD width=10% nowarp>學號</TD>
<TD width=12% nowarp>姓名</TD>
<TD width=10% nowarp>成績</TD>
<TD width=60%>評語</TD>
</TR>";

if ($_POST[seme_chi]!='')
{
$SQL="select a.* , b.stud_name,b.stud_id ,c.seme_num from stud_seme_score_nor a, stud_base b, stud_seme c where
a.student_sn=b.student_sn and b.student_sn=c.student_sn and a.seme_year_seme='".$_POST[seme_chi]."' and c.seme_year_seme='".$_POST[seme_chi]."' and c.seme_class='".$class_chi."' and b.student_sn=c.student_sn order by c.seme_num ";

$rs=$CONN->Execute($SQL) or die($SQL);
$arr=$rs->GetArray();
$color_col=array("#f9f9f9","#F2F2F2");//顏色#e8efe2

for($i=0; $i<$rs->RecordCount(); $i++) {
(($i%2)==0) ? $v_color=$color_col[0]:$v_color=$color_col[1];

echo "
<tr bgcolor='$v_color' style='font-size:10pt;'>
<TD>".$arr[$i][seme_num]."</TD>
<TD>".$arr[$i][stud_id]."</TD>
<TD>".$arr[$i][stud_name]."</TD>
<TD>".$arr[$i][ss_score]."</TD>
<TD>".$arr[$i][ss_score_memo]."</TD>
</TR>";

}

}
echo "</TABLE>";
//佈景結尾
foot();
?>
