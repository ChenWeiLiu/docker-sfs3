<?php
//$Id: a_main_admin.php 8952 2016-08-29 02:23:59Z infodaes $
  include_once( "config.php") ;
  include_once( "../../include/sfs_case_PLlib.php") ;
    
    // --認證 session 
    sfs_check();
   
//非管理者 
$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
if ( !checkid($SCRIPT_FILENAME,1)){
      Header("Location: index.php"); 
}         
//========================================================================  
    if ($_POST["Submit"] == "新增一期") {
        
      //建立校刊存放資料目錄(以日期建立)
      $nday = date("Ymd") ;
      chdir($basepath) ; 	
      $dirstr = "$nday" ;
      $count = 0 ;
      while (is_dir($dirstr)) {
     	 $count ++ ;
     	 $dirstr = "$nday-" . $count;
      }	
      mkdir( $dirstr , 0700) ;        
      
    
        
      $sqlstr =  " insert into  magazine
            (id,num,publish_date,publish,setpasswd,admin,ed_begin,ed_end, book_path ,themes ) 
            values ('0','$_POST[num]','$_POST[publish_date]','$_POST[publish]','$_POST[setpasswd]', '$_POST[admin]','$_POST[edit_begin]' ,'$_POST[edit_end]','$dirstr' ,'$_POST[themes]' )  " ;   
   
      $CONN->Execute($sqlstr) ;         
      header("location:a_main.php") ;
        
    }
    
    if ($_POST["Submit"] == "修改") {
    	echo $templ_file ;
      //樣版檔案
    
      $sqlstr =  "update  magazine set id= '$_POST[id]' ,num = '$_POST[num]' ,publish_date='$_POST[publish_date]',
          publish='$_POST[publish]',setpasswd='$_POST[setpasswd]',admin='$_POST[admin]',
          ed_begin='$_POST[edit_begin]',ed_end='$_POST[edit_end]'  ,
          is_fin= '$_POST[check_fin]' , themes='$_POST[themes]' 
          where id= '$_POST[id]'   " ;
      //$sqlstr = stripslashes($sqlstr);     
      $CONN->Execute($sqlstr) ;                
      header("location:a_main.php") ;
        
    }

    if ($_POST["Submit"] == "回管理介面") {
      echo $templ_file ;
      //樣版檔案          
      header("location:a_main.php") ;        
    }

    if ($_POST["Submit"] == "刪除") {
      echo $templ_file ;
      //樣版檔案

	  //刪除magazine_chap資料表
      $sqlstr1 = "select id from `magazine_chap` where book_num= '$_POST[id]';";
	  $rs = $CONN->Execute($sqlstr1);
      $rows = $rs -> RecordCount();
	  if($rows>0){
	    while($ar = $rs->FetchRow()){
		  //$id = $ar["id"];
          $sqlstr3 =  "delete from `magazine_paper` where chap_num= '".$ar['id']."';";
	      $CONN->Execute($sqlstr3);
		}
		$sqlstr2 = "delete from magazine_chap where book_num= '$_POST[id]';";
	    $CONN->Execute($sqlstr2);
	  }	      
      $sqlstr =  "delete from magazine where id= '$_POST[id]';";
	  $CONN->Execute($sqlstr);
	  $exec_path = "rm -rf ".$basepath.$_POST[m_path];
	  //echo $exec_path;
	  //exit;
	  exec($exec_path);                
      header("location:a_main.php") ;
        
    }
//========================================================================        
    //取得樣版目錄
    $themesdir = $tpl->template_dir ;   
    $handle = @opendir($themesdir) ;
    while ($filelist = readdir($handle)) {
        if ($filelist<>".." and $filelist<>".") 
          if (is_dir($themesdir."/" .$filelist)) 
             $themes_list[$filelist] = $filelist;
    }         
 
     


head("電子校刊期別管理");  
print_menu($m_menu_p);
?>  
  
<html>
<head>
<title>本期電子校刊基本資料編修</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>

<body bgcolor="#FFFFFF">
<form method="post" action="<?php echo $self_php ?>" enctype="multipart/form-data">
  <table width="85%" border="0" cellspacing="0" cellpadding="4" align="center">
    <tr> 
      <td width="73%"> 
        <h1 align="center">電子校刊期別資料設定</h1>
      </td>
      <td width="14%"> 
        <div align="center"><a href="a_main.php">管理主畫面</a></div>
      </td>
    </tr>
  </table>

