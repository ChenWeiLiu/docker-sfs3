<?php
// $Id: signin.php 8764 2016-01-13 13:08:50Z qfon $

include "config.php";

  //登入認証
  //session_start(); 
  //session_register("School_passwd"); 
  //session_register("schoolname"); 
  
$passwd = $_SESSION['School_passwd'] ;
$school_name = $_SESSION['schoolname'] ;
$PHP_SELF = $_SERVER['PHP_SELF'] ;  
$did = $_GET['did'] ;  
$Submit = $_POST['Submit'] ;
$do = $_GET['do'] ;

//mysqli
$mysqliconn = get_mysqli_conn();	


if ($did > 0) {
  
  $pid = $_GET['id'] ;     
  $school_name = $_GET['school_name'] ;    
} 

if ($Submit == "登入") {  
   $dd_passwd = $_POST['dd_passwd'] ; 
   $school_name = $_POST['school_name'] ;     
   $pid = $_POST['pid'] ; 
   $_SESSION['School_passwd'] = $dd_passwd ; 
   $_SESSION['schoolname'] = $school_name ; 
   $passwd =  strval($dd_passwd) ; 
}
  
if ($Submit=="新增") {

   $txtname = $_POST['txtname'] ;
   $team_name = $_POST['team_name'] ;
   $data = $_POST['data'] ;
   $max_fields = $_POST['max_fields'] ;
   $id = $_POST['id'] ;
   $school_name = $_POST['school_name'] ;
   $op = $_POST['op'] ;
   $my_passwd = $_POST['my_passwd'] ;
   
   //個人資料     
   for ($i = 0 ; $i < count($txtname); $i++) {
        $group_row[$i] .= $txtname[$i] ;
        for ($f = 0 ; $f < $max_fields ; $f++) 
           $group_row[$i] .= "##" .  $data[$i][$f] ;
   }     
   $groupdata = implode("," , $group_row ) ;
   
/*   
   $sqlstr = " insert into sign_act_data  
            (did ,pid,school_name,team_id,set_passwd,data ) 
             values (0,'$id','$school_name', '$team_name','$op', '$groupdata' ) " ;
             

   $result =  $CONN->Execute($sqlstr) ;  
*/

   
//mysqli
$sqlstr = " insert into sign_act_data  
            (did ,pid,school_name,team_id,set_passwd,data ) 
             values (0,?,?,?,?,?) " ;
$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('sssss', check_mysqli_param($id),check_mysqli_param($school_name),check_mysqli_param($team_name),check_mysqli_param($op),check_mysqli_param($groupdata));
$stmt->execute();
$stmt->close();
///mysqli	
   
   
   
   
  
  //設定密碼
  if ($my_passwd) { 
  /*
      $sqlstr = " update sign_act_data  set set_passwd ='$my_passwd' where school_name ='$school_name' and pid ='$id' " ;
      $result =  $CONN->Execute($sqlstr) ; 
   */
//mysqli
$sqlstr = " update sign_act_data  set set_passwd =? where school_name =? and pid =? " ;
$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('sss', check_mysqli_param($my_passwd),check_mysqli_param($school_name),check_mysqli_param($id));
$stmt->execute();
$stmt->close();
///mysqli	
	  
      $_SESSION['School_passwd'] = $my_passwd ; 
      $passwd =  $my_passwd ; 
  }
  $pid = $id ;
}

if ($Submit=="修改") {
   $txtname = $_POST['txtname'] ;
   $team_name = $_POST['team_name'] ;
   $data = $_POST['data'] ;
   $max_fields = $_POST['max_fields'] ;
   $id = $_POST['id'] ;
   $school_name = $_POST['school_name'] ;
   $op = $_POST['op'] ;
   $my_passwd = $_POST['my_passwd'] ;
   $did = $_POST['did'] ;
      
   //個人資料     
   for ($i = 0 ; $i < count($txtname); $i++) {

        $group_row[$i] .= $txtname[$i] ;
        for ($f = 0 ; $f < $max_fields ; $f++) 
           $group_row[$i] .= "##" .  $data[$i][$f] ;

   }     
   $groupdata = implode("," , $group_row ) ;
    /*  
   $sqlstr = " update sign_act_data set team_id ='$team_name' ,data ='$groupdata',set_passwd ='$op'  where did = '$did' " ;
   $result = $CONN->Execute($sqlstr) ; 
    */
//mysqli
$sqlstr = " update sign_act_data set team_id =? ,data =?,set_passwd =?  where did = ? " ;
$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('ssss', check_mysqli_param($team_name),check_mysqli_param($groupdata),check_mysqli_param($op),check_mysqli_param($did));
$stmt->execute();
$stmt->close();
///mysqli	


   
   
  //設定密碼
  if ($my_passwd) { 
      /*
      $sqlstr = " update sign_act_data  set set_passwd ='$my_passwd' where school_name ='$school_name' and pid ='$id' " ;
      $result =  $CONN->Execute($sqlstr) ; 
      */
//mysqli
$sqlstr = " update sign_act_data  set set_passwd =? where school_name =? and pid =? " ;
$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('sss', check_mysqli_param($my_passwd),check_mysqli_param($school_name),check_mysqli_param($id));
$stmt->execute();
$stmt->close();
///mysqli	


	  
      $_SESSION['School_passwd'] = $my_passwd ; 
      $passwd =  $my_passwd ; 
  }  
  $do ="" ;
  $pid = $id ;
}

