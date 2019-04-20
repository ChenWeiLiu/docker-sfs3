<?php

// $Id: citaList.php 8848 2016-03-09 03:45:57Z qfon $

include "config.php";
include "../../include/sfs_case_dataarray.php";
sfs_check();
//增加欄位
$rs01=$CONN->Execute("select is_hide from cita_kind where 1");
if(!$rs01) $CONN->Execute(" ALTER TABLE cita_kind ADD is_hide TINYINT DEFAULT '0' NOT NULL  " );

$t=teacher_array_all();

   
head("學校榮譽榜") ;
print_menu($menu_p);
?>

<table width="100%" border='1' cellspacing='0' cellpadding='4' bgcolor='#CCFFFF' bordercolor='#5555ff'>
  <tr bgcolor="#66CCFF" align="center"> 
    <td >日期</td>
   <td >狀態</td>
   <td >屬性</td>
    <td >榮譽榜名稱</td>  
    <td >管理</td>
	<td >發佈者(sn)</td>
  </tr>
<?php
// 計算有多少則公告
$user_t1 = "cita_kind";
$page_unit = "20";// 每頁呈現多少筆資料
$page = $_REQUEST['page'];

if ($page==""){
  $page=1;
}
$page_num=($page-1)*$page_unit;

$sql_1 = "SELECT count(*) FROM ".$user_t1;
$sql_r1 = mysql_query($sql_1);
$board_num = mysql_fetch_array($sql_r1);
$page_total=ceil($board_num[0]/$page_unit);// 將公告數除以每頁呈現筆數後無條件進位

$web_page_list = "";
for ($j=1;$j<=$page_total;$j++){
  if ($j==$page){
    $web_page_list.= "[<a href='".$PHP_SELF."?page=".$j."'>";
    $web_page_list.= "<FONT SIZE='4' COLOR='#FF0000'>".$j."</FONT></a>] ";
  }else{
    $web_page_list.= "[<a href='".$PHP_SELF."?page=".$j."'>";
    $web_page_list.= "<FONT SIZE='2' COLOR='#00CCFF'>".$j."</FONT></a>] ";
  }
}

    $sqlstr ="SELECT * FROM ".$user_t1." order by beg_date DESC limit ".$page_num.", ".$page_unit.";";
    $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
      $id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["doc"] ;	
      $helper = $row[helper] ;
	  $external = $row['external']?'校外':'校內';
	  $bgcolor = $row['external']?'#ffcccc':'#ccffcc';
	  $teach_id = $row[teach_id] ;
	  
      echo "<tr align='center' bgcolor='$bgcolor'><td>$beg_date</td>" ;
      
      //期限檢查    
      if (date("Y-m-d")>=$beg_date and date("Y-m-d")<=$end_date){ 
         echo " <td ><a href=\"citain.php?id=$id\"><img src=\"images/edit.gif\" border=\"0\" alt=\"填報中\">填報中</a></td>" ;
		$do="處理列印";
      }else {
        echo " <td ><img src=\"images/stop.gif\" border=\"0\" alt=\"填報結束\">填報結束</td>" ; 
		$do="查看名單";
		}
      echo "<td>$external</td>       
		<td  align='left'>" . nl2br($doc) ."</td> 

		<td  align='center'>
		  <a href='citaView.php?id=$id'>$do</a>&nbsp;&nbsp; | &nbsp;&nbsp;<a href='csvout.php?id=$id'>CSV輸出</a>&nbsp;&nbsp; | &nbsp;&nbsp;<a href='citaAdmin.php?id=$id&do=edit'>修改設定</a>&nbsp;&nbsp; | &nbsp;&nbsp;<a href='citaAdmin.php?id=$id&do=del'>刪除</a>
		</td>
		<td align=right>$t[$teach_id] ($teach_id)</td>
	  </tr>" ;
  }
  echo "<tr bgcolor='#ffffcc' align='center'>";
  echo "<td colspan=5>".$web_page_list."</td>";
  echo "</tr>";
?>  

</table>
<?php
foot();
?>
