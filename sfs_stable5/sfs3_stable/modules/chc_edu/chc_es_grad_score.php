<?php
include "config.php";
//��������Z��
//include_once "../../include/sfs_case_studclass.php";

sfs_check();

$year = curr_year();
$semester = curr_seme();
$jschool = $_REQUEST[jschool];
$mad_ssid = $_REQUEST[mad_ssid];
$math_ssid = $_REQUEST[math_ssid];

//�ư��S���P�B�ƴN�}�l���H
$sql =  "select grad_sn from grad_stud where stud_grad_year='{$year}' and class_year='6'";
$result = $CONN->Execute($sql);
list($grad_sn) =$result->FetchRow();
if(empty($grad_sn)){
	die('�u�Х��i����U��-���~�ͤɾǸ�ơv-�P�B�ơA�A��n�ǥʹNŪ�ꤤ');
}

$stud_data = get_stud_data($year,$semester,$jschool,$mad_ssid,$math_ssid);

$data = "�y����,�m�W,�ʧO,�첦�~��p�զW,�첦�~��p�Z��,�����Ҹ��X,�NŪ�ꤤ,��y,�ƾ�\r\n";

$i=1;

foreach($stud_data as $k=>$v){
	$data .= "{$i},{$v[stud_name]},{$v[sex]},{$school_sshort_name},{$v[classname]},{$v[stud_person_id]},{$v[new_school]},{$v[mad][�w�����q]},{$v[math][�w�����q]}\r\n";
	$i++;
}


$filename=$year."�Ǧ~".$school_sshort_name."���~�ǥͦ��Z�W�U.csv";
header("Content-disposition: attachment;filename=$filename");
header("Content-type: text/x-csv ; Charset=Big5");
header("Pragma: no-cache");
header("Expires: 0");

echo $data;



function get_stud_data($year,$semester,$jschool,$mad_ssid,$math_ssid){
	global $CONN;

//�����~��
	$sql =  "select a.student_sn,a.new_school,b.stud_name,b.stud_sex,b.stud_person_id,b.curr_class_num from grad_stud a left join stud_base b on a.student_sn = b.student_sn where a.stud_grad_year='{$year}' and a.class_year='6' and a.new_school='{$jschool}' order by a.new_school,b.curr_class_num";
  if($jschool=="�����ꤤ") $sql =  "select a.student_sn,a.new_school,b.stud_name,b.stud_sex,b.stud_person_id,b.curr_class_num from grad_stud a left join stud_base b on a.student_sn = b.student_sn where a.stud_grad_year='{$year}' and a.class_year='6' order by b.curr_class_num";
	$result = $CONN->Execute($sql);
	while(list($student_sn,$new_school,$stud_name,$stud_sex,$stud_person_id,$curr_class_num) =$result->FetchRow()){
		$stud_data[$student_sn][new_school]=$new_school;
		$stud_data[$student_sn][stud_name]=str_replace("�@","",str_replace(" ","",$stud_name));
		if($stud_sex=="1") $stud_data[$student_sn][sex]="�k";
		if($stud_sex=="2") $stud_data[$student_sn][sex]="�k";
		$stud_data[$student_sn][stud_person_id]=$stud_person_id;
		$stud_data[$student_sn][new_school]=$new_school;
		$stud_data[$student_sn][classname]=(int)substr($curr_class_num,1,2);

	}

	//�����~�ŤU�Ǵ��X�����q�Ҹ�
	$sql = "select performance_test_times,test_ratio,score_mode from score_setup where year='{$year}' and semester='{$semester}' and class_year='6'";
	$result = $CONN->Execute($sql);
	list($performance_test_times,$test_ratio,$score_mode) =$result->FetchRow();

	//����y�ƾǪ�ss_id
	/*
	$sql = "select ss_id,link_ss from score_ss where year='{$year}' and semester='{$semester}' and class_year='6' and (link_ss='�y��-����y��' or link_ss='�ƾ�')";
	$result = $CONN->Execute($sql);

	while(list($ss_id,$link_ss) =$result->FetchRow()){
		if($link_ss=='�y��-����y��') $subject6[mad]=$ss_id;
		if($link_ss=='�ƾ�') $subject6[math]=$ss_id;
	}
	*/
	$subject6[mad]=$mad_ssid;
	$subject6[math]=$math_ssid;

	//��Ǵ�����ƪ�
	$score_semester = "score_semester_".$year."_".$semester;

	$like_class_id = $year."_".$semester."_06_";
	//���ǥͰ�Ƥ���
	$sql= "select a.student_sn,a.ss_id,a.score,a.test_kind from {$score_semester} a right join grad_stud b on a.student_sn=b.student_sn where (a.ss_id='{$subject6[mad]}' or a.ss_id='{$subject6[math]}') and a.test_sort='$performance_test_times' and a.class_id like '{$like_class_id}%' and a.score >='0' and b.new_school='{$jschool}'";
	if($jschool=="�����ꤤ") $sql= "select student_sn,ss_id,score,test_kind from {$score_semester} where (ss_id='{$subject6[mad]}' or ss_id='{$subject6[math]}') and test_sort='$performance_test_times' and class_id like '{$like_class_id}%' and score >='0'";
	$result = $CONN->Execute($sql);
	//$i=1;
	while(list($student_sn,$ss_id,$score,$test_kind) =$result->FetchRow()){
		if(!empty($score)){
			if($ss_id == $subject6[mad]) $stud_data[$student_sn][mad][$test_kind]=$score;
			if($ss_id == $subject6[math]) $stud_data[$student_sn][math][$test_kind]=$score;
		}
	}
/*�Ķ��q���Z
	foreach($stud_data as $k=>$v){
	//�C����Ҥ�ҬۦP
		if($score_mode=="all"){
			$ratio = explode("-",$test_ratio);
			$stud_data[$k][mad][����]=round(($v[mad][�w�����q]*$ratio[0]+$v[mad][���ɦ��Z]*$ratio[1])/100,2);
			$stud_data[$k][math][����]=round(($v[math][�w�����q]*$ratio[0]+$v[math][���ɦ��Z]*$ratio[1])/100,2);
	//�p�G��Ҥ��P
		}elseif($score_mode=="severally"){
			$ratio1 = explode(",",$test_ratio);
			$ratio2= explode("-",$ratio1[$performance_test_times-1]);
			$stud_data[$k][mad][����]=round(($v[mad][�w�����q]*$ratio2[0]+$v[mad][���ɦ��Z]*$ratio2[1])/100,2);
			$stud_data[$k][math][����]=round(($v[math][�w�����q]*$ratio2[0]+$v[math][���ɦ��Z]*$ratio2[1])/100,2);
		}
	}

*/
	return $stud_data;
}
