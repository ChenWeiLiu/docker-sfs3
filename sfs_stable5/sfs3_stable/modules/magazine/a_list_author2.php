<?php
//$Id: a_list_author2.php 7708 2013-10-23 12:19:00Z smallduh $
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
    
    $class_year_p = class_base($curr_year_seme); //班級    

    //期別選定===================================================
    if ($book_id) 
      $sqlstr =  "select * from magazine  where id='$id'  " ;  
    else 
      $sqlstr =  "select * from magazine  order by num DESC " ;   
    //目前最近一期
    if ($result) {
        $result = $CONN->Execute($sqlstr); 
        if ($result) {
              $row=$result->FetchRow();
              $book_num = $row["num"] ; //取得期別    
              $book_id = $row["id"] ;
              $publish_date = $row["publish_date"] ;
              $is_fin = $row["is_fin"] ;    
              $bdate = $row["ed_begin"] ;
              $edate = $row["ed_end"] ;
              $editors =  $row["admin"] ;         //編輯群

              if (date("Y-m-d")<$bdate or date("Y-m-d")>$edate) $is_timeout = 1 ;

        }
        else $empty_fg = TRUE ;
    }
    else {
      //找不到任何一期資料  
      $empty_fg = TRUE ;

    }  

  if (!check_is_man2($editors)) {
     echo "你非本期編輯群成員，無權執行此功能！" ;
     redir("paper_list.php?book_num=$book_id&chap_num=$chap_num" ,2) ;
     exit ;
  }    
    

//-----------------------------------------------------------------    

    if ($empty_fg) {
       echo "資料庫是空的，請先進入<期別管理>選項，建立新一期電子校刊內容！" ;   
       exit ;
    }        
    
//----------------------------------------------    
      
    $sqlstr =  "select p.title, p.tmode ,p.author ,p.class_name, c.book_num ,c.chap_name ,c.id from magazine_paper p,magazine_chap c
                where p.chap_num  = c.id and c.book_num = '$id'
                and p.tmode <=1  order by  p.classnum ,p.tmode  " ;   
    //選單
    $result = $CONN->Execute($sqlstr); 
    while ($row=$result->FetchRow()) {
   	 $d[chap_name]= "\"" . $row["chap_name"]  ."\"" ;
   	 $d[title]= "\"" . trim($row["title"])  ."\"" ;

   	 $d[classnum]= "\"" . $row["class_name"] ."\"" ;
   	 $d[author]= "\"" . $row["author"]  ."\"" ;
   	 
   	 reset($d) ;
         $data[]=implode(",",$d);
    }   
    $main=implode("\n",$data);
    

    $filename="author.csv";

	//以串流方式送出 ooo.csv
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo "類別,作品名稱,班級,姓名\n" . $main;
 ?>
