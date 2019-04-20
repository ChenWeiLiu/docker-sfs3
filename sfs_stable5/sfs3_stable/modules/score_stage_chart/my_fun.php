<?php
// $Id: my_fun.php 7326 2013-07-04 07:04:39Z infodaes $ 

//本校目前該年級該班級目前已有階段成績的選單
function select_stage2($year_seme,$year_name){
        global $CONN,$score_semester,$yorn;
        $sel_year = substr($year_seme,0,3);
        $sel_seme = substr($year_seme,-1);
        $c_year = substr($year_name,0,-2);
        $c_name = substr($year_name,-2);
        $score_semester="score_semester_".intval($sel_year)."_".$sel_seme;
        $class_id = sprintf("%03s_%s_%02s_%02s",$sel_year,$sel_seme,$c_year,$c_name);
        if ($yorn=='n')
                $sql="select DISTINCT test_sort from $score_semester where class_id='$class_id'  order by test_sort";
        else
                $sql="select DISTINCT test_sort from $score_semester where class_id='$class_id' and test_sort<>254  order by test_sort";

        $rs=&$CONN->Execute($sql);
        if($rs){
        	while (!$rs->EOF) {
                	if($rs->fields[0]==255)
                        	$temp_name="不分階段";
                	elseif($rs->fields[0]==254)
                        	$temp_name="平時成績";
                	else
                        	$temp_name="第".$rs->fields[0]."階段";
                	$test_sort[$rs->fields[0]]= $temp_name;
                	$rs->MoveNext();
        	}
        }	
        return $test_sort;
}

//是否每一次月考要配合一次平時成績 ,y (是)
function findyorn(){
        global $CONN;
        $rs_yorn=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='score_input' AND pm_item='yorn'");
        $yorn=$rs_yorn->fields['pm_value'];
        return $yorn;
}

//取得某一學生的各種獎懲累積次數
function getOneM_good_bad_data($stud_id,$sel_year,$sel_seme,$start_date,$end_date){
        global $CONN,$REWARD_KIND,$reward_arr;

        //$sql_select = "select gb_kind from stud_good_bad where stud_id='$stud_id' and gb_year = '$sel_year' and gb_seme = '$sel_seme' and gb_add_date >= '$start_date' and gb_add_date <= '$end_date'";
	$sql_select = "select reward_kind from reward where stud_id='$stud_id' and reward_year_seme = '$sel_year"."$sel_seme' and reward_sub='1' and reward_date >= '$start_date' and reward_date <= '$end_date'";
	$rs=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	foreach($REWARD_KIND as $key=>$good_bad_kind)   $thedata[$key]=0;
	while(!$rs->EOF){
		switch ( $rs->fields[reward_kind]) {
			case 1:
				$thedata[ss_good]++;
				break;
			case 2:
				$thedata[ss_good]+=2;
				break;
			case 3:
				$thedata[s_good]++;
				break;
			case 4:
				$thedata[s_good]+=2;
				break;
			case 5:
				$thedata[big_good]++;
				break;
			case 6:
				$thedata[big_good]+=2;
				break;
			case 7:
				$thedata[big_good]+=3;
				break;
			case -1:
				$thedata[ss_bad]++;
				break;
			case -2:
				$thedata[ss_bad]+=2;
				break;
			case -3:
				$thedata[s_bad]++;
				break;
			case -4:
				$thedata[s_bad]+=2;
				break;
			case -5:
				$thedata[big_bad]++;
				break;
			case -6:
				$thedata[big_bad]+=2;
				break;
			case -7:
				$thedata[big_bad]+=3;
				break;
			default:
				break;
		}
	$rs->MoveNext();
        }
//        var_dump($thedata);
        return $thedata;
}

//算出某位學生某科的階段成績
function get_ss_score($student_sn,$sel_year,$sel_seme,$stage){
	global $CONN;
        $score_semester="score_semester_".$sel_year."_".$sel_seme;
        $sql="select ss_id,score,test_kind from $score_semester where student_sn='$student_sn' and test_sort='$stage'";
        
	$rs=$CONN->Execute($sql);
	if($rs){
        	while(!$rs->EOF){
                        $score = $rs->fields[score];
                        $ss_id = $rs->fields[ss_id];
                        $test_kind = $rs->fields[test_kind];

                        if ($score == -100) $score ='';

                        $Sscore[$ss_id][$test_kind] =$score;


                        $rs->MoveNext();
         	}
	}
	return $Sscore;
}

//取得本學期該年級定考科目陣列
function get_stage_test_subject($class_id,$stage){
	global $CONN,$sel_year,$sel_seme;
  
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	$c_year = $class[3];
        $query = "select ss_id from score_ss where class_id='$class_id'";
        $res = $CONN->Execute($query);
        if ($res->EOF)
                $temp = " and class_id = '' ";
        else
                $temp = " and class_id ='$class_id'";


 	//該年級的共通設定
        if($stage==255)
		$sql="select scope_id,subject_id,ss_id from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$c_year' and enable='1' and need_exam='1' $temp  order by sort,sub_sort";
	else
		$sql="select scope_id,subject_id,ss_id  from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$c_year' and enable='1' and need_exam='1' and print='1' $temp order by sort,sub_sort";
	
	$rs=$CONN->Execute($sql) or die($sql);
	//echo $sql;


	$temp_arr = array();
	$scope_arr = array();
	$subject_arr = array();
	$i=0;
	while(!$rs->EOF){
		$scope_ss_id=$rs->fields['ss_id'];
		$scope_id=$rs->fields['scope_id'];
		$subject_id=$rs->fields['subject_id'];
		$temp_arr[$i][ss_id] = $scope_ss_id;
		$temp_arr[$i][subject_name] = ss_id_to_subject_name($scope_ss_id);
		//$temp_arr[$i][subject_name]=$subject_name_arr[$subject_id];
		$i++;
		$rs->MoveNext();
	}

	return $temp_arr;
}

//取得所有同年級的class_id陣列
function get_all_classid($sel_year,$sel_seme,$class_id){
	global $CONN;
	$class = explode("_",$class_id);
	$c_year = $class[2];
	
	$sql_select = "select class_id from school_class where year='$sel_year' and semester = '$sel_seme' and c_year ='$c_year' and enable='1'  order by c_sort";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($class_id) = $recordSet->FetchRow()){
		$class_array[]=$class_id;
	}
	
	return $class_array;
}

//取得定考科目名單
function stage_subject($class_id,$stage,$all,$ss_id_b){

	
	$subject_str = get_stage_test_subject($class_id,$stage);

	while (list($id,$val) = each($subject_str)){
		if(($all!="no")||($ss_id_b[$val[ss_id]]=="1")) $sub_ary[] = $val[subject_name];
	}
	//var_dump($sub_ary);
	return $sub_ary;
	
}

//取得班級成績及計算總分排名
function cal_ar($student_sn,$class_id,$sel_year,$sel_seme,$stage){

        for($m=0;$m<count($student_sn);$m++){
                 $score_ary[$m]= get_score_value_stage($student_sn[$m],$class_id,$sel_year,$sel_seme,$stage);
        }
	//算出名次
        for($m=0;$m<count($student_sn);$m++){
		$sort=1;
		for($n=0;$n<count($student_sn);$n++)
			if($score_ary[$m][total]<$score_ary[$n][total]) $sort++;
                $score_ary[$m][how_big]= $sort;
        } 
       // var_dump($student_sn);exit;
	return $score_ary;
} 

?>