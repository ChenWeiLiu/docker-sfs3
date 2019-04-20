<?php
function big_s(){
	global $CONN,$togeter,$sel_year,$sel_seme,$yyear_name,$select_subject_weight,$select_subject,$periodical;

	//建立一個暫存的學生上下學期成績資料表
    $creat_table_sql="CREATE TEMPORARY TABLE tmp_score(
    sn int(11) NOT NULL auto_increment,
    student_sn int,
    score float,
    PRIMARY KEY  (sn)
    )";
    $rs=$CONN->Execute($creat_table_sql);
    $CONN->Execute("delete from tmp_score");
	$lastone=count($togeter)-1;
	$b=0;
	while(list($key , $val) = each($togeter)) {
		$where0.=($b=="0")?"and(":"";
		$class_id[$key]=sprintf("%03d_%d_%02d_%02d",$sel_year,1,substr($val,0,-2),substr($val,-2));
		$where0.=" class_id='$class_id[$key]'";
		$where0.=($b==$lastone)?")":" || ";
		//echo $where0."<br>";
		$where1.=($b=="0")?"and(":"";
		$class_id[$key]=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($val,0,-2),substr($val,-2));
		$where1.=" class_id='$class_id[$key]'";
		$where1.=($b==$lastone)?")":" || ";
		$b++;
	}
	$score_semester1="score_semester_".$sel_year."_1";
	$score_semester2="score_semester_".$sel_year."_2";
	//判斷上學期的成績資料表是否存在
	$sql="select *  from $score_semester1  where 1=0";
	$rs1=$CONN->Execute($sql);
	//判斷下學期的成績資料表是否存在
	$sql="select *  from $score_semester2  where 1=0";
	$rs2=$CONN->Execute($sql);
	
	$periodical=$periodical?"test_kind='定期評量' and":'';
	$total_score_subject_count=array();
	
	for($i=0;$i<count($select_subject_weight);$i++){
	$lastone=count($togeter)-1;
		if(!$select_subject[$i]) $select_subject[$i]=0;
        if($sel_seme==1){
            if($rs1){
                $sql="select * from $score_semester1 where $periodical ss_id=$select_subject[$i] $where1";
                $rs=$CONN->Execute($sql) or die($sql);
                $j=0;
                while (!$rs->EOF) {
                    $student_sn[$j]=$rs->fields["student_sn"];
                    $score[$j]=$rs->fields["score"];

                    $test_sort[$j]=$rs->fields["test_sort"];
                    //$total_score_m[$j]=$total_score_m[$j]+($score[$j]*$select_subject_weight[$i]);
                    //$total_score[$student_sn[$j]]=$total_score[$student_sn[$j]]+($score[$j]*$select_subject_weight[$i]); <==原舊程式碼
					$total_score[$student_sn[$j]]+=$score[$j]*$select_subject_weight[$i];

                    $j++;
                    $rs->MoveNext();
                }
            }
         }
         else{
            if($rs1){
                $sql="select *  from $score_semester1  where $periodical ss_id=$select_subject[$i] $where0";
                //echo $sql."<br>";
                $rs=$CONN->Execute($sql) or trigger_error($sql);
                $j=0;
                while (!$rs->EOF) {
                    $student_sn[$j]=$rs->fields["student_sn"];
                    $score[$j]=$rs->fields["score"];
                    //echo $score[$j]."<br>";
                    $sql_tmp="INSERT INTO tmp_score(student_sn,score) values('$student_sn[$j]','$score[$j]')";
                    $rs_tmp=$CONN->Execute($sql_tmp) or trigger_error($sql_tmp) ;
                    $j++;
					
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['counter']++;
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['score']+=$rs->fields["score"];
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['weight']=$select_subject_weight[$i];
					$total_score_subject_count[($rs->fields["student_sn"])]['total']+=($select_subject_weight[$i])*$rs->fields["score"];
					
					$total_score[($rs->fields["student_sn"])]+=($select_subject_weight[$i])*$rs->fields["score"];
					
                    $rs->MoveNext();
                }
            }
            if($rs2){
                $sql="select *  from $score_semester2  where $periodical ss_id=$select_subject[$i] $where1";
                $rs=$CONN->Execute($sql) or die($sql);
                //echo $sql."<br>";
                $j=0;
                while (!$rs->EOF) {
                    $student_sn[$j]=$rs->fields["student_sn"];
                    $score[$j]=$rs->fields["score"];
                    //echo $student_sn[$j]."---";
					//echo $score[$j]."<br>";
                    $sql_tmp="INSERT INTO tmp_score(student_sn,score) values('$student_sn[$j]','$score[$j]')";
                    $rs_tmp=$CONN->Execute($sql_tmp) or die($sql_tmp) ;
                    $j++;
					
					
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['counter']++;
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['score']+=$rs->fields["score"];
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['weight']=$select_subject_weight[$i];
					$total_score_subject_count[($rs->fields["student_sn"])]['total']+=($select_subject_weight[$i])*$rs->fields["score"];
		
					$total_score[($rs->fields["student_sn"])]+=($select_subject_weight[$i])*$rs->fields["score"];
					
					
                    $rs->MoveNext();
                }
            }

			
            //echo $sql;
			//echo "--------------------------------<br>";
            $sql="select * from tmp_score";
            $rs=$CONN->Execute($sql) or die($sql);
            $j=0;
            while (!$rs->EOF) {
                $student_sn[$j]=$rs->fields["student_sn"];
                $score[$j]=$rs->fields["score"];
                //echo student_sn_to_stud_name($student_sn[$j])."---";
				//echo $score[$j]."<br>";
                //$total_score_m[$j]=$total_score_m[$j]+($score[$j]*$select_subject_weight[$i]);
                //$total_score[($student_sn[$j])]=$total_score[($student_sn[$j])]+($score[$j]*$select_subject_weight[$i]);  <===原程式碼有問題　by infodaes
				
                $j++;
                $rs->MoveNext();
            }
            $T=$T+$j;
			//echo $j."<br>";
         }
    }
	
			
