<?php
// $Id: stick.php 8298 2015-01-16 16:16:28Z smallduh $
include "config.php";
include "stick/stick-cfg.php";
include_once "stick/dl_pdf.php";

sfs_check();

//主選單設定
//$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year]; //目前學年
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme]; //目前學期
//由系統取得任教班級代號
$class_num=get_teach_class();
$class_all=class_num_2_all($class_num);
$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);
//echo $class_id;

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//執行動作判斷
if($pdf_file){
	readfile ($SFS_PATH_HTML.$UPLOAD_URL.basename($pdf_file), "r");
	exit;
	//http://".$_SERVER["SERVER_NAME"].$UPLOAD_URL.basename($pdf_file)
}

elseif($act=="dl_pdf" ){
	$sel_year=intval(substr($class_seme,0,-1));
	$sel_seme=intval(substr($class_seme,-1));
	$class_id=sprintf("%03d_%d_%02d_%02d",substr($class_seme,0,3),substr($class_seme,-1),substr($class_base,0,-2),substr($class_base,-2));
	$all_stud_array=get_stud_array($sel_year,$sel_seme,substr($class_base,0,-2),substr($class_base,-2),"sn","id");
	$L=0;
	foreach($all_stud_array as $student_sn => $stud_id){
		//班級個人資料
		$temp2=get_stud_base_array($class_id,$stud_id);
		//出缺席資料
		$temp3=get_abs_value($stud_id,$sel_year,$sel_seme,$mode="貼條");
		//缺席總日數
		$temp3['缺席總日數']=$temp3['事假']+$temp3['病假']+$temp3['曠課'];

		//總評與分數
		$temp5=get_nor_value($student_sn,$sel_year,$sel_seme,$class_id);
		//生活表現評量
		$temp6=get_performance_value($stud_id,$sel_year,$sel_seme);
		//取得學期資訊
		$temp7=get_all_days($sel_year,$sel_seme,$class_id);
		//成績資料
		$temp8=get_score_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
		//九年一貫成績資料
		$temp9=get_ss9_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);


		//計算學習領域總平均及等第
		$student_sn1=array($student_sn);
		$semes1[] = sprintf("%03d",$sel_year).$sel_seme;
		$year_name=intval(substr($class_id,7,-1));	
		$fin_score=cal_fin_score($student_sn1,$semes1,"",array($sel_year,$sel_seme,$year_name));
		$sm=get_all_setup("",$sel_year,$sel_seme,$year_name);
		$rule=explode("\n",$sm[rule]);
	
		$test = array_pop(array_pop($fin_score));
		$score_name=score2str($test[score],"",$rule);
		$test1 = array("學期學習領域成績"=>$test[score],"學期學習領域等第"=>$score_name);
	
		$temp8 = array_merge($temp8,$test1);
		$temp9 = array_merge($temp9,$test1);
		
	
		foreach($temp9 as $cx => $vx){
			if(substr($cx,3,4)=="語文" && substr($cx,-4)=="分數"){
				$temp99[$cx]=$vx;
			}
		}
		$cx2_w_total=0;
		$temp999=array();
		foreach($temp99 as $cx2 => $vx2){
			$cx2_w=substr($cx2,0,-4)."加權";
			$cx2_w_total=$cx2_w_total+$temp9[$cx2_w];
			$temp999['九_語文']=$temp999['九_語文']+($vx2*$temp9[$cx2_w]);
			//echo $vx2."---".$temp9[$cx2_w]."<br>";
		}
		$temp9['九_語文平均']=round($temp999['九_語文']/$cx2_w_total,2);
		$class=array(0=>intval(substr($class_seme,0,3)),1=>intval(substr($class_seme,-1)),3=>intval(substr($class_base,0,-2)));
		$temp9['九_語文等第']=score2str($temp9['九_語文平均'],$class);
		$temp_arr=array_merge($temp2,$temp3,$temp5,$temp6,$temp7,$temp8,$temp9);
		$header[$L]=$temp_arr['座號'];
		foreach($course as $key => $val){
			//if(in_array($val,$temp_arr))
			$cova[$val][$L]=$temp_arr[$val];
		}
		$L++;
	}
	$m=0;
	foreach($cova as $key1 => $val1){
		$n=0;
		foreach($val1 as $val2){
			$data[$m][$n]=$val2;
			$n++;
		}
		$m++;
	}

	//echo $wd;
	creat_pdf($title="",$header,$data,$comment1="",$comment2="",$ht,$wd);

}elseif($act=="send_ok"){
	//該班名單
	//print_r($ht);
	//echo "wd:".$wd;
	$sel_year=intval(substr($class_seme,0,-1));
	$sel_seme=intval(substr($class_seme,-1));
	$class_id=sprintf("%03d_%d_%02d_%02d",substr($class_seme,0,3),substr($class_seme,-1),substr($class_base,0,-2),substr($class_base,-2));
	$all_stud_array=get_stud_array($sel_year,$sel_seme,substr($class_base,0,-2),substr($class_base,-2),"sn","id");
	$L=0;
	foreach($all_stud_array as $student_sn => $stud_id){
		//班級個人資料
		$temp2=get_stud_base_array($class_id,$stud_id);
		//出缺席資料
		$temp3=get_abs_value($stud_id,$sel_year,$sel_seme,$mode="貼條");
		//缺席總日數
		$temp3['缺席總日數']=$temp3['事假']+$temp3['病假']+$temp3['曠課'];
		//總評與分數
		$temp5=get_nor_value($student_sn,$sel_year,$sel_seme,$class_id);
		//生活表現評量
		$temp6=get_performance_value($stud_id,$sel_year,$sel_seme);
		//取得學期資訊
		$temp7=get_all_days($sel_year,$sel_seme,$class_id);		
		//成績資料
		$temp8=get_score_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
		//九年一貫成績資料
		$temp9=get_ss9_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
		
		$student_sn1=array($student_sn);
		$semes1[] = sprintf("%03d",$sel_year).$sel_seme;
		$year_name=intval(substr($class_id,7,-1));	
		$fin_score=cal_fin_score($student_sn1,$semes1,"",array($sel_year,$sel_seme,$year_name));
	
		$sm=get_all_setup("",$sel_year,$sel_seme,$year_name);
		$rule=explode("\n",$sm[rule]);
	
		$test = array_pop(array_pop($fin_score));
		$score_name=score2str($test[score],"",$rule);
		$test1 = array("學期學習領域成績"=>$test[score],"學期學習領域等第"=>$score_name);
	
		$temp8 = array_merge($temp8,$test1);
		$temp9 = array_merge($temp9,$test1);
	
		foreach($temp9 as $cx => $vx){
			if(substr($cx,3,4)=="語文" && substr($cx,-4)=="分數"){
				$temp99[$cx]=$vx;
			}
		}
		$cx2_w_total=0;
		$temp999=array();
		foreach($temp99 as $cx2 => $vx2){
			$cx2_w=substr($cx2,0,-4)."加權";
			$cx2_w_total=$cx2_w_total+$temp9[$cx2_w];
			$temp999['九_語文']=$temp999['九_語文']+($vx2*$temp9[$cx2_w]);
			//echo $vx2."---".$temp9[$cx2_w]."<br>";
		}
		$temp9['九_語文平均']=round($temp999['九_語文']/$cx2_w_total,2);
		$class=array(0=>intval(substr($class_seme,0,3)),1=>intval(substr($class_seme,-1)),3=>intval(substr($class_base,0,-2)));
		//print_r($class);
		$temp9['九_語文等第']=score2str($temp9['九_語文平均'],$class);

		$temp_arr=array_merge($temp2,$temp3,$temp5,$temp6,$temp7,$temp8,$temp9);

		foreach($course as $key => $val){
			//if(in_array($val,$temp_arr)){
				//echo $temp_arr[座號].$temp_arr[學生姓名]." => ".$key." => ".$val." => ".$temp_arr[$val]."<br>";
				$LIST[num][$L]=$temp_arr['座號'];
				//echo "LIST[num][$L]=".$temp_arr[座號];
				$LIST[name][$L]=$temp_arr['學生姓名'];
				//echo "LIST[name][$L]=".$temp_arr[學生姓名];
				$LIST[cors][$L]=$val;
				//echo "LIST[cors][$L]=".$val;
				$cova[$val][$L]=$temp_arr[$val];

			//}
		}
		$L++;
	}


	$T1.="<tr><td style=' border-style:solid; border-width:thin ; vertical-align: top' >座號</td>";
	foreach($LIST[num] as $number){
		$T1.="<td style=' border-style:solid; border-width:thin'> $number </td>";
	}
	$T1.="</tr>";

	$T2.="<tr><td style=' border-style:solid; border-width:thin'>姓名</td>";
	foreach($LIST[name] as $st_name){
		$T2.="<td style=' border-style:solid; border-width:thin'> $st_name </td>";
	}
	$T2.="</tr>";


	$m=1;
	foreach($cova as $key1 => $val1){
		$T3.="<tr>";
		$T3.="<td style=' border-style:solid; border-width:thin'> $key1 </td>";
		$course_form.="<input type='hidden' name='course[$m]' value='$key1'>\n";
		$ht_form.="<input type='hidden' name='ht[$m]' value='{$ht[$m]}'>\n";
		foreach($val1 as $val2){
			if($val2=="") $val2="&nbsp;";
			if($key1=="導師評語及建議") $T3.="<td style=' border-style:solid; border-width:thin; vertical-align: top'><font size='-2'>$val2</font></td>";
			else $T3.="<td style=' border-style:solid; border-width:thin'><font size='-2'>$val2</font></td>";
		}
		$T3.="</tr>";
		$m++;
	}

    if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")) $CPDF="產生PDF";
    else $CPDF="下載PDF";

	$main="
	<table><tr><td>\n
		<form action='{$_SERVER['PHP_SELF']}' method='POST'>\n
			<input type='submit' name='submit' value='$CPDF'>\n
			<input type='hidden' name='act' value='dl_pdf'>\n
			<input type='hidden' name='class_seme' value='$class_seme'>\n
			<input type='hidden' name='class_base' value='$class_base'>\n
			$course_form
			$ht_form
			<input type='hidden' name='wd' value='$wd'>\n
		</form>\n
	</td></tr></table>
	<table style=' border-style:solid; border-width:thin' cellspacing='0' cellpadding='0'> $T1 $T2 $T3 </table>
	";
}else{
	$main=&score_paper_mainForm();
}


