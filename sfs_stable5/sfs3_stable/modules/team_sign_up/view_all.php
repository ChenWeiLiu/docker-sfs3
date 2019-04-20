<?
//$Id: view_all.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

sfs_check();

$session_tea_sn =  $_SESSION['session_tea_sn'] ;

	//取得教師所上年級、班級
	$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
	$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ; 

	$row = $result->FetchRow();
	$class_num = $row["class_num"];


   //列出各才藝班資料 
    $sqlstr =" select *  from stud_team_kind   where mid = '$_GET[id]'  " ;
	//echo $sqlstr ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $i = 1 ;
    while (  $row = $result->FetchRow() ) {
      $kid = $row["id"] ;
      $mid = $row["mid"] ;
      $class_kind = $row["class_kind"] ;
      $stud_max = $row["stud_max"] ;
      $stud_ps = $row["stud_back"] ;
      $class_max = $row["class_max"] ;
      $week_set = $row["week_set"] ;
      $year_set = $row["year_set"] ;     
      $cost = $row["cost"] ;   
      $main .= Get_kind_stud_List($kid,$class_kind,$stud_max,$stud_ps,$cost)  ;
    }


  head("班級報表單") ;
  echo '<link href="style.css" rel="stylesheet" type="text/css">' ;
  

	
  print_menu($school_menu_p);  
  echo $main   ;


function Get_kind_stud_List($kid,$class_kind,$stud_max,$stud_ps,$cost) {      
    global $CONN ,$class_num  ;
    $class_base_p = class_base();
    $sqlstr = " select *  from stud_team_sign where kid ='$kid'  order by sid " ;

    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
    $i = 1 ;
    while (  $row = $result->FetchRow() ) {
        $class_id = $row["class_id"] ;	
        $class_name = $class_base_p[$class_id] ;	
        $stud_name = $row["stud_name"] ;
        $sign_time  = $row["sign_time"] ;    	
        if ($i>$stud_max)
           $bk= "備取" ;
        else 
           $bk= "正取" ;   
        
        if ($class_num == $class_id)     
           $main .= "<tr bgcolor='#FFCCFF'><td>$i</td><td>$class_name</td><td>$stud_name</td><td>$sign_time</td><td>$bk</td></tr>\n" ;
        else            
           $main .= "<tr ><td>$i</td><td>$class_name</td><td>$stud_name</td><td>$sign_time</td><td>$bk</td></tr>\n" ;
        $i++ ;  
    }
    $main = "<h2 align='center'>$class_kind 報名名單</hr><table border=1 cellspacing='0' cellpadding='4'><tr class='tr-t'><td>編號</td><td>班級</td><td>姓名</td><td>報名時間</td><td>是否正取</td></tr>\n
     $main
     </table><hr>\n" ;         
    return $main ; 
}    
?>