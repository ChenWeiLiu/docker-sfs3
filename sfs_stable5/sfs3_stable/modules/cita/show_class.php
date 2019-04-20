<?php

// $Id: show_class.php 8883 2016-04-27 08:16:48Z tuheng $

include "config.php";
$class_id = $_GET['class_id'];
$der = $_GET['der'];
$seme_year_seme = $_GET['seme_year_seme'];

if($seme_year_seme==''){
        $sel_year = curr_year(); //目前學年
        $sel_seme = curr_seme(); //目前學期
        $seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
}
$query = "SELECT  DISTINCT SUBSTRING(class_id,1,5) AS year_seme FROM cita_data ORDER BY year_seme DESC ";
$recordSet = $CONN->Execute($query) or trigger_error("SQL 錯誤 <br>$query",E_USER_ERROR);
$year_seme_arr = array();
while($row = $recordSet->FetchRow()){
	$temp = explode('_',$row['year_seme']);
	$year_seme_arr[$temp[0].$temp[1]] = $temp[0].'學年第'.$temp[1].'學期';
}
$seme_str = "<select name='seme_year_seme' onChange='this.form.submit()'>";
foreach($year_seme_arr as $year_seme=>$seme_name){
	$seme_str .="<option value='".$year_seme ."'";
	if ($year_seme == $seme_year_seme){
		$seme_str .=" selected";
	}
	$seme_str .=">$seme_name</option>";
}
$seme_str .="</select>";

if($der=="") $der="up_date desc";

    $class_base_p = class_base();            
$class_str = '<select name="class_id" onChange="this.form.submit()">';
// 取得班級下拉選單
foreach($class_base_p as $c_id=>$c_name){
	$class_str .= '<option value="'.$c_id.'"';
	if ($class_id == $c_id){
		$class_str .= ' selected="true"';
	}
	$class_str .= '>'.$c_name.'</option>';
} 
$class_str .= '</select>';
echo "<form method='get' action='{$_SERVER['php_SELF']}'> <p align=center><font size=5 color=red>$seme_str $class_str 的榮譽榜</font>　　<a href='list.php'>回目錄</a></form></p>";
      //標題行
    
	echo "<table cellSpacing=0 cellPadding=4 width='100%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
          <tr bgcolor='#66CCFF' align=center> 
            <td ><a href=$PHP_SELF?class_id=$class_id&seme_year_seme=$seme_year_seam&der=grada,up_date>▲</a>項目<a href=$PHP_SEL?class_id=$class_id&seme_year_seme=$seme_year_seam&der=grada%20desc,up_date>▼</a></td>
		<td >成績</td>";
	if ($viewfullname !=1)echo "<td >姓名</td>";
		echo "<td ><a href=$PHP_SELF?class_id=$class_id&seme_year_seme=$seme_year_seam&der=up_date>▲</a>日期<a href=$PHP_SELFclass_id=$class_id&seme_year_seme=$seme_year_seme&der=up_date%20desc>▼</a></td></tr>";              

     
     $cti_class_id = sprintf("%03d_%d_%02d_%02d",substr($seme_year_seme,0,3),substr($seme_year_seme,-1),substr($class_id,0,1),substr($class_id,-2));

	 //班上報名資料

///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
if ($cti_class_id <> "") {
    $stmt = $mysqliconn->prepare("select a.id,b.title,a.kind,a.order_pos,a.data_get,a.data_input,a.up_date,a.stud_name,b.doc,b.grada from cita_data a,cita_kind b where (a.kind=b.id and a.class_id=? and a.order_pos>-1) order by $der ,num");
    $stmt->bind_param('s', $cti_class_id);
} 
$stmt->execute();
$stmt->bind_result($did, $item, $kind, $order_pos, $data_get, $data_input, $up_date, $stud_name, $doc, $gra);

while ($stmt->fetch()) {
	$order_pos=$order_pos+1;
	echo "<tr> 
  		<td ><a href='view.php?id=$kind'>$doc</a><font size=2>---$grada[$gra]</font></td>
            <td >$data_get</td>";
			
         if ($viewfullname !=1)echo "<td >$stud_name</td>";          
	     echo "<td >$up_date</td>
         </tr>" ;
   
   }           
   echo "</table>" ;  
   
    //統計 -------------------------------------------------------
   //學校、組數統計	
  
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>項目</td><td>次數</td></tr>\n" ;   
   
if ($cti_class_id <> "") {
    $stmt = $mysqliconn->prepare("select b.grada , count(*) as cc  from  cita_data a,cita_kind b where (a.kind = b.id and a.class_id=?  and a.order_pos>-1) group by b.grada ");
    $stmt->bind_param('s', $cti_class_id);
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
    $sqlstr =" select *  from cita_data a,cita_kind b where (a.kind=b.id and a.class_id='$cti_class_id'  and a.order_pos>-1) order by $der ,num " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
        $did = $row["id"] ;	
        $item = $row["item"] ;
        $order_pos = $row["order_pos"]+1 ;      
        $data_get = $row["data_get"] ;
        $data_input = $row["data_input"] ;   
	    $up_date = $row["up_date"] ;    
        $stud_name = $row["stud_name"] ;              
        $doc = $row["doc"] ;  
	    $gra = $row["grada"] ;  

        echo "<tr> 
  		<td ><a href='view.php?id=$did'>$doc</a><font size=2>---$grada[$gra]</font></td>
            <td >$data_get</td>";
			
         if ($viewfullname !=1)echo "<td >$stud_name</td>";          
	     echo "<td >$up_date</td>
         </tr>" ;
   
   }           
   echo "</table>" ;  
   
   //統計 -------------------------------------------------------
   //學校、組數統計	
  
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>項目</td><td>次數</td></tr>\n" ;   
   $sqlstr = " select b.grada , count(*) as cc  from  cita_data a,cita_kind b where (a.kind = b.id and a.class_id='$cti_class_id'  and a.order_pos>-1) group by b.grada   " ;
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
