<?php
// $Id: view.php 8650 2015-12-18 03:58:09Z qfon $
include "config.php";
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if($der=="") $der="order_pos,class_id,num";

    $class_base_p = class_base();
	
///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
if ($id <> "") {
    $stmt = $mysqliconn->prepare("select doc,title,foot,kind_set,beg_date,end_date,grada,year(beg_date)-1911 AS cita_year from cita_kind  where id = ? ");
    $stmt->bind_param('s', $id);
} else {
    $stmt = $mysqliconn->prepare("select doc,title,foot,kind_set,beg_date,end_date,grada,year(beg_date)-1911 AS cita_year from cita_kind");
}

$stmt->execute();
$stmt->bind_result($doc,$title,$foot,$kind_set,$beg_date,$end_date,$gra,$cita_year);
$stmt->fetch();
$stmt->close();
///mysqli
	
/*	
    $sqlstr =" select *,year(beg_date)-1911 AS cita_year from cita_kind  where id = '$id'   " ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;          
    
	$doc = $row["doc"];     
    $title = $row["title"];  
    $foot = $row["foot"];  
    $kind_set = $row["kind_set"] ;       
    $beg_date = $row["beg_date"];  
    $end_date = $row["end_date"];  
    $gra = $row["grada"];  
    $cita_year = $row["cita_year"];	
*/

//編修日期設為開始日 補登時修正用
//$sqlstr="UPDATE `cita_data` SET `up_date` = '$beg_date' where kind=$id";
//		 $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 

echo "<table align=center width='90%'>";
echo "<tr><td><font size=5 color=red  face=標楷體>$doc</font>( <a href=list.php?gra=$gra>$grada[$gra]</a> )";
     //期限檢查    
      if (date("Y-m-d")>=$beg_date and date("Y-m-d")<=$end_date) 
         echo "　　<a href=\"citain.php?id=$id\"><img src=\"images/edit.gif\" border=\"0\" alt=\"填報中\">填報中</a>" ;
    
echo "</td></tr></table>";

      //標題行
    
	echo "<table cellSpacing=0 cellPadding=4 width='100%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
          <tr bgcolor='#66CCFF' align=center> 
            <td ><a href=$PHP_SELF?id=$id&der=order_pos,class_id,num>▲</a>成績<a href=$PHP_SELF?id=$id&der=order_pos%20desc,class_id,num>▼</a></td>";
	if ($viewyn !=1 && $viewyn !=2)echo "<td ><a href=$PHP_SELF?id=$id&der=class_id,order_pos,num>▲</a>班級<a href=$PHP_SELF?id=$id&der=class_id%20desc,order_pos,num>▼</a></td>";
	if ($viewyn ==2)echo "<td ><a href=$PHP_SELF?id=$id&der=class_id,order_pos,num>▲</a>年級<a href=$PHP_SELF?id=$id&der=class_id%20desc,order_pos,num>▼</a></td>";
    if ($viewyn ==1)echo "<td ><a href=$PHP_SELF?id=$id&der=class_id,order_pos,num>▲</a>班級<a href=$PHP_SELF?id=$id&der=class_id%20desc,order_pos,num>▼</a></td>";
	if ($viewyn !=2)echo "<td >座號</td>";
	echo "<td >學號</td>";
	if ($viewfullname !=1)echo "<td ><a href=$PHP_SELF?id=$id&der=stud_name,order_pos>▲</a>姓名<a href=$PHP_SELF?id=$id&der=stud_name%20desc,order_pos>▼</a></td>";
	echo "<td ><a href=$PHP_SELF?id=$id&der=guidance_name,order_pos>▲</a>指導者<a href=$PHP_SELF?id=$id&der=guidance_name%20desc,order_pos>▼</a></td>
		<td><a href=$PHP_SELF?id=$id&der=up_date,order_pos,class_id,num>▲</a>日期<a href=$PHP_SELF?id=$id&der=up_date%20desc,order_pos,class_id,num>▼</a>　<a href=$PHP_SELF?id=$id&der=order_pos,class_id,num&date=no>隱藏日期</a></td></tr>";              

    //班上報名資料
