<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";

//取得模組參數設定
$m_arr = &get_module_setup("magazine");
extract($m_arr, EXTR_OVERWRITE);

    //上傳路徑
    set_upload_path("/school/magazine");
    set_upload_path("templates_c/magazine");
    
    //存放各圖型、文章所在(目錄權限為 777 )
    $htmlpath = $UPLOAD_URL ."school/magazine/" ;    //網頁相對目錄，最後加 /
    $basepath = $UPLOAD_PATH. "school/magazine/" ; //絕對目錄，最後加 /


    //smarty  設定檔
    //include "class/Smarty.class.php";
    define('__MAG_ROOT', $SFS_PATH . '/modules/magazine'); // 最後沒有斜線
    define('__MAG_HTML', $SFS_PATH_HTML . '/modules/magazine') ;
    
    $tpl = new Smarty();
    $tpl->template_dir = __MAG_ROOT . "/templates/";
    $tpl->compile_dir = $UPLOAD_PATH."templates_c/magazine/";

    //$tpl->config_dir = __MAG_ROOT . "/configs/";
    //$tpl->cache_dir = __MAG_ROOT . "/cache/";
    $tpl->left_delimiter = '<{';
    $tpl->right_delimiter = '}>';
    
    //樣版所在網頁路徑，最後加/
    $templetdir =__MAG_HTML . "/templates/" ;
    
    

  //類別選單
  function print_chap_item($book_num, $chap_num=0 , $chap ){
  	
  	foreach ($chap as $k => $v) {
  		if ($chap_num == $k) 
  		    $seled_str = "selected" ;
  		else 
  		    $seled_str = "" ;   
      $seletc_str .= "<option value='$k' $seled_str>$v</option>\n " ;
  
    }
    $main = "
    
    目前所在類別:<select name='chap_num' onChange='this.form.submit();' >
    $seletc_str
    </select>
    <input name='book_num' type='hidden' value='$book_num'>
  
    " ;
    return $main ;
  }  


  //轉為小圖
  function dosmalljpg($updir , $filelist) {
     //把該目錄中的圖檔轉為 1/10 的小圖 	
     global $debug ;
     chdir($updir) ;
     if ($debug) echo "圖檔要縮圖: $filelist" ;
     $smail_jpg = "___" . $filelist ;

     system("djpeg -pnm \"$filelist\" | pnmscale -xscale 0.1 -yscale 0.1 | cjpeg > \"$smail_jpg\" ");
  }  	
  
    //刪除目錄  
    function do_rmdir($updir) 
    {     
       if (is_dir($updir) ) {
           $dirs = dir($updir) ;
           @$dirs->rewind() ;
           while ( $filelist = $dirs->read()) {
           	 if (($filelist!=".") && ($filelist!="..")){
           	   if ($debug) echo "del $updir $filelist" ;
                 unlink($updir.$filelist);      	
               }
           }
           $dirs->close() ;
           rmdir($updir);  
           //echo $updir ;     
       }else {
          return ;
       } 
    
    }      
    
   
 

  //檢查管理者函式
    function check_is_man2($editors) {

      $session_log_id = $_SESSION[session_log_id] ;

      $flag = false;
      $perr_man = split("," , $editors) ;
      for ($i =0 ;$i < count ($perr_man);$i++)
        if (trim($perr_man[$i]) == "$session_log_id")
           $flag = true ; 
      return $flag;
    }


    $self_php = $_SERVER["PHP_SELF"] ;
//=======================================================================
    //取得全部的期別
    $sqlstr =  "select * from magazine  where is_fin <> '0'  " ;
    $sqlstr .= " order by num DESC  " ;
  
    $result = $CONN->Execute( $sqlstr) ;
    if ($result) 
        while ($row = $result->FetchRow() ) {
          $id=	 $row["id"]  ;
          $books[] = $row["id"] ; //取得期別    
          $mbooks_num[] = $row["num"] ;
          $mbooks_name[$id]= "第" . $row["num"] ."期" ;
        }   
   

    if (!$book_num) $book_num = $books[0] ;  //未指定表示最近一期

?>
