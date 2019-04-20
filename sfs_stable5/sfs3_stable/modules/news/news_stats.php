<?php
// $Id: news_stats.php 5468 2009-04-28 13:40:04Z infodaes $
  require "config.php";
  
  //sfs_check();
  
  $txt_beg = $_POST["txt_beg"] ;
  $txt_end = $_POST["txt_end"] ;

  if (!$txt_beg) {
      //$txt_end = date("Y-m-d") ;
      $txt_beg = GetdayAdd(date("Y-m-d"), 365 * -1) ;
  }
   if (!$txt_end) {
      $txt_end = date("Y-m-d") ;
      //$txt_beg = GetdayAdd(date("Y-m-d"), 365 * -1) ;
  }

  //讀取資料庫
  $sqlstr = "SELECT poster_job , count(*) as dd  FROM $tbname  where msg_date >= '$txt_beg' and msg_date <= '$txt_end' and poster_job <>'' group by  poster_job " ;

  $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
  while ($row = $result->FetchRow() ) {
      $job_array[ $row["poster_job"] ] =  $row["dd"] ;


  }

    //使用樣版
    $template_dir = $SFS_PATH."/".get_store_path()."/templates";

    // 使用 smarty tag
    $smarty->left_delimiter="{{";
    $smarty->right_delimiter="}}";
    //$smarty->debugging = true;
    $smarty->assign("content_file",$content_file);

    $smarty->assign("template_dir",$template_dir);

			head();
    echo $smarty->fetch("$template_dir/stats.htm");
			foot();
?>
