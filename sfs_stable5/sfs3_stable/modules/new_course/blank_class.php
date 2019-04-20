<?php
// $Id: blank_class.php 5310 2009-01-10 07:57:56Z hami $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

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
head("空堂教師查詢 ");

echo $main;
foot();

/*
函式區
*/

//基本設定表單
function class_form_search($sel_year,$sel_seme){
	global $school_menu_p,$PHP_SELF,$view_tsn,$teacher_sn,$class_id;
	if(empty($view_tsn))$view_tsn=$teacher_sn;
	
	//取得年度與學期的下拉選單
	$date_select=class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");

	$tool_bar=make_menu($school_menu_p);

	$list_class_table=teacher_all_class($sel_year,$sel_seme,$view_tsn);

	$main="
	<script language='JavaScript'>
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
	<td>$date_select 所有空堂</td>
	</tr>
	</form>
	</table>
	$list_class_table
	";
	return $main;
}


//教師的空堂總表
function teacher_all_class($sel_year="",$sel_seme="",$tsn=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections,$midnoon;

	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	//有排課教師 sn 
	$sql_select2 =" SELECT teacher_sn  FROM score_course where teacher_sn<>'0'  and year='$sel_year' and  semester='$sel_seme'  group by teacher_sn " ;
  $recordSet2=$CONN->Execute($sql_select2) or trigger_error($sql_select2, E_USER_ERROR);
  while (list($tsn)= $recordSet2->FetchRow()) {
  	$sn_list[$tsn]=1 ;
  }	
	//先找出所有教師的陣列
	$sql_select = "select name,teacher_sn from teacher_base where teach_condition='0'";
	$recordSet=$CONN->Execute($sql_select);
	while (list($name,$teacher_sn)= $recordSet->FetchRow()) {
		if ( $sn_list[$teacher_sn] ==1)  //有排課者
		   $t[$teacher_sn]=$name;
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

			$tlist="";
			reset($t);
			while(list($array_teacher_sn,$name)=each($t)){
				$tsn=get_teacher_course_num($sel_year,$sel_seme,$array_teacher_sn,$i,$j);
				if($tsn==0)$tlist.=$name."<br>";
			}


			//每一格
			$all_class.="<td align='center'  width=110 bgcolor='white' class='small' valign='top'>
			$tlist
			</td>\n";
		}
		$all_class.= "</tr>\n" ;
	}


	//該班課表
	$main_class_list="
	<tr bgcolor='#FBF6C4'><td>節</td>$main_a</tr>
	$all_class";

	$main="
	<table border='0' cellspacing='1' cellpadding='4' bgcolor='#D06030' width='80%'>
	$main_class_list
	</table>
	";
	return  $main;
}
?>