//秀出網頁
head("自訂成績單");
print_menu($menu_p);
echo $main;
foot();


//主要輸入畫面
function &score_paper_mainForm(){
	global $school_menu_p,$cols,$class_seme,$class_base,$tnc_arr,$chc_arr,$tc_arr,$phc_arr,$cyc_arr;


	//說明
	$readme=readme();

	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	//學期班級選單
	$menu=&seme_class_select();


	//指定表格數
	for($i=0;$i<=30;$i++){
		if($cols==$i) $selected[$i]=" selected";
		if($i==0) $cols_options.="<option value=''>格子數</option>";
		else $cols_options.="<option value=\"$i\"$selected[$i]> $i </option>";
	}
	//台南縣版
	if($cols=="tnc") $selected_tnc=" selected";
	$cols_options.="<option value='tnc' $selected_tnc>台南縣版_九</option>";
	//彰化縣版
	if($cols=="chc_1") $selected_chc_1=" selected";
	$cols_options.="<option value='chc_1' $selected_chc_1>彰化縣版_九低</option>";
	if($cols=="chc_2") $selected_chc_2=" selected";
	$cols_options.="<option value='chc_2' $selected_chc_2>彰化縣版_九中</option>";
	//台中市版-1
	if($cols=="tc_1") $selected_tc_1=" selected";
	$cols_options.="<option value='tc_1' $selected_tc_1>台中市版_九_低</option>";
	//台中市版-2
	if($cols=="tc_2") $selected_tc_2=" selected";
	$cols_options.="<option value='tc_2' $selected_tc_2>台中市版_九_中高</option>";
	//澎湖縣版
	if($cols=="phc") $selected_phc=" selected";
	//$cols_options.="<option value='phc' $selected_phc>澎湖縣版_中高</option>";
	if($cols=="phc_1") $selected_phc_1=" selected";
	$cols_options.="<option value='phc_1' $selected_phc_1>澎湖縣版_低</option>";
	if($cols=="phc_2") $selected_phc_2=" selected";
	$cols_options.="<option value='phc_2' $selected_phc_2>澎湖縣版_中高</option>";
	//嘉義縣版-1
	if($cols=="cyc_1") $selected_cyc_1=" selected";
	$cols_options.="<option value='cyc_1' $selected_cyc_1>嘉義縣版_九_低</option>";
	//嘉義縣版-2
	if($cols=="cyc_2") $selected_cyc_2=" selected";
	$cols_options.="<option value='cyc_2' $selected_cyc_2>嘉義縣版_九_中高</option>";
	//嘉義縣版-3
	if($cols=="cyc_3") $selected_cyc_3=" selected";
	$cols_options.="<option value='cyc_3' $selected_cyc_3>嘉義縣版_中高</option>";
	
	$work0="
	<form action='{$_SERVER['PHP_SELF']}' method='POST' name='work0'>
	<input type='hidden' name='class_seme' value='$class_seme'>
	<input type='hidden' name='class_base' value='$class_base'>
	<select name='cols' onchange=\"this.form.submit()\">$cols_options</select><br>\n
	</form>";


	//科目選單
	//echo $class_seme.$class_base;
	$year=substr($class_seme,0,3);
	$seme=substr($class_seme,-1,1);
	$cyear=substr($class_base,0,-2);
	if($year && $seme && $cyear){
		$ss_array=ss_array($year,$seme,$cyear,$class_id="");
		foreach($ss_array as $k => $v){
			foreach($v as $k2 => $v2){
				if(!empty($k2) && $k2=="name") {
					$v21=$v2."分數";
					$course_options_arr[$v21]=$v21;
				}
			}
		}
		//print_r($course_options_arr);
	}
	//再加上九年一貫的科目
	$class_id=$class_base;
	$ss9_array=get_ss9_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
	foreach($ss9_array as $k3 => $v3){
		if(substr($k3,-4)=="分數") {
			//$course_options.="<option value=$k3 STYLE='background-color: #B6BFFB;  color: #F71CFF'>$k3</option>\n";
			$course_options_arr[$k3]=$k3;
			//$course_options.="<option value=".substr($k3,0,-4)."等第 STYLE='background-color: #B6BFFB;  color: #F71CFF'>".substr($k3,0,-4)."等第</option>\n";
			$k31=substr($k3,0,-4)."等第";
			$course_options_arr[$k31]=$k31;
		}
	}
	//print_r($course_options_arr);
	//$course_options.="<option value='九_語文平均' STYLE='background-color: #B6BFFB;  color: #F71CFF'>九_語文平均</option>\n";
	$course_options_arr['九_語文平均']='九_語文平均';
	//$course_options.="<option value='九_語文等第' STYLE='background-color: #B6BFFB;  color: #F71CFF'>九_語文等第</option>\n";
	$course_options_arr['九_語文等第']='九_語文等第';

//學期成績
$course_options_arr['學期學習領域成績']='學期學習領域成績';
$course_options_arr['學期學習領域等第']='學期學習領域等第';
	
	//再加上日常生活表現
	//$course_options.="<option value='表現分數' STYLE='background-color: #B4FF8F;  color: #F71CFF'>生活表現分數</option>\n";
	$course_options_arr['表現分數']='表現分數';
	//$course_options.="<option value='表現等第' STYLE='background-color: #B4FF8F;  color: #F71CFF'>生活表現等第</option>\n";
	$course_options_arr['表現等第']='表現等第';
	//再加上出缺席紀錄
	//$course_options.="<option value='上課日數' STYLE='background-color: #FBF292;  color: #F71CFF'>上課日數</option>\n";
	$course_options_arr['上課日數']='上課日數';
	//$course_options.="<option value='事假' STYLE='background-color: #FBF292;  color: #F71CFF'>事假日數</option>\n";
	$course_options_arr['事假']='事假';
	//$course_options.="<option value='病假' STYLE='background-color: #FBF292;  color: #F71CFF'>病假日數</option>\n";
	$course_options_arr['病假']='病假';
	//$course_options.="<option value='曠課' STYLE='background-color: #FBF292;  color: #F71CFF'>曠課日數</option>\n";
	$course_options_arr['曠課']='曠課';
	//$course_options.="<option value='缺席總日數' STYLE='background-color: #FBF292;  color: #F71CFF'>缺席總日數</option>\n";
	$course_options_arr['缺席總日數']='缺席總日數';
	//再加上評語
	//$course_options.="<option value='導師評語及建議' STYLE='background-color: #F1D4B5;  color: #5D7FB2'>導師評語及建議</option>\n";
	$course_options_arr['導師評語及建議']='導師評語及建議';
	//再加上空白
	//$course_options.="<option value='空白'>空白</option>";
	$course_options_arr['空白']='空白';
	if($cols){
		$j=1;
		foreach($course_options_arr as $coak => $coav){
			$course_options.="<option value='$coak'>$coav</option>\n";
			$j++;
		}
		if($cols=="tnc"){//台南縣選單
			for($j=1;$j<=count($tnc_arr);$j++){
				$add_course_options="";
				$c=array();
				foreach($tnc_arr as $a => $b){
					if($j==($a+1)) {
						$add_course_options="<option value='$b[0]' selected>$b[0]</option>";
						$c[$j]=$b[1];
						$d[$j]=$b[2];
					}
				}
				if($j==1) $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm , 寬<input type='text' name='wd' size='2' value='{$d[$j]}'>mm<br>\n";
				else $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm<br>\n";
			}
		}elseif($cols=="chc_1" ||$cols=="chc_2"){//彰化縣選單
			for($j=1;$j<=count($chc_arr);$j++){
				$add_course_options="";
				$c=array();
				foreach($chc_arr as $a => $b){
					if($j==($a+1)) {
						$add_course_options="<option value='$b[0]' selected>$b[0]</option>";
						$c[$j]=$b[1];
						$d[$j]=$b[2];
					}
				}
				if($j==1) $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm , 寬<input type='text' name='wd' size='2' value='{$d[$j]}'>mm<br>\n";
				else $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm<br>\n";
			}
		}elseif($cols=="tc_1" or $cols=="tc_2"){//台中市選單
			for($j=1;$j<=count($tc_arr);$j++){
				$add_course_options="";
				$c=array();
				foreach($tc_arr as $a => $b){
					if($j==($a+1)) {
						$add_course_options="<option value='$b[0]' selected>$b[0]</option>";
						$c[$j]=$b[1];
						$d[$j]=$b[2];
					}
				}
				if($j==1) $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm , 寬<input type='text' name='wd' size='2' value='{$d[$j]}'>mm<br>\n";
				else $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm<br>\n";
			}
		}elseif($cols=="phc_1" ||$cols=="phc_2"){//澎湖縣選單
			for($j=1;$j<=count($phc_arr);$j++){
				$add_course_options="";
				$c=array();
				foreach($phc_arr as $a => $b){
					if($j==($a+1)) {
						$add_course_options="<option value='$b[0]' selected>$b[0]</option>";
						$c[$j]=$b[1];
						$d[$j]=$b[2];
					}
				}

				if($j==1) $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm , 寬<input type='text' name='wd' size='2' value='{$d[$j]}'>mm<br>\n";
				else $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm<br>\n";
			}			
		}elseif($cols=="cyc_1" or $cols=="cyc_2" or $cols=="cyc_3"){//嘉義縣選單
			for($j=1;$j<=count($cyc_arr);$j++){
				$add_course_options="";
				$c=array();
				foreach($cyc_arr as $a => $b){
					if($j==($a+1)) {
						$add_course_options="<option value='$b[0]' selected>$b[0]</option>";
						$c[$j]=$b[1];
						$d[$j]=$b[2];
					}
				}
				if($j==1) $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm , 寬<input type='text' name='wd' size='2' value='{$d[$j]}'>mm<br>\n";
				else $course.="<select name='course[$j]'>$add_course_options $course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='{$c[$j]}'>mm<br>\n";
			}

		}else{
			for($j=1;$j<=$cols;$j++){
				if($j==1) $course.="<select name='course[$j]'>$course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='6'>mm , 寬<input type='text' name='wd' size='2' value='11'>mm<br>\n";
				else $course.="<select name='course[$j]'>$course_options</select> , 高<input type='text' name='ht[$j]' size='2' value='6'>mm<br>\n";
			}
		}
	}
	if($cols){
		$work="
		<form action='{$_SERVER['PHP_SELF']}' method='POST' name='work1'>
		<input type='hidden' name='class_seme' value='$class_seme'>
		<input type='hidden' name='class_base' value='$class_base'>
		$course
		<input type='hidden' name='act' value='send_ok'>
		<input type='submit' value='確定'>
		<input type='reset' value='取消'>

		</form>";
	}

	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='4' bgcolor='#C0C0C0' class='small' width='100%'>
	<tr bgcolor='#FFFFFF'>
	<td valign=top>$menu $work0 $work</td>
	<td valign=top width='50%' bgcolor='#DFE5FF'>$readme</td>
	</tr>
	</table>
	";
	return $main;
}

