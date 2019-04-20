<?php

// $Id: signList.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

//sfs_check();

  head($MODULE_PRO_KIND_NAME) ;
  print_menu($school_menu_p);
?>

<table width="100%" border='1' cellspacing='0' cellpadding='4' bgcolor='#CCFFFF' bordercolor='#33CCFF'>
  <tr bgcolor="#66CCFF"> 
    <td >狀態</td>
    <td >報名期別</td>
    <td >說明</td>
    <td >管理</td>
  </tr>
<?php

  //頁數計算
  $showpage = ($_GET[showpage]) ? $_GET[showpage] : $_POST[showpage] ;
  $sqlstr = " SELECT * FROM stud_team_kind where mid = '0' order by id DESC " ;
  
  $result = $CONN->Execute( $sqlstr) ;
  if ($result) {
    $totalnum = $result->RecordCount() ;
    $totalpage =ceil( $totalnum / $page_num) ;
    
    if (!$showpage)  $showpage = 1 ;  
  } 
  if (!$totalpage) $totalpage= 1 ;  
  if ($showpage > $totalpage) $showpage= $totalpage ;  
  
  
  //列出資料


    $result = $CONN->PageExecute($sqlstr, $page_num , $showpage );

    //$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
      $id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["doc"] ;	
      $title = $row[class_kind] ;

      echo "<tr>\n" ;
      
      //期限檢查    
      if (date("Y-m-d H:i:s")>=$beg_date and date("Y-m-d H:i:s")<=$end_date) 
         echo " <td nowrap><a href=\"signin.php?id=$id\"><img src=\"images/edit.gif\" border=\"0\" alt=\"填報中\">填報中</a></td>" ;
      else 
         echo " <td nowrap><img src=\"images/stop.gif\" border=\"0\" alt=\"填報結束\">結束</td>" ; 
      echo "  
          <td >$title <a href='view_all.php?id=$id'>(報名名冊)</a></td>
    <td >" . nl2br($doc) ."</td>
    <td nowrap>
      <div align=\"center\"  ><a href=\"signView.php?id=$id\"><img src=\"images/view.gif\" border=\"0\" alt=\"查看報名資料\"></a>&nbsp; | &nbsp;<a href=\"signAdmin.php?id=$id&do=edit\"><img src=\"images/medit.gif\" border=\"0\" alt=\"修改報名單\"></a></div>
    </td>
  </tr>" ;
  }
?>  

</table>
<?

echo show_page_point($showpage, $totalpage) ;    
foot();
?>
