<?php

// $Id: report1.php qfon $

/* 取得設定檔 */

include "config.php";

// 認證檢查
sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}





//只限當學期
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();

$stud_id=$_SESSION['session_log_id'];


//取得登入學生的學號和流水號
$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='".$stud_id."'";
$res=$CONN->Execute($query);
$student_sn=$res->fields['student_sn'];
if ($student_sn) {
	$query="select * from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query);
	if ($res->fields['stud_study_cond']!="0") {
		$student_sn="";
	} else {
		$stud_study_year=$res->fields['stud_study_year'];
	}
}



$main=&mainForm();

//印出檔頭
head("缺曠課明細查詢");

//模組選單
print_menu($menu_p,$linkstr);

if($stud_view_self_absent) echo $main;
foot();

//主要輸入畫面
function &mainForm(){
	global $CONN,$stud_id,$student_sn,$stud_study_year;

	$sql = "select year,semester,class_id,date,absent_kind,section from stud_absent where stud_id='$stud_id' and year>='$stud_study_year' order by year,semester,date";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
	
	while (!$rs->EOF) {
		$absent_kind=$rs->fields['absent_kind'];
		$class_id = $rs->fields['class_id'];
		$date = $rs->fields['date'];
		$section = $rs->fields['section'];
		$semester = $rs->fields['year'].'-'.$rs->fields['semester'];
		
		
		if ($section=="allday")$sectionx="1日";
		else if ($section=="uf")$sectionx="升旗";
		else if ($section=="df")$sectionx="降旗";
		else $sectionx="第".$section."節";
		$cx=explode("_",$class_id);
		
		if ($cx[2]=="07" || $cx[2]=="01")$cx[2]="1";
		if ($cx[2]=="08" || $cx[2]=="02")$cx[2]="2";
		if ($cx[2]=="09" || $cx[2]=="03")$cx[2]="3";
		if ($cx[2]=="04")$cx[2]="4";
		if ($cx[2]=="05")$cx[2]="5";
		if ($cx[2]=="06")$cx[2]="6";
		
		$cx[3]=get_class_name($class_id);
		/*
		$colorz="white";
		if ($absent_kind=="事假")$colorz="#FEFED7";
		if ($absent_kind=="病假")$colorz="#FEFEC4";
		if ($absent_kind=="曠課")$colorz="#FEFEB1";
		if ($absent_kind=="其他")$colorz="#FEFE8B";
		*/
		
		$datas[$semester][$date][$absent_kind].="$section,";
		//$main0.="<tr  align='center'><td bgcolor='$colorz'>$cx[0]學年度第{$cx[1]}學期</td><td bgcolor='$colorz'>$cx[2]年$cx[3]班</td><td bgcolor='$colorz'>$date</td><td bgcolor='$colorz'>$absent_kind $sectionx</td></tr>";
		
		$rs->MoveNext();
	}


	
	
	/*
	echo "<pre>";
	print_r($datas);
	echo "</pre>";
	*/
	
	//$main0.="<li>學期：$semester</li>";
	$main0.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>";
	$main0.="<tr align='center' bgcolor='#C6D7F2'><td>學期</td><td>日期</td><td>類別</td><td>節次</td></tr>"; //<td>就讀班級</td>
	foreach($datas as $semester => $v) {
		$detail='';
		foreach($v as $date => $data) {
			foreach( $data as $kind => $value ) {
				$value=substr($value,0,-1);
				$detail.="<tr align='center' bgcolor='$colorz'><td>$date</td><td>$kind</td><td>$value</td></tr>";
				$rowspan++;				
			}
		}
		$rowspan++;	
		$main0.="<tr align='center'><td rowspan='$rowspan'>$semester</td></tr>";
		$main0.=$detail;
	}
	$main0.="</table>";
	
	/*
	echo "<textarea>";
	echo $main0;
	echo "</textarea>";
	exit;
	*/
	
	/*
	while (!$rs->EOF) {
		$absent_kind=$rs->fields['absent_kind'];
		$class_id = $rs->fields['class_id'];
		$date = $rs->fields['date'];
		$section = $rs->fields['section'];
		
		
		if ($section=="allday")$sectionx="1日";
		else if ($section=="uf")$sectionx="升旗";
		else if ($section=="df")$sectionx="降旗";
		else $sectionx="第".$section."節";
		$cx=explode("_",$class_id);
		
		if ($cx[2]=="07" || $cx[2]=="01")$cx[2]="1";
		if ($cx[2]=="08" || $cx[2]=="02")$cx[2]="2";
		if ($cx[2]=="09" || $cx[2]=="03")$cx[2]="3";
		if ($cx[2]=="04")$cx[2]="4";
		if ($cx[2]=="05")$cx[2]="5";
		if ($cx[2]=="06")$cx[2]="6";
		
		$cx[3]=get_class_name($class_id);
		$colorz="white";
		if ($absent_kind=="事假")$colorz="#FEFED7";
		if ($absent_kind=="病假")$colorz="#FEFEC4";
		if ($absent_kind=="曠課")$colorz="#FEFEB1";
		if ($absent_kind=="其他")$colorz="#FEFE8B";
		$main0.="<tr  align='center'><td bgcolor='$colorz'>$cx[0]學年度第{$cx[1]}學期</td><td bgcolor='$colorz'>$cx[2]年$cx[3]班</td><td bgcolor='$colorz'>$date</td><td bgcolor='$colorz'>$absent_kind $sectionx</td></tr>";
		$rs->MoveNext();
		
		
	}
     $main0.="</table>";
	*/
	return $main0;
}

//取得班級名稱
function get_class_name($class_id){
	global $CONN;

	$sql_select = "select c_name from school_class where class_id='$class_id' and enable='1'";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
    while (!$recordSet->EOF) {
		$c_name=$recordSet->fields['c_name'];
		$recordSet->MoveNext();
	}
	return $c_name;
}



?>
