<?php
// $Id: my_fun.php 2015-10-04 22:12:01Z qfon $


//取得模組設定
$m_arr = &get_module_setup("score_sort");
extract($m_arr, EXTR_OVERWRITE);

//由student_sn得到學生的班級座號姓名
function student_sn_to_classinfo2($student_sn,$viewyear,$seme){
    global $CONN;
	$student_sn=intval($student_sn);
    $rs_sn=$CONN->Execute("select stud_id from stud_base where student_sn='$student_sn'");
    $stud_id=$rs_sn->fields["stud_id"];
    //$seme_year_seme=sprintf("%03d",$viewyear).curr_seme();	
	$seme_year_seme=$viewyear.$seme;
	
	$seme_year_seme1=$viewyear."1";
	$seme_year_seme2=$viewyear."2";

    if (!empty($seme))
	{
	$rs_seme=$CONN->Execute("select seme_class,seme_num from stud_seme where stud_id='$stud_id' and seme_year_seme='$seme_year_seme' and student_sn='$student_sn' limit 1 ");
    }
	else
	{
	$rs_seme=$CONN->Execute("select seme_class,seme_num from stud_seme where stud_id='$stud_id' and (seme_year_seme='$seme_year_seme1' or seme_year_seme='$seme_year_seme2') and student_sn='$student_sn' limit 1 ");
    }
	
	$seme_class=$rs_seme->fields["seme_class"];
	
    $year= substr($seme_class,0,-2); //年級
    $class= substr($seme_class,-2); //班級
	
    $site=$rs_seme->fields["seme_num"]; //座號
	
    //echo $year.$class.$site;
    $rs1=&$CONN->Execute("select  stud_name,stud_sex,curr_class_num,stud_person_id  from  stud_base where student_sn='$student_sn' limit 1");
    $curr_class_num=$rs1->fields['curr_class_num'];
    $stud_sex=$rs1->fields['stud_sex'];
    $stud_name=$rs1->fields['stud_name'];
   
   //$site= substr($curr_class_num,-2);
   //$class= substr($curr_class_num,-4,2);
   //$year= substr($curr_class_num,0,1);
   
	$stud_pid=$rs1->fields['stud_person_id'];
	
    settype($site,"integer");
    settype($class,"integer");
    settype($year,"integer");
    settype($stud_sex,"integer");

    $year_class_site_sex=array($year,$class,$site,$stud_sex,$stud_name,$stud_pid,$stud_id);
    return $year_class_site_sex;
}



//找出第二學期相同科目的ss_id
function  same_name_ss_id($sel_year,$ss_id,$ccd){
    global $CONN;
	
	$score_semester2="score_semester_".$sel_year."_2";
	
	$sql="SELECT DISTINCT ss_id from $score_semester2 where $ccd Group By ss_id";
	
	//echo $sql;
	$rs=$CONN->Execute($sql);
	if(is_object($rs)){
		while (!$rs->EOF) {
			$test_sort=$rs->fields["ss_id"];
			$show_subject=ss_id_to_subject_name($test_sort);
			//echo $show_subject." ".$test_sort."<br>";
			
		    if ($show_subject==ss_id_to_subject_name($ss_id))
			{
			$ss_id=$test_sort;	
			//echo $show_subject." ".$test_sort."<br>";
			break;
			}
			
			
			$rs->MoveNext();
	
			
		}
	}
	
	
    return $ss_id;
		
 
}


//由ss_id找出某學期相同科目的ss_id
function  same_subject_ss_id_from($sel_year,$sel_seme,$ss_id,$ccd){
    global $CONN;
	
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	
	
	$sql="SELECT DISTINCT ss_id from $score_semester where class_id Like '$ccd%'";
	
	//echo $sql;
	$rs=$CONN->Execute($sql);
	if(is_object($rs)){
		while (!$rs->EOF) {
			$test_sort=$rs->fields["ss_id"];
			$show_subject=ss_id_to_subject_name($test_sort);
		    if ($show_subject==ss_id_to_subject_name($ss_id))
			{
			$ss_id=$test_sort;	
			//echo $show_subject;
			break;
			}
			
			
			$rs->MoveNext();
	
			
		}
	}
	
	
    return $ss_id;
		
 
}



//取得領域名稱
function ss_id_scope_name($ss_id,$sel_year,$class_id)
{
	global $CONN;
	
	
	$class_year=substr($class_id,0,1);
	
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
	

	$sql5="select link_ss,ss_id from score_ss where year='$sel_year' and class_year='$class_year' and ss_id='$ss_id' Limit 1";

    $rs5=$CONN->Execute($sql5);
    if(is_object($rs5))
	{
	 while (!$rs5->EOF) 
	{
	$show_subject=$rs5->fields["link_ss"]; 
	 $rs5->MoveNext();
	}		 
	}		
	
	return $show_subject;
}



//由ss_id找出相同領域名稱的ss_id
function  ss_id_to_scope_id($ss_id,$sel_year,$class_id){
    global $CONN;  
	    $ss_id=intval($ss_id);
        $sql3="select link_ss from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $subject_id = $rs3->fields["link_ss"];		
		$class_year=substr($class_id,0,1);		
        $sql4="select ss_id from score_ss where year='$sel_year' and class_year='$class_year' and link_ss='$subject_id'";
        $rs4=$CONN->Execute($sql4);
     	if(is_object($rs4)){
			$h=0;
		while (!$rs4->EOF) {
			$h++;
	        $show_subject[$h]=$rs4->fields["ss_id"];
	
     		$rs4->MoveNext();
	
			
		}
	}

    return $show_subject;
}






