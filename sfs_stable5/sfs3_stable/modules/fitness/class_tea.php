<?php
// $Id: print.php 8655 2015-12-21 04:21:47Z chiming $
// ���o�]�w��
include "config.php";

sfs_check();

//�D���]�w
$tool_bar=&make_menu($menu_p);

//���w�Ǵ�
$this_year=curr_year();
$this_seme=curr_seme();
$seme_year_seme=(empty($_REQUEST[seme_year_seme]))?$this_year.$this_seme:$_REQUEST[seme_year_seme];

//�Ǵ����
$sel_seme = year_seme_menu2($seme_year_seme);


//���ɮv�Z��
$class_num=get_teach_class();

//�޲z��
if ($admin==1){
	$class_num=$_REQUEST[sel_class];
	$class_menu = class_name_menu2($this_year,$this_seme,$class_num,$id);
}

//��X
head();
echo $tool_bar;
if(!empty($class_num)){
	echo "<form method=post name=myform>".$sel_seme.$class_menu."</form>";
	$table = get_class_studs($seme_year_seme,$class_num);
}else{
	if($admin){
		echo "<form method=post name=myform>".$sel_seme.$class_menu."</form>";
		echo "<br>�޲z�̽п�ܯZ��";
	}else{	
		echo "�A���O�ɮv�I";
	}
}


echo $table;

foot();


function get_class_studs($seme_year_seme,$class_num){
	$class_y = substr($class_num,0,1);
	$class_c = substr($class_num,1,2);
	$studs=get_stud_array($year=curr_year(),$seme=curr_seme(),$class_y,$class_c,$k="sn",$v="name");
	
	foreach($studs as $k=>$v){
		$studs_sn .= $k."," ;
	}
	$studs_sn = substr($studs_sn,0,-1);
	$studs_fitness_data=get_fitness_data ($seme_year_seme,$studs_sn);
	$table = "<br><font color=gold>��</font>85�H�H�W <font color=silver>��</font>75�H�H�W <font color=bronze>��</font>50�H�H�W <font color=red>��</font>24�H�H�U<table cellspacing='1' cellpadding='3' bgcolor='#000000'><tr bgcolor=#cccccc><td>�y��</td><td>�m�W</td><td>����</td><td>�魫</td><td>BMI</td><td>�����e�s</td><td>�ߩw����</td><td>���װ_��</td><td>�ߩw����</td></tr>";

	foreach($studs as $k=>$v){
		$stud_num=student_sn_to_site_num($k);
		$stud_name=str_replace(" ","",str_replace("�@","",$v));
		$stud_sex=chk_stu_sex($k);
		$font_color = ($stud_sex=="�k")?"blue":"red";
		$c_t = font_color($studs_fitness_data[$k][prec_t]);
		$c_w = font_color($studs_fitness_data[$k][prec_w]);
		$c_b = font_color($studs_fitness_data[$k][prec_b]);
		$c_t1 = font_color($studs_fitness_data[$k][prec1]);
		$c_t2 = font_color($studs_fitness_data[$k][prec2]);
		$c_t3 = font_color($studs_fitness_data[$k][prec3]);
		$c_t4 = font_color($studs_fitness_data[$k][prec4]);
		$table .= "<tr bgcolor=#ffffff onmouseover=\"this.style.backgroundColor='#FFCDE5';\" onMouseOut=\"this.style.backgroundColor='#FFFFFF';\"><td>{$stud_num}</td><td><font color={$font_color}>{$stud_name}</font></td><td>{$studs_fitness_data[$k][tall]}<font color={$c_t}>[{$studs_fitness_data[$k][prec_t]}]</font></td><td>{$studs_fitness_data[$k][weigh]}<font color={$c_w}>[{$studs_fitness_data[$k][prec_w]}]</font></td><td>{$studs_fitness_data[$k][bmt]}<font color={$c_b}>[{$studs_fitness_data[$k][prec_b]}]</font></td><td>{$studs_fitness_data[$k][test1]}<font color={$c_t1}>[{$studs_fitness_data[$k][prec1]}]</font></td><td>{$studs_fitness_data[$k][test3]}<font color={$c_t3}>[{$studs_fitness_data[$k][prec3]}]</font></td><td>{$studs_fitness_data[$k][test2]}<font color={$c_t2}>[{$studs_fitness_data[$k][prec2]}]</font></td><td>{$studs_fitness_data[$k][test4]}<font color={$c_t4}>[{$studs_fitness_data[$k][prec4]}]</font></td></tr>";
	}
	$table .="</table>";
	return $table;
}