//echo "<pre>";			
//print_r($total_score_subject_count);
//echo "</pre>";		
	
	if($T=="0") trigger_error("您所選的科目均無成績！",E_USER_ERROR);
	$student_sn=array_unique ($student_sn);
	$k=0;
	foreach($student_sn as $stud_val){
        //檢查該生的學籍狀況
        $sql_c="select * from stud_base where student_sn='$stud_val'";
        //echo $sql_c."<br>";
		$rs_c=$CONN->Execute($sql_c) or die($sql_c) ;
        $stud_study_year[$k]=$rs_c->fields["stud_study_year"];
        $curr_class_num[$k]=$rs_c->fields["curr_class_num"];
        $stud_study_cond[$k]=$rs_c->fields["stud_study_cond"];
        $site= substr($curr_class_num[$k],-2);
        $class= substr($curr_class_num[$k],-4,2);
        $year[$k]= substr($curr_class_num[$k],0,1);
        if(($stud_study_cond[$k]!=0) || ($year[$k]!=$yyear_name) ) continue;
        $student_name[$k]=student_sn_to_stud_name($stud_val);
        //echo $student_sn[$k]."-->".$student_name[$k]."-->".$total_score[$student_sn[$k]]."<br>";
        $t_s[$k]=$total_score[$stud_val];
        $sql="INSERT INTO tmp_stud_compile(student_sn,total_score) values('$stud_val','$t_s[$k]')";
        $rs=$CONN->Execute($sql) or die($sql) ;
		$k++;
    }
    $B=school_class_info();
    $sql="select * from tmp_stud_compile order by total_score DESC";
    $rs=$CONN->Execute($sql) or die($sql) ;
    $j=0;
    while (!$rs->EOF) {
        $student_sn[$j]=$rs->fields["student_sn"];
        $total_score[$j]=$rs->fields["total_score"];
        $stud_name[$j]=student_sn_to_stud_name($student_sn[$j]);
        $stud_class[$j]=student_sn_to_classinfo($student_sn[$j]);
        //echo $stud_class[$j][3];
        //判斷男女生
        $jj=$j+1;
        $B_name=$B[$stud_class[$j][0]][$stud_class[$j][1]];
		if($stud_class[$j][3]==1) {
            $boy_sort_name[$j]=$student_sn[$j];
            $boy_list.="<font color='blue'>".$stud_class[$j][0]."年".$B_name."班 ".sprintf('%02d',$stud_class[$j][2])."號 ".$stud_name[$j]."</font> ($jj-".$total_score[$j].")<br>";
            //echo $boy_list;
        }
        if($stud_class[$j][3]==2) {
            $girl_sort_name[$j]=$student_sn[$j];
            $girl_list.="<font color='magenta'>".$stud_class[$j][0]."年".$B_name."班 ".sprintf('%02d',$stud_class[$j][2])."號 ".$stud_name[$j]."</font> ($jj-".$total_score[$j].")<br>";
        }

        $boy_sort_form.="<input type='hidden' name='boy_sort_name[$j]' value='$boy_sort_name[$j]'>";
        $girl_sort_form.="<input type='hidden' name='girl_sort_name[$j]' value='$girl_sort_name[$j]'>";
        $j++;
        $rs->MoveNext();
    }


	//$periodical_limit=$periodical?'※只計算定期評量成績':'';
	
    if($rs1 || $rs2) echo "<table cellspacing=1 cellpadding=4 border=0 bgcolor=#27A208><tr bgcolor=#96FF73 align=center><td>男生</td><td>女生</td><td>編班原則</td></tr><tr bgcolor=#ffffff><td valign='top'>$boy_list</td><td valign='top'>$girl_list</td>";
    else echo "學期成績資料表尚未建立，無法進行排名！";


    $B=school_class_info();
    $many_class=count($B[$yyear_name]);
    $yyear_name2=$yyear_name+1;
    $yyear_name_select="<select name='Year_name'><option>$yyear_name2</option><option>$yyear_name</option></select>";
    $compile_class="
	<form name='form3' method='post' action='{$_SERVER['PHP_SELF']}'>
		$boy_sort_form
        $girl_sort_form
        編為 $yyear_name_select 年級<br>
        班群一：<input type='text' name='group[0]' size='3' maxlength='3' value='$many_class'>班<br>
        班群二：<input type='text' name='group[1]' size='3' maxlength='3' value=''>班<br>
        班群三：<input type='text' name='group[2]' size='3' maxlength='3' value=''>班<br>
        班群四：<input type='text' name='group[3]' size='3' maxlength='3' value=''>班<br>
        班群五：<input type='text' name='group[4]' size='3' maxlength='3' value=''>班<br>
        班群六：<input type='text' name='group[5]' size='3' maxlength='3' value=''>班<br>
        班群七：<input type='text' name='group[6]' size='3' maxlength='3' value=''>班<br>
        班群八：<input type='text' name='group[7]' size='3' maxlength='3' value=''>班<br>
        班群九：<input type='text' name='group[8]' size='3' maxlength='3' value=''>班<br>
        班群十：<input type='text' name='group[9]' size='3' maxlength='3' value=''>班<br>

        <input type='submit' name='Submit3' value='開始大S編班'>
    </form>";

    if($rs1 || $rs2) echo "<td valign='top'>$compile_class</td></tr></table>";

}

