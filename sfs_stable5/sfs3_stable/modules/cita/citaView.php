<?php

// $Id: citaView.php 8443 2015-06-02 05:21:55Z smallduh $

include "config.php";
include "make_ooo.php";
//使用者認證
sfs_check();
$session_tea_sn =  $_SESSION['session_tea_sn'] ;

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
if ( !checkid($SCRIPT_FILENAME,1)){
    Header("Location: citaList.php"); 
}
if($der=="") $der="order_pos,class_id,num";
// 印獎狀
if (count ($sel_stud) >0 and $do_key=='列印'){	
	$title="獎狀";

	for($i=0;$i<count ($sel_stud);$i++){
		$data_arr[$i]["stud_id"]=sel_data($sel_stud[$i],1);
		$data_arr[$i]["head"]=sel_data($sel_stud[$i],2);
		$data_arr[$i]["body"]=sel_data($sel_stud[$i],3);		
	}
	ooo_class($title,$body1,$body2,$data_arr);
	exit();
}
if (count ($sel_del) >0 and $do_key=='刪除'){		
 	$now=date("Y-m-d");
	for($i=0;$i<count ($sel_del);$i++){
		$did=$sel_del[$i];		
		//$sqlstr="UPDATE `cita_data` SET `order_pos` = '-1',`teach_id` = '$session_tea_sn',`up_date` = '$now' where id=$did";
		$sqlstr="DELETE FROM `cita_data` where id=$did";  //原本為註記  `order_pos` = '-1' , 模組並無設計復原功能，故改為直接刪除

		 $result = $CONN->Execute($sqlstr) or user_error("刪除失敗！<br>$sqlstr",256) ; 
	}

}


    $class_base_p = class_base();            
    $sqlstr =" select *  from cita_kind  where id = '$id'   " ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;          
    $doc = $row["doc"];     
    $title = $row["title"];  
    $foot = $row["foot"];  
      $beg_date = $row["beg_date"];  
      $end_date = $row["end_date"];  
      $kind_set = $row["kind_set"] ;       
     //期限檢查    
if (date("Y-m-d")>=$beg_date and date("Y-m-d")<=$end_date) {
  
 ?>
	<script>
	function tagall(status) {		
		var i =0;

	  	while (i < document.chform.elements.length)  {
	    		if (document.chform.elements[i].name=='sel_stud[]') {
      			document.chform.elements[i].checked=status;
	    	}
	    	i++;
	  	}
	}
	function tagall_1(status) {		
		var i =0;

	  	while (i < document.chform.elements.length)  {
	    		if (document.chform.elements[i].name=='sel_del[]') {
      			document.chform.elements[i].checked=status;
	    	}
	    	i++;
	  	}
	}

	</script>
	
<?php


head("報名資料") ;
print_menu($menu_p); 


echo  "<font color=red >$doc</font><br>$helper";


	echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\" name=\"chform\">";

		echo '<input type="button" value="獎狀全選" onClick="javascript:tagall(1);">';
 		echo '<input type="button" value="取消" onClick="javascript:tagall(0);">　';
		echo '<input  type=checkbox name=ima value=1 checked>列印照片　';		
		echo "<input type='submit' name='do_key' value='列印'>　｜　";
  		echo '<input type="button" value="刪除全選" onClick="javascript:tagall_1(1);">';
 		echo '<input type="button" value="取消" onClick="javascript:tagall_1(0);">　';		
		echo "<input type='submit' name='do_key' value='刪除'>";
    
      //標題行
    
	echo "<table cellSpacing=0 cellPadding=4 width='100%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
          <tr bgcolor='#66CCFF'> 
            <td ><a href=$PHP_SELF?id=$id&der=order_pos,class_id,num>成績</a></td><td ><a href=$PHP_SELF?id=$id&der=class_id,order_pos,num>班級</a></td><td>座號</td><td>學號</td><td>學生姓名</td><td>指導者</td><td><a href=$PHP_SELF?id=$id&der=up_date%20desc>編修日期</a></td><td>獎狀</td><td>刪除</td></tr>";              

  
    //班上報名資料
     $sqlstr =" select *  from cita_data   where (kind = '$id'  and order_pos>-1) order by $der  " ;

	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
        $did = $row["id"] ;	
        $item = $row["item"] ;
        $order_pos = $row["order_pos"]+1 ;
        $stud_name = trim($row["stud_name"]) ;
		$stud_id = $row["stud_id"] ;
		$stud_num = $row["num"] ;
        $data_get = $row["data_get"] ;
        $data_input = $row["data_input"] ;   
	  $up_date = $row["up_date"] ;    
        $class_id = $row["class_id"] ;              
        $stud_id = $row["stud_id"] ;  
		$guidance_name = $row["guidance_name"] ; 
	$class_name=class_id_to_full_class_name($class_id);
             	$body=$data_get;
	$head=$class_name.$stud_name."同學";					
	$value=$stud_id."#".$head."#".$body;	
        echo "<tr> 
            <td >$data_get</td>
            <td >$class_name</td>
			<td >$stud_num</td>
			<td >$stud_id</td>
            <td >$stud_name</td>
			<td >$guidance_name</td>
	          <td >$up_date</td>
            <td ><input id=\"c_$stud_id\" type=\"checkbox\" name=\"sel_stud[]\" value=\"$value\"></td>
                   <td ><input id=\"d_$stud_id\" type=\"checkbox\" name=\"sel_del[]\" value=\"$did\"></td></tr>" ;

   }           
   echo "</table>" ;  
   
   //統計 -------------------------------------------------------
   //學校、組數統計	
   echo  "<br><table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>班級</td><td>人數</td></tr>\n" ;   
   $sqlstr = " select class_id , count(*) as cc  from  cita_data where (kind = '$id'  and order_pos>-1) group by class_id   " ;
   $result =  $CONN->Execute($sqlstr) ;      
   while ($row = $result->FetchRow() ) {
     $class_id   = $row["class_id"] ;
         $class_name=class_id_to_full_class_name($class_id);
     $num = $row["cc"] ;
     echo  "<tr><td>$class_name</td><td>$num </td></tr>\n" ;   
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

echo "<input type='hidden' name='body1' value=$title>";
echo "<input type='hidden' name='body2' value=$foot>";
echo "<input type='hidden' name='id' value=$id>";
echo "</form>";
foot() ; 
}else{
        Header("Location: view.php?id=$id");
}             
?>