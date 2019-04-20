<?php
//$Id: editor.php 8952 2016-08-29 02:23:59Z infodaes $
  include_once( "config.php") ;
  include "../../include/sfs_case_PLlib.php" ;
  

  
// --認證 session 
sfs_check();
  
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

  if (!$paper_id)  header("location:paper_list.php") ;

  $class_year_p = class_base($curr_year_seme); //班級
  
  $savepath = $basepath .$book_path . "/" .$chap_path . "/" ;       
//-----------------------------------------------------------------      
  if ($Submit=="刪除這篇稿件") {  
     //刪除圖檔
     if ($old_pic_name) {
        @unlink ($savepath . $old_pic_name) ;
        @unlink($savepath. "___" . $old_pic_name);
     }    
     
     $sqlstr =  "delete  from  magazine_paper " ;
     $sqlstr .=  " where id = $paper_id  " ;      
     
     $CONN->Execute($sqlstr);    
     header("location:paper_list.php?book_num=$book_num&chap_num=$chap_num") ;         
  }    
//-----------------------------------------------------------------      
  if ($Submit=="確定更改") {
     
    //取得章節中的類別
    $sqlstr =  "select * from magazine_chap  where  id = '$tchap_num' " ;   
    $result = $CONN->Execute($sqlstr);
    while ($row=$result->FetchRow()) {
        $cmode  = $row["cmode"] ;
    }       
      
    $nday = date("mdhi-") ;
    
     //圖檔
     if (is_uploaded_file($_FILES['pic_file']['tmp_name'])) {
        //上傳檔案
        if (!eregi("(.jpg|.jpeg|.png|.gif)$",  $_FILES['pic_file']['name']) ) {
          echo "必須上傳圖檔，只支援 .jpg .gif .png 格式" ;
          echo "<a href=\"javascript:history.go(-1)\" > 回上頁 </a> " ;
          exit ;
        }      
        
        if ($old_pic_name){   //有舊圖
            unlink($savepath . $old_pic_name); 
            @unlink($savepath. "___" . $old_pic_name);
        }
                
        $pic_fn =   $nday . $_FILES['pic_file']['name'] ;
        move_uploaded_file($_FILES['pic_file']['tmp_name'], $savepath . $pic_fn);
        dosmalljpg($savepath , $pic_fn) ;        
            
     }
      
     else {
      if ($old_pic_name) $pic_fn = $old_pic_name ;  //保留舊圖檔
     }    
     
     if ($chk_del_pic) {
        //刪除圖檔
        unlink ($savepath . $old_pic_name) ;
        @unlink($savepath. "___" . $old_pic_name);
        $pic_fn = "" ;
     }


     $sqlstr =  "update magazine_paper 
                 set title='$txt_title ',author='$txt_author ',type_name='$txt_type',
                 teacher ='$txt_teacher ' ,parent ='$txt_parent ', chap_num='$tchap_num' , tmode='$cmode' ,
                 classnum ='$classnum', class_name ='$class_year_p[$classnum]' , pic_name ='$pic_fn' ,doc = '$txt_doc' ,judge='$txt_judge',
                 isDel='$chkDel' , editId = '$_SESSION[session_tea_name]' , editDate = now()  " ;
     
     $sqlstr .=  " where id = $paper_id  " ; 
     
         
     //$sqlstr = stripslashes($sqlstr); 

     
     $CONN->Execute($sqlstr);    
     header("location:paper_list.php?book_num=$book_num&chap_num=$chap_num") ;    
  }    
  
//-----------------------------------------------------------------    
  $sqlstr =  "select p.* , c.chap_name , c.chap_path ,c.book_num, p.pwd ,
              m.book_path , m.num ,m.admin ,m.is_fin 
              from magazine_paper p, magazine_chap c ,magazine m  
              where  p.id =$paper_id and p.chap_num= c.id   and c.book_num= m.id " ;   
  $result = $CONN->Execute($sqlstr);
  while ($row=$result->FetchRow()) {
    $chap_name = $row["chap_name"] ;
    $chap_path = $row["chap_path"] ;
    $book_path = $row["book_path"] ;
    $num = $row["num"] ;
    $book_num = $row["book_num"] ;
    $is_fin = $row["is_fin"] ;
    
    $editors =  $row["admin"] ;         //編輯群
    $chap_num = $row["chap_num"] ;
    $cmode = $row["tmode"] ;
    $title = $row["title"] ;
    $author = $row["author"] ;
    $type_name = $row["type_name"] ;
    $teacher = $row["teacher"] ;
    $parent = $row["parent"] ;
    $doc = $row["doc"] ;        
    $judge = $row["judge"] ;
    $classnum = $row["classnum"] ;       
    $pic_name = $row["pic_name"] ;
    $pwd = $row["pwd"] ;
    $chkDel = $row["isDel"] ;  
  }

  if (!check_is_man2($editors)) {
     echo "你非本期編輯群成員，無權執行此功能！" ;
     redir("paper_list.php?book_num=$book_num&chap_num=$chap_num" ,2) ;
     exit ;
  }  
    
  $sqlstr =  "select * from magazine_chap  where  book_num = '$book_num'  order by chap_sort " ;   
  $result = $CONN->Execute($sqlstr);
  while ($row=$result->FetchRow()) {
    $nid = $row["id"] ;
    $nchap= $row["chap_name"] ;
    $chapt_arr[$nid] = $nchap ;
  }    
     
  head() ;
