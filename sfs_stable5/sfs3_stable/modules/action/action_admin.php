<?php

  require("config.php") ;
  
  //$debug = 1;
  // 認證檢查
  sfs_check();
  
  $do = $_GET['do'] ;
  $id = $_GET['id'] ;
  $Submit = $_POST['Submit'] ; 
  $session_tea_name = $_SESSION['session_tea_name'] ;
  
  if ($do=="exit") {
    header("Location:action.php" ) ; 
    exit ;
  }  
  
  

  $nday = date("Y-m-d") ;
  
//新增---------------------------------------------------------  
  if ($Submit == "新增" ) {
     $nday = date("Y-m-d") ;
     
     $Iact_date = $_POST['Iact_date'] ;
     $Iact_name = $_POST['Iact_name'] ;
     $Iact_info = $_POST['Iact_info'] ;
     $Iuser = $_POST['Iuser'] ;     
     
     //建立目錄(以建立日期)
     chdir($savepath) ; 	
     $dirstr = "$nday" ;
     $count = 0 ;
     while (is_dir($dirstr)) {
     	$count ++ ;
     	$dirstr = "$nday-" . $count;
     }	
     mkdir($dirstr , 0700) ;
     
     $updir = $savepath . $dirstr . '/' ;
     //主網頁上傳
     if (is_uploaded_file($_FILES['Iact_index']['tmp_name'])) {
        $Iact_index_name = $_FILES['Iact_index']['name']  ; 
        move_uploaded_file($_FILES['Iact_index']['tmp_name'],  $updir . $Iact_index_name);
     }
     
  
     //簡介圖
     if (is_uploaded_file($_FILES['Iact_icon']['tmp_name'])) {
        $Iact_icon_name = $_FILES['Iact_icon']['name']  ; 
        move_uploaded_file($_FILES['Iact_icon']['tmp_name'],  $updir . $Iact_icon_name);
     }


     //壓縮檔上傳要做解壓縮     
     if (is_uploaded_file($_FILES['Iact_zip']['tmp_name'])) {

        $filename=  $_FILES['Iact_zip']['name']  ;        
        move_uploaded_file($_FILES['Iact_zip']['tmp_name'],  $updir . $filename);
        $tmpfilename = $updir . $filename ;
        //chdir($updir) ; 
        //exec(escapeshellcmd("unzip $tmpfilename ")) ;
        
        p_unzip($tmpfilename , $updir) ;
        
        unlink($tmpfilename);        
     }     
          
     //有數個上傳動作  
     $upfile = $_FILES['Iupload'] ;
     for ($i = 0 ; $i < count($upfile) ; $i++) {
         if (is_uploaded_file($upfile['tmp_name'][$i])) {
             move_uploaded_file($upfile['tmp_name'][$i] ,  $updir .$upfile['name'][$i]);
         }
     }        

     $sqlstr = "insert into $tbname (act_ID,act_date,act_name,act_info,act_icon,act_dir,act_index,act_postdate,act_auth,act_view)
        values ( '0', '$Iact_date', '$Iact_name ' ,'$Iact_info ', '$Iact_icon_name', '$dirstr', '$Iact_index_name', '$nday' , '$Iuser ','0') " ;
     if($debug) echo "$sqlstr <br>" ;
     $result = $CONN->Execute( $sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;   

     redir("action.php" ,1) ; 
     echo "新增一筆完成！" ;
     
     exit ; 
  }
  	
//更新---------------------------------------------------------    	
  if ($Submit == "更新" ) {
        
     $Iact_date = $_POST['Iact_date'] ;
     $Iact_name = $_POST['Iact_name'] ;
     $Iact_info = $_POST['Iact_info'] ;
     $Iact_ID = $_POST['Iact_ID'] ;
     $Ioldicon = $_POST['Ioldicon'] ;
     $Ipath = $_POST['Ipath'] ;
     $chkdelfile = $_POST['chkdelfile'] ;
          
     $updir = $savepath . $Ipath . '/' ;     
     
     //簡介圖
     if (is_uploaded_file($_FILES['Iact_icon']['tmp_name'])) {
        $iconf = $_FILES['Iact_icon']['name']  ; 
        move_uploaded_file($_FILES['Iact_icon']['tmp_name'],  $updir . $iconf);
     }          
     else if ($Ioldicon){
     	$iconf= $Ioldicon ;		//取得舊檔
     }	
     
     //小範例圖有修改
     if (($Ioldicon) && ($iconf <> $Ioldicon)) {
     	 unlink($updir.$Ioldicon);
     }	      
     
     
     //要把檔案刪除
     $numi = count($chkdelfile);
     if ($numi) {
       for ($i=0 ; $i<$numi ;$i++) {
     	 $delfile =$chkdelfile[$i] ;
         unlink($updir.$delfile);
       } 
     }       
     
     
     //壓縮檔上傳要做解壓縮     
     if (is_uploaded_file($_FILES['Iact_zip']['tmp_name'])) {
        $tmpfilename = $updir . $_FILES['Iact_zip']['name'] ;
        move_uploaded_file($_FILES['Iact_zip']['tmp_name'],  $tmpfilename );

        //chdir($updir) ; 
        //exec(escapeshellcmd("unzip $tmpfilename ")) ;
        p_unzip ($tmpfilename , $updir) ;
        
        unlink($tmpfilename);        
     }          
     
   
     //有數個上傳動作  
     $upfile = $_FILES['Iupload'] ;
     for ($i = 0 ; $i < count($upfile) ; $i++) {
         if (is_uploaded_file($upfile['tmp_name'][$i])) {
             move_uploaded_file($upfile['tmp_name'][$i] ,  $updir .$upfile['name'][$i]);
         }
     }           
     
     $sqlstr = "update  $tbname set act_name='$Iact_name ' ,act_date='$Iact_date', act_info='$Iact_info ', act_icon='$iconf' where act_ID = '$Iact_ID' " ;
     if($debug) echo "$sqlstr <br>" ;
     $result = $CONN->Execute( $sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;   
      //圖型部份修改
 

     redir("action.php" ,1) ; 
     echo "更新一筆完成！" ;
     exit ; 
  }
  
//確定刪除---------------------------------------------------------      
  if ($do == "del2" ) {
     $updir = $savepath.'/'. $dpath . '/' ;
     chdir($updir) ;
     $dirs = dir($updir) ;
     $dirs ->rewind() ;
     while ( $filelist = $dirs->read()) {
     	 if (($filelist!=".") && ($filelist!="..")){
     	   if ($debug) echo "del $updir $filelist" ;
           unlink($updir.$filelist);      	
         }
     }
     $dirs->close() ;
     rmdir($updir); 

     $sqlstr = "delete  from $tbname  where act_ID = $id " ;
 
     if($debug) echo "$sqlstr <br>" ;
     $result = $CONN->Execute( $sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;    
     redir("action.php" ,1) ; 
     echo "刪除動作完成！" ;
     exit ; 
  }  
  head("活動花絮") ;
?>
<html>
<head>
<title>活動花絮</title>
<script language="JavaScript">

function chk_empty(item) {
   if (item.value=="") { return true; } 
}

function check() { 
   var errors='' ;
   
   if (chk_empty(document.myform.Iact_name) || chk_empty(document.myform.Iact_date) || chk_empty(document.myform.Iact_info))  {
      errors = '活動名稱、日期、簡介部份不可以空白！' ; }
   else {
     if (chk_empty(document.myform.Iact_index))	
       errors = '主網頁檔案不可以空白！' ;
   }
   if (errors) alert (errors) ;
   document.returnValue = (errors == '');
 
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>

<body bgcolor="#FFFFFF">

<?php 
  //-----------------------------------------編修 ---------------------- 
  if ($do=="edit") {
    $sqlstr = "select * from $tbname where act_ID='$id' " ;
    $result = $CONN->Execute( $sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;  	
    $nb = $result->FetchRow()  ;
    if (trim($nb[act_auth])!=trim($session_tea_name)) {
      	 echo "你非原公佈者，無權修改本篇文章！" ;
      	 exit ;
    }	     
?>  	
<form enctype="multipart/form-data"  name=myform method="post" action="<?php echo basename($PHP_SELF) ?>">
  <h2>活動花絮--修改 </h2>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#FFFFFF" bordercolordark="#3333FF" bgcolor="#CCFFFF" bordercolor="#33CCFF">
    <tr> 
      <td width="21%" bgcolor="#66CCFF">活動名稱：</td>
      <td width="79%"> 
        <input type="text" name="Iact_name" size="60" value="<?php echo $nb[act_name] ?>">
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#66CCFF">活動日期：</td>
      <td width="79%"> 
        <input type="text" name="Iact_date" size="60" value="<?php echo $nb[act_date] ?>">
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#66CCFF">簡介：</td>
      <td width="79%"> 
        <textarea name="Iact_info" cols="60" rows="3"><?php echo $nb[act_info] ?></textarea>
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#66CCFF">圖示：<br>
        200*200以內</td>
      <td width="79%"> 
        <input type="file" name="Iact_icon">
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#66CCFF">檔案整理：</td>
      <td width="79%"> 
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
          <tr>
            <td bgcolor="#33CCCC" valign="top" width="41%"> 
              <p>刪除檔案：</p>
              <p> 
<?php
     $updir = $savepath.'/'. $nb[act_dir] . '/' ;
     chdir($updir) ;
     $dirs = dir($updir) ;
     $dirs ->rewind() ;
     while ( $filelist = $dirs->read()) {
     	 if (($filelist!=".") && ($filelist!=".."))
     	 echo "<input type=\"checkbox\" name=\"chkdelfile[]\" value=\"$filelist\"> $filelist <br> ";
     }
     $dirs->close() ;
?>              

              <p>&nbsp;</p>
            </td>
            <td bgcolor="#CCFFCC" valign="top" width="59%"> 
              <p>再上傳檔案：</p>
               ZIP壓縮檔(大小：2MB以內)：<input name="Iact_zip" type="file" >
               <br>
               <font color="#FF3333" size="-1">多檔案時先把所有檔案壓縮再一次上傳，會自動解開放在該目錄中。(主網頁檔案，不要壓縮)</font>               
              <ol>
<?php        
        for ($i=1 ; $i<= $upfilenum ; $i++) {
          echo "<li> " ;
          echo "  <input type=\"file\" name=\"Iupload[]\" > " ;
          echo "</li>  \n" ;
        }  
?>      

              </ol>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#66CCFF">&nbsp;</td>
      <td width="79%"> 
        <input type="submit" name="Submit" value="更新">
        <input type="reset" name="Submit22" value="重設">
        <input type="hidden" name="Iact_ID" value="<?php echo $nb[act_ID] ?>">
        <input type="hidden" name="Ioldicon" value="<?php echo $nb[act_icon] ?>">
        <input type="hidden" name="Ipath" value="<?php echo $nb[act_dir] ?>">
      </td>
    </tr>
  </table>
</form>  	
  	
  	
<?php 
  }
  //刪除------------------------------------------------------------------
  else if ($do == "delete") {
      $sqlstr = "select * from $tbname where act_ID='$id' " ;  	
      $result = $CONN->Execute( $sqlstr) ;	
      $nb=$result->FetchRow() ; 	
      if (trim($nb[act_auth])!=trim($session_tea_name) ) {
         redir("action.php" ,1) ; 
      	 echo "你非原公佈者，無權刪除本篇文章！" ;
      	 
      	 exit ;
      }	 
      
?>  	

<table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#FFFFFF" bordercolordark="#3333FF">
  <tr>
    <td> 
      <h2>活動花絮--刪除 </h2>
      <p>確定刪除第<?php echo $id. "筆: $nb[act_name]"  ?> &nbsp; <a href="<?php echo basename($PHP_SELF) . '?do=del2&id=' .$id .'&dpath=' .$nb[act_dir] ?>">是</a> 
        &nbsp;&nbsp; <a href="<?php echo basename($PHP_SELF) . '?do=exit' ?>">否</a> </p>
    </td>
  </tr>
</table>  	

<?php  
}	

  else {	
  //---------------------------------------新增	
?>  	
  	
  	
<form name=myform enctype="multipart/form-data" method="post" action="<?php echo basename($PHP_SELF) ?>"  onSubmit="check();return document.returnValue">
  <h2>活動花絮--新增 </h2>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#FFFFFF" bordercolordark="#3333FF" bgcolor="#CCFFFF" bordercolor="#33CCFF">
    <tr> 
      <td width="21%" bgcolor="#66CCFF">活動名稱：</td>
      <td width="79%"> 
        <input type="text" name="Iact_name" size="60">
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#66CCFF">活動日期：</td>
      <td width="79%"> 
        <input type="text" name="Iact_date" size="60" value="<?php echo $nday ?>" >
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#66CCFF">簡介：</td>
      <td width="79%"> 
        <textarea name="Iact_info" cols="60" rows="3"></textarea>
      </td>
    </tr>
    <tr> 
      <td width="21%" height="71" bgcolor="#66CCFF">簡介小圖：<br>
        200*200以內</td>
      <td width="79%" height="71"> 
        <input name="Iact_icon" type="file" size="50">
      </td>
    </tr>
    <tr> 
      <td width="21%" height="71" bgcolor="#66CCFF">壓縮檔案：<br>
        <font color="#FF3333">(ZIP格式、大小：2MB以內)</font></td>
      <td width="79%" height="71"> 
        <input name="Iact_zip" type="file" size="50">
        <br>
        <font color="#FF3333" size="-1">多檔案時先把所有檔案壓縮再一次上傳，會自動解開放在該目錄中。(主網頁檔案，不要壓縮)</font> 
      </td>
    </tr>
    <tr>     
    <tr> 
      <td width="21%" bgcolor="#66CCFF">網頁內容檔案：</td>
      <td width="79%">主網頁檔案: 
        <input type="file" name="Iact_index" size="40">
        <ol>
<?php        
        for ($i=1 ; $i<= $upfilenum ; $i++) {
          echo "<li> " ;
          echo "  <input type=\"file\" name=\"Iupload[]\" size=\"45\"> " ;
          echo "</li>  \n" ;
        }  
?>          
        </ol>
        </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#66CCFF">&nbsp;</td>
      <td width="79%"> 
        <input type="Submit" name="Submit" value="新增" >
        <input type="reset" name="Submit2" value="重設">
        <input type="hidden" name="Iuser" value="<?php echo $session_tea_name ?>" >
      </td>
    </tr>
  </table>
</form>

<?php
  //if ($debug)echo " userFullName =$session_tea_name " ;
  foot() ;
  }
?>


