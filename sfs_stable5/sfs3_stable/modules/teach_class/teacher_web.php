<?php

// $Id: teacher_web.php 8952 2016-08-29 02:23:59Z infodaes $

/*
  教師網頁、email公佈
  prolin(prolin@sy3es.tnc.edu.tw) 92/5/23
*/


//系統設定檔
include_once "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

//選單類別再增加
$modestr = array("all"=>"全部人員","x"=>"行政人員","y"=>"有網頁者" ,"z"=>"科任",);
//處室選單
$post_office_p = room_kind();

$class_base_p = class_base();

$selmode=$_POST['selmode'];


//$debug = 1;

  if (!isset($selmode) ) $selmode = 'all' ;
  switch ($selmode) {
    case 'all':	//全部
      $wherestr = "   order by LPAD(b.teach_title_id,4,' '), b.class_num " ;
      break;
   case 'x':	//行政
      //$wherestr = " and title_name not like '%教師' order by b.post_office ,b.teach_title_id " ;
      $wherestr = " and (post_kind <6 or post_kind>11) order by LPAD(b.teach_title_id,4,' ')  " ;
      break;      
      
    case 'z':	//科任
      $wherestr = "  and title_name like '科任%'  order by b.teach_title_id " ;
      break;
    case 'y':	//有網頁者
      $wherestr = " and (length(selfweb)>0 or length(classweb) >0)  order by LPAD(b.teach_title_id,4,' ') " ;
      break;  

    default:	//一至六年級、幼稚園、資源班
      $wherestr = " and b.class_num LIKE '". intval($selmode) ."%' order by b.class_num " ;  
      break ;
  }    

  
              
  $sqlstr = " SELECT a.teacher_sn , a.name,e.* , b.post_kind, b.post_office, d.title_name ,b.class_num 
              FROM teacher_base a 
              left join teacher_connect e   on a.teacher_sn = e.teacher_sn 
              left join  teacher_post b on a.teacher_sn = b.teacher_sn 
              left join  teacher_title d on b.teach_title_id = d.teach_title_id
              where a.teach_condition = 0 " . $wherestr ;             
              
  if ($debug) echo $sqlstr ;
$result = $CONN->Execute($sqlstr) or die ($sqlstr);			

?>  
<html>
<head>
<title><?php echo $school_short_name ?>教職員帳號、網頁</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<style type="text/css">
<!--
.tr1 {  background-color: #FFFF; text-align: center}
.tr2 {  background-color: #faeaea; text-align: center}
.trtop {  font-weight: bold; background-color: #CCCCFF; text-align: center}

a:visited {  text-decoration: none}
a:active {  text-decoration: none}

a:hover {  background-color: #66FF66}
a:link {  text-decoration: none}
-->
</style>

</head>

<?php 

  head() ;
?>
<body bgcolor="#FFFFFF">
<form name="myform" method="post" >
   <table width="95%" border="0" cellspacing="0" cellpadding="4" align="center">
    <tr bgcolor="#CCCCFF"> 
      <td width="66%"><?php echo $school_short_name ?>教職員電子郵件帳號、網頁一覽表 </td>
      <td width="34%"> 顯示類別： 
        <select name="selmode" onchange="this.form.submit()">
          <?php
            foreach($modestr as $tkey=>$tvalue) {
	      if ($tkey == $selmode)
		echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
	      else
		echo sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
            }       
            $class_year = get_class_year_array();
            foreach($class_year as $tkey=>$tvalue) {       
	      if (strval($tkey) == $selmode)
		echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$school_kind_name[$tvalue]);
	      else
		echo sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$school_kind_name[$tvalue]);
            }   
          ?>
        </select>

    </td>
  </tr>
</table>

  <table width="95%" border="1" cellspacing="0" cellpadding="4" align="center" bordercolorlight="#6666FF" bordercolordark="#FFFFFF">
    <tr  class="trtop"> 
      <td  class="trtop">單位</td>
      <td  class="trtop">職務</td>
      <td  class="trtop">姓名</td>
      <td  class="trtop">電子郵件  </td>
      <td  class="trtop">個人網頁</td>
      <td  class="trtop">班級網頁</td>
    </tr>
    <?php
  //列印出個人資料

  $rowi = 0  ;
  while(!$result->EOF)
    {     
      $s_unit= $result->fields["post_office"] ;

      //隔行變色
      if ($rowi){
        $rowi = 0  ;	
        echo '<tr class="tr2">' ;
      }  
      else {
        $rowi = 1  ; 
        echo '<tr class="tr1">' ;
      } 
       
      //單位
      echo "<td nowap>";
      if (strpos($post_office_p[$s_unit],'科任'))
        echo "&nbsp" ;
      else
        if ($result->fields["class_num"] ) {//級任 
          $class_num  = $result->fields["class_num"] ;
          echo $class_base_p[$class_num] ;
        }  
        else   echo $post_office_p[$s_unit] ;
      echo "</td>" ;   
       
      //職務  
      echo "<td nowap>".$result->fields["title_name"]."</td>" ;
            
      //姓名      
      echo "<td nowrap>".$result->fields["name"]."</td>" ;
      
      //電子郵件
      echo "<td nowrap>" ;
      if ($result->fields["email"]) 
           echo '<img src="mailtopng.php?text='.$result->fields["email"].'" alt="email 圖檔顯示" />';
      else 
        echo "&nbsp" ;
      echo "</td>" ;  
            
      //個人網頁
      echo "<td nowrap>" ;
      if ($result->fields["selfweb"]) 
           echo "<a href=\"".$result->fields["selfweb"]."\"  target=\"_blank\"><img src=\"images/myhome.gif\" border=\"0\"></a> " ;
      else 
        echo "&nbsp" ;
      echo "</td>" ;        
      
      //班級網頁
      echo "<td nowrap> " ;
      if ($result->fields["classweb"]) 
        echo "<a href=\"".$result->fields["classweb"]."\"  target=\"_blank\"><img src=\"images/home.gif\" border=\"0\"></a> " ;
      else 
        echo "&nbsp" ;  
      echo "</td>" ;  
      
      echo "</tr> \n" ;
      
      $result->MoveNext();
     }
?>
  </table>
</form>

<?php foot() ; ?>