if ($do=="del") {
	/*
   $sqlstr = "delete  from sign_act_data  where did = $did " ;
   $result =  $CONN->Execute($sqlstr) ; 
    */
//mysqli
$sqlstr = "delete  from sign_act_data  where did = ? " ;
$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('s', $did);
$stmt->execute();
$stmt->close();
///mysqli	

   
}	
if ($do=="add") {
  $pid = $_GET['id'] ;     
  $school_name = $_GET['school_name'] ;  
}	





if ($pid)
  $main1 =  showdata($pid , $school_name) ;
  
if ($passwd === $def_passwd)  //預設密碼
         $must_passwd = TRUE ; 	//必需要有密碼	
else 
  $must_passwd = FALSE ;       


  $main2 = input_data($pid , $school_name ,$did , $do ) ;
    if  ($set_passwd) {  		//已有密碼
        $must_passwd = FALSE ; 	//必需要有密碼	
       if ($passwd !== $set_passwd)    	//密碼不正確
         Header('Location: index.php'); 
         
    } else { 
      if ($passwd === $def_passwd)  //預設密碼
         $must_passwd = TRUE ; 	//必需要有密碼	

//      else 
  //       Header('Location: index.php'); 	
    }  

  head("報名表") ;
  print_menu($menu_p);
  
  echo $main1 ;
  echo $main2 ;
   foot() ;
  //----------------------------------------------------------------------------


