<?php
// $Id: my_fun.php 5310 2009-01-10 07:57:56Z hami $
include "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";


//取得模組設定
$m_arr = &get_module_setup("score_input");
extract($m_arr, EXTR_OVERWRITE);




//本校目前該年級該班級該科目目前已有階段成績的選單
function now_stage($id,$col_name,$teacher_id,$sel_year,$sel_seme,$class_id,$ss_id){
    global $CONN,$yorn;
    if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
	if (!$sel_year) user_error("沒有傳入學年！請檢查！",256);
	if (!$sel_seme) user_error("沒有傳入學期！請檢查！",256);
	//if (!$class_id) user_error("沒有傳入班級！請檢查！",256);
	if (!$ss_id) user_error("沒有傳入科目！請檢查！",256);
	if($class_id) {
		$class=class_id_2_old($class_id);
		$class_year=$class[3];
	}else{
		$class_year=ss_id_to_class_year($ss_id);
	}
	


    $times_qry="select performance_test_times from score_setup where class_year='$class_year' and year=$sel_year and semester='$sel_seme' and enable='1'";
    $times_rs=&$CONN->Execute($times_qry) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $performance_test_times=$times_rs->fields["performance_test_times"];

    $score_semester="score_semester_".$sel_year."_".$sel_seme;
    $sql="select test_sort from $score_semester where class_id='$class_id' and ss_id='$ss_id' and sendmit='0' order by score_id";
    $rs=&$CONN->Execute($sql);
    $i=0;
    if(is_object($rs)){
        while (!$rs->EOF) {
            $test_sort=$rs->fields["test_sort"];
            $i++;
            $rs->MoveNext();
        }
    }
    if(($test_sort=="")||($test_sort>=$performance_test_times)){ $test_sort=0; }
    $now=$test_sort+1;
    $option="<option value=''>選擇階段</option>\n";
    for($i=1;$i<=$performance_test_times;$i++){
        $selected=($id==$i)?"selected":"";
        if($id==""){
            $selected=($i==$now)?"selected":"";
        }
        $option.="<option value='$i' $selected>第".$i."階段</option>\n";
    }
	if($yorn=='n'){
		$sd=($id==254)?"selected":"";
		$option.="<option value='254' $sd>平時成績</option>\n";
	}
    return $option;
}

//本校目前該年級該班級該科目目前已有階段成績的選單
function Nnow_stage($id,$col_name,$teacher_id,$sel_year,$sel_seme,$class_id,$ss_id){
    global $CONN;
    if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
   
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
	if (!$sel_year) user_error("沒有傳入學年！請檢查！",256);
	if (!$sel_seme) user_error("沒有傳入學期！請檢查！",256);
	//if (!$class_id) user_error("沒有傳入班級！請檢查！",256);
	if (!$ss_id) user_error("沒有傳入科目！請檢查！",256);
    if($class_id) {
		$class=class_id_2_old($class_id);
		$class_year=$class[3];
	}else{
		$class_year=ss_id_to_class_year($ss_id);
	}

    $times_qry="select performance_test_times from score_setup where  class_year=$class_year and year=$sel_year and semester='$sel_seme' and enable='1'";
    $times_rs=&$CONN->Execute($times_qry) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $performance_test_times=$times_rs->fields["performance_test_times"];
    $score_semester="score_semester_".$sel_year."_".$sel_seme;
    $sql="select test_sort from $score_semester where class_id='$class_id' and ss_id='$ss_id' and sendmit='0' order by score_id";
    $rs=&$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $i=0;
    if(is_object($rs)){
        while (!$rs->EOF) {
            $test_sort_a[$i]=$rs->fields["test_sort"];
            $i++;
            $rs->MoveNext();
        }
    }
    $t_max=max($test_sort_a);
    if(($t_max=="")||($t_max>=$performance_test_times)){ $t_max=0; }
    $now=$t_max+1;
    return $now;
}


