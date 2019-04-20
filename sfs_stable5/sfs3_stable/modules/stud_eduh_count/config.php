<?php
                                                                                                                             
// $Id: config.php 6596 2011-10-19 06:55:54Z infodaes $

include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";

sfs_check();

//選單

$menu_p = array("index2.php"=>"舊式輔導紀錄表","eduh_count.php"=>"輔導紀錄填寫情形","eduh_check.php"=>"輔導紀錄資料完整性檢查","talk_count.php"=>"訪談紀錄填寫情形","home_count.php"=>"家庭狀況統計","spec_class_count.php"=>"特殊班學生查詢");

//取得目前學年度(第一位不含0)
function get_curr_year($year_seme){
         if (empty($year_seme))
               $get_year=curr_year();
         else
               if (substr($year_seme,0,1)!="0")
                  $get_year=substr($year_seme,0,strlen($year_seme)-1);
               else
                  $get_year=substr($year_seme,1,strlen($year_seme)-2);
         return $get_year;
}
//取得目前學期(1或2)
function get_curr_seme($year_seme){
         if (empty($year_seme))
               $get_seme=curr_seme();
         else
               $get_seme=substr($year_seme,-1);
         return $get_seme;
}
//將學生家庭紀錄的代號轉成文字輸出
function get_sfs_text($t_kind,$d_id){
         global $CONN;
         $d_id=($d_id=='0')?'-1':$d_id;
         //比對sfs_text中，符合之t_name
         $sql_select = "select t_name from sfs_text where t_kind='$t_kind' and d_id='$d_id'";
	 $record=$CONN->Execute($sql_select) or die($sql_select);
         $num=$record->RecordCount();
         if ($num<1) return " ";//如果未找到，則傳回空白
	 $sss = $record->FetchRow();
	 return $sss[t_name];
}
//以下為紀錄表用
 //預設第一個開啟年級
 $default_begin_class = 6;
 //左選單設定顯示筆數
 $gridRow_num = 16;
 //左選單底色設定
 $gridBgcolor="#DDDDDC";
//左選單男生顯示顏色
 $gridBoy_color = "blue";
 //左選單女生顯示顏色
 $gridGirl_color = "#FF6633";
//新增按鈕名稱
$newBtn = " 新增資料 ";
//修改按鈕名稱
$editBtn = " 確定修改 ";
//刪除按鈕名稱
$delBtn = " 確定刪除 ";
//確定新增按鈕名稱
$postBtn = " 確定新增 ";
$editModeBtn = " 修改模式 ";
$browseModeBtn = " 瀏覽模式 ";

?>
