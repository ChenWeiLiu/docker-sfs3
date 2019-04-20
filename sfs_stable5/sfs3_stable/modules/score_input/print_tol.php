<?php
// $Id: print_tol.php 9099 2017-06-29 08:13:51Z chiming $
include "config.php";
//引入函數
include "./myfun2.php";
//使用者認證
sfs_check();

$add_normal=($_POST[add_normal])?"$_POST[add_normal]":"$_GET[add_normal]";
$add_rate=($_POST[add_rate])?"$_POST[add_rate]":"$_GET[add_rate]";
$temp_check=($_POST[temp_check])?"$_POST[temp_check]":"$_GET[temp_check]";
if ($add_normal=='' && $temp_check=='' )
	$add_normal=1;
if ($add_rate=='' && $temp_check=='')
	$add_rate=1;
//echo $add_normal;
$sel_year= curr_year();
$sel_seme= curr_seme();
$year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
if ($is_print=="y")
	$year_name = $_REQUEST[year_name];
else {
//取得任教班級代號
	$year_name = get_teach_class();
	if ($year_name == '') {
		head("權限錯誤");
		print_menu($menu_p);
		stud_class_err();
		foot();
		exit;
	}
}
$stage = $_REQUEST[stage];
$doc=$_GET['doc'];
$sxw=$_GET['sxw'];
$fine_print = $_GET[fine_print];
if(($doc=="1")||($sxw=="1")){
    $filename=($doc=="1")?"score_paper.doc":"score_paper.sxw";
    header("Content-disposition: filename=$filename");
    header("Content-type: application/octetstream ; Charset=Big5");
    //header("Pragma: no-cache");
    				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改
				header("Cache-Control: max-age=0");
				header("Pragma: public");
		header("Expires: 0");
}

//秀出網頁
if($fine_print!="1"){
	head("成績查詢");
	print_menu($menu_p);
}
else{
	echo "<html>
		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; Charset=Big5\">
		</head>
	<body>";
}

//設定主網頁顯示區的背景顏色
if($fine_print!="1") echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor=#FFFFFF>";
//$score_semester="score_semester_".$sel_year."_".$sel_seme;
$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id
$score_semester="score_semester_".$sel_year."_".$sel_seme;
$show_class_year = class_base($year_seme);
$ss1 = new drop_select();
if ($is_print=="y") {
	$ss1->s_name ="year_name";
	$ss1->top_option = "選擇班級";
	$ss1->id = $year_name;
	$ss1->arr = $show_class_year;
	$ss1->is_submit = true;
	$class_year_menu =$ss1->get_select();
}

if($year_name){
	$show_stage = select_stage2($year_seme,$year_name);
	$ss1->s_name = "stage";
	$ss1->id =$stage;
	$ss1->arr = $show_stage;
	$ss1->top_option = "選擇階段";
	$ss1->is_submit = true;
	$stage_menu= $ss1->get_select();
	$checked=($add_normal)?"checked":"";
	$normal_menu="<input type='hidden' name='temp_check' value='1' ><input type='checkbox' name='add_normal' $checked value='1' onclick='this.form.submit()'>加計平時成績?";
	$checked=($add_rate)?"checked":"";
	$normal_menu.=" &nbsp;&nbsp;<input type='checkbox' name='add_rate' $checked value='1' onclick='this.form.submit()'>加權計算成績?";
}

$menu="<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
    <table cellspacing=0 cellpadding=0>
        <tr>
            <td>$class_year_menu</td><td>$stage_menu</td><td>$normal_menu</td>
        </tr>
    </table>
 </form>";
if($fine_print!="1") echo $menu;

//以上為選單bar


