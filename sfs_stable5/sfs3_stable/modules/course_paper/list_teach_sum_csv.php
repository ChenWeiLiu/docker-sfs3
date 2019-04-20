<?php

// $Id: list_teach_sum.php 5671 2009-09-25 06:18:03Z infodaes $

/* 取得基本設定檔 */
include "config.php";
include "../../include/sfs_case_dataarray.php";

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$year_seme=$_GET['year_seme'];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期


$filename=$sel_year."學年度第".$sel_seme."學期教師配課總表.csv";
header("Content-disposition: attachment;filename=$filename");
header("Content-type: text/x-csv ; Charset=Big5");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

header("Expires: 0");


//截取教師姓名參照
$teacher_name=teacher_array();

// 取出班級名稱陣列
$class_name= class_base($year_seme);

//截取課表名稱
$sql= "select a.ss_id,a.link_ss,b.subject_name from score_ss a left join score_subject b on a.subject_id=b.subject_id where a.enable=1 and a.year=$sel_year and a.semester=$sel_seme";
$res=$CONN->Execute($sql) or trigger_error("錯誤訊息： $sql", E_USER_ERROR);
$subject_array=array();
while(!$res->EOF) {
	$ss_id=$res->fields['ss_id'];
	$subject_array[$ss_id]=$res->fields['subject_name']?$res->fields['subject_name']:$res->fields['link_ss'];
	$res->MoveNext();
}


//抓取最大星期日數與最大節數
$sql="select max(day) as day,max(sector) as sector from score_course where year=$sel_year and semester=$sel_seme";
$res=$CONN->Execute($sql) or trigger_error("錯誤訊息： $sql", E_USER_ERROR);
$max_day=$res->fields['day'];
$max_sector=$res->fields['sector'];

//抓取選定學期的課表資料
$sql="select course_id,teacher_sn,cooperate_sn,day,sector,ss_id,room,class_id,c_kind
	  from score_course where year='$sel_year' and semester='$sel_seme' order by teacher_sn,day,sector";
$res=$CONN->Execute($sql) or trigger_error("錯誤訊息： $sql", E_USER_ERROR);
$course_data=array();	  
while(!$res->EOF) {
	$teacher_sn=$res->fields['teacher_sn'];
	$day=$res->fields['day'];
	$sector=$res->fields['sector'];
	$class_data=explode('_',substr($res->fields['class_id'],-5));
	$course_data[$teacher_sn][$day][$sector]['ss_id']=$subject_array[$res->fields['ss_id']];
	$course_data[$teacher_sn][$day][$sector]['room']=$res->fields['room'];
	$course_data[$teacher_sn][$day][$sector]['c_kind']=$res->fields['c_kind']?'★':'';
	$course_data[$teacher_sn][$day][$sector]['ss_id']=$course_data[$teacher_sn][$day][$sector]['c_kind'].$course_data[$teacher_sn][$day][$sector]['ss_id'];
	$course_data[$teacher_sn][$day][$sector]['class_name']=$class_name[sprintf('%d%02d',$class_data[0],$class_data[1])];
	
	
	$cooperate_sn=$res->fields['cooperate_sn'];
	if($cooperate_sn){
		$course_data[$cooperate_sn][$day][$sector]['ss_id']=$subject_array[$res->fields['ss_id']];
		$course_data[$cooperate_sn][$day][$sector]['room']=$res->fields['room'];
		$course_data[$cooperate_sn][$day][$sector]['c_kind']=$res->fields['c_kind']?'★':'';
		$course_data[$cooperate_sn][$day][$sector]['ss_id']='*'.$course_data[$teacher_sn][$day][$sector]['c_kind'].$course_data[$teacher_sn][$day][$sector]['ss_id'];
		$course_data[$cooperate_sn][$day][$sector]['class_name']=$class_name[sprintf('%d%02d',$class_data[0],$class_data[1])];
	}
	
	
	$res->MoveNext();
}

//資料已經準備好  可已開始輸出了
$dow_array=array('1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'日');

$title1=",";
$title2="姓名,";
for($i=1;$i<=$max_day;$i++) {	
	$title1.='星期'.$dow_array[$i].',';
	for($j=1;$j<=$max_sector;$j++) {
		$title1.=',';
		$title2.=$i.'_'.$j.',';
	}
	$title1=substr($title1,0,-1);
}
echo substr($title1,0,-1)."\n";
echo substr($title2,0,-1)."\n";

foreach($course_data as $teacher_sn=>$data) {
	$teacher_data=$teacher_name[$teacher_sn].',';
	for($i=1;$i<=$max_day;$i++) {
		for($j=1;$j<=$max_sector;$j++) {
			$ss_id=$data[$i][$j]['ss_id'];
			$room=$data[$i][$j]['room'];
			$class_name=$data[$i][$j]['class_name'];
			$teacher_data.=$ss_id?"$ss_id($class_name)$room,":',';
		}
	}
	echo substr($teacher_data,0,-1)."\n";	
}

?>
