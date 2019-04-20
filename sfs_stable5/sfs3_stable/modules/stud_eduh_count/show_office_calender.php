<?php
include "config_calendar.php";


//處理傳遞之參數
while(list($mode,$val)=each($_POST)){
        if ($mode=="year") $year=$val;//取得原年
        if ($mode=="month") $month=$val;//取得原月
        if ($mode=="day") $day=$val;//取得原日
        if (substr($mode,0,3)=="act"){
                $id=substr($mode,3);
                $kind=$val;
        }
}


//查詢
if ($kind=="<<前一年"){
        $year=$year-1;
}
if ($kind=="後一年>>"){
        $year=$year+1;
}
if ($kind=="<前一月"){
        $month=$month-1;
        if ($month==0){
                $month=12;
                $year=$year-1;
        }
}
if ($kind=="後一月>"){
        $month=$month+1;
        if ($month==13){
                $month=1;
                $year=$year+1;
        }
}
if ($kind=="跳回現今年月"){
        $year=date("Y");
        $month=date("m");
        $day=date("d");
}
if ($kind=="送出查詢"){
        $year=$_POST["new_year"];
        $month=$_POST["new_month"];
}

if ($year=="") $year=date("Y");
if ($month=="") $month=date("m");
if ($day=="") $day=date("d");

$year=sprintf ("%04d", $year);//將年改成四位數(補0)
$month=sprintf ("%02d", $month);//將月改成二位數(補0)

echo "<head><title>外埔國中輔導室日誌</title></head>";
echo "<body bgcolor='#d7d7d7'>";

//標題
echo "<p align='center'><b><font size='4' face='標楷體'> 外埔國中輔導室日誌";
echo "</font></b><hr>";

//顯示查詢跳頁
echo "<form method='POST' action='".basename($_SERVER["PHP_SELF"])."'>";
echo "<table width='100%'><tr>";
echo "<input type='hidden' value='".$year."' name='year'>";
echo "<input type='hidden' value='".$month."' name='month'>";

echo "<td><input type='submit' name='act' value='<<前一年'></td>";
echo "<td><input type='submit' name='act' value='<前一月'></td>";
echo "<td><input type='submit' name='act' value='跳回現今年月'></td>";
echo "<td><input type='submit' name='act' value='後一月>'></td>";
echo "<td><input type='submit' name='act' value='後一年>>'></td>";

echo "<td align='center'>";
echo "<input type='text' name='new_year' size='4'  maxlength='4' value='".$year."'>年　";
echo "<select name='new_month'>";
for ($i=1;$i<13;$i++){
     if (strlen($i)<2) $i="0".$i;
     if ($i==$month){
              echo "<option selected>".$i."</option>";
     }
          else{
              echo "<option>".$i."</option>";
     }
}

echo "</select>月　";

echo "<input type='submit' name='act' name='查詢'>";
echo "</td></tr></table>";
echo "</form>";

//日誌表格開始
echo "<table width='100%'><tr><td width='100%' valign='top'>";

echo "<font size='2'>今日：".date(Y)."/".date(m)."/".date(d)."</font>　";
echo "<font color='blue' size='2'>週".get_week(date(Ymd))."</font>　　";

//秀出行事紀錄
echo "<font size='2'>現在檢視年月：</font>";
echo "<font color='red' size='2'>".$year."</font><font color='blue' size='2'> 年 </font>";
echo "<font color='red' size='2'>".$month."</font><font color='blue' size='2'> 月 </font>";
//主表格開始
echo "<table width='100%' border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111'>";
echo "<tr bgcolor='#000080'><td width='20'><font size='2' color='white'>月</font></td>";
echo "<td width='20'><font size='2' color='white'>日</font></td>";
echo "<td width='20'><font size='2' color='white'>週</font></td>";
echo "<td width='30%' align='center'><font size='2' color='white'>輔導主任</font></td>";
echo "<td width='30%' align='center'><font size='2' color='white'>輔導組長</font></td>";
echo "<td width='30%' align='center'><font size='2' color='white'>資料組長</font></td>";
echo "</tr>";

$d="01";
while(checkdate($month,$d,$year)){
        if (strlen($d)<2) $d="0".$d;//將$d 設成2位數之日期

        if (substr($d,0,1)=="0")//將$temp設成 1~月底之日期
             $temp=substr($d,1);
         else
             $temp=$d;

        if (get_c_week($year.$month.$d)=="六"||get_c_week($year.$month.$d)=="日")
                echo "<tr bgcolor='#ffccff' height='38'>";
           else
                echo "<tr bgcolor='#ffffff' height='38'>";

        if (date("Ymd")==$year.$month.$d){//檢查是否歷史今日
                echo "<td bgcolor='red'><font size='2'><b>".$month."</b></font></td>";
                echo "<td bgcolor='red'><font size='2'><b>".$d."</b></font></td>";
                echo "<td bgcolor='red'><font size='2'><b>".get_c_week($year.$month.$d)."</b></font></td>";
        }
        else{
                echo "<td><font size='2'>".$month."</font></td>";
                echo "<td><font size='2'>".$d."</font></td>";
                echo "<td><font size='2'>".get_c_week($year.$month.$d)."</font></td>";
        }
        $test_day=$year.$month.$d;//設定比對日期
        $work_array=array("輔導室主任","輔導組長","資料組長");

        while (list($val,$work_name)=each($work_array)){
                echo "<td>";
                //找出職稱id
                $sql_select="select a.teacher_sn from teacher_post a ,teacher_title b where a.teach_title_id=b.teach_title_id and b.title_name='$work_name'";
                $record_teacher = $CONN->Execute($sql_select) or die($sql_select);
                $array = $record_teacher->FetchRow();
                $teacher_sn=$array[teacher_sn];
                $post="";

                $sql_select="select * from calendar where from_teacher_sn='$teacher_sn' and (kind='3' or kind='0') and from_cal_sn='0' order by post_time";
                $recordSet = $CONN->Execute($sql_select) or die($sql_select);
                while ($c=$recordSet->FetchRow()) {
                    if (check_date($c[restart_day],$c[restart_end],$c[year],$c[month],$c[day],$c[week],$test_day,$c[restart])){
                       $import=($c[import]>6)?"<font color='red' size='2'> **緊急** </font>":"";
                       $post.= "<li><font size='2' color='blue'>";
                       $post.=$c[thing];
                       $post.="</font>";
                       $post.= "　".$import."<br>";
                    }
                }
                echo $post."</td>";
         }

        echo "</tr>";
        $d=$d+1;
}
echo "</table>";
echo "</td></tr></table>";

