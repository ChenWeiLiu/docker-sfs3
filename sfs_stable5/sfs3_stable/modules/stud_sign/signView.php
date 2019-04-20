<?php

// $Id: signView.php 7712 2013-10-23 13:31:11Z smallduh $

include "config.php";

sfs_check();
$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
if ( !checkid($SCRIPT_FILENAME,1)){
    Header("Location: signList.php"); 
}

$id = $_GET['id'] ;
$Submit = $_POST['Submit'] ;

if ($Submit == "匯出CSV格式檔") 
   dl_csv() ;
   
//清除細項報名資料   
if (($_GET['do']=='empt') and $_GET['id'] and $_GET['iid']) {
   $sqlstr = " delete from sign_data   where kind='$_GET[id]' and item = '$_GET[iid]'  " ;
   $result = $CONN->Execute($sqlstr)	;	
	echo $sqlstr ;
}
   
head("報名資料") ;
print_menu($menu_p);

$main = ShowView()   ;
   
echo $main ;
foot() ;




function ShowView() {
    global  $CONN ,$id ,$STUD_FIELD ;    
    $class_base_p = class_base();    
        
    $sqlstr =" select *  from sign_kind  where id = '$id'   " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;
      $id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["doc"] ;	
      $title = $row["title"] ;
      $input_classY = $row["input_classY"] ;
      $kind_set = $row["kind_set"] ;
      $data_item = $row["data_item"] ;
      $input_data_item = $row["input_data_item"] ;
      $admin = $row["admin"] ;
      $helper = $row["helper"] ;
      $title = $row["title"] ;
      
      //年級限制
      $class_year_arr = split (",", $input_classY);      
      //if (in_array ($key,$class_year_arr) )

      
      //項目
      $tmparr = split (",", $kind_set);   
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {
      	if ($tmparr[$i-1]<>"") {
      	   $ni = $i ;	
      	   
      	   $tmparr1 = split ("##",$tmparr[$i-1]) ; //代號|類別名|正取|備取|文字說明
      	   $kind_arr[] = $tmparr1 ;
      	   $kind_name[$tmparr1[0]] = $tmparr1[1] ;
      	   $kind_max[$tmparr1[0]]  =  $tmparr1[2] ;
        }  
      }    
      $max_kind = $ni ;
      
      
      //一併匯出
      $data_item_arr = split (",", $data_item);   
      $max_get = count($data_item_arr) ;
      
      
      //額外欄位
      $tmparr="" ;
      $ni = 0 ;
      $tmparr = split (",", $input_data_item);   
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {

      	if ($tmparr[$i-1]<>"") {
      	   $ni = $i ;	
      	   $input_arr[$i-1] = split ("##",$tmparr[$i-1]) ; //代號|欄名|格式代號|長度|預設值,      
      	}
      } 	 
      $max_input = $ni ;  
      
      //標題行
      $tab_title = "<table cellSpacing=0 cellPadding=4 width='100%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
          <tr bgcolor='#66CCFF'> 
            <td >類別</td>
            <td >班級</td>
            <td >順序</td>
            <td >正取標記</td>
            <td >姓名</td> \n" ;
            
            for ($i_i = 0 ; $i_i < $max_input ; $i_i ++) {
              $tab_title .= "<td >".$input_arr[$i_i][1]."</td>" ;
            }  
            for ($d_i = 0 ; $d_i < $max_get ; $d_i ++) 
              $tab_title .= "<td >".$STUD_FIELD[$data_item_arr[$d_i]] ."</td>\n" ;   
       $tab_title .= "</tr>\n" ;       
              
    $main .= "<div align='center'><form name='form1' method='post' action=''>
      <input type='submit' name='Submit' value='匯出CSV格式檔'>
     </form></div> \n  \n " ;
    
    
    
   //統計 -------------------------------------------------------
   //學校、組數統計	
   foreach ($kind_name as $item => $item_name ) 
        $td_item_name.="<td>$item_name(<a href=\"javascript:if(confirm('確定要刪除?\\n會清除此細項的報名資料！'))location='signView.php?id=$id&iid=$item&do=empt'\">清空)</a></td>" ;
   $main .= "<br><table cellSpacing=0 cellPadding=4 width='70%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>班級統計</td>$td_item_name<td>合計人數 </td></tr>\n" ;  
   
   //依班別統計           
   $sqlstr = " select class_id ,item , count(*) as cc  from  sign_data where kind ='$id' group by class_id ,item   " ;
   $result =  $CONN->Execute($sqlstr) ;     
   //echo $sqlstr ; 
   while ($row = $result->FetchRow() ) {
     $class_num   = $row["class_id"] ;
     $item = $row["item"] ;
     $num = $row["cc"] ;
     $all_sum_data[$class_num][$item] = $num ;
     //$main .= "<tr><td> $class_base_p[$class_num] </td><td>$num </td></tr>\n" ;   
     if ($item<>0 )     //該班已填報，但為空值
        $group_num += $num ;
   }   
     
   
   foreach( $all_sum_data as $class_num => $item_arr ) {
       $class_sum = 0 ;
       $td_main ="" ;
       $school_num ++ ;
       foreach($kind_name as $item => $item_name) {
           $class_sum += $all_sum_data[$class_num][$item] ;
           $td_main .= "<td>" . ($all_sum_data[$class_num][$item]+0) ."</td>" ;
       } 
       $main .= "<tr><td> $class_base_p[$class_num] </td>$td_main<td>$class_sum </td></tr>\n" ;   
   }   
   $main .= "<tr><td>$school_num </td><td>$group_num </td></tr></table>\n<br>" ;   
   
   $main .= "<table cellSpacing=0 cellPadding=4 width='50%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
             <tr bgcolor='#66CCFF'><td>組別統計</td><td>報名人數 </td></tr>\n" ;   
             
   //依組別統計          item 0 表示班級有填報但為空值
   $sqlstr = " select item , count(*) as cc  from  sign_data where kind ='$id' and item<>'0' group by item   " ;
   $result =  $CONN->Execute($sqlstr) ;     

   while ($row = $result->FetchRow() ) {
     $item   = $row["item"] ;
     $num = $row["cc"] ;
     $main .= "<tr><td> $kind_name[$item] </td><td>$num </td></tr>\n" ;   

     $tearm_num += $num ;
   }          
   $main .= "<tr><td>共計</td><td>$tearm_num </td></tr></table>\n<br>" ;   
   
                     


    //---------------------------------------------------------
    
    $main .=  $tab_title ."\n" ;
    //班上報名資料
    $sqlstr =" select *  from sign_data   where kind = '$id'  and item <>0 order by item ,class_id , order_pos  " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
        $did = $row["id"] ;	
        $item = $row["item"] ;
        $order_pos = $row["order_pos"]+1 ;
        $stud_name = $row["stud_name"] ;
        $data_get = $row["data_get"] ;
        $data_input = $row["data_input"] ;    
        $class_id = $row["class_id"] ; 
        
        // $d_stud_name =  $stud_name ;
        if ($order_pos > $kind_max[$item])
           $mark = "備取" ;
        else 
           $mark = "正取" ;   
        //$data_get = addslashes ($data_get) ; 
        //echo $data_get ;
        $d_data_get_arr="" ;
        $d_data_input_arr="" ;
        
        if ($data_get)  
           $d_data_get_arr = preg_split  ("/##/",$data_get) ;
        if ($data_input)  
           $d_data_input_arr = preg_split  ("/##/",$data_input) ;
        
        $main .="<tr > 
            <td > $kind_name[$item] </td>
            <td >$class_base_p[$class_id]</td>
            <td >$order_pos</td>
            <td >$mark</td>          
            <td >$stud_name</td>\n" ;
            
            //輸入欄位
            for ($i_i = 0 ; $i_i < $max_input ; $i_i ++) {
              $main .="<td>". $d_data_input_arr[$i_i] . "&nbsp;</td> \n" ;
            }
              
            //匯出欄位  
            for ($d_i = 0 ; $d_i < $max_get ; $d_i ++) {  
               $main .="<td >".$d_data_get_arr[$d_i] . "&nbsp;</td>\n" ;
            }
        
          $main .="</tr>\n" ;
   
   }           
   $main .="</table>" ;  
   

    
   return $main ;
}

