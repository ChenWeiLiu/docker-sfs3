<?php

// $Id: new_normal.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
include "../../include/config.php";
//引入函數
include "./my_fun.php";
//使用者認證
sfs_check();

//不需要 register_globals
$teacher_course=$_POST['teacher_course'];
$weighted=trim($_POST['weighted']);
if(!$weighted) $weighted=1;
$test_name=trim($_POST['test_name']);
$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id
$nor_score="nor_score_".curr_year()."_".curr_seme();
if(!empty($test_name)){
	if(strstr ($teacher_course, 'g')){
		$group_arr=explode("g",$teacher_course);
		//$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
		$ss_id=$group_arr[1];
		$sql="select student_sn from elective_stu where group_id='{$group_arr[0]}' ";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		while(!$rs->EOF){
			$stud_sn[]=$rs->fields['student_sn'];
			$rs->MoveNext();
		}
		if(!is_array($stud_sn) or sizeof($stud_sn)==0) trigger_error("找不到該組學生名單，故您無法使用此功能。<ol>
			<li>請確認教務處已經將該組學生名單資料輸入系統中。
			<li>要分配該組學生請至：(<a href='".$SFS_PATH_HTML."modules/elective/elective_stu.php'>學科能力分組</a>)</ol>", E_USER_ERROR);
		for($j=0;$j<count($stud_sn);$j++){
			$sql="INSERT INTO $nor_score(teach_id,stud_sn,class_subj,stage,test_name,weighted,enable,freq) values('$teacher_id','$stud_sn[$j]','$teacher_course','{$_POST['stage']}','$test_name','$weighted','1','{$_POST['freq']}')";
			$rs=$CONN->Execute($sql) ;
		}
	}else{
    	$stud_sn=class_id_to_student_sn($_POST['class_id']);
		if(!is_array($stud_sn) or sizeof($stud_sn)==0) trigger_error("找不到該班學生名單，故您無法使用此功能。<ol>
			<li>請確認教務處已經將該班學生名單資料輸入系統中。
			<li>匯入學生資料：『學務系統首頁>教務>註冊組>匯入資料』(<a href='".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php'>".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php</a>)</ol>", E_USER_ERROR);
		for($j=0;$j<count($stud_sn);$j++){
			$sql="INSERT INTO $nor_score(teach_id,stud_sn,class_subj,stage,test_name,weighted,enable,freq) values('$teacher_id','$stud_sn[$j]','{$_POST['class_subj']}','{$_POST['stage']}','$test_name','$weighted','1','{$_POST['freq']}')";
			$rs=$CONN->Execute($sql) ;
		}
	}
	header("Location:./normal.php?teacher_course={$_POST['teacher_course']}&stage={$_POST['stage']}");

}
else{
	header("Location:./normal.php?teacher_course={$_POST['teacher_course']}&stage={$_POST['stage']}");
}
?>
