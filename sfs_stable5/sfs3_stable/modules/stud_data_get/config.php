<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
//您可以自己加入引入檔

function get_str_to_array($put_data) {


   $LineWord = preg_split ("/\n/", $put_data);     //分換行
   $all_stud= count( $LineWord) ;

   for ($i=0 ; $i < $all_stud ; $i++) {
     $keywords = "" ;
     $keywords = preg_split ("/[\s,]+/", $LineWord[$i]);     //分隔
     $ngroup   = count( $keywords) ;
     if ($ngroup >1) {  
       for ($j=0 ; $j < ($ngroup-1)  ; $j++) {
          $doarr[$i][$j] = $keywords[$j] ;//放入陣列中
       }
       $nall_stud ++ ;
     }
   }

   return $doarr ;

}
?>
