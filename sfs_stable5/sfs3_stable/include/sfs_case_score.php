<?php
// $Id: sfs_case_score.php 8895 2016-05-09 03:42:18Z brucelyc $

//給student_sn和ss_id算出該生此科的學期總成績
function seme_score($student_sn,$ss_id,$sel_year="",$sel_seme=""){
  	global $CONN;

	if (!$student_sn) user_error("沒有傳入學生代碼！請檢查！",256);
	if (!$ss_id) user_error("沒有傳入科目！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    if(empty($sel_year))$sel_year = curr_year(); //目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

    $ratio=test_ratio($sel_year,$sel_seme);
    //$score=edu_score($student_sn,$ss_id,$test_sort,$sel_year,$sel_seme);

    //由學生的流水號查出是幾年級的
    //$rs_curr_class_num=$CONN->Execute("select curr_class_num from stud_base where student_sn='$student_sn'") or user_error("讀取失敗！",256);
    $seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
	$rs_seme_class=$CONN->Execute("select seme_class from stud_seme where student_sn='$student_sn' and seme_year_seme='$seme_year_seme' ") or user_error("讀取失敗！",256);
    //echo "select seme_class from stud_seme where student_sn='$student_sn' and seme_year_seme='$seme_year_seme' ";
	$class_year=substr($rs_seme_class->fields['seme_class'],0,-2);

    $rs_times=$CONN->Execute("select performance_test_times from score_setup where year='$sel_year' and semester='$sel_seme' and class_year='$class_year'") or user_error("讀取失敗！",256);

   //echo "select performance_test_times from score_setup where year='$sel_year' and semester='$sel_seme' and class_year='$class_year'";
	$performance_test_times=  $rs_times->fields['performance_test_times'];

    for($j=1;$j<=$performance_test_times;$j++){
        $test_sort=$j;
        $test_sort_A=$j-1;
        //該階段的比率
        $ratio_plus= ($ratio[$class_year][$test_sort_A][0]+$ratio[$class_year][$test_sort_A][1])/100;
        //echo $class_year.$test_sort_A;
		//echo $ratio[$class_year][$test_sort_A][0]." + ".$ratio[$class_year][$test_sort_A][1]." = ".$ratio_plus."<br>";
        //該階段的分數
        //$stage_score=$score[$student_sn][$ss_id][$test_sort]*$ratio_plus;
		$stage_score=edu_score($student_sn,$ss_id,$test_sort,$sel_year,$sel_seme)*$ratio_plus;
		//echo $score[$student_sn][$ss_id][$test_sort]." * ".$ratio_plus." = ".$stage_score."<br>";
        //整學期的分數
        $seme_score=$seme_score+$stage_score;
    }

    return $seme_score;
}

//由資料庫取得總成績
function seme_score2($student_sn,$ss_id,$sel_year="",$sel_seme=""){
	global $CONN;
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	$query = "select ss_score from stud_seme_score where seme_year_seme = '$seme_year_seme' and ss_id=$ss_id and student_sn='$student_sn'";
	//echo $query."<br>";
	$res = $CONN->Execute($query);
	if ($res->EOF)
		return -1;
	else
		return $res->fields[0];
}

//轉換成績甲乙丙丁為結果
function score2str($score="",$class=array(),$rule){
	global $CONN;
	if(empty($score)) return "";

	if ($rule) {
		$r=$rule;
	} else {
		$year=$class[0];
		$seme=$class[1];

		//轉換成等第
		//取考試設定資料
		$sm=&get_all_setup("",$year,$seme,$class[3]);

		//分解規則
		$r=explode("\n",$sm[rule]);
	}
	reset($r);
	while(list($k,$v)=each($r)){
		$rule_a=array();
		$str=explode("_",$v);
		$du_str = (double)$str[2];

		if($str[1]==">="){
		if($score >= $du_str)return $str[0];
		}elseif($str[1]==">"){
			if($score > $du_str)return $str[0];
		}elseif($str[1]=="="){
			if($score == $du_str)return $str[0];
		}elseif($str[1]=="<"){
			if($score < $du_str)return $str[0];
		}elseif($str[1]=="<="){
			if($score <= $du_str)return $str[0];
		}
	}
	$score_name="";
	return $score_name;
}

//傳回轉換成績甲乙丙丁陣列
function score2str_arrs($class=array()){
	global $CONN;

	$year=$class[0];
	$seme=$class[1];

	//轉換成等第
	//取考試設定資料
	$sm=&get_all_setup("",$year,$seme,$class[3]);

	//分解規則
	$r=explode("\n",$sm[rule]);
	reset($r);
	while(list($k,$v)=each($r)){
		$rule_a=array();
		$str=explode("_",$v);
		$du_str=(double)$str[2];
		$rule_all[$k]=$str;
	}
	return $rule_all;
}

//轉換成績甲乙丙丁為結果 並傳回百分數陣列
function &score2str_arr($class=array()){
	global $CONN;
	$year=$class[0];
	$seme=$class[1];

	//轉換成等第
	//取考試設定資料
	$sm=&get_all_setup("",$year,$seme,$class[3]);

	//分解規則
	$r=explode("\n",$sm[rule]);
	$res_arr = array();
	for($i=100;$i>0;$i--){
		reset($r);
		while(list($k,$v)=each($r)){
			$rule_a=array();
			$str=explode("_",$v);
			$du_str = (double)$str[2];

			if($str[1]==">="){
				if($i >= $du_str) {$res_arr[$i] = $str[0]."-- $i 分";break;}
			}elseif($str[1]==">"){
				if($i > $du_str){$res_arr[$i] = $str[0]."-- $i 分";break;}

			}elseif($str[1]=="="){
				if($i == $du_str){$res_arr[$i] = $str[0]."-- $i 分";break;}

			}elseif($str[1]=="<"){
				if($i < $du_str){$res_arr[$i] = $str[0]."-- $i 分";break;}

			}elseif($str[1]=="<="){
				if($i <= $du_str){$res_arr[$i] = $str[0]."-- $i 分";break;}

			}
		}

	}
	return $res_arr;
}



//取得考試設定
function &get_all_setup($setup_id="",$sel_year="",$sel_seme="",$Cyear=""){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if(!empty($setup_id)){
		$where="where setup_id=$setup_id";
	}elseif(!empty($sel_year) and !empty($sel_seme) and !is_null($Cyear)){
		$where="where year = '$sel_year' and semester='$sel_seme' and class_year='$Cyear'";
	}else{
		return false;
	}

	// init $main
	$main=array();

	$sql_select = "select * from score_setup $where";
	//die($sql_select);
	$recordSet=$CONN->Execute($sql_select) or trigger_error("SQL語法錯誤： $sql_select", E_USER_ERROR);
	$main = $recordSet->FetchRow();
	return $main;
}



//您所輸入的是領域還是分科呢
function more_ss($ss_id){
	global $CONN;

	if (!$ss_id) user_error("沒有傳入領域或科目！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    if(empty($sel_year))$sel_year = curr_year(); //目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

	// init $new_ss_id_array
	$new_ss_id_array=array();

    $sql="select * from score_ss where ss_id='$ss_id'";
    $rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
    $i=0;
    while(!$rs->EOF){
        $ss_id[$i]= $rs->fields['ss_id'];
        $year[$i]= $rs->fields['year'];
        $semester[$i]= $rs->fields['semester'];
        $scope_id[$i]= $rs->fields['scope_id'];
        $subject_id[$i]= $rs->fields['subject_id'];
        $enable[$i]= $rs->fields['enable'];
        $rate=$rs->fields['rate'];
        $need_exam[$i]= $rs->fields['need_exam'];
        $print[$i]=  $rs->fields['print'];
        //if subject_id=0, this ss_id is scope if enable=0, have subject
        if($subject_id[$i]!="0") {//本身就是一個分科
            $new_ss_id_array[ss_id]=$ss_id;
            $new_ss_id_array[need_exam]=$need_exam[$i];
            $new_ss_id_array[rate]=$rate[$i];
            $new_ss_id_array[pprint]=$print[$i];
        }
        else{
            if($enable[$i]!="0"){//本身是一個無分科的領域
                $new_ss_id_array[ss_id]=$ss_id;
                $new_ss_id_array[need_exam]=$need_exam[$i];
                $new_ss_id_array[rate]=$rate[$i];
                $new_ss_id_array[pprint]=$print[$i];
            }
            else{//本身是一個有分科的領域
                $rs_sec=$CONN->Execute("select * from score_ss where scope_id='$scope_id[$i]' and subject_id<>0 and enable<>0 ");
                $j=0;
                while(!$rs_sec->EOF){
                    $new_ss_id_array[ss_id][$j]= $rs_sec->fields['ss_id'];
                    $new_ss_id_array[rate][$j]= $rs_sec->fields['rate'];
                    $new_ss_id_array[need_exam][$j]= $rs_sec->fields['need_exam'];
                    $new_ss_id_array[pprint][$j]=  $rs_sec->fields['print'];
                    $j++;
                    $rs_sec->MoveNext();
                }
            }
        }
        $i++;
        $rs->MoveNext();
    }
    return $new_ss_id_array;
}


//取出各學年各學期各年級的學校成績共通設定
function test_ratio($sel_year="",$sel_seme=""){
    global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    if(empty($sel_year))$sel_year = curr_year(); //目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

	// init $test_ratio_all
	$test_ratio_all=array();

    $sql="select * from score_setup where year='$sel_year' and semester='$sel_seme' and enable=1";
    $rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
    $i=0;
    while(!$rs->EOF){
        $class_year[$i]= $rs->fields['class_year'];
        $score_mode[$i]= $rs->fields['score_mode'];
        $test_ratio[$i]= $rs->fields['test_ratio'];
		$Ttest_ratio[$class_year[$i]]=$test_ratio[$i];
        $performance_test_times[$i]=  $rs->fields['performance_test_times'];

		if($score_mode[$i]=="all"){
            $Ttest_ratio[$class_year[$i]]=explode("-",$Ttest_ratio[$class_year[$i]]);
            if ($Ttest_ratio[$class_year[$i]][0]=="") $Ttest_ratio[$class_year[$i]][0]=60;
            if ($Ttest_ratio[$class_year[$i]][1]=="") $Ttest_ratio[$class_year[$i]][1]=40;
            $m1[$i]=$Ttest_ratio[$class_year[$i]][0]/$performance_test_times[$i];
            $m2[$i]=$Ttest_ratio[$class_year[$i]][1]/$performance_test_times[$i];
            $Ttest_ratio[$class_year[$i]]=array("$m1[$i]","$m2[$i]");
            for($j=0;$j<$performance_test_times[$i];$j++){
                $test_ratio_all[$class_year[$i]][$j]=$Ttest_ratio[$class_year[$i]];
				//echo "$class_year[$i] => $j : ".$test_ratio_all[$class_year[$i]][$j][0]."=>".$test_ratio_all[$class_year[$i]][$j][1]."<br>";

            }

        }
        elseif($score_mode[$i]=="severally"){
            //echo $test_ratio[$i]."<br>";
			//echo $Ttest_ratio[$class_year[$i]]."<br>";
			$Ttest_ratio[$class_year[$i]]=explode(",",$Ttest_ratio[$class_year[$i]]);
            for($j=0;$j<count($Ttest_ratio[$class_year[$i]]);$j++){
			//echo "CC".count($Ttest_ratio[$class_year[$i]]);
                $test_ratio_all[$class_year[$i]][$j]=explode("-",$Ttest_ratio[$class_year[$i]][$j]);
				//echo  "$class_year[$i] => $j : ".$test_ratio_all[$class_year[$i]][$j][0]."=>".$test_ratio_all[$class_year[$i]][$j][1]."<br>";
            }

        }
        else{
          	$Ttest_ratio[$class_year[$i]][0]=60;
           	$Ttest_ratio[$class_year[$i]][1]=40;
            for($j=0;$j<$performance_test_times[$i];$j++){
                $test_ratio_all[$class_year[$i]][$j]=$Ttest_ratio[$class_year[$i]];
            }
        }
    $i++;
    $rs->MoveNext();

    }
    return $test_ratio_all;
}

//取出該學年各年級該科目該階段的分數
function test_score($sel_year="",$sel_seme=""){
    global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    if(empty($sel_year))$sel_year = curr_year(); //目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
    //$score_edu_adm=score_edu_adm."_".$sel_year."_".$sel_seme;
	$score_edu_adm=sprintf("score_edu_adm_%d_%d",$sel_year,$sel_seme);
	// init $score_array
	$score_array=array();

    $sql="select * from $score_edu_adm";
    $rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
    $i=0;
    while(!$rs->EOF){
        $edu_adm_id[$i]=$rs->fields['edu_adm_id'];
        $class_id[$i]=$rs->fields['class_id'];
        $student_sn[$i]=$rs->fields['student_sn'];
        $ss_id[$i]=$rs->fields['ss_id'];
        $score[$i]=$rs->fields['score'];
        $test_sort[$i]=$rs->fields['test_sort'];
        $score_array[$student_sn[$i]][$ss_id[$i]][$test_sort[$i]]=$score[$i];
        $i++;
        $rs->MoveNext();
    }

    return $score_array;
}

//給student_sn和ss_id取得文字描述
function seme_score_memo($student_sn,$ss_id,$sel_year="",$sel_seme=""){
  	global $CONN;

	if (!$student_sn) user_error("沒有傳入學生代碼！請檢查！",256);
	if (!$ss_id) user_error("沒有傳入科目！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    if(empty($sel_year))$sel_year = curr_year(); //目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
		//找出評語
	$seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
	$sql="select ss_score_memo from stud_seme_score where seme_year_seme='$seme_year_seme' and ss_id='$ss_id' and student_sn='$student_sn' ";
	//echo $sql;
	$rs=$CONN->Execute($sql);
	$ss_score_memo=$rs->fields['ss_score_memo'];

	return $ss_score_memo;
}

//取得學生日常生活表現檢核表項目
function get_chk_item($sel_year,$sel_seme) {
	global $CONN;

	$ary=array();
	$query="select * from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' order by main,sub";
	$res=$CONN->Execute($query);
	$ary[items]=$res->GetRows();
	$query="select count(item) as num from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' group by main order by main,sub";
	$res=$CONN->Execute($query);
	$ary[nums]=$res->GetRows();

	return $ary;
}

//取得學生日常生活表現檢核表值 mode:value,input
function get_chk_value($student_sn,$sel_year,$sel_seme,$chk_kind_sel="",$mode="value") {
	global $CONN;
	$seme_year_seme = sprintf("%03d",$sel_year).$sel_seme;
	$query="select * from stud_seme_score_nor_chk where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' order by main,sub";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$d[$res->fields[main]][$res->fields[sub]][score]=$res->fields[ms_score];
		$d[$res->fields[main]][$res->fields[sub]][memo]=$res->fields[ms_memo];
		$res->MoveNext();
	}
	$query="select * from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' order by main,sub";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$main=$res->fields[main];
		$sub=$res->fields[sub];
		if ($mode=="input") {
			$temp_input="";
			reset($chk_kind_sel);
			while(list($k,$v)=each($chk_kind_sel)) {
				$checked=($d[$main][$sub][score]==$k)?"checked":"";
				$temp_input.="<input type=\"radio\" id=\"c$k"."_"."$student_sn\" name=\"chk[$student_sn][$main][$sub]\" value=\"$k\" $checked>$v\n";
			}
			$temp[$main][$sub][score]=$temp_input;
		} else {
			$temp[$main][$sub][score]=($d[$main][$sub][score])?$chk_kind_sel[$d[$main][$sub][score]]:"";
		}
		$temp[$main][$sub][memo]=$d[$main][$sub][memo];
		$res->MoveNext();
	}

	return $temp;
}

//取得學生日常行為表現值
function &get_oth_value($stud_id,$sel_year,$sel_seme,$ss_kind_sel='') {
	global $CONN;
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	if ($ss_kind_sel=='')
		$query = "select ss_kind,ss_id,ss_val from stud_seme_score_oth where seme_year_seme='$seme_year_seme' and stud_id='$stud_id'";
	else
		$query = "select ss_kind,ss_id,ss_val from stud_seme_score_oth where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_kind='$ss_kind_sel' ";

	$res = $CONN->Execute($query) or trigger_error("執行錯誤 $query",E_USER_ERROR);
	$temp_arr = array();
	while(!$res->EOF){
		//類別
		$ss_kind = $res->fields[ss_kind];
		//科目
		$ss_id= $res->fields[ss_id];
		//值
		$ss_val = $res->fields[ss_val];
		if ($ss_kind_sel=='')
			$temp_arr[$ss_kind][$ss_id] = $res->fields[ss_val];
		else
			$temp_arr[$ss_id] = $res->fields[ss_val];
		$res->MoveNext();
	}
	return $temp_arr;

}

//合併學生日常生活核檢表文字
function merge_chk_text($sel_year,$sel_seme,$student_sn,$ary=array()) {
	global $CONN;

	$temp_str="";
	$s="";
	$r="";
	while(list($main,$v)=each($ary)) {
		$s=trim($v[0][memo]);
		$r=(strlen($s)>1)?substr($s,-2,2):"";
		if ($r!="。" && $r!="！" && $r!="？")
			$r="。";
		else
			$r="";
		$temp_str.=($s=="")?"":$s.$r;
	}
	$temp_str=addslashes(($temp_str=="")?"":$temp_str);
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$query="select * from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='0'";
	$res=$CONN->Execute($query);
	if ($res->RecordCount()>0)
		$CONN->Execute("update stud_seme_score_nor set ss_score_memo='$temp_str' where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='0'");
	else
		$CONN->Execute("insert into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score_memo) values ('$seme_year_seme','$student_sn','0','$temp_str')");
}

//取得學生日常行為表現值 傳回班級陣列
//$class_id = sprintf("%d%02d",$temp_id_arr[2],$temp_id_arr[3]);
function &get_class_oth_value($class_id,$sel_year,$sel_seme,$ss_id_sel,$ss_kind_sel='') {
	global $CONN,$IS_JHORES;
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	if ($ss_kind_sel=='')
		$query = "select a.stud_id,a.ss_kind,a.ss_val from stud_seme_score_oth a ,stud_base b where a.stud_id=b.stud_id and a.seme_year_seme='$seme_year_seme' and a.ss_id='$ss_id_sel' and b.curr_class_num like '$class_id%'";
	else
		$query = "select a.stud_id,a.ss_kind,a.ss_val from stud_seme_score_oth a ,stud_base b where a.stud_id=b.stud_id and a.seme_year_seme='$seme_year_seme' and a.ss_id='$ss_id_sel' and a.ss_kind='$ss_kind_sel'  and b.curr_class_num like '$class_id%'";
	
	$query=$IS_JHORES?$query." and ($sel_year - b.stud_study_year between 0 and 9)":$query;

	$res = $CONN->Execute($query) or trigger_error("執行錯誤 $query",E_USER_ERROR);
	$temp_arr = array();
	while(!$res->EOF){
		//類別
		$stud_id = $res->fields[stud_id];
		$ss_kind = $res->fields[ss_kind];
		//科目
		$ss_id= $res->fields[ss_id];
		//值
		$ss_val = $res->fields[ss_val];
		if ($ss_kind_sel=='')
			$temp_arr[$ss_kind][$stud_id] = $res->fields[ss_val];
		else
			$temp_arr[$stud_id] = $res->fields[ss_val];
		$res->MoveNext();
	}
	return $temp_arr;

}

//取得學生日常行為文字
function get_nor_text($student_sn,$sel_year,$sel_seme) {
	global $CONN;

	$temp_arr = array();
	$nor_text_arr=nor_text();
	foreach($nor_text_arr as $k=>$v){
		$temp_arr[$v."_文"]="";
	}
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	$query = "select * from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$student_sn'";
	$res = $CONN->Execute($query) or trigger_error("執行錯誤 $query",E_USER_ERROR);
	while(!$res->EOF){
		$temp_arr[$nor_text_arr[$res->fields[ss_id]]."_文"] = $res->fields[ss_score_memo];
		$res->MoveNext();
	}
	return $temp_arr;
}

//取得學生日常表現檢核表文字
function get_chk_text($student_sn,$sel_year,$sel_seme,$chk_kind_arr=array()) {
	global $CONN;

	$temp_arr = array();
	$chk_text_arr=get_chk_item($sel_year,$sel_seme);
	foreach($chk_text_arr['items'] as $k=>$v){
		if ($v['sub']==0) $temp_arr["檢核主項_".$v['main']."_文"]="";
	}
	reset($chk_text_arr['items']);
	foreach($chk_text_arr['items'] as $k=>$v){
		if ($v['sub']) $temp_arr["檢核細項_".$v['main']."_".$v['sub']."_狀況"]="";
	}
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);

	$query = "select * from stud_seme_score_nor_chk where seme_year_seme='$seme_year_seme' and student_sn='$student_sn'";
	$res = $CONN->Execute($query) or trigger_error("執行錯誤 $query",E_USER_ERROR);
	while(!$res->EOF){
		if ($res->fields['sub']==0) {
			$temp_arr["檢核主項_".$res->fields['main']."_文"] = $res->fields['ms_memo'];
		} else {
			$temp_arr["檢核細項_".$res->fields['main']."_".$res->fields['sub']."_狀況"] = $chk_kind_arr[$res->fields['ms_score']];
		}
		$res->MoveNext();
	}
	return $temp_arr;
}

