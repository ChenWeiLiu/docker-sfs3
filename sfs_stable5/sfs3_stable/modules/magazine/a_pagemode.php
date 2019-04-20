<?php
//$Id: a_pagemode.php 8952 2016-08-29 02:23:59Z infodaes $
  include_once( "config.php") ;
  include_once( "../../include/sfs_case_PLlib.php") ;
  // --認證 session 
  sfs_check();
  
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
    
//非管理者 
$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
if ( !checkid($SCRIPT_FILENAME,1)){
      Header("Location: index.php"); 
}   
       
//-----------------------------------------------------------------          
  if ($_GET["do"]=="build") {
  	
    //按照上期單元設定
    //取得上一期期別
      $sqlstr =  "select id from magazine  where id < '$_GET[book_num]' order by num DESC " ;   
      $result = $CONN->Execute($sqlstr) or die ($sqlstr);
      if ($result) {
        $row=$result->FetchRow() ;
        $prev_id = $row["id"] ;
      }
    
      //取得上一期單元
      if ($prev_id) {
          $sqlstr =  "select * from magazine_chap  where  book_num = '$prev_id' order by chap_sort " ;   
          $result = $CONN->Execute($sqlstr)  or die ($sqlstr);
          $dirstr = "1" ;
          while ($row=$result->FetchRow() ) {

            $a_chap_name =  $row["chap_name"] ;
            $a_cmode = $row["cmode"] ;
            $a_chap_sort  = $row["chap_sort" ];
            $a_small_pic = $row["small_pic" ];
            $a_new_win = $row["new_win" ];
            $a_stud_upload = $row["stud_upload" ];
            
            //增加目錄
            chdir($basepath . $_GET[book_path] ."/" ) ; 	
            
            while (is_dir($dirstr)) {
           	  $dirstr ++ ;
            }	
            mkdir($dirstr , 0700) ; 
            
            $sqlstr_add =  "insert into magazine_chap (id ,book_num, chap_name ,cmode ,chap_sort ,chap_path , small_pic , new_win ,stud_upload)
                      values ( '0','$_GET[book_num]' , '$a_chap_name' ,'$a_cmode' , '$a_chap_sort','$dirstr' , '$a_small_pic' , '$a_new_win', '$a_stud_upload' ) " ;   
                      
            //$sqlstr_add = stripslashes($sqlstr_add);           
            //echo $sqlstr_add . "<br>" ;
            $result2 = $CONN->Execute($sqlstr_add) or die ($sqlstr_add);               
            $dirstr ++ ;
          }  
       }
    $do = "" ;
  }         
       
//-----------------------------------------------------------------  
  if ($_POST[Submit] == "確定新增") {
     
     //增加各項目的目錄
      chdir($basepath . $_POST[book_path] ."/" ) ; 	
      $dirstr = "1" ;
      while (is_dir($dirstr)) {
     	   $dirstr ++ ;
      }	
      mkdir($dirstr , 0700) ;          
      
 
      if (is_uploaded_file($_FILES['templ_file']['tmp_name'])) {
          //上傳檔案
          $save_path = $basepath . $_POST[book_path] . "/" .$dirstr . "/" ; 
          $fname =   $_FILES['templ_file']['name'] ;
          move_uploaded_file($_FILES['templ_file']['tmp_name'], $save_path . $fname);
          //dounzip( $save_path , $fname ) ; 
          p_unzip($save_path . $fname , $save_path) ;
          unlink($save_path . $fname);     
      }
      
      $sqlstr =  "insert into magazine_chap (id ,book_num, chap_name ,cmode ,chap_sort ,chap_path , small_pic , new_win ,stud_upload , include_mode)
                values ( '0','$_POST[book_num]' , '$_POST[txt_name]' ,'$_POST[sel_mode]' , '$_POST[txt_sort]','$dirstr' , '$_POST[chk_small]' , '$_POST[chk_new_win]', '$_POST[chk_stud_upload]' ,'$_POST[chk_include]' ) " ;   

      $CONN->Execute($sqlstr) ;       

  }
  
//-----------------------------------------------------------------  
  if ($_POST[Submit] == "確定修改") {
 
     
     if (is_uploaded_file($_FILES['templ_file']['tmp_name'])) {
          //上傳檔案
          $save_path = $basepath . $_POST[book_path] . "/" .$_POST[chap_path] . "/"  ; 
          $fname =   $_FILES['templ_file']['name'] ;
          move_uploaded_file($_FILES['templ_file']['tmp_name'], $save_path . $fname);
          p_unzip($save_path . $fname , $save_path) ;
          unlink($save_path . $fname);  
     }
           
     $sqlstr =  "update magazine_chap set chap_name= '$_POST[txt_name]' ,cmode ='$_POST[sel_mode]' , small_pic='$_POST[chk_small]' ,
                chap_sort = '$_POST[txt_sort]' ,new_win='$_POST[chk_new_win]' , stud_upload='$_POST[chk_stud_upload]' ,include_mode = '$_POST[chk_include]' 
                 where  id  = '$_POST[id]' " ;   
     $sqlstr = stripslashes($sqlstr);             
     $CONN->Execute($sqlstr) ;   

     $do = "" ;
  }  
  
