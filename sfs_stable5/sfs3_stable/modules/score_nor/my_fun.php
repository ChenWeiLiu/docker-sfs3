<?php

// $Id: my_fun.php 7690 2013-10-23 07:39:00Z smallduh $

//school_class的訊息，傳回某學年某學期的班級陣列，$A[某一年級][某一班次]=班名
function  school_class_info($year="",$semester=""){
    global $CONN;
    if(empty($year))$year = curr_year(); //目前學年
    if(empty($semester))$semester = curr_seme(); //目前學期
    $sql="select * from school_class where year='$year' and semester='$semester' and enable='1'";
    $rs=$CONN->Execute($sql) or die($sql);
    $i=0;
    while (!$rs->EOF) {
        $c_year[$i]=$rs->fields["c_year"];
        $c_name[$i]=$rs->fields["c_name"];
        $c_sort[$i]=$rs->fields["c_sort"];
        $A[$c_year[$i]][$c_sort[$i]]=$c_name[$i];
        $i++;
        $rs->MoveNext();
    }
    return $A;
}

function  stage_score($id,$col_name,$year="",$semester="",$year_name,$me,$scope_subject){
    global $CONN;
    if(empty($year))$year = curr_year(); //目前學年
    if(empty($semester))$semester = curr_seme(); //目前學期
    $option="<option value=''>選擇階段成績</option>\n";
    //取出成績的名稱
    $A=explode("_",$scope_subject);
    $ss_id=$A[0];
    $print=$A[1];
    if($print!=1){
        $selected=($id=="all")?"selected":"";
        $option.="<option value='all' $selected>總成績</option>";
    }
    else{
        $sql="select * from score_setup where year='$year' and semester='$semester' and class_year='$year_name' and enable=1";
        $rs=$CONN->Execute($sql);
        $performance_test_times=$rs->fields["performance_test_times"];
        $setup_id=$rs->fields["setup_id"];
        for($i=0;$i<$performance_test_times;$i++){
            $j=$i+1;
            $selected=($id==$j)?"selected":"";
            $option.="<option value='$j' $selected>第".$j."階段</option>";
        }
    }
    return $option;

}

//傳回某一學年某一學期某一個年級的所有課程
function  scope_subject($id,$col_name,$year="",$semester="",$class_year){
    global $CONN;
    if(empty($year))$year = curr_year(); //目前學年
    if(empty($semester))$semester = curr_seme(); //目前學期
    $option="<option value=''>選擇科目</option>\n";
    $sql1="select subject_id,print,ss_id,scope_id from score_ss where year='$year' and semester='$semester' and  class_year='$class_year' and enable=1 and need_exam=1";
    $rs1=$CONN->Execute($sql1) or die($sql1);
    $i=0;
    while(!$rs1->EOF){
        $subject_id[$i] = $rs1->fields["subject_id"];
        $print[$i] = $rs1->fields["print"];
        if($print[$i]=="") $print[$i]=0;
        $ss_id[$i] = $rs1->fields["ss_id"];
        $scope_id[$i] = $rs1->fields["scope_id"];
        if($subject_id[$i]=="0") $subject_id[$i] = $scope_id[$i];
        $rs2=$CONN->Execute("select subject_name from score_subject where subject_id='$subject_id[$i]'");
        $subject_name[$i] = $rs2->fields["subject_name"];
        $ss_id_print[$i]=$ss_id[$i]."_".$print[$i];
        $selected=($id==$ss_id_print[$i])?"selected":"";
        $option.="<option value='$ss_id_print[$i]' $selected>$subject_name[$i]</option>";
        $i++;
        $rs1->MoveNext();
    }
    if($i==0) trigger_error("對不起！您尚未設定課程！",E_USER_ERROR);
    return $option;
}

//找出該ss_id是哪一學年度哪一學期的課程
function  ss_id_to_year_seme($ss_id){
    global $CONN;
    $sql1="select * from score_ss where ss_id=$ss_id and enable='1'";
    $rs1=$CONN->Execute($sql1);
    $year = $rs1->fields["year"];
    $semester = $rs1->fields["semester"];
    $y_s[0]=$year;
    $y_s[1]=$semester;
    return $y_s;
}