//取得學生出缺席狀況
function get_abs_value($stud_id,$sel_year,$sel_seme,$mode="",$start_date="",$end_date="") {
	global $CONN;

	$abs_kind_arr=stud_abs_kind();
	if ($start_date=="" && $end_date=="" && ($mode=="" || $mode=="標籤_成" || $mode=="貼條")) {
		//取得學期資料
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$t_arr=array();
		$sql_select = "select * from stud_seme_abs where stud_id='$stud_id' and seme_year_seme='$seme_year_seme'";
		$recordSet = $CONN->Execute($sql_select) or trigger_error("SQL 指令錯誤 $sql_select",E_USER_ERROR);
		for ($i=1;$i<7;$i++) $t_arr[$i]=0;
		while (!$recordSet->EOF) {
			$abs_kind=$recordSet->fields['abs_kind'];
			$t_arr[$abs_kind]=$recordSet->fields['abs_days'];
			$recordSet->MoveNext();
		}
		if ($mode=="標籤_成") {
			foreach($t_arr as $id=>$v){
				$temp_arr[$abs_kind_arr[$id]."_成"]=$v;
			}
		}elseif($mode=="貼條"){
			foreach($t_arr as $id=>$v){
				$temp_arr[$abs_kind_arr[$id]]=$v;
			}
		}else {
			$temp_arr=$t_arr;
		}
	} else {
		if ($start_date=="" && $end_date=="") {
			//取得資料庫中之日期資料
			$db_date=curr_year_seme_day($sel_year,$sel_seme);
			$date_str=" and date <= '".$db_date['end']."'";
		} else {
			$date_str=" and date <= '$end_date' and date >= '$start_date'";
		}
		//取得期中資料
		$sql_select="select section, absent_kind from stud_absent where stud_id='$stud_id' and year='$sel_year' and semester='$sel_seme' $date_str";
		$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
		for($i=1;$i<=6;$i++) $t_arr[$i]=0;
		while(list($section,$absent_kind)=$recordSet->FetchRow()){
			if($mode=="種類"||$mode=="標籤"){
				$n=($section=="allday")?7:1;
				//事假
				if(($absent_kind=="事假")&&($section!="uf")&&($section!="df")) $t_arr[1]+=$n;
				//病假
				if(($absent_kind=="病假")&&($section!="uf")&&($section!="df")) $t_arr[2]+=$n;
				//曠課
				if(($absent_kind=="曠課")&&($section!="uf")&&($section!="df")) $t_arr[3]+=$n;
				//集會(只有曠課的才計算)
				if(($absent_kind=="曠課")&&(($section=="uf")||($section=="df"))) $t_arr[4]+=$n;
				//公假
				if(($absent_kind=="公假")&&($section!="uf")&&($section!="df")) $t_arr[5]+=$n;
				//其他
				if((($absent_kind=="喪假")||($absent_kind=="不可抗力"))&&(($section!="uf")&&($section!="df"))) $t_arr[6]+=$n;
			} else {
				//按節數統計
				$t_arr[$section]+=1;
			}
		}
		if ($mode=="標籤") {
			foreach($t_arr as $id=>$v){
				$temp_arr[$abs_kind_arr[$id]]=$v;
			}
		} else {
			$temp_arr=$t_arr;
		}
	}
	return $temp_arr;
}

