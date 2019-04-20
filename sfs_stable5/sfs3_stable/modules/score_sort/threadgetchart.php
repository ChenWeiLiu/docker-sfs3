<?php    
include "config.php";
sfs_check();
//global $strp,$sel_year,$sel_seme,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$h,$subject1,$class_year,$scopeall,$test;

     
$sel_year=$_POST['sel_year'];
$sel_seme=$_POST['sel_seme'];
$class_id=$_POST['class_id'];
$ss_id=$_POST['ss_id'];
$test_sort=$_POST['test_sort'];
$test_kind=$_POST['test_kind'];
$rate=$_POST['rate'];
$student_sn=$_POST['student_sn'];
$c_name=$_POST['c_name'];
$subject1=$_POST['subject1'];
$colorid=$_POST['colorid'];

$scopeall=$_POST['scopeall'];
$scopev=explode(",",$scopeall);


	if (substr($class_id,0,1)=="c")
	{
	$class_year=substr($class_id,3,1);

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

   
  function pr_view($scopeall)
  {
  
  global $CONN,$strp,$sel_year,$sel_seme,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$h,$subject1,$class_year;
  
  $str="";
  
  $t=$class_year; //國小
  if ($class_year>=7)$t=$class_year-7; //國中
  $tt=$t+1;


 for ($i=$sel_year-$t;$i<=$sel_year;$i++)
 {
 $tt--;
 $sel_semex1=1;
 
 $cid=$i."_".$sel_semex1;
 $score_semester="score_semester_".$i."_$sel_semex1";	
 
 
  $sql2="select distinct class_id from $score_semester WHERE student_sn='$student_sn' and class_id Like '$cid%' limit 1";
  $rs2=&$CONN->Execute($sql2);
  $classid1= $rs2->fields['class_id'];
  
  
  if ($classid1)
  {
  $getpr1[$i]=get_pr_from_student_sn($i,$sel_semex1,$classid1,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt,$subject1,$scopeall);
  
  
  }
  else
 {
  $getpr1[$i]=-1;

  }
  
  	  if ($getpr1[$i]==-1)
	  {
	  $getpr1[$i]=0;
	  $sel_semex1=iconv("UTF-8","BIG5","非本校");
	  }
  
  //$str.="{ label: \"$i-$sel_semex1\", y: $getpr1[$i] },";
  
  $str.="$i-$sel_semex1&$getpr1[$i]&";

 
   if ($i<$sel_year || ($i==$sel_year && $sel_seme>1))
  {
  $sel_semex2=2;
  $cid=$i."_".$sel_semex2;
  $score_semester="score_semester_".$i."_$sel_semex2";	
  
  $sql2="select distinct class_id from $score_semester WHERE student_sn='$student_sn' and class_id Like '$cid%' limit 1";
  $rs2=&$CONN->Execute($sql2);
  $classid2= $rs2->fields['class_id'];
  
  if ($classid2)
  {
	  
  $getpr2[$i]=get_pr_from_student_sn($i,$sel_semex2,$classid2,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt,$subject1,$scopeall);
  
  }
  else
  {
	 
	  $getpr2[$i]=-1;
	
  }
  
  if ($getpr2[$i]==-1)
  {
	  $getpr2[$i]=0;
	  $sel_semex2=iconv("UTF-8","BIG5","非本校");
  }
  
  
   //$str.="{ label: \"$i-$sel_semex2\", y: $getpr2[$i] },";
   
   $str.="$i-$sel_semex2&$getpr2[$i]&";
    
  }
  
  
 }
   

  return $str;  
	 
}

 
  
function pr_view_mix($scopeall)
{
 global $CONN,$sel_year,$sel_seme,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$h,$subject1,$class_year;
	  
 $t=$class_year; //國小
 if ($class_year>=7)$t=$class_year-7; //國中
 $tt=$t+1;
 $str=""; 

 for ($i=$sel_year-$t;$i<=$sel_year;$i++)
 {//for
 $tt--;
 $sel_semex1=1;
 $class_id=$i."_".$sel_semex1."_0".($class_year-$tt);
 $getpr1[$i]=get_pr_from_student_sn($i,$sel_semex1,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt,$subject1,$scopeall);

 if ($getpr1[$i]==-1){$sel_semex1=iconv("UTF-8","BIG5","非本校");$getpr1[$i]="0";}
 //$str.="{ label: \"$i-$sel_semex1\", y: $getpr1[$i] },";
 $str.="$i-$sel_semex1&$getpr1[$i]&";

  if ($i<$sel_year || ($i==$sel_year && $sel_seme>1))
  {
  $sel_semex2=2;
  $class_id=$i."_".$sel_semex2."_0".($class_year-$tt);
  $getpr2[$i]=get_pr_from_student_sn($i,$sel_semex2,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt,$subject1,$scopeall);
  if ($getpr2[$i]==-1){$sel_semex2=iconv("UTF-8","BIG5","非本校");$getpr2[$i]="0";}
 
  //$str.="{ label: \"$i-$sel_semex2\", y: $getpr2[$i] },";
  $str.="$i-$sel_semex2&$getpr2[$i]&";
  
  }
 }//for
	  
	return $str;
	
 }
  

   
  
if($sel_seme>0)//學期
{  


 $ax=explode("_",$class_id);
 $str="";
 
 if (!empty($ax[3])) //班級
 {
	 
		  $strp[0]=pr_view($scopev[0]);		  
		  $vxp[0]=$scopev[1];


 }
 else //全年級混合
 {
	 
		  
		  $strp[0]=pr_view_mix($scopev[0]);	 
		  $vxp[0]=$scopev[1];
  
 
 }
 
}
 
 
 
if(empty($sel_seme))//學年度
{


 if (substr($class_id,0,1)=="c")
 {//
	
 
 if (strlen($class_id)!=5) 
 {//
     
		  
		  $strp[0]=pr_view_mix($scopev[0]);	 
		  $vxp[0]=$scopev[1];

 
  }
  else
  {
	

     
		  
		  $strp[0]=pr_view_mix($scopev[0]);	 
		  $vxp[0]=$scopev[1];


 
   }	
 
 
 
 
 
 
 }
 else //各班
 {

		  
		  $strp[0]=pr_view($scopev[0]);
		  $vxp[0]=$scopev[1];

	
	
 }	



}

  

$vx=iconv("UTF-8","big5","各領域PR值");
		
		if (!empty($strp[0]))
        {
         $strp[0]=substr($strp[0],0,-1);
 
		}

	
	
	$xvv="";	
	
	$color=array("red","orange","pink","green","blue","#8FAABB","purple","black","#99DD00");
	
	

		if (!empty($strp[0]))
		{	

		$xvv.="$vxp[0]&$color[$colorid]&$strp[0]&";
		
		}

  
	$xvv=substr($xvv,0,-1);
	

	
	echo json_encode($xvv);


 ?>

