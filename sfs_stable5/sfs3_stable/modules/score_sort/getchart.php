<?php    
include "config.php";
sfs_check();
global $strp,$sel_year,$sel_seme,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$h,$subject1,$class_year,$scopeall,$test;

     
$sel_year=$_REQUEST['sel_year'];
$sel_seme=$_REQUEST['sel_seme'];
$class_id=$_REQUEST['class_id'];
$ss_id=$_REQUEST['ss_id'];
$test_sort=$_REQUEST['test_sort'];
$test_kind=$_REQUEST['test_kind'];
$rate=$_REQUEST['rate'];
$student_sn=$_REQUEST['student_sn'];
$c_name=$_REQUEST['c_name'];
$subject1=$_REQUEST['subject1'];
$scopeall=$_REQUEST['scopeall'];
//print_r($scopeall);

 $t = microtime();

//print_r($scopeall);

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
  
  $str.="{ label: \"$i-$sel_semex1\", y: $getpr1[$i] },";

 
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
  
  
   $str.="{ label: \"$i-$sel_semex2\", y: $getpr2[$i] },";
    
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
 $str.="{ label: \"$i-$sel_semex1\", y: $getpr1[$i] },";

  if ($i<$sel_year || ($i==$sel_year && $sel_seme>1))
  {
  $sel_semex2=2;
  $class_id=$i."_".$sel_semex2."_0".($class_year-$tt);
  $getpr2[$i]=get_pr_from_student_sn($i,$sel_semex2,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$tt,$subject1,$scopeall);
  if ($getpr2[$i]==-1){$sel_semex2=iconv("UTF-8","BIG5","非本校");$getpr2[$i]="0";}
 
  $str.="{ label: \"$i-$sel_semex2\", y: $getpr2[$i] },";
  }
 }//for
	  
	return $str;
	
 }
  
  
if (is_array($scopeall))
{
  	for($i=0;$i<count($scopeall);$i++)
	{
		$strp[$i]="";
	}
}
else
{
	$strp[0]="";
}





   
  
if($sel_seme>0)//學期
{  



 $ax=explode("_",$class_id);
 $str="";
 
 if (!empty($ax[3])) //班級
 {
	 
 	
		 if (is_array($scopeall))
		 {
		  for($i=0;$i<count($scopeall);$i++)
		  {
             
		  $scopev=explode(",",$scopeall[$i]);
		  //$strp[$i]=pr_view($sel_year,$sel_seme,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$h,$subject1,$class_year,$scopev[0]);
		  $strp[$i]=pr_view($scopev[0]);
		  
		  $vxp[$i]=$scopev[1];
		
	      }
		  
		  
		  
		 }
		 else
		 {
		  $strp[0]=pr_view("");
		  $vxp[0]="";
		 }
		 
		


 }
 else //全年級混合
 {

		if (is_array($scopeall))
		 {
		  for($i=0;$i<count($scopeall);$i++)
		  {
     
		  $scopev=explode(",",$scopeall[$i]);
		  $strp[$i]=pr_view_mix($scopev[0]);	 
		  $vxp[$i]=$scopev[1];
			  
	      }
		 }
		 else
		 {
		  $strp[0]=pr_view_mix("");	 
		  $vxp[0]="";
		 }
   
 
   
 
 }
 
}
 
 
 
if(empty($sel_seme))//學年度
{


 if (substr($class_id,0,1)=="c")
 {//
	
 
 if (strlen($class_id)!=5) 
 {//

 
  		if (is_array($scopeall))
		 {
		  for($i=0;$i<count($scopeall);$i++)
		  {
     
		  $scopev=explode(",",$scopeall[$i]);
		  $strp[$i]=pr_view_mix($scopev[0]);	 
		  $vxp[$i]=$scopev[1];
			  
	      }
		 }
		 else
		 {
		  $strp[0]=pr_view_mix("");	 
		  $vxp[0]="";
		 }


 
  }
  else
  {
	

      	if (is_array($scopeall))
		 {
		  for($i=0;$i<count($scopeall);$i++)
		  {
     
		  $scopev=explode(",",$scopeall[$i]);
		  $strp[$i]=pr_view_mix($scopev[0]);	 
		  $vxp[$i]=$scopev[1];
			  
	      }
		 }
		 else
		 {
		  $strp[0]=pr_view_mix("");	 
		  $vxp[0]="";
		 }



 
   }	
 
 
 
 
 }
 else //各班
 {
		 if (is_array($scopeall))
		 {
		  for($i=0;$i<count($scopeall);$i++)
		  {
     
		  $scopev=explode(",",$scopeall[$i]);
		  $strp[$i]=pr_view($scopev[0]);
		  $vxp[$i]=$scopev[1];
			  
	      }
		 }
		 else
		 {
		  $strp[0]=pr_view("");
		  $vxp[0]="";
		 }
	

	
	
 }	



}


if ($student_sn)
{
$sa=student_sn_to_classinfo2($student_sn,$sel_year,$sel_seme);	
$sname=$sa[4];
$vx=iconv("UTF-8","BIG5","PR值如下：");
$vx1=iconv("UTF-8","BIG5","年");
$vx2=iconv("UTF-8","BIG5","班");
$vx3=iconv("UTF-8","BIG5","號");

if ($sa[0]>6)$sa[0]=$sa[0]-6;
$vx=$sa[0].$vx1.$sa[1].$vx2.$sa[2].$vx3." ".$sname.$vx;
}




   if (is_array($scopeall))
   {
 	 for($i=0;$i<count($scopeall);$i++)
	 {
		
		if (!empty($strp[$i]))
        {
         $strp[$i]=substr($strp[$i],0,-1);
 
		}
	 } 
   }
   else
   {
	   $strp[$i]=substr($strp[$i],0,-1);
   }
	
	
	$xvv="";	
	
	$color=array("red","orange","pink","green","blue","#8FAABB","purple","black","#99DD00");
	
	
	if (is_array($scopeall))
  {
	for($i=0;$i<count($scopeall);$i++)
	{
		if (!empty($strp[$i]))
		{	
		$xvv.="{
			showInLegend: true,
            legendText: \"$vxp[$i]\",
            color: \"$color[$i]\",
			type: \"spline\", 
			dataPoints: [
		       $strp[$i]
			]
		},";
		}
	}
	
  }
  else
  {
	  	if (!empty($strp[0]))
		{	
		$xvv.="{
	
            color: \"$color[0]\",
			type: \"spline\", 
			dataPoints: [
		       $strp[0]
			]
		},";
		}
	  
  }
  
	$xvv=substr($xvv,0,-1);
	
	
	
	$t1 = microtime();
	 
        list($m0,$s0) = split(" ",$t);
        list($m1,$s1) = split(" ",$t1);
		
      echo sprintf("%.3f ms",($s1+$m1-$s0-$m0)*1000);	
	

 
 
 ?>


<!DOCTYPE HTML>
<html>
<head>

<script type="text/javascript" src="../../javascripts/jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../../javascripts/jquery/jquery.canvasjs.min.js"></script>
<script type="text/javascript">



$(function () {
	var options = {
		 
		title: {
			text: "<?php echo $vx;?>"
		},
                animationEnabled: true,
				height:600,
	   axisY:{
        
      maximum: 100
     },
		
		data: [
		<?php
		echo $xvv;
		?>

		
		]
		
	
		
	};

	$("#chartContainer").CanvasJSChart(options);

});

</script>


</head>
<body>
<?php




?><p>
<div id="chartContainer" style="height: 300px; width: 100%;"></div>

</body>



</html>