//列出該年級所有科目
function show_ss_id($year_name,$id,$col_name,$sel_year,$sel_seme){
    global $CONN;
    $sql="select * from score_ss where year=$sel_year and semester=$sel_seme and class_year=$year_name and enable=1 and need_exam=1";
    $rs=$CONN->Execute($sql) or die($sql);
    $i=0;
    while (!$rs->EOF) {
        $ss_id[$i]=$rs->fields["ss_id"];
        $i++;
        $rs->MoveNext();
    }
    $ss_id=deldup($ss_id);
    return $ss_id;
}

//本校目前該年級該班級該科目目前已有階段成績的選單
function now_stage($id,$col_name,$teacher_id,$sel_year,$sel_seme,$class_id,$ss_id){
    global $CONN;
    
    $class=class_id_2_old($class_id);

    $times_qry="select performance_test_times from score_setup where class_year=$class[3] and year=$sel_year and semester='$sel_seme' and enable='1'";
    $times_rs=&$CONN->Execute($times_qry);
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

    return $option;
}

//本校目前該年級該班級該科目目前已有階段成績的選單
function Nnow_stage($id,$col_name,$teacher_id,$sel_year,$sel_seme,$class_id,$ss_id){
    global $CONN;
    
    $class=class_id_2_old($class_id);

    $times_qry="select performance_test_times from score_setup where  class_year=$class[3] and year=$sel_year and semester='$sel_seme' and enable='1'";
    $times_rs=&$CONN->Execute($times_qry);
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

    return $now;
}

//由student_sn得到本學期學生的班級座號姓名
function student_sn_to_classinfo($student_sn){
    global $CONN;
    $rs_sn=$CONN->Execute("select stud_id from stud_base where student_sn='$student_sn'");
    $stud_id=$rs_sn->fields["stud_id"];
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $rs_seme=$CONN->Execute("select seme_class,seme_num from stud_seme where stud_id='$stud_id' and seme_year_seme='$seme_year_seme'");
    $seme_class=$rs_seme->fields["seme_class"];
    $year= substr($seme_class,0,-2);
    $class= substr($seme_class,-2);
    $site=$rs_seme->fields["seme_num"];
    //echo $year.$class.$site;
    $rs1=&$CONN->Execute("select  stud_name,stud_sex,curr_class_num  from  stud_base where student_sn='$student_sn'");
    $curr_class_num=$rs1->fields['curr_class_num'];
    $stud_sex=$rs1->fields['stud_sex'];
    $stud_name=$rs1->fields['stud_name'];
    //$site= substr($curr_class_num,-2);
    //$class= substr($curr_class_num,-4,2);
    //$year= substr($curr_class_num,0,1);
    settype($site,"integer");
    settype($class,"integer");
    settype($year,"integer");
    settype($stud_sex,"integer");
    $year_class_site_sex=array($year,$class,$site,$stud_sex,$stud_name);
    return $year_class_site_sex;
}
//由student_sn得到該位學生本學期的座號
function student_sn_to_site_num($student_sn){
    global $CONN;
    $rs_sn=$CONN->Execute("select stud_id from stud_base where student_sn='$student_sn'");
    $stud_id=$rs_sn->fields["stud_id"];
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $rs_seme=$CONN->Execute("select seme_num from stud_seme where stud_id='$stud_id' and seme_year_seme='$seme_year_seme'");
    $site=$rs_seme->fields["seme_num"];
    //$rs1=&$CONN->Execute("select  curr_class_num  from  stud_base where student_sn='$student_sn'");
    //$curr_class_num=$rs1->fields['curr_class_num'];
    //$site_num= substr($curr_class_num,-2);
    settype($site,"integer");
    return $site;
}

//取得本學期該班所有學生的基本資料
function class_id_to_student_sn($class_id){
    global $CONN;
    $class_id_array=explode("_",$class_id);
    $class_num=intval($class_id_array[2]).$class_id_array[3];
    $sql="select student_sn from stud_base where stud_study_cond=0 and curr_class_num like '$class_num%' order by curr_class_num ";
    $rs=$CONN->Execute($sql) or die($sql);;
    while (!$rs->EOF) {
        $student_sn[]=$rs->fields["student_sn"];
        $rs->MoveNext();
    }
    return $student_sn;
}


