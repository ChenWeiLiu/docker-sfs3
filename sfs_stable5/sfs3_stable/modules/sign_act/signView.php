<?php

// $Id: signView.php 7710 2013-10-23 12:40:27Z smallduh $

include "config.php";

sfs_check();

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
$Submit = $_POST['Submit'] ;


if ( !checkid($SCRIPT_FILENAME,1)){
    Header('Location: index.php'); 
}

if ($Submit == "匯出excel格式檔") {
   $id = $_POST['id'] ;
   dl_csv($id) ;
}   

$PHP_SELF = $_SERVER["PHP_SELF"] ;   
$id = $_GET['id'] ;

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
}
   
head("報名資料") ;
print_menu($menu_p);
if ($id)
   $main = ShowView($id) ;
else 
   $main = DoList() ;   
   
   
echo $main ;
foot() ;

function DoList() {
  global $PHP_SELF ,$CONN ;	
  //顯示各項資料	
  $sqlstr = " select * from  sign_act_kind  order by id DESC " ;
  $result =  $CONN->Execute($sqlstr) ;  
  
  $main = "<table cellSpacing=0 cellPadding=4 width='80%' align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF'>
          <tr bgcolor='#66CCFF'><td>項目</td><td>管理</td></tr>\n" ;

  if($result) {
	while ($row=$result->FetchRow()) {        
  	  $title = $row["act_name"] ;
  	  $id = $row["id"] ;
  	  $main .= "<tr><td>$title</td><td><a href='$PHP_SELF?id=$id'>報名資料(僅姓名)</a> - 
  	  <a href='signView2.php?id=$id'>報名資料</a> - 
  	  <a href='sign_act_Admin.php?id=$id&do=edit'>修改報名單</a></td>
  	  </tr>" ;
  	}  
  	
  }	
  $main .= "</table> \n" ;
  return $main ;
}	

function ShowView($id) {
  global $PHP_SELF ,$member_set_arr , $member_set ,$CONN;	
  
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
  
  $main .=  "<form name='form1'  method='post' >
    <input type='hidden' name='id' value='$id'>
    <div align='center'><input type='submit' name='Submit' value='匯出excel格式檔'></div>
   </form>\n" ;
  
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
      $set_passwd = $row["set_passwd"] ;	

      $school_name = $row["school_name"] ;
      $data = $row["data"] ;	
	

      $main .= "<tr><td>$school_name<br>$set_passwd</td><td>$team_id</td> \n" ;
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
  }	
  return $main ;
}

//下載標準csv檔

function dl_csv($id){

    global $PHP_SELF ,$member_set_arr , $member_set ,$CONN ;
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
  $main .= "<table border=1 >
          <tr >
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
      $set_passwd = $row["set_passwd"] ;	

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
  }	
	$filename ="sign_data1.xls" ;
	
	//以串流方式送出 ooo.csv

	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	
	header("Expires: 0");
	echo "<html>$main </html>";
	exit;
	return;

  }
              
?>