//本校目前學年與學期下拉式選單
function select_year_seme($id,$col_name){
    global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
    if (!$id) user_error("沒有傳入表單名稱！請檢查！",256);
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
    $sql="select * from school_class";
    $rs=$CONN->Execute($sql);

    $option="<option value=''>選擇學年度</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $year[$i]=$rs->fields["year"];
        $semester[$i]=$rs->fields['semester'];
        $year_semester[$i]=$year[$i]."_".$semester[$i];
        $i++;
        $rs->MoveNext();
    }
    $year_semester=deldup($year_semester);
    for($i=0;$i<count($year_semester);$i++){
        $selected=($id==$year_semester[$i])?"selected":"";
        $YS=explode("_",$year_semester[$i]);
        $option.="<option value='$year_semester[$i]' $selected>".$YS[0]."學年度第".$YS[1]."學期</option>\n";
    }
    $select_school_class="<select name='$col_name'>$option</select>";
	//return $select_school_class;
    return $option;
}

//本校目前年級下拉式選單
function &select_school_class($id,$col_name,$sel_year,$sel_seme){
    global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
	if (!$sel_year) user_error("沒有傳入學年！請檢查！",256);
	if (!$sel_seme) user_error("沒有傳入學期！請檢查！",256);
    $sql="select * from school_class where year=$sel_year and semester=$sel_seme";
    $rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
    $option="<option value=''>選擇年級</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $c_year[$i]=$rs->fields["c_year"];
        $i++;
        $rs->MoveNext();
    }
    $c_year=deldup($c_year);
    for($i=0;$i<count($c_year);$i++){
        $selected=($id==$c_year[$i])?"selected":"";
        $option.="<option value='$c_year[$i]' $selected>".$school_kind_name[$c_year[$i]]."級</option>\n";
    }
    $select_school_class="<select name='$col_name'>$option</select>";
	//return $select_school_class;
    return $option;
}

//本校目前該年級的所有班級下拉式選單
function &select_school_class_name($c_year,$id,$col_name,$sel_year,$sel_seme){
    global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    
    
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
	if (!$sel_year) user_error("沒有傳入學年！請檢查！",256);
	if (!$sel_seme) user_error("沒有傳入學期！請檢查！",256);
	if (!$c_year) user_error("沒有傳入年級！請檢查！",256);
	
    if(empty($c_year)) $c_year=1;
    $sql="select * from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year";
    $rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $option="<option value=''>選擇班級</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $c_name[$i]=$rs->fields["c_name"];
        $c_sort[$i]=$rs->fields["c_sort"];
        $i++;
        $rs->MoveNext();
    }
    $c_name=deldup($c_name);
    $c_sort=deldup($c_sort);
    for($i=0;$i<count($c_name);$i++){
        $selected=($id==$c_sort[$i])?"selected":"";
        $option.="<option value='$c_sort[$i]' $selected>".$c_name[$i]."班</option>\n";
    }
    $select_school_class_name="<select name='$col_name'>$option</select>";
	//return $select_school_class_name;
    return $option;
}

//本校目前該年級該班級目前已有階段成績的選單
function &select_stage($c_year,$c_name,$id,$col_name,$sel_year,$sel_seme){
    global $CONN,$score_semester;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
	if (!$sel_year) user_error("沒有傳入學年！請檢查！",256);
	if (!$sel_seme) user_error("沒有傳入學期！請檢查！",256);
	if (!$c_year) user_error("沒有傳入年級！請檢查！",256);
	if (!$c_name) user_error("沒有傳入班級！請檢查！",256);	

    $sql="select class_id from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year and c_sort=$c_name";
    $rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $class_id=$rs->fields["class_id"];
    $sql="select * from $score_semester where class_id='$class_id' order by test_sort";
    $rs=&$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $i=0;
    if(is_object($rs)){
        while (!$rs->EOF) {
            $test_sort[$i]=$rs->fields["test_sort"];
            $i++;
            $rs->MoveNext();
        }
        $test_sort=deldup($test_sort);
        $option="<option value=''>選擇階段成績</option>\n";
        for($i=0;$i<=count($test_sort);$i++){
            $selected=($id==$test_sort[$i])?"selected":"";
            //$selectedd=($id=="255")?"selected":"";
            //$test_sort_name[$i]=$test_sort[$i];
            if($test_sort[$i]==255) $test_sort_name[$i]="全學期";
            else $test_sort_name[$i]="第".$test_sort[$i]."階段";
            if($i<count($test_sort)) $option.="<option value='$test_sort[$i]' $selected>".$test_sort_name[$i]."</option>\n";
            //if($i==count($test_sort)){
                //if(count($test_sort)!=0){
                    //$option.="<option value='255' $selectedd>全學期</option>";
                //}
            //}
        }
        if (!in_array("255", $test_sort)) $option.="<option value='255' $selected>全學期</option>\n";
    }
    else{
        $option="<option $selectedd>尚無資料</option>";
    }
    return $option;
}

