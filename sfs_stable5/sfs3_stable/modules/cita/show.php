<?php
// $Id: show.php 8883 2016-04-27 08:16:48Z tuheng $
include "config.php";
$stud_id = $_GET['stud_id'];
$der = $_GET['der'];
$cita_year = $_GET['cita_year'];

if($der=="") $der="up_date desc";

$class_base_p = class_base();
	
///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
if ($stud_id <> "") {
    $stmt = $mysqliconn->prepare("select stud_name from stud_base where stud_id = ? and ($cita_year-stud_study_year<9)");
    $stmt->bind_param('s', $stud_id);
}

$stmt->execute();

$stmt->bind_result($stud_name);
$stmt->fetch();
$stmt->close();
///mysqli

	/*
    $sqlstr =" select stud_name  from stud_base where stud_id = '$stud_id' and ($cita_year-stud_study_year<9)" ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;          
    $stud_name = $row["stud_name"];   
	*/
		if($viewfullname==2)
		{
	     $stud_name_replace = preg_replace("/&#([0-9]{5});/", "○",$stud_name);
		 
		 if ($stud_name_replace==$stud_name)
         {			 
		 //$stud_name_replace=mb_substr($stud_name,1,1,"BIG5");
         $stud_name=str_replace(mb_substr($stud_name,1,1,"BIG5"),"○",$stud_name);
		 }
		 else
		 {
		 $stud_name=$stud_name_replace;	 
		 }	 
		}
   
   
   
echo " <p align=center><font size=5 color=red>$stud_name 的榮譽榜</font>　　<a href='list.php'>回目錄</a></p>";
      //標題行
    
	echo "<table cellSpacing=0 cellPadding=4 width='100%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
          <tr bgcolor='#66CCFF' align=center> 
            <td ><a href=$PHP_SELF?stud_id=$stud_id&der=grada,up_date>▲</a>項目<a href=$PHP_SELF?stud_id=$stud_id&der=grada%20desc,up_date>▼</a></td>
		<td >成績</td><td >年班</td>
		<td ><a href=$PHP_SELF?stud_id=$stud_id&der=up_date>▲</a>日期<a href=$PHP_SELF?stud_id=$stud_id&der=up_date%20desc>▼</a></td></tr>";              

  
    //班上報名資料

if ($stud_id <> "") {
	
    $stmt = $mysqliconn->prepare("select a.id,b.title,a.kind,a.order_pos,a.data_get,a.data_input,a.up_date,a.class_id,b.doc,b.grada from cita_data a inner join cita_kind b on a.kind=b.id where (a.stud_id = ?  and a.order_pos>-1) order by $der  ");
    $stmt->bind_param('s', $stud_id);

} 
$stmt->execute();
$stmt->bind_result($did, $item, $kind, $order_pos, $data_get, $data_input, $up_date, $class_id, $doc, $gra);

while ($stmt->fetch()) {

	$order_pos=$order_pos+1;
	$class_name=class_id_to_full_class_name($class_id);
	
	$tempx = explode("_",$class_id);
	 if ($viewyn ==2)
	 {
	 
     $class_name=$tempx[1]."年級";
     }
	 if ($viewyn ==1)
	 {
     $class_name=$tempx[3]."班";
     }
        		
		echo "<tr> 
  		<td ><a href='view.php?id=$kind'>$doc</a><font size=2>---$grada[$gra]</font></td>
            <td >$data_get</td>
		     <td >$class_name</td>          
	          <td >$up_date</td>
         </tr>" ;
   
   }           
   echo "</table>" ;  
 
   //統計 -------------------------------------------------------
   //學校、組數統計	
  
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>項目</td><td>次數</td></tr>\n" ;   
   
   if ($stud_id <> "") {
    $stmt = $mysqliconn->prepare(" select b.grada , count(*) as cc  from cita_data a,cita_kind b where (a.kind = b.id and a.stud_id=?  and a.order_pos>-1) group by b.grada   ");
    $stmt->bind_param('s', $stud_id);
} 

$stmt->execute();
$stmt->bind_result($data_get,$num);


while ($stmt->fetch()) {
	 echo  "<tr><td>$grada[$data_get]</td><td>$num </td></tr>\n" ;   
     $school_num_g ++ ;
     $group_num_g += $num ;
	
}
		
   echo "<tr><td>共 $school_num_g 項</td><td>共 $group_num_g 次</td></tr></table>\n<br>" ;  

	
	
/*	
     $sqlstr ="select * from cita_data a inner join cita_kind b on a.kind=b.id where ( a.stud_id = '$stud_id' and a.order_pos>-1) order by $der  " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
    while ($row = $result->FetchRow() ) {
        $did = $row["id"] ;	
        $item = $row["title"] ;
        $order_pos = $row["order_pos"]+1 ;      
        $data_get = $row["data_get"] ;
        $data_input = $row["data_input"] ;   
	    $up_date = $row["up_date"] ;    
        $class_id = $row["class_id"] ;              
        $doc = $row["doc"] ;  
	    $gra = $row["grada"] ;  

	$class_name=class_id_to_full_class_name($class_id);
	
	$tempx = explode("_",$class_id);
	 if ($viewyn ==2)
	 {
	 
     $class_name=$tempx[1]."年級";
     }
	 if ($viewyn ==1)
	 {
     $class_name=$tempx[3]."班";
     }
        
		
		echo "<tr> 
  		<td ><a href='view.php?id=$did'>$doc</a><font size=2>---$grada[$gra]</font></td>
            <td >$data_get</td>
		     <td >$class_name</td>          
	          <td >$up_date</td>
         </tr>" ;
   
   }           
   echo "</table>" ;  
   
   //統計 -------------------------------------------------------
   //學校、組數統計	
  
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>項目</td><td>次數</td></tr>\n" ;   
   $sqlstr = " select b.grada , count(*) as cc  from cita_data a,cita_kind b where (a.kind = b.id and a.stud_id='$stud_id'  and a.order_pos>-1) group by b.grada   " ;
   $result =  $CONN->Execute($sqlstr) ;      
   while ($row = $result->FetchRow() ) {
    $data_get  = $row["grada"] ;
       $num = $row["cc"] ;
     echo  "<tr><td>$grada[$data_get]</td><td>$num </td></tr>\n" ;   
     $school_num_g ++ ;
     $group_num_g += $num ;
   } 
	         
   echo "<tr><td>共 $school_num_g 項</td><td>共 $group_num_g 次</td></tr></table>\n<br>" ;  
  */
              
?>
