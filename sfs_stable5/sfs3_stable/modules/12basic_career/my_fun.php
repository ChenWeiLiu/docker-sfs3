<?php

function get_recent_semester_select($select_name,$default)
{
	$seme_list=get_class_seme();
	$recent_semester="<select name='$select_name' onchange='this.form.submit()'><option value=''>- 請選擇學期 -</option>";
	$curr_year=curr_year();
	foreach($seme_list as $key=>$value){
		$thisyear=substr($key,0,-1);
		$thisseme=substr($key,-1);
		if($curr_year-$thisyear<3)
		$recent_semester.="<option ".($key==$default?"selected":"")." value='$key'>$value</option>";
	}
	$recent_semester.="</select>";
	
	return $recent_semester;
}

function get_semester_grade($select_name,$year,$semester,$default='')
{
	global $class_year;
	$grade_arr=get_class_year_array($year,$semester);
	$grade_semester="<select name='$select_name' onchange='this.form.submit()'><option value=''>- 請選擇年級 -</option>";
	foreach($grade_arr as $key=>$value){
		$grade_semester.="<option ".($key==$default?"selected":"")." value='$key'>{$class_year[$value]}級</option>";
	}
	$grade_semester.="</select>";
	
	return $grade_semester;
}


function get_student_sn_list($year_seme,$grade)
{
	global $CONN;
	$student_sn_list='';
	$sql_select="select a.student_sn from stud_seme a inner join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$year_seme' and seme_class like '$grade%' and b.stud_study_cond in (0,15) order by student_sn";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$listed=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$student_sn_list.="'$student_sn',";
		$recordSet->MoveNext();
	}
	$student_sn_list=substr($student_sn_list,0,-1);
	return $student_sn_list;	
}



//抓取學生學期就讀班級
function get_student_seme($student_sn){
	global $CONN;
	$stud_seme_arr=array();
	$table=array('stud_seme_import','stud_seme');
	foreach($table as $key=>$value){
		$query="select * from $value where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		while(!$res->EOF){
			$stud_grade=substr($res->fields['seme_class'],0,-2);
			$year_seme=$res->fields['seme_year_seme'];
			$semester=substr($year_seme,-1);	
			$seme_key=$stud_grade.'-'.$semester;
			$stud_seme_arr[$seme_key]=$year_seme;
			//抓取本學期相關資料
			if($year_seme==$seme_year_seme) {
				$curr_stud_grade=$stud_grade;
				$curr_seme_class=$res->fields['seme_class'];
				$curr_seme_num=$res->fields['seme_num'];
				$curr_seme_key=$seme_key;
			}
			$res->MoveNext();
		}
	}
	//進行排序
	asort($stud_seme_arr);
	return $stud_seme_arr;
}


//抓取學生學期就讀班級
function get_student_seme_list($student_sn){
	global $CONN,$class_year;
	
	$stud_seme_arr=array();
	$table=array('stud_seme_import','stud_seme');
	foreach($table as $key=>$value){
		$query="select * from $value where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		while(!$res->EOF){
			$stud_grade=substr($res->fields['seme_class'],0,-2);
			$year_seme=$res->fields['seme_year_seme'];
			$semester=substr($year_seme,-1);	
			$seme_key=$stud_grade.'-'.$semester;

			$stud_seme_arr[$seme_key]['year_seme']=$year_seme;
			$stud_seme_arr[$seme_key]['seme_class']=$res->fields['seme_class'];
			$stud_seme_arr[$seme_key]['seme_class_name']=$class_year[$stud_grade].$res->fields['seme_class_name'].'班';
			$stud_seme_arr[$seme_key]['seme_num']=$res->fields['seme_num'];
			
			$res->MoveNext();
		}
	}
	//進行排序
	asort($stud_seme_arr);
	return $stud_seme_arr;
}

//抓取處室聯絡電話　　room_name room_tel room_fax 
function get_room_tel(){
	global $CONN;
	$query="select * from school_room where enable='1'";
	$res=$CONN->Execute($query);
	while(!$res->EOF){
		$room_name=$res->fields['room_name'];
		$room_tel[$room_name]=$res->fields['room_tel'];
		$res->MoveNext();
	}
	return $room_tel;
}
?>