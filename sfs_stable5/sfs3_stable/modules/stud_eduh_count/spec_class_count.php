<?php
// $Id: spec_class_count.php 8912 2016-06-21 04:04:39Z qfon $

include "config.php";

//取得選擇條件
$select_year_seme=$_POST["select_year_seme"];//學年度
$year=get_curr_year($select_year_seme);//取得選擇之學年，如92
$semester=get_curr_seme($select_year_seme);//取得選擇之學期，如1
if(empty($year))$year=curr_year();//預設為本年度
if(empty($semester))$semester=curr_seme();//預設為本學期

$stud_spe_kind=$_POST["stud_spe_kind"];//取得特殊班類別
if(empty($stud_spe_kind)){
  $stud_spe_kind=0;
  $show_word="類別：不分";
}
  else{
  $spe_kind=stud_spe_kind();
  $show_word="類別：".$spe_kind[$stud_spe_kind];
  }
$stud_spe_class_kind=$_POST["stud_spe_class_kind"];//取得特殊班班別
if(empty($stud_spe_class_kind)){
  $stud_spe_class_kind=0;
  $show_word.="　　班別：不分";
}
else{
  $spe_class_kind=stud_spe_class_kind();
  $show_word.="　　班別：".$spe_class_kind[$stud_spe_class_kind];
}


//依條件設定尋找資料關鍵字

$find_spe="";
if ($stud_spe_kind>0)
   $find_spe.=" and stud_spe_kind='".intval($stud_spe_kind)."'";
if ($stud_spe_class_kind>0)
   $find_spe.=" and stud_spe_class_kind='".intval($stud_spe_class_kind)."'";

if ($stud_spe_kind<=0 || $stud_spe_class_kind<=0)
{
$find_spe.=" and (stud_spe_kind>'0' or stud_spe_class_kind>'0')";
}
/*
if ($stud_spe_class_kind==0)//決定是否要尋找特殊班班別
   $find_spe="a.stud_spe_kind='$stud_spe_kind'";//不
  else
   $find_spe="a.stud_spe_kind='$stud_spe_kind' and a.stud_spe_class_kind='$stud_spe_class_kind'";//要
*/

$find_year_seme=sprintf("%03d%d",$year,$semester);//格式化學期成4位數，如0911

if (!empty($_POST["download"])) save_csv($find_year_seme,$find_spe,$show_word);//串流送出資料

head("特殊班學生查詢");

echo make_menu($menu_p);

$help_text="
本程式主要依據學生之[基本資料]中[特殊班班別]、[特殊班類別]欄位為分類之依據。";
$help=&help($help_text);
echo $help;

//選單

echo "<form method='post' action='".basename($_SERVER["PHP_SELF"])."'>";
echo "<table><tr><td align='center'><font size='2'>學年度</font><br>";
        //底下使用select_year_seme 顯示學年度學期下拉選單
        $class_seme_p = get_class_seme(); //學年度
        echo "<select name='select_year_seme' onchange='this.form.submit()'>";
        while (list($tid,$tname)=each($class_seme_p)){
        	if ($tid=="0".$year.$semester)
              		echo "<option value='".$tid."' selected>".$tname."</option>";//$tid如"0921"
              	else
              		echo "<option value='".$tid."'>".$tname."</option>";
        }
        echo "</select></td>";
	//特殊班類別
        echo "<td width='220' align='center'><font size='2'>特殊班類別(空白代表不限)</font><br>";
	$sel1 = new drop_select(); //選單類別
	$sel1->s_name = "stud_spe_kind"; //選單名稱
	$sel1->id = intval($stud_spe_kind);
	$sel1->arr = stud_spe_kind(); //內容陣列
	$sel1->has_empty =true;
	$sel1->is_submit = true;
	$sel1->do_select();
        echo "</td>";
	//特殊班班別
        echo "<td align='center'><font size='2'>特殊班班別(空白代表不限)</font><br>";
	$sel1 = new drop_select(); //選單類別
	$sel1->s_name = "stud_spe_class_kind"; //選單名稱
	$sel1->id = intval($stud_spe_class_kind);
	$sel1->arr = stud_spe_class_kind(); //內容陣列
	$sel1->has_empty =true;
	$sel1->is_submit = true;
	$sel1->do_select();
        echo "</td><tr><td>";
        //下載資料按鈕
        echo "<input type='submit' name='download' value='下載詳細資料(xls)'></td>";

        echo "</tr></table></form>";