function get_pr_from_student_sn($sel_year,$sel_seme,$class_id,$ss_id,$test_sort,$test_kind,$rate,$student_sn,$h,$subject1,$scopeall)
{
global $CONN;

//echo "\$class_id".$class_id."<br>";

////////////////////////
	if (substr($class_id,0,1)=="c")
	{
	$class_year=substr($class_id,3,1)-$h;
	//echo "\$class_year".$class_year."<br>";
	}
	else
	{
		
	 if (substr($class_id,3,1)=="_")
	 {
		 $class_year=substr($class_id,7,1);
		 //echo "\$class_year=".$class_year;
		 $bangi=substr($class_id,9,2);
	 }	
	 else
	 {
		 $class_year=substr($class_id,0,1);
	 }
	 
	}
	
//$ccd=substr($class_id,0,8);


//echo "\$class_year".$class_year."<br>";


///////////////////////////

//////
//if (isset($_POST['subject1'])) 
//{
	
$sss="";


//print_r($subject1);

if (!empty($scopeall))
{


$arryscope=ss_id_to_scope_id($scopeall,$sel_year,$class_year);

//print_r($arryscope);

 while (list($key, $value) = each($arryscope)) 
 {
 $sss.="or a.ss_id='".$value."' ";

     if ($sel_seme>0)
	{
	$ccd=$sel_year."_".$sel_seme."_0".$class_year; 	
    $otherssid=same_subject_ss_id_from($sel_year,$sel_seme,$value,$ccd); 
    $sss.="or a.ss_id='".$otherssid."' "; 

	}
    else
	{
	/*
	$ccd=$sel_year."_1"."_0".$class_year; 
    $ccd1="class_id Like '$ccd%'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd1);
	$sss.="or a.ss_id='".$ss_idv."' ";  
	*/
    $ccd=$sel_year."_2"."_0".$class_year; 
    $ccd1="class_id Like '$ccd%$bangi'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd1);
	$sss.="or a.ss_id='".$ss_idv."' "; 
   // echo "mm".$ccd1."<br>";
    }
	
	
}	
}


//echo $sss."<br>";
 
for ($ii=0; $ii<count($subject1); $ii++)
{//for
		
 $ss_idx=$subject1[$ii]; 
 //echo "\$ss_idx=".$subject1[$ii]."<br>";
		
//包含上下學期

if (substr($ss_idx,0,1)=="m")
{
$ss_idx=substr($ss_idx,2);
$arryx=explode("_",$ss_idx);

 while (list($key, $value) = each($arryx)) 
 {
  $sss.="or a.ss_id='".$value."' ";  
  
  
  if (empty($sel_seme))
  {
	  
	$ccd1="class_id Like '$ccd%'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd);
	$sss.="or a.ss_id='".$ss_idv."' ";  
   // echo "mmyyy".$ss_idv."<br>";
  }
  
     
    if ($sel_seme>0)
	{
	$ccd=$sel_year."_".$sel_seme."_0".$class_year; 	
    $otherssid=same_subject_ss_id_from($sel_year,$sel_seme,$value,$ccd); 
    $sss.="or a.ss_id='".$otherssid."' "; 

	}
    else
	{
	/*
	$ccd=$sel_year."_1"."_0".$class_year; 
    $ccd1="class_id Like '$ccd%'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd1);
	$sss.="or a.ss_id='".$ss_idv."' ";  
	*/
    $ccd=$sel_year."_2"."_0".$class_year; 
    $ccd1="class_id Like '$ccd%$bangi'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd1);
	$sss.="or a.ss_id='".$ss_idv."' "; 
   // echo "mm".$ccd1."<br>";
    }
  

   
 }
}

//包含上下學期		
		
		
//領域
if (substr($ss_idx,0,1)=="s")
{
$ss_idx=substr($ss_idx,1);
$arryz=ss_id_to_scope_id($ss_idx,$sel_year,$class_year);

 while (list($key, $value) = each($arryz)) 
 {
 $sss.="or a.ss_id='".$value."' ";
 
 
     if ($sel_seme>0)
	{
	$ccd=$sel_year."_".$sel_seme."_0".$class_year; 	
    $otherssid=same_subject_ss_id_from($sel_year,$sel_seme,$value,$ccd); 
    $sss.="or a.ss_id='".$otherssid."' "; 

	}
    else
	{
	/*
	$ccd=$sel_year."_1"."_0".$class_year; 
    $ccd1="class_id Like '$ccd%'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd1);
	$sss.="or a.ss_id='".$ss_idv."' ";  
	*/
    $ccd=$sel_year."_2"."_0".$class_year; 
    $ccd1="class_id Like '$ccd%$bangi'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd1);
	$sss.="or a.ss_id='".$ss_idv."' "; 
   // echo "mm".$ccd1."<br>";
    }
 


 }


}
//領域
		
	
     if ($optionArray[$i]>1)$sss.="or a.ss_id='".$optionArray[$i]."' ";

   }//for
	

	
//}
///////


	if ($sel_seme>0)
	{
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	//$ccd1="class_id Like '%$class_id%'";
	
	}
	else
	{
		
	$score_semester1="score_semester_".$sel_year."_1";	
	$score_semester2="score_semester_".$sel_year."_2";
	
	
    $cc1f="$sel_year"."_1";
	$cc2f="$sel_year"."_2";
	
	$cf="_0".$class_year;
	
    $ccd1="class_id Like '$cc1f$cf%$bangi'";
	$ccd2="class_id Like '$cc2f$cf%$bangi'";
	
	$ccd1a="a.class_id Like '$cc1f$cf%$bangi'";
	$ccd2a="a.class_id Like '$cc2f$cf%$bangi'";
	
	
	/*
    $ccd1="class_id Like '$sel_year%$class_id%'";	
	$ccd1a="a.class_id Like '$sel_year%$class_id%'";	
	$ccd2="class_id Like '$sel_year%$class_id%'";	
	$ccd2a="a.class_id Like '$sel_year%$class_id%'";
	*/
	
     if (substr($class_id,0,1)=="c")
	 {
	//echo "ffff";
	 $kf1=substr($class_id,1);	
	 
	/*
    $ccd1="class_id Like '$cc1f$cf%'";
	$ccd2="class_id Like '$cc2f$cf%'";
	
	$ccd1a="a.class_id Like '$cc1f$cf%'";
	$ccd2a="a.class_id Like '$cc2f$cf%'";
	*/
	
	 
	 $ccd1="class_id Like '$sel_year%$kf1%'";
	 $ccd1a="a.class_id Like '$sel_year%$kf1%'";
	 $ccd2="class_id Like '$sel_year%$kf1%'";
	 $ccd2a="a.class_id Like '$sel_year%$kf1%'";	 
	 

	 }
	 
	 	
	if (!empty($ss_id))$ssid1="and (a.ss_id='$ss_id' $sss)";
    if (!empty($ss_idv))$ssid2="and (a.ss_id='$ss_idv' $sss)";	
	
	 
	}

	/*
	if ($sel_seme>0)
	{
	$score_semester="score_semester_".$sel_year."_".$sel_seme;


	}
	else
	{
		
	$score_semester1="score_semester_".$sel_year."_1";	
	$score_semester2="score_semester_".$sel_year."_2";
	
	$cc1f="$sel_year"."_1";
	$cc2f="$sel_year"."_2";
	

	$cf="_0".$class_year;

	 if (substr($class_id,0,1) !="c")
	 {
		 
	  $kf1="_".substr($class_id,6);	
	
      $ccd1="class_id Like '$cc1f$kf1%'";
	  $ccd2="class_id Like '$cc2f$kf1%'";
	
	  $ccd1a="a.class_id Like '$cc1f$kf1%'";
	  $ccd2a="a.class_id Like '$cc2f$kf1%'";
	
	  //echo "ccd1a:".$ccd1a." 2:".$ccd2a." "."<br>";
	  
	 }	
     else
	 {
	
	 //$kf1=substr($class_id,1);	
	 
	 $ccd1="class_id Like '$cc1f$cf%'";
	 $ccd2="class_id Like '$cc2f$cf%'";
	 
	 $ccd1a="a.class_id Like '$cc1f$cf%'";
	 $ccd2a="a.class_id Like '$cc2f$cf%'";
	 
	 //echo "ccd1a:".$ccd1a." 2:".$ccd2a." "."<br>";
	 
	 }
	 
	 

	//$ss_idv=same_name_ss_id($sel_year,$ss_id,$ccd1);
	
	
	
	//if (!empty($ss_id)  && is_int($ss_id))$ssid1="and (a.ss_id='$ss_id' $sss)";
    //if (!empty($ss_idv))$ssid2="and (a.ss_id='$ss_idv' $sss)";	
	


	}	
	*/
     
	
	$ccd="";
	
	if (!empty($class_id))
	{
	 $ccd="a.class_id Like '$class_id%'";	 
	}
	
	
	
	$st="";
    
	if ($test_sort<255 and !empty($test_sort))
	{
		$st="and a.test_sort='$test_sort'";
	}
	
	$ssid="";
	if (!empty($ss_id) || !empty($scopeall))
	{
	$ssid="and (a.ss_id='$ss_id' $sss)";
	}
	
	
	$kin="";
	if (!empty($test_kind))
	{
	$kin="and a.test_kind Like '%$test_kind%'";	
	}
	

	if ($sel_seme>0)
	{

      $sql2="SELECT distinct * from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and  $ccd $ssid $st $kin Group By a.student_sn";

	}
	else
	{
		//這裡不能用UNION ALL
		
	  $sql2="select distinct a.student_sn as sn from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin UNION select distinct a.student_sn as sn from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin Group By sn";

	}
	

    $rs2=&$CONN->Execute($sql2);
	
    $count=$rs2->RecordCount();
	

	$p="";

	
	
if (empty($kin))//平時+定期
{		
      //取出本學年本學期的學校成績共通設定
	
	$sel_semex=$sel_seme;
	$class_yearx=$class_year;
	$sel_yearx=$sel_year;
	
	if(empty($sel_seme))//全學年度
	{
		$sel_semex=1;
		$sel_seme2=2;
				
		$sql="select * from score_setup where class_year='$class_yearx' and year='$sel_yearx' and semester='$sel_seme2'";
	    $rs=$CONN->Execute($sql);
	    $score_mode2= $rs->fields['score_mode'];
	    $performance_test_times2= $rs->fields['performance_test_times'];
	
	  if ($score_mode2=="all" || $performance_test_times2==1)
	  {		  
	    $test_ratio=explode("-",$rs->fields['test_ratio']);	 

		for($j=0;$j<=$performance_test_times2;$j++)
        {
			$jj=$j+1;
			$sratioi2[$jj]=$test_ratio[0]*0.01;
			$nratioi2[$jj]=$test_ratio[1]*0.01;
		}					 

	  
	  
	  }
	  else
	  {
  
		 $test_rv=explode(",",$rs->fields['test_ratio']);
		 for($j=0;$j<=$performance_test_times2;$j++)
         {
			$jj=$j+1;
			$rv=explode("-",$test_rv[$j]);
			$sratioi2[$jj]=$rv[0]*0.01;
			$nratioi2[$jj]=$rv[1]*0.01;	

		 }
    
	 }	
		
		
	}
	 
	
	
	
	$sql="select * from score_setup where class_year='$class_yearx' and year='$sel_yearx' and semester='$sel_semex'";
	$rs=$CONN->Execute($sql);
	$score_mode= $rs->fields['score_mode'];
	$performance_test_times= $rs->fields['performance_test_times'];
	
	

	if ($score_mode=="all" || $performance_test_times==1)
	 {
		 
	 $test_ratio=explode("-",$rs->fields['test_ratio']);
	 
	 $sratioi[$test_sort]=$test_ratio[0]*0.01;
	 $nratioi[$test_sort]=$test_ratio[1]*0.01;	
	 
	   	
		if (empty($test_sort) || $test_sort==255) //全階段
	    {
	   	  for($j=0;$j<=$performance_test_times;$j++)
          {
			$jj=$j+1;
			$sratioi[$jj]=$sratioi[$test_sort];
			$nratioi[$jj]=$nratioi[$test_sort];
		  }			
	    }
	   
	 
	 
	   if(empty($sel_seme))//全學年度
		{
		 $test_ratio=explode("-",$rs->fields['test_ratio']);	     
		  for($j=0;$j<=$performance_test_times2;$j++)
          {
			$jj=$j+1;
			$sratioi[$jj]=$test_ratio[0]*0.01;
			$nratioi[$jj]=$test_ratio[1]*0.01;
		  }					 
			
		}
	 
 	
	 }	 
	 else
	 {

		$test_rv=explode(",",$rs->fields['test_ratio']);
		for($j=0;$j<=$performance_test_times;$j++)
        {
			$jj=$j+1;
			$rv=explode("-",$test_rv[$j]);
			$sratioi[$jj]=$rv[0]*0.01;
			$nratioi[$jj]=$rv[1]*0.01;	
			
			//echo "ok".$sratioi[$jj]." ".$nratioi[$jj]."<br>";
		}					 
	 }
	 
	
	$kin1="and a.test_kind Like '%定期%'";	
	$kin2="and a.test_kind Like '%平時%'";	
	
	
}//平時+定期
	
	
	
	
if ($sel_seme>0)
{
	
	 if (empty($kin))//平時+定期各占比例
	 {
	 	
      $j=$test_sort;
	  $sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin1 Group By sn UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin2 Group By sn UNION ALL ";	 
	  if($rate==1)$sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin1 Group By sn UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin2 Group By sn UNION ALL ";	 
	 
     
	 if (empty($test_sort) || $test_sort==255) //全階段
	 {
         $sqlstr=""; 
		
		 for($j=1;$j<=$performance_test_times;$j++)
		 {
          $sti="and a.test_sort='$j'";
	      $sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $sti $kin1 Group By sn UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $sti $kin2 Group By sn UNION ALL ";	 
	      if($rate==1)$sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $sti $kin1 Group By sn UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $sti $kin2 Group By sn UNION ALL ";	 
			 
		 }
		 
	 }
      
	  $sqlstr=substr($sqlstr,0,-10);
	  $sql="select *,Sum(tscore) as tt from($sqlstr) MyDerivedTable Group By sn Order By tt $p";	 
	
	   //echo $sql;
		
	 }
	 else
	 {
		 
	  $sql="select distinct a.student_sn as sn,Sum(a.score) as tt from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin Group By sn Order By tt $p";
      if($rate==1)$sql="select distinct a.student_sn as sn,Sum(a.score*b.rate) as tt from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin Group By sn Order By tt $p";			
       
	   //echo $sql."<br>";
	  }
	
	
}
else
{
		
      if (empty($kin))//平時+定期各占比例
	  {	

          $j=$test_sort;
	      $sqlstr.="select a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin1 Group By sn 
	  UNION ALL select a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin2 Group By sn 
	  UNION ALL select a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin1 Group By sn 
	  UNION ALL select a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin2 Group By sn UNION ALL ";	 
	      
		  if($rate==1)$sqlstr.="select a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin1 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin2 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin1 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin2 Group By sn UNION ALL ";	 

	     
	 if (empty($test_sort) || $test_sort==255) //全階段
	 {
         //echo "ok$performance_test_times2";
		 $sqlstr=""; 
		 
		 for($j=1;$j<=$performance_test_times2;$j++)
		 {
			 
          $sti="and a.test_sort='$j'";
	      $sqlstr.="select  a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $sti $kin1 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $sti $kin2 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$sratioi2[$j]) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $sti $kin1 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$nratioi2[$j]) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $sti $kin2 Group By sn UNION ALL ";	 

	      if($rate==1)$sqlstr.="select  a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $sti $kin1 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $sti $kin2 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$sratioi2[$j]*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $sti $kin1 Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*$nratioi2[$j]*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $sti $kin2 Group By sn UNION ALL ";	 
			 
		 }
		 
	 }


		 $sqlstr=substr($sqlstr,0,-10);
	 	 
	      $sql="select *,Sum(tscore) as tt from($sqlstr) MyDerivedTable Group By sn Order By tt $p";	 
	      //echo "nnn:".$sql."<br>";
		  

	  
	  }
	  else
	  {
      $sql="select *,Sum(tscore) as tt from (
	            select  a.student_sn as sn,SUM(a.score) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin Group By sn
	  ) MyDerivedTable Group By sn Order By tt $p";
	  if($rate==1)$sql="select *,Sum(tscore) as tt from (
	            select  a.student_sn as sn,SUM(a.score*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin Group By sn 
	  UNION ALL select  a.student_sn as sn,SUM(a.score*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin Group By sn
	  ) MyDerivedTable Group By sn Order By tt $p";
	  
	  //echo "nnn:".$sql."<br>";
	  
	  }	
	
	
	
}
	
   $rs=&$CONN->Execute($sql);

	
    $i=0;
    $jx=0;
    if(is_object($rs))
	{
	 
	    while (!$rs->EOF) 
	   {
		    $i++;
            $stu_sn=$rs->fields["sn"];		
			$tscore[$i]=$rs->fields["tt"];			
			$ttr[$i]=$stu_sn; //取得學生流水號	
            $rs->MoveNext();
			
        }
		

		$m=0;
		
		
		for($k=$i;$k>=0;$k--)
		{
			if ($tscore[$k] !=$tscore[$k+1])
			{
			$m=$m+$jx+1;
		    $jx=0;	
            		
			}
			else
			{
			
			$jx++;
			 
			}	
			
			$ym[$k]=$m;
		}
		
		
		$prr=$count;	

		$vb=0;
		
	    for($k=1;$k<=$prr;$k++)
	    {//for2
			
			$pr_value[$k]=floor((($count-$ym[$k])/$count)*100);
			
			if ($student_sn==$ttr[$k])
			{	
			$return_prvalue=$pr_value[$k];
            break;		    
			}
			else
			{
			$vb++;
			}
  	
		}//for2

		
    }
        
		   
         if (empty($return_prvalue))$return_prvalue=0;
		 if($vb==$count)$return_prvalue=-1;
		 
		 
		 return $return_prvalue;
	
}





