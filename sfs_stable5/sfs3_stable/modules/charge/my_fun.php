<?php

// $Id: my_fun.php 6413 2011-04-21 07:19:56Z infodaes $

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

function get_item_detail_list($item_id) {
	global $CONN;
	$sql="select detail_sort,detail from charge_detail where item_id=$item_id order by detail_sort";
	$res=$CONN->Execute($sql);
	$item_detail=array();
	while(!$res->EOF){
		$item_detail[]=$res->fields[detail_sort]."-".$res->fields[detail];
		$res->MoveNext();
	}
	return $item_detail;	
}

function get_item_detail_list_multi($item_id_list) {
	global $CONN;
	$sql="select detail_sort,detail from charge_detail where item_id in ($item_id_list)";
	$res=$CONN->Execute($sql);
	$item_detail=array();
	while(!$res->EOF){
		$detail_sort=$res->fields[detail_sort];
		$item_detail[$detail_sort]=$res->fields[detail];
		$res->MoveNext();
	}
	return $item_detail;	
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

		$grade_decrease[$sn][$detail][dollars]=round($res->fields[percent]*($grade_dollars[$my_grade][$detail])/100);

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

	global $UPLOAD_PATH;

	//$dir_data=scandir($dir);
	$op = opendir($dir);
	while (false !== ($filename = readdir($op))) {
		$dir_data[] = $filename;
	}

	array_shift($dir_data);

	array_shift($dir_data);

	$result="<select name='announce_template'>";

	foreach($dir_data as $value){

		if($value<>"" AND is_dir($dir."/".$value)) $result.="<option value='$dir/$value'>$value</option>";		

	}
	
	//檢查是否有上傳格式
	$myown_dir=$UPLOAD_PATH."charge";
	if(file_exists("$myown_dir/content.xml")) $myown_template="<option value='$myown_dir' selected>自訂上傳的格式</option>"; else $myown_template="";
	$result.="$myown_template</select>";
	return $result;

}

function get_item_all_stud_list($item_id) {
	global $CONN;
	
	//取得各年級收費列表及計算減免金額
	$grade_dollars=get_grade_charge($item_id);
	$decrease_dollars=get_charge_decrease($item_id);

	//print_r($decrease_dollars);
	$data_list=array();

	//取得所有收費學生名單
	$sql="select a.*,b.stud_name,b.stud_id,b.stud_birthday,year(b.stud_birthday) as birth_year,month(b.stud_birthday) as birth_month,dayofmonth(b.stud_birthday) as birth_day,b.stud_person_id from charge_record a,stud_base b where a.student_sn=b.student_sn AND item_id=$item_id ORDER BY record_id";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);;

	while(!$res->EOF){
	
		$student_sn=$res->fields[student_sn];

		$data_list[$student_sn][record_id]=$res->fields[record_id];
		$data_list[$student_sn][stud_id]=$res->fields[stud_id];
		$data_list[$student_sn][stud_birthday]=$res->fields[stud_birthday];
		
		$data_list[$student_sn][birth_year]=$res->fields[birth_year]-1911;
		$data_list[$student_sn][birth_month]=$res->fields[birth_month];
		$data_list[$student_sn][birth_day]=$res->fields[birth_day];
		
		
		$data_list[$student_sn][stud_person_id]=$res->fields[stud_person_id];
		
		$data_list[$student_sn][stud_name]=$res->fields[stud_name];

		//$data_list[$student_sn][guardian]=$student[3];

		

		//將年級收費細目加進來

		$grade=substr($res->fields[record_id],4,1);

		foreach($grade_dollars[$grade] as $key=>$value)

		{

			$data_list[$student_sn][detail][$key][original]=$value;

			$data_list[$student_sn][detail][$key][percent]=$decrease_dollars[$student_sn][$key][percent];

			$data_list[$student_sn][detail][$key][decrease_dollars]=$decrease_dollars[$student_sn][$key][dollars];

			$data_list[$student_sn][detail][$key][cause]=$decrease_dollars[$student_sn][$key][cause];

			//計算應繳總額

			$data_list[$student_sn][total]+=$value-$data_list[$student_sn][detail][$key][decrease_dollars];

		}		
		$res->MoveNext();
	}

	return $data_list;

}

function get_item_all_stud_list_multi($item_id_list) {
	global $CONN;
	
	//將ID轉為陣列
	$item_id_list_array=split(',',$item_id_list);
	$data_list=array();
	
	foreach($item_id_list_array as $key=>$item_id)
	{
		//取得各年級收費列表及計算減免金額
		$grade_dollars=get_grade_charge($item_id);
		$decrease_dollars=get_charge_decrease($item_id);

		//取得所有收費學生名單
		$sql="select a.*,b.curr_class_num,b.stud_name,b.stud_id,b.stud_birthday,year(b.stud_birthday) as birth_year,month(b.stud_birthday) as birth_month,dayofmonth(b.stud_birthday) as birth_day,b.stud_person_id from charge_record a left join stud_base b on a.student_sn=b.student_sn where b.stud_study_cond=0 AND item_id=$item_id ORDER BY record_id";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);;

		while(!$res->EOF){
		
			$student_sn=$res->fields[student_sn];
			$key_sn=$res->fields[curr_class_num].'_'.$res->fields[student_sn];

			$data_list[$key_sn][record_id]=$res->fields[record_id];
			$data_list[$key_sn][stud_id]=$res->fields[stud_id];
			$data_list[$key_sn][stud_birthday]=$res->fields[stud_birthday];
			
			$data_list[$key_sn][birth_year]=$res->fields[birth_year]-1911;
			$data_list[$key_sn][birth_month]=$res->fields[birth_month];
			$data_list[$key_sn][birth_day]=$res->fields[birth_day];
			
			
			$data_list[$key_sn][stud_person_id]=$res->fields[stud_person_id];
			
			$data_list[$key_sn][stud_name]=$res->fields[stud_name];

			//$data_list[$student_sn][guardian]=$student[3];

			

			//將年級收費細目加進來

			$grade=substr($res->fields[record_id],4,1);

			foreach($grade_dollars[$grade] as $key=>$value)

			{
				$real_key_array=split('-',$key);
				$real_key=$real_key_array[0];
				$real_detail=$real_key_array[1];
				
				$data_list[$key_sn][detail][$real_key][item]=$real_detail;
				$data_list[$key_sn][detail][$real_key][original]=$value;
				$data_list[$key_sn][detail][$real_key][percent]=$decrease_dollars[$student_sn][$key][percent];
				$data_list[$key_sn][detail][$real_key][decrease_dollars]=$decrease_dollars[$student_sn][$key][dollars];
				$data_list[$key_sn][detail][$real_key][cause]=$decrease_dollars[$student_sn][$key][cause];
				$data_list[$key_sn][detail][$real_key][need_to_pay]=$value-$decrease_dollars[$student_sn][$key][dollars];
				//計算應繳總額
				$data_list[$key_sn][total]+=$value-$data_list[$student_sn][detail][$real_key][decrease_dollars];

			}
			$res->MoveNext();
		}
	}
	return $data_list;

}

?>