//$class_id 是專門給自訂成績單模組使用的
//取得學生日常生活表現分數及導師評語建議
//$mode=1 取得團體活動、公共服務、特殊表現等文字
function get_nor_value($student_sn,$sel_year,$sel_seme,$class_id="",$mode=0) {
	global $CONN;
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	if ($mode) {
		$ss_str="order by ss_id";
	} else {
		$ss_str="and ss_id=0";
	}
	$query="select ss_id,ss_score,ss_score_memo from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' $ss_str";
	$res = $CONN->Execute($query) or trigger_error("執行錯誤 $query",E_USER_ERROR);
	$temp_arr = array();
	if ($mode) {
		while(!$res->EOF) {
			$temp_arr[ss_score][$res->fields['ss_id']]=$res->fields['ss_score'];
			$temp_arr[ss_score_memo][$res->fields['ss_id']]=$res->fields['ss_score_memo'];
			$res->MoveNext();
		}
	} else {
		if ($class_id) {
			$class=class_id_2_old($class_id);
			$score=intval($res->fields[ss_score]);
			$score_txt=score2str($score,$class);

			$temp_arr['導師評語及建議']=$res->fields[ss_score_memo];
			$temp_arr['分數等第']=$score."-".$score_txt;
			$temp_arr['表現等第']=$score_txt;
			$temp_arr['表現分數']=$score;
		} else {
			$temp_arr[ss_score]=$res->fields[ss_score];
			$temp_arr[ss_score_memo]=$res->fields[ss_score_memo];
		}
	}

	return $temp_arr;
}

//取得將懲記錄
function get_reward_value($stud_id,$sel_year,$sel_seme,$mode="") {
	global $CONN;
	$reward_kind_arr=stud_rep_kind();
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$query = "select * from stud_seme_rew where stud_id='$stud_id' and seme_year_seme='$seme_year_seme'";
	$res = $CONN->Execute($query) or trigger_error("SQL 指令錯誤 $query",E_USER_ERROR);
	$t_arr=array();
	for ($i=1;$i<7;$i++) $t_arr[$i]=0;
	while(!$res->EOF){
		$sr_kind_id=$res->fields['sr_kind_id'];
		$t_arr[$sr_kind_id]=$res->fields['sr_num'];
		$res->MoveNext();
	}
	if ($mode=="標籤_成") {
		foreach($t_arr as $id=>$v){
			$temp_arr[$reward_kind_arr[$id]."_成"]=$v;
		}
	} else {
		$temp_arr=$t_arr;
	}
	return $temp_arr;
}

//給student_sn和ss_id算出該生此科每一階段要存放在edu_adm_score的成績（包含平時成績和定期考查成績）
function edu_score($student_sn,$ss_id,$test_sort,$sel_year="",$sel_seme=""){
 	global $CONN;

	if (!$student_sn) user_error("沒有傳入學生代碼！請檢查！",256);
	if (!$ss_id) user_error("沒有傳入科目！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    if(empty($sel_year))$sel_year = curr_year(); //目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

    $seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
	$rs_seme_class=$CONN->Execute("select seme_class from stud_seme where student_sn='$student_sn' and seme_year_seme='$seme_year_seme' ") or user_error("讀取失敗！",256);
	$class_year=substr($rs_seme_class->fields['seme_class'],0,-2);


	$score_semester=sprintf("score_semester_%d_%d",$sel_year,$sel_seme);
	$sql="select score,test_kind from $score_semester where student_sn='$student_sn' and  test_sort='$test_sort' and ss_id='$ss_id' ";
	$rs=$CONN->Execute($sql) or trigger_error("$sql",256);
	$i=0;
	while(!$rs->EOF){
		$score[$i]=$rs->fields['score'];
		$test_kind[$i]=$rs->fields['test_kind'];
		$Sscore[$test_kind[$i]]=$score[$i];
		$rs->MoveNext();
		$i++;
	}
	$test_radio=test_ratio($sel_year,$sel_seme);
	$test_sort_p=$test_sort-1;
	$total_score=0;
	if($test_sort==255){
		$total_score=$Sscore['全學期'];
	}
	else{
		if($Sscore['定期評量']>=0 )  {
			$total_score=$total_score+($Sscore['定期評量'])*($test_radio[$class_year][$test_sort_p][0]/($test_radio[$class_year][$test_sort_p][0]+$test_radio[$class_year][$test_sort_p][1]));
			//echo "定期評量:".$total_score."<br>";
		}
		if($Sscore['平時成績']>=0 )  {
			$total_score=$total_score+($Sscore['平時成績'])*($test_radio[$class_year][$test_sort_p][1]/($test_radio[$class_year][$test_sort_p][0]+$test_radio[$class_year][$test_sort_p][1]));
			//echo "平時成績:".$total_score."<br>";
		}
	}
	$total_score=round($total_score,2);

	return $total_score;
}

//由class_id找出該班需月考的科目
function class_id2subject($class_id){
	global $CONN;
	//分解class_id
	$class_id_array=explode("_",$class_id);
	$year=intval($class_id_array[0]);
	$semester=intval($class_id_array[1]);
	$class_year=intval($class_id_array[2]);
//	092_2_06_01
	$class_id_t = sprintf("%03d_%d_%02d_%02d",$class_id_array[0],$class_id_array[1],$class_id_array[2],$class_id_array[3]);
	$sql="select * from score_ss where enable=1 and need_exam=1 and print=1 and year='$year' and semester='$semester' and class_year='$class_year' and class_id='$class_id_t' order by sort,sub_sort";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	if ($rs->RecordCount() ==0){
		$sql="select * from score_ss where enable=1 and need_exam=1 and print=1 and year='$year' and semester='$semester' and class_year='$class_year' and class_id='' order by sort,sub_sort";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	}
	$i=0;
	while(!$rs->EOF){
		$ss_id[$i]=$rs->fields['ss_id'];
		$scope_id[$i]=$rs->fields['scope_id'];
		$subject_id[$i]=$rs->fields['subject_id'];
		$real_subject[$i]=($subject_id[$i]!="0")?"$subject_id[$i]":"$scope_id[$i]";
			//轉成中文名稱
			$sql2="select subject_name from score_subject where subject_id='$real_subject[$i]' and enable=1";
			$rs2=$CONN->Execute($sql2) or trigger_error($sql2,256);
			$subject_name[$i]=$rs2->fields['subject_name'];

		$SS[$ss_id[$i]]=$subject_name[$i];

 		$rs->MoveNext();
		$i++;
	}
	//傳回ss_id（課程代號）陣列,ss_name（課程名稱）陣列
	return $SS;
}

//由student_sn,ss_id,tetst_kind,test_sort取出成績
function score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort="1"){
	global $CONN;
	if($curr_year=="") $curr_year = curr_year();
	if($curr_seme=="") $curr_seme = curr_seme();
	$curr_year=intval($curr_year);
	$score_semester="score_semester_".$curr_year."_".$curr_seme;
	$sql="select score from $score_semester where student_sn='$student_sn' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$score=$rs->fields['score'];
	return $score;
}


//該班目前的成績階段選單
function test_sort_select($curr_year,$curr_seme,$class_num){
	global $CONN,$test_sort;
	if($curr_year=="") $curr_year = curr_year();
	if($curr_seme=="") $curr_seme = curr_seme();
	$curr_year=intval($curr_year);
	$class_year=substr($class_num,0,-2);
	$sql="select  performance_test_times from score_setup where enable=1 and year='$curr_year' and semester='$curr_seme' and class_year='$class_year' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$times=$rs->fields['performance_test_times'];
	$option="<option>請選擇階段</option>";
	for($i=1;$i<=$times;$i++){
		$selected[$i]=($i==$test_sort)?"selected":"";
		$option.="<option value='$i' $selected[$i]>第 $i 階段</option>";
	}
	return $option;
}

//陣列（數字的陣列）中的某一數值在陣列中排行第幾
function sort_sort($num,$num_array){
	//echo count($num_array)."<br>";
	if(!in_array($num, $num_array)) return 0;
	sort($num_array,SORT_NUMERIC);
	//降冪
	$num_array=array_reverse($num_array);
	foreach($num_array as $key => $value){
		//echo $key."///".$value."<br>";
		$order=$key+1;
		if($num==$value) return $order;
	}
}

