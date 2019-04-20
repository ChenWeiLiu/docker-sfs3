<?php
//$Id: a_main.php 8952 2016-08-29 02:23:59Z infodaes $
    include_once( "config.php") ;
    
    // --認證 session 
    sfs_check();

//非管理者 
$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
if ( !checkid($SCRIPT_FILENAME,1)){
      Header("Location: index.php"); 
}               
 
 head("期別管理");
 print_menu($m_menu_p);
?>
 
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr><td valign=top bgcolor="#CCCCCC">

<table width="85%" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
    <td width="20%" > 

    </td>
    <td width="60%" > 
      <h1 align="center">電子校刊管理</h1>
    </td>
    <td width="20%"> 
      <p align="center"><a href="a_main_admin.php">新增一期</a></p>
    </td>
  </tr>
  <tr> </tr>
</table>
<table width="85%" border="1" cellspacing="0" cellpadding="4" align="center" bordercolorlight="#6666FF" bordercolordark="#FFFFFF" bgcolor="#99CCFF">
  <tr>
    <td width="27%">日期</td>
    <td width="41%">期別</td>
    <td width="32%">單元編修</td>
  </tr>
<?
    $sqlstr =  "select * from magazine  order by num DESC " ;   
    $result = $CONN->Execute($sqlstr) or die ($sqlstr); 
    if ($result) 
        while ( $row=$result->FetchRow() ) {
          $book_num = $row["num"] ; //取得期別    
          $id = $row["id"] ;
          $publish_date = $row["publish_date"] ;
          $is_fin = $row["is_fin"] ;
          echo "<tr><td>$publish_date</td>";
          echo "<td>\n";
		  echo "<a href=\"a_main_admin.php?id=$id\">第 $book_num 期修改</a>";
		  if (!$is_fin) {
		    echo "|<a href=\"a_main_admin.php?id=$id&del=1\">刪除</a>";
		  }
		  echo "|<a href=\"a_list_author2.php?id=$id\">列出作品學生名單</a>";
		  echo "</td>\n";
          if ($is_fin) echo "<td>本期已完成</td> ";
          else echo "<td><a href=\"a_pagemode.php?book_num=$id\">設定單元</a> | <a href='a_real_del.php'>清理垃圾埇</a></td>" ;
          echo "</tr>" ;
        }   
?>  

</table>
<br>
</td></tr></table>
<?php foot(); ?>