<?php
//----------------------------------------------------------------------------
//新增一期
  if (!$_GET[id]) {
    //預設日期
    $nday= date("Y-m-d") ;
    $eday = GetdayAdd($nday,10) ;  //加上10天

?>  
  <table width="80%" border="1" cellspacing="0" cellpadding="4" align="center" bgcolor="#99CCFF" bordercolorlight="#333333" bordercolordark="#FFFFFF">
    <tr> 
      <td width="29%">期別：</td>
      <td width="71%"> 第 
        <input type="text" name="num" size="5" maxlength="5">
        期 </td>
    </tr>
    <tr> 
      <td width="29%">發行日期：</td>
      <td width="71%"> 
        <input type="text" name="publish_date" value="<?php echo $eday ?>">
      </td>
    </tr>
    <tr> 
      <td width="29%">發行人訊息：</td>
      <td width="71%"> 
        <textarea name="publish" cols="30" rows='4' >發行人：
編輯群：</textarea>
      </td>
    </tr>
    <tr> 
      <td width="29%">編輯人員代號：<br>
      </td>
      <td width="71%"> 
        <input type="text" name="admin" size="40">
        <br>
        (以逗號做分隔) </td>
    </tr>
    <tr> 
      <td width="29%">預設密碼：</td>
      <td width="71%"> 
        <input type="text" name="setpasswd" size="10" maxlength="10">
        (各班上傳使用密碼) </td>
    </tr>
    <tr> 
      <td width="29%">開始編輯日期：</td>
      <td width="71%"> 
        <input type="text" name="edit_begin" value="<?php echo $nday ?>">
      </td>
    </tr>
     
    <tr> 
      <td width="29%">上傳結束日期：</td>
      <td width="71%"> 
        <input type="text" name="edit_end" value="<?php echo $eday ?>">
      </td>
    </tr>
    <tr> 
      <td width="29%">選用樣版：</td>
      <td width="71%"> 
          <select name="themes">
	<?php
	reset($themes_list);
	 while(list($tkey,$tvalue)= each($themes_list))
	 {
          if ($tkey == $themes)	  
             echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
           else
             echo  sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
          }             	 
 	?>          
          </select>
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <input type="submit" name="Submit" value="新增一期">
        <input type="reset" name="Submit3" value="重設">
      </td>
    </tr>
  </table>
<?
//刪除
}elseif($_GET[id]>0 and $_GET[del]==1){
      $sqlstr =  "select * from magazine  where  id='$_GET[id]' " ;   
      
      $result = mysql_query ($sqlstr,$conID) ; 
      if ($result) 
        while ($row=mysql_fetch_array($result)) {
          $book_num = $row["num"] ; //取得期別    
          $id = $row["id"] ;
          $publish_date = $row["publish_date"] ;
          $publish = $row["publish"] ;
          $setpasswd = $row["setpasswd"] ;
          $admin = $row["admin"] ;
          $is_fin = $row["is_fin"] ;
          $ed_begin = $row["ed_begin"] ;

          $ed_end = $row["ed_end"] ;
          $book_path = $row["book_path"] ;
          $themes = $row["themes"] ;
        }   

?>    
  <table width="80%" border="1" cellspacing="0" cellpadding="4" align="center" bgcolor="#99CCFF" bordercolorlight="#333333" bordercolordark="#FFFFFF">
    <tr> 
      <td width="29%">期別：</td>
      <td width="71%"> 第
        <input type="text" name="num" size="5" maxlength="5" value="<?php echo $book_num ?>" disabled>
        期 </td>
    </tr>
    <tr> 
      <td width="29%">發行日期：</td>
      <td width="71%"> 
        <input type="text" name="publish_date" value="<?php echo $publish_date ?>" disabled>
      </td>
    </tr>
    <tr> 
      <td width="29%">發行人訊息：</td>
      <td width="71%"> 
        <textarea name="publish" cols="30" rows="4" disabled><?php echo $publish ?></textarea>
      </td>
    </tr>
    <tr> 
      <td width="29%">負責人代號：<br>
      </td>
      <td width="71%"> 
        <input type="text" name="admin" size="40" value="<?php echo $admin ?>" disabled>
        <br>
        (以逗號做分隔) </td>
    </tr>
    <tr> 
      <td width="29%">預設密碼：</td>
      <td width="71%"> 
        <input type="text" name="setpasswd" size="10" maxlength="10" value="<?php echo $setpasswd ?>" disabled>
        (各班上傳使用密碼) </td>
    </tr>
    <tr> 
      <td width="29%">開始編輯日期：</td>
      <td width="71%"> 
        <input type="text" name="edit_begin" value="<?php echo $ed_begin ?>" disabled>
      </td>
    </tr>

    <tr> 
      <td width="29%">上傳結束日期：</td>
      <td width="71%"> 
        <input type="text" name="edit_end" value="<?php echo $ed_end ?>" disabled>
      </td>
    </tr>
    <tr> 
      <td width="29%">完成標記：</td>
      <td width="71%"> 
        <input type="checkbox" name="check_fin" value="1" <?php if ($is_fin) echo "checked" ?>  disabled>
        已完成，不可再修改 </td>
    </tr>
    <tr> 
      <td width="29%">樣版選用：</td>
      <td width="71%"> 
          <select name="themes" disabled>
	<?php
	reset($themes_list);
	 while(list($tkey,$tvalue)= each($themes_list))
	 {
          if ($tkey == $themes)	  
             echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
           else
             echo  sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
          }             	 
 	?>          
          </select>
      </td>
    </tr>
    <tr> 
      <td colspan="2">
        <input type="submit" name="Submit" value="刪除">
		<input type="submit" name="Submit" value="回管理介面">
		<FONT SIZE="" COLOR="#FF0000">請小心刪除後，原來上傳的資料都將一併刪除!!</FONT>
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="m_path" value="<?php echo $book_path ?>">
      </td>
    </tr>
  </table>  
<?
//修改
}else
{
      $sqlstr =  "select * from magazine  where  id='$_GET[id]' " ;   
      
      $result = mysql_query ($sqlstr,$conID) ; 
      if ($result) 
        while ($row=mysql_fetch_array($result)) {
          $book_num = $row["num"] ; //取得期別    
          $id = $row["id"] ;
          $publish_date = $row["publish_date"] ;
          $publish = $row["publish"] ;
          $setpasswd = $row["setpasswd"] ;
          $admin = $row["admin"] ;
          $is_fin = $row["is_fin"] ;
          $ed_begin = $row["ed_begin"] ;

          $ed_end = $row["ed_end"] ;
          $book_path = $row["book_path"] ;
          $themes = $row["themes"] ;
        }   

?>    
  <table width="80%" border="1" cellspacing="0" cellpadding="4" align="center" bgcolor="#99CCFF" bordercolorlight="#333333" bordercolordark="#FFFFFF">
    <tr> 
      <td width="29%">期別：</td>
      <td width="71%"> 第
        <input type="text" name="num" size="5" maxlength="5" value="<?php echo $book_num ?>">
        期 </td>
    </tr>
    <tr> 
      <td width="29%">發行日期：</td>
      <td width="71%"> 
        <input type="text" name="publish_date" value="<?php echo $publish_date ?>">
      </td>
    </tr>
    <tr> 
      <td width="29%">發行人訊息：</td>
      <td width="71%"> 
        <textarea name="publish" cols="30" rows="4"><?php echo $publish ?></textarea>
      </td>
    </tr>
    <tr> 
      <td width="29%">負責人代號：<br>
      </td>
      <td width="71%"> 
        <input type="text" name="admin" size="40" value="<?php echo $admin ?>">
        <br>
        (以逗號做分隔) </td>
    </tr>
    <tr> 
      <td width="29%">預設密碼：</td>
      <td width="71%"> 
        <input type="text" name="setpasswd" size="10" maxlength="10" value="<?php echo $setpasswd ?>">
        (各班上傳使用密碼) </td>
    </tr>
    <tr> 
      <td width="29%">開始編輯日期：</td>
      <td width="71%"> 
        <input type="text" name="edit_begin" value="<?php echo $ed_begin ?>">
      </td>
    </tr>

    <tr> 
      <td width="29%">上傳結束日期：</td>
      <td width="71%"> 
        <input type="text" name="edit_end" value="<?php echo $ed_end ?>">
      </td>
    </tr>
    <tr> 
      <td width="29%">完成標記：</td>
      <td width="71%"> 
        <input type="checkbox" name="check_fin" value="1" <?php if ($is_fin) echo "checked" ?> >
        已完成，不可再修改 </td>
    </tr>
    <tr> 
      <td width="29%">樣版選用：</td>
      <td width="71%"> 
          <select name="themes">
	<?php
	reset($themes_list);
	 while(list($tkey,$tvalue)= each($themes_list))
	 {
          if ($tkey == $themes)	  
             echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
           else
             echo  sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
          }             	 
 	?>          
          </select>
      </td>
    </tr>
    <tr> 
      <td colspan="2">
        <input type="submit" name="Submit" value="修改">
        <input type="reset" name="Submit3" value="重設">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="m_path" value="<?php echo $book_path ?>">
      </td>
    </tr>
  </table>  
<?
}  
foot();
?>  
</form>
</body>
</html>