function sortview($sel_year,$sel_seme,$class_id,$ss_id,$test_sort,$test_kind,$test_percent,$stylep=0,$rate,$scopeall)
{
global $CONN;

	//if ($test_sort==255) $test_kind='全學期';

	if (substr($class_id,0,1)=="c")
	{
	$class_year=substr($class_id,3,1);
	}
	else
	{
	 if (substr($class_id,3,1)=="_")
	 {
		 $class_year=substr($class_id,7,1);
		 $bangi=substr($class_id,9,2);
	 }	
	 else
	 {
		 $class_year=substr($class_id,0,1);
	 }
	}
	

/*
//科目下拉選單
if (isset($_POST['subject'])) 
{
	
$sss="";
//包含上下學期
if (substr($ss_id,0,1)=="m")
{
$ss_id=substr($ss_id,2);
$arryx=explode("_",$ss_id);
while (list($key, $value) = each($arryx)) {
$sss.="or a.ss_id='".$value."' ";

}


}
//包含上下學期		


//領域
if (substr($ss_id,0,1)=="s")
{
$ss_id=substr($ss_id,1);

$arryz=ss_id_to_scope_id($ss_id,$sel_year,$class_year);

while (list($key, $value) = each($arryz)) {
$sss.="or a.ss_id='".$value."' ";
}


}
//領域

}

*/


if (isset($_POST['subject1'])) 
{
	
$sss="";


$optionArray = $_POST['subject1'];
 
//print_r($optionArray);
 
for ($ii=0; $ii<count($optionArray); $ii++)
{//for
		
 $ss_idx=$optionArray[$ii];
 
		
//包含上下學期
if (substr($ss_idx,0,1)=="m")
{
$ss_idx=substr($ss_idx,2);
$arryx=explode("_",$ss_idx);

 while (list($key, $value) = each($arryx)) 
 {
   $sss.="or a.ss_id='".$value."' ";
   
  // $ccd=$sel_year."_".$sel_seme."_0".$class_year; 
  // $otherssid=same_subject_ss_id_from($sel_year,$sel_seme,$value,$ccd); 
  // $sss.="or a.ss_id='".$otherssid."' ";
  // echo "$otherssid<br>";  
 
   
    if (empty($sel_seme))
	{
	$ccd=$sel_year."_2"."_0".$class_year;
	
    $ccd1="class_id Like '$ccd%$bangi'";	
    //$ccd1="class_id Like '$sel_year%$class_id%'";
	//$ccd1="class_id Like '$ccd%'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd1);
	$sss.="or a.ss_id='".$ss_idv."' ";
	//echo "mm".$sss."<br>";
	
	//if (!empty($ss_id))$ssid1="and (a.ss_id='$ss_id' $sss)";
	//if (!empty($ss_id))$ssid2="and (a.ss_id='$ss_id' $sss)";
	if (!empty($ss_idv))$ssid1="and (a.ss_id='$ss_idv' $sss)";
    if (!empty($ss_idv))$ssid2="and (a.ss_id='$ss_idv' $sss)";	  
    }
  
 /*
  
  ///////////////////////////////////////////////////////////////////////////
    if ($sel_seme>0)
   {
   $otherssid=same_subject_ss_id_from($sel_year,$sel_seme,$value,$ccd); 
   $sss.="or a.ss_id='".$otherssid."' ";     
   }
   else
   {
	   
	   //echo "\$value=".$value."<br>";
	   
	    $t=$class_year; //國小
       if ($class_year>=7)$t=$class_year-7; //國中
       $tt=$t+1;
	    $sss="";
       for ($i=$sel_year-$t;$i<=$sel_year;$i++)
	   {
	   $tt--;
	   $sel_semev=1;
	   $class_yearv=$class_year-$tt;
	   $ccd=$i."_".$sel_semev."_0".$class_yearv;
	   $otherssid=same_subject_ss_id_from($i,$sel_semev,$value,$ccd);
       $sss.="or a.ss_id='".$otherssid."' "; 
       $sel_semev=2;	   
	   $ccd=$i."_".$sel_semev."_0".$class_yearv;
       $otherssid=same_subject_ss_id_from($i,$sel_semev,$value,$ccd);
       $sss.="or a.ss_id='".$otherssid."' ";  
       }
        
   }
 /////////////////////////////////////////////////////////////////////////
*/
 

 }
}
//包含上下學期		


		
//領域
if (substr($ss_idx,0,1)=="s")
{
$ss_idx=substr($ss_idx,1);
$arryz=ss_id_to_scope_id($ss_idx,$sel_year,$class_year);

 while (list($key, $value) = each($arryz)) 
 {
 $sss.="or a.ss_id='".$value."' ";

// echo $value."<br>";
 
     if (empty($sel_seme))
	{
	$ccd=$sel_year."_2"."_0".$class_year;
	
    $ccd1="class_id Like '$ccd%$bangi'";	
    //$ccd1="class_id Like '$sel_year%$class_id%'";
	//$ccd1="class_id Like '$ccd%'";
	$ss_idv=same_name_ss_id($sel_year,$value,$ccd1);
	$sss.="or a.ss_id='".$ss_idv."' ";
	//echo "mm".$sss."<br>";
	
	//if (!empty($ss_id))$ssid1="and (a.ss_id='$ss_id' $sss)";
	//if (!empty($ss_id))$ssid2="and (a.ss_id='$ss_id' $sss)";
	if (!empty($ss_idv))$ssid1="and (a.ss_id='$ss_idv' $sss)";
    if (!empty($ss_idv))$ssid2="and (a.ss_id='$ss_idv' $sss)";	  
    }
 
 
 /*
   ////////////////////////////////////////////////////////////////////////
   if ($sel_seme>0)
   {
   $otherssid=ss_id_to_scope_id($ss_id,$i,$class_yearv); 
   $sss.="or a.ss_id='".$otherssid."' ";     
   }
   else
   {
	   
	   $t=$class_year; //國小
       if ($class_year>=7)$t=$class_year-7; //國中
       $tt=$t+1;
	   $sss="";
       for ($i=$sel_year-$t;$i<=$sel_year;$i++)
	   {
		$tt--;
	   $class_yearv=$class_year-$tt;
	   $otherssid=ss_id_to_scope_id($ss_id,$i,$class_yearv);
       $sss.="or a.ss_id='".$otherssid."' ";  
       }
        
   }
   //////////////////////////////////////////////////////////////////////
*/
 }


}
//領域
		
	
     if ($optionArray[$i]>1)$sss.="or a.ss_id='".$optionArray[$i]."' ";

   }//for
	

	
}


	
if ($test_percent>0)
{
	
	if ($sel_seme>0)
	{
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	//$ccd1="class_id Like '%$class_id%'";
	
	}
	else
	{
		
	$score_semester1="score_semester_".$sel_year."_1";	
	$score_semester2="score_semester_".$sel_year."_2";
	
	/*
    $cc1f="$sel_year"."_1";
	$cc2f="$sel_year"."_2";
	
	$cf="_0".$class_year;
	
    $ccd1="class_id Like '$cc1f$cf%'";
	$ccd2="class_id Like '$cc2f$cf%'";
	
	$ccd1a="a.class_id Like '$cc1f$cf%'";
	$ccd2a="a.class_id Like '$cc2f$cf%'";
	*/
	
    $ccd1="class_id Like '$sel_year%$class_id%'";	
	$ccd1a="a.class_id Like '$sel_year%$class_id%'";	
	$ccd2="class_id Like '$sel_year%$class_id%'";	
	$ccd2a="a.class_id Like '$sel_year%$class_id%'";
	
	
	//echo "xxxxx $ccd1a";
	
     if (substr($class_id,0,1)=="c")
	 {
	
	
	 $kf1=substr($class_id,1);	
	 //echo "$kf1<br>";

	/*
    $ccd1="class_id Like '$cc1f$cf%'";
	$ccd2="class_id Like '$cc2f$cf%'";
	$ccd1a="a.class_id Like '$cc1f$cf%'";
	$ccd2a="a.class_id Like '$cc2f$cf%'";
	*/
	
	 
	 $ccd1="class_id Like '$sel_year%$kf1%'";
	 $ccd1a="a.class_id Like '$sel_year%$kf1%'";
	 $ccd2="class_id Like '$sel_year%$kf1%'";
	 $ccd2a="a.class_id Like '$sel_year%$kf1%'";	

	 
	 

	 }
    
	
	/*
	$ss_idv=same_name_ss_id($sel_year,$ss_id,$ccd1);
	
	
	
	if (!empty($ss_id))$ssid1="and (a.ss_id='$ss_id' $sss)";
    if (!empty($ss_idv))$ssid2="and (a.ss_id='$ss_idv' $sss)";	
     */


	}	
	
     
	
	$ccd="";
	if (!empty($class_id))
	{
	 $ccd="a.class_id Like '$class_id%'";	 
	}
	
	
	
	$st="";
    
	if ($test_sort<255 and !empty($test_sort))
	{
		$st="and a.test_sort='$test_sort'";
		//if ($test_sort=='255') $test_kind='';
	}
	
	$ssid="";
	if (!empty($ss_id))
	{
		$ssid="and (a.ss_id='$ss_id' $sss)";
	}
	
	
	
	
	$kin="";
	if (!empty($test_kind))
	{
	$kin="and a.test_kind Like '%$test_kind%'";	
	}
	

	if ($sel_seme>0)
	{

      //$sql2="SELECT * from $score_semester as a WHERE a.ss_id IN (SELECT DISTINCT ss_id FROM score_ss b WHERE b.enable = '1') and  $ccd $ssid $st $kin Group By a.student_sn";
	 // $sql2="SELECT * from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and  $ccd $ssid $st $kin Group By a.student_sn";
	   $sql2="SELECT distinct * from $score_semester as a,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin Group By a.student_sn";

	}
	else
	{
		//這裡不能用UNION ALL
	  $sql2="SELECT distinct a.student_sn as sn from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin UNION select distinct a.student_sn as sn from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin Group By sn";
     
	}
	

    $rs2=&$CONN->Execute($sql2);
	
    $count=$rs2->RecordCount();
	
	
	//echo $sql2." ".$count."<br>";
	//exit();

	$p="";
	
	/*
	if (!empty($test_percent))
	{
	$prr=round($test_percent*$count*0.01);	
	$p="Limit $prr";
	}
	*/
	
	
if (empty($kin))
{		
    //取出本學年本學期的學校成績共通設定
	
	$sel_semex=$sel_seme;
	$class_yearx=$class_year;
	$sel_yearx=$sel_year;
	
	if(empty($sel_seme))//全學年度
	{
		$sel_semex=1;
		$sel_seme2=2;
		
		
		$sql="select * from score_setup where class_year='$class_yearx' and year='$sel_yearx' and semester='$sel_seme2'";
	    $rs=$CONN->Execute($sql);
	    $score_mode2= $rs->fields['score_mode'];
	    $performance_test_times2= $rs->fields['performance_test_times'];
	

	  if ($score_mode2=="all" || $performance_test_times2==1)
	  {		  
	    $test_ratio=explode("-",$rs->fields['test_ratio']);	 

		for($j=0;$j<=$performance_test_times2;$j++)
        {
			$jj=$j+1;
			$sratioi2[$jj]=$test_ratio[0]*0.01;
			$nratioi2[$jj]=$test_ratio[1]*0.01;
		}					 

	  
	  
	  }
	  else
	  {
  
		 $test_rv=explode(",",$rs->fields['test_ratio']);
		 for($j=0;$j<=$performance_test_times2;$j++)
         {
			$jj=$j+1;
			$rv=explode("-",$test_rv[$j]);
			$sratioi2[$jj]=$rv[0]*0.01;
			$nratioi2[$jj]=$rv[1]*0.01;	

		 }
    
	 }	
		
		
	}
	 
	
	
	
	$sql="select * from score_setup where class_year='$class_yearx' and year='$sel_yearx' and semester='$sel_semex'";
	$rs=$CONN->Execute($sql);
	$score_mode= $rs->fields['score_mode'];
	$performance_test_times= $rs->fields['performance_test_times'];
	
	

	if ($score_mode=="all" || $performance_test_times==1)
	 {
	 $test_ratio=explode("-",$rs->fields['test_ratio']);
	 
	 $sratioi[$test_sort]=$test_ratio[0]*0.01;
	 $nratioi[$test_sort]=$test_ratio[1]*0.01;	
	 
	   	
		if (empty($test_sort) || $test_sort==255) //全階段
	    {
	   	  for($j=0;$j<=$performance_test_times;$j++)
          {
			$jj=$j+1;
			$sratioi[$jj]=$sratioi[$test_sort];
			$nratioi[$jj]=$nratioi[$test_sort];
		  }			
	    }
	   
	 
	 
	   if(empty($sel_seme))//全學年度
		{
		 $test_ratio=explode("-",$rs->fields['test_ratio']);	     
		  for($j=0;$j<=$performance_test_times2;$j++)
          {
			$jj=$j+1;
			$sratioi[$jj]=$test_ratio[0]*0.01;
			$nratioi[$jj]=$test_ratio[1]*0.01;
		  }					 
			
		}
		 
	 
	 
 	
	 }
	 
	 else
	 {

		$test_rv=explode(",",$rs->fields['test_ratio']);
		for($j=0;$j<=$performance_test_times;$j++)
        {
			$jj=$j+1;
			$rv=explode("-",$test_rv[$j]);
			$sratioi[$jj]=$rv[0]*0.01;
			$nratioi[$jj]=$rv[1]*0.01;	
			
			//echo "ok".$sratioi[$jj]." ".$nratioi[$jj]."<br>";
		}					 
	 }
	
	

	 

	 
	 
	 
	
	$kin1="and a.test_kind Like '%定期%'";	
	$kin2="and a.test_kind Like '%平時%'";	
	
	
	}
	
	
	
	
if ($sel_seme>0)
{
	
	 if (empty($kin))//平時+定期各占比例
	 {
	 	
      $j=$test_sort;
	  //$sqlstr.="select a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester as a WHERE a.ss_id IN (SELECT DISTINCT ss_id FROM score_ss b WHERE b.enable = '1') and $ccd $ssid $st $kin1 Group By sn UNION ALL select a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester as a WHERE a.ss_id IN (SELECT DISTINCT ss_id FROM score_ss b WHERE b.enable = '1') and $ccd $ssid $st $kin2 Group By sn UNION ALL ";	 

	  $sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin1 Group By sn UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin2 Group By sn UNION ALL ";	 
	  
	  if($rate==1)$sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin1 Group By sn UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin2 Group By sn UNION ALL ";	 
	 
     
	 if (empty($test_sort) || $test_sort==255) //全階段
	 {
         $sqlstr=""; 
		
		 for($j=1;$j<=$performance_test_times;$j++)
		 {
          $sti="and a.test_sort='$j'";
		 //$sqlstr.="select a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester as a WHERE a.ss_id IN (SELECT DISTINCT ss_id FROM score_ss b WHERE b.enable = '1') and $ccd $ssid $sti $kin1 Group By sn UNION ALL select a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester as a WHERE a.ss_id IN (SELECT DISTINCT ss_id FROM score_ss b WHERE b.enable = '1') and $ccd $ssid $sti $kin2 Group By sn UNION ALL ";	 

	      $sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $sti $kin1 Group By sn UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $sti $kin2 Group By sn UNION ALL ";	 
	      if($rate==1)$sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $sti $kin1 Group By sn UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $sti $kin2 Group By sn UNION ALL ";	 
			 
		 }
		 
	 }
      
	  $sqlstr=substr($sqlstr,0,-10);
	  $sql="select *,Sum(tscore) as tt from($sqlstr) MyDerivedTable Group By sn Order By tt $p";	 
	 
	   //echo $sql."<br>";
	   
	   
		
	 }
	 else
	 {
		 
	  $sql="select distinct a.student_sn as sn,Sum(a.score) as tt from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin Group By sn Order By tt $p";
      if($rate==1)$sql="select distinct a.student_sn as sn,Sum(a.score*b.rate) as tt from $score_semester as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd $ssid $st $kin Group By sn Order By tt $p";			
      
	  //echo "單學期：".$sql."<br>";
	  
	  }
	
	
}
else
{
	  	
      if (empty($kin))//平時+定期各占比例
	  {	

          $j=$test_sort;
	      $sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin1 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin2 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin1 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin2 Group By sn UNION ALL ";	 
	      
		  if($rate==1)$sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin1 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin2 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin1 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin2 Group By sn UNION ALL ";	 


	
	if (empty($test_sort) || $test_sort==255) //全階段
	 {
             
		 $sqlstr=""; 
		 
		 for($j=1;$j<=$performance_test_times2;$j++)
		 {
			 
          $sti="and a.test_sort='$j'";
	      $sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $sti $kin1 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $sti $kin2 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$sratioi2[$j]) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $sti $kin1 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi2[$j]) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $sti $kin2 Group By sn UNION ALL ";	 

	      if($rate==1)$sqlstr.="select distinct a.student_sn as sn,SUM(a.score*$sratioi[$j]*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $sti $kin1 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi[$j]*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $sti $kin2 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$sratioi2[$j]*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $sti $kin1 Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*$nratioi2[$j]*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $sti $kin2 Group By sn UNION ALL ";	 
			 
		 }
		 
	 }		




		$sqlstr=substr($sqlstr,0,-10);
	 	 
	      $sql="select *,Sum(tscore) as tt from($sqlstr) MyDerivedTable Group By sn Order By tt $p";	 

	  
	  }
	  else
	  {
		  
		  
		  
		  
      $sql="select *,Sum(tscore) as tt from (
	            select distinct a.student_sn as sn,SUM(a.score) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin Group By sn
	  ) MyDerivedTable Group By sn Order By tt $p";
	  
	  if($rate==1)$sql="select *,Sum(tscore) as tt from (
	            select distinct a.student_sn as sn,SUM(a.score*b.rate) as tscore from $score_semester1 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd1a $ssid1 $st $kin Group By sn 
	  UNION ALL select distinct a.student_sn as sn,SUM(a.score*b.rate) as tscore from $score_semester2 as a ,score_ss as b WHERE a.ss_id=b.ss_id and b.enable='1' and $ccd2a $ssid2 $st $kin Group By sn
	  ) MyDerivedTable Group By sn Order By tt $p";
	     
		 
		 //echo "vvvv:".$sql;

	  }	
	
	
	
}
	
   $rs=&$CONN->Execute($sql);
	
	//echo $sql;
	//exit();

    $i=0;
    $jx=0;
    if(is_object($rs))
	{
	 
	    while (!$rs->EOF) 
	   {
		    $i++;
            $stu_sn=$rs->fields["sn"];		
			$tscore[$i]=$rs->fields["tt"];
			
			$ttr[$i]=$stu_sn; //取得學生流水號
					
            $rs->MoveNext();
			
        }

		
		$main0="";
		$csvmain="";
		$main="";
		
		$pl=$count*150;
		
		$main0.="<head>";
	    $main0.="<link rel='stylesheet' href='TableCSSCode.css' type='text/css'/>";	
	    $main0.="</head>";
		
		$main0.="<center><div class='CSSTableGenerator' style='width:600px;height:$pl px;'>";
		$main0.="<table >";
		$main0.="<tr><td>補救順位</td><td>年級</td><td>班級</td><td>座號</td><td>姓名</td><td>成績</td><td>名次</td><td>PR值</td><td>PR值曲線</td><td>學號</td></tr>";
				
			
		$main.= "<center><table width=90% border=1><tr>";
		
		$main.= "<td width=5% align=middle>補救順位</td><td width=12% align=middle>身分證字號</td><td>年級</td><td width=8% align=middle>班級</td><td width=8% align=middle>座號</td><td width=10% align=middle>姓名</td><td width=10% align=middle>成績</td><td width=5% align=middle>名次</td><td width=5% align=middle>PR值</td><td width=5% align=middle>學號</td><td width=5% align=middle>備註</td></tr>";
		
		$csvmain.="補救順位,身分證字號,年級,班級,座號,姓名,成績,名次,PR值,學號,備註\r\n";

		$m=0;
		
		
		for($k=$i;$k>=0;$k--)
		{
			if ($tscore[$k] !=$tscore[$k+1])
			{
			$m=$m+$jx+1;
		    $jx=0;		
           	
			}
			else
			{
			
			$jx++;
			
			}	
			
			$ym[$k]=$m;
		}
		
		//列出多少後面%人數
		$prr=round($test_percent*$count*0.01);	
		
		
		 $go="getchart.php";
		 if (is_array($scopeall))$go="chart1.php";

		
		
		
	for($k=1;$k<=$prr;$k++)
	{//for2    
				
			$pr_value[$k]=floor((($count-$ym[$k])/$count)*100);
			
			$sa=student_sn_to_classinfo2($ttr[$k],$sel_year,$sel_seme);

	         $sak[1]=$sa[1];
		     if ($sa[1]<10)$sak[1]="0".$sa[1];
		     $c_c_c=$sel_year."_".$sel_seme."_0".$sa[0]."_".$sak[1];
		   
			if ($sa[0]>6)$sa[0]=$sa[0]-6;

		    
			$main0.= "<tr><td align=middle>".$k."</td><td>".$sa[0]."</td><td>".$sa[1]."</td><td>".$sa[2]."</td><td><a href=../stud_reg/stud_list.php?student_sn=$ttr[$k]&c_curr_class=$c_c_c&c_curr_seme=$sel_year$sel_seme target='_new'>".$sa[4]."</a></td><td>".round($tscore[$k],2)."</td><td>".$ym[$k]."</td>
			<td>$pr_value[$k]
			</td>
			<td>

            <table><tr><td>		
			
  <form action='$go' method='post' target='_new' />
  <input type='hidden' id='student_sn' name='student_sn' value='$ttr[$k]' />
  <input type='hidden' id='sel_year' name='sel_year' value='$sel_year' />
  <input type='hidden' id='sel_seme' name='sel_seme' value='$sel_seme' />
  <input type='hidden' id='class_id' name='class_id' value='$class_id' />
  <input type='hidden' id='ss_id' name='ss_id' value='$ss_id' />
  <input type='hidden' id='test_sort' name='test_sort' value='$test_sort' />
  <input type='hidden' id='test_kind' name='test_kind' value='$test_kind' />
  <input type='hidden' id='rate' name='rate' value='$rate' />";
  
  if (isset($_POST['subject1'])) 
 {
         $arrb=$_POST['subject1'];
  		  for($i=0;$i<count($arrb);$i++)
		  {

				$main0.="<input type='hidden' id='subject1[]' name='subject1[]' value='$arrb[$i]' />";
	
			
		  }
  
 }
 
 //print_r($scopeall);
   if (is_array($scopeall)) 
  {
         $arrb=$scopeall;
          while (list($key, $value) = each($arrb)) 
		  {
				$main0.="<input type='hidden' name='scopeall[]' value='$key,$value' />";			
		  }
  
  }
  
  
   
  $main0.="<input type='submit' value='觀察'/>			
			
   </form>			

</td></tr></table>   
  
			
			<td>".$sa[6]."</td></td></tr>";
			
			$main.= "<tr><td align=middle>".$k."</td><td align=middle>".$sa[5]."</td><td align=middle>".$sa[0]."</td><td align=middle>".$sa[1]."</td><td align=middle>".$sa[2]."</td><td align=middle>".$sa[4]."</td><td align=middle>".round($tscore[$k],2)."</td><td align=middle>".$ym[$k]."</td><td align=middle>".$pr_value[$k]."</td><td align=middle>".$sa[6]."</td><td align=middle></td></tr>";
			
			
			
			$csvmain.=$k.",".$sa[5].",".$sa[0].",".$sa[1].",".$sa[2].",".$sa[4].",".round($tscore[$k],2).",".$ym[$k].",".$pr_value[$k].",".$sa[6]."\r\n";
 			
			$excelmain.=$k.",".$sa[5].",".$sa[0].",".$sa[1].",".$sa[2].",".$sa[4].",".round($tscore[$k],2).",".$ym[$k].",".$pr_value[$k].",".$sa[6]." ".",\n";
		     
		}//for2
        
		$main.= "</table></center>";
		 
		 
		$main0.="</table>";
		
		$main0.="</div>";
		 
		
    }
	
	}
	if ($stylep==0)return $main0;
	if ($stylep==1)return $main;
	if ($stylep==2)return $csvmain;
	if ($stylep==3)return $excelmain;
}

