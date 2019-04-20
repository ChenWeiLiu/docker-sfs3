<?php

// $Id: signList.php 8794 2016-01-26 02:25:07Z qfon $

include "config.php";

//sfs_check();
//增加欄位
$rs01=$CONN->Execute("select is_hide from sign_kind where 1");
if(!$rs01) $CONN->Execute(" ALTER TABLE sign_kind ADD is_hide TINYINT DEFAULT '0' NOT NULL  " );

   
  head("班級報名表") ;
  print_menu($menu_p);
?>

<table width="100%" border='1' cellspacing='0' cellpadding='4' bgcolor='#CCFFFF' bordercolor='#33CCFF'>
   
  <tr bgcolor="#66CCFF"> 
    <td >狀態</td>
    <td >報名表名稱</td>
    <td >說明</td>
    <td >管理</td>
  </tr>
<?php

  //頁數計算
  $showpage = ($_GET[showpage]) ? $_GET[showpage] : $_POST[showpage] ;
  $sqlstr = " SELECT * FROM sign_kind order by id DESC " ;
  
  $result = $CONN->Execute( $sqlstr) ;
  if ($result) {
    $totalnum = $result->RecordCount() ;
    $totalpage =ceil( $totalnum / $page_num) ;
    
    if (!$showpage)  $showpage = 1 ;  
  } 
  if (!$totalpage) $totalpage= 1 ;  
  if ($showpage > $totalpage) $showpage= $totalpage ;  
  
  
  //列出資料

    //$sqlstr =" select *  from sign_kind  order by id DESC LIMIT $b , 10   ";
    $result = $CONN->PageExecute($sqlstr, $page_num , $showpage );

    //$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
      $id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["doc"] ;	
      $title = $row[title] ;
	  
	  $admin = $row['admin'] ;
	
      echo "<tr>\n" ;
	  
	  //管理檢查
		if($admin=='' or $admin==$_SESSION['session_tea_sn']) $admin_link=" | &nbsp;<a href=\"signAdmin.php?id=$id&do=edit\"><img src=\"images/medit.gif\" border=\"0\" alt=\"修改報名單\" title=\"修改報名單\"></a>
		&nbsp; | &nbsp;
		<a href=\"javascript:if(confirm('確定要刪除?\\n已報名資料會一併刪除！'))location='signAdmin.php?id=$id&do=delete'\"><img src=\"images/delete.gif\" border=\"0\" alt=\"刪除\" title=\"刪除\"></a>"; else $admin_link='';
      
      //期限檢查    
      if (date("Y-m-d")>=$beg_date and date("Y-m-d")<=$end_date) 
         echo " <td nowrap><a href=\"signin.php?id=$id\"><img src=\"images/edit.gif\" border=\"0\" alt=\"填報中\">填報中</a></td>" ;
      else if (date("Y-m-d")<$beg_date) 
         echo " <td nowrap>尚未開始</a></td>" ;
      else 
         echo " <td nowrap><img src=\"images/stop.gif\" border=\"0\" alt=\"填報結束\">結束</td>" ; 
      echo "  
          <td >$title</td>
    <td >" . nl2br($doc) ."</td>
    <td nowrap>
      <div align=\"center\"  ><a href=\"signView.php?id=$id\"><img src=\"images/view.gif\" border=\"0\" alt=\"查看報名資料\" title=\"查看報名資料\"></a>
      &nbsp; $admin_link
      </div>
    </td>
  </tr>" ;
  }
?>  

</table>
<?

echo show_page_point($showpage, $totalpage) ;    
foot();
?>
