<?php

include "config.php";
include_once "../../include/sfs_case_dataarray.php";

sfs_check();

if($_POST['act']=='�x�s'){
	/*
	echo "<pre>";
	print_r($_POST['campus']);
	echo "</pre>";
	*/
	foreach($_POST['campus'] as $k => $v) {
		$sql="UPDATE school_class SET campus='$v' WHERE class_sn=$k";
		$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
		$note = "�w��".date("Y-m-d H:i:s")."�x�s�]�w�I";
	}
}

if($_POST['act']=='���M��'){
	$sql="UPDATE school_class SET campus='' WHERE class_id like '{$_POST['work_year_seme']}_%'";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$note = "�w��".date("Y-m-d H:i:s")."�M�ŭ�]�w�I";
}



//�q�X����
head("�Z�Ůհϳ]�w");
print_menu($school_menu_p);

//�Ǵ��O
$work_year_seme = $_REQUEST['work_year_seme'] ? $_REQUEST['work_year_seme'] : sprintf("%03d_%d",curr_year(),curr_seme());


//��V������
echo print_menu($menu_p);

//���o�~�׻P�Ǵ����U�Կ��
$sql="SELECT distinct year,semester FROM school_class ORDER BY year desc,semester desc limit 10";
$res=$CONN->Execute($sql) or user_error("Ū���Ҫ�]�w��ƥ��ѡI<br>$sql",256);
$semesters="<select name='work_year_seme' onchange=\"this.form.target=''; this.form.submit();\"><option value=''>-�п�ܾǴ�-</option>";
while(!$res->EOF) {
	$year_seme=sprintf("%03d_%d",$res->fields[year],$res->fields[semester]);
	$year_seme_name=$res->fields[year].'�Ǧ~�ײ�'.$res->fields[semester].'�Ǵ�';
	$selected=( $work_year_seme == $year_seme )?'selected':''; 
	$semesters.="<option $selected value=$year_seme>$year_seme_name</option>";
	
	$res->MoveNext();
}
$semesters.="</select>";


//���ͯZ�ų]�w�C��
$sql="SELECT * FROM school_class WHERE class_id LIKE '{$work_year_seme}_%' ORDER BY class_id";
$res=$CONN->Execute($sql) or user_error("Ū���Ҫ�]�w��ƥ��ѡI<br>$sql",256);
while(!$res->EOF) {
	$class_sn=$res->fields['class_sn'];	
	$c_year=$res->fields['c_year'];	
	$c_name=$res->fields['c_name'];
	$campus=$res->fields['campus'];
	
	$classes .= "<li>{$c_year}�~{$c_name}�Z �G<input type='text' length=10 name='campus[$class_sn]' value='$campus'></li>";
	
	$res->MoveNext();
}

$button = "<input type='submit' value='���M��' name='act' style='border-width:1px; cursor:hand; color:white; background:#5555ff;' onclick=\"return confirm('�T�w�n�N�w�]�w���Z�ũҳB�հϲM�šH');\">";
$button .= "<input type='submit' value='�x�s' name='act' style='border-width:1px; cursor:hand; color:white; background:#ff5555;'>";

$note = $note ? $note : "<font color='brown'> �L���թΤ��Z�A�Х��Ưd�ŧY�i�I</font>";
echo "<form name='myform' method='post'>��ܾǴ��G $semesters $button<hr>$note<hr><ol>$classes</ol></form>";


foot();
?>