/*
//科目或領域選單
function subject_menu($sel_year,$sel_seme,$class_id,$ss_id,$test_kind,$test_sort,$id) {
	global $CONN,$score_semester,$choice_kind,$yorn;
	
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	
	$ccd="";
	if (!empty($class_id))
	{
	$ccd="class_id Like '$class_id%'";
	}
	
	if (empty($sel_seme))
	{
		
	 $sel_seme1=1;
	// $sel_seme2=2;

     $score_semester="score_semester_".intval($sel_year)."_".$sel_seme1;
	// $score_semester2="score_semester_".intval($sel_year)."_".$sel_seme2;
	 
	 $ccd="class_id Like '$sel_year%$class_id%'";

	 
	 if (substr($class_id,0,1)=="c")
	 {
	
	 //$kf1=substr($class_id,1);	 
	 $ccd="class_id Like '$sel_year%$class_year%'";
	 }
	 
	    //$score_semester2="score_semester_".intval($sel_year)."_2";
	}
	


	$sql="SELECT DISTINCT ss_id from $score_semester where $ccd Group By ss_id";	

	$rs=$CONN->Execute($sql);
	
	if(is_object($rs))
	{
		
		$arry=array();
		
		 while (!$rs->EOF)
		 {
			$test_sort=$rs->fields["ss_id"];
			$show_subject[$test_sort]=ss_id_to_subject_name($test_sort);			
			$arry=ss_id_to_scope_id($test_sort,$sel_year,$class_id);		   
            $ac[$test_sort]=ss_id_scope_name($test_sort,$sel_year,$class_id);			
			$rs->MoveNext();
		 }
	}	


		 
		 //刪除陣列重複值
		 $ac=array_unique($ac);
		//print_r($ac);
		
		 //$show_subject=array_merge($show_subject,$ac);
		 
          while (list($key, $value) = each($ac)) 
		  {
         // echo "Key: $key; Value: $value<br />\n";
		  $show_subject["s".$key]=$value;
		   
          }

	

	$ss = new drop_select();
	$ss->s_name ="subject";
	$ss->top_option = "選擇科目或領域";
	$ss->id = $id;
	$ss->arr = $show_subject;
	$ss->is_submit = false;

	return $ss->get_select();
}

*/

