<?php

// $Id: signView_open.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

//sfs_check();

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
$Submit = $_POST['Submit'] ;



/*
if ($Submit == "匯出CSV格式檔") {
   $id = $_POST['id'] ;
   dl_csv($id) ;
}   
*/
$PHP_SELF = $_SERVER["PHP_SELF"] ;   
$id = $_GET['id'] ;
/*
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
}
*/   
head("報名資料") ;
print_menu($menu_p);
if ($_GET['id']) 
   $main = ShowView($_GET['id']) ;
 

   
echo $main ;
foot() ;


function ShowView($id) {
  global $PHP_SELF ,$member_set_arr , $member_set ,$CONN;	
  
  $item_num = 0 ;
  
  //報名單資料
  $sqlstr = " select * from  sign_act_kind where id = '$id'  " ;
  $result =  $CONN->Execute($sqlstr) ;  
  $row = $result->FetchRow() ;
      //$id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["act_doc"] ;	
      $title = $row["act_name"] ;
      $act_passwd = $row["act_passwd"] ;
      $team_set = $row["team_set"] ;
      $max_team = $row["max_team"] ;
      $max_each = $row["max_each"] ;
      $member_set = $row["member_set"] ;
      $fields_set = $row["fields_set"] ;
      
      

  //成員 (人數)   
  $tmparr = split (",", $member_set) ;      //隊長*1,帶隊*2,隊長*1,隊員*4
  for ($i= 1 ; $i <= count($tmparr) ; $i++) {
    if ($tmparr[$i-1]<>"") {
      	$ni = $i ;	  
  	$tmp_arr1 = split ("\*",$tmparr[$i-1]) ;    
  	$member_set_arr[] = $tmp_arr1 ;
    } 	  
  }  
  
  //欄位 (自行輸入)   
  $tmparr = split (",", $fields_set) ;      //身份証字號|10|預設值|說明,生日|6|預設值|說明
  $fields_count = count($tmparr) ;
  for ($i= 1 ; $i <= count($tmparr) ; $i++) {
    if ($tmparr[$i-1]<>"") {
      	$ni = $i ;	  
  	$tmp_arr1 = split ("##",$tmparr[$i-1]) ;    
  	$fields_set_arr[] = $tmp_arr1 ;
    } 	  
  }    
  

  
  //標題列
  $main .= "<table cellSpacing=0 cellPadding=4 width='100%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
          <tr bgcolor='#66CCFF'>
          <td>校名</td><td>組別</td>" ;
  
     //成員
     for($i=0 ; $i < count($member_set_arr) ; $i ++) {
        for ($m = 0 ; $m < $member_set_arr[$i][1] ; $m ++) {
            $main .= "<td>" . $member_set_arr[$i][0] ."</td>\n" ; 
        }
     }  
      	           
  $main .="</tr>\n" ;
  
  //顯示報名隊伍各項資料	
  $sqlstr = " select * from  sign_act_data where pid='$id' order by school_name ,team_id   " ;
  $result =  $CONN->Execute($sqlstr) ;  

  while ($row = $result->FetchRow() ) {
      $did_arr = $row["did"] ;	
      $team_id = $row["team_id"] ;	
     // $set_passwd = $row["set_passwd"] ;	

      $school_name = $row["school_name"] ;
      $data = $row["data"] ;	
	

      $main .= "<tr><td>$school_name</td><td>$team_id</td> \n" ;
      //欄位    
      $member_data_arr="" ;
      $tmparr = split (",", $data) ;      //(姓名|欄位1|欄位2,姓名|欄位1|欄位2)
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {
      	    $tmp_arr1 = @split ("##",$tmparr[$i-1]) ;    
      	    $member_data_arr[] = $tmp_arr1 ;
      	    $main .="<td>" .$member_data_arr[$i-1][0] ;
      	    
      	     $main .= "</td>\n" ;
      }    
      $main .="</tr>\n" ;
      $item_num ++ ;
  }	
  $main .="</table>\n " ;
  $main .= "共 $item_num 組 " ;
  return $main ;
}


              
?>