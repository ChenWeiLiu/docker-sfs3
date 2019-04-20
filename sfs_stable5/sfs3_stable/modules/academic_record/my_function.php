<?php

// $Id: my_function.php 7727 2013-10-28 08:26:17Z smallduh $

//取得科目節數
function get_ss_num($class_id="",$stud_id="",$student_sn="",$ss_id=""){
	global $CONN;

	$sql_select = "select count(*) from score_course where ss_id=$ss_id and class_id='$class_id'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	list($n)= $recordSet->FetchRow();

	return $n;
}



//算出某位學生某科的全學期成績，並以甲乙丙丁為結果
function get_ss_score($class_id="",$stud_id="",$student_sn="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];
	//只求出總成績
	$score=seme_score2($student_sn,$ss_id,$year,$seme);
	// 未輸入
	if ($score == -1)
		$score_name = "無資料";
	else
		$score_name=score2str($score,$class);
	return $score_name;
}

//取得各領域文字描述 
function get_ss_score_memo($class_id="",$stud_id="",$student_sn="",$ss_id=""){
	global $CONN;
	$class=class_id_2_old($class_id);
	$year=$class[0];
	$seme=$class[1];

	$seme_year_seme = sprintf("%03d%d",$year,$seme);
	$query = "select ss_score_memo from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn = $student_sn and ss_id =$ss_id ";
	$res = $CONN->Execute($query) or trigger_error($query);
	return $res->fields[0];
}

//規則口語化
function &say_rule_2($class){
	$year=$class[0];
	$seme=$class[1];
         //取考試設定資料
        $sm=&get_all_setup("",$year,$seme,$class[3]);
                                                                                                                            
        //分解規則
        $r=explode("\n",$sm[rule]);
        reset($r);
        while(list($k,$v)=each($r)){
                $str=explode("_",$v);
                $main.="<text:p text:style-name=\"P11\">學期分數 ".htmlspecialchars($str[1])." $str[2] 時，等第為『$str[0]』</text:p>";
        }
        return $main;
}

function teacher_sn_to_class_name($teacher_sn){
    global $CONN;
        $sql="select class_num from teacher_post where teacher_sn='$teacher_sn'";
        $rs=$CONN->Execute($sql);
        $class_num = $rs->fields["class_num"];
        if($class_num=="") trigger_error("您沒有擔任導師！",E_USER_ERROR);
        $sel_year = curr_year(); //目前學年
        $sel_seme = curr_seme(); //目前學期
        $class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($class_num,0,-2),substr($class_num,-2));
        $class_cname=class_id_to_full_class_name($class_id);
        $class_name[0]=$class_num;//數字
        $class_name[1]=$class_cname;//中文
		$class_name[3]=$class_id;//中文
        return $class_name;
}

//取得該班課程節數
function get_class_cn($class_id=""){
	global $CONN;
	//取得某班學生陣列
	$c=class_id_2_old($class_id);
	
	//取得該班有幾節課
	$sql_select = "select sections from score_setup where year = '$c[0]' and semester='$c[1]' and class_year='$c[3]'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("SQL語法錯誤： $sql_select", E_USER_ERROR);
	list($all_sections) = $recordSet->FetchRow();
	return $all_sections;
}
?>
