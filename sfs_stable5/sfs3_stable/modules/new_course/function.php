<?php
// $Id: function.php 8102 2014-08-31 15:06:51Z infodaes $

//列出某個班級的課表
function search_class_table($sel_year="",$sel_seme="",$class_id="",$tsn="" , $view_room="") {
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections,$midnoon,$SFS_PATH_HTML;

	if(empty($class_id)){
		//取得任教班級代號
		$class_num=get_teach_class();
		$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);
	}

	//取得班級資料
	$the_class=get_class_all($class_id);

	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	$sql_select = "select course_id,teacher_sn,cooperate_sn,day,sector,ss_id,room from score_course where class_id='$class_id' order by day,sector";

	$recordSet=$CONN->Execute($sql_select) or user_error("錯誤訊息：",$sql_select,256);
	while (list($course_id,$teacher_sn,$cooperate_sn,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$co[$k]=$cooperate_sn;
		$r[$k]=$room;
	}

	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >星期".$weekN[$i-1]."</td>";
	}

	//取得考試所有設定
	$sm=get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	$sections=$sm[sections];

//	if($sections==0)
//		trigger_error("請先設定 $sel_year 學年 $sel_seme 學期 [成績設定]項目,再操作課表設定<br><a href=\"$SFS_PATH_HTML/modules/every_year_setup/score_setup.php\">進入設定</a>",E_USER_ERROR);
	if(!empty($class_id)){

		//取得課表
		for ($j=1;$j<=$sections;$j++){

			if ($j==$midnoon){
				$all_class.= "<tr bgcolor='white'><td colspan='$dayn' align='center'>午休</td></tr>\n";
			}

			$all_class.="<tr bgcolor='#E1ECFF'><td align='center'>$j</td>";

			//列印出各節
			for ($i=1;$i<=count($weekN); $i++) {

				$k2=$i."_".$j;

				
				$teacher_search_mode=(!empty($tsn) and $tsn==$b[$k2])?true:false;
				$room_search_mode=(!empty($view_room) and $view_room==$r[$k2])?true:false;

				//科目的下拉選單
				$subject_sel="<font size=3>".get_ss_name("","","短",$a[$k2])."</font>";
				
				//教師的下拉選單
				$teacher_sel="<font size=2><a href='teacher_class.php?sel_year=$sel_year&sel_seme=$sel_seme&view_tsn=$b[$k2]'>".get_teacher_name($b[$k2])."</a></font>";
				if($co[$k2]) $teacher_sel.="<br><font size=1><a href='teacher_class.php?sel_year=$sel_year&sel_seme=$sel_seme&view_tsn=$co[$k2]'>*".get_teacher_name($co[$k2])."*</a></font>";
				
				$room_name=(empty($r[$k2]))?"&nbsp;":"<font color='red'>$r[$k2]</font>";
				
				$align="align='center'";
				$color=($teacher_search_mode or $room_search_mode)?"#FFF158":"white";


				//每一格
				$all_class.="<td $align bgcolor='$color' width=110>
				$subject_sel<br>
				$teacher_sel<br>
				<font size='2'>$room_name</font>
				</td>\n";
			}

			$all_class.= "</tr>\n" ;
		}
		
		if((!empty($tsn)) or (!empty($view_room)))$class_name="<tr bgcolor='#B9C5FF'><td colspan=6>$the_class[name] 課程表</td></tr>";

		//該班課表
		$main_class_list="
		$class_name
		<tr bgcolor='#E1ECFF'><td align='center'>節</td>$main_a</tr>
		$all_class
		";
	}else{
		$main_class_list="";
	}

	$main="
	<table border='0' cellspacing='1' cellpadding='4' bgcolor='#9EBCDD' width='80%'>
	$main_class_list
	</table>
	";
	return  $main;
}

?>
