<?php

// $Id: my_fun.php 9240 2018-05-04 03:25:04Z igogo $

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

//本校目前學年與學期下拉式選單
function select_year_seme($id,$col_name){
    global $CONN;
    $sql="select * from school_class order by year,semester";
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
    $sql="select distinct c_year from school_class where year=$sel_year and semester=$sel_seme order by year,semester,c_year";
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
    $sql="select distinct c_name,c_sort from school_class where year=$sel_year and semester=$sel_seme and c_year=$c_year order by year,semester,c_year,c_sort";
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

	$sql_select = "select  stud_base.$str[$k],stud_base.$str[$v] from stud_base,stud_seme where stud_base.stud_id=stud_seme.stud_id and  stud_seme.seme_year_seme='$stud_year' and stud_seme.seme_class='$class_num' and stud_study_cond=0 order by stud_seme.seme_num";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($k, $v) = $recordSet->FetchRow()){
		$stu[$k]=$v;
	}
	return $stu;
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

function seme_menu($start_year,$id) {
	global $CONN,$IS_JHORES;

	$s_y=$start_year;
	$e_y=($IS_JHORES=="6")?"2":"5";
	$e_y+=$s_y;
	$query="select * from school_class where year >= '$s_y' and year <= '$e_y' order by year,semester";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$temp_arr[$res->fields["year"]."_".$res->fields['semester']]=$res->fields["year"]."學年度第".$res->fields['semester']."學期";
		$res->MoveNext();
	}
	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "選擇學期";
	$scys->id = $id;
	$scys->arr = $temp_arr;
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
	global $CONN,$score_semester,$choice_kind;

	$sql="select class_id from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$sel_class' and c_sort='$sel_num'";
	$rs=$CONN->Execute($sql);
	$class_id=$rs->fields["class_id"];
	if ($all) {
		$class_id=substr($class_id,0,strlen($class_id)-2)."%";
		$sql="select distinct test_sort from $score_semester where class_id like '$class_id' and test_kind = '$choice_kind' and test_sort < '200' order by test_sort";
	} else {
		$sql="select distinct test_sort from $score_semester where class_id='$class_id' order by test_sort";
	}
	$rs=&$CONN->Execute($sql);
	if(is_object($rs)){
		while (!$rs->EOF) {
			$test_sort=$rs->fields["test_sort"];
			if($test_sort<200)	$show_stage[$test_sort]="第".$test_sort."階段";
			$rs->MoveNext();
		}
	}
	$show_stage[255]="全學期";
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
	$show_kind=array("1"=>"定期評量","2"=>"平時成績","3"=>"定期+平時");

	$sk = new drop_select();
	$sk->s_name ="kind";
	$sk->top_option = "選擇種類";
	$sk->id = $id;
	$sk->arr = $show_kind;
	$sk->is_submit = true;
	return $sk->get_select();
}

function stud_menu($sel_year,$sel_seme,$sel_class,$sel_num,$sel_sn) {
	global $CONN;

	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$sel_class.sprintf("%02d",$sel_num);
	$query="select a.*,b.stud_name,b.stud_sex from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' order by a.seme_num";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$temp_arr[$res->fields['student_sn']]=$res->fields['stud_name'];
		$cr_arr[$res->fields['student_sn']]=$res->fields['stud_sex'];
		$res->MoveNext();
	}
	$s = new drop_select();
	$s->s_name ="student_sn";
	$s->top_option = "選擇學生";
	$s->id = $sel_sn;
	$s->arr = $temp_arr;
	//依性別顯示顏色
	$s->is_display_color = true;
	$s->color_index_arr = $cr_arr;
	$s->color_item = array("black","blue","red");
	$s->is_submit = true;
	return $s->get_select();
}

//匯到教務處
function cal_seme_score($sel_year,$sel_seme,$class_id,$ss_id) {
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
	$query = "select distinct ss_id from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn in ($temp_sn) $check_ss";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
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
		if ($rs->recordcount()>0)
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
