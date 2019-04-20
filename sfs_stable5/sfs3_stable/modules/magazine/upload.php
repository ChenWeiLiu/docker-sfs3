<?php
//$Id: upload.php 8817 2016-02-15 16:31:45Z qfon $
  include_once( "config.php") ;
  include_once( "../../include/PLlib.php") ;
  
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}



//mysqli	
$mysqliconn = get_mysqli_conn();

function check_mysqli_param($param){
	if (!isset($param))$param="";
	return $param;
}


  if (!$chap_num)header("location:paper_list.php") ;
  
  
  $nday = date("mdhi-") ;
  $savepath = $basepath .$book_path . "/" .$chap_path . "/" ;
  //session_start();
  //session_register("magazine_upload_pwd");     
      
  $class_year_p = class_base($curr_year_seme); //班級


//密碼輸入
  if (( $_POST[txt_up_pwd]) or ($_POST[pwd_Submit] == "送出" )) {
    $magazine_upload_pwd  = $_POST[txt_up_pwd] ;  
  }    
  
//-----------------------------------------------------------------  
  if ($Submit=="確定上傳") {

     //有圖檔
     if (is_uploaded_file($_FILES['pic_file']['tmp_name'])) {
        //上傳檔案
        if (!eregi("(.jpg|.jpeg|.png|.gif|.swf)$",  $_FILES['pic_file']['name']) ) {
          echo "必須上傳圖檔，只支援 .jpg .gif .png .swf 格式" ;
          echo "<a href=\"javascript:history.go(-1)\" > 回上頁 </a> " ;
          exit ;
        }           
        $pic_fn =   $nday . $_FILES['pic_file']['name'] ;
        move_uploaded_file($_FILES['pic_file']['tmp_name'], $savepath . $pic_fn);
        dosmalljpg($savepath ,  $pic_fn) ;
     }
     /*
     $sqlstr =  "insert into  magazine_paper (id,chap_num,tmode,title,author,type_name,
                 teacher,parent,doc,classnum,class_name , pwd,pic_name) 
                 values ('0','$chap_num','$cmode','$txt_title' , '$txt_author', '$txt_type' ,
                 '$txt_teacher', '$txt_parent' ,'$txt_doc' ,'$classnum' , '$class_year_p[$classnum]' ,'$txt_pwd' ,'$pic_fn') " ;   
     //$sqlstr = stripslashes($sqlstr);             
     $CONN->Execute($sqlstr) ;   
	 */
	 

//mysqli	
$chap_num=intval($chap_num);
$sqlstr =  "insert into  magazine_paper (id,chap_num,tmode,title,author,type_name,
            teacher,parent,doc,classnum,class_name , pwd,pic_name) 
            values ('0','$chap_num',?,?,?,?,?,?,?,?, '$class_year_p[$classnum]' ,? ,'$pic_fn') " ;   
$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('sssssssss',check_mysqli_param($cmode),check_mysqli_param($txt_title),check_mysqli_param($txt_author),check_mysqli_param($txt_type),check_mysqli_param($txt_teacher),check_mysqli_param($txt_parent),check_mysqli_param($txt_doc),check_mysqli_param($classnum),check_mysqli_param($txt_pwd));
$stmt->execute();
$stmt->close();
//mysqli
	
     header("location:paper_list.php?book_num=$book_num&chap_num=$chap_num") ;

}