//-----------------------------------------------------------------          
  if ($_GET["do"]=="del") {
    //刪除
    $updir = $basepath . $_GET[book_path] ."/" . $_GET[chap_path] ;
 
    do_rmdir($updir);     //刪除此章目錄
     
    $sqlstr =  "delete  from magazine_chap  where  id  = '$_GET[id]' " ;   
    $CONN->Execute($sqlstr) ;   
    $do = "" ;
  }  
  
//=======================================================================    
  $book_num = $_GET[book_num]? $_GET[book_num] :  $_POST[book_num] ;
  if (!$book_num) header("location:a_main.php") ;

  $sqlstr =  "select * from magazine  where  id='$book_num' " ;   

  $result = $CONN->Execute($sqlstr);
  while ( $row = $result->FetchRow()) {
  
    $book_path = $row["book_path"] ;    //目錄
    $is_fin = $row["is_fin"] ;
    $num = $row["num"] ;
  }
  if ($is_fin) header("location:a_main.php") ;

head("電子校刊期別管理");  
print_menu($m_menu_p);
?>  
<style type="text/css">
<!--
.tr_s {  background-color: #FFFF66}
-->
</style>
<form method="post" action="<?php echo $self_php ?>" enctype="multipart/form-data">
  <table width="95%" border="0" cellspacing="0" cellpadding="4" align="center">
    <tr> 
      <td width="73%"> 
        <h1 align="center">第<?php echo $num ?>期電子校刊類別</h1>
      </td>
      <td width="14%"> 
        <div align="center"><a href="a_main.php" >管理主畫面</a></div>
      </td>
    </tr>
  </table>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" align="center" bgcolor="#99CCCC" bordercolorlight="#333333" bordercolordark="#FFFFFF">
    <tr> 
      <td width="32%">標題</td>
      <td width="20%">類別</td>
      <td width="10%">順序</td>
      <td width="16%">編修</td>
      <td width="21%">目錄</td>
    </tr>
<?
  $sqlstr =  "select * from magazine_chap  where  book_num = '$book_num' order by chap_sort " ;   
  $result = $CONN->Execute($sqlstr) or die ($sqlstr);
  while ($row = $result->FetchRow()) {
    $nid = $row["id"] ;
    $chap_path = $row["chap_path"] ;
    if ($nid == $id and $do == "edit") echo '<tr><td><font color=red> ☆' . $row["chap_name"] ."</font></td>";
    else echo "<tr><td>" . $row["chap_name"] ."</td>";

    echo "<td>".$chap_mode[$row["cmode"]] ."</td>" ;
    echo "<td>".$row["chap_sort"] ."</td>" ;
    echo "<td> <a href=a_pagemode.php?book_num=$book_num&id=$nid&do=edit>修改</a> | <a href=a_pagemode.php?book_num=$book_num&id=$nid&do=del&chap_path=$chap_path&book_path=$book_path>刪除</a></td>" ;
    
    if ( $row["cmode"]==1)  //圖檔
    	echo "<td>$chap_path &nbsp;- <a href='a_resize.php?dopath=$book_path/$chap_path'>重製縮圖</a> </td>" ;
    else 
      echo "<td>$chap_path &nbsp; </td>\n" ;
    echo "</tr>\n" ;
  }  
  
  //尚未建立單元
  if (!isset($nid)) 
    echo "<tr><td colspan='5' >
      <div align='center'>
      <a href=\"$self_php?book_num=$book_num&do=build&book_path=$book_path\">依照上期單元設定</a>
      </div>
    </td></tr>\n" 
  	
?>    

  </table>
  <hr noshade align="center" width="75%">
<?
//編修=======================================================================
if ($_GET["do"] == "edit" ) {
    $sqlstr =  "select * from magazine_chap  where  id  = '$_GET[id]' " ;   
    $result = $CONN->Execute($sqlstr) or die ($sqlstr);      
    $row= $result->FetchRow()  ;
    $name = $row["chap_name"]  ;
    $cmode= $row["cmode"] ;
    $chap_sort = $row["chap_sort"]  ;
    $chap_path = $row["chap_path"] ;
    $small_pic = $row["small_pic"] ;
    $new_win = $row["new_win"] ;
    $stud_upload = $row["stud_upload"] ;
    $include_mode = $row["include_mode"] ;
    
    echo "<p align=\"center\">修改類別-$name 
    &nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"$self_php?book_num=$book_num\">新增類別</a></p> " ;

?>      
  
  <table width="95%" border="1" cellspacing="0" cellpadding="4" align="center" bgcolor="#99CCFF" bordercolorlight="#333333" bordercolordark="#FFFFFF">
    <tr> 
      <td width="25%">標題：</td>
      <td > 
        <input type="text" name="txt_name" value="<?php echo $name ?>">
      </td>
      <td>
      <input type="checkbox" name="chk_stud_upload" value="1" <?php if ($stud_upload) echo "checked" ?> >
        允許學生上傳</td>      
    </tr>
    <tr> 
      <td width="25%">類別：</td>
      <td width="14%"> 
        <select name="sel_mode">
          <?php
           $n = count($chap_mode) ;
           for ($i = 0 ; $i<$n;$i++)  {
             echo  "<option value=\"$i\" " ;
             if ($i==$cmode)echo " selected " ;
             echo " >" .$chap_mode[$i] ."</option>" ;
           }
        ?>
        </select>
      </td>
      <td width="61%"> 
        <input type="checkbox" name="chk_small" value="1" <?php if ($small_pic) echo "checked" ?> >
        縮圖預覽 <font size="2" color="#CC0000">(僅對圖檔類型有用)</font></td>
    </tr>
    <tr> 
      <td width="25%">排序：</td>
      <td width="14%"> 
        <input type="text" name="txt_sort" size="5" maxlength="3" value="<?php echo  $chap_sort ?>">
      </td>
      <td width="61%"> 
        <input type="checkbox" name="chk_new_win" value="1" <?php if ($new_win) echo "checked" ?> >
        在新視窗呈現</td>
    </tr>
    <tr> 
      <td width="25%">上傳網頁：<br>
        <font color="#CC0000" size="-1">(僅對網頁類別有作用)</font></td>
      <td colspan="2"> 
        <input type="file" name="templ_file"><input type="checkbox" name="chk_include" value="1" <?php if ($include_mode) echo "checked" ?>>直接載入網頁。
        <br>
        <font size="2" color="#CC0000">(主索引網頁需為index.htm，可上傳zip壓縮檔)</font> 
      </td>
    </tr>
    <tr> 
      <td colspan="3"> 
        <input type="submit" name="Submit" value="確定修改">
        <input type="reset" name="Submit3" value="重設">
        <input type="hidden" name="book_num" value="<?php echo $book_num ?>">
        <input type="hidden" name="id" value="<?php echo $_GET[id] ?>">
        <input type="hidden" name="book_path" value="<?php echo $book_path ?>">
        <input type="hidden" name="chap_path" value="<?php echo $chap_path ?>">
      </td>
    </tr>
  </table>
  <p>&nbsp;</p>
<?
}
else
//新增=======================================================================
{
?>      
  <p align="center">新增類別</p>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" align="center" bgcolor="#99CCFF" bordercolorlight="#333333" bordercolordark="#FFFFFF">
    <tr> 
      <td width="25%">標題：</td>
      <td > 
        <input type="text" name="txt_name">
      </td>
      <td>
      <input type="checkbox" name="chk_stud_upload" value="1"  >
        允許學生上傳</td>
        
    </tr>
    <tr> 
      <td width="25%">類別：</td>
      <td width="14%"> 
        <select name="sel_mode">
          <?php
           $n = count($chap_mode) ;
           for ($i = 0 ; $i<$n;$i++)  {
             echo  "<option value=\"$i\" >" .$chap_mode[$i] ."</option>" ;
           }
        ?>
        </select>
      </td>
      <td width="61%"> 
        <input type="checkbox" name="chk_small" value="1" checked >
        縮圖預覽 <font size="2" color="#CC0000">(僅對圖檔類型有用)</font></td>
    </tr>
    <tr> 
      <td width="25%">排序：</td>
      <td width="14%"> 
        <input type="text" name="txt_sort" size="5" maxlength="3">
      </td>
      <td width="61%"> 
        <input type="checkbox" name="chk_new_win" value="1" <?php if ($new_win) echo "checked" ?> >
        在新視窗呈現</td>
    </tr>
    <tr> 
      <td width="25%">上傳網頁：</td>
      <td colspan="2"> 
        <input type="file" name="templ_file"><input type="checkbox" name="chk_include" value="1" >直接載入網頁。
        <br>
        <font size="2" color="#660000">(<font color="#CC0000">主網頁需為index.htm，可上傳zip壓縮檔)</font></font> 
      </td>
    </tr>
    <tr> 
      <td colspan="3"> 
        <input type="submit" name="Submit" value="確定新增">
        <input type="reset" name="Submit3" value="重設">
        <input type="hidden" name="book_num" value="<?php echo $book_num ?>">
        <input type="hidden" name="book_path" value="<?php echo $book_path ?>">
      </td>
    </tr>
  </table>
  </form>
<table width="80%" border="1" align="center" cellspacing="0" bgcolor="#eeeeee">
  <tr>
    <td width="19%">網頁模式</td>
    <td width="81%"><p>要以index.htm為主索引，可以多檔案，以內含視窗方式做呈現。如果僅以單一文字檔，可以勾選直接載入網頁，則改以表格內容呈現。</p>
      <p>在此畫面中做檔案上傳。</p></td>
  </tr>
  <tr>
    <td>允許學生上傳</td>
    <td><p>可能為作文或班級點滴等。</p>
      <p>未指定，則為編輯教師才能上傳，例如上傳做好的網頁、美勞作品攝影圖等。</p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>  
<?
}  
foot();
?>  



</body>
</html>