//由ss_id取出該科加權數
function subj_wet($ss_id=""){
	global $CONN;
	if($ss_id=="") return 0;
	$sql="select rate from score_ss where ss_id='$ss_id' and enable=1";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$wet=$rs->fields['rate'];
	return $wet;
}

// 取得成績檔
function &get_score_value($stud_id,$student_sn,$class_id,$oth_data,$sel_year,$sel_seme,$stage="") {
	global $CONN,$oth_arr_score,$oth_arr_score_2,$style_ss_num;
	$class=class_id_2_old($class_id);
	// 取得努力程度文字敘述
//	$arr_1 = sfs_text("努力程度");

	// 取得課程每週時數
	$ss_num_arr = get_ss_num_arr($class_id);
	
	if ($style_ss_num==1)//以課程設定每周節數為主
	{
	$ss_num_arr =get_ss_num_arr_from_score_ss($class_id);
	}
	
	if ($style_ss_num==2)//以課程設的加權數作為節數
	{
	$ss_num_arr =get_ss_num_arr_from_score_ss_rate($class_id);
	}
	
	if ($stage=="") {
		// 學期成績
		// 取得本年級的課程陣列
		$ss_name_arr = &get_ss_name_arr($class,"短");
		
		// 取得學習成就
		$ss_score_arr =get_ss_score_arr($class,$student_sn);
	} else {
		// 階段成績
		// 取得本年級的課程陣列
		$ss_name_arr = &get_ss_name_arr($class);
		// 取得學習成就
		$ss_score_arr =get_ss_score($student_sn,$sel_year,$sel_seme,$stage);
	}

//計算平均
$sectors=0;
foreach($ss_score_arr as $key=>$value){
	$ss_score_sum['定期評量']+=$value['定期評量']*$ss_num_arr[$key];
	$ss_score_sum['平時成績']+=$value['平時成績']*$ss_num_arr[$key];
	
	$sectors+=$ss_num_arr[$key];	

}
$ss_score_sum['定期評量']=$ss_score_sum['定期評量']/$sectors;
$ss_score_sum['平時成績']=$ss_score_sum['平時成績']/$sectors;

$ss_score_avg['平均']['定期評量']=round($ss_score_sum['定期評量'],0);
$ss_score_avg['平均']['平時成績']=round($ss_score_sum['平時成績'],0);

	$temp_str = "<table bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"4\" width=\"100%\">
	<tr bgcolor=\"#c4d9ff\">
	<td>科目</td>";
	if ($stage=="") {
		$temp_str.="
			<td align=\"center\">每週節數</td>
			<td align=\"center\">努力程度</td>
			<td align=\"center\">學習成就</td>
			<td align=\"center\">學習描述文字說明</td>
			</tr>";
		$ss_sql_select = "select link_ss,count(ss_id) as cc from score_ss where enable='1' and class_id='$class_id' and need_exam='1' group by link_ss order by sort,sub_sort";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
		if ($ss_recordSet->RecordCount() ==0){
			$ss_sql_select = "select link_ss,count(ss_id) from score_ss where enable='1' and year='$class[0]' and semester='$class[1]' and need_exam='1' and class_id='' and class_year='$class[3]' group by link_ss order by sort,sub_sort";
			$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
		}
		$hidden_ss_id='';
		$temp_9_arr = array();
		while (!$ss_recordSet->EOF) {
			$link_ss=$ss_recordSet->fields['link_ss'];
			if ($link_ss!="彈性課程") $temp_9_arr[$link_ss][num]=$ss_recordSet->fields['cc'];
			$ss_recordSet->MoveNext();
		}
		$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and class_id='$class_id' and need_exam='1' order by sort,sub_sort";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
		if ($ss_recordSet->RecordCount() ==0){
			$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and year='$class[0]' and semester='$class[1]' and class_id='' and need_exam='1' and class_year='$class[3]' order by sort,sub_sort";
			$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
		}
		while ($SS=$ss_recordSet->FetchRow()) {
			$ss_id=$SS[ss_id];
			$link_ss=$SS[link_ss];
			$rate=$SS[rate];
			$ss_name= $ss_name_arr[$ss_id];
			$sub_link=0;
			if ($link_ss=="彈性課程" or $link_ss=='') {
				$link_ss="彈性課程-".$ss_name;
				$sub_link=1;
			}
			$temp_9_arr[$link_ss][ss_hours] += $ss_num_arr[$ss_id];
			$temp_9_arr[$link_ss][ss_score] += $ss_score_arr[$ss_id][ss_score]*$rate;
			$temp_9_arr[$link_ss][rate] += $rate;
			$oth_data_rate = 0;
			$temp_9_arr[$link_ss][oth_data] += $oth_arr_score[$oth_data["努力程度"]["$ss_id"]]*$rate;
			if ($ss_score_arr[$ss_id][ss_score_memo]<>'') {
				if ($sub_link == 0 && $temp_9_arr[$link_ss][num]>1) $temp_9_arr[$link_ss][ss_score_memo] .= "$ss_name :";
				$temp_9_arr[$link_ss][ss_score_memo] .= $ss_score_arr[$ss_id][ss_score_memo]."<br/>";
			}
			//if ($temp_sel=='')
			//	$temp_sel = "--";
			//將ss_id 放在 hidden
			$hidden_ss_id .="$ss_id,";
		}
		reset($temp_9_arr);
		while(list($id,$val)=each($temp_9_arr)){
			if($id){
				$score_temp = $val[ss_score]/$val[rate];
				$score_oth = $oth_arr_score_2[round($val[oth_data]/$val[rate],0)];
				if ($score_temp>0)
					$score_temp_str = "<font color=#cccccc>(".round($score_temp,0).")</font>";
				else
					$score_temp_str ='';
				$score_memo = score2str($score_temp,$class);
				$temp_str .= "<tr bgcolor='white'>
					<td>$id</td>
					<td align='center'>$val[ss_hours]節</td>
					<td nowrap align='center'>".$score_oth."</td>
					<td align='center'>$score_memo $score_temp_str</td>
					<td>".substr($val[ss_score_memo],0,-5)."</td>
					</tr>";
			}
		}
	} else {
		$temp_str.="
			<td align=\"center\">每週節數</td>
			<td align=\"center\">定期考查</td>
			<td align=\"center\">平時成績</td>
			</tr>";
		$ss_sql_select = "select ss_id from score_ss where enable='1' and class_id='$class_id' and need_exam='1' and print='1' order by sort,sub_sort";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
		if ($ss_recordSet->RecordCount() ==0){
			$ss_sql_select = "select ss_id from score_ss where enable='1' and year='$class[0]' and semester='$class[1]' and class_year='$class[3]' and class_id='' and need_exam='1' and print='1' order by sort,sub_sort";
			$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
		}

		while ($SS=$ss_recordSet->FetchRow()) {
			$ss_id=$SS[ss_id];
			$ss_name= $ss_name_arr[$ss_id];

			if ($ss_score_arr[$ss_id]['定期評量']=='') $ss_score_arr[$ss_id]['定期評量'] = "--";
			if ($ss_score_arr[$ss_id]['平時成績']=="") $ss_score_arr[$ss_id]['平時成績']="--";
			$temp_str .= "<tr bgcolor='white'>
				<td>$ss_name</td>
				<td align='center'>$ss_num_arr[$ss_id]節</td>
				<td nowrap align='center'>".round($ss_score_arr[$ss_id]['定期評量'],0)."</td>
				<td align='center'>".round($ss_score_arr[$ss_id]['平時成績'],0)."</td>
				</tr>";
		}
		$temp_str .= "<tr align='center' bgcolor='#ffdddd'><td colspan=2>加權平均</td><td>{$ss_score_avg['平均']['定期評量']}</td><td>{$ss_score_avg['平均']['平時成績']}</td></tr>";
	}
	$hidden_str=($hidden_ss_id)?"<input type=\"hidden\" name=\"hidden_ss_id\" value=\"$hidden_ss_id\">\n":"";
	return $temp_str.$hidden_str;
}