//-----------------------------------------------------------------      
  if ($Submit=="確定更改") {
  	
 
     //有圖檔
     if (is_uploaded_file($_FILES['pic_file']['tmp_name'])) {
        //上傳檔案
        if (!eregi("(.jpg|.jpeg|.png|.gif|.swf)$",  $_FILES['pic_file']['name']) ) {
          echo "必須上傳圖檔，只支援 .jpg .gif .png .swf 格式" ;
          echo "<a href=\"javascript:history.go(-1)\" > 回上頁 </a> " ;
          exit ;
        }           
        
        if ($old_pic_name){   //有舊圖
            @unlink($savepath. "___" . $old_pic_name );
            @unlink($savepath . $old_pic_name); 
        }
        
        $pic_fn =   $nday . $_FILES['pic_file']['name'] ;
        move_uploaded_file($_FILES['pic_file']['tmp_name'], $savepath . $pic_fn);
        dosmalljpg($savepath , $pic_fn) ;
     }else {
      if ($old_pic_name) $pic_fn = $old_pic_name ;  //保留舊圖檔名
     }
          
     /*
     $sqlstr =  "update magazine_paper set chap_num='$chap_num', tmode ='$cmode',
                 title='$txt_title',author='$txt_author',type_name='$txt_type',
                 teacher ='$txt_teacher' ,parent ='$txt_parent',
                 classnum ='$classnum', class_name ='$class_year_p[$classnum]' , pic_name ='$pic_fn' ,doc = '$txt_doc' " ;   

     if ($txt_pwd) $sqlstr .=  " ,pwd='$txt_pwd' " ;           
     $sqlstr .=  " where id = $paper_id  " ;      
     //$sqlstr = stripslashes($sqlstr); 
     //echo $sqlstr ;
	   $CONN->Execute($sqlstr) ; 
	 */
	 
	 //mysqli
     $chap_num=intval($chap_num);
     $paper_id=intval($paper_id);	 
     $sqlstr =  "update magazine_paper set chap_num='$chap_num', tmode =?,title=?,author=?,type_name=?,teacher =? ,parent =?,classnum =?, class_name ='$class_year_p[$classnum]' , pic_name ='$pic_fn' ,doc = ? " ;   

     if ($txt_pwd) $sqlstr .=  " ,pwd=? " ;           
     $sqlstr .=  " where id = $paper_id  " ;      

$stmt = "";
$stmt = $mysqliconn->prepare($sqlstr);
$stmt->bind_param('ssssssss',check_mysqli_param($cmode),check_mysqli_param($txt_title),check_mysqli_param($txt_author),check_mysqli_param($txt_type),check_mysqli_param($txt_teacher),check_mysqli_param($txt_parent),check_mysqli_param($classnum),check_mysqli_param($txt_doc));
if ($txt_pwd)$stmt->mbind_param('s',check_mysqli_param($txt_pwd));
$stmt->execute();
$stmt->close();
//mysqli
	 
     header("location:paper_list.php?book_num=$book_num&chap_num=$chap_num") ;    
    
  }    
  
//-----------------------------------------------------------------    
//取得期別、單元  
  $chap_num=intval($chap_num);  
  $sqlstr =  "select a.* ,b.book_path ,b.ed_begin,b.ed_end  ,b.setpasswd , b.is_fin
              from magazine_chap a ,magazine b  
              where  a.id = '$chap_num'  and a.book_num= b.id " ;   
  $result = $CONN->Execute($sqlstr) ;
  while ($row= $result->FetchRow()) {
    $chap_name = $row["chap_name"] ;
    $chap_path = $row["chap_path"] ;
    $book_path = $row["book_path"] ;
    $cmode = $row["cmode"] ;
    $bdate = $row["ed_begin"] ;
    $enddate = $row["ed_end"] ;
    $setpasswd = $row["setpasswd"] ; //預設密碼
    $is_fin = $row["is_fin"] ;    
  }

  if (( $is_fin ) or (date("Y-m-d")<$bdate or date("Y-m-d")>$enddate)) {
     echo "未在規定時間內( $bdate ~ $enddate)上傳稿件！" ;
     redir ("paper_list.php" ,2) ;
     exit ;
  } 
  
  
?>
<html>
<head>
<title>稿件上傳</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<script language="JavaScript">

function chk_empty(item) {
   if (item.value=="") { return true; } 
}

function check() { 
   var errors='' ;
   <?php
    //編修模式
    if ($paper_id) echo "var editmode = 1 ;\n" ;
    else echo "var editmode= false ; \n" ;
    
    //型態
    echo "var cmode = $cmode ; \n" ;
    
   ?>
   
   if (chk_empty(document.myform.txt_title) )  {
      errors = '題目欄不可為空白！ \n' ; }
   if (chk_empty(document.myform.txt_author)) {	
       errors += '作者欄不可以空白！\n' ;
   }
   
   
   if (cmode ==1) { //上傳圖型
     // if (chk_empty(document.myform.txt_doc)) {	
     //   errors += '作品說明欄不可以空白！\n' ;
     // }    
     
     if (!editmode && chk_empty(document.myform.pic_file))	{
       errors += '上傳圖檔不可以空白！\n' ;
     }          
   }else {
     if (chk_empty(document.myform.txt_doc))	{
       errors += '文章內容不可空白！\n' ;
     }
  
   }  
  
  
   
   if (cmode != 2) { //非班級訊息
     if (chk_empty(document.myform.txt_teacher)){	
       errors += '指導教師欄不可以空白！\n' ;
     }   
     if (chk_empty(document.myform.txt_parent))	{
       errors += '家長姓名欄不可以空白！\n' ;
     }       
     
   }
   
   if (!editmode && chk_empty(document.myform.txt_pwd))	{
       errors += '第一次上傳，密碼欄不可以空白！\n' ;
   }   
   if (errors) alert (errors) ;
   document.returnValue = (errors == '');
 
}

</script>
</head>

<body bgcolor="#FFFFFF">


