<?php
// $Id: photo_admin2.php 5310 2009-01-10 07:57:56Z hami $

  require "config.php";

  //$debug = 1;
  // 認證檢查
  sfs_check();
  
  /*
  function microtime_float()
  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  */
  
  function dosmalljpg($updir) {
     global $chkfile ;
     
     if (WIN_PHP_OS()) {  
        return ;
     }   
     chdir($updir) ;
     $dirs = dir($updir) ;
     $dirs ->rewind() ;
     while ( $filelist = $dirs->read()) {
     	 if (($filelist!=".") && ($filelist!="..")){
     	   if (!strstr($filelist,'!!!_')) {  	//非縮小圖	
     	     for ($j=0;$j<count($chkfile);$j++){  
     	       if (strstr(strtolower($filelist),$chkfile[$j])){  //為 jpg圖檔
     	         if ($debug) echo "圖檔要縮圖: $filelist" ;
     	         
     	         //圖最大寬度
     	         ImageResized($filelist , $filelist ,$BIG_PIC_X ,$BIG_PIC_Y) ; 
     	         
     	         //縮圖
     	         $smail_jpg = "!!!_" . $filelist ;
     	         ImageResized($filelist , $smail_jpg ,160 ,120) ; 
     	         //system("djpeg -pnm \"$filelist\" | pnmscale -xscale 0.2 -yscale 0.2 | cjpeg > \"$smail_jpg\" ");
     	       }
     	     }    
           }  
         }
     }
     $dirs->close() ;  	
  }  	  
  
  $Submit = $_POST['Submit'] ;
  $do = $_GET['do'] ;
  $id = $_GET['id'] ;
  $session_log_id = $_SESSION['session_log_id'] ;
  $session_tea_name = $_SESSION['session_tea_name'] ;

    
  if ($do=="exit") {
    header("Location:photo.php" ) ; 
    exit ;
  }  
  
  if ($_GET['step']==3) {
    @unlink("/tmp/photoview-".$session_log_id );        
    header("Location:photo.php" ) ; 
    exit ;    
  }  
  
    
  $nday = date("Y-m-d") ;
  $step = 0 ;
  
  
//新增---------------------------------------------------------  
  if ($Submit == "下一步" ) {
     $nday = date("Y-m-d") ;
     
     $Iact_date = $_POST['Iact_date'] ;
     $Iact_name = $_POST['Iact_name'] ;
     $Iact_info = $_POST['Iact_info'] ;
     $Iuser = $_POST['Iuser'] ;
     
     
     //建立目錄(以建立日期)
     chdir($savepath) ; 	
     $dirstr = "$nday" ;
     $count = 0 ;
     while (is_dir($dirstr)) {
     	$count ++ ;
     	$dirstr = "$nday-" . $count;
     }	
     mkdir($dirstr , 0755) ;
     
     //$updir = $savepath. $dirstr . '/' ;
     //要做認證的文字檔案
     $session_name =  session_name() ; // 預設值為 PHPSESSID
     /*
     $time_end = (microtime_float()+600);
     saveFile ("/tmp/photoview-".$session_log_id  ,$_REQUEST[$session_name].'--'.$time_end ) ;
     */
     saveFile ("/tmp/photoview-".$session_log_id  ,$_REQUEST[$session_name]) ;
     
     $sqlstr = "insert into $tbname (act_ID,act_date,act_name,act_info,act_dir,act_postdate,act_auth,act_view)
        values ( '0', '$Iact_date', '$Iact_name' ,'$Iact_info',  '$dirstr',  '$nday' , '$Iuser','0') " ;
     $result = $CONN->Execute( $sqlstr) ;      
          
     $step = 2 ;
  } 

	
//更新---------------------------------------------------------    	
  if ($Submit == "更新" ) {
     $Iact_date = $_POST['Iact_date'] ;
     $Iact_name = $_POST['Iact_name'] ;
     $Iact_info = $_POST['Iact_info'] ;
     $Iact_ID = $_POST['Iact_ID'] ;
     $Ioldicon = $_POST['Ioldicon'] ;
     $Ipath = $_POST['Ipath'] ;
     $chkdelfile = $_POST['chkdelfile'] ;
     
     $updir = $savepath . $Ipath . '/' ;     
     

     //要把檔案刪除
     $numi = count($chkdelfile);
     if ($numi) {
       for ($i=0 ; $i<$numi ;$i++) {
     	 $delfile =$chkdelfile[$i] ;
         unlink($updir.$delfile);
       } 
     }       
     

     //壓縮檔上傳要做解壓縮     
     if (is_uploaded_file($_FILES['Iact_zip']['tmp_name'])) {

        $filename=  $_FILES['Iact_zip']['name']  ;        
        move_uploaded_file($_FILES['Iact_zip']['tmp_name'],  $updir . $filename);
        $tmpfilename = $updir . $filename ;
        //chdir($updir) ; 
        //exec(escapeshellcmd("unzip $tmpfilename ")) ;
        p_unzip($tmpfilename , $updir) ;
        
        unlink($tmpfilename);        
     }     
          
     //有數個上傳動作  
     $upfile = $_FILES['Iupload'] ;
     for ($i = 0 ; $i < count($upfile) ; $i++) {
         if (is_uploaded_file($upfile['tmp_name'][$i])) {
             move_uploaded_file($upfile['tmp_name'][$i] ,  $updir .$upfile['name'][$i]);
         }
     }   
          
    

     
     //製作小圖
     dosmalljpg($updir) ;     
     
     $sqlstr = "update  $tbname set act_name='$Iact_name' ,act_date='$Iact_date', act_info='$Iact_info' where act_ID = '$Iact_ID' " ;
     if($debug) echo "$sqlstr <br>" ;
     $result = $CONN->Execute( $sqlstr) ;      
 

     redir("photo.php" ,3) ; 
     echo "更新一筆完成！" ;
     exit ; 
  }
  
//確定刪除---------------------------------------------------------      
  if ($do == "del2" ) {
     $dpath = $_GET['dpath'] ;
     $updir = $savepath .  $dpath . '/' ;
     chdir($updir) ;
     $dirs = dir($updir) ;
     $dirs ->rewind() ;
     while ( $filelist = $dirs->read()) {
     	 if (($filelist!=".") && ($filelist!="..")){
     	   if ($debug) echo "del $updir $filelist" ;
           unlink($updir.$filelist);      	
         }
     }
     $dirs->close() ;
     rmdir($updir); 

     $sqlstr = "delete  from $tbname  where act_ID = $id " ;
 
     if($debug) echo "$sqlstr <br>" ;
     $result = $CONN->Execute( $sqlstr) ;      
     redir("photo.php" ,1) ; 
     echo "刪除動作完成！" ;
     exit ; 
  }  
  
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
$smarty->template_dir = $template_dir;
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","相片展管理");

$smarty->assign("now_date",date("Y-m-d"));

$smarty->assign("dir",$dirstr);
$smarty->assign("PHP_SELF",basename($PHP_SELF));
$smarty->assign("Iact_name",$Iact_name);
$smarty->assign("session_tea_name",$session_tea_name);
$smarty->assign("login_id",$_SESSION['session_log_id']);
$smarty->assign("session_id",$_REQUEST["PHPSESSID"]);


if ($step == 2) 
   $smarty->display("admin_add2.htm");
else    
   $smarty->display("admin_add1.htm");
  
?>