//本校目前學年與學期下拉式選單
function select_year_seme($id,$col_name){
    global $CONN;
    $sql="select distinct * from school_class order by year,semester";
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
function select_school_class($id,$col_name,$sel_year,$sel_seme){
    global $CONN;
    $sql="select * from school_class where year=$sel_year and semester=$sel_seme";
    $rs=$CONN->Execute($sql);
    $school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
    $option="<option value=''>選擇年級</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $c_year[$i]=$rs->fields["c_year"];
        $i++;
        $rs->MoveNext();
    }
    if($i==0) trigger_error("對不起！您尚未設定班級！",E_USER_ERROR);
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
function select_school_class_name($c_year,$id,$col_name,$sel_year,$sel_seme){
    global $CONN;
    if(empty($c_year)) $c_year=1;
    $sql="select * from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year";
    $rs=$CONN->Execute($sql);
    $option="<option value=''>選擇班級</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $c_name[$i]=$rs->fields["c_name"];
        $c_sort[$i]=$rs->fields["c_sort"];
        $i++;
        $rs->MoveNext();
    }
    if($i==0) trigger_error("對不起！您尚未設定班級！",E_USER_ERROR);
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
function select_stage($c_year,$c_name,$id,$col_name,$sel_year,$sel_seme){
    global $CONN,$score_semester;
    $sql="select class_id from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year and c_sort=$c_name";
    $rs=$CONN->Execute($sql);
    $class_id=$rs->fields["class_id"];
    $sql="select * from $score_semester where class_id='$class_id'";
//091_1_01_01
    $err_arr = explode ("_",$class_id);
    $err_str = sprintf("%d 學年第 %d 學期 平時成績尚未建立!!",$err_arr[0],$err_arr[1]);
    $rs=&$CONN->Execute($sql)or trigger_error($err_str, E_USER_ERROR);
    $i=0;
    while (!$rs->EOF) {
        $test_sort[$i]=$rs->fields["test_sort"];
        $i++;
        $rs->MoveNext();
    }
    $test_sort=deldup($test_sort);
    $option="<option value=''>選擇階段成績</option>\n";
    for($i=0;$i<=count($test_sort);$i++){
        $selected=($id==$test_sort[$i])?"selected":"";
        $selectedd=($id=="all")?"selected":"";
        if($i<count($test_sort)) $option.="<option value='$test_sort[$i]' $selected>第".$test_sort[$i]."階段</option>\n";
        if($i==count($test_sort)){
            if(count($test_sort)!=0){
                $option.="<option value='all' $selectedd>全學期</option>";
            }
        }
    }

    return $option;
}

//本校目前該年級該班級目前已有階段成績的選單
function select_stage1($c_year,$c_name,$id,$col_name,$sel_year,$sel_seme){
    global $CONN,$score_semester;
    $sql="select class_id from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year and c_sort=$c_name";
    $rs=$CONN->Execute($sql);
    $class_id=$rs->fields["class_id"];
    $sql="select * from $score_semester where class_id='$class_id'";
    $rs=&$CONN->Execute($sql);
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
function select_teacher_ss($id,$col_name,$teacher_id,$sel_year,$sel_seme){
    global $CONN;
    //echo $id." ".$col_name." ".$teacher_id." ".$sel_year." ".$sel_seme;
/***************************************************************************************/
//  將teacher_id 轉成 teacher_sn
    $sql="select teacher_sn from teacher_base where teach_id='$teacher_id'";
    $rs=$CONN->Execute($sql) or die($sql);
    $teacher_sn = $rs->fields["teacher_sn"];
/***************************************************************************************/

    $sql="select * from score_course where year=$sel_year and semester=$sel_seme and teacher_sn=$teacher_sn";
    $rs=$CONN->Execute($sql);
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
/*
        $sql1="select subject_id from score_ss where ss_id=$ss_id";
        $rs1=$CONN->Execute($sql1);
        $subject_id = $rs1->fields["subject_id"];
        if($subject_id!=0){
            $sql2="select subject_name from score_subject where subject_id=$subject_id";
            $rs2=$CONN->Execute($sql2);
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
*/
/***************************************************************************************/
        //$teacher_course[$i]=course_id_to_full_class_name($course_id[$i]);
        //$teacher_course[$i].=ss_id_to_subject_name($ss_id[$i]);
        $i++;
        $rs->MoveNext();
    }
    //$class_id=deldup($class_id);
    //$ss_id=deldup($ss_id);
    for($k=0;$k<$i;$k++){
        $sql="select course_id from score_course where class_id='$class_id[$k]' and ss_id='$ss_id[$k]'";
        $rs=$CONN->Execute($sql) or die($sql);
        $course_id[$k] = $rs->fields["course_id"];
        //echo $course_id[$k];
        $teacher_course[$k]=course_id_to_full_class_name($course_id[$k]);
        $teacher_course[$k].=ss_id_to_subject_name($ss_id[$k]);
    }
    $course_id=deldup($course_id);
    $teacher_course=deldup($teacher_course);
    $aa=$course_id.$teacher_course;
    $bgcolor=array("#E3DBFF","#E2D9FD","#DBD3F6","#D5CDEF","#CDC6E6","#C4BDDC","#C5BEDD","#BCB5D3","#B4ADCA","#ABA5CD");
    for($j=0;$j<count($teacher_course);$j++){
        $selected=($id==$course_id[$j])?"selected":"";
        $option.="<option value='$course_id[$j]'  style='background-color: $bgcolor[$j];' $selected>".$teacher_course[$j]."</option>\n";
    }

    $select_teacher_ss="<select name='$col_name'>$option</select>";
	$select_teacher_ss_1="$option";
	return $select_teacher_ss_1;
}

//
function select_teacher_course_id($id,$col_name,$teacher_id,$sel_year,$sel_seme){
    global $CONN;

/***************************************************************************************/
//  將teacher_id 轉成 teacher_sn
    $sql="select teacher_sn from teacher_base where teach_id=$teacher_id";
    $rs=$CONN->Execute($sql);
    $teacher_sn = $rs->fields["teacher_sn"];
/***************************************************************************************/

    $sql="select * from score_course where year=$sel_year and semester=$sel_seme and teacher_sn=$teacher_sn";
    $rs=$CONN->Execute($sql);
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
        $rs=$CONN->Execute($sql) or die($sql);
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
    $sort=1;
    for($i=0;$i<count($b);$i++){
        if($a<$b[$i]) $sort++;
    }
    return  $sort;
}


//由ss_id找出科目名稱的函數
function  ss_id_to_subject_name($ss_id){
    global $CONN;
    $sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2);
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

//由course_id找出幾年幾班
function  course_id_to_full_class_name($course_id){
    global $CONN;
    $select_course_id_sql="select * from score_course where course_id=$course_id";
    $rs_select_course_id=$CONN->Execute($select_course_id_sql);
    $class_id= $rs_select_course_id->fields['class_id'];
    $ss_id= $rs_select_course_id->fields['ss_id'];
    $school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
    //$full_year_class_name=$school_kind_name[$class_year];
    $sql="select * from school_class where class_id='$class_id'";
    $rs=$CONN->Execute($sql);
    $c_year= $rs->fields['c_year'];
    $c_name= $rs->fields['c_name'];
    $full_year_class_name=$school_kind_name[$c_year];
    $full_year_class_name.=$c_name."班";
    return $full_year_class_name;
}

//由student_sn找出學生的姓名
function  student_sn_to_stud_name($student_sn){
    global $CONN;
    $rs=&$CONN->Execute("select  stud_name  from  stud_base where student_sn='$student_sn'");
    $stud_name=$rs->fields['stud_name'];
    return $stud_name;
}

//由ss_id找出領域名稱的函數
function  ss_id_to_scope_name($ss_id){
    global $CONN;
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $scope_name = $rs4->fields["subject_name"];

    return $scope_name;
}

//班級學生選單
function get_stud_select($class_id, $stud_id="",$name="stud_id",$jump_fn="",$size=""){

	if (!$class_id) user_error("沒有傳入班級代碼！請檢查！",256);

	//取得學生資料陣列
	$c=class_id_2_old($class_id);
	$stud=get_stud_array($c[0],$c[1],$c[3],$c[4],"id","name");
	if(empty($size))$size=sizeof($stud);
	if(empty($stud))return "無學生資料";

	//加入java函數
	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";

	//製作班級選單
	$select_option="<option value='0'>選擇學生</option>\n";
	while(list($k,$v)=each($stud)){
		$selected=($stud_id==$k)?"selected":"";
		$select_option.="<option value='$k' $selected>$v</option>\n";
	}
	$select_stud="<select name='$name' size='$size' $jump>
	$select_option
	</select>";
	return $select_stud;
}

//取得某班學生陣列，傳回$stu[$k]=$v
//$k和$v的值可以是 id=學號，sn=流水號，name=姓名，sex=性別，num=座號
function get_stud_array($year=0,$seme=0,$Cyear=0,$Cnum=0,$k="id",$v="name"){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$year=(empty($year))?curr_year():$year*1;
	$seme=(empty($seme))?curr_seme():$seme*1;
	$str=array("id"=>"stud_id","sn"=>"student_sn","name"=>"stud_name","sex"=>"stud_sex","num"=>"right(curr_class_num,2)");

	$stud_year=(strlen($year)==2)?"0".$year.$seme:$year.$seme;
	$class_num=$Cyear*100+$Cnum;

	// init $stu
	$stu=array();

	$sql_select = "select  stud_base.$str[$k],stud_base.$str[$v] from stud_base,stud_seme where stud_base.student_sn=stud_seme.student_sn and  stud_seme.seme_year_seme='$stud_year' and stud_seme.seme_class='$class_num' and stud_study_cond=0 order by stud_seme.seme_num";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($k, $v) = $recordSet->FetchRow()){
		$stu[$k]=$v;
	}
	return $stu;
}

