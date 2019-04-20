<?php
// $Id: get_data_function.php 8105 2014-09-02 05:44:58Z hami $
//取得全校班級名稱，陣列["091_2_01_02"]
function get_global_class_name($sel_year="",$sel_seme="",$mode="長") {
    global $CONN, $school_kind_name ,$c_name;
    
    if(empty($sel_year))$sel_year = curr_year(); //目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期    
    
    $sql_select = "select class_id,c_year,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' $and_Cyear order by c_year,c_sort ";
	$recordSet=$CONN->Execute($sql_select)  or user_error("錯誤訊息：",$sql_select,256);
	while(list($class_id,$c_year,$c_name) = $recordSet->FetchRow()){
	    if ($mode == "長") 
	       $global_class_name[$class_id]= $school_kind_name[$c_year].$c_name."班" ;
	    else 
	       $global_class_name[$class_id]= substr($school_kind_name[$c_year],0,2) . $c_name;
	}    
    return $global_class_name ;
}    


//找出某個老師的任教班級
function get_teacher_class($teacher_sn){
	global $CONN;
	$sql_select = "SELECT  t.teach_id , t.name ,t.teacher_sn  , p.class_num
	   FROM   teacher_base t ,teacher_post p   
       WHERE p.teach_id =t.teach_id and t.teach_condition =0 and t.teacher_sn='$teacher_sn' ";
       
	$recordSet=$CONN->Execute($sql_select) or user_error("錯誤訊息：",$sql_select,256);

	if ($recordSet) {

		list($teach_id, $tname , $teacher_sn , $class_num)= $recordSet->FetchRow();

    	$global_class_name = get_global_class_name();
    
		$man[name]=$tname;
		$man[sn]=$teacher_sn;
		$man[id]=$teach_id;
		$class_id = class_id_2_new($class_num);
		$man[classid]=$class_id;
		$man[classname] = $global_class_name[$class_id] ; 
	} else {
		$man=array();
	}

	return $man;

}

function class_id_2_new($class_id){
    if ($class_id) {
    $sel_year = curr_year(); //目前學年
    $sel_seme = curr_seme(); //目前學期    
    $cy = substr($class_id,0,1) ;
    $cc = substr($class_id,-2) ;
    $tmpst = sprintf("%03d_%d_%02d_%02d" , $sel_year ,$sel_seme ,$cy ,$cc ) ;
    //echo $tmpst ;
    }
    return $tmpstr ;
}

?>
