<?php
// $Id: stud_pass_list.php 8246 2014-12-16 05:25:12Z smallduh $

include "config.php";

//認證
sfs_check();

$find_year_seme=sprintf("%03d%d",curr_year(),curr_seme());//格式化學期成4位數，如0911

if (!empty($_POST["download"])) save_csv($find_year_seme,$find_spe,$show_word);//串流送出資料

head("全校學生密碼查詢");
echo make_menu($menu_p);

//下載資料按鈕
echo "<form method='post' action='".basename($_SERVER["PHP_SELF"])."'>";
echo "<table><tr><td>";
echo "<input type='submit' name='download' value='下載詳細資料(xls)'></td>";
echo "</tr></table></form>";

//開始尋找
$sql_select = "select a.stud_study_cond ,a.stud_id ,a.stud_name, a.email_pass, b.seme_num, b.seme_class from stud_base a, stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$find_year_seme' order by b.seme_class,b.seme_num";
$record=$CONN->Execute($sql_select) or die($sql_select);
if ($record->RecordCount()<1){
   echo "找不到資料！";
   exit;
}

$move_kind_arr=study_cond();//本行新增 by misser 93.10.20
$class_array=class_base();

echo "<font color='blue'>總計符合人數：".$record->RecordCount()."人</font>";
echo "<table border='1' cellpadding='2' cellspacing='0'  bordercolorlight='#333354' bordercolordark='#FFFFFF' width='100%'>";
echo "<tr class='main_body'>";
echo "<td><font size='2'>班級</font></td>";
echo "<td><font size='2'>座號</font></td>";
echo "<td><font size='2'>姓名</font></td>";
echo "<td><font size='2'>學號</font></td>";
echo "<td><font size='2'>密碼</font></td>";
echo "<td><font size='2'>狀況</font></td>";
echo "</tr>";
//列出資料
while ($array_stud = $record->FetchRow()) {
      //$array_stud[seme_class]=(substr($array_stud[seme_class],0,1)>6)?$array_stud[seme_class]=$array_stud[seme_class]-600:$array_stud[seme_class];
      
      $temp_bgcolor=($temp_bgcolor=="#EFE0ED")?"#ffffff":"#EFE0ED";//間隔變換背景顏色
      echo "<tr bgcolor='$temp_bgcolor'>";
      if ($array_stud[stud_study_cond]==0) $temp_color=""; else $temp_color="color='red'";
	  $class_id=$array_stud[seme_class];
	  
      echo "<td><font size='2' ".$temp_color.">$class_array[$class_id]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[seme_num]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_name]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_id]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[email_pass]</font></td>";
      echo "<td><font size='2' ".$temp_color.">".$move_kind_arr[$array_stud[stud_study_cond]]."</font></td>";
      echo "</tr>";
}
echo "</tr></table>";

foot();
function save_csv($find_year_seme){
       	global $CONN;

        //$select_year_seme=(strlen($year.$semester)==4) ? $year.$semester : "0".$year.$semester;//設定欲搜尋之學年度及學期
        //輸出
   	$filename="pass_".$find_year_seme.".xls";
    	header("Content-disposition: filename=$filename");
    	//header("Content-type: application/octetstream ; Charset=Big5");
    	header("Content-type: application/octetstream");
    	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
    	header("Expires: 0");

        //開始尋找
        $sql_select = "select a.stud_study_cond ,a.stud_name,a.stud_id ,a.email_pass, b.seme_num, b.seme_class from stud_base a, stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$find_year_seme' order by b.seme_class,b.seme_num";
        $record=$CONN->Execute($sql_select) or die($sql_select);
        if ($record->RecordCount()<1){
           echo "找不到資料，請重新設定尋找條件！";
           exit;
        }

        $move_kind_arr=study_cond();//本行新增 by misser 93.10.20

        //秀出各班符合資料
	echo "<html><head><meta http-equiv='content-type' content='text/html; charset=big5'></head><body>";
        echo "<table border='1' cellpadding='2' cellspacing='0'  bordercolorlight='#333354' bordercolordark='#FFFFFF' width='100%'>";
        echo "<tr class='main_body'>";
        echo "<td colspan='6'><font color='blue'>".$find_year_seme."學生資料查詢：</font>";
        echo $show_word;
        echo "</td>";
        echo "</tr>";
        echo "<tr class='main_body'>";
        echo "<td colspan='6'><font color='green'>總計符合人數：".$record->RecordCount()."人</font></td>";
        echo "</tr>";
        echo "<td><font size='2'>班級</font></td>";
        echo "<td><font size='2'>座號</font></td>";
        echo "<td><font size='2'>姓名</font></td>";
        echo "<td><font size='2'>學號</font></td>";
        echo "<td><font size='2'>密碼</font></td>";
        echo "<td><font size='2'>狀況</font></td>";
        echo "</tr>";
        //列出資料
        while ($array_stud = $record->FetchRow()) {
              $array_stud[seme_class]=(substr($array_stud[seme_class],0,1)>6)?$array_stud[seme_class]=$array_stud[seme_class]-600:$array_stud[seme_class];
              $temp_bgcolor=($temp_bgcolor=="#EFE0ED")?"#ffffff":"#EFE0ED";//間隔變換背景顏色
              echo "<tr bgcolor='$temp_bgcolor'>";
              if ($array_stud[stud_study_cond]==0) $temp_color=""; else $temp_color="color='red'";
			  $class_array=class_base();
			  $class_id=$array_stud[seme_class];
              echo "<td><font size='2' ".$temp_color.">$class_array[$class_id]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[seme_num]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_name]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_id]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[email_pass]</font></td>";
              echo "<td><font size='2' ".$temp_color.">".$move_kind_arr[$array_stud[stud_study_cond]]."</font></td>";
              echo "</tr>";
        }
        echo "</tr></table></body></html>";
        exit;
}

?>
