<?php
// $Id: room_class.php 5310 2009-01-10 07:57:56Z hami $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

$year_seme = $_REQUEST['year_seme'];
$class_id = $_REQUEST['class_id'];
$view_room = $_REQUEST['view_room'];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
//錯誤設定
if($error==1){
	$act="error";
	$error_title="無年級和班級設定";
	$error_main="找不到 $sel_year 學年度第 $sel_seme 學期的年級設定，故您無法使用此功能。<ol><li>請先到『<a href='".$SFS_PATH_HTML."school_affairs/every_year_setup/class_year_setup.php'>班級設定</a>』設定年級以及班級資料。<li>以後記得每一學期的學期出都要設定一次喔！</ol>";
}

//執行動作判斷
if($act=="error"){
	$main=error_tbl($error_title,$error_main);
}else{
	$main=class_form_search($sel_year,$sel_seme);
}


//秀出網頁
head("專科教室課表查詢");

echo $main;
foot();

/*
函式區
*/

//基本設定表單
function class_form_search($sel_year,$sel_seme){
	global $school_menu_p,$PHP_SELF,$view_room,$teacher_sn,$class_id;
	if(empty($view_tsn))$view_tsn=$teacher_sn;

  //只出現有排課者
	//$teacher_select=select_teacher("teacher_sn",$view_tsn,'1',$sel_year,$sel_seme,"jumpMenu");
	//$teacher_select=select_teacher_in_course("teacher_sn",$view_tsn,'1',$sel_year,$sel_seme,"jumpMenu");
	
  //取得專科教室列表
  $room_select = select_room("room_name" , $view_room , $sel_year,$sel_seme,"jumpMenu" ) ;

	//取得年度與學期的下拉選單
	$date_select=class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");

	$tool_bar=make_menu($school_menu_p);
	
	if ($view_room) 
	   $list_class_table=search_room_class_table($sel_year,$sel_seme,$view_room);

	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		location=\"$PHP_SELF?act=$act&&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + \"&view_room=\" + document.myform.room_name.options[document.myform.room_name.selectedIndex].value;
	}
	function jumpMenu_seme(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"$PHP_SELF?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<table cellspacing='1' cellpadding='4'  bgcolor=#9EBCDD>
	<form action='$PHP_SELF' method='post' name='myform'>
	<tr bgcolor='#F7F7F7'>
	<td>$date_select</td>
	<td>教室： $room_select	</td>
	</tr>
	</form>
	</table>
	$list_class_table
	";
	return $main;
}

//教師的任課表
function search_room_class_table($sel_year="",$sel_seme="",$view_room=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections;
	
	$main=room_all_class($sel_year,$sel_seme,$view_room)."<br>";

	//取得教師授課的班級資料（陣列）
	$sql_select = "SELECT class_id FROM score_course WHERE year = $sel_year AND semester=$sel_seme AND room ='$view_room' group by class_id";
	$recordSet=$CONN->Execute($sql_select) or user_error("錯誤訊息：",$sql_select,256);
	while(list($clas_id)= $recordSet->FetchRow()){
		$clas_id_array[]=$clas_id;
	}

	for($i=0;$i<sizeof($clas_id_array);$i++){
		$main.=search_class_table($sel_year,$sel_seme,$clas_id_array[$i], '' , $view_room )."<br>";
	}
	return $main;
}

//專科教室課總表
function room_all_class($sel_year="",$sel_seme="",$view_room=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections,$midnoon;

	//$teacher_name=get_teacher_name($tsn);

	$double_class=array();
	$kk=array();
	
	//每週的日數
	$dayn=sizeof($weekN)+1;

	//找出教師該年度所有課程
	$sql_select = "select course_id,class_id,day,sector,ss_id, teacher_sn  from score_course where room='$view_room' and year='$sel_year' and semester='$sel_seme' order by day,sector";

	$recordSet=$CONN->Execute($sql_select) or user_error("錯誤訊息：",$sql_select,256);
	while (list($course_id,$class_id,$day,$sector,$ss_id,$teacher_sn)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$class_id;
		//$room[$k]=$room;
		
		$teacher_name=get_teacher_name($teacher_sn);
		$ta[$k]= $teacher_name ;
		$ta_sn[$k] = $teacher_sn ;
		
		//若是日期節數有重複的紀錄起來
		if(in_array($k,$kk))$double_class[]=$k;

		//把所有日期節數放日陣列
		$kk[]=$k;
	}

	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >星期".$weekN[$i-1]."</td>";
	}

	//取得節數的最大值
	$sections=get_most_class($sel_year,$sel_seme);


	//取得課表
	for ($j=1;$j<=$sections;$j++){

		if ($j==$midnoon){
			$all_class.= "<tr bgcolor='white'><td colspan='$dayn' align='center'>午休</td></tr>\n";
		}


		$all_class.="<tr bgcolor='#FBEC8C'><td align='center'>$j</td>";

		//列印出各節
		for ($i=1;$i<=count($weekN); $i++) {

			$k2=$i."_".$j;

			//取得班級資料
			$the_class=get_class_all($b[$k2]);
			$class_name=($the_class[name]=="班")?"":$the_class[name];
      
      //任課教師
      $teacher_name= $ta[$k2] ;
      if ($teacher_name) 
         $teacher_show= "<font size=2><a href='teacher_class.php?sel_year=$sel_year&sel_seme=$sel_seme&view_tsn=$ta_sn[$k2]'>$teacher_name</a></font>";
      else 
         $teacher_show="" ;   

       
			//科目
			$subject_show="<font size=3>".get_ss_name("","","短",$a[$k2])."</font>";

			//班別
			if ($b[$k2]) 
			   $class_show="<font size=2><a href='index.php?sel_year=$sel_year&sel_seme=$sel_seme&class_id=$b[$k2]'>$class_name</a></font>";
			else 
			   $class_show="" ;   

			//若是該日期節數有在重複陣列理，秀出紅色底色
			$d_color=(in_array($k2,$double_class))?"red":"white";

			//每一格
			$all_class.="<td align='center'  width=110 bgcolor='$d_color'>
			$class_show<br>
			$subject_show<br>
			$teacher_show
			</td>\n";
		}

		$all_class.= "</tr>\n" ;
	}


	//該班課表
	$main_class_list="
	<tr bgcolor='#FBDD47'><td colspan=6>『".$view_room."』專科教室課表（若有出現紅色底色，表示該堂課有衝堂。）</td></tr>
	<tr bgcolor='#FBF6C4'><td align='center'>節</td>$main_a</tr>
	$all_class";

	$main="
	<table border='0' cellspacing='1' cellpadding='4' bgcolor='#D06030' width='80%'>
	$main_class_list
	</table>
	";
	return  $main;
}


//取得專科教室的下拉選單(只出現有排課者)
function &select_room($col_name="room_name",$room_id="", $sel_year="",$sel_seme="",$jump_fn="",$day="",$sector=""){
	global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	
	$option="<option value='0'></option>";
	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";
	

	//有專科教室
	$sql_select2 =" SELECT room  FROM score_course 
	                where room <>''  and year='$sel_year' and  semester='$sel_seme'  group by room  " ;
  $recordSet2=$CONN->Execute($sql_select2) or trigger_error($sql_select2, E_USER_ERROR);
  while (list($room)= $recordSet2->FetchRow()) {
		   $selected=($room==$room_id)?"selected":"";
		   $option.="<option value='$room' $selected style='color: $color'>$room</option>\n";
  }	
  

	$select_teacher="
	<select name='$col_name' $jump>
	$option
	</select>";
	return $select_teacher;
}

?>