//科目或領域選單
function subject_menu_checkbox($sel_year,$sel_seme,$class_id,$Subject1,$test_kind,$test_sort,$viewok) {
	global $CONN,$score_semester,$choice_kind,$yorn;
	
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	
	$ccd="";
	
	if (!empty($class_id))
	{
	$ccd="where class_id Like '$class_id%'";
	}
	  //echo $ccd."<br>";
	
	
	if (empty($sel_seme))
	{
	
	  $sel_seme1=1;
	  $sel_seme2=2;

      $score_semester="score_semester_".intval($sel_year)."_".$sel_seme1;
	  $score_semester2="score_semester_".intval($sel_year)."_".$sel_seme2;
	 
	   $ccd="where class_id Like '$sel_year%$class_id%'";
       
	 
	   if (substr($class_id,0,1)=="c")
	    {
	     
	     $kf1=substr($class_id,1);	 
	     $ccd="where class_id Like '$sel_year%$kf1%'";
	    }
	    //echo $ccd."<br>";
	   
	}
	

	$sql="SELECT DISTINCT ss_id from $score_semester $ccd Group By ss_id";	

	$rs=$CONN->Execute($sql);
	
	//echo $sql;
	
	if(is_object($rs))
	{
		$arry=array();
		
		 while (!$rs->EOF)
		 {
			$test_sort=$rs->fields["ss_id"];
			$show_subject[$test_sort]=ss_id_to_subject_name($test_sort);
			//echo $test_sort.$show_subject[$test_sort]."<br>";
			//$show_subjectb[$test_sort]=ss_id_to_subject_name($test_sort);	
			
			$nss=ss_id_to_subject_name($test_sort);
			
		    if (empty($sel_seme))
	        {
			   $sqlu="select subject_id,subject_kind from score_subject where subject_name='$nss'";
               $rsu=$CONN->Execute($sqlu);
               
			   	if(is_object($rsu))
	           {		
		
		         while (!$rsu->EOF)
		        {
		    	 $subject_id = $rsu->fields["subject_id"];
			     
				 $sqlu1="select ss_id from score_ss where (subject_id='$subject_id' or 	scope_id='$subject_id') and year='$sel_year' and semester='1' and enable='1'  Limit 1";
                 $rsu1=$CONN->Execute($sqlu1);
				 $ss_id = $rsu1->fields["ss_id"];
				 
				
				 $ddd=ss_id_to_subject_name($ss_id);

                 //echo $ss_id." ".$ddd."<br>";				 
				 if(!empty($ss_id)) $show_subjectb[$ss_id]=ss_id_to_subject_name($ss_id); 
			 
			     $rsu->MoveNext();
		         }
               }	
			   
			   
			} 
			

			
			$arry=ss_id_to_scope_id($test_sort,$sel_year,$class_id);		   
            $ac[$test_sort]=ss_id_scope_name($test_sort,$sel_year,$class_id);			
			$rs->MoveNext();
			
		 }
	}	


			  
		  
          while (list($key, $value) = each($show_subject)) 
		  {
		   
		   if(substr($key,0,1) !="s")
		   {	  
			 
			 if (!empty($sel_seme))
			 {				 
		     $kkp="m_".$key;
		     $uppx[$kkp]=$value;
			 
			 
		     }
			 
			 
		   ///////////////	 
		   if (empty($sel_seme))
		   {
			   
			while (list($key2, $value2) = each($show_subjectb)) 
		    {
			   if ($value2==$value)
			   {
				   $kkp="m_".$key."_".$key2;
				   $uppx[$kkp]=$value2;
				   break;
			   }
			   
		    }
			  
		   } 
           ////////////////
		   
			
		   }
  			
		   
          }
		  
		 //刪除陣列重複值
		 $ac=array_unique($ac);
		//print_r($ac);
		
		 //$show_subject=array_merge($show_subject,$ac);
          
		  //print_r($show_subject);
		  
		  while (list($key, $value) = each($ac)) 
		  {
        
		  $show_subject["s".$key]=$value;
		  $uppx["s".$key]=$value;
          }
	
	
         // print_r($uppx);
	
		
	      $show_s="";
		  
		  //print_r($Subject1);
		  
		  while (list($key, $value) = each($uppx)) 
		  {
			
		      $optionArray =$Subject1;
			  
			   
              for ($ii=0; $ii<count($optionArray); $ii++) 
			  {
				  
				  if ($optionArray[$ii]==$key)
				  {
					 //echo $optionArray[$ii];
					  $is_show_cc[$key]="checked";
					  break;
				  }
			  }
			  
		   if (!empty($value))
		   {
			 if (substr($key,0,1)=="m")$bgcolor="#EEFFBB";
 		     if (substr($key,0,1)=="s")$bgcolor="#FFEE99";
			 
		    //$show_s.="<tr><td bgcolor='$bgcolor'><input type='checkbox' name='subject1[]' value='$key' $is_show_cc[$key] onchange=\"this.form.submit();\">$value</td></tr>";
		  if($viewok==1)$show_s.="<tr><td bgcolor='$bgcolor'><input type='checkbox' name='subject1[]' value='$key' $is_show_cc[$key]>$value</td></tr>";

		   }
         
		  
		  }
		 // $show_s.="<tr><td><button type=\"submit\" form=\"form1\" name=\"run\" value=\"Submit\" style=\"width:230px;height:40px;font-size:20px;\">開始處理</button></td></tr>";
		  if($viewok==1)$show_s.="<tr><td><input type=\"submit\"  name=\"run\" value=\"開始處理\" style=\"width:230px;height:40px;font-size:20px;\"></td></tr>";
	
			

			
		  if ($viewok==0)return $ac;
          if ($viewok==1)return $show_s;
}