//本校目前該年級該班級目前已有階段成績的選單
function &select_stage1($c_year,$c_name,$id,$col_name,$sel_year,$sel_seme){
    global $CONN,$score_semester;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);    
    
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
	if (!$sel_year) user_error("沒有傳入學年！請檢查！",256);
	if (!$sel_seme) user_error("沒有傳入學期！請檢查！",256);
	if (!$c_year) user_error("沒有傳入年級！請檢查！",256);
	if (!$c_name) user_error("沒有傳入班級！請檢查！",256);	
    $sql="select class_id from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year and c_sort=$c_name";
    $rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $class_id=$rs->fields["class_id"];
    $sql="select * from $score_semester where class_id='$class_id'";
    $rs=&$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $i=0;
    while (!$rs->EOF) {
        $test_sort[$i]=$rs->fields["test_sort"];
        $i++;
        $rs->MoveNext();
    }
    $test_sort=deldup($test_sort);
    $option="<option value=''>選擇階段</option>\n";
    for($i=0;$i<count($test_sort);$i++){
        $selected=($id==$test_sort[$i])?"selected":"";
        $option.="<option value='$test_sort[$i]' $selected>第".$test_sort[$i]."階段</option>\n";
    }

    return $option;
}

//登入老師目前任課的班級與科目的選單
function &select_teacher_ss($id,$col_name,$teacher_id,$sel_year,$sel_seme){
    global $CONN,$is_allow;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
	if (!$sel_year) user_error("沒有傳入學年！請檢查！",256);
	if (!$sel_seme) user_error("沒有傳入學期！請檢查！",256);
	if (!$teacher_id) user_error("沒有傳入老師代碼！請檢查！",256);
    //echo $id." ".$col_name." ".$teacher_id." ".$sel_year." ".$sel_seme;
/***************************************************************************************/
	$teacher_sn = $_SESSION[session_tea_sn];

	//取得任教班級代號
	$class_num = get_teach_class();
	$class_year=substr($class_num,0,-2);
	$class_name=intval(substr($class_num,-2));
/***************************************************************************************/
    if($class_num && $is_allow=='y'){//是導師
        $sql="select * from score_course where year=$sel_year and semester=$sel_seme and (teacher_sn='$teacher_sn' or (class_year='$class_year' and class_name='$class_name' and allow='0')) and day<>'' and sector<>0";
        $rs=$CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
        $option="<option value='0'>選擇班級科目</option>\n";
        $i=0;
        while (!$rs->EOF) {
            $course_id[$i] = $rs->fields["course_id"];
            $class_year = $rs->fields["class_year"];
            $class_name = $rs->fields["class_name"];
            $class_id[$i] = $rs->fields["class_id"];
            $ss_id[$i] = $rs->fields["ss_id"];
            $teacher_sn = $rs->fields["teacher_sn"];
            //將subject_id 轉成 subject_name
            $sql1="select need_exam from score_ss where ss_id=$ss_id[$i]";
            $rs1=$CONN->Execute($sql1);
            $need_exam = $rs1->fields["need_exam"];            
            if($need_exam==0) $i--;
            $i++;
            $rs->MoveNext();
        }

    }
    else{//科任老師
        $sql="select * from score_course where year=$sel_year and semester=$sel_seme and teacher_sn='$teacher_sn' order by class_id,ss_id";
        $rs=$CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
        $option="<option value='0'>選擇班級科目</option>\n";
        $i=0;
        while (!$rs->EOF) {
            $course_id[$i] = $rs->fields["course_id"];
            $class_year = $rs->fields["class_year"];
            $class_name = $rs->fields["class_name"];
            $class_id[$i] = $rs->fields["class_id"];
            $ss_id[$i] = $rs->fields["ss_id"];
            $teacher_sn = $rs->fields["teacher_sn"];

    /***************************************************************************************/
    //  將subject_id 轉成 subject_name
            $sql1="select need_exam from score_ss where ss_id=$ss_id[$i]";
            $rs1=$CONN->Execute($sql1);
            $need_exam = $rs1->fields["need_exam"];
            if($need_exam==0) $i--;
            $i++;
            $rs->MoveNext();
        }
    }



	if($i==0) trigger_error("對不起！找不到您所任教的科目，請確認課表中有排上您任教的科目！",E_USER_ERROR);

    for($k=0;$k<$i;$k++){
        $sql="select course_id from score_course where class_id='$class_id[$k]' and ss_id='$ss_id[$k]'";
        $rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
        $course_id[$k] = $rs->fields["course_id"];
        $teacher_course[$k]=course_id_to_full_class_name($course_id[$k]);
        $teacher_course[$k].=ss_id_to_subject_name($ss_id[$k]);
    }
    $course_id=deldup($course_id);
	//print_r ($course_id);
    $teacher_course=deldup($teacher_course);
	//print_r ($teacher_course);


	//2003-12-25新增，還要加上分組課程的選單
	$sql_sub="select * from elective_tea where teacher_sn='$teacher_sn' ";
	//echo $sql_sub;
	$rs_sub=$CONN->Execute($sql_sub) or creat_elective();
	$sub=0;
	if($rs_sub){
		while(!$rs_sub->EOF){
			//一樣要找出科目名
			$group_id=$rs_sub->fields['group_id'];
			$group_name=$rs_sub->fields['group_name'];
			$ss_id=$rs_sub->fields['ss_id'];

			//這個科目需要考試嗎？
			$query = "select need_exam from score_ss where ss_id='$ss_id' ";
			$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			$need_exam = $res->fields['need_exam'];
			if($need_exam){
				$class_subj=ss_id_to_class_subject_name($ss_id);
				$class_subj_group=$class_subj."-".$group_name;
				$course_id[]=$group_id."g".$ss_id;
				$teacher_course[] = $class_subj_group;
				//echo $class_subj_group."---".$gs_id."<br>";
				$sub++;
			}
			$rs_sub->MoveNext();
		}
	}
    //$aa=$course_id.$teacher_course;
    $bgcolor=array("#E3DBFF","#E2D9FD","#DBD3F6","#D5CDEF","#CDC6E6","#C4BDDC","#C5BEDD","#BCB5D3","#B4ADCA","#ABA5CD");
    for($j=0;$j<count($teacher_course);$j++){
        $rs_tea=$CONN->Execute("select teacher_sn from score_course where course_id='$course_id[$j]'");
        $teacher_sn[$j]=$rs_tea->fields['teacher_sn'];
        $color[$j]=($teacher_sn[$j]==$_SESSION['session_tea_sn'])?"#000000":"#F71CFF";
        $selected=($id==$course_id[$j])?"selected":"";
        $c=$j % 10;
        $option.="<option value='$course_id[$j]'  style='background-color: $bgcolor[$c]; color:$color[$c]' $selected >".trim($teacher_course[$j])."</option>\n";
    }

    $select_teacher_ss="<select name='$col_name'>$option</select>";
	$select_teacher_ss_1="$option";
	return $select_teacher_ss_1;
}

