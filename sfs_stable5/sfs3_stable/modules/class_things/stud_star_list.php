<?php

include "config.php";

//取得星座別
$star_type=$_POST[type];

//定義星座陣列
if($star_type<>'十三星座')
{     //內定是12星座
      $type_select="<select size='1' name='type' onchange='this.form.submit()'><option selected>十二星座</option><option>十三星座</option></select>";
      $star_name=array("摩羯<br>12/23-01/20","水瓶<br>01/21-02/19","雙魚<br>02/20-03/20","牡羊<br>03/21-04/20",
           "金牛<br>04/21-05/21","雙子<br>05/22-06/21","巨蟹<br>06/22-07/23","獅子<br>07/24-08/23",
           "處女<br>08/24-09/23","天秤<br>09/24-10/23","天蠍<br>10/24-11/22","射手<br>11/23-12/22",
           "班級<br>人數");
           $star_date=array(120,219,320,420,521,621,723,823,923,1023,1122,1222,1231);
      } else {
            $type_select="<select size='1' name='type' onchange='this.form.submit()'><option>十二星座</option><option selected>十三星座</option></select>";
      $star_name=array("射手<br>12/18-01/19","山羊<br>01/20-02/17","水瓶<br>02/18-03/12","雙魚<br>03/13-04/18",
           "白羊<br>04/19-05/13","金牛<br>05/14-06/22","雙子<br>06/23-07/21","巨蟹<br>07/22-08/10",
           "獅子<br>08/11-09/16","處女<br>09/17-10/31","天秤<br>11/01-11/23","天蝎<br>11/24-11/29",
           "蛇夫<br>11/30-12/17","班級<br>人數");
           $star_date=array(119,217,312,418,513,622,721,810,916,1031,1123,1129,1217,1231);
              }

$star_arr=array();
$star_total=array();
$star_len=count($star_name)-1;

$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);

//取得學生生日資料
$sql="select stud_name,DATE_FORMAT(stud_birthday,'%m%d') as md
      from stud_base where stud_study_cond=0 and curr_class_num like '".$class_name[0]."%' order by md";

$recordSet=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);

head("學生生日星座列表");
print_menu($menu_p);


//填入$star_arr陣列
while ($rd=$recordSet->FetchRow()) {
       for($i=0;$i<=$star_len;$i++)
       {
       if($rd[md]<=$star_date[$i]) { $star_arr[$i].="$rd[stud_name]($rd[md])，"; $star_total[$i]+=1; break; }
       }
       //最後與最初相加
       if($i=$star_len)
       {
       $star_arr[0].=$star_arr[$i];
       $star_total[0]+=$star_total[$i];
       }
}

//取得抬頭
$data="<table border=1 width=100% bordercolor=#008000 cellspacing=0 cellpadding=3>
        <form name=\"year_form\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
        <tr bgcolor=#ccffcc>
        <td align=center width=120>星座名稱</td><td align=center width=70>人數統計</td><td align=center>$class_name[1]學生生日 $type_select 列表</td>
        </tr>";

//將資料列表
for($i=0;$i<$star_len;$i++)
{
       $data.="<tr><td align=center>$star_name[$i]</td><td align=center>".($star_total[$i]?$star_total[$i]:0)."</td><td>".($star_arr[$i]?substr($star_arr[$i],0,-2):'　')."</td></tr>";
}

echo "$data</form></table>";
foot();

?>