// 後面%選單

function percent_menu($id) {

      for ($i=0;$i<=100;$i++)
	  {
		  $test_sort=$i;
		  $show_percent[$test_sort]=$i."%";
	
		  
	  }
	  
	if (empty($id))$id=100;  

	$ss = new drop_select();
	$ss->s_name ="percent";
	$ss->top_option = "選擇後面%";
	$ss->id = $id;
	$ss->arr = $show_percent;
	
	if (isset($_POST['subject1']))
	{
		$ss->is_submit = false;
	}
	else
	{
		$ss->is_submit = true;
	}
	return $ss->get_select();
}






//由ss_id找出科目名稱的函數
function  ss_id_to_subject_name($ss_id){
    global $CONN;
    $sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2);
        $subject_name = $rs2->fields["subject_name"];
    }
    else{
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $subject_name = $rs4->fields["subject_name"];
    }
    return $subject_name;
}



//由ss_id找出領域名稱的函數
function  ss_id_to_scope_name($ss_id){
    global $CONN;
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $scope_name = $rs4->fields["subject_name"];

    return $scope_name;
}




function stage_menu2($sel_year,$sel_seme,$sel_class,$sel_num,$id,$all="",$other_script="") {
	global $CONN,$score_semester,$choice_kind,$yorn;
    
	$score_semester="score_semester_".intval($sel_year)."_".$sel_seme;
		
	if (empty($sel_class) && !empty($sel_num))
	{
		$sel_class=substr($sel_num,1); 
		$tt="";
	}
	
	if (!empty($sel_class) && !empty($sel_num))
	{
		$tt="and c_sort='$sel_num'";
	}
	
	 
	 
	 if (empty($sel_seme))
     {	
	 $sel_seme=1;
     $score_semester="score_semester_".intval($sel_year)."_".$sel_seme;	
     }
	 
//////////////////////
	$sql="select class_id from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$sel_class' $tt";
	
	//echo $sql;
	$rs=$CONN->Execute($sql);
	$class_id=$rs->fields["class_id"];
	if ($all) {
		$class_id=substr($class_id,0,strlen($class_id)-2)."%";
		$sql="select distinct test_sort from $score_semester where class_id like '$class_id' and test_sort < '200' order by test_sort";
	} else {
		$sql="select distinct test_sort from $score_semester where class_id Like '$class_id%' order by test_sort";
	}
	
	$rs=$CONN->Execute($sql);
	if(is_object($rs)){
		
		$k1=0;
		while (!$rs->EOF) {
			$test_sort=$rs->fields["test_sort"];
			
			if($test_sort<200)
		    {	
			$show_stage[$test_sort]="第".$test_sort."階段";
			$k1++;
			}
			$rs->MoveNext();
		}
	}
///////////////////////	
	
if (empty($sel_seme))
{
		
		
	$sel_seme=2;
    $score_semester="score_semester_".intval($sel_year)."_".$sel_seme;
	
	//////////////////////////////////////////
	$sql="select class_id from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$sel_class' $tt";
	
	//echo $sql;
	$rs=$CONN->Execute($sql);
	$class_id=$rs->fields["class_id"];
	if ($all) {
		$class_id=substr($class_id,0,strlen($class_id)-2)."%";
		$sql="select distinct test_sort from $score_semester where class_id like '$class_id' and test_sort < '200' order by test_sort";
	} else {
		$sql="select distinct test_sort from $score_semester where class_id Like '$class_id%' order by test_sort";
	}
	
	$rs=$CONN->Execute($sql);
	if(is_object($rs)){
		
		$k2=0;
		while (!$rs->EOF) {
			$test_sort=$rs->fields["test_sort"];
			
			if($test_sort<200)
		    {	
			$show_stage2[$test_sort]="第".$test_sort."階段";
			$k2++;
			}
			$rs->MoveNext();
		}
	}
/////////////////////////////////////////////	
	
	
}
	
	//echo "k1:".$k1." k2:".$k2;
	
	//array_push($show_stage, "全階段");
    $show_stage["255"]="全階段";
	$show_stage2["255"]="全階段";
	if ($yorn=="n") $show_stage["254"]="平時成績";
	$rs=$CONN->Execute("select distinct print from score_ss where class_year='$sel_class' and enable='1' and need_exam='1' and print!='1'");
	if ($rs->recordcount()>0) $show_stage["255"]="全階段";
	
	$ss = new drop_select();
	$ss->s_name ="stage";
	$ss->top_option = "選擇階段";
	$ss->id = $id;
	
	if ($k1>$k2)
	{
		$ss->arr = $show_stage;
	}
	else
	{
		$ss->arr = $show_stage2;
	}
	
	
	
	if (isset($_POST['subject1']))
	{
	$ss->is_submit = false;
	}
	else
	{
	$ss->is_submit = true;
	}
	$ss->other_script = $other_script;
	return $ss->get_select();
}