//開始尋找
$sql_select = "select a.stud_study_cond ,a.stud_id,a.student_sn ,a.stud_name, a.stud_sex, a.stud_person_id, a.stud_birthday, a.stud_addr_1, a.stud_tel_1, a.stud_tel_2, b.seme_num, b.seme_class from stud_base a, stud_seme b where a.student_sn=b.student_sn $find_spe and b.seme_year_seme='$find_year_seme' order by b.seme_class,b.seme_num";
$record=$CONN->Execute($sql_select) or die($sql_select);
if ($record->RecordCount()<1){
   echo "找不到資料，請重新設定尋找條件！";
   exit;
}

$move_kind_arr=study_cond();//本行新增 by misser 93.10.20

echo "<font color='blue'>總計符合人數：".$record->RecordCount()."人</font>";
echo "<table border='1' cellpadding='2' cellspacing='0'  bordercolorlight='#333354' bordercolordark='#FFFFFF' width='100%'>";
echo "<tr class='main_body'>";
echo "<td><font size='2'>班級</font></td>";
echo "<td><font size='2'>座號</font></td>";
echo "<td><font size='2'>姓名</font></td>";
echo "<td><font size='2'>性別</font></td>";
echo "<td><font size='2'>學號</font></td>";
echo "<td><font size='2'>身分證字號</font></td>";
echo "<td><font size='2'>父親</font></td>";
echo "<td><font size='2'>母親</font></td>";
echo "<td><font size='2'>生日</font></td>";
echo "<td><font size='2'>地址</font></td>";
echo "<td><font size='2'>電話1</font></td>";
echo "<td><font size='2'>電話2</font></td>";
echo "<td><font size='2'>狀況</font></td>";
echo "</tr>";
//列出資料
while ($array_stud = $record->FetchRow()) {
      $array_stud[seme_class]=(substr($array_stud[seme_class],0,1)>6)?$array_stud[seme_class]=$array_stud[seme_class]-600:$array_stud[seme_class];
      $array_stud[stud_sex]=($array_stud[stud_sex]=='1')?"男":"女";
      $parents=find_parents($array_stud[student_sn]);//找出父母
      $temp_bgcolor=($temp_bgcolor=="#EFE0ED")?"#ffffff":"#EFE0ED";//間隔變換背景顏色
      echo "<tr bgcolor='$temp_bgcolor'>";
      if ($array_stud[stud_study_cond]==0) $temp_color="";
         else $temp_color="color='red'";
      echo "<td><font size='2' ".$temp_color.">$array_stud[seme_class]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[seme_num]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_name]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_sex]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_id]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_person_id]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$parents[0]</font></td>";//父親
      echo "<td><font size='2' ".$temp_color.">$parents[1]</font></td>";//母親
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_birthday]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_addr_1]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_tel_1]</font></td>";
      echo "<td><font size='2' ".$temp_color.">$array_stud[stud_tel_2]</font></td>";
      echo "<td><font size='2' ".$temp_color.">".$move_kind_arr[$array_stud[stud_study_cond]]."</font></td>";
      echo "</tr>";
}
echo "</tr></table>";

