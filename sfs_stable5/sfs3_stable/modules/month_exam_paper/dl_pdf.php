<?php
// $Id: dl_pdf.php 5310 2009-01-10 07:57:56Z hami $
// 引入 SFS3 的函式庫
include "../../include/config.php";
// 引入您自己的 config.php 檔
require "config.php";
require('../../include/sfs_case_chinese.php');

// 認證
sfs_check();

//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$test_sort=($_POST['test_sort'])?"{$_POST['test_sort']}":"{$_GET['test_sort']}";
$class_num=($_POST['class_num'])?"{$_POST['class_num']}":"{$_GET['class_num']}";

class PDF extends PDF_Chinese
{
	//Page header
	function Header($TT)
	{
		global $TT;
		$this->SetFont('Big5','B',15);
		//Title
		$this->MultiCell(0,10,$TT,0,'C');
		//Line break
		$this->Ln(10);
	}

	//Page footer
	function Footer()
	{
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Big5','I',8);
		//Page number
		$this->Cell(0,10,'頁 '.$this->PageNo().'/{nb}',0,0,'C');
	}
	
	//Simple table
	function BasicTable($header,$data)
	{
		//Header
		$col_num=count($header);
		if($col_num>10) $this->SetFont('Big5','',10);
		//$col_width=round(170/$col_num,0);
		$col_width=2.2;
		$i=0;
		foreach($header as $col){
			if($i==1) $col_width_a[$i]=$col_width*strlen($col)+6;
			elseif($col=="總分") $col_width_a[$i]=$col_width*strlen($col)+2;
			elseif($col=="平均") $col_width_a[$i]=$col_width*strlen($col)+2;
			else $col_width_a[$i]=$col_width*strlen($col) ;
			$this->Cell($col_width_a[$i],7,$col,1);
			$i++;
		}	
		$this->Ln();
		//Data
		$this->SetFont('Big5','',10);
		foreach($data as $row)
		{
			$i=0;
			foreach($row as $col){
				$this->Cell($col_width_a[$i],6,$col,1);
				$i++;
			}	
			$this->Ln();
		}
	}	
}

//Instanciation of inherited class
//取出資料
$curr_year = curr_year();
$curr_seme = curr_seme();
if(sizeof($curr_year)<3) $curr_year="0".$curr_year;
$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));	
$class_info=curr_year()."學年度第".$curr_seme."學期".class_id_to_full_class_name($class_id);
$school_name=$school_long_name;	
$test_info="第".$test_sort."次定期考查";
$TT=$school_name.$class_info.$test_info;

//科目
$SS=class_id2subject($class_id);
foreach($SS as $ss_id => $subject_name){
	$subj_str.="$subject_name";
}

//產生pdf檔
$pdf=new PDF();
$pdf->Open();
$pdf->AddBig5Font();
$pdf->Header($TT);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Big5','',12); 
//科目
$SS=class_id2subject($class_id);
$header=array("座號","姓名");
foreach($SS as $ss_id => $subject_name){
	array_push($header,$subject_name);
}
array_push($header,"總分","平均","名次");

//找出該班學生流水號陣列
$st_array=class_id_to_student_sn($class_id);
$p=0;		
foreach ($st_array as $student_sn){			
	foreach($SS as $ss_id => $s_name){				
		//成績
		$score_b[$ss_id]=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
		if($score_b[$ss_id]==-100) $score_b[$ss_id]="";
		if($score_b[$ss_id]!="") {$count_b[$p]++; $count_stud[$ss_id]++;}
		$total_score_b[$p]=$total_score_b[$p]+$score_b[$ss_id];
		$calss_score[$ss_id]=$calss_score[$ss_id]+$score_b[$ss_id];
	}
	$avg_score_b[$p]=$total_score_b[$p]/$count_b[$p];
	$p++;		
}

$i=0;
$j=0;
foreach ($st_array as $student_sn){
	//找出座號，姓名，由學生流水號
	$classinfo_array=student_sn_to_classinfo($student_sn);
	$one_student[$i][]=$classinfo_array[2];
	$one_student[$i][]=$classinfo_array[4];
	foreach($SS as $ss_id => $subject_name){
		//成績
		$score=score_base($curr_year,$curr_seme,$student_sn,$ss_id,$test_kind="定期評量",$test_sort);
		if($score==-100) $score="";
		if($score!="") $count_subj[$j]++;
		$one_student[$i][]=$score;
		if($score) $total_score[$i]=$total_score[$i]+$score;
		$b++;
	}
	$avg_score[$i]=$total_score[$i]/$count_subj[$j];
	if($total_score[$i]) $avg_score_r[$i]=round($total_score[$i]/$count_subj[$j],2);
	//排名
	//echo $avg_score[$i].$avg_score_b."<br>";
	if($avg_score[$i]) $sort_name[$i]=sort_sort($avg_score[$i],$avg_score_b);
	//$t3.="<td bgcolor='#BED7FD'>$total_score[$i]</td><td bgcolor='#A3C7FD'>$avg_score_r[$i]</td><td bgcolor='#84A2CE'>$sort_name[$i]</td></tr>";	
	$one_student[$i][]=$total_score[$i];
	$one_student[$i][]=$avg_score_r[$i];
	$one_student[$i][]=$sort_name[$i];
	$i++;
	$j++;	
	$a++;
}
foreach($SS as $ss_id => $subject_name){
	if($calss_score[$ss_id]) $X[$ss_id]=round($calss_score[$ss_id]/$count_stud[$ss_id],2);
	//$one_student[]=$X[$ss_id];
}
//print_r($one_student);
//exit;

$pdf->BasicTable($header,$one_student);
$pdf->Output();
?>