function kind_menu2($sel_year,$sel_seme,$sel_class,$sel_num,$stage,$id) {
	global $CONN;
	$show_kind=array("1"=>"定期評量","2"=>"平時成績","3"=>"定期+平時","4"=>"全學期(該科只輸入總成績)");

	$sk = new drop_select();
	$sk->s_name ="kind";
	$sk->top_option = "選擇種類";
	$sk->id = $id;
	$sk->arr = $show_kind;
	
	if (isset($_POST['subject1']))
	{
	$sk->is_submit = false;
	}
	else
	{
	$sk->is_submit = true;	
	}
	
	return $sk->get_select();
}




function score_head2($sel_year,$sel_seme,$year_name,$me,$stage,$chart_kind,$subject_name){
    global $CONN,$school_kind_name;
	
    $yn=substr($me,1);
	if ($yn>6)$yn=$yn-6;
	
    $rs1=&$CONN->Execute("select * from school_base");
    $sch_sheng=$rs1->fields['sch_sheng'];
    $sch_cname=$rs1->fields['sch_cname'];
    if(strlen($sel_year)==2) $sel_year="0".$sel_year;
    if(strlen($year_name)==1) $year_name="0".$year_name;
    if(strlen($me)==1) $me="0".$me;
    $class_id=$sel_year."_".$sel_seme."_".$year_name."_".$me;
    $rs2=&$CONN->Execute("select * from school_class where class_id='$class_id'");
    $c_year=$rs2->fields['c_year'];
    $c_name=$rs2->fields['c_name'];
    settype($sel_year,"integer");
    $stage_name=array(1=>"第一階段",2=>"第二階段",3=>"第三階段",4=>"第四階段","255"=>"全階段");
	
	$subject_name_pr=ss_id_to_subject_name($subject_name);
	
	if (empty($chart_kind))$chart_kind="(定期+平時)";
	
	
if (is_array($subject_name))
{
 
for ($i=0; $i<count($subject_name); $i++)
{//for
		
 $ss_id=$subject_name[$i];
		
 
		
//包含上下學期
if (substr($ss_id,0,1)=="m")
{
$ss_id=substr($ss_id,2);
$arryx=explode("_",$ss_id);

 while (list($key, $value) = each($arryx)) 
 {
 

 $sprint[]=ss_id_to_subject_name($value);
 
 }

}
//包含上下學期		
		
//領域
if (substr($ss_id,0,1)=="s")
{
$ss_id=substr($ss_id,1);
//$arryz=ss_id_to_scope_id($ss_id,$sel_year,$class_year);

 //while (list($key, $value) = each($arryz)) 
 //{
 $sprint[]=ss_id_scope_name($ss_id,$sel_year,$class_id);	

// }


}
//領域


}//for
	

  $sprint=array_unique($sprint);

  while (list($key, $value) = each($sprint)) 
  {
  $spr=$spr."+".$value;		   
  }	
  $subject_name_pr="[".substr($spr,1)."]";	
  
  	

}	



if (substr($subject_name,0,1)=="s")
{
$ss_id=substr($subject_name,1);
$subject_name_pr=ss_id_scope_name($ss_id,$sel_year,$class_id);	
}

if (empty($subject_name_pr))$subject_name_pr="全科目";


	if (!empty($c_name))
	{
		$c_name=$c_name."班";
	}
	else
	{
		$c_name=$yn."年級";
	}
	
	if($sel_seme==0){
		$sel_seme="上下";
		if (intval($year_name)>6)$year_name=intval($year_name)-6;
		$c_name=$year_name."年".$me."班";
		
		if (substr($me,0,1)=="c" || substr($me,0,1)=="p")
		{
			$c_name=(substr($me,1)-6)."年級";
		}
		
		
	}
	//echo $c_name;
	
	if (empty($stage_name[$stage]))$stage_name[$stage]="全階段";
	
    return $sch_cname.$sel_year."學年度第".$sel_seme."學期".$school_kind_name[$c_year].$c_name.$stage_name[$stage].$subject_name_pr.$chart_kind."成績表";
}



?>
