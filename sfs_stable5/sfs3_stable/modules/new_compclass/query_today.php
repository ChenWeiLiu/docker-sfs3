<?php

// $Id: query.php 6408 2011-04-18 03:49:04Z infodaes $

include "config.php";
include "module-upgrade.php";

//sfs_check();
$template_file = dirname (__file__)."/templates/chc_query_room.htm";
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
//取得模組設定
$m_arr = &get_sfs_module_set('course_paper');
extract($m_arr, EXTR_OVERWRITE);
if ($midnoon=='') $midnoon=5;

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
 
//執行動作判斷
$main=&main_query_form($sel_year,$sel_seme,$room,$teacher_sn,$page);


//秀出網頁
head("今日各節次所有專科教室使用列表");
echo $main;
foot();

/*******************************************************/
//相關函數
/*******************************************************/
//主要查詢畫面

function &main_query_form($sel_year,$sel_seme,$room,$teacher_sn,$page){
   global $today,$CONN,$SFS_PATH_HTML,$school_menu_p,$weekN7,$weekN,$daymm,$dayww,$sunday,$saturday,$midnoon;
   include_once "../../include/chi_page2.php";

   //--處理換頁
   $size=20;  //每頁20筆
   if($page=='') {
      $page=0;
   }
   //debug_msg("第".__LINE__."行 _REQUEST ", $_REQUEST);
   if($room=='all'){ 
      $qStr="WHERE date='".$today."'";
   }elseif($room!=''){
      $qStr=" WHERE room='".$room."' AND date='".$today."'";
   }
	$tool_bar=&make_menu($school_menu_p);
	$class_arr=class_base();

	//找出教室列表

	$room_sel=&get_room($sel_year,$sel_seme,"room",$room,jumpMenu);
	$sel1=new drop_select();
	$sel1->s_name="seme_class";
	$sel1->id=$_POST[seme_class];
	$sel1->arr=class_base();
	$sel1->has_empty=false;
	$sel1->is_submit=true;
	$chk[intval($_POST[class_kind])]="checked";
	$tol=0;

	if($room!=''){
      $SQL="select crsn from course_room ".$qStr;
      $rs=&$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
      $tol=$rs->RecordCount();
	}
	//debug_msg("第".__LINE__."行 tol ", $tol);
   $start=$page*$size;
   if($room!=''){
      $SQL="select * from course_room ".$qStr." order by date desc, sector desc limit $start , $size";
      //debug_msg(__LINE__."行  SQL", time().$SQL);
   	$rs=&$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
   	while ($rs and $ro=$rs->FetchNextObject(false)) {

   		$roomR[] = get_object_vars($ro);
   	}
   }

   $goto="{$_SERVER['PHP_SELF']}?act=$act&sel_year=$sel_year&sel_seme=$sel_seme&room=$room";

   $Chi_page= new Chi_Page($tol,$size,$page,$goto) ;  //方式2
   $ShowPage=$Chi_page->show_page();//方式2
   
	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		if(document.myform.room.options[document.myform.room.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&sel_year=$sel_year&sel_seme=$sel_seme&room=\" + document.myform.room.options[document.myform.room.selectedIndex].value;
		}
	}
	</script>
	<table width='100%' cellspacing='0' cellpadding='0'>
	<tr><td>$tool_bar
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
   $room_sel $ShowPage
   </form>
	</td></tr></table>
	";
   $ii=count($roomR);
   if($ii>0){
      $main.="<table  width='100%'  border='0' align='center' cellpadding='2' cellspacing='1' bgcolor='#9EBCDD'>
      <tr align=center  style='font-size:10pt' bgcolor='white'>
      <td nowrap bgcolor=#FFFFCC>使用日期</td>
      <td nowrap bgcolor=#FFFFCC>星期</td>
      <td nowrap bgcolor=#FFFFCC>節次</td>
      <td nowrap bgcolor=#FFFFCC>地點</td>
      <td nowrap bgcolor=#FFFFCC>借用老師</td>
      <td nowrap bgcolor=#FFFFCC>借用班級或處室</td>
      <td nowrap bgcolor=#FFFFCC>預約登記時間</td></tr>";
      foreach($roomR as $r){
         $class_num=$r[seme_class];		 
	    if($class_num>0)$class_num=$class_arr[$class_num];
 
		 switch ($r[sector]) {
			case 0:
				$j_title='早修';
				break;
			case 100:
				$j_title='午休';
				break;
			default:
				$j_title="第 $r[sector] 節";
				break;
			}
		 
         //$cht_class_num=class_id2big5($class_num,$sel_year,$sel_seme);
         $main.="<tr align=center  style='font-size:10pt' bgcolor='white'>
      <td nowrap>$r[date]</td>
      <td nowrap>".$weekN7[$r[day]]."</td>
      <td nowrap>$j_title</td>
      <td nowrap>$r[room]</td>
      <td nowrap>".get_teacher_name($r[teacher_sn])."</td>
      <td nowrap>".$class_num."</td>
      <td nowrap>$r[sign_date]</td></tr>";
      }
      $main.= '</table>';
   }
   
	return $main;
}

//找出教室列表
function &get_room($sel_year,$sel_seme,$name="room",$now_room="",$jump_fn=""){
	global $school_menu_p,$today,$teacher_sn,$CONN;
	//從課表中查找
	$sql_select = "select room_name from spec_classroom where enable='1'";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while(list($room) = $recordSet->FetchRow()){
		if(empty($room))continue;
		$selected=($now_room==$room)?"selected":"";
		$option.="<option value='$room' $selected>$room</option>\n";
	}
	
	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";
    $main="<select name='$name' size='1' $jump>
    <option value=''>請選擇專科教室</option>
	<option value='all'>全部專科教室</option>";
//	$option</select>";
	return $main;
}


function debug_msg($title, $showarry){
	echo "<pre>";
	echo "<br>$title<br>";
	print_r($showarry);
}


?>
