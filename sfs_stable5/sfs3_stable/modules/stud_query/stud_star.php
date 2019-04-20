<?php
include "stud_query_config.php";


//取得星座別
$star_type=$_POST[type];

//定義星座陣列
if($star_type<>'十三星座')
{     //內定是12星座
      $type_select="<select size='1' name='type' onchange='this.form.submit()'><option selected>十二星座</option><option>十三星座</option></select>";
      $star_name=array("摩羯<br>12/23<br>|<br>01/20","水瓶<br>01/21<br>|<br>02/19","雙魚<br>02/20<br>|<br>03/20","牡羊<br>03/21<br>|<br>04/20",
           "金牛<br>04/21<br>|<br>05/21","雙子<br>05/22<br>|<br>06/21","巨蟹<br>06/22<br>|<br>07/23","獅子<br>07/24<br>|<br>08/23",
           "處女<br>08/24<br>|<br>09/23","天秤<br>09/24<br>|<br>10/23","天蠍<br>10/24<br>|<br>11/22","射手<br>11/23<br>|<br>12/22",
           "班級<br>人數");
           $star_date=array(120,219,320,420,521,621,723,823,923,1023,1122,1222,1231);
      } else {
            $type_select="<select size='1' name='type' onchange='this.form.submit()'><option>十二星座</option><option selected>十三星座</option></select>";
      $star_name=array("射手<br>12/18<br>|<br>01/19","山羊<br>01/20<br>|<br>02/17","水瓶<br>02/18<br>|<br>03/12","雙魚<br>03/13<br>|<br>04/18",
           "白羊<br>04/19<br>|<br>05/13","金牛<br>05/14<br>|<br>06/22","雙子<br>06/23<br>|<br>07/21","巨蟹<br>07/22<br>|<br>08/10",
           "獅子<br>08/11<br>|<br>09/16","處女<br>09/17<br>|<br>10/31","天秤<br>11/01<br>|<br>11/23","天蝎<br>11/24<br>|<br>11/29",
           "蛇夫<br>11/30<br>|<br>12/17","班級<br>人數");
           $star_date=array(119,217,312,418,513,622,721,810,916,1031,1123,1129,1217,1231);
              }

$star_arr=array();
$star_total=array();
$star_len=count($star_name)-1;

//取得學生生日資料
$sql="select left(curr_class_num,3) as class_num,DATE_FORMAT(stud_birthday,'%m%d') as md,count(*) as total
      from stud_base where stud_study_cond=0 group by class_num,md";

$recordSet=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);

//填入$star_arr陣列
while ($rd=$recordSet->FetchRow()) {
       for($i=0;$i<=$star_len;$i++)
       {
                if($rd[md]<=$star_date[$i]) { $star_arr[$rd[class_num]][$i]+=$rd[total]; $star_total[$i]+=$rd[total]; break; }
               }
       //最後與最初相加
       if($i=$star_len)
       {
       $star_arr[$rd[class_num]][0]+=$star_arr[$rd[class_num]][$i];
       $star_arr[$rd[class_num]][$i]=0;

       $star_total[0]+=$star_total[$i];
       $star_total[$i]=0;
       }
}

//print_r($star_arr);


// 取出班級陣列
$class_base = class_base($work_year_seme);


//取得抬頭
for($i=0;$i<=$star_len;$i++)
{
      $data.="<td align=center>$star_name[$i]</td>";
}
$data="<table border=1 width=100% bordercolor=#008000 cellspacing=0 cellpadding=0><form name=\"year_form\" method=\"post\" action=\"$_SERVER[PHP_SELF]\"><tr bgcolor=#ccffcc><td align=center>$type_select<BR><BR>班級名稱</td>$data</tr>";

//所有資料表格化
foreach($star_arr as $key => $value) {
       $rowdata="<td align=center>$class_base[$key]</td>";
       for($j=0;$j<=$star_len;$j++)
       {
             //班級人數合計
             if($j<$star_len) { $value[$star_len]+=$value[$j]; }

             $rowdata.="<td align=center>".($value[$j]?$value[$j]:0);

       }
       $data.="<tr>$rowdata</tr>";
}


//取得星座資料合計
for($i=0;$i<=$star_len;$i++)
{
      if($i<$star_len) { $star_total[$star_len]+=$star_total[$i]; }
      $data_total.="<td align=center>".($star_total[$i]?$star_total[$i]:'　')."</td>";
}
$data_total="<tr bgcolor=#ccccff><td align=center>星座統計</td>$data_total</tr>";

head("學生生日星座統計");
print_menu($menu_p);
echo "$data $data_total</form></table>";
foot();




 ?>
