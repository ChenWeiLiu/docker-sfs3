<?php
// $Id: upload.php 5310 2009-01-10 07:57:56Z hami $
require "config.php";

  /*
  function microtime_float()
  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  */

 $updir = $savepath. $_GET['dir'] . '/' ;

 //讀取 $login_id.txt 判斷session 是否相同
 function check_sission($logid , $sid) {
 	  $lines = file("/tmp/photoview-".$logid);
 	  /*
      list($session_id,$end_time) = explode("--", $lines[0]);
      $now_time = microtime_float() ;
 	  if (( $session_id == $sid ) and ($now_time <= $end_time) )
 	  */
 	  if ( $lines[0] == $sid ) 
 	     return true ;
 	  else 
 	     return false ;
 	        
 }	


 if ( check_sission($_GET['lid'] ,$_GET['sid']) ){
     //echo $updir ;
     //轉成 BIG5 碼
     // $fn = mb_convert_encoding($_FILES['Filedata']['name'] ,"big5"   ,"utf-8"  ) ;
     $fn = $_FILES['Filedata']['name'] ;
     //saveFile ("/tmp/t1.txt" ,"aaa".  $fn) ;
     //saveFile ("/tmp/t2.txt" , $_FILES['Filedata']['tmp_name'] . '  ' .$_FILES['Filedata']['name'] . '  ' .$updir. $fn ) ;
     move_uploaded_file($_FILES['Filedata']['tmp_name'], $updir.$fn);
     
     $filelist =$updir.$fn ;
     //圖最大寬度
     ImageResized($filelist , $filelist ,$BIG_PIC_X ,$BIG_PIC_Y) ; 
     //縮圖
     $smail_jpg = $updir. "!!!_" . $fn ;
     ImageResized($filelist , $smail_jpg ,160 ,120) ; 
 }

?>
