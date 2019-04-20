<?php
/* 取得設定檔 */
include "config.php";

//取得模組設定
$m_arr = &get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

if ($_GET['api_key']!=$api_key) {
  $main[][]="API Key 錯誤!";
  exit(json_encode($main));
}

$month=($_GET['month'])?$_GET[month]:date("m");
$year=($_GET['year'])?$_GET[year]:date("Y");
$day=($_GET['day'])?$_GET[day]:date("d");

switch ($_GET['act']) {
 //某日
 case 'getOneDayThing':
 $main=getOneDayThing($year,$month,$day);
 //轉碼 把二維陣列資料轉成 utf-8 , 並以base64編碼
 $main=array_base64_encode($main);
 break;
 //某月列表
 case 'getMonthList':
  $main=getMonthList($year,$month,$day);
  //轉碼 把二維陣列資料轉成 utf-8 , 並以base64編碼
  $main=array_base64_encode($main);
 break;
 //某月行事曆
 case 'getMonthView':
  $row=getMonthView($year,$month,$day); //三維陣列
  //轉碼 把二維陣列資料轉成 utf-8 , 並以base64編碼
  $main=array();
  foreach ($row as $k=>$v) {
   $main[$k]=array_base64_encode($v);
  }  
 break;
}
//echo "<pre>";
// print_r($main);
// exit();
 
//送出
exit(json_encode($main));

/***********************************************************************************/
//取得某月所有事件 二維陣列
function &getMonthList($year,$month,$day){
	global $CONN;
	$mounth_num=date("t",mktime(0,0,0,$month,$day,$year));
	$AllThings=array();
	$ii=0;
	for($i=1;$i<=$mounth_num;$i++){
		$data=getOneDayThing($year,$month,$i);
	  foreach($data as $OneThing) {
      $ii++;
      foreach ($OneThing as $k=>$v) {
      	$AllThings[$ii][$k]=$v;      
      } // end foreach One	  
	  } //end foreach data
	} // end for
	return $AllThings;
}

//取得某月所有事件 三維陣列
function &getMonthView($year,$month,$day){
	global $CONN;
	$mounth_num=date("t",mktime(0,0,0,$month,$day,$year));
	$AllThings=array();
	$ii=0;
	for($i=1;$i<=$mounth_num;$i++){
		$data=getOneDayThing($year,$month,$i);
	  foreach($data as $OneThing) {
      $ii++;
      foreach ($OneThing as $k=>$v) {
      	$AllThings[$i][$ii][$k]=$v; //以日期為第一索引      
      } // end foreach One	  
	  } //end foreach data
	} // end for
	return $AllThings;
}

//取得某日事件 二維陣列
function getOneDayThing($year,$month,$day){
	global $CONN,$MODULE_TABLE_NAME;
 //星期
 $w=date ("w", mktime(0,0,0,$month,$day,$year));
	
 $sql_select = "
	select * from $MODULE_TABLE_NAME[0]
	where 
	(
		(year='$year' and month='$month' and day='$day') or
		(
			(restart='md' and month='$month' and day='$day') or 
			(restart='d' and day='$day') or 
			(restart='w' and week='$w')
		) 
	) and (kind=0)";
	$sql_select .= " order by time";
	$res = $CONN->Execute($sql_select) or die("SQL Error, query=".$sql_select);
	
	$row=$res->getRows();
	
	return $row;	
	
}

//將陣列編碼 , 二維陣列
function array_base64_encode($arr) {
  $B_arr=array();
  
  foreach ($arr as $K1=>$V1) {
		foreach ($V1 as $K2=>$V2) {
		 
     $B_arr[$K1][$K2]=base64_encode(addslashes($V2));
    }
  }
  	return $B_arr;
}
