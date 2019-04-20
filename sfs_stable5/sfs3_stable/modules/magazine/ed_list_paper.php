<?php
//$Id: ed_list_paper.php 8952 2016-08-29 02:23:59Z infodaes $
  include_once( "config.php") ;
  include_once( "../../include/sfs_case_PLlib.php") ;
  
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}   

  $class_year_p = class_base($curr_year_seme); //班級
  
    // --認證 session 
    sfs_check();

    //期別選定===================================================
    if ($book_id) 
      $sqlstr =  "select * from magazine  where id='$book_id'  " ;  
    else 
      $sqlstr =  "select * from magazine  order by num DESC " ;   
    //目前最近一期
    if ($result) {
        $result = $CONN->Execute($sqlstr); 
        if ($result) {
              $row=$row=$result->FetchRow() ;
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
    
  $savepath = $basepath .$book_path . "/" .$chap_path . "/" ;       


?>


<style type="text/css">
<!--
.td_s {  background-color: #FFCC99; text-align: center}
.tr_m {  background-color: #CCCCCC; text-align: center}
-->
</style>
</head>


<body bgcolor="#FFFFFF">
<?
//-----------------------------------------------------------------    
  head("文章列印") ;
  print_menu($m_menu_p);
  echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr><td valign=top bgcolor="#CCCCCC" align="center">  ' ;
    if ($empty_fg) {
       echo "資料庫是空的，請先進入<期別管理>選項，建立新一期電子校刊內容！" ;   
       exit ;
    }        
    
//----------------------------------------------    
    $sqlstr =  "select * from magazine_chap where book_num=$book_id  and ( cmode=0 or cmode=2) " ;   
    //類別選單
    $result = $CONN->Execute($sqlstr);
    if ($result) {
       while ($row=$result->FetchRow()) {
       	      $tname = $row["chap_name"] ;
              $tid = $row["id"] ;
              if (!$chap_num) $chap_num=$tid ;
              
              $chap_array[$tid] = $tname ;
              
       }
       $chap_menu =   print_chap_item($book_num, $chap_num , $chap_array ) ;
    }    
  
//-----------------------       

  if (!isset($curr_year)) $curr_year=1 ;
?>    
    
<form name="myform" method="post" action="<?php echo basename($PHP_SELF)?>">
  <p align="center">
  <?php echo $chap_menu ; ?>
   | 列印年級：
    <select name="curr_year" onchange="this.form.submit()" >
           
      <?php 
           foreach($class_year as $key => $val) { 
            if ($key <= '6'){	
              if ($curr_year == $key)  
                 echo  "<option value= $key selected>"  ; 
              else
                 echo "<option value= $key >" ;
              echo $val."級" ;
            }  
          }  
        ?>
    </select>
    <input type="hidden" name="book_id" value="<?php echo $book_id ?>">
   
  
</form>     
<?

    $sqlstr =  "select * from magazine_paper where chap_num =$chap_num and classnum  like '$curr_year%' order by classnum  " ;   
    //選單
    $result = $CONN->Execute($sqlstr); 
    while ($row=$result->FetchRow()) {
         $tmode = $row["tmode"] ;
         $title = $row["title"] ;
         $author = $row["author"] ;
         $type_name = $row["type_name"] ;
         $teacher = $row["teacher"] ;
         $parent = $row["parent"] ;
         $doc = $row["doc"] ;

         $classnum = $row["class_name"] ;
         $pic_name = $row["pic_name"] ;
         $chap_path = $row["chap_path"] ;
         $book_path = $row["book_path"] ;
         $doc =htmlspecialchars($doc) ;
         $doc = ereg_replace("\n","<br>",$doc) ;
         $doc = ereg_replace("[[:space:]]","&nbsp;",$doc);     	
       echo "<table width ='95%' border='1' cellspacing='0' cellpadding='4'>\n" ;

       echo "<tr><td  rowspan='3' width='72%' > $title </td><td>$classnum  $author</td></tr> \n" ;
       echo "<tr><td width='28%'>家長:$parent </td></tr>\n" ;
       echo "<tr><td width='28%'>指導老師:$teacher </td></tr>\n" ;
       echo "<tr><td colspan='2'>$doc</td></tr>\n" ;
       echo "</table><br>\n" ;
    }   
echo "</td></td></table>" ;
foot();
 ?>
