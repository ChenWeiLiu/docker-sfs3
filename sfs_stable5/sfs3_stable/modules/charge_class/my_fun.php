<?php

// $Id: my_fun.php 5310 2009-01-10 07:57:56Z hami $

function get_item_class($item_id,$class_base,$selectd_class_id) {
	global $CONN;
	$sql="select DISTINCT MID(record_id,5,3) as class_id from charge_record where item_id=$item_id order by class_id";
	$res=$CONN->Execute($sql);
	$result="<select name='class_id' onChange='this.form.submit()'><option></option>";
	while(!$res->EOF){
		$class_id=$res->fields[class_id];
		$result.="<option value='$class_id'".($class_id==$selectd_class_id?" selected":"").">".$class_base[$class_id]."</option>";
		$res->MoveNext();
	}
	$result.="</select>";
	return $result;	
}


function get_grade_charge($item_id) {

	global $CONN,$m_arr;



	$grade_charge=array();

	if ($IS_JHORES==0) $grade_offset=1; else $grade_offset=7;

	$sql="select * from charge_detail where item_id='$item_id' order by detail_sort";

	//echo "<BR>$sql<BR>";

	

	$res=$CONN->Execute($sql);

	while(!$res->EOF){

		$grade_dollars=explode(',',$res->fields[dollars]);

		foreach($grade_dollars as $key=>$grade_dollar){

			$key+=$grade_offset;

			$detail=($m_arr['is_sort']=="Y"?$res->fields[detail_sort]."-":"").$res->fields[detail];

			$grade_charge[$key][$detail]=$grade_dollar;

		}

		$res->movenext();

	}

	//echo "<PRE>";

	//print_r($grade_charge);

	//echo "</PRE>";

	return $grade_charge;

}



function get_charge_decrease($item_id) {

	global $CONN,$m_arr;



	$charge_decrease=array();

	$grade_dollars=array();

	$grade_dollars=get_grade_charge($item_id);

	

	

	$sql="select a.*,b.item_id,b.detail_sort,b.detail from charge_decrease a,charge_detail b where item_id=$item_id AND a.detail_id=b.detail_id order by detail_id";

	$res=$CONN->Execute($sql);

	while(!$res->EOF){

		$detail=($m_arr['is_sort']=="Y"?$res->fields[detail_sort]."-":"").$res->fields[detail];

		$sn=$res->fields[student_sn];

		$my_grade=substr($res->fields[curr_class_num],0,1);



		//echo "<BR>".$res->fields[curr_class_num]."   the grade is $my_grade";

		//echo "<BR>VALUE OF GRADE --->".$grade_dollars[$my_grade][$detail];



		$grade_decrease[$sn][$detail][original]=$grade_dollars[$my_grade][$detail];

		$grade_decrease[$sn][$detail][percent]=$res->fields[percent];

		$grade_decrease[$sn][$detail][dollars]=$res->fields[percent]*($grade_dollars[$my_grade][$detail])/100;

		$grade_decrease[$sn][$detail][cause]=$res->fields[cause];

		$grade_decrease[$sn][$detail][should_paid]=$grade_decrease[$sn][$detail][original]-$grade_decrease[$sn][$detail][dollars];

		$res->movenext();

	}

	//計算總減免金額

	foreach($grade_decrease as $sn=>$value)

	{

		foreach($value as $item=>$value2)

		{

			$grade_decrease[$sn][total]+=$grade_decrease[$sn][$item][dollars];

		}

	}

	//echo "<PRE>";

	//print_r($grade_decrease);

	//echo "</PRE>";

	return $grade_decrease;

}



function get_item_stud_list($item_id,$selected_stud) {



	//$selected_stud格式為 $student_sn,$record_id,$stud_name,$guardian_name

	

	//取得各年級收費列表及計算減免金額

	$grade_dollars=get_grade_charge($item_id);

	$decrease_dollars=get_charge_decrease($item_id);

	

	//print_r($decrease_dollars);

	

	$data_list=array();

	foreach($selected_stud as $student)

	{

		$student=explode(",",$student);

		$student_sn=$student[0];

		$data_list[$student_sn][record_id]=$student[1];

		$data_list[$student_sn][stud_name]=$student[2];

		$data_list[$student_sn][guardian]=$student[3];

		

		//將年級收費細目加進來

		$grade=substr($student[1],4,1);

		foreach($grade_dollars[$grade] as $key=>$value)

		{

			$data_list[$student_sn][detail][$key][original]=$value;

			$data_list[$student_sn][detail][$key][percent]=$decrease_dollars[$student_sn][$key][percent];

			$data_list[$student_sn][detail][$key][decrease_dollars]=$decrease_dollars[$student_sn][$key][dollars];

			$data_list[$student_sn][detail][$key][cause]=$decrease_dollars[$student_sn][$key][cause];

			//計算應繳總額

			$data_list[$student_sn][total]+=$value-$data_list[$student_sn][detail][$key][decrease_dollars];

		}		

	}

	return $data_list;

}



function get_announce_template($dir){



	$dir_data=scandir($dir);

	array_shift($dir_data);

	array_shift($dir_data);

	$result="<select name='announce_template'>";

	foreach($dir_data as $value){

		if($value<>"" AND is_dir($dir."/".$value)) $result.="<option>$value</option>";		

	}

	$result.="</select>";



	return $result;

}



?>