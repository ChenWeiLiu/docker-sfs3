<?php

// $Id: signList.php 8809 2016-02-05 16:45:43Z qfon $

include "config.php";

//sfs_check();
  //登入認証
  
  //session_start(); 
  //session_register("schoolname"); 
  $schoolname = $_SESSION['schoolname'] ;
  
   
  head("校際報名表") ;
  print_menu($menu_p);
  

  $now_day = date("Y-m-d") ; 
  $_GET[id]=intval($_GET[id]);
  if ($_GET[id]) 
     $sqlstr = " select * from  sign_act_kind where id = '$_GET[id]' " ;
  else    
     $sqlstr = " select * from  sign_act_kind where beg_date <= '$now_day' and end_date>='$now_day' order by  id DESC " ;
  $result = $CONN->Execute($sqlstr) ; 

  while ($row = $result->FetchRow() ) {
      $id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["act_doc"] ;	
      $title = $row["act_name"] ;
      $act_passwd = $row["act_passwd"] ;
      $team_set = $row["team_set"] ;
      $max_team = $row["max_team"] ;
      $max_each = $row["max_each"] ;
      $member_set = $row["member_set"] ;
      $fields_set = $row["fields_set"] ;  
      $manager = $row["manager"] ;  
      $school_list = $row["school_list"] ;  
      
      $main .=  "<tr><td><a href='signList.php?id=$id'>$title</a></td><td><a href='signView_open.php?id=$id'>查看報名狀況</a></td></tr>\n" ;
      $num ++ ;
      
  }
  switch ($num) {
    case 0:
      echo "目前無報名項目" ;
      break ;
    case 1:
      echo  disp_Show($id)   ;
      break;
    default:
      if ($_GET[id]) {
         if (( $now_day >= $beg_date) and ($now_day <= $end_date))  {
            echo  disp_Show($id)   ;
         } else 
           echo "目前無報名項目" ;        
      }else {
         echo "<table border=1 width=80% align=center border=1 bordercolor='#33CCFF' bgcolor='#CCFFFF' >
           <tr bgcolor='#66CCFF'><td colspan=2>選擇可報名項目</td></tr>  \n $main </table>" ;
      }     
      
  }       



  foot();              
  
//=================================================================
function disp_Show($id) {
  global $title ,$doc ,$schoolname ,$manager ,$school_list;      
 
  //取得全縣校名
  $selstr = get_school_name($schoolname , $school_list) ;
  
  $main = "<form name='form1' method='post' action='signin.php'>
  <table cellSpacing=0 cellPadding=4 width='70%' align=center border=1 bordercolor='#CCFFFF' bgcolor='#99CCFF'>
    <tr> 
      <td colspan='2' align='center'>
        <h2>$title 報名</h2>
        <a href='signView_open.php?id=$id'>查看目前已報名資料</a>
      </td>
    </tr>
    <tr> 
      <td>校名：</td>
      <td> 
        <select name='school_name'>
          $selstr
        </select>
      </td>
    </tr>
    <tr> 
      <td>密碼 </td>
      <td> 
        <input type='password' name='dd_passwd' >
        <input type='hidden' name='pid' value='$id'>
      </td>
    </tr>
    <tr> 
      <td colspan='2'> 
        <div align='center'> 
          <input type='submit' name='Submit' value='登入'>
        </div>
      </td>
    </tr>
  </table>
  
  <table align=center width='40%' border='1' cellspacing=0   bgcolor='#CCCCCC' >
  <tr>
  <td>說明:</td>
  <td>第一次填報密碼，請查看公文中說明。<br>再次進入，則使用自己設定的密碼。<br>$doc</td>
  </tr>
  <tr>
  <td>負責人:</td>
  <td>$manager</td>
  </tr>  
  </table>
  </form>" ;
   
  
  return $main ;


}
?>
