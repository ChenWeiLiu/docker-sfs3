<?php
// $Id:  
require "config.php";
  //讀此筆記錄
  $_GET[id]=intval($_GET[id]);
  $sqlstr = " select * from $tbname where act_ID='$_GET[id]' " ;
  $result =$CONN->Execute( $sqlstr) ; 	
  $nb=$result->FetchRow()  ;  
 
  $picdir=$htmpath ."/" .$nb[act_dir] ;
  $updir=$savepath ."/" .$nb[act_dir] ;
     
  $act_dir =$nb[act_dir] ;

     //chdir($updir) ;
  $pic_i = 0 ;   
  $file_list = get_smaill_list($updir) ;
  
  foreach ($file_list as $k=>$v ) {
  	//
     $file_xml .= "<track>
			<title>".iconv("Big5","UTF-8",$nb['act_name'])."</title>
			<location>http://".$_SERVER["HTTP_HOST"]."$picdir/".rawurlencode($v)."</location>
		</track>" ;
  } 
 
 $main = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\">
	<trackList>
    $file_xml
	</trackList>
</playlist>" ;

echo  $main ;

  function get_smaill_list($updir){
     //取得小圖列表
   if ( is_dir($updir) ) {
 
     $dirs = dir($updir) ;
     $dirs ->rewind() ;
     while ( ($filelist = $dirs->read()) and !$stop_m) {
     	 if (($filelist!=".") && ($filelist!="..")){

     	       if ( (eregi("(.jpg|.jpeg|.png|.gif|.bmp)$", $filelist))  and !(strstr($filelist,'!!!_')) )
         	 $filelist_arr[] = $filelist ;

         }
     }
     $dirs->close() ;  	
     sort ($filelist_arr) ;
     return $filelist_arr ;
   }
  }
?>
