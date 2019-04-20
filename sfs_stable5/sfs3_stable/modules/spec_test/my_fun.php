<?php
// $Id: my_fun.php 5310 2009-01-10 07:57:56Z hami $

function year_seme_menu($sel_year,$sel_seme) {
	global $CONN;

	$sql="select year,semester from school_class where enable='1' order by year,semester";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$year=$rs->fields["year"];
		$semester=$rs->fields["semester"];
		if ($year!=$oy || $semester!=$os)
			$show_year_seme[$year."_".$semester]=$year."學年度第".$semester."學期";
		$oy=$year;
		$os=$semester;
		$rs->MoveNext();
	}
	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "選擇學期";
	$scys->id = $sel_year."_".$sel_seme;
	$scys->arr = $show_year_seme;
	$scys->is_submit = true;
	return $scys->get_select();
}

function test_menu($sel_year,$sel_seme,$id) {
	global $CONN;

	$sql="select id,title from test_manage where year='$sel_year' and semester='$sel_seme' order by id desc";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$title[$rs->fields[id]]=$rs->fields[title];
		$rs->MoveNext();
	}
	$title_keys=array_keys($title);
	if (!in_array($id,$title_keys)) $id="";
	$st = new drop_select();
	$st->s_name ="id";
	$st->top_option = "選擇測驗";
	$st->id = $id;
	$st->arr = $title;
	$st->is_submit = true;
	return $st->get_select();
}

function class_menu($sel_year,$sel_seme,$c_year,&$class_id) {
	global $CONN,$class_year;

	$class_str=($c_year)?"and c_year='$c_year'":"";
	$sql="select class_id,c_year,c_name from school_class where enable='1' and year='$sel_year' and semester='$sel_seme' $class_str order by class_id";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$class[$rs->fields[class_id]]=$class_year[$rs->fields[c_year]].$rs->fields[c_name]."班";
		$rs->MoveNext();
	}
	$class_keys=array_keys($class);
	if (!in_array($class_id,$class_keys)) $class_id="";
	$sc = new drop_select();
	$sc->s_name ="class_id";
	$sc->top_option = "選擇班級";
	$sc->id = $class_id;
	$sc->arr = $class;
	$sc->is_submit = true;
	return $sc->get_select();
}
?>
