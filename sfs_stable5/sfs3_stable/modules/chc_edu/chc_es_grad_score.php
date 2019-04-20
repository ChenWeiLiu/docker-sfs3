<?php
include "config.php";
//為取中文班級
//include_once "../../include/sfs_case_studclass.php";

sfs_check();

$year = curr_year();
$semester = curr_seme();
$jschool = $_REQUEST[jschool];
$mad_ssid = $_REQUEST[mad_ssid];
$math_ssid = $_REQUEST[math_ssid];

//排除沒有同步化就開始的人
$sql =  "select grad_sn from grad_stud where stud_grad_year='{$year}' and class_year='6'";
$result = $CONN->Execute($sql);
list($grad_sn) =$result->FetchRow();
if(empty($grad_sn)){
	die('「請先進行註冊組-畢業生升學資料」-同步化，再填好學生就讀國中');
}

$stud_data = get_stud_data($year,$semester,$jschool,$mad_ssid,$math_ssid);

$data = "流水號,姓名,性別,原畢業國小校名,原畢業國小班級,身分證號碼,就讀國中,國語,數學\r\n";

$i=1;

foreach($stud_data as $k=>$v){
	$data .= "{$i},{$v[stud_name]},{$v[sex]},{$school_sshort_name},{$v[classname]},{$v[stud_person_id]},{$v[new_school]},{$v[mad][定期評量]},{$v[math][定期評量]}\r\n";
	$i++;
}


$filename=$year."學年".$school_sshort_name."畢業學生成績名冊.csv";
header("Content-disposition: attachment;filename=$filename");
header("Content-type: text/x-csv ; Charset=Big5");
header("Pragma: no-cache");
header("Expires: 0");

echo $data;



function get_stud_data($year,$semester,$jschool,$mad_ssid,$math_ssid){
	global $CONN;

//取畢業生
	$sql =  "select a.student_sn,a.new_school,b.stud_name,b.stud_sex,b.stud_person_id,b.curr_class_num from grad_stud a left join stud_base b on a.student_sn = b.student_sn where a.stud_grad_year='{$year}' and a.class_year='6' and a.new_school='{$jschool}' order by a.new_school,b.curr_class_num";
  if($jschool=="全部國中") $sql =  "select a.student_sn,a.new_school,b.stud_name,b.stud_sex,b.stud_person_id,b.curr_class_num from grad_stud a left join stud_base b on a.student_sn = b.student_sn where a.stud_grad_year='{$year}' and a.class_year='6' order by b.curr_class_num";
	$result = $CONN->Execute($sql);
	while(list($student_sn,$new_school,$stud_name,$stud_sex,$stud_person_id,$curr_class_num) =$result->FetchRow()){
		$stud_data[$student_sn][new_school]=$new_school;
		$stud_data[$student_sn][stud_name]=str_replace("　","",str_replace(" ","",$stud_name));
		if($stud_sex=="1") $stud_data[$student_sn][sex]="男";
		if($stud_sex=="2") $stud_data[$student_sn][sex]="女";
		$stud_data[$student_sn][stud_person_id]=$stud_person_id;
		$stud_data[$student_sn][new_school]=$new_school;
		$stud_data[$student_sn][classname]=(int)substr($curr_class_num,1,2);

	}

	//取六年級下學期幾次階段考試
	$sql = "select performance_test_times,test_ratio,score_mode from score_setup where year='{$year}' and semester='{$semester}' and class_year='6'";
	$result = $CONN->Execute($sql);
	list($performance_test_times,$test_ratio,$score_mode) =$result->FetchRow();

	//取國語數學的ss_id
	/*
	$sql = "select ss_id,link_ss from score_ss where year='{$year}' and semester='{$semester}' and class_year='6' and (link_ss='語文-本國語文' or link_ss='數學')";
	$result = $CONN->Execute($sql);

	while(list($ss_id,$link_ss) =$result->FetchRow()){
		if($link_ss=='語文-本國語文') $subject6[mad]=$ss_id;
		if($link_ss=='數學') $subject6[math]=$ss_id;
	}
	*/
	$subject6[mad]=$mad_ssid;
	$subject6[math]=$math_ssid;

	//當學期的資料表
	$score_semester = "score_semester_".$year."_".$semester;

	$like_class_id = $year."_".$semester."_06_";
	//取學生國數分數
	$sql= "select a.student_sn,a.ss_id,a.score,a.test_kind from {$score_semester} a right join grad_stud b on a.student_sn=b.student_sn where (a.ss_id='{$subject6[mad]}' or a.ss_id='{$subject6[math]}') and a.test_sort='$performance_test_times' and a.class_id like '{$like_class_id}%' and a.score >='0' and b.new_school='{$jschool}'";
	if($jschool=="全部國中") $sql= "select student_sn,ss_id,score,test_kind from {$score_semester} where (ss_id='{$subject6[mad]}' or ss_id='{$subject6[math]}') and test_sort='$performance_test_times' and class_id like '{$like_class_id}%' and score >='0'";
	$result = $CONN->Execute($sql);
	//$i=1;
	while(list($student_sn,$ss_id,$score,$test_kind) =$result->FetchRow()){
		if(!empty($score)){
			if($ss_id == $subject6[mad]) $stud_data[$student_sn][mad][$test_kind]=$score;
			if($ss_id == $subject6[math]) $stud_data[$student_sn][math][$test_kind]=$score;
		}
	}
/*採階段成績
	foreach($stud_data as $k=>$v){
	//每次月考比例相同
		if($score_mode=="all"){
			$ratio = explode("-",$test_ratio);
			$stud_data[$k][mad][平均]=round(($v[mad][定期評量]*$ratio[0]+$v[mad][平時成績]*$ratio[1])/100,2);
			$stud_data[$k][math][平均]=round(($v[math][定期評量]*$ratio[0]+$v[math][平時成績]*$ratio[1])/100,2);
	//如果比例不同
		}elseif($score_mode=="severally"){
			$ratio1 = explode(",",$test_ratio);
			$ratio2= explode("-",$ratio1[$performance_test_times-1]);
			$stud_data[$k][mad][平均]=round(($v[mad][定期評量]*$ratio2[0]+$v[mad][平時成績]*$ratio2[1])/100,2);
			$stud_data[$k][math][平均]=round(($v[math][定期評量]*$ratio2[0]+$v[math][平時成績]*$ratio2[1])/100,2);
		}
	}

*/
	return $stud_data;
}