// $oth_data -- 與科目無關資料
// $abs_data -- 缺曠課記錄
// $reward_data -- 將懲記錄
// $score_data -- 成績記錄
// $mode 0:不含檢核表, 1:含檢核表
function &html2code2($class,$sel_year,$sel_seme,$oth_data,$nor_data,$abs_data,$reward_data,$score_data,$student_sn,$mode=0) {
	global $SFS_PATH_HTML,$TOTAL_DAYS,$CONN,$IS_JHORES,$is_summary_input;
	$arr_1 = sfs_text("日常行為表現");
	$arr_2 = sfs_text("團體活動表現");
	$arr_3 = sfs_text("公共服務表現");
	$arr_4 = sfs_text("校外特殊表現");
	//假別
	$abs_kind_arr = stud_abs_kind();
	//獎懲
	$rep_kind_arr = stud_rep_kind();
	$sel1 = new drop_select();
	$sel1->use_val_as_key = true;
	for($i=1;$i<=4;$i++) {
		if ($IS_JHORES==0&&$_SESSION[session_who]=="教師") {
			$ss_name = "a_$i";
			$sel1->s_name=$ss_name;
			$sel1->arr= ${"arr_$i"};
			$sel1->id=$oth_data['生活表現評量'][$i];
			${"sel_str_$i"} = $sel1->get_select();
		} else {
			${"sel_str_$i"} = $oth_data['生活表現評量'][$i];
		}
	}
	//日常生活表現評量
	if ($IS_JHORES==0&&$_SESSION[session_who]=="教師") {
		$score_str_arr = &score2str_arr($class);
		$sel1->s_name="nor_score";
		$sel1->id = $nor_data[ss_score];
		$sel1->top_option="選擇等第";
		$sel1->use_val_as_key = false;
		$sel1->arr = $score_str_arr;
		$nor_score_sel = $sel1->get_select();
	} else {
		$final_nor_score=$nor_data[ss_score];
		$final_nor=score2str($final_nor_score,$class);
		$score_str_arr = &score2str_arr($class);
	}


	$temp_str ="
	<table cellspacing=\"0\" cellpadding=\"0\">
	<tr>
	<td>
	<table bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"4\" width=\"100%\">
	";

	if ($mode==1) {
		$temp_str .="
		<tr bgcolor=\"#c4d9ff\">
		<td colspan=\"13\" align=\"center\" nowrap>日常生活表現</td>
		</tr>
		<tr bgcolor=\"white\">
		<td nowrap>生活行為</td><td colspan=\"12\"><textarea name='nor_score_memo' id='nor_score_memo'  rows=5 style='width: 100%'>".$nor_data[ss_score_memo][0]."</textarea><br><font color=\"red\" size=\"2\">※若本欄內容與檢核表建議文字不同，請到檢核表重新儲存一次即可。</font></td>
		</tr>
		<tr bgcolor=\"white\">
		<td nowrap>團體活動</td><td colspan=\"12\">".$nor_data[ss_score_memo][1]."</td>
		</tr>
		<tr bgcolor=\"white\">
		<td nowrap>公共服務</td><td colspan=\"12\">校內服務：".$nor_data[ss_score_memo][2]."<br>社區服務：".$nor_data[ss_score_memo][3]."</td>
		</tr>
		<tr bgcolor=\"white\">
		<td nowrap>特殊表現</td><td colspan=\"12\">校內特殊表現：".$nor_data[ss_score_memo][4]."<br>校外特殊表現：".$nor_data[ss_score_memo][5]."</td>
		</tr>
		";
	} else {
		$temp_str .="
		<tr bgcolor=\"white\">
		<td colspan=\"13\" nowrap>日常生活表現評量</td>
		</tr>
		<tr bgcolor=\"white\">
		<td nowrap>日常行為表現</td>
		<td colspan=\"3\">$sel_str_1
		</td>
		<td colspan=\"8\" nowrap>導師評語及建議";

		if ($IS_JHORES==0&&$_SESSION[session_who]=="教師") $temp_str.="<img src='$SFS_PATH_HTML/images/comment1.png' border='0' title='批次匯入評語' onclick=\"return OpenWindow2('批次編修評語')\">";

		$temp_str.="
		</td>
		<td nowrap>等第</td>
		</tr>
		<tr bgcolor=\"white\">
		<td nowrap>團體活動表現</td>

		<td colspan=\"3\">$sel_str_2
		</td>";
		if ($IS_JHORES==0&&$_SESSION[session_who]=="教師") {
			$temp_str.="
				<td rowspan=\"3\" colspan=\"8\"><img src='$SFS_PATH_HTML/images/comment.png' width=16 height=16 border=0 title='詞庫輸入' align='left' name='nor_score_memo' value='nor_score_memo_s' onClick=\"return OpenWindow('nor_score_memo')\"><textarea name='nor_score_memo' id='nor_score_memo' cols=30 rows=5>$nor_data[ss_score_memo]</textarea></td>
				<td rowspan=\"3\" colspan=\"1\">$nor_score_sel</td>";
		} else {
			$temp_str.="
				<td rowspan=\"3\" colspan=\"8\">".$nor_data[ss_score_memo]."</td>
				<td rowspan=\"3\" colspan=\"1\" align='center'>$final_nor</td>";
		}
		$temp_str.="
		</tr>

		<tr bgcolor=\"white\">
		<td nowrap>公共服務</td>
		<td colspan=\"3\">$sel_str_3
		</td>

		</tr>
		<tr bgcolor=\"white\">
		<td nowrap>校外特殊表現</td>
		<td colspan=\"3\">$sel_str_4
		</td>
		</tr>";
	}
	if ($IS_JHORES==0) {
		$temp_str.="
			<tr bgcolor=\"white\">
			<td nowrap>上課總日數</td>
			<td colspan=\"13\">$TOTAL_DAYS 天
			</td>
			</tr>";
	}

if($is_summary_input=='y' || $IS_JHORES!=0) {
	$temp_str.="
	<tr bgcolor=\"white\">";

	if ($IS_JHORES!=0)
	$temp_str.="
	<td nowrap>學生缺席情況</td>";
	else
	$temp_str.="
	<td nowrap>學生缺席情況，<br/><a href=\"absent.php\">填寫勤惰記錄</a> $summary_input<br>
	</td>";
	reset($abs_kind_arr);
	while(list($id,$val)=each($abs_kind_arr)){
		$ttt =($IS_JHORES==0)?"天數":"節數";
		if ($id==4) $ttt= "次數";
//		if ($IS_JHORES==0) {
//			if ($_SESSION[session_who]=="教師")
//				$temp_str .="<td nowrap>$val<br>$ttt</td>\n<td><input type='text' name='abs_$id' id='abs_$id' value='".$abs_data[$id]."' size=5 ></td>\n";
//			else
//				$temp_str .="<td nowrap>$val<br>$ttt</td>\n<td>$abs_data[$id]</td>\n";
//		} else
			$temp_str .="<td nowrap>$val<br>$ttt</td>\n<td>$abs_data[$id]</td>\n";
	}
}

	if ($IS_JHORES!=0) {
		$temp_str.= "</tr>
		<tr bgcolor=\"white\">
		<td nowrap>獎懲<br>
		</td>";
		//列出獎懲
		reset($rep_kind_arr);
		while(list($id,$val)= each($rep_kind_arr)) $temp_str .= "<td nowrap>$val<br>次數</td>\n<td>$reward_data[$id]</td>\n";
	}

	if ($mode==1) {
		$temp_str.= "</tr>
		<tr bgcolor=\"#c4d9ff\">
		<td colspan=\"13\" align=\"center\" nowrap>學習領域評量</td>";
	} else {
		$temp_str.= "</tr>
		<tr bgcolor=\"white\">
		<td nowrap>其他</td>";

		if  ($IS_JHORES==0&&$_SESSION[session_who]=="教師")
			$temp_str.= "<td colspan=\"12\"><input type='text' name='oth_rep' id='oth_rep' value='".$oth_data['其他設定'][0]."'></td>";
		else
			$temp_str.= "<td colspan=\"12\">".$oth_data['其他設定'][0]."</td>";
	}

	$temp_str.= "
	</tr>
	</table>
	</td></tr>
	<tr><td>
	$score_data
	</td></tr>
	</table>
	</td>
	</tr>
	</table>";
	return $temp_str;
}

//成績表
class score_chart {

	var $upper_title;	//表格最上面的標題
	var $kind=array(); 	//成績種類 : 平時成績,定期考查,全學期
	var $sort; 		//階段別 : 1,2,3...
	var $score_arr=array(); //成績內容 : $score_arr[$student_sn][$ss_id][$sort][$kind]
//	var $score_cal=array(); //計算項目 : $score_cal[$student_sn][sum]	總成績
				//                                  [num]	總科目數
				//                                  [avg]	平均
//	var $subject_cal=array(); 計算項目 : $subject_cal[$ss_id][num]		各科目人數
				//                               [avg]		各科目平均
				//                               [std]		各科目標準差
	var $col_arr=array(); 	//標題列內容 : $col_arr[$ss_id][name]		科目名稱
				//                             [ratio]		配分比例
	var $row_arr=array();	//列頭內容 : $row_arr[$site_num][sn]		學生序號
				//                              [name]		學生姓名
				//                              [class_site]	班級座號(用於分組) 5-10 五班十號
	var $left_col_arr=array();	//科目之前的標題內容
	var $right_col_arr=array();	//科目之後的標題內容
	var $title_bgcolor="'#FDC3F5'";
	var $col_bgcolor=array("0"=>"'#B8FF91'","1"=>"'#CFFFC4'","2"=>"'#B4BED3'","3"=>"'#CBD6ED'","4"=>"'#D8E4FD'");
	var $ratio_enable=false;

	function score_chart() {
		$this->left_col_arr[][name]="座號";
		$this->left_col_arr[][name]="姓名";
	}

	function show_header($mode) {
		if ($mode=="output") {
			echo '	<HTML><HEAD><TITLE>'.$this->upper_title.'</TITLE>
				<META http-equiv=Content-Language content=zh-tw>
				<META http-equiv=Content-Type content="text/html; charset=big5">
				<BODY>
				<P align=center><FONT size=4>'.$this->upper_title.'</FONT></P>
				<TABLE style="BORDER-COLLAPSE: collapse" borderColor=#111111 cellSpacing=0 cellPadding=0 width=610 align=center border=0>
				<TBODY>
				<tr>';
			$i=1;
			while(list($k,$v)=each($this->left_col_arr)) {
				$l_border=($i==1)?"1.5":"0.75";
				echo '<td style="border-style:solid; border-width:1.5pt 0.75pt 0.75pt '.$l_border.'pt;" align="center" width="40">'.$this->left_col_arr[$k][name].'</td>';
				$i++;
			}
			while(list($k,$v)=each($this->col_arr)) {
				$ratio_str=($this->ratio_enable && $this->col_arr[$k][ratio])?"<br>(x".$this->col_arr[$k][ratio].")":"";
				echo '<td style="border-style:solid; border-width:1.5pt 0.75pt 0.75pt 0.75pt;" align="center" width="40">'.$this->col_arr[$k][name].$ratio_str.'</td>';
			}
			while(list($k,$v)=each($this->right_col_arr)) {
				echo '<td style="border-style:solid; border-width:1.5pt 1.5pt 0.75pt 1.5pt;" align="center" width="40">'.$this->right_col_arr[$k][name].'</td>';
			}
		} elseif ($mode=="file_out") {
			header("Content-disposition: attachment; filename=file_out.csv");
			header("Content-type: application/octetstream");
			header("Pragma: no-cache");
			header("Expires: 0");
			while(list($k,$v)=each($this->left_col_arr)) {
				echo $this->left_col_arr[$k][name].",";
			}
			while(list($k,$v)=each($this->col_arr)) {
				$ratio_str=($this->ratio_enable && $this->col_arr[$k][ratio])?"<br>(x".$this->col_arr[$k][ratio].")":"";
				echo $this->col_arr[$k][name].",";
			}
			$i=1;
			while(list($k,$v)=each($this->right_col_arr)) {
				$j=count($this->right_col_arr);
				echo $this->right_col_arr[$k][name];
				if ($i==$j)
					echo "\r\n";
				else
					echo ",";
				$i++;
			}
		} else {
			echo "	<table bgcolor='#0000ff' border='0' cellpadding='6' cellspacing='1'>\n
				<tr bgcolor=$this->title_bgcolor>";
			if ($this->upper_title) {
				$cols=count($this->left_col_arr)+count($this->col_arr)+count($this->right_col_arr);
				echo "<td colspan='$cols' align='center'>$this->upper_title</td></tr><tr bgcolor=$this->title_bgcolor>";
			}
			while(list($k,$v)=each($this->left_col_arr)) {
				echo "<td>".$this->left_col_arr[$k][name]."</td>";
			}
			while(list($k,$v)=each($this->col_arr)) {
				$ratio_str=($this->ratio_enable && $this->col_arr[$k][ratio])?"<br><font color='#ff0000'>(x".$this->col_arr[$k][ratio].")</font>":"";
				echo "<td align='center'>".$this->col_arr[$k][name].$ratio_str."</td>";
			}
			while(list($k,$v)=each($this->right_col_arr)) {
				echo "<td>".$this->right_col_arr[$k][name]."</td>";
			}
			echo "</td>\n";
		}
	}

	function summary() {
		$this->right_col_arr[sum][name]="總分";
		$this->is_summary=true;
		reset ($this->score_arr);
		while(list($student_sn,$z)=each($this->score_arr)) {
			reset($z);
			while(list($ss_id,$y)=each($z)) {
				reset($y);
				while(list($sort,$x)=each($y)) {
					reset($x);
					while(list($kind,$s)=each($x)) {
						$ratio=($this->ratio_enable)?$this->col_arr[$ss_id][ratio]:1;
						$this->score_cal[$student_sn][num]+=$ratio;
						$this->score_cal[$student_sn][sum]+=$s*$ratio;
						$this->score_subject[$ss_id][num]++;
						$this->score_subject[$ss_id][sum]+=$s;
						$this->sort_no[$student_sn]=$this->score_cal[$student_sn][sum];
						$this->subject_cal[$ss_id]["s".floor($s/10)]++;
					}
				}
			}
			//計算總人數及總分加總
			$this->score_cal[avg][num]++;
			$this->score_cal[avg][sum]+=$this->score_cal[$student_sn][sum];
		}
		//計算總分平均
		$this->score_cal[avg][sum]=number_format($this->score_cal[avg][sum]/$this->score_cal[avg][num],$precision);
		//讓各科標準差顯示時不會出現錯誤
		$this->score_cal[std][sum]=" ";
	}

