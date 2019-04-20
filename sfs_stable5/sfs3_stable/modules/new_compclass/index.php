<?php

// $Id: index.php 9265 2018-08-11 15:00:25Z chiming $

include "config.php";
include "module-upgrade.php";
include_once "../../include/sfs_case_dataarray.php";
//sfs_check(); 已在更新處做檢查

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


//檢查是否有模組管理權
$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];
$is_module_manager=checkid($SCRIPT_FILENAME,1);

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//執行動作判斷
if($act=="預約"){
	sfs_check();
	$msg=add_set_room($time,$room,$teacher_sn);
	$room=stripslashes($room);
	header("location: {$_SERVER['PHP_SELF']}?room=$room");
}elseif($act=="del"){
	sfs_check();
	$room=stripslashes($room);
	del_set_room($date,$day,$sector,$room,$teacher_sn);
    $room=stripslashes($room);
	header("location: {$_SERVER['PHP_SELF']}?room=$room");
}else{
	$main=&main_form($sel_year,$sel_seme,$room,$teacher_sn);
}


//秀出網頁
head("專科教室預約");
echo $main.$msg;
foot();



/*******************************************************/
//相關函數
/*******************************************************/
//主要預約畫面
function &main_form($sel_year,$sel_seme,$room,$teacher_sn){
	global $today,$CONN,$SFS_PATH_HTML,$school_menu_p,$weekN7,$weekN,$daymm,$dayww,$sunday,$saturday,$midnoon,$is_module_manager,$after_school;
	$tool_bar=&make_menu($school_menu_p);
	//取得任教班級代號
	$class_num=get_teach_class();
	$class_name=(empty($class_num))?"":class_id2big5($class_num,$sel_year,$sel_seme);
	$class_teacher_name=get_teacher_name($teacher_sn);
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
    
    //處室列表
	$sel2=new drop_select();
	$sel2->s_name="room_kind_class";
	$sel2->arr=room_kind();
	$sel2->id=$_POST[room_kind_class];	
	$sel2->has_empty=false;
	$sel2->is_submit=true;
	$chk[intval($_POST[class_kind])]="checked";
    $sel_room=$sel2->arr[$sel2->id];
	
	$class_sel="<input type=radio name=class_kind value=0 ".$chk[0].">依課表填班級 <input type=radio name=class_kind value=1 ".$chk[1].">自選班級 ".$sel1->get_select()."<input type=radio name=class_kind value=2 ".$chk[2].">依處室 ".$sel2->get_select()."<p>";

	//設定前三天 至 三加預約日數 開放預約
//	$daymm=3; //目前系統 初值 0
//	$dayww=7; //目前系統 初值 7
	$daysum=$daymm+$dayww;

	//注意事項
	$notes="
		<ol>
		<li class='small'>專科教室會抓取課表設定中的教室設定，因此，若課表中的課程均無設定專科教室，那麼此系統可能會無法使用。</li>
		<li class='small'>一周內每班只填選一節為原則。〈但並不強制〉</li>
		<li class='small'>如預約後不使用，請記得取消，以維其他老師預約權利。</li>
		<li class='small'>";

	$notes.=(  $daymm>0 ? "前".$daymm."天至前".$daysum."天，開放受理預約。":"可以在前一周開始預約。");
	$notes.="</li>
		<li class='small'>如預約沒有出現班級名稱，則是該堂沒有排課也沒有選擇班級。</li>
		<li class='small'><font color='red'>如需增加可預約的課後節數，請設定本模組之模組變數after_school。</font></li>
		</ol>";

	//計算出空堂課表
	if(!empty($room)){
		//找出該教室有上課的資料
		$room_class=get_room_class($sel_year,$sel_seme,$room);
		//可預約
		$room_notfree=get_room_notfree($sel_year,$sel_seme,$room);

		//找出今年度每日課堂最多的節數
		$sections=get_most_class($sel_year,$sel_seme)+$after_school;

		//今天星期幾，取得周一日期
		$mday=date("w");


		$day=$mday;

		//取得星期那一列
		for ($i=0;$i<$daysum; $i++) {
			$mktime=mktime (0,0,0,date("m")  ,date("d")+$i,date("Y"));
			$the_day=date("m月d日",$mktime);
			$all_mktime[$i]=date("Y-m-d",$mktime);
			$day=($day>=7)?$day%7:$day;
			$main_a.="<td align='center' nowrap>".$weekN7[$day]."</td>";
			$main_b.="<td align='center' nowrap>$the_day</td>";
			$day++;
		}

		//表格總列數
		$dayn=$daysum+1;

		//取得課表
		$noon='午休';
		for ($j=0;$j<=$sections;$j++){
			if ($j==$midnoon){
				if($noon)
				$j=100;
				//$all_class.= "<tr></tr>";
			}
			switch ($j) {
			case 0:
				$j_title='早修';
				break;
			case 100:
				$j_title=$noon;
				break;
			default:
				$j_title=$j;
				break;
			}
			$all_class.="<tr bgcolor='#E1ECFF' class='small'><td align='center'>$j_title</td>";
			//列印出各節
			$sday=$mday;
			for ($i=0;$i<$daysum; $i++) {
				$mktime=$all_mktime[$i];
				//有預約
				
				$set_data=&get_set_room($mktime,$room);

				$sday=($sday>=7)?$sday%7:$sday;
				$show="";

				$k2=$sday."_".$j;
				$k3=$mktime."_".$k2;
				$sid=$room_class[$k2][ss_id];
				$tsn=$room_class[$k2][teacher_sn];
				$cid=$room_class[$k2][class_id];

				if(!empty($cid))	$tc=class_id_2_old($cid);
				//有上課
				$teacher_name=get_teacher_name($tsn);
				$subject_name=&get_ss_name("","","短",$sid);
				$bgcolor="#FFFFFF";
				if($room_notfree[$sday][$j]) $show="" ;
				elseif(!empty($sid)){
					//原有上課
					$show="<font color='#0000FF'>$tc[5]</font><br>$subject_name<br><font color='#CC0000'>$teacher_name</font>";
				}elseif(!empty($set_data[$k3][teacher_sn])){
					//已預約
					$del_tool=($set_data[$k3][teacher_sn]==$teacher_sn)?"<br><a href='{$_SERVER['PHP_SELF']}?act=del&date=$mktime&day=$sday&sector=$j&teacher_sn=$teacher_sn&room=$room'><font color='#FF0000'>[取消]</font></a>":"";
					
					if ($set_data[$k3][seme_class]>0)
					{
					$show="<font color='#0000FF'>".$class_arr[$set_data[$k3][seme_class]]."</font><br>".get_teacher_name($set_data[$k3][teacher_sn]).$del_tool;
					}
					else
					{
					$show="<font color='#0000FF'>".$set_data[$k3][seme_class]."</font><br>".get_teacher_name($set_data[$k3][teacher_sn]).$del_tool;						
					}
					
					if ($set_data[$k3][teacher_sn]==$teacher_sn) $bgcolor="#FFF188";
				}elseif(($sday==0 && $sunday==0) or ($sday==6 && $saturday==0)){
					$show="";
				}elseif($i>($daymm-1)){
					if ($room_notfree[$sday][$j] == TRUE)
					  $show="" ;
					else
					  //可預約
					  if($is_module_manager) $com_type='checkbox'; else $com_type='radio';
					  $show="<input type='$com_type' name='time[]' value='$k3'><font color='#C0C0C0'>空堂</font>";					  
				}else{
					$show="<font color='#006600'>過期了</font>";
				}

				//每一格
				$all_class.="<td align='center' nowrap bgcolor='$bgcolor'>
				$show
				</td>\n";
				$sday++;
			}
			if($j==100){ $j=$midnoon-1; $noon=''; }
			$all_class.= "</tr>\n" ;
		}
	    if (!isset($_SESSION['session_log_id'])) $disabled="disabled=true" ;else $disabled='';
		//該班課表
		$main_class_list="
		<table width='100%' cellspacing='0' cellpadding='0'><tr><td>
		<table cellspacing='1' cellpadding='3' bgcolor='#9EBCDD'>
		<tr bgcolor='#E1ECFF' class='small'><td align='center'>日</td>$main_b</tr>
		<tr bgcolor='#E1ECFF' class='small'><td align='center'>節</td>$main_a</tr>
		$all_class
		</table>
		<input type='hidden' name='sel_room' value='$sel_room'>
		<input type='submit' name='act' value='預約' $disabled ></td></tr>
		<tr bgcolor='#FBFBC4'><td><img src='../../images/filefind.png' width='16' height='16' hspace='3' border='0'>相關說明</td></tr>
		<tr><td style='line-height:150%;'>$notes</td></tr>
		</table>
		";
	} else {
		//只顯示注意事項
		$main_class_list="
		<tr bgcolor='#FBFBC4'><td><img src='../../images/filefind.png' width='16' height='16' hspace='3' border='0'>相關說明</td></tr>
		<tr><td style='line-height:150%;'>$notes</td></tr>
		</table>
		";
	}

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
	$room_sel 預約者： $class_name $class_teacher_name $class_sel
	$main_class_list
	</form>
	</td></tr></table>
	";
	return $main;
}

