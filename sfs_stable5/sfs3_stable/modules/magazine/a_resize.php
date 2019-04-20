<?php
//$Id: a_resize.php 5310 2009-01-10 07:57:56Z hami $
  include_once( "config.php") ;

  // --認證 session 
  sfs_check();
  
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}        
  
//非管理者 
$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
if ( !checkid($SCRIPT_FILENAME,1)){
      Header("Location: index.php"); 
}        
       
//-----------------------------------------------------------------  
  function dosmalljpg_dir($updir) {
     //把該目錄中的圖檔轉為 1/10 的小圖 	
     //global $chkfile ;
     $chkfile=array(".jpg",".jpeg");	//只可以上傳jpg格式圖檔
     
     chdir($updir) ;
     $dirs = dir($updir) ;
     $dirs ->rewind() ;
     while ( $filelist = $dirs->read()) {
     	 if (($filelist!=".") && ($filelist!="..")){
     	   if (!strstr($filelist,'___')) {  	//非縮小圖	
     	     for ($j=0;$j<count($chkfile);$j++){  
     	       if (strstr(strtolower($filelist),$chkfile[$j])){  //為 jpg圖檔
     	         if ($debug) echo "圖檔要縮圖: $filelist" ;
     	         $smail_jpg = "___" . $filelist ;
     	         system("djpeg -pnm \"$filelist\" | pnmscale -xscale 0.15 -yscale 0.15 | cjpeg > \"$smail_jpg\" ");
     	       }
     	     }    
           }  
         }
     }
     $dirs->close() ;  	
  }  
  
  dosmalljpg_dir($basepath . $dopath) ;	 
  
  header("location:a_pagemode.php") ;
  foot();
?>