	function average_one() {
		//如果還沒計算成績，就先計算
		if ($this->is_summary!=true) {
			$this->summary();
			$out=array_pop($this->right_col_arr);
		}
		$this->right_col_arr[avg][name]="平均";
		$this->is_average_one=true;
		reset ($this->score_cal);
		while(list($student_sn,$v)=each($this->score_cal)) {
			$this->score_cal[$student_sn][avg]=number_format($this->score_cal[$student_sn][sum]/$this->score_cal[$student_sn][num],$precision);
		}
		$this->score_cal[avg][avg]=number_format($this->score_cal[avg][sum]/count($this->col_arr),$precision);
		//讓各科標準差顯示時不會出現錯誤
		$this->score_cal[std][avg]=" ";
	}

	function sorting() {
		$this->right_col_arr[sort][name]="名次";
		$this->is_sorting=true;
		arsort($this->sort_no);
		$total=count($this->sort_no);
		$i=1;
		reset ($this->sort_no);
		while(list($student_sn,$v)=each($this->sort_no)) {
			if ($this->score_cal[$student_sn][sum]!=$pre_score) $j=$i;
			$this->score_cal[$student_sn][sort]=$j;
			$pre_score=$this->score_cal[$student_sn][sum];
			$i++;
		}
		//讓各科平均、各科標準差顯示時不會出現錯誤
		$this->score_cal[avg][sort]=" ";
		$this->score_cal[std][sort]=" ";
	}

	function average_subject() {
		$this->row_arr[avg][name]="各科平均";
		$this->is_average_subject=true;
		//如果還沒計算成績，就先計算
		if ($this->is_summary!=true) $this->summary();
		reset ($this->score_subject);
		while(list($ss_id,$v)=each($this->score_subject)) {
			$this->subject_cal[$ss_id][avg]=number_format($this->score_subject[$ss_id][sum]/$this->score_subject[$ss_id][num],$precision);
		}
	}

	function std() {
		$this->row_arr[std][name]="各科標準差";
		//如果還沒計算各科平均，就先計算
		if ($this->is_average_subject!=true) $this->average_subject();
		reset ($this->score_arr);
		while(list($student_sn,$z)=each($this->score_arr)) {
			while(list($ss_id,$y)=each($z)) {
				while(list($sort,$x)=each($y)) {
					while(list($kind,$s)=each($x)) {
						$this->subject_cal[$ss_id][std]+=pow($s-$this->subject_cal[$ss_id][avg],2);
					}
				}
			}
		}
		reset ($this->score_subject);
		while(list($ss_id,$v)=each($this->score_subject)) {
			$this->subject_cal[$ss_id][std]=number_format(sqrt($this->subject_cal[$ss_id][std]/$this->score_subject[$ss_id][num]),$precision);
		}
	}

	function stat() {
		$this->row_arr[w1][name]="成績分佈表";
		$this->row_arr[w1][sn]="line";
		$this->row_arr[s10][name]="100分";
		$this->row_arr[s9][name]="90分~100分";
		$this->row_arr[s8][name]="80分~ 90分";
		$this->row_arr[s7][name]="70分~ 80分";
		$this->row_arr[s6][name]="60分~ 70分";
		$this->row_arr[s5][name]="50分~ 60分";
		$this->row_arr[s4][name]="40分~ 50分";
		$this->row_arr[s3][name]="30分~ 40分";
		$this->row_arr[s2][name]="20分~ 30分";
		$this->row_arr[s1][name]="10分~ 20分";
		$this->row_arr[s0][name]=" 0分~ 10分";
		for ($i=0;$i<=10;$i++) {
			$j="s".$i;
			$this->score_cal[$j][sum]=" ";
			$this->score_cal[$j][avg]=" ";
			$this->score_cal[$j][sort]=" ";
		}
	}

	function view() {
		$this->show_header("view");
		$left_cols=count($this->left_col_arr);
		while(list($k,$v)=each($this->row_arr)) {
			//若有各科平均、各科標準差則列出
			if (intval($k)==0 && $k!="0" && $k!="") {
				if ($this->row_arr[$k][sn]=="line") {
					$line_cols=$left_cols+count($this->col_arr)+count($this->score_cal);
					echo "<tr><td colspan='$line_cols'><font color='#ffffff'>".stripslashes($this->row_arr[$k][name])."</font></td></tr>";
				} else {
					echo "<tr bgcolor=".$this->title_bgcolor."><td colspan='$left_cols'>".stripslashes($this->row_arr[$k][name])."</td>";
					reset ($this->col_arr);
					while(list($ss_id,$j)=each($this->col_arr)) {
						echo "<td bgcolor='".$this->title_bgcolor."'>".$this->subject_cal[$ss_id][$k]."</td>";
					}
					//個人總分、平均、排名等欄位要留空白
					while(list($cal_kind,$j)=each($this->score_cal[$k])) {
						if ($cal_kind=="num" || $this->right_col_arr[$cal_kind]=="") continue;
						echo "<td>$j</td>";
						$i++;
					}
					echo "</tr>";
				}
			} else {
			//列出各人各科成績
				//如果是分組則座號顯示的是班級座號，否則顯示座號
				if ($this->row_arr[$k][class_site])
					$site=$this->row_arr[$k][class_site];
				else
					$site=$k;
				echo "<tr bgcolor='#ffffff'><td bgcolor=".$this->col_bgcolor[0].">$site</td><td bgcolor=".$this->col_bgcolor[1].">".stripslashes($this->row_arr[$k][name])."</td>";
				reset ($this->col_arr);
				while(list($ss_id,$j)=each($this->col_arr)) {
					while(list($kind,$b)=each($this->score_arr[$this->row_arr[$k][sn]][$ss_id][$this->sort])) {
						if ($kind=="num") continue;
						$s=$this->score_arr[$this->row_arr[$k][sn]][$ss_id][$this->sort][$kind];
						$score_fcolor=(intval($s)<60)?"'#ff0000'":"'#000000'";
						echo "<td><font color=$score_fcolor>".$s."</font></td>";
					}
				}
				//列出個人總分、平均、排名
				$i=$left_cols;
				while(list($cal_kind,$j)=each($this->score_cal[$this->row_arr[$k][sn]])) {
					if ($cal_kind=="num" || $this->right_col_arr[$cal_kind]=="") continue;
					$score_fcolor=(intval($j)<60)?"'#ff0000'":"'#000000'";
					if ($cal_kind=="sort") $score_fcolor="'#000000'";
					echo "<td bgcolor=".$this->col_bgcolor[$i]."><font color=$score_fcolor>".$j."</font></td>";
					$i++;
				}
				echo "</tr>\n";
			}
		}
		echo "</table>";
	}

	function edit() {
		$this->show_header("edit");
	}

	function save() {
	}

	function file_in() {
	}

	function file_out() {
		$this->show_header("file_out");
		$left_cols=count($this->left_col_arr);
		while(list($ss_id,$v)=each($this->col_arr)) {
			echo $this->col_arr[$ss_id][name].'\r\n';
		}
		$h=1;
		while(list($k,$v)=each($this->row_arr)) {
			//若有各科平均、各科標準差則列出
			if (intval($k)==0 && $k!="0" && $k!="") {
				if ($this->row_arr[$k][sn]=="line") {
					$line_cols=$left_cols+count($this->col_arr)+count($this->score_cal);
					echo stripslashes($this->row_arr[$k][name]).',';
				} else {
					echo ','.stripslashes($this->row_arr[$k][name]).',';
					reset ($this->col_arr);
					while(list($ss_id,$j)=each($this->col_arr)) {
						echo $this->subject_cal[$ss_id][$k].',';
					}
					//個人總分、平均、排名等欄位要留空白
					while(list($cal_kind,$j)=each($this->score_cal[$k])) {
						if ($cal_kind=="num" || $this->right_col_arr[$cal_kind]=="") continue;
						echo $j.',';
					}
					echo "\r\n";
				}
			} else {
				//列出各人各科成績
				//如果是分組則座號顯示的是班級座號，否則顯示座號
				if ($this->row_arr[$k][class_site])
					$site=$this->row_arr[$k][class_site];
				else
					$site=$k;
				echo $site.','.stripslashes($this->row_arr[$k][name]).',';
				reset ($this->col_arr);
				$i=1;
				while(list($ss_id,$j)=each($this->col_arr)) {
					while(list($kind,$b)=each($this->score_arr[$this->row_arr[$k][sn]][$ss_id][$this->sort])) {
						if ($kind=="num") continue;
						$s=$this->score_arr[$this->row_arr[$k][sn]][$ss_id][$this->sort][$kind];
						echo $s.',';
					}
					$i++;
				}
				//列出個人總分、平均、排名
				$i=$left_cols;
				$j=1;
				while(list($cal_kind,$j)=each($this->score_cal[$this->row_arr[$k][sn]])) {
					if ($cal_kind=="num" || $this->right_col_arr[$cal_kind]=="") continue;
					echo $j.',';
					$i++;
					$j++;
				}
				echo "\r\n";
			$h++;
			}
		}

	}

