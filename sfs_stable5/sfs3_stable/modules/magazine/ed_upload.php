<?php
//$Id: ed_upload.php 8952 2016-08-29 02:23:59Z infodaes $
  include_once( "config.php") ;
  include_once( "../../include/PLlib.php") ;
  // --認證 session 
  sfs_check();
  
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
  
  if (!$chap_num)  header("location:paper_list.php") ;
  
  
  $class_year_p = class_base($curr_year_seme); //班級

  

  
//-----------------------------------------------------------------  
  if ($Submit=="確定上傳") {
     $nday = date("mdhi-") ;
     $savepath = $basepath .$book_path . "/" .$chap_path . "/" ;

     //圖檔
     if (is_uploaded_file($_FILES['pic_file']['tmp_name'])) {
        //上傳檔案
        if (!eregi("(.jpg|.jpeg|.png|.gif|.swf)$",  $_FILES['pic_file']['name']) ) {
          echo "必須上傳圖檔，只支援 .jpg .gif .png .swf格式" ;
          echo "<a href=\"javascript:history.go(-1)\" > 回上頁 </a> " ;
          exit ;
        }      
        
        $pic_fn =   $nday . $_FILES['pic_file']['name'] ;
        move_uploaded_file($_FILES['pic_file']['tmp_name'], $savepath . $pic_fn);
        dosmalljpg($savepath , $pic_fn) ;        
            
     }
     $sqlstr =  "insert into  magazine_paper (id,chap_num,tmode,title,author,type_name,
                 teacher,parent,doc,classnum ,class_name ,pwd,pic_name) 
                 values ('0','$chap_num','$cmode','$txt_title ' , '$txt_author ', '$txt_type' ,
                 '$txt_teacher ', '$txt_parent ' ,'$txt_doc ' ,'$classnum'  , '$class_year_p[$classnum]' ,'uxfd03' ,'$pic_fn') " ;   
     
     $CONN->Execute($sqlstr) ;  

     header("location:paper_list.php?book_num=$book_num&chap_num=$chap_num") ;

}


  
//-----------------------------------------------------------------    
//取得期別、單元    
  $sqlstr =  "select a.* ,b.admin ,b.book_path ,b.ed_begin,b.ed_end  ,b.setpasswd , b.is_fin
              from magazine_chap a ,magazine b  
              where  a.id = $chap_num  and a.book_num= b.id " ;   
  $result = $CONN->Execute($sqlstr) ;
  while ($row=$result->FetchRow()) {
    $chap_name = $row["chap_name"] ;
    $chap_path = $row["chap_path"] ;
    $book_path = $row["book_path"] ;
    $cmode = $row["cmode"] ;
    $bdate = $row["ed_begin"] ;
    $enddate = $row["ed_end"] ;
    $setpasswd = $row["setpasswd"] ; //預設密碼
    $is_fin = $row["is_fin"] ;    
    $editors =  $row["admin"] ;         //編輯群
  }

  if (( $is_fin ) ) {
     echo "已完稿，無法上傳稿件！" ;
     redir ("paper_list.php" ,2) ;
     exit ;
  } 
  
  if (!check_is_man2($editors)) {
     echo "你非本期編輯群成員，無權執行此功能！" ;
     redir("paper_list.php?book_num=$book_num&chap_num=$chap_num" ,2) ;
     exit ;
  }  
  head() ;   
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
     //if (chk_empty(document.myform.txt_doc)) {	
     //  errors += '作品說明欄不可以空白！\n' ;
     //}    
     
     if (!editmode && chk_empty(document.myform.pic_file))	{
       errors += '上傳圖檔不可以空白！\n' ;
     }          
   } 
   else {
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
   

   if (errors) alert (errors) ;
   document.returnValue = (errors == '');
 
}

</script>
</head>

<body bgcolor="#FFFFFF">

<form method="post" action="<?php echo $self_php ?>" enctype="multipart/form-data" name="myform" onSubmit="check();return document.returnValue">
  <div align="center">
    <h2>第<?php echo " $book_num 期 $chap_name 上傳" ?> </h2>
    <table width="80%" border="1" cellspacing="0" cellpadding="4" bgcolor="#FFCC99" bordercolorlight="#333333" bordercolordark="#FFFFFF">
      <tr> 
        <td width="23%">標題：</td>
        <td width="77%"> 
          <input type="text" name="txt_title" size="40">
        </td>
      </tr>
      <tr> 
        <td width="23%">班級：</td>
        <td width="77%"> 
          <select name="classnum">
            <?php
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
        <td width="23%">作者：</td>
        <td width="77%"> 
          <input type="text" name="txt_author">
        </td>
      </tr>
      <?php

if ($cmode < 2) {
?>
      <tr> 
        <td width="23%">指導教師：</td>
        <td width="77%"> 
          <input type="text" name="txt_teacher">
        </td>
      </tr>
      <tr> 
        <td width="23%">家長：</td>
        <td width="77%"> 
          <input type="text" name="txt_parent">
        </td>
      </tr>
      <?php
}
  if ($cmode ==1) {
  //上傳圖檔類型時  
?>
      <tr> 
        <td width="23%">上傳圖檔：</td>
        <td width="77%"> 
           <p> 上傳圖檔： 
            <input type="file" name="pic_file" size="40">
            <br>
            <font color="#FF0066" size="2">(限JPG、PNG、GIF格式檔)</font> </p>
          <p>         
          <textarea name="txt_doc" rows="3" cols="40" ></textarea>
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
{
  //上傳文章、班級訊息時
?>
      <tr> 
        <td width="23%">上傳文章：</td>
        <td width="77%"> 
          <p> 上傳小插圖檔： 
            <input type="file" name="pic_file" size="40">
            <br>
            <font color="#FF0066" size="2">(限JPG、PNG、GIF 格式檔)</font> </p>
          <p> 
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
      <input type="hidden" name="chap_num" value="<?php echo $chap_num ?>">
      <input type="hidden" name="cmode" value="<?php echo $cmode ?>">
      <input type="hidden" name="book_path" value="<?php echo $book_path ?>">
      <input type="hidden" name="chap_path" value="<?php echo $chap_path ?>">
    </p>
  </div>
</form>  
<?php

foot() ;
?>        

</body>
</html>