class data_student {

	var $student_sn="";
	var $base="";
	var $move="";
	var $seme="";
	var $abs="";
	var $rew="";
	var $rew_record="";
	var $seme_arr="";
	var $seme_arr2="";

	function data_student($student_sn="") {
		global $CONN;
		if (!empty($student_sn)) {
			$this->student_sn = $student_sn;
			$this->base = $this->get_base_data($student_sn);
			$this->move = $this->get_move_data($student_sn);
			$this->seme = $this->get_seme_data($student_sn);
			$this->service = $this->get_service_data($student_sn);  //服務學習
			$this->club = $this->get_club_data($student_sn);				//社團
			$this->get_abs_data($this->base[stud_id]);
			$this->get_rew_data($this->base[stud_id]);
			$this->get_rew_record($student_sn);
		}
	}

	//取得學生的所有社團資料 2013.10.23 by smallduh
	function get_club_data($student_sn="") {
		global $CONN;
		$PASS[1]="合格";
		$PASS[0]="未合格";
			if (!empty($student_sn)) {
  		$sql="select * from association where student_sn='$student_sn' order by seme_year_seme";
			$res = $CONN->Execute($sql) or die("Sql error, ".$sql);
			$row=$res->GetRows();
			//若為校內社團, 檢查是否通過 , 增加一個 pass 變數 0未過 1通過
			foreach ($row as $k=>$v) {
			 if ($v['club_sn']>0) {
			 	$query="select pass_score from stud_club_base where club_sn='".$v['club_sn']."'";
			 	$res_check=$CONN->Execute($query);
			 	$pass_score=$res_check->fields['pass_score'];
			 	 $row[$k]['pass']=($v['score']>=$pass_score)?1:0;
			 	 $row[$k]['pass_txt']=$PASS[$row[$k]['pass']];
			 } else {
			 //校外社團或直接匯入者
			   $row[$k]['pass']=1;
			   $row[$k]['pass_txt']=$PASS[$row[$k]['pass']];
			 } // end if
			} //end foreach
			
			return $row;
		}	
	}
	