function showdata($pid , $school_name) {
  	
  global $CONN, $team_set_arr ,$member_set_arr ,$fields_set_arr ,$PHP_SELF , $max_team ,$have_team ,$def_passwd , $set_passwd ,$max_each;      
  //報名單總類 ===============================
  $pid=intval($pid);
  $sqlstr = " select * from  sign_act_kind where  id ='$pid' " ;

  $result =  $CONN->Execute($sqlstr) ; 
  $row = $result->FetchRow() ;
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
      $def_passwd = $row["act_passwd"] ;     
      

        
  //組別    
  $tmparr = split (",", $team_set) ;  
  for ($i= 1 ; $i <= count($tmparr) ; $i++) {
    if ($tmparr[$i-1]<>"") {
      	$ni = $i ;	  
  	$tmp_arr1 = split ("##",$tmparr[$i-1]) ;  //甲組名|男姓說明,乙組|女生  
  	$team_set_arr[] = $tmp_arr1 ;
    } 	  
  }

  //成員    
  $tmparr = split (",", $member_set) ;      //隊長*1,帶隊*2,隊長*1,隊員*4
  for ($i= 1 ; $i <= count($tmparr) ; $i++) {
    if ($tmparr[$i-1]<>"") {

      	$ni = $i ;	  
  	$tmp_arr1 = split ("\*",$tmparr[$i-1]) ;    
  	$member_set_arr[] = $tmp_arr1 ;

  	$group_man += $member_set_arr[$i-1][1] ;
    } 	  
  }  
  
  //欄位    
  $tmparr = split (",", $fields_set) ;      //身份証字號|10|預設值|說明,生日|6|預設值|說明
  for ($i= 1 ; $i <= count($tmparr) ; $i++) {
    if ($tmparr[$i-1]<>"") {
      	$ni = $i ;	  
  	$tmp_arr1 = split ("##",$tmparr[$i-1]) ;    
  	$fields_set_arr[] = $tmp_arr1 ;
    } 	  
  }    

  //該校的報名資料======================================================================
  $sqlstr = " select * from  sign_act_data where  pid ='$id'   and school_name ='$school_name' order by did " ;
  $result = $CONN->Execute($sqlstr);  
  

  $mi=0 ;
  while($row =$result->FetchRow()) {
      $did_arr[] = $row["did"] ;	
      $team_id[] = $row["team_id"] ;	
      $set_passwd = $row["set_passwd"] ;	
      $data = $row["data"] ;	

      //欄位    
      $tmparr = split (",", $data) ;      //(姓名|欄位1|欄位2,姓名|欄位1|欄位2)
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {
      	    $tmp_arr1 = @split ("##",$tmparr[$i-1]) ;    
      	    $member_data_arr[$mi][] = $tmp_arr1 ;
 
      }    
      $mi++ ;
      $have_team ++ ;
  }
  
  $add_link = "$PHP_SELF?id=$id&school_name=$school_name&do=add" ; 
  
  $main = "<h2 align='center'>$school_name 已報名組別資料</h2><a href='$add_link'>新增一組</a>
  <table width='100%' border='1' cellspacing='0' cellpadding='4' bgcolor='#CCFFFF' bordercolor='#33CCFF'>
  <tr bgcolor='#66CCFF'> 
     <td>處理</td><td>編號</td><td>組別</td>\n " ;
     //成員

     $main .="</tr>\n" ;
     $edit_img = "<image src = 'images/medit.gif' border='0' alt='修改'>修改" ;
     $del_img = "<image src = 'images/delete.gif' border='0'  alt='刪除'>刪除" ;
     //資料列表
     for ($r = 0 ; $r < count($team_id) ; $r++ ) {
         $link = "$PHP_SELF?id=$id&school_name=$school_name&did=$did_arr[$r]" ; 
         $main .= "<tr><td><a href='$link&do=edit'>$edit_img<a> | <a href='$link&do=del'>$del_img<a></td><td>$did_arr[$r]</td><td>$team_id[$r]</td>\n" ;
         $main .="</tr>\n" ;   
    }
/*     
     //成員
     for($i=0 ; $i < count($member_set_arr) ; $i ++) {
        for ($m = 0 ; $m < $member_set_arr[$i][1] ; $m ++) {
            $main .= "<td>" . $member_set_arr[$i][0] ."</td>\n" ; 
        }
     }   
     $main .="</tr>\n" ;
     $edit_img = "<image src = 'images/medit.gif' border='0' alt='修改'>修改" ;
     $del_img = "<image src = 'images/delete.gif' border='0'  alt='刪除'>刪除" ;
     //資料列表
     for ($r = 0 ; $r < count($team_id) ; $r++ ) {
         $link = "$PHP_SELF?id=$id&school_name=$school_name&did=$did_arr[$r]" ; 
         $main .= "<tr><td><a href='$link&do=edit'>$edit_img<a> | <a href='$link&do=del'>$del_img<a></td><td>$did_arr[$r]</td><td>$team_id[$r]</td>\n" ;
         for($i=0 ; $i < count($member_data_arr[$r]) ; $i ++) {

                $main .= "<td>" . $member_data_arr[$r][$i][0] ."</td>\n" ; 

        }    
        $main .="</tr>\n" ;   
    }
    $main .="</table>
*/  
    
    $main .="</table>     
  
    <p ><a href='sign_paper.php?pid=$pid' target='paper'>列印報名總表</a></p>

    <hr>\n" ;
    
    return $main ;
}    