function get_fitness_data ($seme_year_seme,$studs_sn) {
	global $CONN;  
	$sql="select student_sn,tall,prec_t,weigh,prec_w,bmt,prec_b,test1,prec1,test2,prec2,test3,prec3,test4,prec4 from fitness_data where c_curr_seme='$seme_year_seme' and student_sn in ($studs_sn)";
	$res=$CONN->Execute($sql);
	while(list($student_sn,$tall,$prec_t,$weigh,$prec_w,$bmt,$prec_b,$test1,$prec1,$test2,$prec2,$test3,$prec3,$test4,$prec4)=$res->fetchrow()){
		$studs_fitness_data[$student_sn][tall]=$tall;
		$studs_fitness_data[$student_sn][prec_t]=$prec_t;
		$studs_fitness_data[$student_sn][weigh]=$weigh;
		$studs_fitness_data[$student_sn][prec_w]=$prec_w;
		$studs_fitness_data[$student_sn][bmt]=$bmt;
		$studs_fitness_data[$student_sn][prec_b]=$prec_b;
		$studs_fitness_data[$student_sn][test1]=$test1;
		$studs_fitness_data[$student_sn][prec1]=$prec1;
		$studs_fitness_data[$student_sn][test2]=$test2;
		$studs_fitness_data[$student_sn][prec2]=$prec2;
		$studs_fitness_data[$student_sn][test3]=$test3;
		$studs_fitness_data[$student_sn][prec3]=$prec3;
		$studs_fitness_data[$student_sn][test4]=$test4;
		$studs_fitness_data[$student_sn][prec4]=$prec4;
	}
	return $studs_fitness_data;
}
//�Ǵ������
function year_seme_menu2($seme_year_seme) {
	 $semes = get_class_seme();
	 $select = "<select name=seme_year_seme onchange='submit();'>";
	foreach($semes as $k=>$v){
		$default=($seme_year_seme==$k)?"selected":"";
		$select .="<option value={$k} {$default}>{$v}</option>";
	}
	$select .="</select>";
	return $select;
}
//�d�ʧO
function chk_stu_sex($stu_sn){
	global $CONN;
	$str = "select stud_sex from stud_base where student_sn = '".$stu_sn."'";
	$result = $CONN->Execute($str) or die ($str);
	list($stu_sex)=$result->FetchRow();
	if($stu_sex=="1") $cht_stu_sex= "�k";
	if($stu_sex=="2") $cht_stu_sex= "�k";
	return $cht_stu_sex;
}
function font_color($num){
	if($num>=85) $color="gold";
	if($num>=75 and $num <85) $color="silver";
	if($num>=50 and $num <75) $color="bronze";
	if($num>=25 and $num <50) $color="black";
	if($num<=24) $color="red";
	return $color;
}
function class_name_menu2($sel_year,$sel_seme,$id) {

	$sc = new drop_select();
	$sc->s_name ="sel_class";
	$sc->top_option = "��ܯZ��";
	$sc->id = $id;
	$sc->arr = class_base(sprintf("%03d",$sel_year).$sel_seme);
	$sc->is_submit = true;
	//$sc->other_script="this.form.act.value=''";
	return $sc->get_select();
}
?>
