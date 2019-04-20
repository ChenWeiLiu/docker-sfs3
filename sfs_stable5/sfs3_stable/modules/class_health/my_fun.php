<?php
// $Id: my_fun.php 9240 2018-05-04 03:25:04Z igogo $

function stud_menu($sel_year,$sel_seme,$sel_class,$sel_num,$sel_sn) {
	global $CONN;

	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$sel_class.sprintf("%02d",$sel_num);
	$query="select a.*,b.stud_name,b.stud_sex from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' order by a.seme_num";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$temp_arr[$res->fields['student_sn']]=$res->fields['stud_name'];
		$cr_arr[$res->fields['student_sn']]=$res->fields['stud_sex'];
		$res->MoveNext();
	}
	$s = new drop_select();
	$s->s_name ="student_sn";
	$s->top_option = "選擇學生";
	$s->id = $sel_sn;
	$s->arr = $temp_arr;
	//依性別顯示顏色
	$s->is_display_color = true;
	$s->color_index_arr = $cr_arr;
	$s->color_item = array("black","blue","red");
	$s->is_submit = false;
	return $s->get_select();
}

function teacher_sn_to_class_name($teacher_sn){
    global $CONN;

	$query="select class_num from teacher_post where teacher_sn='$teacher_sn'";
	$res=$CONN->Execute($query);
	$class_num = $res->fields["class_num"];
	if($class_num=="") trigger_error("您沒有擔任導師！",E_USER_ERROR);
	return $class_num;
}
?>