<?php

include "config.php";
include_once "../../include/sfs_case_dataarray.php";

sfs_check();


if($_POST['act']=='���]�����q�Z'){
	$sql="UPDATE school_class SET c_kind_k12ea='A' WHERE class_id like '{$_POST['work_year_seme']}_%'";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);	
}


if($_POST['act']=='�x�s'){
	/*
	echo "<pre>";
	print_r($_POST['kind_select']);
	echo "</pre>";
	*/
	foreach($_POST['kind_select'] as $k => $v) {
		$sql="UPDATE school_class SET c_kind_k12ea='$v' WHERE class_sn=$k";
		$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	}
}



//�q�X����
head("�Z�������]�w");
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


//���o�Z�����O�C��(�]�w�ﶵ)
$class_kind = k12ea_class_kind();
$class_kind['']='�����Z (����X�Ҫ�)';

//���ͯZ�ų]�w�C��
$sql="SELECT * FROM school_class WHERE class_id LIKE '{$work_year_seme}_%' ORDER BY class_id";
$res=$CONN->Execute($sql) or user_error("Ū���Ҫ�]�w��ƥ��ѡI<br>$sql",256);
while(!$res->EOF) {
	$class_sn=$res->fields['class_sn'];	
	$c_year=$res->fields['c_year'];	
	$c_name=$res->fields['c_name'];
	$kind=$res->fields['c_kind_k12ea'];
	$kind_select="<select name='kind_select[$class_sn]'>";
	foreach($class_kind as $k => $v) {
		$selected = ($k==$kind) ? 'selected' : '' ;
		$bg_color = ($k==$kind) ? "style='background-color: #ffcccc;'" : '' ;
		$kind_select.="<option value='$k' $selected $bg_color>$k : $v</option>";
	}
	$kind_select.="</select>";
	
	$classes .= "<li>{$c_year}�~{$c_name}�Z ( $kind )�G$kind_select</li>";
	
	$res->MoveNext();
}

$button = "<input type='submit' value='���]�����q�Z' name='act' style='border-width:1px; cursor:hand; color:white; background:#5555ff;' onclick=\"return confirm('�T�w�n�N�|���x�s�������Z�ų]�����q�Z�H');\">";
$button .= "<input type='submit' value='�x�s' name='act' style='border-width:1px; cursor:hand; color:white; background:#ff5555;'>";


echo "<form name='myform' method='post'>��ܾǴ��G $semesters $button<hr><ol>$classes</ol></form>";


foot();
?>