function small_s(){
	global $CONN,$togeter,$sel_year,$sel_seme,$yyear_name,$select_subject_weight,$select_subject,$periodical;

	//建立一個暫存的學生上下學期成績資料表
    $creat_table_sql="CREATE TEMPORARY TABLE tmp_score(
    sn int(11) NOT NULL auto_increment,
    student_sn int,
    score float,
    PRIMARY KEY  (sn)
    )";
    $rs=$CONN->Execute($creat_table_sql);
    $CONN->Execute("delete from tmp_score");
	$lastone=count($togeter)-1;
	$b=0;
	while(list($key , $val) = each($togeter)) {
		$where0.=($b=="0")?"and(":"";
		$class_id[$key]=sprintf("%03d_%d_%02d_%02d",$sel_year,1,substr($val,0,-2),substr($val,-2));
		$where0.=" class_id='$class_id[$key]'";
		$where0.=($b==$lastone)?")":" || ";
		//echo $where0."<br>";
		$where1.=($b=="0")?"and(":"";
		$class_id[$key]=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($val,0,-2),substr($val,-2));
		$where1.=" class_id='$class_id[$key]'";
		$where1.=($b==$lastone)?")":" || ";
		$b++;
	}
	$score_semester1="score_semester_".$sel_year."_1";
	$score_semester2="score_semester_".$sel_year."_2";
	//判斷上學期的成績資料表是否存在
	$sql="select *  from $score_semester1  where 1=0";
	$rs1=$CONN->Execute($sql);
	//判斷下學期的成績資料表是否存在
	$sql="select *  from $score_semester2  where 1=0";
	$rs2=$CONN->Execute($sql);
	
	$periodical=$periodical?"test_kind='定期評量' and":'';
	$total_score_subject_count=array();
	

	for($i=0;$i<count($select_subject_weight);$i++){
	$lastone=count($togeter)-1;
		if(!$select_subject[$i]) $select_subject[$i]=0;
        if($sel_seme==1){
            if($rs1){
                $sql="select * from $score_semester1 where $periodical ss_id=$select_subject[$i] $where1";
                $rs=$CONN->Execute($sql) or die($sql);
                $j=0;
                while (!$rs->EOF) {
                    $student_sn[$j]=$rs->fields["student_sn"];
                    $score[$j]=$rs->fields["score"];

                    $test_sort[$j]=$rs->fields["test_sort"];
                    //$total_score_m[$j]=$total_score_m[$j]+($score[$j]*$select_subject_weight[$i]);
                    $total_score[$student_sn[$j]]=$total_score[$student_sn[$j]]+($score[$j]*$select_subject_weight[$i]);

                    $j++;
                    $rs->MoveNext();
                }
            }
         }
         else{
            if($rs1){
                $sql="select *  from $score_semester1  where $periodical ss_id=$select_subject[$i] $where0";
                $rs=$CONN->Execute($sql) or trigger_error($sql);
                $j=0;
                while (!$rs->EOF) {
                    $student_sn[$j]=$rs->fields["student_sn"];
                    $score[$j]=$rs->fields["score"];
                    $sql_tmp="INSERT INTO tmp_score(student_sn,score) values('$student_sn[$j]','$score[$j]')";
                    $rs_tmp=$CONN->Execute($sql_tmp) or trigger_error($sql_tmp) ;
                    $j++;
					
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['counter']++;
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['score']+=$rs->fields["score"];
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['weight']=$select_subject_weight[$i];
					$total_score_subject_count[($rs->fields["student_sn"])]['total']+=($select_subject_weight[$i])*$rs->fields["score"];
					
					$total_score[($rs->fields["student_sn"])]+=($select_subject_weight[$i])*$rs->fields["score"];
					
					
                    $rs->MoveNext();
                }
            }
            if($rs2){
                $sql="select *  from $score_semester2  where $periodical ss_id=$select_subject[$i] $where1";
                $rs=$CONN->Execute($sql) or die($sql);
                $j=0;
                while (!$rs->EOF) {
                    $student_sn[$j]=$rs->fields["student_sn"];
                    $score[$j]=$rs->fields["score"];
                    $sql_tmp="INSERT INTO tmp_score(student_sn,score) values('$student_sn[$j]','$score[$j]')";
                    $rs_tmp=$CONN->Execute($sql_tmp) or die($sql_tmp) ;
                    $j++;
					
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['counter']++;
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['score']+=$rs->fields["score"];
					$total_score_subject_count[($rs->fields["student_sn"])][($rs->fields["ss_id"])]['weight']=$select_subject_weight[$i];
					$total_score_subject_count[($rs->fields["student_sn"])]['total']+=($select_subject_weight[$i])*$rs->fields["score"];
					
					$total_score[($rs->fields["student_sn"])]+=($select_subject_weight[$i])*$rs->fields["score"];
					
                    $rs->MoveNext();
                }
            }
            $sql="select * from tmp_score";
            $rs=$CONN->Execute($sql) or die($sql);
            $j=0;
            while (!$rs->EOF) {
                $student_sn[$j]=$rs->fields["student_sn"];
                $score[$j]=$rs->fields["score"];
                //$total_score[$student_sn[$j]]=$total_score[$student_sn[$j]]+($score[$j]*$select_subject_weight[$i]);  <--有問題的程式碼
                $j++;
                $rs->MoveNext();
            }
            $T=$T+$j;
			//echo $j."<br>";
         }
    }
	if($T=="0") trigger_error("您所選的科目均無成績！",E_USER_ERROR);
	$student_sn=array_unique ($student_sn);
	$k=0;
	foreach($student_sn as $stud_val){
        //檢查該生的學籍狀況
        $sql_c="select * from stud_base where student_sn='$stud_val'";
        //echo $sql_c."<br>";
		$rs_c=$CONN->Execute($sql_c) or die($sql_c) ;
        $stud_study_year[$k]=$rs_c->fields["stud_study_year"];
        $curr_class_num[$k]=$rs_c->fields["curr_class_num"];
        $stud_study_cond[$k]=$rs_c->fields["stud_study_cond"];
        $site= substr($curr_class_num[$k],-2);
        $class= substr($curr_class_num[$k],-4,2);
        $year[$k]= substr($curr_class_num[$k],0,1);
        if(($stud_study_cond[$k]!=0) || ($year[$k]!=$yyear_name) ) continue;
        $student_name[$k]=student_sn_to_stud_name($stud_val);
        //echo $student_sn[$k]."-->".$student_name[$k]."-->".$total_score[$student_sn[$k]]."<br>";
        $t_s[$k]=$total_score[$stud_val];
        $sql="INSERT INTO tmp_stud_compile(student_sn,total_score,c_class) values('$stud_val','$t_s[$k]','$class')";
        $rs=$CONN->Execute($sql) or trigger_error($sql) ;
		$k++;
    }
    $B=school_class_info();
    $sql="select * from tmp_stud_compile order by c_class,total_score DESC";
    $rs=$CONN->Execute($sql) or die($sql) ;
    $j=0;
	$kk=1;
    while (!$rs->EOF) {
        $student_sn[$j]=$rs->fields["student_sn"];
        $total_score[$j]=$rs->fields["total_score"];
		$c_class[$j]=$rs->fields["c_class"];
		//if($j>0 && $c_class[$j]!=$c_class[$j-1]) $j=0;
        $stud_name[$j]=student_sn_to_stud_name($student_sn[$j]);
        $stud_class[$j]=student_sn_to_classinfo($student_sn[$j]);
        //echo $stud_class[$j][3];//性別
        //判斷男女生
        //$jj=$j+1;//名次顯示
		if(($j>0 && $c_class[$j]!=$c_class[$j-1]) || $j==0) {
			$kk=1;
			$jj=$c_class[$j]."-".$kk." {".$total_score[$j]."}";
		}
		else $jj=$c_class[$j]."-".$kk." {".$total_score[$j]."}";
		//$jj=$c_class[$j]."-".($j+1);
        $B_name=$B[$stud_class[$j][0]][$stud_class[$j][1]];
		if($stud_class[$j][3]==1) {
            $boy_sort_name[$c_class[$j]][$kk]=$student_sn[$j];
            $boy_list.="<font color='blue'>".$stud_class[$j][0]."年".$B_name."班 ".sprintf('%02d',$stud_class[$j][2])."號 ".$stud_name[$j]."</font> ( ".$jj." )<br>";
            $boy_sort_form.="<input type='hidden' name='boy_sort_name[$c_class[$j]-$kk]' value='{$boy_sort_name[$c_class[$j]][$kk]}'>";
        }
        if($stud_class[$j][3]==2) {
            $girl_sort_name[$c_class[$j]][$kk]=$student_sn[$j];
            $girl_list.="<font color='magenta'>".$stud_class[$j][0]."年".$B_name."班 ".sprintf('%02d',$stud_class[$j][2])."號 ".$stud_name[$j]."</font> ( ".$jj." )<br>";
			$girl_sort_form.="<input type='hidden' name='girl_sort_name[$c_class[$j]-$kk]' value='{$girl_sort_name[$c_class[$j]][$kk]}'>";
        }

        //$boy_sort_form.="<input type='hidden' name='boy_sort_name[$c_class[$j]-$kk]' value='{$boy_sort_name[$c_class[$j]][$kk]}'>";
        //$girl_sort_form.="<input type='hidden' name='girl_sort_name[$c_class[$j]-$kk]' value='{$girl_sort_name[$c_class[$j]][$kk]}'>";
        $j++;
		$kk++;
        $rs->MoveNext();
    }


    if($rs1 || $rs2) echo "<table cellspacing=1 cellpadding=4 border=0 bgcolor=#27A208><tr bgcolor=#96FF73 align=center><td>男生</td><td>女生</td><td>編班原則</td></tr><tr bgcolor=#ffffff><td valign='top'>$boy_list</td><td valign='top'>$girl_list</td>";
    else echo "學期成績資料表尚未建立，無法進行排名！";


    $B=school_class_info();
    $many_class=count($B[$yyear_name]);
    $yyear_name2=$yyear_name+1;
    $yyear_name_select="<select name='Year_name'><option>$yyear_name2</option><option>$yyear_name</option></select>";
    $compile_class="
	<form name='form3' method='post' action='{$_SERVER['PHP_SELF']}'>
		$boy_sort_form
        $girl_sort_form
        編為 $yyear_name_select 年級<br>
        班群一：<input type='text' name='group[0]' size='3' maxlength='3' value='$many_class'>班<br>
        班群二：<input type='text' name='group[1]' size='3' maxlength='3' value=''>班<br>
        班群三：<input type='text' name='group[2]' size='3' maxlength='3' value=''>班<br>
        班群四：<input type='text' name='group[3]' size='3' maxlength='3' value=''>班<br>
        班群五：<input type='text' name='group[4]' size='3' maxlength='3' value=''>班<br>
        班群六：<input type='text' name='group[5]' size='3' maxlength='3' value=''>班<br>
        班群七：<input type='text' name='group[6]' size='3' maxlength='3' value=''>班<br>
        班群八：<input type='text' name='group[7]' size='3' maxlength='3' value=''>班<br>
        班群九：<input type='text' name='group[8]' size='3' maxlength='3' value=''>班<br>
        班群十：<input type='text' name='group[9]' size='3' maxlength='3' value=''>班<br>
        <input type='submit' name='Submit3' value='開始小S編班'>
    </form>";

    if($rs1 || $rs2) echo "<td valign='top'>$compile_class</td></tr></table>";
}