?>
<html>
<head>
<title>稿件上傳</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>

<body bgcolor="#FFFFFF">
<form method="post" action="" enctype="multipart/form-data">
  <div align="center"> 
    <p><font size="5"><?php echo "第 $num 期 $chap_name 審稿" ?> </font>
<font color ='red'>審稿完成後要按下確定更改鍵，才會出已審稿圖示！</font>
    <table width="90%" border="1" cellspacing="0" cellpadding="4" bgcolor="#FFCC99" bordercolorlight="#333333" bordercolordark="#FFFFFF">
      <tr> 
        <td width="14%">修改類別：</td>
        <td width="86%"> 
        <select name="tchap_num">
<?php         
	foreach ($chapt_arr as $key => $value) {
          if ($key == $chap_num)	  
             echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$key,$value);
           else
             echo  sprintf ("<option value=\"%s\">%s</option>\n",$key,$value);
	}  
?>      
        </select> 
        </td>
      </tr>    
      <tr> 
        <td width="14%">標題：</td>
        <td width="86%"> 
          <input type="text" name="txt_title" size="40" value="<?php  echo $title ?>">
        </td>
      </tr>
      <tr> 
        <td width="14%">班級：</td>
        <td width="86%"> 
          <select name="classnum">
	<?php
	reset($class_year_p);
	 while(list($tkey,$tvalue)= each($class_year_p))
	 {
          if ($tkey == $classnum)	  
             echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
           else
             echo  sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
          }             	 
 	?>          
          </select>
           </td>
      </tr>
      <tr> 
        <td width="14%">作者：</td>
        <td width="86%"> 
          <input type="text" name="txt_author" value="<?echo $author ?>">
        </td>
      </tr>
      
      <?

      if ($cmode<2) {    //班級訊息
      ?>        
      <tr> 
        <td width="14%">指導教師：</td>
        <td width="86%"> 
          <input type="text" name="txt_teacher" value="<?echo $teacher ?>">
        </td>
      </tr>
      <tr> 
        <td width="14%">家長：</td>
        <td width="86%"> 
          <input type="text" name="txt_parent" value="<?php echo $parent ?>">
        </td>
      </tr>
      <tr> 
        <td width="14%">密碼：</td>
        <td width="86%"> 
          <?php echo $pwd ?>
        </td>
      </tr>
      <?php }    //班級訊息
      
      if ($cmode==1) {// 上傳圖型
      ?>        
      <tr> 
        <td width="14%">重新上傳：</td>
        <td width="86%"> 圖檔：<input type="file" name="pic_file" size="40">
            <br>
            <font color="#FF0066" size="2">(限JPG、PNG、GIF 格式檔)</font><br> 
    
          <textarea name="txt_doc" wrap="OFF" cols="60" rows="4"><?php echo $doc ?></textarea>
          <br>
          <?php if ( ($pic_name))   
               echo '<input type="checkbox" name="chk_del_pic" value="1"   > 刪除插圖檔：' .$pic_name ; 
            ?>
        </td>
      </tr>
      <?php 
      }
      else {
      ?>
      <tr> 
        <td width="14%">文章：</td>
        <td width="86%"> 
          <textarea name="txt_doc"  cols="74" rows="20"><?php echo $doc ?></textarea>
        </td>
      </tr>      
      <?
      }
      ?>
    </table>
    <p> 
      <input type="checkbox" name="chkDel" value="1" <?php if ($chkDel) echo "checked" ?> >本篇刪除
      <input type="submit" name="Submit" value="確定更改">
      <input type="reset" name="Submit2" value="重設">
      <input type="hidden" name="book_path" value="<?php echo $book_path ?>">
      <input type="hidden" name="chap_path" value="<?php echo $chap_path ?>">
      <input type="hidden" name="paper_id" value="<?php echo $paper_id ?>">
      <input type="hidden" name="old_pic_name" value="<?php echo $pic_name ?>">
      <input type="hidden" name="chap_num" value="<?php echo $chap_num ?>">
      <input type="hidden" name="book_num" value="<?php echo $book_num ?>">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>
      <font color ='red'>審稿完成後要按下確定更改鍵，才會出已審稿圖示！</font>
  </div>
  
</form>
<?php foot(); ?>