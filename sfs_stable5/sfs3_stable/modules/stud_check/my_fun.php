<?php
// $Id: my_fun.php 9240 2018-05-04 03:25:04Z igogo $

function sub_menu($sub_menu_arr=array(),$id="",$s_name="") {
	global $CONN;

	$sm = new drop_select();
	$sm->s_name =($s_name=="")?"sub_menu_id":$s_name;
	$sm->has_empty = false;
	$sm->id = $id;
	$sm->arr = $sub_menu_arr;
	$sm->is_submit = true;
	if ($s_name=="kmenu_id" && $id) $sm->other_script = "document.myform.target='';document.getElementById('act').value='';document.getElementById('sn').value=''";
	return $sm->get_select();
}

function year_menu($sel_year,$other_script="") {
	global $CONN;

	$s = new drop_select();
	$s->s_name ="sel_year";
	$s->top_option = "選擇學年";
	$s->id = sprintf("%03d",$sel_year);
	$s->arr = get_class_year();
	$s->is_submit = true;
	$s->other_script = $other_script;
	return $s->get_select();
}

function year_seme_menu($sel_year,$sel_seme,$other_script="") {
	global $CONN;

	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "選擇學期";
	$scys->id = sprintf("%03d",$sel_year).$sel_seme;
	$scys->arr = get_class_seme();
	$scys->is_submit = true;
	$scys->other_script = $other_script;
	return $scys->get_select();
}

function class_menu($sel_year,$sel_seme,$id,$other_script="",$mode=0) {
	global $school_kind_name,$class_year,$CONN;

	$scy = new drop_select();
	$scy->s_name ="class_name";
	$scy->top_option = ($mode==2)?"選擇年級":"選擇班級";
	$scy->id = $id;
	if ($mode!=2) $tmp_arr = class_base(sprintf("%03d",$sel_year).$sel_seme);
	if ($mode!=0) {
		foreach($class_year as $k=>$v) if (intval($k)>0) $tmp_arr[$k] = $v."級";
		if ($mode!=2) $tmp_arr["all"] = "全校";
	}
	$scy->arr = $tmp_arr;
	$scy->is_submit = true;
	$scy->other_script = $other_script;
	return $scy->get_select();
}

function stud_menu($sel_year,$sel_seme,$sel_class,$id,$other_script="",$mode=0) {
	global $CONN;

	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$s = new drop_select();
	$s->s_name ="student_sn";
	$s->top_option = "選擇學生";
	$s->id = $id;
	$tmp_arr=array();
	$tmp_str="";
	$query = "select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$sel_class' order by seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$tmp_arr[$res->fields['student_sn']]="(".$res->fields['seme_num'].") ";
		$tmp_str.="'".$res->fields['student_sn']."',";
		$res->MoveNext();
	}
	if ($tmp_str) {
		$tmp_str = substr($tmp_str,0,-1);
		$query="select * from stud_base where student_sn in ($tmp_str)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			switch ($res->fields['stud_sex']) {
				case 1: $c=1; break;
				case 2: $c=0; break;
				default: $c=3;
			}
			$tmp_arr[$res->fields['student_sn']] .= $res->fields['stud_name'];
			$c_arr[$res->fields['student_sn']] = $c;
			$res->MoveNext();
		}
	}
	$s->arr = $tmp_arr;
	$s->is_display_color = true;
	$s->color_index_arr = $c_arr;
	$s->is_submit = true;
	$s->other_script = $other_script;
	return $s->get_select();
}

function get_stu_arr($sel_year,$sel_seme,$id) {
	global $CONN,$study_str;

	$query = "select a.student_sn,a.stud_name,a.stud_sex,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='".sprintf("%03d",$sel_year).$sel_seme."' and b.seme_class='$id' and a.stud_study_cond in ($study_str) order by b.seme_num";
	$res=$CONN->Execute($query);
	return $res->GetRows();
}
?>
