<?php

// $Id: signin.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();
$session_tea_sn =  $_SESSION['session_tea_sn'] ;


$Submit = $_POST['Submit'] ;
$class_num = $_POST['class_num']?$_POST['class_num']:$_GET['class_num'] ;
$id = $_GET["id"]?$_GET['id']:$_POST['id'] ;
$show_inp = $_POST["show_inp"] ;

if ($Submit=="報名") {

    $txt_stud = $_POST['txt_stud'] ;
    $class_num = $_POST['class_num'] ;
    $kid = $_POST['kid'] ;

    //取得已報名人數判斷
    $sqlstr = " select count(*) as cc from stud_team_sign where kid= '$kid' " ;
    $result =  $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;            
  
    $row = $result->FetchRow() ;
    $cc = $row["cc"] ;  
    
    if ($cc < ($_POST['stud_max'] +  $_POST['stud_ps'] ) ){ //尚可報名
       if ( $cc >= $_POST['stud_max'])  //為備取
          $bk_fg =1 ;         
    
        $stud_data = Get_stud_data ($class_num ,  $txt_stud ) ; //取得姓名及匯入資料   
        
        $stud_name = addslashes($stud_data[0]) ; 	
        $stud_id = $stud_data[1] ; 		
        if ($stud_name ) {
           $sqlstr = " insert into stud_team_sign 
                (sid  ,kid,class_id ,stud_name,stud_id ,bk_fg   ) 
                 values (0,'$kid','$class_num','$stud_name','$stud_id', '$bk_fg') " ;       
           //echo  $sqlstr ;     
           $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;       
        }
              
    }
}


      
//===================================================================



$class_base_p = class_base();

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;

if ( checkid($SCRIPT_FILENAME,1)){
   if (!isset($class_num) ) 
      $class_num = "601" ;	 

 //管理者可以做的事
    $class_num_temp .= "<form><p align=center>班級 :<select name=\"class_num\" onchange=\"this.form.submit()\">\n";
		foreach ($class_base_p as $key => $value) {
			if ($key == $class_num)
				$class_num_temp .= "<option value=\"$key\" selected>$value</option>\n";
			else
				$class_num_temp .= "<option value=\"$key\">$value</option>\n";							
		}
    $class_num_temp .= "</select></p>
                        <input type='hidden' name='id' value='$id'> 
                       </form>" ;
    $is_admin = true ; 
}
else {
	//取得教師所上年級、班級
	$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
	$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ; 

	$row = $result->FetchRow();
	$class_num = $row["class_num"];
	 
	if ($class_num <= 0)    {
	   Header("Location: index.php");
	   exit ;
	}	
	$class_num_temp = "<p align=center>" . $class_base_p[$class_num] ."</p>";
	$is_admin = false ; 
}

if ($_GET["do"]=="del") {
   if (($class_num == $_GET[class_num]) or $is_admin) {
   	$sqlstr = " delete from stud_team_sign where sid ='$_GET[sid]' " ;
   
        //echo  $sqlstr ;     
        $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;     
        //是否有備取人員，改為正取
           
   }	
        
}  

//=======================================================================

  head("班級報表單") ;
  echo '<link href="style.css" rel="stylesheet" type="text/css">' ;
  

	
  print_menu($school_menu_p);
  
  $main = Input($id) ;
  echo $class_num_temp ;
  echo $main ;
   foot() ;
   
   
//----------------------------------------------------------------------------

