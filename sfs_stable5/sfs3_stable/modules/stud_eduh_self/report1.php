<?php

// $Id: report1.php qfon $

/* ���o�]�w�� */

include "config.php";

// �{���ˬd
sfs_check();

// ���O�d�d��
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}





//�u����Ǵ�
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();

$stud_id=$_SESSION['session_log_id'];


//���o�n�J�ǥͪ��Ǹ��M�y����
$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='".$stud_id."'";
$res=$CONN->Execute($query);
$student_sn=$res->fields['student_sn'];
if ($student_sn) {
	$query="select * from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query);
	if ($res->fields['stud_study_cond']!="0") {
		$student_sn="";
	} else {
		$stud_study_year=$res->fields['stud_study_year'];
	}
}



$main=&mainForm();

//�L�X���Y
head("���m�ҩ��Ӭd��");

//�Ҳտ��
print_menu($menu_p,$linkstr);

if($stud_view_self_absent) echo $main;
foot();

//�D�n��J�e��
function &mainForm(){
	global $CONN,$stud_id,$student_sn,$stud_study_year;

	$sql = "select year,semester,class_id,date,absent_kind,section from stud_absent where stud_id='$stud_id' and year>='$stud_study_year' order by year,semester,date";
	$rs=$CONN->Execute($sql) or trigger_error("SQL�y�k���~�G $sql", E_USER_ERROR);
	
	while (!$rs->EOF) {
		$absent_kind=$rs->fields['absent_kind'];
		$class_id = $rs->fields['class_id'];
		$date = $rs->fields['date'];
		$section = $rs->fields['section'];
		$semester = $rs->fields['year'].'-'.$rs->fields['semester'];
		
		
		if ($section=="allday")$sectionx="1��";
		else if ($section=="uf")$sectionx="�ɺX";
		else if ($section=="df")$sectionx="���X";
		else $sectionx="��".$section."�`";
		$cx=explode("_",$class_id);
		
		if ($cx[2]=="07" || $cx[2]=="01")$cx[2]="1";
		if ($cx[2]=="08" || $cx[2]=="02")$cx[2]="2";
		if ($cx[2]=="09" || $cx[2]=="03")$cx[2]="3";
		if ($cx[2]=="04")$cx[2]="4";
		if ($cx[2]=="05")$cx[2]="5";
		if ($cx[2]=="06")$cx[2]="6";
		
		$cx[3]=get_class_name($class_id);
		/*
		$colorz="white";
		if ($absent_kind=="�ư�")$colorz="#FEFED7";
		if ($absent_kind=="�f��")$colorz="#FEFEC4";
		if ($absent_kind=="�m��")$colorz="#FEFEB1";
		if ($absent_kind=="��L")$colorz="#FEFE8B";
		*/
		
		$datas[$semester][$date][$absent_kind].="$section,";
		//$main0.="<tr  align='center'><td bgcolor='$colorz'>$cx[0]�Ǧ~�ײ�{$cx[1]}�Ǵ�</td><td bgcolor='$colorz'>$cx[2]�~$cx[3]�Z</td><td bgcolor='$colorz'>$date</td><td bgcolor='$colorz'>$absent_kind $sectionx</td></tr>";
		
		$rs->MoveNext();
	}


	
	
	/*
	echo "<pre>";
	print_r($datas);
	echo "</pre>";
	*/
	
	//$main0.="<li>�Ǵ��G$semester</li>";
	$main0.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>";
	$main0.="<tr align='center' bgcolor='#C6D7F2'><td>�Ǵ�</td><td>���</td><td>���O</td><td>�`��</td></tr>"; //<td>�NŪ�Z��</td>
	foreach($datas as $semester => $v) {
		$detail='';
		foreach($v as $date => $data) {
			foreach( $data as $kind => $value ) {
				$value=substr($value,0,-1);
				$detail.="<tr align='center' bgcolor='$colorz'><td>$date</td><td>$kind</td><td>$value</td></tr>";
				$rowspan++;				
			}
		}
		$rowspan++;	
		$main0.="<tr align='center'><td rowspan='$rowspan'>$semester</td></tr>";
		$main0.=$detail;
	}
	$main0.="</table>";
	
	/*
	echo "<textarea>";
	echo $main0;
	echo "</textarea>";
	exit;
	*/
	
	/*
	while (!$rs->EOF) {
		$absent_kind=$rs->fields['absent_kind'];
		$class_id = $rs->fields['class_id'];
		$date = $rs->fields['date'];
		$section = $rs->fields['section'];
		
		
		if ($section=="allday")$sectionx="1��";
		else if ($section=="uf")$sectionx="�ɺX";
		else if ($section=="df")$sectionx="���X";
		else $sectionx="��".$section."�`";
		$cx=explode("_",$class_id);
		
		if ($cx[2]=="07" || $cx[2]=="01")$cx[2]="1";
		if ($cx[2]=="08" || $cx[2]=="02")$cx[2]="2";
		if ($cx[2]=="09" || $cx[2]=="03")$cx[2]="3";
		if ($cx[2]=="04")$cx[2]="4";
		if ($cx[2]=="05")$cx[2]="5";
		if ($cx[2]=="06")$cx[2]="6";
		
		$cx[3]=get_class_name($class_id);
		$colorz="white";
		if ($absent_kind=="�ư�")$colorz="#FEFED7";
		if ($absent_kind=="�f��")$colorz="#FEFEC4";
		if ($absent_kind=="�m��")$colorz="#FEFEB1";
		if ($absent_kind=="��L")$colorz="#FEFE8B";
		$main0.="<tr  align='center'><td bgcolor='$colorz'>$cx[0]�Ǧ~�ײ�{$cx[1]}�Ǵ�</td><td bgcolor='$colorz'>$cx[2]�~$cx[3]�Z</td><td bgcolor='$colorz'>$date</td><td bgcolor='$colorz'>$absent_kind $sectionx</td></tr>";
		$rs->MoveNext();
		
		
	}
     $main0.="</table>";
	*/
	return $main0;
}

//���o�Z�ŦW��
function get_class_name($class_id){
	global $CONN;

	$sql_select = "select c_name from school_class where class_id='$class_id' and enable='1'";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
    while (!$recordSet->EOF) {
		$c_name=$recordSet->fields['c_name'];
		$recordSet->MoveNext();
	}
	return $c_name;
}



?>