	//取得學生的所有服務學習資料 2013.10.22 by smallduh
	function get_service_data($student_sn="") {
		global $CONN;
			if (!empty($student_sn)) {
  		$sql="select a.*,b.student_sn,b.item_sn,b.minutes,b.studmemo from stud_service a,stud_service_detail b where a.sn=b.item_sn and b.student_sn='$student_sn' and a.confirm=1 order by service_date";
			$res = $CONN->Execute($sql) or die("Sql error, ".$sql);
			return $res->GetRows();
		}	
	}
	
	function get_base_data($student_sn="") {
		global $CONN;
		if (!empty($student_sn)) {
			$sql = "SELECT a.stud_id , a.stud_name , a.stud_sex , a.stud_birthday , a.stud_birth_place , a.stud_person_id , 
							a.stud_addr_1 , a.stud_addr_2 , a.stud_mschool_name , a.stud_Mschool_status , a.stud_study_cond ,
							a.stud_tel_1 , a.stud_tel_2 , a.stud_tel_3 , a.stud_study_year,
							b.fath_name , b.fath_relation , b.fath_phone , b.moth_name , b.moth_relation , b.moth_phone , 
							b.guardian_name , b.guardian_relation , b.guardian_phone 
							FROM stud_base as a LEFT JOIN stud_domicile as b
							ON a.student_sn=b.student_sn
							WHERE a.student_sn='$student_sn'";
			$res = $CONN->Execute($sql) or die("Sql error, ".$sql);
			return $res->FetchRow();
		}
	}