//找出教室列表
function &get_room($sel_year,$sel_seme,$name="room",$now_room="",$jump_fn=""){
	global $school_menu_p,$today,$teacher_sn,$CONN;
	//從課表中查找
	$sql_select = "select room_name from spec_classroom where enable='1' order by room_name";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	$now_room=stripslashes($now_room);
	while(list($room) = $recordSet->FetchRow()){
		if(empty($room))continue;
		$selected=($now_room==$room)?"selected":"";
		$option.="<option value='$room' $selected>$room</option>\n";
	}

	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";

	$main="<select name='$name' size='1' $jump>
	<option value=''>請選擇專科教室</option>
	$option</select>";

	return $main;
}
//不可以預約的節數
function get_room_notfree($sel_year,$sel_seme,$room){
	global $school_menu_p,$today,$teacher_sn,$CONN;

	$sql_select = "select notfree_time from spec_classroom where enable='1' and room_name = '$room' ";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while(list($notfree_time) = $recordSet->FetchRow()){
		$day_sec = split("," , $notfree_time) ;
		foreach ($day_sec as $k=>$v) {
		  $d = substr($v,1);
		  $s = substr($v,0,1);
		  //echo "$s _ $d <br>" ;
		  $main[$s][$d]= TRUE ;
		}

	}
	return $main;
}

