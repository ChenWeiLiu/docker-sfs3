<?php
//$Id: paper_list.php 8952 2016-08-29 02:23:59Z infodaes $
  include_once( "config.php" );
  //session_start();
  //session_register("session_log_id");

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

    $sqlstr =  "select * from magazine  order by num DESC " ;   
    //目前最近一期
    $result = $CONN->Execute( $sqlstr) ;
    if ( $result ) {
              $row=$result->FetchRow()  ;
              $book_num = $row["num"] ; //取得期別    
              $id = $row["id"] ;
              $publish_date = $row["publish_date"] ;
              $is_fin = $row["is_fin"] ;    
              $bdate = $row["ed_begin"] ;
              $edate = $row["ed_end"] ;
              $editors =  $row["admin"] ;         //編輯群

              if (date("Y-m-d")<$bdate or date("Y-m-d")>$edate) $is_timeout = 1 ;

    }

    if (check_is_man2($editors)) {
       //擁用審稿權 
       $is_editor = 1 ;	
    }  
    
    head("上傳作品");
?>
<style type="text/css">
<!--
.td_s {  background-color: #FFCC99; text-align: center}
.tr_m {  background-color: #CCCCCC; text-align: center}
-->
</style>
</head>
<body bgcolor="#FFFFFF">

<form method="post" action="">
<?php print_menu($m_menu_p); ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr><td valign=top bgcolor="#CCCCCC"  align="center">
        <h3 >第<?php echo $book_num ?>期電子校刊</h3>
        學生上傳期限:<?php echo "$bdate 至 $edate" ; ?>

<?php
    if (!$id) {
       echo "資料庫是空的，請先進入<期別管理>選項，建立新一期電子校刊內容！" ;   
       exit ;
    }      
    
	$id=intval($id);
    if ($is_editor) 
       $sqlstr =  "select * from magazine_chap where book_num=$id  and cmode<=5  order by chap_sort " ;   
    else 
       $sqlstr =  "select * from magazine_chap where book_num=$id  and cmode<=5  and stud_upload = 1 order by chap_sort " ;   
       
    //選單、各章節要上傳
    $result = $CONN->Execute($sqlstr);
    if ($result) {
       while ($row=$result->FetchRow()) {
       	      $tname = $row["chap_name"] ;
              $tid = $row["id"] ;
              if (!$chap_num) $chap_num=$tid ;
              
              $chap_array[$tid] = $tname ;
              
       }
       //選單 ;
       $tchap_name = print_chap_item($book_num, $chap_num , $chap_array ) ;
    }       

  echo "<p>$tchap_name" ;
  if (!$is_fin) {
     if (!$is_timeout)    
        echo "<a href='upload.php?book_num=$book_num&chap_num=$chap_num'>學生第一次上傳</a> &nbsp;|&nbsp; " ;
     echo "<a href='ed_upload.php?book_num=$book_num&chap_num=$chap_num'>編輯群上傳</a>" ;
  }

  ?>
  </p>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" bgcolor="#FFCC99" bordercolorlight="#333333" bordercolordark="#FFFFFF" align="center">
    <tr> 
      <td width="14%">班級</td>
      <td width="42%">標題(查看)</td>
      <td width="16%">作者</td>
      <td width="13%">編修</td>
      <td width="15%" bgcolor="#CCCCFF"> 編輯群 </td>
    </tr>
    <?php
    
    //此章節已上傳的文章
    $sqlstr =  "select * from magazine_paper where chap_num=$chap_num order by classnum" ;   
 
    $result = $CONN->Execute($sqlstr);
    if ($result) 
       while ($row=$result->FetchRow()) {
          $paper_id = $row["id"] ;     
          $title = $row["title"] ;   
          $author = $row["author"] ; 

          $classnum = $row["class_name"] ;
          $isDel = $row["isDel"] ; 
          $editor = $row["editId"] ; 

          echo "<tr>" ;
          echo "<td>$classnum </td>" ;
          echo "<td>" ;
          if ($isDel) echo "<img src='images/trash.gif' border='0' alt='要刪除' >" ;
          echo "<a href=\"showpaper.php?paper_id=$paper_id\">$title </a></td>" ;
          echo "<td>$author</td>" ; 
          
          if ($is_fin or $is_timeout)
              echo "<td>非上傳期間</td>" ;
          else 
              echo "<td><a href=\"upload.php?book_num=$book_num&chap_num=$chap_num&paper_id=$paper_id\">重新上傳</a></td>" ;   
          
          if ($is_fin)
            echo "<td bgcolor=\"#CCCCFF\">本期已完成</td>" ;
          else {    
            echo "<td bgcolor=\"#CCCCFF\"><a href=\"editor.php?paper_id=$paper_id\">審稿</a> " ; 
            if ($editor) echo "<img src='images/ok.gif' border='0' alt='已審稿($editor)' title='已審稿($editor)' />" ;
            echo "</td> " ;
          }     
          echo "</tr>\n" ;
       }
?> 
  </table><br>
</td></tr></table>
</form>
<?php foot(); ?>