function small_s2(){
global $CONN,$group,$boy_sort_name,$girl_sort_name,$Year_name;
	//print_r($boy_sort_name);
	//算出共分成幾班
    for($i=0;$i<count($group);$i++){
        $many_class=$many_class+$group[$i];
    }

	reset($boy_sort_name);
    $r=0;
    while(list($key , $val)=each($boy_sort_name)){
        //echo $key."---->".$val."<br>";
        if($val=="") continue;
		$key_arr=explode("-",$key);
		$new_key=intval(sprintf("%02d%02d",$key_arr[1],$key_arr[0]));
		$new_boy[$new_key]=$val;
    }
    reset($girl_sort_name);
    $r=0;
    while(list($key , $val)=each($girl_sort_name)){
        //echo $key."---->".$val."<br>";
        if($val=="") continue;
		$key_arr=explode("-",$key);
		$new_key=intval(sprintf("%02d%02d",$key_arr[1],$key_arr[0]));
		$new_girl[$new_key]=$val;
    }
ksort($new_boy);
ksort($new_girl);
	//產生新的排序
	reset($new_boy);
    $r=0;
    while(list($key , $val)=each($new_boy)){
        //echo $key."---->".$val."<br>";
        if($val=="") continue;
        $n_boy_sort[$r]=$key;
        $n_boy_sort_name[$r]=$val;
        $r++;
    }
    reset($new_girl);
    $r=0;
    while(list($key , $val)=each($new_girl)){
        //echo $key."---->".$val."<br>";
        if($val=="") continue;
        $n_girl_sort[$r]=$key;
        $n_girl_sort_name[$r]=$val;
        $r++;
    }
//區域編班開始
	$boy_A=$n_boy_sort_name;
    $girl_A=$n_girl_sort_name;
    //echo "開始處理男生-----------------";
    $every_class_boys=ceil(count($boy_A)/$many_class);//每班男生人數
    //echo "人數".count($boy_A)."<br>";
    //echo "班級數".$many_class."<br>";
    //echo "每班人數".$every_class_boys."<br>";
    for($i=0;$i<count($group);$i++){
        $a=$i-1;
        if($a<0) $end_stud[$a]="-1";
        $start_stud[$i]=$end_stud[$a]+1; //起始號
        $end_stud[$i]=$start_stud[$i]+($every_class_boys*$group[$i])-1; //結束號
        if($end_stud[$i]>(count($boy_A)-1)) $end_stud[$i]=(count($boy_A)-1);
        for($m=$start_stud[$i];$m<=$end_stud[$i];$m++){
            $b=$i-1;
            if($b<0) $end_class[$b]="-1";
            $start_class[$i]=$end_class[$b]+1;
            $end_class[$i]=$start_class[$i]+$group[$i]-1;
            //echo ">>>>>>>為$boy_A[$m]編班>>>>>>>>";
            for($n=$start_class[$i];$n<=$end_class[$i];$n++){
                //echo ($m%(($end_class[$i]-$start_class[$i]+1)*2))."=".$n."或".(($end_class[$i]-$start_class[$i]+1)*2-$n-1)."<br>";
                if(($m%(($end_class[$i]-$start_class[$i]+1)*2)==($n-$start_class[$i])) || ($m%(($end_class[$i]-$start_class[$i]+1)*2)==(($end_class[$i]-$start_class[$i]+1)*2-($n-$start_class[$i])-1))){
                    $class[$n][]=$boy_A[$m]."_".$n_boy_sort[$m];
                    //echo $n."班==>".$boy_A[$m]."<br>";
                }
            }
        }
        //echo "---------換班群了！--------<br>";
    }

    //echo "開始處理女生-----------------";
    $every_class_girls=ceil(count($girl_A)/$many_class);//每班女生人數
    //echo "人數".count($girl_A)."<br>";
    //echo "班級數".$many_class."<br>";
    //echo "每班人數".$every_class_girls."<br>";
    for($i=0;$i<count($group);$i++){
        $a=$i-1;
        if($a<0) $end_stud[$a]="-1";
        $start_stud[$i]=$end_stud[$a]+1; //起始號
        $end_stud[$i]=$start_stud[$i]+($every_class_girls*$group[$i])-1; //結束號
        if($end_stud[$i]>(count($girl_A)-1)) $end_stud[$i]=(count($girl_A)-1);
        for($m=$start_stud[$i];$m<=$end_stud[$i];$m++){
            $b=$i-1;
            if($b<0) $end_class[$b]="-1";
            $start_class[$i]=$end_class[$b]+1;
            $end_class[$i]=$start_class[$i]+$group[$i]-1;
            //echo ">>>>>>>為$girl_A[$m]編班>>>>>>>>";
            for($n=$end_class[$i];$n>=$start_class[$i];$n--){
                //echo ($m%(($end_class[$i]-$start_class[$i]+1)*2))."=".$n."或".(($end_class[$i]-$start_class[$i]+1)*2-$n-1)."<br>";
                if(($m%(($end_class[$i]-$start_class[$i]+1)*2)==($end_class[$i]-$n)) || ($m%(($end_class[$i]-$start_class[$i]+1)*2)==($end_class[$i]-$start_class[$i]-$start_class[$i]+$n+1))){
                    $class[$n][]=$girl_A[$m]."_".$n_girl_sort[$m];
                    //echo $n."班==>".$girl_A[$m]."<br>";
                }
            }
        }
        //echo "---------換班群了！--------<br>";
    }


//區域編班結束


    //顯示出編班後的結果
    echo "<table cellspacing=2 cellpadding=4 border=0 bgcolor=#27a208 width=100%>";
    for($k=0;$k<$many_class;$k++){
        $BB=school_class_info();
        $kk=$k+1;
        $new_k=$BB[$Year_name][$kk];
        echo "<tr><td bgcolor=#96FF73 width=20%><table width=100%><tr><td></td><td></td><td></td><td></td><td></td></tr><tr align='left'><td width=20%>".($Year_name)."年".$new_k."班</td>";
        for($m=0;$m<count($class[$k]);$m++){
            $values=$class[$k][$m];
            $VA = explode ("_", $values);
            $new_class.="<input type='hidden' name='class[$k][$m]' value='$values'>";
            //echo $class[$k][$m];
            $stud_name[$k]=student_sn_to_stud_name($VA[0]);
            $stud_class[$k]=student_sn_to_classinfo($VA[0]);
            if($stud_class[$k][3]==1) $color="blue";
            else $color="magenta";

            $BB_name=$BB[$stud_class[$k][0]][$stud_class[$k][1]];
            echo "<td bgcolor=#ffffff width=20%>".$stud_class[$k][0]."年".$BB_name."班 ".sprintf('%02d',$stud_class[$k][2])."號 <font color=$color>".$stud_name[$k]."</font> ( ".sprintf("%d-%d",substr($VA[1],-2),substr($VA[1],0,-2))." ) </td>";
            if($m%4==3) echo "</tr><tr><td width=20%></td>";
        }
        $mm=$m+1;
        echo "</tr></table></td></tr>";
    }
    echo "</table>";
    //傳回$class後將編班結果寫入編班的資料表
    //echo $class[0][4];
    echo "
        <form name='form4' method='post' action='./save_compile.php'>
            <input type='hidden' name='year_name' value='$Year_name'>
            <input type='hidden' name='many_class' value='$many_class'>
			<input type='hidden' name='bs' value='small'>
            $new_class
            <input type='submit' name='Submit4' value='儲存編班結果'>
        </form>";
}
?>