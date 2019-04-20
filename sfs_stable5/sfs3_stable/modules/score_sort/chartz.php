<?php

include "config.php";
sfs_check();


$sel_year=$_POST['sel_year'];
$sel_seme=$_POST['sel_seme'];
$class_id=$_POST['class_id'];
$ss_id=$_POST['ss_id'];
$test_sort=$_POST['test_sort'];
$test_kind=$_POST['test_kind'];
$rate=$_POST['rate'];
$student_sn=$_POST['student_sn'];
$c_name=$_POST['c_name'];



//echo $class_id." ".$sel_seme."";

	if (substr($class_id,0,1)=="c")
	{
	$class_year=substr($class_id,3,1);
	//echo "\$class_year".$class_year."<br>";
	}
	else
	{
		
	 if (substr($class_id,3,1)=="_")
	 {
		 $class_year=substr($class_id,7,1);
	 }	
	 else
	 {
		 $class_year=substr($class_id,0,1);
	 }
	 
	}

  //ignore_user_abort(true);
  //set_time_limit(0);
  
  
  
if($sel_seme>0)//學期
{  

 $ax=explode("_",$class_id);
 $str="";
 
 if (!empty($ax[3])) //班級
 {
	 
  if ($class_year>=7)$t=$class_year-7; 
  $tt=$t+1;
  
 for ($i=$sel_year-$t;$i<=$sel_year;$i++)
 {
 $tt--;
 $sel_semex=1;
 
 $cid=$i."_".$sel_semex;
 $score_semester="score_semester_".$i."_$sel_semex";	
  
  $sql2="select class_id from $score_semester WHERE student_sn='$student_sn' and class_id Like '$cid%' limit 1";
  $rs2=&$CONN->Execute($sql2);
  $classid= $rs2->fields['class_id'];
 $getpr[$i]=get_pr_from_student_sn($i,$sel_semex,$classid,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt);
 if (!empty($getpr[$i]))$str.="{ label: \"$i ($sel_semex)\", y: $getpr[$i] },";
 
 
   if ($i<$sel_year || ($i==$sel_year && $sel_seme>1))
  {
  $sel_semex=2;
  $cid=$i."_".$sel_semex;
  $score_semester="score_semester_".$i."_$sel_semex";	
  
  $sql2="select class_id from $score_semester WHERE student_sn='$student_sn' and class_id Like '$cid%' limit 1";
  $rs2=&$CONN->Execute($sql2);
  $classid= $rs2->fields['class_id'];
  $getpr[$i]=get_pr_from_student_sn($i,$sel_semex,$classid,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt);
  if (!empty($getpr[$i]))$str.="{ label: \"$i ($sel_semex)\", y: $getpr[$i] },";
  }
 }
 
 
 
 
	 
 }
 else //全年級
 {
 if ($class_year>=7)$t=$class_year-7; 
 $tt=$t+1;
 $str=""; 

 for ($i=$sel_year-$t;$i<=$sel_year;$i++)
 {
 $tt--;
 $sel_semex=1;
 $class_id=$i."_".$sel_semex."_0".($class_year-$tt);
 $getpr[$i]=get_pr_from_student_sn($i,$sel_semex,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt);
 if (!empty($getpr[$i]))$str.="{ label: \"$i (1)\", y: $getpr[$i] },";

  if ($i<$sel_year || ($i==$sel_year && $sel_seme>1))
  {
  $sel_semex=2;
  $class_id=$i."_".$sel_semex."_0".($class_year-$tt);
  $getpr[$i]=get_pr_from_student_sn($i,$sel_semex,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt);
  if (!empty($getpr[$i]))$str.="{ label: \"$i (2)\", y: $getpr[$i] },";
  }
 }
 
 }
 
}
 
if(empty($sel_seme))//學年度
{

if (substr($class_id,0,1)=="c")
{//混合

//echo "\$class_id:".$class_id;

if (strlen($class_id)!=5)
{

if ($class_year>=7)$t=$class_year-7; 
 $tt=$t+1;
  
 for ($i=$sel_year-$t;$i<=$sel_year;$i++)
 {
 $tt--;
 $sel_semex=2;

 $score_semester="score_semester_".$i."_$sel_semex";	
  
  $sql2="select class_id from $score_semester WHERE student_sn='$student_sn' limit 1";
  $rs2=&$CONN->Execute($sql2);
  $classid= $rs2->fields['class_id'];
  
  //echo $classid." ";
 $getpr[$i]=get_pr_from_student_sn($i,$sel_seme,$classid,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt);
 if (!empty($getpr[$i]))$str.="{ label: \"$i \", y: $getpr[$i] },";
 
 
 
 
 }




 
}
else
{
	
	
 if ($class_year>=7)$t=$class_year-7;
 $tt=$t+1;
 for ($i=$sel_year-$t;$i<=$sel_year;$i++)
 {
	$tt--;
	$getpr[$i]=get_pr_from_student_sn($i,$sel_seme,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt);
    if (!empty($getpr[$i]))$str.="{ label: \"$i\", y: $getpr[$i] },";

 }
 
 }	
 
}
else //各班
{
	
 if ($class_year>=7)$t=$class_year-7; 
 $tt=$t+1;
  
 for ($i=$sel_year-$t;$i<=$sel_year;$i++)
 {
 $tt--;
 $sel_semex=2;

 $score_semester="score_semester_".$i."_$sel_semex";	
  
  $sql2="select class_id from $score_semester WHERE student_sn='$student_sn' limit 1";
  $rs2=&$CONN->Execute($sql2);
  $classid= $rs2->fields['class_id'];
  
 // echo $classid." ";
 $getpr[$i]=get_pr_from_student_sn($i,$sel_seme,$classid,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt);
 if (!empty($getpr[$i]))$str.="{ label: \"$i \", y: $getpr[$i] },";
 
 
 
 
 }
	
	
	
	
	
}	

}

  $str=substr($str,0,-1);
/*
  //echo $str;
  $ty=explode("},",$str);
  $arr = array();
  
  for($i=0;$i<count($ty);$i++)
  {
  if ($i<count($ty)-1)
  {
	  $arr[$i] = $ty[$i]."}";
  }
  else
  {
	  $arr[$i] = $ty[$i];
  }
  }
  //$cart = array($arr);
  */
echo json_encode($str);

  
  ?>