	function output() {
		$this->show_header("output");
		$left_cols=count($this->left_col_arr);
		$col_width=floor(510/(count($this->col_arr)+count($this->score_cal)));
		$i=1;
		while(list($ss_id,$v)=each($this->col_arr)) {
			if ($i!=count($this->col_arr)) {
				echo '<td style="border-style:solid; border-width:1.5pt 0.75pt;" align="center" width="'.$col_width.'">'.$this->col_arr[$ss_id][name].'</td>\n';
			} else {
				echo '<td style="border-style:solid; border-width:1.5pt 1.5pt 1.5pt 0.75pt;" align="center" width="'.$col_width.'">'.$this->col_arr[$ss_id][name].'</td>\n';
			}
			$i++;
		}
		echo '</tr>';
		$h=1;
		while(list($k,$v)=each($this->row_arr)) {
			//若有各科平均、各科標準差則列出
			if (intval($k)==0 && $k!="0" && $k!="") {
				if ($this->row_arr[$k][sn]=="line") {
					$line_cols=$left_cols+count($this->col_arr)+count($this->score_cal);
					echo '<tr><td style="border-style:solid; border-width:1.5pt 1.5pt 1.5pt 0.75pt;" align="center" colspan="'.$line_cols.'">'.stripslashes($this->row_arr[$k][name]).'</td></tr>';
				} else {
					echo '<tr><td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 1.5pt;" align="center" colspan="'.$left_cols.'">'.stripslashes($this->row_arr[$k][name]).'</td>';
					reset ($this->col_arr);
					while(list($ss_id,$j)=each($this->col_arr)) {
						echo '<td style="border-style:solid; border-width:1.5pt 0.75pt;" align="center">'.$this->subject_cal[$ss_id][$k].'</td>';
					}
					//個人總分、平均、排名等欄位要留空白
					while(list($cal_kind,$j)=each($this->score_cal[$k])) {
						if ($cal_kind=="num" || $this->right_col_arr[$cal_kind]=="") continue;
						echo '<td style="border-style:solid; border-width:1.5pt;" align="center">'.$j.'</td>';
						$i++;
					}
					echo "</tr>";
				}
			} else {
				$d_str=($h % 5==0 || $h==count($this->row_arr))?"1.5pt":"0.75pt";
				//列出各人各科成績
				//如果是分組則座號顯示的是班級座號，否則顯示座號
				if ($this->row_arr[$k][class_site])
					$site=$this->row_arr[$k][class_site];
				else
					$site=$k;
				echo '<td style="border-style:solid; border-width:0.75pt 0.75pt '.$d_str.' 1.5pt;" align="center" width="40">'.$site.'</td><td style="border-style:solid; border-width:0.75pt 0.75pt '.$d_str.' 0.75pt;" align="center" width="60">'.stripslashes($this->row_arr[$k][name]).'</td>';
				reset ($this->col_arr);
				$i=1;
				while(list($ss_id,$j)=each($this->col_arr)) {
					while(list($kind,$b)=each($this->score_arr[$this->row_arr[$k][sn]][$ss_id][$this->sort])) {
						if ($kind=="num") continue;
						$s=$this->score_arr[$this->row_arr[$k][sn]][$ss_id][$this->sort][$kind];
						$r_str=($i!=count($this->col_arr))?"0.75pt":"1.5pt";
						echo '<td style="border-style:solid; border-width:0.75pt '.$r_str.' '.$d_str.' 0.75pt;" align="center" width="'.$col_width.'">'.$s.'</td>';
					}
					$i++;
				}
				//列出個人總分、平均、排名
				$i=$left_cols;
				$j=1;
				while(list($cal_kind,$j)=each($this->score_cal[$this->row_arr[$k][sn]])) {
					if ($cal_kind=="num" || $this->right_col_arr[$cal_kind]=="") continue;
					echo '<td style="border-style:solid; border-width:0.75pt '.$r_str.' '.$d_str.' 0.75pt;" align="center" width="'.$col_width.'">'.$j.'</td>';
					$i++;
					$j++;
				}
				echo "</tr>\n";
			$h++;
			}
		}
	}
}

function cal_fin_score($student_sn=array(),$seme=array(),$succ="",$strs="",$precision=1)   //$succ:需合格領域數 $strs:等第評斷代換字串
{

	//取出學期初設定中  畢業成績計算方式  0:算數平均   1:加權平均(學分概念加權)

	global $CONN;
	if (count($seme)==0) return;
	$SQL="select * from pro_module where pm_name='every_year_setup' AND pm_item='FIN_SCORE_RATE_MODE'";
        $RES=$CONN->Execute($SQL);
        $FIN_SCORE_RATE_MODE=INTVAL($RES->fields['pm_value']);

	$sslk=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","健康與體育"=>"health","生活"=>"life","社會"=>"social","藝術與人文"=>"art","自然與生活科技"=>"nature","數學"=>"math","綜合活動"=>"complex");
	if (count($student_sn)>0 && count($seme)>0) {
		$all_sn="'".implode("','",$student_sn)."'";
		$all_seme="'".implode("','",$seme)."'";
		//取得科目成績
		$query="select a.*,b.link_ss,b.rate from stud_seme_score a left join score_ss b on a.ss_id=b.ss_id where a.student_sn in ($all_sn) and a.seme_year_seme in ($all_seme) and b.enable='1' and b.need_exam='1'";
		// 若彰化縣..則修正資料庫語法,加入針對SS_ID的年級作檢查,是否與學生所在年級相符
/*		$sch=get_school_base();
		if($sch[sch_sheng]=='彰化縣'){
			$query="select a.*,b.link_ss,b.rate,b.class_year ,b.year as chc_year,b.semester as chc_semester,c.seme_class as chc_seme_class from stud_seme_score a left join score_ss b on a.ss_id=b.ss_id left join stud_seme as c on (a.seme_year_seme=c.seme_year_seme and a.student_sn =c.student_sn) where a.student_sn in ($all_sn) and a.seme_year_seme in ($all_seme) and b.enable='1' and b.need_exam='1' and (b.class_year=left(c.seme_class,1))";
		}
*/		
		$res=$CONN->Execute($query);
		//取得各學期領域學科成績.加權數並加總
		while(!$res->EOF) {
			//取得領域加權總分
			$subj_score[$res->fields[student_sn]][$res->fields[link_ss]][$res->fields[seme_year_seme]]+=$res->fields[ss_score]*$res->fields[rate];
			//領域總加權數
			$rate[$res->fields[student_sn]][$res->fields[link_ss]][$res->fields[seme_year_seme]]+=$res->fields[rate];
			$res->MoveNext();
		}

		//處理各學期領域平均
		$IS5=false;
		$IS7=false;
		while(list($sn,$v)=each($subj_score)) {
			$sys=array();
			reset($v);
			while(list($link_ss,$vv)=each($v)) {
				reset($vv);
				$ls=$sslk[$link_ss];
				if($ls){  //學期成績計算排除九年一貫對應為"非預設領域科目"與"彈性課程"(非五大或七大領域) 的成績 
					if($ls=="life") $IS5=true;
					if($ls=="art") $IS7=true;
					//計算各領域學期成績
					while(list($seme_year_seme,$s)=each($vv)) {
						$fin_score[$sn][$ls][$seme_year_seme][score]=number_format($s/$rate[$sn][$link_ss][$seme_year_seme],$precision);
						$fin_score[$sn][$ls][$seme_year_seme][rate]=$rate[$sn][$link_ss][$seme_year_seme];

						//$FIN_SCORE_RATE_MODE=1為加權平均  0為算數平均   假設畢業總平均加權數來自原始科目加權數   須注意各學期加權是否合理  比如  前一學期以100 200  500 設定   但次一學期以節數 2  3 6  設定  如此會造成單一學期的該領域成績比重失衡問題
						if($FIN_SCORE_RATE_MODE=='1') {
							//領域畢業總成績
							$fin_score[$sn][$ls][total][score]+=$fin_score[$sn][$ls][$seme_year_seme][score]*$rate[$sn][$link_ss][$seme_year_seme];
							//領域畢業總平均
							$fin_score[$sn][$ls][total][rate]+=$rate[$sn][$link_ss][$seme_year_seme];
						} else {
							$fin_score[$sn][$ls][total][score]+=$fin_score[$sn][$ls][$seme_year_seme][score];
							$fin_score[$sn][$ls][total][rate]+=1;
						}

						//當學期學期總平均處理
						if ($ls=="chinese" || $ls=="local" || $ls=="english") {
							//語文領域特別處理部份
							if ($sys[$seme_year_seme]!=1) $sys[$seme_year_seme]=1;
							$fin_score[$sn][language][$seme_year_seme][score]+=$fin_score[$sn][$ls][$seme_year_seme][score]*$fin_score[$sn][$ls][$seme_year_seme][rate];
							$fin_score[$sn][language][$seme_year_seme][rate]+=$fin_score[$sn][$ls][$seme_year_seme][rate];
						} else {

							if($FIN_SCORE_RATE_MODE=='1') {
								$fin_score[$sn][$seme_year_seme][total][score]+=$fin_score[$sn][$ls][$seme_year_seme][score]*$rate[$sn][$link_ss][$seme_year_seme];
								$fin_score[$sn][$seme_year_seme][total][rate]+=$rate[$sn][$link_ss][$seme_year_seme];
							} else {
								$fin_score[$sn][$seme_year_seme][total][score]+=$fin_score[$sn][$ls][$seme_year_seme][score];
								$fin_score[$sn][$seme_year_seme][total][rate]+=1;
							}
						}
					}
				}
				$fin_score[$sn][$ls][avg][score]=number_format($fin_score[$sn][$ls][total][score]/$fin_score[$sn][$ls][total][rate],$precision);

				//除 本國語文  鄉土語言  英語  和 彈性課程 外   將其他領域平均成績加入"畢業"總成績
				if ($ls!="chinese" && $ls!="local" && $ls!="english" && $ls!="") {
					if($FIN_SCORE_RATE_MODE=='1') {
						$fin_score[$sn][total][score]+=$fin_score[$sn][$ls][total][score];
						$fin_score[$sn][total][rate]+=$fin_score[$sn][$ls][total][rate];
					} else {
						$fin_score[$sn][total][score]+=$fin_score[$sn][$ls][avg][score];
						$fin_score[$sn][total][rate]+=1;
//echo $sn."---".$fin_score[$sn][total][score]." --- ".$fin_score[$sn][$ls][avg][score]."---".$fin_score[$sn][total][rate]."<BR>";
					}
					//判斷及格領域數
					if ($fin_score[$sn][$ls][avg][score] >= 60) $fin_score[$sn][succ]++; else  $fin_score[$sn][fail]++;
				}
			}


			//生活領域成績特別處理
			if($IS5 && $IS7) {
				$fin_score[$sn][art][total][score]+=$fin_score[$sn][life][avg][score]*$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][nature][total][score]+=$fin_score[$sn][life][avg][score]*$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][social][total][score]+=$fin_score[$sn][life][avg][score]*$fin_score[$sn][life][total][rate]/3;

				$fin_score[$sn][art][total][rate]+=$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][nature][total][rate]+=$fin_score[$sn][life][total][rate]/3;
				$fin_score[$sn][social][total][rate]+=$fin_score[$sn][life][total][rate]/3;

				$fin_score[$sn][art][avg][score]=number_format($fin_score[$sn][art][total][score]/$fin_score[$sn][art][total][rate],$precision);
				$fin_score[$sn][nature][avg][score]=number_format($fin_score[$sn][nature][total][score]/$fin_score[$sn][nature][total][rate],$precision);
				$fin_score[$sn][social][avg][score]=number_format($fin_score[$sn][social][total][score]/$fin_score[$sn][social][total][rate],$precision);
			}


			//語文領域成績特別獨立計算
			if (count($sys)>0) {
				$r=0;
				while(list($seme_year_seme,$s)=each($sys)) {
					$fin_score[$sn][language][$seme_year_seme][score]=number_format($fin_score[$sn][language][$seme_year_seme][score]/$fin_score[$sn][language][$seme_year_seme][rate],$precision);


					if($FIN_SCORE_RATE_MODE=='1')	{
						$fin_score[$sn][language][avg][score]+=$fin_score[$sn][language][$seme_year_seme][score]*$fin_score[$sn][language][$seme_year_seme][rate];
						$fin_score[$sn][language][total][score]+=$fin_score[$sn][language][$seme_year_seme][score]*$fin_score[$sn][language][$seme_year_seme][rate];
						$fin_score[$sn][language][total][rate]+=$fin_score[$sn][language][$seme_year_seme][rate];



						$fin_score[$sn][$seme_year_seme][total][score]+=$fin_score[$sn][language][$seme_year_seme][score]*$fin_score[$sn][language][$seme_year_seme][rate];
						$r+=$fin_score[$sn][language][$seme_year_seme][rate];
		//echo $sn."---".$r."---".$fin_score[$sn][language][$seme_year_seme][rate]."---".$fin_score[$sn][language][avg][score]."<BR>";
						$fin_score[$sn][$seme_year_seme][total][rate]+=$fin_score[$sn][language][$seme_year_seme][rate];
					} else {
						$fin_score[$sn][language][avg][score]+=$fin_score[$sn][language][$seme_year_seme][score];
						$fin_score[$sn][$seme_year_seme][total][score]+=$fin_score[$sn][language][$seme_year_seme][score];
						$r+=1;
						$fin_score[$sn][$seme_year_seme][total][rate]+=1;
					}
					$fin_score[$sn][$seme_year_seme][avg][score]=number_format($fin_score[$sn][$seme_year_seme][total][score]/$fin_score[$sn][$seme_year_seme][total][rate],$precision);
				}

				$fin_score[$sn][language][avg][score]=number_format($fin_score[$sn][language][avg][score]/$r,$precision);
				if($FIN_SCORE_RATE_MODE=='1')	{
					$fin_score[$sn][total][score]+=$fin_score[$sn][language][avg][score]*$r;
					$fin_score[$sn][total][rate]+=$r;
				} else {
					$fin_score[$sn][total][score]+=$fin_score[$sn][language][avg][score];
					$fin_score[$sn][total][rate]+=1;
				}

				$fin_score[$sn][avg][score]=number_format($fin_score[$sn][total][score]/$fin_score[$sn][total][rate],$precision);
				//複製到排名陣列
				$rank_score[$sn]=$fin_score[$sn]['total']['score'];


				if ($fin_score[$sn][language][avg][score] >= 60) $fin_score[$sn][succ]++;else $fin_score[$sn][fail]++;
			}

			if ($succ) {
				if ($fin_score[$sn][succ] < $succ) $show_score[$sn]=$fin_score[$sn];
			}
      
      //針對最後結果做排序
			arsort($rank_score);
			//計算名次
			$rank=0;
			foreach($rank_score as $key=>$value) {
				$rank+=1;
				$fin_score[$key]['total']['rank']=$rank;
			}

		}


		if ($succ)
			return $show_score;
		else
			return $fin_score;
	} elseif (count($student_sn)==0) {
		return "沒有傳入學生流水號";
	} else {
		return "沒有傳入學期";
	}
}