<?php
  // 編修-----------------------------------------------------------
  
  if ($paper_id) {
	$paper_id=intval($paper_id);
    $sqlstr =  "select * from magazine_paper  where  id = $paper_id  " ;   
    $result = $CONN->Execute($sqlstr);
    while ($row= $result->FetchRow()) {
      $title = $row["title"] ;
      $author = $row["author"] ;
      $type_name = $row["type_name"] ;
      $teacher = $row["teacher"] ;
      $parent = $row["parent"] ;
      $doc = $row["doc"] ;      
      $classnum = $row["classnum"] ;
      $pwd = $row["pwd"] ;      
      $pic_name = $row["pic_name"] ;     
      $pwd = $row["pwd"] ;
    }    

    if (  $magazine_upload_pwd <> $pwd) {
      echo "  <form method='post'  >
        <p>先前自定的密碼： 
        <input type='text' name='txt_up_pwd'>
        <input type='submit' name='pwd_Submit' value='送出'>
        <input type='hidden' name='paper_id' value='$paper_id'> </p>
        <input type='hidden' name='book_num' value='$book_num'>
        <input type='hidden' name='chap_num' value='$chap_num'> 
        <a href='paper_list.php'>放棄！回稿件列表<a>
        </form >" ;
      exit ;        
    }
            
?>      
<form method="post" action="<?php echo $self_php ?>" enctype="multipart/form-data" name="myform" onSubmit="check();return document.returnValue">
  <div align="center">
    <h2>第<?php echo " $book_num 期 $chap_name 編修" ?> </h2>
    <table width="80%" border="1" cellspacing="0" cellpadding="4" bgcolor="#FFCC99" bordercolorlight="#333333" bordercolordark="#FFFFFF">
      <tr> 
        <td width="15%">標題：</td>
        <td colspan="3"> 
          <input type="text" name="txt_title" value="<?php echo $title; ?>" size="40">
        </td>
      </tr>
      <tr> 
        <td width="15%">班級：</td>
        <td width="34%"> 
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
        <td width="10%">作者：</td>
        <td width="41%"> 
          <input type="text" name="txt_author" value="<?php echo $author; ?>">
        </td>
      </tr>
<?php
if ($cmode < 2) { // 文章、圖檔有
?>
      <tr> 
        <td width="15%">指導教師：</td>
        <td width="34%"> 
          <input type="text" name="txt_teacher" value="<?php echo $teacher; ?>">
        </td>
        <td width="10%">家長：</td>
        <td width="41%"> 
          <input type="text" name="txt_parent" value="<?php echo $parent; ?>">
        </td>
      </tr>
<?php
}
?>      
      <tr> 
        <td width="15%">設定密碼：</td>
        <td colspan="3"> 
          <p> 
            <input type="text" name="txt_pwd">
            <br>
            <font color="#FF0000" size="2">(下次編修需要以此密碼進入) </font></p>
        </td>
      </tr>
 <?php
  if ($cmode ==1) {
      //上傳圖檔類型時  
?>
      <tr> 
        <td width="15%">圖型檔案：</td>
        <td colspan="3"> 
          <p>上傳圖檔： 
            <input type="file" name="pic_file" size="40">
            <font color="#FF0000" size="2">(jpg、png格式)</font></p>
          <p>圖檔說明：<br>
            <textarea name="txt_doc" cols="60" rows="4"><?php echo $doc; ?></textarea>
          </p>
        </td>
      </tr>
<?php
} elseif($cmode ==4) {  //上傳SWF類型時  
?>
      <tr> 
        <td width="23%">上傳SWF動畫：</td>
        <td width="77%"> 
          <p> 上傳SWF動畫檔： 
            <input type="file" name="pic_file" size="40">
            <br>
            <font color="#FF0066" size="2">(限SWF格式檔)</font> </p>
          <p> 
            <textarea name="txt_doc" cols="60" rows="15"></textarea>
          </p>
        </td>
      </tr>
<?php
}
else 
//上傳文章、班級訊息時 ---beg
{
?>
      <tr> 
        <td width="15%">上傳文章：</td>
        <td colspan="3"> 
          <p> 上傳圖檔： 
            <input type="file" name="pic_file" size="40">
            <br>
            <font color="#FF0000" size="2">(100*100以內的小圖，可省略)</font> </p>
          <p>文章修改：<br>

            <textarea name="txt_doc" cols="60" rows="15"><?php echo $doc; ?></textarea>
          </p>
        </td>
      </tr>
<?php
}
?>      
    </table>
    <p> 
      <input type="submit" name="Submit" value="確定更改">
      <input type="reset" name="Submit2" value="重設">
      <input type="hidden" name="chap_num" value="<?php echo $chap_num; ?>">
      <input type="hidden" name="cmode" value="<?php echo $cmode; ?>">
      <input type="hidden" name="book_path" value="<?php echo $book_path; ?>">
      <input type="hidden" name="chap_path" value="<?php echo $chap_path; ?>">
      <input type="hidden" name="old_pic_name" value="<?php echo $pic_name; ?>">
      <input type="hidden" name="paper_id" value="<?php echo $paper_id; ?>">
    </p>
  </div>
</form>    
<?php
}
else
{
// 新上傳-----------------------------------------------------------
    if (  $magazine_upload_pwd <> $setpasswd) {
     
      echo "  <form  method=\"post\" >" ;  
      echo '<p>上傳密碼(最新消息中有公佈)： 
        <input type="text" name="txt_up_pwd">
        <input type="submit" name="pwd_Submit" value="送出">
        <input type="hidden" name="paper_id" value="0">
      </p>' ;
      echo "<input type=\"hidden\" name=\"book_num\" value=\"$book_num\"> \n" ;
      echo "<input type=\"hidden\" name=\"chap_num\" value=\"$chap_num\"> \n" ;
      echo '<a href="paper_list.php">放棄！回稿件列表<a>' ;      
      echo "  </form >" ;
      exit ;        
    }    
?>
<form method="post" action="<?php echo $self_php ?>" enctype="multipart/form-data" name="myform" onSubmit="check();return document.returnValue">
  <div align="center">
    <h2>第<?php echo " $book_num 期 $chap_name 上傳" ?> </h2>
    <table width="80%" border="1" cellspacing="0" cellpadding="4" bgcolor="#FFCC99" bordercolorlight="#333333" bordercolordark="#FFFFFF">
      <tr> 
        <td width="17%">標題：</td>
        <td colspan="3"> 
          <input type="text" name="txt_title" size="40">
        </td>
      </tr>
      <tr> 
        <td width="17%">班級：</td>
        <td width="32%"> 
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
        <td width="11%">作者：</td>
        <td width="40%"> 
          <input type="text" name="txt_author">
        </td>
      </tr>
<?php
if ($cmode < 2) {
?>
      <tr> 
        <td width="17%">指導教師：</td>
        <td width="32%"> 
          <input type="text" name="txt_teacher">
        </td>
        <td width="11%">家長：</td>
        <td width="40%"> 
          <input type="text" name="txt_parent">
        </td>
      </tr>
<?php
}
?>      
      <tr> 
        <td width="17%">設定密碼：</td>
        <td colspan="3"> 
          <input type="text" name="txt_pwd">
          <br>
          <font color="#FF0000" size="2">(下次編修需要以此密碼進入) </font> </td>
      </tr>
<?php

  if ($cmode ==1) {
  //上傳圖檔類型時  
?>
      <tr> 
        <td width="17%">上傳圖檔：</td>
        <td colspan="3"> 
          <p>圖檔： 
            <input type="file" name="pic_file" size="40">
            <font color="#FF0000" size="2">(請上傳JPG格式)</font> </p>
          <p>圖檔說明：<br>
            <textarea name="txt_doc" cols="60" rows="4"></textarea>
          </p>
        </td>
      </tr>
<?php
} elseif($cmode ==4) {  //上傳SWF類型時  
?>
      <tr> 
        <td width="23%">上傳SWF動畫：</td>
        <td width="77%"> 
          <p> 上傳SWF動畫檔： 
            <input type="file" name="pic_file" size="40">
            <br>
            <font color="#FF0066" size="2">(限SWF格式檔)</font> </p>
          <p> 
            <textarea name="txt_doc" cols="60" rows="15"></textarea>
          </p>
        </td>
      </tr>
<?php
}
else{
  //上傳文章、班級訊息時
?>
      <tr> 
        <td width="17%">上傳文章：</td>
        <td colspan="3"> 
          <p>上傳圖檔： 
            <input type="file" name="pic_file" size="40">
            <br>
            <font color="#FF0000" size="2">(100*100以內的小圖，可省略)</font> </p>
          <p>文章內容：<font color="#FF0000" size="2">(直接把文字貼到下方文字盒中。)</font> <br>
            <textarea name="txt_doc" cols="60" rows="15"></textarea>
          </p>
          </td>
      </tr>
<?php
}
?>
    </table>
    <p> 
      <input type="submit" name="Submit" value="確定上傳">
      <input type="reset" name="Submit2" value="重設">
      <input type="hidden" name="chap_num" value="<?php echo $chap_num; ?>">
      <input type="hidden" name="cmode" value="<?php echo $cmode; ?>">
      <input type="hidden" name="book_path" value="<?php echo $book_path; ?>">
      <input type="hidden" name="chap_path" value="<?php echo $chap_path; ?>">
    </p>
  </div>
</form>  
<?php
}
?>        

</body>
</html>