//找出該位老師任教科目的選單
function select_teacher_course_id($id,$col_name,$teacher_id,$sel_year,$sel_seme){
    global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	if (!$col_name) user_error("沒有表單名稱！請檢查！",256);
	if (!$sel_year) user_error("沒有傳入學年！請檢查！",256);
	if (!$sel_seme) user_error("沒有傳入學期！請檢查！",256);
	if (!$teacher_id) user_error("沒有傳入老師代碼！請檢查！",256);
/***************************************************************************************/
//  將teacher_id 轉成 teacher_sn
    $sql="select teacher_sn from teacher_base where teach_id=$teacher_id";
    $rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $teacher_sn = $rs->fields["teacher_sn"];
/***************************************************************************************/

    $sql="select * from score_course where year=$sel_year and semester=$sel_seme and teacher_sn=$teacher_sn";
    $rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
	$option="<option value='0'>選擇班級科目</option>\n";
        $i=0;
	while (!$rs->EOF) {
		$course_id[$i] = $rs->fields["course_id"];
        $class_year = $rs->fields["class_year"];
        $class_name = $rs->fields["class_name"];
        $class_id[$i] = $rs->fields["class_id"];
        $ss_id[$i] = $rs->fields["ss_id"];
        $teacher_sn = $rs->fields["teacher_sn"];

/***************************************************************************************/
//  將subject_id 轉成 subject_name
        $sql1="select need_exam from score_ss where ss_id=$ss_id[$i]";
        $rs1=$CONN->Execute($sql1);
        $need_exam = $rs1->fields["need_exam"];
        if($need_exam==0) $i--;
        $i++;
        $rs->MoveNext();
    }
    for($k=0;$k<$i;$k++){
        $sql="select course_id from score_course where class_id='$class_id[$k]' and ss_id='$ss_id[$k]'";
        $rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤", E_USER_ERROR);
        $course_id[$k] = $rs->fields["course_id"];
        //echo $course_id[$k];
        $teacher_course[$k]=course_id_to_full_class_name($course_id[$k]);
        $teacher_course[$k].=ss_id_to_subject_name($ss_id[$k]);
    }
    $course_id=deldup($course_id);
	return $course_id;
}