if ($id <> "") {
	
    $stmt = $mysqliconn->prepare("select id,kind,num,order_pos,stud_name,guidance_name,data_get,data_input,class_id,stud_id,up_date from cita_data  where (kind = ?  and order_pos>-1) order by $der  ");
    $stmt->bind_param('s', $id);

} 
 
$stmt->execute();
$stmt->bind_result($did, $item, $stud_num,$order_pos, $stud_name, $guidance_name,$data_get,$data_input,$class_id,$stud_id,$up_date);
	
	while ($stmt->fetch()) {
		
	$order_pos=$order_pos+1;
		
		if($viewfullname==2)
		{
	     $stud_name_replace = preg_replace("/&#([0-9]{5});/", "○",$stud_name);
		 
		 if ($stud_name_replace==$stud_name)
         {			 
         $stud_name=str_replace(mb_substr($stud_name,1,1,"BIG5"),"○",$stud_name);
		 }
		 else
		 {
		 $stud_name=$stud_name_replace;	 
		 }	 
		}
    
		if($date=='no')$up_date=""; 
		$class_name=class_id_to_full_class_name($class_id);
        $temp = explode("_",$class_id);
        $seme_year_seme = $temp[0].$temp[1];
        $cti_class_id = sprintf("%d%02d",$temp[2],$temp[3]);

        echo "<tr align='center'> 
            <td >$data_get</td>";
       	if ($viewyn !=1 && $viewyn !=2)echo "<td><a href='show_class.php?seme_year_seme=$seme_year_seme&class_id=$cti_class_id'>$class_name</td>";
      	if ($viewyn ==2)echo "<td>$temp[1]</td>";
      	if ($viewyn ==1)echo "<td>$temp[3]</td>";
			
		if ($viewyn !=2)echo "<td>$stud_num</td>";
		echo "<td>$stud_id</td>";
        if ($viewfullname !=1) echo "<td ><a href='show.php?cita_year=$cita_year&stud_id=$stud_id'>$stud_name</a></td>";
		echo "<td >$guidance_name</td>
	          <td >$up_date</td>
         </tr>" ;
   
   }           
   echo "</table>" ;  
   
      //統計 -------------------------------------------------------
   //學校、組數統計	
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'>";
   if ($viewyn ==2)echo "<td>年級</td>";
   if ($viewyn !=2)echo "<td>班級</td>";
   echo "<td>人數</td></tr>\n";
   
   if ($id <> "") {
	
    $stmt = $mysqliconn->prepare("select class_id , count(*) as cc  from  cita_data where (kind = ?  and order_pos>-1) group by class_id ");
    $stmt->bind_param('s', $id);

   } 
 
   $stmt->execute();
   $stmt->bind_result($class_id,$num);
   while ($stmt->fetch()) {
	 
	 $class_name=class_id_to_full_class_name($class_id);
     $temp = explode("_",$class_id);
     $seme_year_seme = $temp[0].$temp[1];
     $cti_class_id = sprintf("%d%02d",$temp[2],$temp[3]);
	 
	 if ($viewyn ==2)
	 {
	  $class_name=$temp[1];
      echo  "<tr><td>$class_name</td><td>$num </td></tr>\n" ;  		 
		 
	 }
	 
	 if ($viewyn ==1)
	 {
		 $class_name=$temp[3];
      echo  "<tr><td>$class_name</td><td>$num </td></tr>\n" ;   

	 }
	
	
	  if ($viewyn !=1 && $viewyn !=2)
	  {
      echo  "<tr><td><a href='show_class.php?seme_year_seme=$seme_year_seme&class_id=$cti_class_id'>$class_name</td><td>$num </td></tr>\n" ;   
	  }	 
	 
     $school_num ++ ;
     $group_num += $num ;
   } 
	         
   echo "<tr><td>共 $school_num 班</td><td>共 $group_num 人</td></tr></table>\n<br>" ;  
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>項目</td><td>人數</td></tr>\n" ;   

	if ($id <> "") {
	
    $stmt = $mysqliconn->prepare("select data_get , count(*) as cc  from  cita_data where (kind = ?  and order_pos>=0) group by order_pos order by order_pos  ");
    $stmt->bind_param('s', $id);

   } 
 
   $stmt->execute();
   $stmt->bind_result($data_get,$num);		 
			  
   while ($stmt->fetch()) {   
     echo  "<tr><td>$data_get</td><td>$num </td></tr>\n" ;   
     $school_num_g ++ ;
     $group_num_g += $num ;
   } 
	         
   echo "<tr><td>共 $school_num_g 項</td><td>共 $group_num_g 人</td></tr></table>\n<br>" ;  
		 
			 
			 
			 
			 
   /*
   $sqlstr = " select class_id , count(*) as cc  from  cita_data where (kind = '$id'  and order_pos>-1) group by class_id   " ;
   $result =  $CONN->Execute($sqlstr) ;      
   while ($row = $result->FetchRow() ) {
     $class_id   = $row["class_id"] ;
     $class_name=class_id_to_full_class_name($class_id);
     $temp = explode("_",$class_id);
     $seme_year_seme = $temp[0].$temp[1];
     $cti_class_id = sprintf("%d%02d",$temp[2],$temp[3]);
     $num = $row["cc"] ;
	 if ($viewyn ==2)
	 {
	  $class_name=$temp[1];
      echo  "<tr><td>$class_name</td><td>$num </td></tr>\n" ;  		 
		 
	 }
	 
	 if ($viewyn ==1)
	 {
		 $class_name=$temp[3];
      echo  "<tr><td>$class_name</td><td>$num </td></tr>\n" ;   

	 }
	
	
	  if ($viewyn !=1 && $viewyn !=2)
	  {
      echo  "<tr><td><a href='show_class.php?seme_year_seme=$seme_year_seme&class_id=$cti_class_id'>$class_name</td><td>$num </td></tr>\n" ;   
	  }	 
	 
	 

	 
     $school_num ++ ;
     $group_num += $num ;
   } 
	         
   echo "<tr><td>共 $school_num 班</td><td>共 $group_num 人</td></tr></table>\n<br>" ;  
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>項目</td><td>人數</td></tr>\n" ;   
   
   
   $sqlstr = " select data_get , count(*) as cc  from  cita_data where (kind = '$id'  and order_pos>=0) group by order_pos order by order_pos  " ;
   $result =  $CONN->Execute($sqlstr) ;      
   while ($row = $result->FetchRow() ) {
    $data_get  = $row["data_get"] ;
       $num = $row["cc"] ;
     echo  "<tr><td>$data_get</td><td>$num </td></tr>\n" ;   
     $school_num_g ++ ;
     $group_num_g += $num ;
   } 
	         
   echo "<tr><td>共 $school_num_g 項</td><td>共 $group_num_g 人</td></tr></table>\n<br>" ;  
 
 
     $sqlstr =" select * from cita_data  where (kind = '$id'  and order_pos>-1) order by $der  " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
        $did = $row["id"] ;	
        $item = $row["item"] ;
        $order_pos = $row["order_pos"]+1 ;		
        $stud_name = $row["stud_name"] ;
		
		if($viewfullname==2)
		{
	     $stud_name_replace = preg_replace("/&#([0-9]{5});/", "○",$stud_name);
		 
		 if ($stud_name_replace==$stud_name)
         {			 
         $stud_name=str_replace(mb_substr($stud_name,1,1,"BIG5"),"○",$stud_name);
		 }
		 else
		 {
		 $stud_name=$stud_name_replace;	 
		 }	 
		}
		
		$guidance_name = $row["guidance_name"] ;
		$stud_num = $row["num"] ;
        $data_get = $row["data_get"] ;
        $data_input = $row["data_input"] ; 
			
     	
	    $up_date = $row["up_date"] ;      
		if($date=='no')$up_date="";
        $class_id = $row["class_id"] ;              
        $stud_id = $row["stud_id"] ;  
		$class_name=class_id_to_full_class_name($class_id);
        $temp = explode("_",$class_id);
        $seme_year_seme = $temp[0].$temp[1];
        $cti_class_id = sprintf("%d%02d",$temp[2],$temp[3]);
     
        echo "<tr align='center'> 
            <td >$data_get</td>";
       	if ($viewyn !=1 && $viewyn !=2)echo "<td><a href='show_class.php?seme_year_seme=$seme_year_seme&class_id=$cti_class_id'>$class_name</td>";
      	if ($viewyn ==2)echo "<td>$temp[1]</td>";
      	if ($viewyn ==1)echo "<td>$temp[3]</td>";
			
		if ($viewyn !=2)echo "<td>$stud_num</td>";
		echo "<td>$stud_id</td>";
        if ($viewfullname !=1) echo "<td ><a href='show.php?cita_year=$cita_year&stud_id=$stud_id'>$stud_name</a></td>";
		echo "<td >$guidance_name</td>
	          <td >$up_date</td>
         </tr>" ;
   
   }           
   echo "</table>" ;  
   
   //統計 -------------------------------------------------------
   //學校、組數統計	
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'>";
   if ($viewyn ==2)echo "<td>年級</td>";
   if ($viewyn !=2)echo "<td>班級</td>";
   echo "<td>人數</td></tr>\n";
   
   $sqlstr = " select class_id , count(*) as cc  from  cita_data where (kind = '$id'  and order_pos>-1) group by class_id   " ;
   $result =  $CONN->Execute($sqlstr) ;      
   while ($row = $result->FetchRow() ) {
     $class_id   = $row["class_id"] ;
     $class_name=class_id_to_full_class_name($class_id);
     $temp = explode("_",$class_id);
     $seme_year_seme = $temp[0].$temp[1];
     $cti_class_id = sprintf("%d%02d",$temp[2],$temp[3]);
     $num = $row["cc"] ;
	 if ($viewyn ==2)
	 {
	  $class_name=$temp[1];
      echo  "<tr><td>$class_name</td><td>$num </td></tr>\n" ;  		 
		 
	 }
	 
	 if ($viewyn ==1)
	 {
		 $class_name=$temp[3];
      echo  "<tr><td>$class_name</td><td>$num </td></tr>\n" ;   

	 }
	
	
	  if ($viewyn !=1 && $viewyn !=2)
	  {
      echo  "<tr><td><a href='show_class.php?seme_year_seme=$seme_year_seme&class_id=$cti_class_id'>$class_name</td><td>$num </td></tr>\n" ;   
	  }	 
	 
	 

	 
     $school_num ++ ;
     $group_num += $num ;
   } 
	         
   echo "<tr><td>共 $school_num 班</td><td>共 $group_num 人</td></tr></table>\n<br>" ;  
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>項目</td><td>人數</td></tr>\n" ;   
   $sqlstr = " select data_get , count(*) as cc  from  cita_data where (kind = '$id'  and order_pos>=0) group by order_pos order by order_pos  " ;

	$result =  $CONN->Execute($sqlstr) ;      
   while ($row = $result->FetchRow() ) {
    $data_get  = $row["data_get"] ;
       $num = $row["cc"] ;
     echo  "<tr><td>$data_get</td><td>$num </td></tr>\n" ;   
     $school_num_g ++ ;
     $group_num_g += $num ;
   } 
	         
   echo "<tr><td>共 $school_num_g 項</td><td>共 $group_num_g 人</td></tr></table>\n<br>" ;  
*/
              
?>
