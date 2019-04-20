<?php
// $Id: my_fun.php 8850 2016-03-11 03:35:43Z chiming $

function year_seme_menu($sel_year,$sel_seme) {

	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "選擇學期";
	$scys->id = sprintf("%03d",$sel_year).$sel_seme;
	$scys->arr = get_class_seme();
	$scys->is_submit = true;
	$scys->other_script="this.form.act.value=''";
	return $scys->get_select();
}

function class_name_menu($sel_year,$sel_seme,$id) {

	$sc = new drop_select();
	$sc->s_name ="me";
	$sc->top_option = "選擇班級";
	$sc->id = $id;
	$sc->arr = class_base(sprintf("%03d",$sel_year).$sel_seme);
	$sc->is_submit = true;
	$sc->other_script="this.form.act.value=''";
	return $sc->get_select();
}

function class_menu($sel_year,$sel_seme,$id,$arr=array()) {

	if (empty($arr)) {
		$temp_arr=class_base(sprintf("%03d",$sel_year).$sel_seme);
		$arr=array($id=>$temp_arr[$id]);
	}
	$sc = new drop_select();
	$sc->s_name ="me";
	$sc->has_empty = false;
	$sc->id = $id;
	$sc->arr = $arr;
	$sc->is_submit = true;
	return $sc->get_select();
}

//讀取學生體適能資料
function read_fitness($seme_year_seme,$stud_str) {
	global $CONN;

	$query="select * from fitness_data where student_sn in ($stud_str) and c_curr_seme='$seme_year_seme'";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$d=array();
		$d=$res->FetchRow();
		$g=0;
		$s=0;
		$c=0;
		for($i=1;$i<=4;$i++) {
			$str="prec".$i;
			if($d[$str]>=85) $g++;
			if($d[$str]>=75) $s++;
			if($d[$str]>=50) $c++;
		}
		if ($g==4) $d[reward]="金";
		elseif ($s==4) $d[reward]="銀";
		elseif ($c==4) $d[reward]="銅";
		$temp[$d[student_sn]]=$d;
	}
	return $temp;
}

// 計算百分等級
function cal_per($grade,$sex,$age,$s) {
	global $CONN,$IS_JHORES;

	if ($IS_JHORES==0 && $grade==5 && $sex==1 && $age>12) $age=12; //國小男女心肺 800m
	if ($IS_JHORES==6 && $grade==5 && $sex==1 && $age<13) $age=13; //國中男生心肺 1600M

	$query="select * from fitness_mod where grade='$grade' and sex='$sex' and age='$age'";
	$res=$CONN->Execute($query);
	$row=$res->FetchRow();
	for($i=1;$i<100;$i++){
		$files="p".$i;
		$m[$i]=$row[$files];
	}
	$prec="";
	if($s>0){
		for($i=99;$i>=1;$i--) {
			if($grade==5){
				if($s<=$m[$i]) {
					$prec=$i;
					$i=-1;
				}
			} else {
				if($s>=$m[$i]) {
					$prec=$i;
					$i=-1;
				}
			}
		}
		if($prec==0) $prec=1;
	}
	return $prec;
}
?>