//一個比較兩個陣列，然後去除重複的值的函數
function  deldup($a){
    if (!$a) user_error("沒有傳入陣列！請檢查！",256);

	//初始化陣列 
	$d=array();
        $i=count($a);
        for  ($j=0;$j<=$i;$j++){
                      for  ($k=0;$k<$j;$k++){
                                    if($a[$k]==$a[$j]){
                                            $a[$j]="";
                                    }
                      }
        }
        $q=0;
        for($r=0;$r<=$i;$r++){
                      if($a[$r]!=""){
                                      $d[$q]=$a[$r];
                                      $q++;
                      }
          }

return  $d;
}

//一個比較兩個陣列，然後去除重複的值的函數
function  delarray($a,$b){
    if (!$a) user_error("沒有傳入陣列！請檢查！",256);
	if (!$b) user_error("沒有傳入陣列！請檢查！",256);	
	//初始化陣列 
	$d=array();
                for($i=0;$i<count($a);$i++){
                            for($j=0;$j<count($b);$j++){
                                          if  ($a[$i]==$b[$j])  $a[$i]="";
                            }
                  }
                            $q=0;
                            for($r=0;$r<=$i;$r++){
                                                if($a[$r]!=""){
                                                                  $d[$q]=$a[$r];
                                                                  $q++;
                                                }
                              }
                      return  $d;
}


//算出這個值是陣列中第幾大的，a是一個數，b是一個陣列
function  how_big($a,$b){
    //if (!$a) user_error("沒有傳入陣列！請檢查！",256);
	if (!$b) user_error("沒有傳入陣列！請檢查！",256);
	$sort=1;
    for($i=0;$i<count($b);$i++){
        if($a<$b[$i]) $sort++;
    }
    return  $sort;
}


//由subject_id找出科目名稱的函數
function  subject_id_to_subject_name($subject_id){
    global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if (!$subject_id) user_error("沒有傳入科目代碼！請檢查！",256);
    $sql1="select subject_name from score_subject where subject_id=$subject_id and enable=1";
    $rs1=$CONN->Execute($sql1) or trigger_error("SQL語法錯誤", E_USER_ERROR);
    $subject_name = $rs1->fields["subject_name"];
    return $subject_name;
}
/*
//由ss_id找出科目名稱的函數
function  ss_id_to_subject_name($ss_id){
    global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
    $sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2) or trigger_error("SQL語法錯誤", E_USER_ERROR);
        $subject_name = $rs2->fields["subject_name"];
    }
    else{
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $subject_name = $rs4->fields["subject_name"];
    }
    return $subject_name;
}
*/



//由ss_id找出領域名稱的函數
function  ss_id_to_scope_name($ss_id){
    global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if (!$ss_id) user_error("沒有傳入科目代碼！請檢查！",256);
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3) or trigger_error("SQL語法錯誤", E_USER_ERROR);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4) or trigger_error("SQL語法錯誤", E_USER_ERROR);
        $scope_name = $rs4->fields["subject_name"];

    return $scope_name;
}
//是否每一次月考要配合一次平時成績
function  findyorn(){
	global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	$rs_yorn=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='score_input' AND pm_item='yorn'") or trigger_error("SQL語法錯誤", E_USER_ERROR);
	$yorn=$rs_yorn->fields['pm_value'];
	return $yorn;
}

//列出陣列內容

function list_array($arr){
echo "count: ".count($arr)."<BR>";
reset($arr);
while(list($id,$val)= each($arr))
	echo "ID: ".$id."-- VAL: ".$val."<BR>";

}

?>