function readme(){
	$main="
	成績貼條說明
	<ol style='line-height:2'>
	<li>選擇要製作的學年度與學期</li>
	<li>選擇要製作的班級</li>
	<li>自我設定您的成績貼條所須得格子數（請參考貴校的學籍資料紀錄表）</li>
	<li>指定您的每一個格子的高度和全體的寬度，但在導師評語與建議所設定的高度則為每一個字元所佔的高度，建議不要設超過其餘格子的高度，4mm應該差不多</li>
	<li>選擇每一個格子所要顯示的科目成績，『空白』表示這個格子不顯示任何資料，科目若您設重複了程式會自動去除重複者</li>
	<li>按下確定，系統會將結果顯示網頁上</li>
	<li>按『下載PDF』則可將成績貼條以pdf檔的方式下載回來，再用pdf的reader打開即可</li>
	<li>您所下載的pdf成績貼條，除了第一列為座號之外，其餘的就是您所設定的科目成績，您可直接將其剪下貼到學籍資料紀錄表，隔子的大小已經照您剛才所指定的設好了！</li>
	</ol>
	";
	return $main;
}
function &seme_class_select(){
	global $CONN,$class_seme,$class_base,$class_id;
	//學年學期班級選單
	$class_seme_array=get_class_seme();
	$class_seme_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form1'>\n
	<select  name='class_seme' onchange='this.form.submit()'>\n";
	$i=0;
	foreach($class_seme_array as $k => $v){
		if(!$class_seme) $class_seme=sprintf("%03d%d",curr_year(),curr_seme());
		$selected[$i]=($class_seme==$k)?" selected":" ";
		$class_seme_select.="<option value='$k'$selected[$i] >$v</option> \n";
		$i++;
	}
	$class_seme_select.="</select></form>\n";
	
	//拆解將取得的班級$class_id 
	$test_1 = explode("_",$class_id);
	$class_base = ((int)$test_1[2]).$test_1[3];

	/*
	$class_base_array=class_base($class_seme);
	$class_base_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form2'>\n
	<select  name='class_base' onchange='this.form.submit()'>\n";
	$j=0;
	foreach($class_base_array as $k2 => $v2){
		if(!$class_base) $class_base=$k2;
		$selected2[$j]=($class_base==$k2)?" selected":" ";
		$class_base_select.="<option value='$k2'$selected2[$j] >$v2</option> \n";
		$j++;
	}
	*/
	
	$class_base_select.="</select><input type='hidden' name='class_seme' value='$class_seme'></form>\n";
	//$menu="<td nowrap width='1%' align='left'> $class_seme_select </td><td nowrap width='1%' align='left'> $class_base_select </td>";
	$menu=$class_seme_select.$class_base_select;
	//$class_num=$class_base;
	$curr_year = substr($class_seme,0,-1);
	$curr_seme =  substr($class_seme,-1);
	return $menu;
}
?>