function cal_fin_nor_score($student_sn=array(),$seme=array(),$mode="")
{
	global $CONN;
	if (count($student_sn)>0 && count($seme)>0) {
		$all_sn="'".implode("','",$student_sn)."'";
		$all_seme="'".implode("','",$seme)."'";
		$query="select * from stud_seme_score_nor where student_sn in ($all_sn) and seme_year_seme in ($all_seme) and ss_id='0'";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$fin_score[$res->fields[student_sn]][$res->fields[seme_year_seme]][score]=number_format($res->fields[ss_score],$precision);
			if ($mode=="word") $fin_score[$res->fields[student_sn]][$res->fields[seme_year_seme]][word]=$res->fields[ss_score_memo];
			$res->MoveNext();
		}
		$s_num=count($seme);
		while(list($sn,$v)=each($fin_score)) {
			while(list($seme_year_seme,$vv)=each($v)) {
				if ($vv[score] != "") {
					$s=$vv[score];
					settype($s,"double");
					if ($s < 0 ) {
						//無動作
					} elseif ($s <= 100) {
						$fin_score[$sn][total][score]+=$s;
					} else {
						$fin_score[$sn][total][score]+=100;
					}
					$fin_score[$sn][num]++;
					if ($s < 60) $fin_score[$sn][dissucc]++;
				}
			}
			$fin_score[$sn][avg][score]=number_format($fin_score[$sn][total][score]/$fin_score[$sn][num],$precision);
			if ($mode=="disgrad" && $fin_score[$sn][dissucc]>0) $show_score[$sn]=$fin_score[$sn];
		}
		if ($mode=="disgrad") {
			return $show_score;
		} else {
			return $fin_score;
		}
	} elseif (count($student_sn)==0) {
		return "沒有傳入學生流水號";
	} else {
		return "沒有傳入學期";
	}
}

function get_nor_score($sel_year="",$sel_seme="",$sel_stage="",$class_subj="",$teacher_sn="",$precision="")
{
	global $CONN;
	if($precision==='') $precision=2;

	if ($sel_year=="") {
		$data_arr[err]="沒有傳入學年";
	} elseif ($sel_seme=="") {
		$data_arr[err]="沒有傳入學期";
	} elseif ($sel_stage=="") {
		$data_arr[err]="沒有傳入階段";
	} elseif ($class_subj=="") {
		$data_arr[err]="沒有傳入課程代碼";
	} elseif ($teacher_sn=="") {
		$data_arr[err]="沒有傳入教師代碼";
	} else {
		$nor_score="nor_score_".$sel_year."_".$sel_seme;
		$query = "select distinct * from $nor_score where class_subj='$class_subj' and stage='$sel_stage' and enable='1' group by class_subj,stage,freq";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$data_arr[status][$res->fields[stage]][$res->fields[freq]][name]=$res->fields[test_name];
			$data_arr[status][$res->fields[stage]][$res->fields[freq]][weighted]=$res->fields[weighted];
			$data_arr[status][$res->fields[stage]][$res->fields[freq]][teach_id]=$res->fields[teach_id];
			$res->MoveNext();
		}
		$query = "select a.* from $nor_score a,stud_base b where a.stud_sn=b.student_sn and a.class_subj='$class_subj' and a.stage='$sel_stage' and b.stud_study_cond in ('0','15') and a.enable='1' order by b.curr_class_num,a.sn";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$data_arr[score][$res->fields[stage]][$res->fields[freq]][$res->fields[stud_sn]]=$res->fields[test_score];
			if ($res->fields[test_score]!="-100") {
				$data_arr[score][$res->fields[stage]][avg][$res->fields[stud_sn]]+=$res->fields[test_score]*$data_arr[status][$res->fields[stage]][$res->fields[freq]][weighted];
				$data_arr[score][$res->fields[stage]][weighted][$res->fields[stud_sn]]+=$data_arr[status][$res->fields[stage]][$res->fields[freq]][weighted];
			}
			$res->MoveNext();
		}
		reset($data_arr[score]);
		while(list($stage,$v)=each($data_arr[score])) {
			reset($data_arr[score][$stage][avg]);
			while(list($sn,$vv)=each($data_arr[score][$stage][avg])) {
				if (!empty($data_arr[score][$stage][avg][$sn])) {
					if ($data_arr[score][$stage][weighted][$sn]>0) $data_arr[score][$stage][avg][$sn]=number_format($data_arr[score][$stage][avg][$sn]/$data_arr[score][$stage][weighted][$sn],$precision);
				}
			}
		}
	}
	return $data_arr;
}

function count_nor($student_sn=array(),$seme=array(),$mode="") {
	global $CONN,$_POST;

	if (count($student_sn)>0 && count($seme>0)) {
		$all_sn="'".implode("','",$student_sn)."'";
		$all_seme="'".implode("','",$seme)."'";
		$stud_id=array();
		$query="select * from stud_base where student_sn in ($all_sn)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$stud_id[]=$res->fields['stud_id'];
			$id2sn[$res->fields['stud_id']]=$res->fields['student_sn'];
			$res->MoveNext();
		}
		if (count($stud_id)>0) {
			$all_id="'".implode("','",$stud_id)."'";
			$data_arr=array();
			if ($mode==0) {
				//$query="select * from stud_seme_abs where seme_year_seme in ($all_seme) and stud_id in ($all_id) and abs_kind in ('1','2','3') order by stud_id";
				//2015.03.03 by smallduh ,依據教育部最新規定 病假不列入修畢業條件計算 array("事假"=>"1","病假"=>"2","曠課"=>"3","集會"=>"4","公假"=>"5","其他"=>"6","喪假"=>"6");
				$query="select * from stud_seme_abs where seme_year_seme in ($all_seme) and stud_id in ($all_id) and abs_kind in ('1','3') order by stud_id";
				$res=$CONN->Execute($query);
				$dis=0;
				$sday=intval($_POST['semeday']);
				$sday2=intval($_POST['semeday2']);
				$d_arr=array();
				while(!$res->EOF) {
					$sn=$id2sn[$res->fields[stud_id]];
					if ($osn==0) $osn=$sn;
					if ($osn!=$sn) {
						if ($dis==1) $data_arr[$osn]=$d_arr;
						$dis=0;
						$d_arr=array();
						$osn=$sn;
					}
					$sms=$res->fields['seme_year_seme'];
					$kind=$res->fields['abs_kind'];
					$days=$res->fields['abs_days'];
					$d_arr[$sms][abs][$kind]+=$days;
					$d_arr[$sms][abs][all]+=$days;
					if ($_POST['chk1'] && $kind==3 && $days>=$sday) $dis=1;
					if ($_POST['chk2'] && $d_arr[$sms][abs][all]>=$sday2) $dis=1;
					$res->MoveNext();
				}
				if ($osn && $dis) $data_arr[$osn]=$d_arr;
			}
			if ($mode==1) {
				if (intval($_POST['neu'])>0)
					$r_arr=array(0,0,0,0,"-9","-3","-1");
				else
					$r_arr=array(0,9,3,1,"-9","-3","-1");
				$osn=0;
				$dis=0;
				$query="select * from stud_seme_rew where seme_year_seme in ($all_seme) and student_sn in ($all_sn) order by student_sn";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn=$res->fields[student_sn];
					if ($osn==0) $osn=$sn;
					if ($osn!=$sn) {
						if ($d_arr[all][rew][all] <= (-27)) $data_arr[$osn]=$d_arr;
						$dis=0;
						$d_arr=array();
						$osn=$sn;
					}
					$sms=$res->fields['seme_year_seme'];
					$kind=$res->fields['sr_kind_id'];
					$r=$res->fields['sr_num']*$r_arr[$kind];
					$d_arr[$sms][rew][$kind]+=$r;
					$d_arr[$sms][rew][all]+=$r;
					$d_arr[all][rew][all]+=$r;
					$res->MoveNext();
				}
				if ($osn && $dis) $data_arr[$osn]=$d_arr;
				if ($d_arr[all][rew][all] <= (-27)) $data_arr[$osn]=$d_arr;
			}
			return $data_arr;
		}
	} elseif (count($student_sn)==0) {
		return "沒有傳入學生流水號";
	} else {
		return "沒有傳入學期";
	}
}