//找出教室列表
function get_room_class($sel_year,$sel_seme,$room){
	global $school_menu_p,$today,$teacher_sn,$CONN;
	//從課表中查找
	$sql_select = "select class_id,teacher_sn,day,sector,ss_id from score_course where year='$sel_year' and semester='$sel_seme' and room='$room'";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while(list($class_id,$teacher_sn,$day,$sector,$ss_id) = $recordSet->FetchRow()){
		$k=$day."_".$sector;
		$main[$k][class_id]=$class_id;
		$main[$k][teacher_sn]=$teacher_sn;
		$main[$k][ss_id]=$ss_id;
	}
	return $main;
}


//預約教室
function add_set_room($time,$room,$teacher_sn){
	global $CONN,$sel_room;
	echo "sel_room=".$sel_room;
	foreach($time as $key=>$value){
		$t=explode("_",$value);
		if ($_POST[class_kind]==0) {
			$query="select class_id from score_course where year='".curr_year()."' and semester='".curr_seme()."' and day='$t[1]' and sector='$t[2]' and teacher_sn='".$_SESSION[session_tea_sn]."'";
			$res=$CONN->Execute($query);
			$c=explode("_",$res->fields[class_id]);
			$seme_class=intval($c[2].$c[3]);
			if ($seme_class=="0") $seme_class="";
		} else {			
			$seme_class=$_POST[seme_class];
			if($_POST[class_kind]==2)$seme_class=$sel_room;
		}
		
		$str="INSERT INTO course_room (date,day,sector,room,teacher_sn,sign_date,seme_class) VALUES ('$t[0]','$t[1]','$t[2]','$room','$teacher_sn',now(),'$seme_class')";
		$CONN->Execute($str) or trigger_error("預約失敗： $str \n\n 有可能您太晚決定了,以致於在您送出[預約]請求時,已經有人先預約了!", E_USER_ERROR);
	}
	return $msg;
}

//刪除預約教室
function del_set_room($date,$day,$sector,$room,$teacher_sn){
	global $CONN;
	$str="delete from course_room where date='$date' and day='$day' and sector='$sector' and room='$room' and teacher_sn='$teacher_sn'";
	$CONN->Execute($str) or trigger_error("SQL語法錯誤： $str", E_USER_ERROR);
	return true;
}

//讀取教室設定
function &get_set_room($the_day,$room){
	global $CONN;
	$str="select date,day,sector,teacher_sn,seme_class from course_room where date='$the_day' and room='$room'";
	$recordSet=$CONN->Execute($str) or trigger_error("SQL語法錯誤： $str", E_USER_ERROR);
	while(list($date,$day,$sector,$teacher_sn,$seme_class)=$recordSet->FetchRow()){
		$k=$date."_".$day."_".$sector;
		$main[$k][teacher_sn]=$teacher_sn;
		$main[$k][seme_class]=$seme_class;
	}
	return $main;
}
?>
