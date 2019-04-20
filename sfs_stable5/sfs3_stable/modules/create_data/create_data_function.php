<?php
// $Id: create_data_function.php 5310 2009-01-10 07:57:56Z hami $

function change_addr($addr,$mode=0) {
	//縣市
	$temp_str = split_str($addr,"縣",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//鄉鎮
	$temp_str = split_str($addr,"鄉",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"鎮",1);

	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"區",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//mode=2, 只取縣市鄉鎮
	if ($mode==2) return $res;

	//村里
	$temp_str = split_str($addr,"村",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"里",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//鄰
	$temp_str = split_str($addr,"鄰",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//路
	$temp_str = split_str($addr,"路",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"街",1);
	
	$res[] = $temp_str[0];
	$addr=$temp_str[1];

      	//段
	$temp_str = split_str($addr,"段",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//巷
	$temp_str = split_str($addr,"巷",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//弄
	$temp_str = split_str($addr,"弄",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//號
	$temp_str = split_str($addr,"號",$mode);
	$temp_arr = explode("-",$temp_str);
	if (sizeof($temp_arr)>1){
		$res[]=$temp_arr[0];
		$res[]=$temp_arr[1];
	}else {
		$res[]=$temp_str[0];
		$res[]="";
	}
	$addr=$temp_str[1];
	
	//樓
	$temp_str = split_str($addr,"樓",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//樓之
	if ($addr != "") {
		if ($mode)
			$temp_str = $addr;
		else
			$temp_str = substr(chop($addr),2);
	} else
		$temp_str ="";
		
	$res[]=$temp_str ;
      	return $res;
}

function split_str($addr,$str,$last=0) {
	$temp = explode ($str, $addr);
	if (count($temp)<2 ){
		$t[0]="";
		$t[1]=$addr;
	}else{
		$t[0]=(!empty($last))?$temp[0].$str:$temp[0];
		$t[1]=$temp[1];
	}
	return $t;
}

function check_student_data($base_arr=array()) {
	global $CONN,$ZIP_ARR,$IS_JHORES;

	//檢查學號是否存在
	if($base_arr['stud_id']=="") {
		return $temp_arr['msg']="姓名：".$base_arr['stud_name']." 的學號空白";
	}

	//檢查學號是否重複
	$query="select stud_name from stud_base where stud_id='".$base_arr['stud_id']."'";
	$res=$CONN->Execute($query) or trigger_error($query,256);
	if($res->fields['stud_name']!="")  {
		return $temp_arr['msg']="您所要匯入的學生資料中學號：".$base_arr['stud_id']." 與 ".$res->fields['stud_name']." 重複"; 
	}

	//檢查姓名
	if($base_arr['stud_name']=="") {
		return $temp_arr['msg']="學號：".$base_arr['stud_id']." 的學生沒有姓名";
	}

	//檢查性別				
	$sex_arr=array("0"=>"女","1"=>"男");
	if(!in_array($base_arr['stud_sex'],array_keys($sex_arr))) {
		return $temp_arr['msg']="學號：".$base_arr['stud_id']."  姓名：".$base_arr['stud_name']." 的性別錯誤"; 
	}
	$base_arr['stud_sex']=2-$base_arr['stud_sex'];

	//檢查生日
	$stud_birthday_arr=split("[/.-]",$base_arr['stud_birthday']);
	if($stud_birthday_arr[0]<1900 || $stud_birthday_arr[0]>2030 || $stud_birthday_arr[1]<1 || $stud_birthday_arr[1]>12 || $stud_birthday_arr[2]<1 || $stud_birthday_arr[2]>31) {
		return $temp_arr['msg']="學號：".$base_arr['stud_id']."  姓名：".$base_arr['stud_name']." 的生日（ ".$base_arr['stud_birthday']." ）填寫錯誤";
	} else {
		$base_arr['stud_birthday']=$stud_birthday_arr[0]."-".$stud_birthday_arr[1]."-".$stud_birthday_arr[2];
	}

	//檢查身份證
	if($base_arr['stud_person_id']=="") {
		return $temp_arr['msg']="學號：".$base_arr['stud_id']."  姓名：".$base_arr['stud_name']." 的身份證空白";
	}

	//檢查身份證是否重複
	$query="select stud_id,stud_name from stud_base where stud_person_id='".$base_arr['stud_person_id']."'";
	$res=$CONN->Execute($query) or trigger_error($query,256);
	if($res->fields['stud_id']!="")  {
		return $temp_arr['msg']="學號：".$base_arr['stud_id']."  姓名：".$base_arr['stud_name']." 的身份證字號：".$base_arr['stud_person_id']." 與學號：".$res->fields['stud_id']."  姓名：".$res->fields['stud_name']."重複"; 
	}

	//拆解地址
	$addr_arr=change_addr($base_arr['stud_addr_1']);
	if ($addr_arr[0]=="") {
		$addr_arr[0]=$base_arr['default'];
		$base_arr['stud_addr_1']=$base_arr['default'].$base_arr['stud_addr_1'];
	}
	$zip_id=0;
	if ($base_arr['stud_addr_2']) {
		$addr_arr2=change_addr($base_arr['stud_addr_2'],2);
		if ($addr_arr2[0]=="") $addr_arr2[0]=$base_arr['default'];
		if ($addr_arr2[0] && $addr_arr2[1]) {
			$zip_id=$ZIP_ARR[$addr_arr2[0].$addr_arr2[1]];
		}
	}

	//解析座號
	for($i=6;$i>1;$i--) {
		if ($base_arr['seme_year'][$i] && $base_arr['seme_class'][$i] && $base_arr['seme_num'][$i]) {
			$base_arr['curr_class_num']=intval($base_arr['seme_year'][$i]-$base_arr['stud_study_year']+1+$IS_JHORES)*10000+intval($base_arr['seme_class'][$i]*100)+intval($base_arr['seme_num'][$i]);
			break;
		}
	}

	//新增至資料庫
	$stud_kind =",0,";
	$query="insert into stud_base (stud_id,stud_name,stud_person_id,stud_birthday,stud_sex,stud_study_cond,curr_class_num,stud_study_year,stud_addr_a,stud_addr_b,stud_addr_c,stud_addr_d,stud_addr_e,stud_addr_f,stud_addr_g,stud_addr_h,stud_addr_i,stud_addr_j,stud_addr_k,stud_addr_l,stud_addr_m,stud_addr_1,stud_addr_2,stud_tel_2,stud_kind,stud_mschool_name,addr_zip) values ('".$base_arr['stud_id']."','".$base_arr['stud_name']."','".$base_arr['stud_person_id']."','".$base_arr['stud_birthday']."','".$base_arr['stud_sex']."','0','".$base_arr['curr_class_num']."','".$base_arr['stud_study_year']."','$addr_arr[0]','$addr_arr[1]','$addr_arr[2]','$addr_arr[3]','$addr_arr[4]','$addr_arr[5]','$addr_arr[6]','$addr_arr[7]','$addr_arr[8]','$addr_arr[9]','$addr_arr[10]','$addr_arr[11]','$addr_arr[12]','".$base_arr['stud_addr_1']."','".$base_arr['stud_addr_2']."','".$base_arr['stud_tel_2']."','$stud_kind','".$base_arr['stud_mschool_name']."','$zip_id')";
	$res=$CONN->Execute($query) or trigger_error($query,256);
	$student_sn=$CONN->Insert_ID();
	if ($student_sn) {
		$query="insert into stud_domicile (stud_id,guardian_name,guardian_relation,guardian_address,guardian_phone,student_sn) values('".$base_arr['stud_id']."','".$base_arr['guardian_name']."','".$base_arr['guardian_relation']."','".$base_arr['guardian_address']."','".$base_arr['guardian_phone']."','$student_sn')";
		$res=$CONN->Execute($query) or trigger_error($query,256);

		//解析座號
		for($i=1;$i<=6;$i++) {
			if ($base_arr['seme_year'][$i] && $base_arr['seme_class'][$i] && $base_arr['seme_num'][$i]) {
				$query="insert into stud_seme (student_sn,stud_id,seme_year_seme,seme_class,seme_num) values ('$student_sn','".$base_arr['stud_id']."','".sprintf("%03d",$base_arr['seme_year'][$i]).intval(($i-1)%2+1)."','".intval($base_arr['seme_year'][$i]-$base_arr['stud_study_year']+1+$IS_JHORES).$base_arr['seme_class'][$i]."','".$base_arr['seme_num'][$i]."')";
				$res=$CONN->Execute($query) or trigger_error($query,256);
			}
		}
	}
}
?>