foot();
function find_parents($student_sn,$kind){
	global $CONN ;
	    $student_sn=intval($student_sn);
        $sql_select = "select fath_name, moth_name from stud_domicile where student_sn='$student_sn'";
        $recordSet=$CONN->Execute($sql_select) or die($sql_select);
        return $recordSet->FetchRow();
}
function save_csv($find_year_seme,$find_spe,$show_word){
       	global $CONN;

        //$select_year_seme=(strlen($year.$semester)==4) ? $year.$semester : "0".$year.$semester;//設定欲搜尋之學年度及學期
        //輸出
   	$filename="spec_".$find_year_seme.".xls";
    	header("Content-disposition: filename=$filename");
    	//header("Content-type: application/octetstream ; Charset=Big5");
    	header("Content-type: application/octetstream");
    	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
    	header("Expires: 0");

        //開始尋找
        $sql_select = "select a.stud_study_cond ,a.stud_id,a.student_sn ,a.stud_name, a.stud_sex, a.stud_person_id, a.stud_birthday, a.stud_addr_1, a.stud_tel_1, a.stud_tel_2, b.seme_num, b.seme_class from stud_base a, stud_seme b where a.student_sn=b.student_sn $find_spe and b.seme_year_seme='$find_year_seme' order by b.seme_class,b.seme_num";
        $record=$CONN->Execute($sql_select) or die($sql_select);
        if ($record->RecordCount()<1){
           echo "找不到資料，請重新設定尋找條件！";
           exit;
        }

        $move_kind_arr=study_cond();//本行新增 by misser 93.10.20

        //秀出各班符合資料
        echo "<table border='1' cellpadding='2' cellspacing='0'  bordercolorlight='#333354' bordercolordark='#FFFFFF' width='100%'>";
        echo "<tr class='main_body'>";
        echo "<td colspan='13'><font color='blue'>".$find_year_seme."學生資料查詢：</font>";
        echo $show_word;
        echo "</td>";
        echo "</tr>";
        echo "<tr class='main_body'>";
        echo "<td colspan='13'><font color='green'>總計符合人數：".$record->RecordCount()."人</font></td>";
        echo "</tr>";
        echo "<td><font size='2'>班級</font></td>";
        echo "<td><font size='2'>座號</font></td>";
        echo "<td><font size='2'>姓名</font></td>";
        echo "<td><font size='2'>性別</font></td>";
        echo "<td><font size='2'>學號</font></td>";
        echo "<td><font size='2'>身分證字號</font></td>";
        echo "<td><font size='2'>父親</font></td>";
        echo "<td><font size='2'>母親</font></td>";
        echo "<td><font size='2'>生日</font></td>";
        echo "<td><font size='2'>地址</font></td>";
        echo "<td><font size='2'>電話1</font></td>";
        echo "<td><font size='2'>電話2</font></td>";
        echo "<td><font size='2'>狀況</font></td>";
        echo "</tr>";
        //列出資料
        while ($array_stud = $record->FetchRow()) {
              $array_stud[seme_class]=(substr($array_stud[seme_class],0,1)>6)?$array_stud[seme_class]=$array_stud[seme_class]-600:$array_stud[seme_class];
              $array_stud[stud_sex]=($array_stud[stud_sex]=='1')?"男":"女";
              $parents=find_parents($array_stud[student_sn]);//找出父母
              $temp_bgcolor=($temp_bgcolor=="#EFE0ED")?"#ffffff":"#EFE0ED";//間隔變換背景顏色
              echo "<tr bgcolor='$temp_bgcolor'>";
              if ($array_stud[stud_study_cond]==0) $temp_color="";
                 else $temp_color="color='red'";
              echo "<td><font size='2' ".$temp_color.">$array_stud[seme_class]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[seme_num]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_name]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_sex]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_id]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_person_id]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$parents[0]</font></td>";//父親
              echo "<td><font size='2' ".$temp_color.">$parents[1]</font></td>";//母親
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_birthday]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_addr_1]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_tel_1]</font></td>";
              echo "<td><font size='2' ".$temp_color.">$array_stud[stud_tel_2]</font></td>";
              echo "<td><font size='2' ".$temp_color.">".$move_kind_arr[$array_stud[stud_study_cond]]."</font></td>";
              echo "</tr>";
        }
        echo "</tr></table>";
        exit;
}

?>
