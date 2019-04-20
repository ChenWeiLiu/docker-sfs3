<?php
// $Id: my_fun.php 5310 2009-01-10 07:57:56Z hami $
//本校目前學年與學期下拉式選單
function select_year_seme($id,$col_name){
    global $CONN;
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
    $rs=&$CONN->Execute($sql);
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
    global $CONN,$score_semester,$score_semester;
    $sql="select class_id from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year and c_sort=$c_name";
    $rs=$CONN->Execute($sql);
    $class_id=$rs->fields["class_id"];
    $sql="select * from $score_semester where class_id='$class_id' order by test_sort";	
    $rs=&$CONN->Execute($sql);
    $i=0;
    if(is_object($rs)){
        while (!$rs->EOF) {
            $test_sort[$i]=$rs->fields["test_sort"];
            $i++;
            $rs->MoveNext();
        }
        $test_sort=deldup($test_sort);
        $option="<option value=''>選擇階段</option>\n";
        for($i=0;$i<count($test_sort);$i++){
            $selected=($id==$test_sort[$i])?"selected":"";
            $test_sort_name[$i]="第".$test_sort[$i]."階段";
            if($test_sort[$i]==255) $test_sort_name[$i]="不分階段";
			if($test_sort[$i]==254) $test_sort_name[$i]="平時階段";
            $option.="<option value='$test_sort[$i]' $selected>$test_sort_name[$i]</option>\n";
        }		 
    }
    else{
        $option.="<option $selected>尚無資料</option>\n";
    }

    return $option;
}

//登入老師目前任課的班級與科目的選單
function select_teacher_ss($id,$col_name,$teacher_id,$sel_year,$sel_seme){
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
        $ss_id[$i] = $rs->fields["ss_id"];
        $teacher_sn = $rs->fields["teacher_sn"];

/***************************************************************************************/
//  將subject_id 轉成 subject_name

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

/***************************************************************************************/
        $teacher_course[$i]=course_id_to_full_class_name($course_id[$i]);
        $teacher_course[$i].=ss_id_to_subject_name($ss_id[$i]);
        $i++;
        $rs->MoveNext();
    }
    $course_id=deldup($course_id);
    $teacher_course=deldup($teacher_course);
    $bgcolor=array("#E3DBFF","#E2D9FD","#DBD3F6","#D5CDEF","#CDC6E6","#A59FB9","#C5BEDD","#BCB5D3","#B4ADCA","#ABA5CD");
    for($j=0;$j<count($teacher_course);$j++){
        $selected=($id==$course_id[$j])?"selected":"";
        $option.="<option value='$course_id[$j]'  style='background-color: $bgcolor[$j];' $selected>".$teacher_course[$j]."</option>\n";
    }

    $select_teacher_ss="<select name='$col_name'>$option</select>";
	$select_teacher_ss_1="$option";
	return $select_teacher_ss_1;
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

//取得本學期該班所有學生的基本資料
function class_id_to_student_sn($class_id){
    global $CONN;
    $c_id=explode("_",$class_id);
    $sel_year=$c_id[0];
    if(strlen($sel_year)==3) {if(substr($sel_year,0,1)==0){ $sel_year=substr($sel_year,1);} }
    if(strlen($c_id[2])==2) {if(substr($c_id[2],0,1)==0){ $c_id[2]=substr($c_id[2],1);} }
    $class_num=$c_id[2].$c_id[3];
    $sql="select student_sn from stud_base where stud_study_cond=0 and curr_class_num like '$class_num%' order by curr_class_num ";
    //echo $sql;
    $rs=$CONN->Execute($sql);
    $i=0;
    while (!$rs->EOF) {
        $student_sn[$i]=$rs->fields["student_sn"];
        $i++;
        $rs->MoveNext();
    }
    return $student_sn;
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
//由流水號取得某一學期的學生資料
function classinfo($student_sn,$year="",$semester=""){
    global $CONN;
    
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if (!$student_sn) user_error("沒有傳入學生流水號！請檢查！",256);
	
	if($year=="") $year=curr_year();
	if($semester=="") $semester=curr_seme();
	$seme_year_seme=sprintf("%03d%d",$year,$semester);
    $rs_seme=$CONN->Execute("select seme_class,seme_num,seme_class_name from stud_seme where student_sn='$student_sn' and seme_year_seme='$seme_year_seme' order by seme_num ") or trigger_error("SQL語法錯誤 ", E_USER_ERROR);
    $seme_class=$rs_seme->fields["seme_class"];
	$seme_class_name=$rs_seme->fields['seme_class_name'];
	$site=$rs_seme->fields['seme_num'];
    $year= substr($seme_class,0,-2);
	$rt=$year."年".$seme_class_name."班".$site."號";
	return $rt;
}
?>