//下載標準csv檔
function dl_csv(){
    global $CONN , $id ,$STUD_FIELD ;    
    $class_base_p = class_base();    
        
    $sqlstr =" select *  from sign_kind  where id = '$id'   " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;
      $id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["doc"] ;	
      $title = $row["title"] ;
      $input_classY = $row["input_classY"] ;
      $kind_set = $row["kind_set"] ;
      $data_item = $row["data_item"] ;
      $input_data_item = $row["input_data_item"] ;
      $admin = $row["admin"] ;
      $helper = $row["helper"] ;
      $title = $row["title"] ;
      
      //年級限制
      $class_year_arr = split (",", $input_classY);      
      //if (in_array ($key,$class_year_arr) )

      
      //項目
      $tmparr = split (",", $kind_set);   
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {
      	if ($tmparr[$i-1]<>"") {
      	   $ni = $i ;	
      	   
      	   $tmparr1 = split ("##",$tmparr[$i-1]) ; //代號|類別名|正取|備取|文字說明
      	   $kind_arr[] = $tmparr1 ;
      	   $kind_name[$tmparr1[0]] = $tmparr1[1] ;
      	   $kind_max[$tmparr1[0]]  =  $tmparr1[2] ;
        }  
      }    
      $max_kind = $ni ;
      
      
      //一併匯出
      $data_item_arr = split (",", $data_item);   
      $max_get = count($data_item_arr) ;
      
      
      //額外欄位
      $tmparr="" ;
      $ni = 0 ;
      $tmparr = split (",", $input_data_item);   
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {

      	if ($tmparr[$i-1]<>"") {
      	   $ni = $i ;	
      	   $input_arr[$i-1] = split ("##",$tmparr[$i-1]) ; //代號|欄名|格式代號|長度|預設值,      
      	}
      } 	 
      $max_input = $ni ;  
      
      //標題行
       $row_data[0] = "報名時刻,類別,班級,順序,正取標記,姓名" ;
            
       for ($i_i = 0 ; $i_i < $max_input ; $i_i ++) {
               $row_data[0] .= "," .$input_arr[$i_i][1] ;
            }  
       for ($d_i = 0 ; $d_i < $max_get ; $d_i ++) 
               $row_data[0] .= "," . $STUD_FIELD[$data_item_arr[$d_i]]  ;   
     
              
                

    //---------------------------------------------------------
    //班上報名資料
    $sqlstr =" select *  from sign_data   where kind = '$id'  and item<>'0' order by item ,class_id , order_pos  " ;
	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row =$result->FetchRow() ) {
        $did = $row["id"] ;	
        $item = $row["item"] ;
        $order_pos = $row["order_pos"]+1 ;
        $stud_name = $row["stud_name"] ;
        $data_get = $row["data_get"] ;
        $data_input = $row["data_input"] ;    
        $class_id = $row["class_id"] ; 
		$update_time = $row["update_time"] ; 
        
        // $d_stud_name =  $stud_name ;
        if ($order_pos > $kind_max[$item])
           $mark = "備取" ;
        else 
           $mark = "正取" ;   

        $d_data_get_arr="" ;
        $d_data_input_arr="" ;
         
        if ($data_get)  
           $d_data_get_arr = split ("##",$data_get) ;
        if ($data_input)  
           $d_data_input_arr = split ("##",$data_input) ;
        
        $line_data ="$update_time,$kind_name[$item],$class_base_p[$class_id],$order_pos,$mark,$stud_name" ;
            
            //輸入欄位
        for ($i_i = 0 ; $i_i < $max_input ; $i_i ++) {
              $line_data .=",". $d_data_input_arr[$i_i]  ;
        }
              
            //匯出欄位  
        for ($d_i = 0 ; $d_i < $max_get ; $d_i ++) {  
               $line_data .=",". $d_data_get_arr[$d_i] ;
        }
        $row_data[] = $line_data ;

   
   }              

    


	$main=implode("\n",$row_data);
	
	$filename ="sign_data.csv" ;
	
	//以串流方式送出 ooo.csv

	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $main;
	
	exit;
	return;
}
              
?>