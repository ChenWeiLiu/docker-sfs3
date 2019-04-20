<?php
                                                                                                                             
// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";

//取得模組設定
$m_arr = get_sfs_module_set("stud_eduh");
extract($m_arr, EXTR_OVERWRITE);

sfs_check();

//選單

$menu_p = array("eduh_count.php"=>"查_輔導及訪談紀錄","stud_seme_talk2.php"=>"補_訪談紀錄");

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
//選項顯示行數
$chk_cols = 5;
//預設第一個開啟班級
$default_begin_class = "601";
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

function stud_class_err() {

	echo "<center><h2>本項作業須具導師資格</h2>";
	echo "<h3>若有疑問請洽 系統管理員</h3></center>";
}


//計算二日期相差的天數
function daydiff($day1,$day2){//$day1,2為00000000格式
	//$day1= substr($day1,0,8);
	$day1=strtotime($day1);
	$day2=strtotime($day2);
	return (($day2-$day1)/(24*60*60));
}
//取得星期
function get_week($selectday=""){//$selectday為00000000格式
	if ($selectday=="")
		$selectday=getdate();
	else{
		$y=substr($selectday,0,4);
		$m=substr($selectday,4,2);
		$d=substr($selectday,6,2);
		$selectday=mktime(0,0,0,$m,$d,$y);
	}
	$selectday=getdate("$selectday");

	return $selectday["wday"];
}
//取得星期(中)
function get_c_week($selectday=""){//$selectday為00000000格式
	if ($selectday=="")
		$selectday=getdate();
	else{
		$y=substr($selectday,0,4);
		$m=substr($selectday,4,2);
		$d=substr($selectday,6,2);
		$selectday=mktime(0,0,0,$m,$d,$y);
	}
	$selectday=getdate("$selectday");
	switch ($selectday["wday"]){
		case '0':
			$week="日";
			break;
		case '1':
			$week="一";
			break;
		case '2':
			$week="二";
			break;
		case '3':
			$week="三";
			break;
		case '4':
			$week="四";
			break;
		case '5':
			$week="五";
			break;
		case '6':
			$week="六";
			break;
		default:
			$week="???";
	}
	return $week;
}


//比對日期是否符合出現原則
//參數：週期起始,週期結束,出現年,出現月,出現日,出現星期,比對日,種類
function check_date($restart_day,$restart_end,$sele_year,$sele_month,$sele_day,$sele_week,$test_day,$kind=""){

   $sele_time=$sele_year.sprintf("%02d",$sele_month).sprintf("%02d",$sele_day);

   //未循環且未符合出現日期
   if ($kind=='0' and $sele_time!=$test_day) return false;

   $restart_day=substr($restart_day,0,4).substr($restart_day,5,2).substr($restart_day,8,2);
   $restart_end=substr($restart_end,0,4).substr($restart_end,5,2).substr($restart_end,8,2);

   //檢查是否符合出現日期

   if ($sele_time==$test_day)
      return true;

   //檢查是否在循環外

   if ($restart_day!="0000-00-00"){
      if (daydiff($restart_day,$test_day)< 0) return false;
   }

   if ($restart_end!="0000-00-00"){
      if (daydiff($test_day,$restart_end) < 0) return false;
   }

//   if ((daydiff($restart_day,$test_day)< 0 and $restart_day!="0000-00-00") or (daydiff($test_day,$restart_end)<0 and $restart_end!="0000-00-00"));
//      return false;

   //比對循環
   switch($kind){

      case '0'://每週只
           if ($test_day==$sele_time)
              return true;
           else
              return false;
           break;

      case 'w'://每週止
           if (get_week($test_day)==$sele_week)
              return true;
           else
              return false;
           break;
      case 'd'://每月該日止
           if (substr($test_day,6,2)==$sele_day)
              return true;
           else
              return false;
           break;
      case 'md'://每年該日
           if (substr($test_day,4,2)==$sele_month and substr($test_day,6,2)==$sele_day)
              return true;
           else
              return false;
           break;
      default:
           echo false;
   }
}

?>