function Input($id) {
    global $CONN ,$PHP_SELF ,$class_base_p ,$class_num   ;
    
    $y = substr($class_num,0,1) ;

   //各才藝班已報名人數
   $sqlstr =" select kid , count(*) as cc   from stud_team_sign   group by kid order by kid DESC " ;
   $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
   while (  $row = $result->FetchRow() ) {
      $kid = $row["kid"] ;  
      $studs[$kid] =  $row["cc"] ;  
   }     
   
   //列出各才藝班資料 
    $sqlstr =" select *  from stud_team_kind   where mid = '$id' and ((year_set like '%$y%') or (year_set='0') ) " ;
	//echo $sqlstr ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $i = 1 ;
    while (  $row = $result->FetchRow() ) {
      $kid = $row["id"] ;
      $mid = $row["mid"] ;
      $class_kind = $row["class_kind"] ;
      $teach = $row["teach"] ;
      $stud_max = $row["stud_max"] ;
      $stud_ps = $row["stud_back"] ;
      $class_max = $row["class_max"] ;
      $week_set = $row["week_set"] ;
      $year_set = $row["year_set"] ;      
      $doc = $row["doc"] ;
      $cost = $row["cost"] ;
      if (!isset($studs[$kid]))
         $studs[$kid] =0 ;  
         
      //可否報名   
      if ($studs[$kid]>=($stud_max+$stud_ps)) {
      	 $form_input ="已額滿" ;
      }	elseif ($studs[$kid]>=$stud_max) {
      	$form_input = "<input name='txt_stud' type='text' id='txt_stud' size='8' maxlength='8'> 
        <input type='submit' name='Submit' value='備取報名'> 
        <input type='hidden' name='kid' value='$kid'>
        <input type='hidden' name='stud_max' value='$stud_max'>
        <input type='hidden' name='stud_ps' value='$stud_ps'>
        <input type='hidden' name='class_num' value='$class_num'>" ;
      }	else {
        $form_input = "<input name='txt_stud' type='text' id='txt_stud' size='8' maxlength='8'> 
        <input type='submit' name='Submit' value='報名'> 
        <input type='hidden' name='kid' value='$kid'>
        <input type='hidden' name='stud_max' value='$stud_max'>
        <input type='hidden' name='stud_ps' value='$stud_ps'>
        <input type='hidden' name='class_num' value='$class_num'>" ;
      }
   $main .= "      
<form name='form1' method='post' action=''>
  <h3 align='center'>班別：$class_kind </h3>
  <table width='95%' border='1' align='center' cellspacing='0'>
    <tr class='tr-t'> 
      <td width='8%'>師資</td>
      <td width='17%'>說明</td>
      <td width='10%'>人數上限(備取)</td>
      <td width='12%'>上課日別</td>
      <td width='12%'>已報名</td>
      <td width='26%'>報名座號或姓名:</td>
    </tr>
    <tr> 
      <td>$teach</td>
      <td>$doc</td>
      <td>$stud_max ($stud_ps)</td>
      <td>$week_set</td>
      <td>$studs[$kid]人<a href='view.php?kid=$kid&stud_max=$stud_max&stud_ps=$stud_ps&class_kind=$class_kind' target='new'>查看</a></td>
      <td>$form_input </td>
    </tr>
  </table>" ;
  

      
      
//---------------------------------------------------------
    $stud_list ="" ;
    //班上報名資料
    $sqlstr =" select *  from stud_team_sign where kid  = '$kid' and class_id ='$class_num'  " ;
	//echo $query ;
    $result2 = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row2 = $result2->FetchRow() ) {
        $sid = $row2["sid"] ;	
        $stud_name = $row2["stud_name"] ;
        $bk_fg  = $row2["bk_fg "] ;
        
        if ($bk_fg) {
           $stud_list .=    "$stud_name(備) <a href=$PHP_SELF?sid=$sid&do=del&id=$id&kid=$kid&class_num=$class_num>刪</a> , " ;  
        } else {
           $stud_list .=    "$stud_name<a href=$PHP_SELF?sid=$sid&do=del&id=$id&kid=$kid&class_num=$class_num><img src='images/button_drop.png' border =0 alt='刪除'></a> , " ;     
        }                  

    }
  
  
  $main .= " 
  &nbsp;&nbsp;&nbsp;&nbsp;班上已報名: $stud_list 
</form><hr> " ;      
  } //while

  return  $main ;
}
