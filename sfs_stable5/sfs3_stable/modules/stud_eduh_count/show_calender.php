<?php
include "config_calendar.php";
$bgcolor=(trim($_GET["bgcolor"])=="")?"#e5ea9f":"#".$_GET["bgcolor"];


echo "<body bgcolor='$bgcolor'>";
echo "<table border='1' cellpadding='2' cellspacing='0'  bordercolorlight='#333354' bordercolordark='#FFFFFF' width='100%'>";

for ($i=1;$i<8;$i++){
    $test_day=date ("Ymd", mktime (0,0,0,date(m),date(d) + $i - 1,date(Y)));
    $temp_bgcolor=($temp_bgcolor=="#EFE0ED")?"#ffffff":"#EFE0ED";//間隔變換背景顏色
    echo "<tr bgcolor='$temp_bgcolor'>";
    echo "<td width='60'>";

    if (get_c_week($test_day)=="日" or get_c_week($test_day)=="六")
        echo "<font color='red' size='2'>";
      else
        echo "<font size='2'>";
       
    echo substr($test_day,0,4)."-".substr($test_day,4,2)."-".substr($test_day,6,2);
    echo "</font></td>";
    //判定週休
    if (get_c_week($test_day)=="日" or get_c_week($test_day)=="六")
        echo "<td width='23'><font size='2' color='red'>".get_c_week($test_day)."</font></td>";
      else
        echo "<td width='23'><font size='2'>".get_c_week($test_day)."</font></td>";

    $post="";
    //讀取行事曆資料表
    $sql_select="select a.*,b.teach_title_id from calendar a ,teacher_post b where a.from_teacher_sn=b.teacher_sn and a.kind='0' and from_cal_sn='0' order by a.post_time";
    $recordSet = $CONN->Execute($sql_select) or die($sql_select);
    while ($c=$recordSet->FetchRow()) {
        if (check_date($c[restart_day],$c[restart_end],$c[year],$c[month],$c[day],$c[week],$test_day,$c[restart])){
           $name=get_teacher_name($c[from_teacher_sn]);//找出公告人
           $import=($c[import]>6)?"<font color='red' size='2'> **緊急** </font>":"";
           $post.= "<li><font size='2' color='blue'>";
           //填補空白
           $post.=((strlen($c[thing]) % 2)==0)?str_pad($c[thing],32, "　"):str_pad($c[thing],31, "　");
           $post.="</font>";
           //找出公告人之職稱
           $sql_select="select title_name from teacher_title where teach_title_id='$c[teach_title_id]'";
           $record_teacher = $CONN->Execute($sql_select) or die($sql_select);
           $work_name = $record_teacher->FetchRow();
           $post.="<font size='2'>";
           $post.=((strlen($work_name[title_name]) % 2)==0)?str_pad($work_name[title_name],10, "　"):str_pad($work_name[title_name],11, "　");
           $post.="　";

           $post.= "<font size='2' color='green'>".$name."</font> ";
           if (strlen($c[place])>1) $post.="　<font size='2'>".str_pad($c[place],5)."　</font>";
           $post.= "　".$import."<br>";
        }
    }
    $post.= (strlen($post)<19)?"<li><font size='2'>無</font>":"";
    echo "<td>".$post."</td></tr>";
}
echo "</table>";
?>