//==========================================================================================
function input_data($pid , $school_name , $did = 0 , $do="") {
   global $CONN, $team_set_arr ,$member_set_arr ,$fields_set_arr ,$PHP_SELF , $max_team ,$have_team ,$must_passwd , $set_passwd ,$max_each;     
   
   
  //該校的報名資料======================================================================
  if ($did>0  and $do =="edit" ){
	  $did=intval($did);
      $sqlstr = " select * from  sign_act_data where  did ='$did' " ;

      $result = $CONN->Execute($sqlstr)  ;  
      while($row = $result->FetchRow()) {
          $did = $row["did"] ;	
          $pid = $row["pid"] ;
          $team_id = $row["team_id"] ;	
          $set_passwd = $row["set_passwd"] ;	
          $data = $row["data"] ;	
          
          $edit_fg = TRUE ;
          
          //欄位    
          $tmparr = split (",", $data) ;      //(姓名|欄位1|欄位2,姓名|欄位1|欄位2)
          for ($i= 1 ; $i <= count($tmparr) ; $i++) {
              	$tmp_arr1 = split ("##",$tmparr[$i-1]) ;    
              	$member_data_arr[] = $tmp_arr1 ;
      
          } 

      }   
  }
  
  $max_fields = count($fields_set_arr) ; //額外欄位數
  
  if ($team_id) {
     $title ="修改組別(輸入完後要按下'修改'鍵)" ;
     $button_name ="修改" ;
  }else{ 
     $title ="新增一組(輸入完後要按下'新增'鍵)" ;
     $button_name ="新增" ;
  }   
  
  
$main ="<script language='JavaScript'>

function chk_empty(item) {
   if (item.value=='') { return true; } 
}

function check() { 
   var errors='' ;
   
   if (chk_empty(document.myform.my_passwd))  {
      errors = '第一次一定要設定新密碼！' ; }

   if (errors) alert (errors) ;
   document.returnValue = (errors == '');
 
}

</script>" ;

if ($must_passwd)  
   $onSubmit = " onSubmit='check();return document.returnValue' " ;
 
if ($do=='edit' or $do =='add')   {
//先判斷是否有超過各組可報組數 
$sqlstr = " select team_id ,count(*) as cc  from  sign_act_data where  school_name ='$school_name' and pid ='$pid'  group by  team_id " ;
//echo $sqlstr ;


$result = $CONN->Execute($sqlstr)  ;  
while($row = $result->FetchRow()) {
   $team_id_have[] = $row["team_id"] ;	 
   $team_id_cc[$row["team_id"]] =$row["cc"] ;	 
   //echo $team_id_cc[$row["team_id"]] 
   
}          
$main .= "<form name='myform' method='post' action='$PHP_SELF'  $onSubmit >
  <h2 align='center'>$title</h2>
  <table width='98%' border='1' cellspacing='0' cellpadding='0' align='center' bgcolor='#99CCFF' bordercolor='#CCFFFF'>
    <tr> 

      <td width='84%'> 參加組別 :

       <select name='team_name'>\n" ;
       for ($i = 0 ; $i < count($team_set_arr) ; $i++ ) {
       	   if ($team_id == $team_set_arr[$i][0] ) 
       	     $main .= " <option value='" . $team_set_arr[$i][0] . "' selected>" .$team_set_arr[$i][0] ."</option>\n" ;
       	   else {
       	     $tt =  $team_set_arr[$i][0] ;
       	     //echo $tt ;
       	     if (($team_id_cc[$tt] < $max_each  )and  ( !in_array($team_set_arr[$i][0] ,$team_id_have)) ) 
                $main .= " <option value='" . $team_set_arr[$i][0] . "' >" .$team_set_arr[$i][0] ."</option>\n" ;       	    
       	     /*
       	     if ( !in_array($team_set_arr[$i][0] ,$team_id_have) ) 
                $main .= " <option value='" . $team_set_arr[$i][0] . "' >" .$team_set_arr[$i][0] ."</option>\n" ;
             */
           }     
       }
       
       $main .=" </select> 
        設定密碼:<input type='text' name='my_passwd' size ='10' >" ;
       if ($must_passwd) 
          $main .=" <font color='#FF0000'>(必須要設新密碼)</font>" ;
      $main .= "</td>
    </tr>
    <tr> 
      <td width='84%'> 
        <table width='100%' border='1' cellspacing='0' cellpadding='0' bgcolor='#CCFFFF' bordercolor='#33CCFF'>
          <tr bgcolor='#66CCFF'> 
            <td width='21%'>名稱</td>
            <td >姓名</td>" ;
             for ($f = 0 ; $f< count($fields_set_arr) ; $f++ ) 
                $main .="<td>" . $fields_set_arr[$f][0] ."</td>\n" ; //各項欄位
                       
          $main .="</tr>\n" ;
       $mj = 0 ;   
       for($m =0 ; $m < count($member_set_arr) ; $m++ ) 
          for($mi = 0 ; $mi < $member_set_arr[$m][1] ; $mi++) {
            if ($member_set_arr[$m][1]  > 1 )  //人數大於一
               $strNum = $mi+1 ;
            else 
               $strNum = "" ;      
            $main .="<tr><td>" . $member_set_arr[$m][0] ."$strNum</td>\n" ;
            $main .="<td> <input type='text' name='txtname[]' value='" . $member_data_arr[$mj][0] ."'> </td>\n" ; //姓名
            for ($f = 0 ; $f< count($fields_set_arr) ; $f++ ) 
                $main .="<td> <input type='text' name='data[$mj][$f]' value='" . $member_data_arr[$mj][$f+1] ."'> </td>\n" ; //各項欄位
            
            $main .="</tr>\n" ;
            $mj ++ ;
          }                

        $main .= "</table>

      </td>

    </tr>

    <tr> 

      <td width='84%'> 

        <div align='center'>

          <input type='submit' name='Submit' value='$button_name'>
          <input type='hidden' name='did' value='$did'>
          <input type='hidden' name='must_passwd' value='$must_passwd'>
          <input type='hidden' name='id' value='$pid'>
          <input type='hidden' name='op' value='$set_passwd'>
          <input type='hidden' name='max_fields' value='$max_fields'>
          <input type='hidden' name='school_name' value='$school_name'>

        </div>

      </td>

    </tr>

  </table>

</form>" ;
}
  if (($have_team >= $max_team)  and ($button_name == "新增"))
     $main = "" ;
     
  return $main ;
}
