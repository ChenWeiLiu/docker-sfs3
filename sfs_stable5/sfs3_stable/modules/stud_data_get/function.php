<?php

function Get_teach_name($class_id) {
	global $CONN ;
	//取得班級的級任名，<50 代表取得級任，非實習老師 
	$sql =" select name  from teacher_base b ,teacher_post p 
	          where b.teacher_sn  = p.teacher_sn  and b.teach_condition =0 
	          and class_num ='$class_id' and p.teach_title_id < 50  ";
	$result =  $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;

	$row = $result->FetchRow() ;
	$name = $row["name"];	
	return $name ;

}	 


function Get_stud_name($name) {
    //姓名....
    global $CONN ;


    $sql="select stud_id , stud_name  from  stud_base  
           where  (stud_name = '$name' or stud_id = '$name' or curr_class_num = '$name')  and stud_study_cond = 0   ";

    $rs = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    while(!$rs->EOF){   
       $data[] = $rs->fields["stud_id"];	
       $rs->MoveNext();
       $m++ ;
    }	
    return $data ;           
}           


function Get_parent_name2($name) {
     //姓名....
     global $CONN ;


    $sql=" select d.stud_id , b.stud_name  from  stud_base b , stud_domicile d
           where d.stud_id= b.stud_id and  (d.fath_name = '$name' or d.guardian_name = '$name' or d.moth_name = '$name') and b.stud_study_cond in (0,15)   ";

    $rs = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    while(!$rs->EOF){   
       $data[] = $rs->fields["stud_id"];	
       $rs->MoveNext();
       $m++ ;
    }	
    return $data ;           
}           



function Get_stud_data($stud_id) {
     //由班級+座號及 $get_arr 陣列中取得：姓名....
     global $CONN ,$class_name_arr  ;



    $sql="select * from  stud_base  
           where  stud_id  = '$stud_id'   and stud_study_cond = 0   ";

    //座號、姓名、生日、地址、電話、家長、家長工作、工作電話

    $result = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    
    
    $row = $result->FetchRow() ;
        $have_stud_data_fg = TRUE ;
	$s_addres = $row["stud_addr_1"]  ;
	$s_home_phone = $row["stud_tel_1"]  ;	//家中電話
	$stud_tel_2 =$row["stud_tel_2"]  ;	//緊急電話
	$s_cell_phone =$row["stud_tel_3"]  ;	//工作地電話
	$stud_id = $row["stud_id"];		//學號
	$stud_name = $row["stud_name"];		//姓名
	$stud_person_id = $row["stud_person_id"]; //身份証
	$stud_sex = $row["stud_sex"];		//性別
	
	$stud_sex = ($stud_sex ==1 ) ? "男" : "女" ;
	
	$class_num_curr = $row["curr_class_num"];		//目前班級、座號
 
        $classid = intval(substr($class_num_curr,0,3));	//取得班級	
        $stud_class_id = intval(substr($class_num_curr,-2));	//座號	

	
	$s_birthday = $row["stud_birthday"]  ;
	/*
	//轉換民國日期
	if ( substr($s_birthday,0,4)>1911) 
	  $s_birthday = (substr($s_birthday,0,4) - 1911). substr($s_birthday,4) ;
	else 
	  $s_birthday = " " ; 
	*/
    unset( $dd );	
    if ($have_stud_data_fg) {	
        $sql="select a.* from stud_domicile a,stud_base b where a.student_sn=b.student_sn and b.stud_study_cond = 0  and a.stud_id = '$stud_id'";
        $result = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    
        $row = $result->FetchRow() ;           

	//家長

         $d_guardian_name = $row["guardian_name"] ;	  
         $fath_name =$row["fath_name"] ;	
         $fath_hand_phone =$row["fath_hand_phone"] ;  
         $moth_name =$row["moth_name"] ;
         


    	
        //可匯出欄位選項
        //$STUD_FIELD = array(姓名，"學號","生日","身份証字號","班級","座號","性別","電話","地址","監護人","父親","母親","級任") ;	
        $dd[] = $stud_name ;
        $dd[] = $stud_id ;
        $dd[] = $s_birthday ;
        $dd[] = $stud_person_id ;
        $dd[] = $class_name_arr[$classid] ;
        $dd[] = $stud_class_id ;
        $dd[] = $stud_sex ;
        $dd[] = "=T(\"$s_home_phone\")" ;
        $dd[] = "=T(\"$stud_tel_2\")" ;
        $dd[] = $s_addres ;
        $dd[] = $d_guardian_name ;
        $dd[] = $fath_name ;
        $dd[] = "=T(\"$fath_hand_phone\")";
        $dd[] = $moth_name ;
        //$dd[] = $teacher_name ;

    
        //一併匯出
        $data_str  = @implode("</td><td>" , $dd) ;
      

    }
    $data_str  ="<tr><td>$data_str</td></tr>" ;
    return $data_str  ;
      	
}	


?>