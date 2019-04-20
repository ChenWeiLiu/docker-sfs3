<?php
// $Id: my_fun.php 5310 2009-01-10 07:57:56Z hami $


//取得模組設定
$m_arr = &get_module_setup("score_input");
extract($m_arr, EXTR_OVERWRITE);


//由student_sn得到本學期學生的班級座號姓名
function student_sn_to_classinfo($student_sn){
    global $CONN;
    $rs_sn=$CONN->Execute("select stud_id from stud_base where student_sn='$student_sn'");
    $stud_id=$rs_sn->fields["stud_id"];
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $rs_seme=$CONN->Execute("select seme_class,seme_num from stud_seme where stud_id='$stud_id' and seme_year_seme='$seme_year_seme' order by seme_num ");
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

//本校目前該年級該班級該科目目前已有階段成績的選單
function now_stage($id,$col_name,$teacher_id,$sel_year,$sel_seme,$class_id,$ss_id){
    global $CONN,$yorn;
    
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
	if($yorn=='n'){
		$sd=($id==254)?"selected":"";
		$option.="<option value='254' $sd>平時成績</option>\n";
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
    $rs=$CONN->Execute($sql) or trigger_error($sql);;
    while (!$rs->EOF) {
        $student_sn[]=$rs->fields["student_sn"];
        $rs->MoveNext();
    }
    return $student_sn;
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


//由subject_id找出科目名稱的函數
function  subject_id_to_subject_name($subject_id){
    global $CONN;
    $sql1="select subject_name from score_subject where subject_id=$subject_id and enable=1";
    $rs1=$CONN->Execute($sql1);
    $subject_name = $rs1->fields["subject_name"];
    return $subject_name;
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

//由class_id找出幾年幾班
function  class_id_to_full_class_name($class_id){
    global $CONN;
    $class_sql="select * from school_class where class_id='$class_id'";
    $rs_class=$CONN->Execute($class_sql);
    $c_year= $rs_class->fields['c_year'];
    $c_name= $rs_class->fields['c_name'];
    $school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
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
//是否每一次月考要配合一次平時成績
function  findyorn(){
	global $CONN;
	$rs_yorn=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='score_input' AND pm_item='yorn'");
	$yorn=$rs_yorn->fields['pm_value'];
	return $yorn;
}

function year_seme_menu($sel_year,$sel_seme) {
	global $CONN;

	$sql="select year,semester from school_class where enable='1' order by year,semester";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$year=$rs->fields["year"];
		$semester=$rs->fields["semester"];
		if ($year!=$oy || $semester!=$os)
			$show_year_seme[$year."_".$semester]=$year."學年度第".$semester."學期";
		$oy=$year;
		$os=$semester;
		$rs->MoveNext();
	}
	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "選擇學期";
	$scys->id = $sel_year."_".$sel_seme;
	$scys->arr = $show_year_seme;
	$scys->is_submit = true;
	return $scys->get_select();
}

function class_year_menu($sel_year,$sel_seme,$id) {
	global $school_kind_name,$CONN;

	$sql="select distinct c_year from school_class where year='$sel_year' and semester='$sel_seme' and enable='1' order by c_year";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$show_year_name[$rs->fields["c_year"]]=$school_kind_name[$rs->fields["c_year"]]."級";
		$rs->MoveNext();
	}
	$scy = new drop_select();
	$scy->s_name ="year_name";
	$scy->top_option = "選擇年級";
	$scy->id = $id;
	$scy->arr = $show_year_name;
	$scy->is_submit = true;
	return $scy->get_select();
}

function class_name_menu($sel_year,$sel_seme,$sel_class,$id) {
	global $CONN;

	$sql="select distinct c_name,c_sort from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$sel_class' and enable='1' order by c_sort";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$show_class_year[$rs->fields["c_sort"]]=$rs->fields["c_name"]."班";
		$rs->MoveNext();
	}
	$sc = new drop_select();
	$sc->s_name ="me";
	$sc->top_option = "選擇班級";
	$sc->id = $id;
	$sc->arr = $show_class_year;
	$sc->is_submit = true;
	return $sc->get_select();
}

function stage_menu($sel_year,$sel_seme,$sel_class,$sel_num,$id,$all="") {
	global $CONN,$score_semester,$choice_kind,$yorn;

	$sql="select class_id from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$sel_class' and c_sort='$sel_num'";
	$rs=$CONN->Execute($sql);
	$class_id=$rs->fields["class_id"];
	if ($all) {
		$class_id=substr($class_id,0,strlen($class_id)-2)."%";
		$sql="select distinct test_sort from $score_semester where class_id like '$class_id' and test_kind = '$choice_kind' and test_sort < '200' order by test_sort";
	} else {
		$sql="select distinct test_sort from $score_semester where class_id='$class_id' order by test_sort";
	}
	$rs=$CONN->Execute($sql);
	if(is_object($rs)){
		while (!$rs->EOF) {
			$test_sort=$rs->fields["test_sort"];
			if($test_sort<200)	$show_stage[$test_sort]="第".$test_sort."階段";
			$rs->MoveNext();
		}
	}
	if ($yorn=="n") $show_stage["254"]="平時成績";
	$rs=$CONN->Execute("select distinct print from score_ss where class_year='$sel_class' and enable='1' and need_exam='1' and print!='1'");
	if ($rs->recordcount()>0) $show_stage["255"]="不分階段";
	$ss = new drop_select();
	$ss->s_name ="stage";
	$ss->top_option = "選擇階段";
	$ss->id = $id;
	$ss->arr = $show_stage;
	$ss->is_submit = true;
	return $ss->get_select();
}

function kind_menu($sel_year,$sel_seme,$sel_class,$sel_num,$stage,$id) {
	global $CONN;
	$show_kind=array("1"=>"定期評量","2"=>"平時成績","3"=>"定期+平時","4"=>"定期；平時");

	$sk = new drop_select();
	$sk->s_name ="kind";
	$sk->top_option = "選擇種類";
	$sk->id = $id;
	$sk->arr = $show_kind;
	$sk->is_submit = true;
	return $sk->get_select();
}

function score_head($sel_year,$sel_seme,$year_name,$me,$stage,$chart_kind){
    global $CONN,$school_kind_name;
    $rs1=&$CONN->Execute("select * from school_base");
    $sch_sheng=$rs1->fields['sch_sheng'];
    $sch_cname=$rs1->fields['sch_cname'];
    if(strlen($sel_year)==2) $sel_year="0".$sel_year;
    if(strlen($year_name)==1) $year_name="0".$year_name;
    if(strlen($me)==1) $me="0".$me;
    $class_id=$sel_year."_".$sel_seme."_".$year_name."_".$me;
    $rs2=&$CONN->Execute("select * from school_class where class_id='$class_id'");
    $c_year=$rs2->fields['c_year'];
    $c_name=$rs2->fields['c_name'];
    settype($sel_year,"integer");
    $stage_name=array(1=>"第一階段",2=>"第二階段",3=>"第三階段",4=>"第四階段","all"=>"全學期");
    return $sch_cname.$sel_year."學年度第".$sel_seme."學期".$school_kind_name[$c_year].$c_name."班".$stage_name[$stage].$chart_kind."成績表";
}

//匯到教務處
function seme_score_input($sel_year,$sel_seme,$class_id,$ss_id) {
	global $CONN,$now,$yorn;
	//學期資料表名稱
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	$seme_year_seme = sprintf("%03d",$sel_year).$sel_seme;
	$temp_class_id_arr=explode("_",$class_id);
	//將班級字串轉為陣列
	$class_arr=class_id_2_old($class_id);
	$query = "select performance_test_times,score_mode,test_ratio from score_setup where class_year=$class_arr[3] and year='$sel_year' and semester='$sel_seme' and enable='1'";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	//測驗次數
	$performance_test_times = $res->fields[performance_test_times];
	//成績配分比例相關設定
	$score_mode = $res->fields[score_mode];
	//比率
	$test_ratios = $res->fields[test_ratio];
	 //比率換算
        if($score_mode=="all"){
       	        $test_ratio=explode("-",$test_ratios);
	}
	//每階段評量都是不同比率
	elseif($score_mode=="severally"){
		$temp_arr=explode(",",$test_ratios);
		while(list($id,$val) = each($temp_arr)){
			$test_ratio_temp=explode("-",$val);
			$test_ratio[$id][0]=$test_ratio_temp[0];
			$test_ratio[$id][1]=$test_ratio_temp[1];
		}
	}else{
		$test_ratio[0]=60;
		$test_ratio[1]=40;
	}

	//先取出學生資料
	$seme_class=intval($temp_class_id_arr[2]).$temp_class_id_arr[3];
	$query = "select student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class'";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	while(!$res->EOF) {
		$temp_sn.="'".$res->fields['student_sn']."',";
		$res->MoveNext();
	}
	$temp_sn=substr($temp_sn,0,-1);
	//檢查 stud_seme_score 學期成績表有無記錄
	$check_ss=($ss_id)?"and ss_id='$ss_id'":"";
	$all_ss=array();
	$query = "select ss_id from score_ss where year='$sel_year' and semester='$sel_seme' and class_id='$class_id' and enable='1' $check_ss";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	if ($res->fields[ss_id]=="") {
		$query = "select ss_id from score_ss where year='$sel_year' and semester='$sel_seme' and enable='1' $check_ss";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	}
	while(!$res->EOF){
		$all_ss[]=$res->fields['ss_id'];
		$res->MoveNext();
	}
	while(list($k,$ss_id)=each($all_ss)){
		$query = "select print from score_ss where ss_id='$ss_id'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$print=$res->fields['print'];
		$temp_sn_seme_arr = "";
		$query = "select student_sn from stud_seme_score where ss_id='$ss_id' and seme_year_seme='$seme_year_seme' and student_sn in($temp_sn)";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$temp_sn_seme_arr.="'".$res->fields[0]."',";
			$res->MoveNext();
		}
		$temp_sn_seme_arr=substr($temp_sn_seme_arr,0,-1);

		//先將文字描述取出
		$rs=$CONN->Execute("select student_sn,ss_score_memo from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn in ($temp_sn_seme_arr) and ss_id='$ss_id'");
		if ($rs->fields['student_sn'])
			while (!$rs->EOF) {
				$val_arr[$rs->fields['student_sn']]=addslashes($rs->fields['ss_score_memo']);
				$rs->MoveNext();
			}

		//階段成績 平時成績
		if ($print==1) {

			//如果每學期只設定一次學期平時成績且每階段評量比率皆不同時,比率為 100 - 各階段評量比率
			if ($yorn =='n' and $score_mode=="severally"){
				$temp_ratio=0;
				for($i=0;$i<$performance_test_times;$i++) $temp_ratio += $test_ratio[$i][0];
				$temp_ratio = (100-$temp_ratio);
			}

			//計算學期成績
			//全學期都是一種設定
			if($score_mode=="all"){
				if($yorn =='y')
					$query = "select student_sn,test_kind,sum(score) as cc from $score_semester where ss_id=$ss_id and class_id='$class_id' and test_sort <= $performance_test_times and score <> '-100' group by student_sn,test_kind ";
				else
					$query = "select student_sn,test_kind,sum(score) as cc from $score_semester where ss_id=$ss_id and class_id='$class_id' and test_sort <= $performance_test_times and score <> '-100' and (test_kind='定期評量' or test_kind='平時成績') group by student_sn,test_kind";
//				echo $query."<BR>";
				$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
				$score_arr = array();
				$test_ratio_1  = $test_ratio[0]/100;
				$test_ratio_2  = $test_ratio[1]/100;

				while(!$res->EOF){
					$student_sn = $res->fields[student_sn];
					$test_kind = $res->fields[test_kind];
					$score = $res->fields[cc];
					if ($score=='') $score=0;
					if ($test_kind == "定期評量")
						$cc = ($score/$performance_test_times)*$test_ratio_1;
					else
						$cc = $score * $test_ratio_2 / $performance_test_times;
//					echo "$student_sn --  $test_kind -- $test_ratio_1 --  $test_ratio_2 -- $cc <BR>";
					$score_arr[$student_sn] += $cc;
					$res->MoveNext();
				}
			}
			//每次評量都不同設定
			else {
				if ($yorn=='y')
					$query = "select student_sn,test_kind,test_sort,score from $score_semester where ss_id='$ss_id' and class_id='$class_id' and test_sort<255 ";
				else
					$query = "select student_sn,test_kind,test_sort,score from $score_semester where ss_id='$ss_id' and class_id='$class_id' and (test_kind='定期評量' or test_kind='平時成績')";
				$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
				while(!$res->EOF){
					$test_sort = $res->fields[test_sort];
					$student_sn = $res->fields[student_sn];
					$test_kind = $res->fields[test_kind];
					$score = $res->fields[score];
					if ($score=="-100") $score=0;
					$id = $test_sort-1;
					if ($test_kind=='定期評量')
						$cc = $score*$test_ratio[$id][0]/100;
	                                else
						$cc = $score*$test_ratio[$id][1]/100;
					$score_arr[$student_sn] += $cc;
					$res->MoveNext();
				}
			}
			//將成績填入學期成績檔
			while(list($id,$val) = each($score_arr)){
				$query = "replace into stud_seme_score (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo,teacher_sn)values('$seme_year_seme','$id','$ss_id','$val','$val_arr[$id]','$_SESSION[session_tea_sn]')";
				$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			}
		}
		//全學期一次成績
		else if ($print==0) {
			//將成績填入學期成績檔
			$score_arr=array();
			$query = "select student_sn,score from $score_semester where ss_id='$ss_id' and class_id='$class_id' and test_sort=255";
			$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			while(!$res->EOF){
				$score_arr[$res->fields['student_sn']]=$res->fields['score'];
				$res->MoveNext();
			}
			reset($score_arr); 
			while(list($sn,$score) = each($score_arr)){
				$query = "replace into stud_seme_score (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo,teacher_sn)values('$seme_year_seme','$sn','$ss_id','$score','$val_arr[$sn]','$_SESSION[session_tea_sn]')";
				$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			}
		}
	}
}
?>