//顯示查詢跳頁
echo "<form method='POST' action='".basename($_SERVER["PHP_SELF"])."'>";
echo "<table width='100%'><tr>";
echo "<input type='hidden' value='".$year."' name='year'>";
echo "<input type='hidden' value='".$month."' name='month'>";

echo "<td><input type='submit' name='act' value='<<前一年'></td>";
echo "<td><input type='submit' name='act' value='<前一月'></td>";
echo "<td><input type='submit' name='act' value='跳回現今年月'></td>";
echo "<td><input type='submit' name='act' value='後一月>'></td>";
echo "<td><input type='submit' name='act' value='後一年>>'></td>";

echo "<td align='center'>";
echo "<input type='text' name='new_year' size='4'  maxlength='4' value='".$year."'>年　";
echo "<select name='new_month'>";
for ($i=1;$i<13;$i++){
     if (strlen($i)<2) $i="0".$i;
     if ($i==$month){
              echo "<option selected>".$i."</option>";
     }
          else{
              echo "<option>".$i."</option>";
     }
}

echo "</select>月　";

echo "<input type='submit' name='act' name='查詢'>";
echo "</td></tr></table>";
echo "</form>";



//分第2欄

//顯示年曆
echo "<center><font color='blue' face='標楷體' size='4'><b>".$year."年 年曆表</b></font><p>";
echo "<table width='100%'>";
for ($i=1;$i<13;$i++){
        if ($i % 3==1) echo "<tr>";
        echo "<td valign='top'>";
        echo "<table bgcolor='#ffffff' border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='85%'>";
        $temp_now_month=date("m");
        if (substr($temp_now_month,0,1)=="0") $temp_now_month=substr($temp_now_month,1);//將$temp月份轉成 0~12
        if ($i==$temp_now_month and $year==date("Y"))//尋找當月
                echo "<tr bgcolor='#ff0000'><td colspan='7' align='center'>";
        else
                echo "<tr bgcolor='#00ffff'><td colspan='7' align='center'>";
        echo "<b>".$i."月</b>";
        echo "</td></tr>";
        echo "<tr bgcolor='#6363d3'>";
        echo "<td align='center'><font color='#ffffff' size='2'>日</font></td>";
        echo "<td align='center'><font color='#ffffff' size='2'>一</font></td>";
        echo "<td align='center'><font color='#ffffff' size='2'>二</font></td>";
        echo "<td align='center'><font color='#ffffff' size='2'>三</font></td>";
        echo "<td align='center'><font color='#ffffff' size='2'>四</font></td>";
        echo "<td align='center'><font color='#ffffff' size='2'>五</font></td>";
        echo "<td align='center'><font color='#ffffff' size='2'>六</font></td>";
        echo "</tr>";
        //分週列出日曆
        $d="01";
        $week=0;
        while(checkdate($i,$d,$year)){
                if ($week==0) echo "<tr>";//週日
                if (strlen($d)<2) $d="0".$d;//確保$d 為 01~月底
                $temp=getdate(mktime(0,0,0,$i,$d,$year));
                if ($d==1){//當月第一日，補空格

                        for ($j=1;$j<$temp["wday"]+1;$j++){
                                echo "<td></td>";
                        }
                        $week=$week+$temp["wday"];
                }
                if ($year.$i.$d==date("Y").$temp_now_month.date("d"))//尋找本日
                        echo "<td align='center' bgcolor='red'>";
                elseif ($week==0 || $week==6)
                        echo "<td align='center' bgcolor='#f0b4f1'>";
                else
                        echo "<td align='center'>";
                        
                //印出日期
                echo "<font size='2'>".$d."<font>";
                echo "</td>";
                
                $d=$d+1;
                $week=$week+1;
                if ($week==7){//週末
                        echo "</tr>";
                        $week=0;
                }
        }
        if ($week==0) echo "</tr>";
        echo "</table>";

        echo "</td>";
        if ($i % 3 ==0) echo "</tr><tr><td colspan='3'><p>　</td></tr>";

}
//分欄結束註記
echo "</table>";
echo "</body>";

?>
