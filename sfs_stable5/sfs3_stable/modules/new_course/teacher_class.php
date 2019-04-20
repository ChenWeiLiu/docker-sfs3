<?php
// $Id: teacher_class.php 8102 2014-08-31 15:06:51Z infodaes $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

if($_POST['course_id'] and checkid($_SERVER['SCRIPT_FILENAME'],1)) {
	$sql="UPDATE score_course SET c_kind='".$_POST['course_id_kind']."' WHERE course_id=".$_POST['course_id'];
	$rs=$CONN->Execute($sql) or user_error("錯誤訊息：",$sql,256);
}


$teacher_sn = $_REQUEST['view_tsn'];
$year_seme = $_REQUEST['year_seme'];
$class_id = $_REQUEST['class_id'];

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
head("教師課表查詢");

echo $main;
foot();

/*
函式區
*/

//基本設定表單
function class_form_search($sel_year,$sel_seme){
	global $school_menu_p,$PHP_SELF,$view_tsn,$teacher_sn,$class_id;
	if(empty($view_tsn))$view_tsn=$teacher_sn;

  //只出現有排課者
	//$teacher_select=select_teacher("teacher_sn",$view_tsn,'1',$sel_year,$sel_seme,"jumpMenu");
	$teacher_select=select_teacher_in_course("teacher_sn",$view_tsn,'1',$sel_year,$sel_seme,"jumpMenu");


	//取得年度與學期的下拉選單
	$date_select=class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");

	$tool_bar=make_menu($school_menu_p);
	
	if ($view_tsn)
	   $list_class_table=search_teacher_class_table($sel_year,$sel_seme,$view_tsn);

	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		location=\"$PHP_SELF?act=$act&&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + \"&view_tsn=\" + document.myform.teacher_sn.options[document.myform.teacher_sn.selectedIndex].value;
	}
	function jumpMenu_seme(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"$PHP_SELF?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<form action='$PHP_SELF' method='post' name='myform'>
	<table cellspacing='1' cellpadding='4'  bgcolor=#9EBCDD>
	
	<tr bgcolor='#F7F7F7'>
	<td>$date_select</td>
	<td>教師： $teacher_select	</td>
	</tr>	
	</table>
	$list_class_table
	</form>
	";
	return $main;
}

//教師的任課表
function search_teacher_class_table($sel_year="",$sel_seme="",$view_tsn=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections;
	
	$main=teacher_all_class($sel_year,$sel_seme,$view_tsn)."<br>";

	//取得教師授課的班級資料（陣列）
	$sql_select = "SELECT class_id FROM score_course WHERE year = $sel_year AND semester=$sel_seme AND (teacher_sn ='$view_tsn' OR cooperate_sn ='$view_tsn') group by class_id";
	$recordSet=$CONN->Execute($sql_select) or user_error("錯誤訊息：",$sql_select,256);
	while(list($clas_id)= $recordSet->FetchRow()){
		$clas_id_array[]=$clas_id;
	}

	for($i=0;$i<sizeof($clas_id_array);$i++){
		$main.=search_class_table($sel_year,$sel_seme,$clas_id_array[$i],$view_tsn)."<br>";
	}
	return $main;
}

//教師的任課總表
function teacher_all_class($sel_year="",$sel_seme="",$tsn=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections,$midnoon;

	$teacher_name=get_teacher_name($tsn);

	$double_class=array();
	$kk=array();
	
	//每週的日數
	$dayn=sizeof($weekN)+1;

	//找出教師該年度所有課程
	$sql_select = "select course_id,class_id,day,sector,ss_id,room,c_kind from score_course where year='$sel_year' and semester='$sel_seme' and (teacher_sn='$tsn' or cooperate_sn='$tsn') order by day,sector";
	$recordSet=$CONN->Execute($sql_select) or user_error("錯誤訊息：",$sql_select,256);
	while (list($course_id,$class_id,$day,$sector,$ss_id,$room,$c_kind)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$class_id;
		$room[$k]=$room;
		$course_id_arr[$k]=$course_id;
		//記錄是否為兼課  0:一般  1:兼課
		$c_kind_arr[$k]=$c_kind;		
		
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

			//科目
			$subject_show="<font size=3>".get_ss_name("","","短",$a[$k2])."</font>";

			//班別
			if ($b[$k2]) 
			   $class_show="<font size=2><a href='index.php?sel_year=$sel_year&sel_seme=$sel_seme&class_id=$b[$k2]'>$class_name</a></font>";
			else 
			   $class_show="" ;   

			//若是該日期節數有在重複陣列理，秀出紅色底色
			$d_color=(in_array($k2,$double_class))?"red":"white";
			
			
			//若是兼課  則顯示★
			if($c_kind_arr[$k2]){
				$c_kind_message='★';
				$c_kind_value='0';
			} else {
				$c_kind_message='';
				$c_kind_value='1';
			}

			//每一格
			$this_course_id=$course_id_arr[$k2];
			//$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'";
			if(checkid($_SERVER['SCRIPT_FILENAME'],1)) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.course_id.value=\"$this_course_id\"; document.myform.course_id_kind.value=\"$c_kind_value\"; document.myform.submit();'";
			$all_class.="<td align='center'  width=110 bgcolor='$d_color' $java_script>
			$class_show<br>
			$c_kind_message$sub$subject_show$c_kind_message<br>
			<!--<input type='text' name='room' value='".$room[$i][$j]."' size='10'>-->
			</td>\n";
		}

		$all_class.= "</tr>\n" ;
	}


	//該班課表
	$main_class_list="
	<tr bgcolor='#FBDD47'><td colspan=6>『".$teacher_name."』授課總表（若有出現紅色底色，表示該堂課有衝堂；若科目名稱有★，表示該節為兼課。）</td></tr>
	<tr bgcolor='#FBF6C4'><td align='center'>節</td>$main_a</tr>
	$all_class";

	$main="<input type='hidden' name='course_id' value=''><input type='hidden' name='course_id_kind' value=''>
	<table border='0' cellspacing='1' cellpadding='4' bgcolor='#D06030' width='80%'>
	$main_class_list
	</table></form>
	";
	return  $main;
}


//取得今年教師的下拉選單(只出現有排課者)
function &select_teacher_in_course($col_name="teacher_sn",$teacher_sn="",$enable='1',$sel_year="",$sel_seme="",$jump_fn="",$day="",$sector=""){
	global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	
	$option="<option value='0'></option>";
	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";
	

	//有排課教師 sn 標記
	$sql_select2 =" SELECT teacher_sn FROM score_course where teacher_sn<>'0'  and year='$sel_year' and  semester='$sel_seme'  group by teacher_sn " ;
  $recordSet2=$CONN->Execute($sql_select2) or trigger_error($sql_select2, E_USER_ERROR);
  while (list($tsn)= $recordSet2->FetchRow()) {
  	$sn_list[$tsn]=1 ;
  }	
  
	//先找出所有教師的陣列，判斷是否有排課
	$sql_select = "select name,teacher_sn from teacher_base where teach_condition='0' order by name";
	$recordSet=$CONN->Execute($sql_select);
	while (list($name,$tsn)= $recordSet->FetchRow()) {
		if ( $sn_list[$tsn] == 1) { //有排課者
		   $selected=($tsn==$teacher_sn)?"selected":"";
		   $option.="<option value='$tsn' $selected style='color: $color'>$name</option>\n";
		}   
	}		   

	
	$select_teacher="
	<select name='$col_name' $jump>
	$option
	</select>";
	return $select_teacher;
}

?>