	function get_move_data($student_sn="") {
		global $CONN;
		if (!empty($student_sn)) {
			$sql = "SELECT * FROM stud_move WHERE student_sn='$student_sn'";
			$res = $CONN->Execute($sql) or die("Sql error, ".$sql);
			return $res->GetRows();
		}
	}

	function get_seme_data($student_sn="") {
		global $CONN, $IS_JHORES;

		$cnum_arr=array("1"=>"一","2"=>"二","3"=>"三","4"=>"四","5"=>"五","6"=>"六","7"=>"七","8"=>"八","9"=>"九");
		$cseme_arr=array("1"=>"上","2"=>"下");
		$max_grade=6;
		if ($IS_JHORES>0) $max_grade=3;
		$query="select stud_study_year from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$stud_study_year=$res->fields['stud_study_year'];
		$arys = array();
		for($i=1;$i<=$max_grade;$i++) {
			$y=$IS_JHORES+$i;
			for($j=1;$j<=2;$j++) {
				$year=$stud_study_year-1+$i;
				$arys["{$y}_{$j}"]["year"]=$year;
				$arys["{$y}_{$j}"]["cseme"]=$cnum_arr[$y].$cseme_arr[$j];
				$this->seme_arr2[$year.$j]=$cnum_arr[$y].$cseme_arr[$j];
				$this->seme_arr[(sprintf("%03d",$year).$j)]=$y."_".$j;
				for($k=1;$k<=6;$k++) {
					$this->abs["{$y}_{$j}"][$k]="";
					$this->rew["{$y}_{$j}"][$k]="";
				}
			}
		}
		if (!empty($student_sn)) {
			$query="select seme_year_seme,seme_class,seme_num from stud_seme where student_sn='{$student_sn}' order by seme_year_seme,seme_class";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$c=substr($res->fields['seme_class'],-2,2);
				$n=sprintf("%02d",$res->fields['seme_num']);
				$arys[$this->seme_arr[$res->fields['seme_year_seme']]]["num"]=$c."班".$n."號";
				$res->MoveNext();
			}
			$query="select * from stud_seme_score_nor where student_sn='{$student_sn}' and ss_id='0' order by seme_year_seme";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$arys[$this->seme_arr[$res->fields['seme_year_seme']]]["memo"]=$res->fields['ss_score_memo'];
				$res->MoveNext();
			}
		}
		return $arys;
	}

	function get_abs_data($stud_id="") {
		global $CONN;

		if (!empty($stud_id)) {
			$s=array_keys($this->seme_arr);
			$seme_str="'".implode("','",$s)."'";
			$query="select * from stud_seme_abs where stud_id='$stud_id' and seme_year_seme in ($seme_str) order by seme_year_seme,abs_kind";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->abs[$this->seme_arr[$res->fields['seme_year_seme']]][$res->fields['abs_kind']]=$res->fields['abs_days'];
				$res->MoveNext();
			}
		}
	}

	function get_rew_data($stud_id="") {
		global $CONN;

		if (!empty($stud_id)) {
			$s=array_keys($this->seme_arr);
			$seme_str="'".implode("','",$s)."'";
			$query="select * from stud_seme_rew where stud_id='$stud_id' and seme_year_seme in ($seme_str) order by seme_year_seme,sr_kind_id";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$this->rew[$this->seme_arr[$res->fields['seme_year_seme']]][$res->fields['sr_kind_id']]=$res->fields['sr_num'];
				$res->MoveNext();
			}
		}
	}

	function get_rew_record($student_sn="") {
		global $CONN;

		if (!empty($student_sn)) {
			$query="select * from reward where student_sn='$student_sn' order by reward_year_seme,reward_date";
			$res=$CONN->Execute($query);
			$this->rew_record=$res->GetRows();
		}
	}
}
?>