if($year_name && $stage){
//	$sel_year = intval(substr($year_seme,0,-1));
//	$sel_seme = substr($year_seme,-1);
	$c_year = substr($year_name,0,-2);
        $c_name = substr($year_name,-2);
	$class_id = sprintf("%03s_%s_%02s_%02s",$sel_year,$sel_seme,$c_year,$c_name);

	//取出本學年本學期的學校成績共通設定
	$sql="select * from score_setup where class_year=$c_year and year='$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql);
	$score_mode= $rs->fields['score_mode'];
	$test_ratio_a= $rs->fields['test_ratio'];
	$performance_test_times=  $rs->fields['performance_test_times'];

	//每次評量都相同比率
	//echo "score_mode".$score_mode;
	if($score_mode=="all"){
		$test_ratio_arr=explode("-",$test_ratio_a);
		if ($test_ratio_arr[0]=="") $test_ratio_arr[0]=60;
		if ($test_ratio_arr[1]=="") $test_ratio_arr[1]=40;
		if (!$add_normal) {
			$test_ratio_arr[0]=100;
			$test_ratio_arr[1]=0;
		}
		$m1=$test_ratio_arr[0]/$performance_test_times;
		$m2=$test_ratio_arr[1]/$performance_test_times;
		$test_ratio=array("$m1","$m2");
		//echo $test_ratio_arr[0]."===".$test_ratio_arr[1];
		for($i=0;$i<$performance_test_times;$i++){
			$test_ratio_all[$i]=$test_ratio;
			//echo $test_ratio_all[$i]."<br>";
		}

    //$test_ratio_all_1=$test_ratio_all_2=$test_ratio_all_3=$test_ratio_all_4=$test_ratio_all_5=$test_ratio;
	}
	//每次評量不同比率
	elseif($score_mode=="severally"){
		$test_ratio=explode(",",$test_ratio_a);
		for($i=0;$i<count($test_ratio);$i++){
			$test_ratio_all[$i]=explode("-",$test_ratio[$i]);
			//echo "tta".$test_ratio_all[$i][0];
			if($test_ratio_all[$i][0]=="") $test_ratio_all[$i][0]=(60/$performance_test_times);
			if($test_ratio_all[$i][1]=="") $test_ratio_all[$i][1]=(40/$performance_test_times);
			if($stage==($i+1)) {
				$test_ratio=$test_ratio_all[$i];
				$test_ratio_arr[0]=($test_ratio_all[$i][0]/($test_ratio_all[$i][0]+$test_ratio_all[$i][1]))*100;
				//echo "(".$test_ratio_all[$i][0]."/(".$test_ratio_all[$i][0]."+".$test_ratio_all[$i][1]."))*100";
				$test_ratio_arr[1]=($test_ratio_all[$i][1]/($test_ratio_all[$i][0]+$test_ratio_all[$i][1]))*100;
				//echo $test_ratio_arr[0]."======".$test_ratio_arr[1];
			}
		}
	}
	else{
		$test_ratio_arr[0]=60;
		$test_ratio_arr[1]=40;
		$test_ratio[0]=(60/$performance_test_times);
		$test_ratio[1]=(40/$performance_test_times);
		for($i=0;$i<$performance_test_times;$i++){
			$test_ratio_all[$i]=$test_ratio;
		}
	}
	/*
	//每次評量都相同比率
	if($score_mode=="all"){
		$test_ratio_arr=explode("-",$test_ratio_a);
		if ($test_ratio_arr[0]=="") $test_ratio_arr[0]=60;
		if ($test_ratio_arr[1]=="") $test_ratio_arr[1]=40;
		$m1=$test_ratio_arr[0]/$performance_test_times;
		$m2=$test_ratio_arr[1]/$performance_test_times;
		$test_ratio=array("$m1","$m2");

		for($i=0;$i<$performance_test_times;$i++){
			$test_ratio_all[$i]=$test_ratio;
		}

    //$test_ratio_all_1=$test_ratio_all_2=$test_ratio_all_3=$test_ratio_all_4=$test_ratio_all_5=$test_ratio;
	}
	//每次評量不同比率
	elseif($score_mode=="severally"){
		$test_ratio=explode(",",$test_ratio);
		for($i=0;$i<count($test_ratio);$i++){
			$test_ratio_all[$i]=explode("-",$test_ratio[$i]);
			if($test_ratio_all[$i][0]=="") $test_ratio_all[$i][0]=(60/$performance_test_times);
			if($test_ratio_all[$i][1]=="") $test_ratio_all[$i][1]=(40/$performance_test_times);
			if($stage==($i+1)) $test_ratio=$test_ratio_all[$i];

		}
	}
	else{
		$test_ratio[0]=(60/$performance_test_times);
		$test_ratio[1]=(40/$performance_test_times);
		for($i=0;$i<$performance_test_times;$i++){
			$test_ratio_all[$i]=$test_ratio;
		}
	}
	*/
	$scope_name = array();
	//取得所有科目名稱
	$subject_name_arr = get_all_subject_arr();
			//檢查是否有設班級課程
		$sql="SELECT count(*) FROM score_ss WHERE enable='1' and class_id='$class_id' ";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$where_class_id=($rs->fields[0])?" and class_id='$class_id'":" and class_id=''";
        //該年級的共通設定
        if($stage==255)
		$sql="select scope_id,subject_id,ss_id,rate,print from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$c_year' and enable='1' and need_exam='1' $where_class_id order by sort,sub_sort";
	else
		$sql="select scope_id,subject_id,ss_id,rate,print  from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$c_year' and enable='1' and need_exam='1' and print='1' $where_class_id order by sort,sub_sort";
	$rs=$CONN->Execute($sql) or die($sql);
	$scope_arr = array();
	$subject_arr = array();
	while(!$rs->EOF){
		$scope_ss_id=$rs->fields['ss_id'];
		$exam_ss_id.=$rs->fields['ss_id']." ";
		$scope_id=$rs->fields['scope_id'];
		$subject_id=$rs->fields['subject_id'];
		$rate=$rs->fields['rate'];
		$print=$rs->fields['print'];
		$ss_id_print_arr[$scope_ss_id] = $print;
		$subject_arr[$scope_id] =$subject_id;
		$scope_arr[$scope_id][$subject_id]['ss_id'] =$scope_ss_id;
		$scope_arr[$scope_id][$subject_id]['rate'] =$rate;
		$scope_arr[$scope_id][$subject_id]['print'] =$print;
		$rs->MoveNext();
	}

	//該年級該班級該階段的成績
	$score_semester="score_semester_".$sel_year."_".$sel_seme;

	//如果每學期只設定一次學期平時成績且每階段評量比率皆不同時,比率為 100 - 各階段評量比率
	if ($yorn =='n'){
		$temp_ratio=0;
		for($i=0;$i<$performance_test_times;$i++)
			$temp_ratio += $test_ratio_all[$i][0];
		$temp_ratio = 100-$temp_ratio;
	}
	//全學期成績
	if($stage==255){
		$sql="select student_sn,ss_id,score,test_sort,test_kind from $score_semester where class_id='$class_id' ";
//echo $sql;
		$rs=$CONN->Execute($sql) or die($sql);
		while(!$rs->EOF){
			$test_sort=$rs->fields['test_sort'];
			$student_sn = $rs->fields[student_sn];
			$score = $rs->fields[score];
			$ss_id = $rs->fields[ss_id];
			$test_kind = $rs->fields[test_kind];
			if($score=="-100") $score="";
			if($test_sort==255 && $ss_id_print_arr["$ss_id"]=='' ){
				 $Sscore["$student_sn"]["$ss_id"]=$score;
//				 echo "$ss_id -- $score <br>";
			}
			else if ($ss_id_print_arr["$ss_id"]==1 ){
				if($yorn=='y'){
					$c_ts=$test_sort-1;
					if ($test_kind == '定期評量')
						$RR=($test_ratio_all[$c_ts][0])/100;
					else
						$RR=($test_ratio_all[$c_ts][1])/100;
					$temp_score["$student_sn"]["$ss_id"] += $score*$RR ;
					$Sscore["$student_sn"]["$ss_id"] = $temp_score["$student_sn"]["$ss_id"] ;
//					echo $ss_id."-- $test_sort".$test_kind."--".$RR."-- $score --". $Sscore["$student_sn"]["$ss_id"]."<br>";
				}
				else{
					$c_ts=$test_sort-1;
					if($test_sort==254) {
						//如果每學期只設定一次學期平時成績且每階段評量比率皆不同時
						$scoreA=$score*$temp_ratio/100;
						$temp_score["$student_sn"]["$ss_id"] += $scoreA ;
						$Sscore["$student_sn"]["$ss_id"] = $temp_score["$student_sn"]["$ss_id"];
					}
					else{
						if ($test_kind == '定期評量') {
							$scoreA=$score*$test_ratio_all[$c_ts][0]/100;
							$temp_score["$student_sn"]["$ss_id"] += $scoreA ;
							$Sscore["$student_sn"]["$ss_id"] = $temp_score["$student_sn"]["$ss_id"];
						}
						// add by chunkai 修改yorn=否時，顯示階段成績的錯誤
						if ($test_kind == '平時成績') {
							$scoreA=$score*$test_ratio_all[$c_ts][1]/100;
							$temp_score["$student_sn"]["$ss_id"] += $scoreA ;
							$Sscore["$student_sn"]["$ss_id"] = $temp_score["$student_sn"]["$ss_id"];
						}
						
					}

				}
			}

			$rs->MoveNext();
		}
		//echo "<pre>";print_r($Sscore);echo "</pre>";
	}
	// 非全學期記錄
        else{
		$sql="select student_sn,ss_id,score,test_kind from $score_semester where test_sort='$stage' and class_id='$class_id' ";
		$rs=$CONN->Execute($sql) or die($sql);
		while(!$rs->EOF){
			$student_sn = $rs->fields[student_sn];
			$score = $rs->fields[score];
			$ss_id = $rs->fields[ss_id];
			$test_kind = $rs->fields[test_kind];
			if ($score == -100)
				$score ='';
			if($yorn=='y'){
				if ($test_kind == '定期評量')
					$RR=($test_ratio_arr[0])/100;
				else
					$RR=($test_ratio_arr[1])/100;
				$Sscore["$student_sn"]["$ss_id"]=$Sscore["$student_sn"]["$ss_id"]+($score*$RR);

			//	echo $RR."--".$score." $test_ratio_a --".$Sscore["$student_sn"]["$ss_id"]."<BR>";
				//$Sscore["$student_sn"]["$ss_id"]=$score;
			}
			else {
				if ($stage == 254 or $test_kind == '定期評量')
					$Sscore["$student_sn"]["$ss_id"] =$score;

			}

			$rs->MoveNext();
		}
	}
	while(list($id,$val) = each($scope_arr)){
		$cc[]= count($val);
	}
	//if(max($cc)>1) $rowspan=" rowspan=2";
	//else $rowspan=" rowspan=1";
	$rowspan=" rowspan=2";
	$ss_title="<tr bgcolor='#F2E18E'><td$rowspan width='40' align='center'>座號</td><td$rowspan width='80' align='center'>姓名</td>";
	$ss_title_2 ='';
	reset($scope_arr);
	while(list($id,$val) = each($scope_arr)){
		$colspan= count($val)+1;
		//echo $colspan;
		//領域下有學科
		if ($subject_arr[$id]>0){
			$tol_rate =0;
			while (list($id_1,$val_1) = each($val)){
				if ($add_rate){
					$ss_title_2 .="<td width='80' align='center'>$subject_name_arr[$id_1]*$val_1[rate]</td>";
					$tol_rate += $val_1[rate];
				}
				else
					$ss_title_2 .="<td width='80' align='center'>$subject_name_arr[$id_1]</td>";

			}

					$ss_title_2.="<td  align='center'>平均</td>";  //dtes


			if ($add_rate)
				$ss_title.="<td  colspan=$colspan  align='center'>".$subject_name_arr[$id]."*$tol_rate</td>";
			else
				$ss_title.="<td  colspan=$colspan  align='center'>".$subject_name_arr[$id]."</td>";


		}

		else {
			if ($add_rate)
				$ss_title.="<td$rowspan width='40'  align='center'>$subject_name_arr[$id]*".$val[0][rate]."</td>\n";
			else
				$ss_title.="<td$rowspan width='40'  align='center'>$subject_name_arr[$id]</td>\n";

		}
		$temp_avg_list .="<td>$avg_arr[$id]</td>";
	}
        $ss_title.="<td align='center'$rowspan width='40'>總分</td><td align='center'$rowspan width='40'>平均</td></tr>\n";
        $ss_title.="<tr bgcolor='#FDF1B5'>$ss_title_2";
        $ss_title.="</tr>";

	//學生的成績
	$query = "select a.student_sn,a.stud_name,b.seme_num from stud_base a right join stud_seme b on a.student_sn=b.student_sn where  seme_year_seme='$year_seme' and seme_class ='$year_name' and (stud_study_cond=0)order by b.seme_num";
	$res = $CONN->Execute($query);
	$temp_score_list = array();
	while(!$res->EOF){
		$stud_name = $res->fields[stud_name];
		$student_sn = $res->fields[student_sn];
		$seme_num =  $res->fields[seme_num];
		$temp_score_list[$i][stud_name]=$stud_name;
		$temp_score_list[$i][seme_num]=$seme_num;
		reset ($scope_arr);
		$tol_score =0;
		$rate_num=0;
		while(list($id,$val) = each($scope_arr)){

			$dtes_score = 0;    //dtes
			$dtes_rate  = 0;    //dtes

			$colspan= count($val);
			//領域下有學科
			if ($subject_arr[$id]>0){
				while (list($id_1,$val_1) = each($val)){

					$temp_score_list[$i][score][]=$Sscore["$student_sn"][$val_1[ss_id]];   //dtes

					if ($add_rate){
						$temp_score_list[$i][tol_score]+= ($Sscore["$student_sn"][$val_1[ss_id]]*$val_1[rate]);
						$temp_score_list[$i][rate_num] +=$val_1[rate];

						$dtes_score += $Sscore["$student_sn"][$val_1[ss_id]]*$val_1[rate];
						$dtes_rate  += $val_1[rate];


					}
					else {
						$temp_score_list[$i][tol_score]+= ($Sscore["$student_sn"][$val_1[ss_id]]);
						$temp_score_list[$i][rate_num] ++;

						$dtes_score += $Sscore["$student_sn"][$val_1[ss_id]];
						$dtes_rate  += 1;



					}

				}
				$temp_score_list[$i][score][]=$dtes_score / $dtes_rate;    //dtes
			}
			else{
				$temp_score_list[$i][score][]=$Sscore["$student_sn"][$val[0][ss_id]];
				if ($add_rate){
					$temp_score_list[$i][tol_score]+= ($Sscore["$student_sn"][$val[0][ss_id]]*$val[0][rate]);
					$temp_score_list[$i][rate_num] +=$val[0][rate];
				}
				else {
					$temp_score_list[$i][tol_score]+= ($Sscore["$student_sn"][$val[0][ss_id]]);
					$temp_score_list[$i][rate_num] ++;

				}
			}

		}
		$how_big_arr[] =  $temp_score_list[$i][tol_score];
		$i++;
		$res->MoveNext();
	}



	$student_score_list ='';
	$tttt_arr = array();
	$statistics = array();
      	while(list($id,$val)=each($temp_score_list)) {
	       	$student_score_list .="<tr><td bgcolor='#B8FF91'>".$val[seme_num]."</td><td bgcolor='#CFFFC4'>".$val[stud_name]."</td>";
		$tttt = 0;

		while(list($id_1,$val_1) = each($val[score])){
			$student_score_list .= "<td width=80 align=right bgcolor='#FFFFFF'>".round($val_1,2)."</td>";
			$tttt_arr[$tttt] += $val_1;
			if($val_1==100) $statistics['100'][$tttt]++;
			elseif($val_1>=90) $statistics['90'][$tttt]++;
			elseif($val_1>=80) $statistics['80'][$tttt]++;
			elseif($val_1>=70) $statistics['70'][$tttt]++;
			elseif($val_1>=60) $statistics['60'][$tttt]++;
			elseif($val_1>=50) $statistics['50'][$tttt]++;
			elseif($val_1>=40) $statistics['40'][$tttt]++;
			elseif($val_1>=30) $statistics['30'][$tttt]++;
			elseif($val_1>=20) $statistics['20'][$tttt]++;
			elseif($val_1>=10) $statistics['10'][$tttt]++;
			else $statistics[0][$tttt]++;
			$tttt++;
		}
		//計算平均
		$avg_score = round($val[tol_score]/$val[rate_num],2);
		//計算名次
		$how_big_val = how_big2($val[tol_score],$how_big_arr);

		$student_score_list.="<td bgcolor='#B4BED3' align=right>".round($val[tol_score])."</td><td bgcolor='#CBD6ED' align=right>$avg_score</td></tr>\n";

	}
	//計算各科平均
	$statistics_list_average = '';
	$student_count = count($temp_score_list);
	while(list($id,$val) = each($tttt_arr))
		$statistics_list_average .="<td align=right>".round($val/$student_count,2)."</td>";

	//共有幾科
	$ccc = count($tttt_arr);

	$statistics_str ='';
	//計算成績分佈圖
	for($i=10;$i>=0;$i--) {
		$id = $i*10;
		if ($id<100)
			$score_id = $id."-".($id+10)."分";
		else
			$score_id = $id."分";

		$statistics_str .="<tr>
		<td width='40' bgcolor='#B8FF91'>&nbsp;</td>
		<td width='80' bgcolor='#CFFFC4'>$score_id</td>";
		for($j=0;$j<$ccc;$j++)
			$statistics_str .="<td width='80' bgcolor='#FFFFFF' align=right>".$statistics[$id][$j]."</td>";

		$statistics_str .= "<td width='80' bgcolor='#B4BED3'>&nbsp;</td>
		<td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
		</tr>";
	}

        settype($sel_year,"integer");
        $score_bar = $SCHOOL_BASE[sch_cname].$sel_year."學年度第".$sel_seme."學期".$show_class_year[$year_name].$show_stage[$stage]."成績表";

        settype($sel_year,"integer");
        if($fine_print!="1") echo "<a href='{$_SERVER['PHP_SELF']}?fine_print=1&sel_year=$sel_year&sel_seme=$sel_seme&year_name=$year_name&stage=$stage&add_normal=$add_normal&add_rate=$add_rate&temp_check=$temp_check'>友善列印</a>&nbsp;&nbsp;";
        if($fine_print!="1") echo "<a href='{$_SERVER['PHP_SELF']}?fine_print=1&sel_year=$sel_year&sel_seme=$sel_seme&year_name=$year_name&stage=$stage&sxw=1&add_normal=$add_normal&add_rate=$add_rate&temp_check=$temp_check'>轉成sxw檔</a>&nbsp;&nbsp;";
        if($fine_print!="1") echo "<a href='{$_SERVER['PHP_SELF']}?fine_print=1&sel_year=$sel_year&sel_seme=$sel_seme&year_name=$year_name&stage=$stage&doc=1&add_normal=$add_normal&add_rate=$add_rate&temp_check=$temp_check'>轉成doc檔</a>";
        if($fine_print==1) echo "<font size=+1>$score_bar</font>" ;
        if(($doc=="1")||($sxw=="1")){
            echo "<table  border='1' cellpadding='6' cellspacing='0'>";
        }
        else{
            echo "<table bgcolor=#0000ff border='0' cellpadding='6' cellspacing='1'>";
        }
        echo $ss_title;
        echo "$student_score_list";
	echo "<tr bgcolor=#FDC3F5>
		<td width='40'>&nbsp;</td>
		<td width='80'>各科平均</td>
		$statistics_list_average
		<td width='80'>&nbsp;</td>
		<td width='80'>&nbsp;</td>
		</tr>
        <tr>
		<td colspan=2><font color=#FFFFFF>成績分佈表</font></td>
		</tr>
		$statistics_str
	</table>";

}

//結束主網頁顯示區
if($fine_print!="1") echo "</td>";
if($fine_print!="1") echo "</tr>";
if($fine_print!="1") echo "</table>";
//echo "<table><tr>";
//while(list($id,$val)= each($avg_arr))
//	echo "$id--$val<BR>";


//程式檔尾
if($fine_print!="1")
	foot();
else
	echo "</body></html>";



