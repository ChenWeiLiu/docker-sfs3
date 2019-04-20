<?php
  // $Id: news_admin.php 8952 2016-08-29 02:23:59Z infodaes $
  include "config.php" ;

  
  //$debug = 1;

  // --認證 session 
  sfs_check();
  
  $session_log_id = $_SESSION['session_log_id'] ;
  
  $do = $_GET['do'] ;
  $msg_id = $_GET['msg_id'] ;    
  $Submit = $_POST['Submit'] ;
 
    
  if ($do=="exit") {
    header("Location:news.php" ) ; 
    exit ;
  }  

 

  
//新增---------------------------------------------------------  
  if ($Submit == "張貼" ) {
     $nday = date("Y-m-d") ;
     
     $subject = $_POST['subject'] ;
     $msg_body = $_POST['msg_body'] ;
     $txtURL = $_POST['txtURL'] ;
     $chkTop = $_POST['chkTop'] ;


     $end_date = $_POST['end_date'] ;
     $inSchool = $_POST['inSchool'] ;
     //最長置頂日期，跑馬燈不限
     $exp_day = GetdayAdd($nday , $topdays) ;
     
     if (($chkTop==1) and ($end_date >  $exp_day) )
        $end_date =  $exp_day ;
        
     $filename= "" ;
     
     if (is_uploaded_file($_FILES['attach']['tmp_name'])) {
     	//上傳檔案

        //建立目錄(以建立日期)
        $dirstr = "$nday" ;
        $filename= $dirstr . $_FILES['attach']['name']  ;        

        move_uploaded_file($_FILES['attach']['tmp_name'], $savepath .$filename);
     }
     
     
     if (!$chkTop)  
        $end_date = "null" ;
     else 
        $end_date ="'$end_date'" ;   
        
     userdata($session_log_id);
     

     $sqlstr = "insert into $tbname (userid,poster_name , poster_job, msg_id,msg_subject,msg_body,msg_date,attach ,inschool , url ,TopNews ,msg_date_expire )
        values ( '$session_log_id','$user_name','$group_name' ,'0', '$subject ', '$msg_body' ,now(), '$filename' ,'$inSchool', '$txtURL' ,'$chkTop' , $end_date ) " ;

     //echo  $sqlstr ;
     $result =  $CONN->Execute( $sqlstr) ;      

     redir("news.php" ,1) ; 
     echo "新增一筆完成！" ;
     exit ; 
  }
  	
//更新---------------------------------------------------------    	
  if ($Submit == "更新" ) {
  	
     $subject = $_POST['subject'] ;
     $msg_body = $_POST['msg_body'] ;
     $txtURL = $_POST['txtURL'] ;
     $chkTop = $_POST['chkTop'] ;

     $end_date = $_POST['end_date'] ;
     $inSchool = $_POST['inSchool'] ;
     $oldattach = $_POST['oldattach'] ;
     
     $nday = date("Y-m-d") ;     
     $dirstr = "$nday" ;
     
     //最長置頂日期，跑馬燈不限
     $exp_day = GetdayAdd($nday , $topdays) ;
     
     if (($chkTop==1) and ($end_date >  $exp_day) )
        $end_date =  $exp_day ;     
     
     if (is_uploaded_file($_FILES['attach']['tmp_name'])) {
     	//上傳檔案
     	
     	//刪除舊檔
        if ($oldattach) unlink($savepath . $oldattach);  
         
        //建立目錄(以建立日期)
        $dirstr = "$nday" ;
        $filename= $dirstr . $_FILES['attach']['name']  ;        

        move_uploaded_file($_FILES['attach']['tmp_name'], $savepath .$filename);
     }
     else 
       if ($oldattach) $filename = $oldattach ;
       
     if (!$chkTop)  
        $end_date =  "null" ;
     else 
        $end_date ="'$end_date'" ;   
     
     $sqlstr = "update  $tbname set msg_subject='$subject ' ,msg_body='$msg_body', attach='$filename' , inschool= '$inSchool' ,
                url= '$txtURL' ,TopNews = '$chkTop' , msg_date_expire = $end_date 
                where msg_id = '$_POST[msg_id]' " ;
     
     if($debug) echo "$sqlstr <br>" ;
     $result =  $CONN->Execute( $sqlstr) ;      
 

     redir("news.php" ,1) ; 
     echo "更新一筆完成！" ;
     exit ; 
  }
  
//確定刪除---------------------------------------------------------      
  if ($do == "del2" ) {
      $attch = $_GET['attch'] ;	
      
      $sqlstr = "select * from $tbname where msg_id='$msg_id' " ;  	
      $result =  $CONN->Execute( $sqlstr) ; 	
      $nb=$result->FetchRow() ;  	

      if ($nb[userid]!=$session_log_id ) {
      	 redir("news.php" ,1) ; 
      	 echo "你非原公佈者，無權修改本篇文章！" ;
      	 exit ;
      }	    
        	
     if ($debug) echo  $savepath . $attch ;
     
     if ($attch) unlink($savepath . $attch) ;

     $sqlstr = "delete  from $tbname  where msg_id = $msg_id " ;
 
     if($debug) echo "$sqlstr <br>" ;
     $result =  $CONN->Execute( $sqlstr) ;      
     redir("news.php" ,1) ; 
     echo "刪除動作完成！" ;
     exit ; 
  }  
  
  head("最新消息管理") ;
?>
<link href="sample.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../javascripts/FCKeditor/fckeditor.js"></script>
<script language="JavaScript">

function chk_empty(item) {
   if (item.value=="") { return true; } 
}

function check() { 
   var errors='' ;
   
   if (chk_empty(document.myform.subject) )  {
      errors = '主旨不可為空白！' ; }
   else {
     if (chk_empty(document.myform.msg_body))	
       errors = '內容不可以空白！' ;
   }
   if (errors) alert (errors) ;
   document.returnValue = (errors == '');
 
}

</script>

</head>

<body bgcolor="#FFFFFF">
<?php 
  //-----------------------------------------編修 ---------------------- 
  if ($do=="edit") {
    $sqlstr = "select * from $tbname where msg_id='$msg_id' " ;
    $result =  $CONN->Execute( $sqlstr) ; 	
    $nb=$result->FetchRow() ;
    
    if ($nb[userid]!=$session_log_id ) {
    	 redir("news.php" ,1) ; 
      	 echo "你非原公佈者，無權修改本篇文章！" ;
      	 exit ;
    }
    $inSchool = $nb["inSchool"] ;
    $TopNews = $nb["TopNews"] ;
    $endday =$nb["msg_date_expire"] ; 
    $msg_body =$nb[msg_body] ;
    //判斷有無 <br> 
    $msg_body= disphtml($msg_body) ;
    $msg_body = str_replace("\r\n","",$msg_body) ;
    $msg_body =addslashes($msg_body ) ;
    

?> 
<form ENCTYPE="multipart/form-data" method="post" action="<?php echo basename($PHP_SELF) ?>" name="myform">
  <h2>最新消息--修改 </h2>
  <table border="1" width="100%" cellspacing="1" cellpadding="" bgcolor="#FFFFFF">
    <tr> 
      <th width="9%" align="right" bgcolor="#DDDDDD" valign="middle">主旨： </th>
      <td  bgcolor="#DDDDDD" valign="middle"> 
        <input type="text" name="subject" size="60" maxlength="40" value="<?php echo $nb[msg_subject] ?>">
      </td>
    </tr>
    <tr> 
      <th colspan="2" align="center" bgcolor="#D8E9FE" valign="middle">內容 </th>
    </tr>
    <tr bgcolor="#DDDDDD"> 
      <td colspan="2" valign="middle"> 
        <div align="center"> 
		<script type="text/javascript">
<!--
// Automatically calculates the editor base path based on the _samples directory.
// This is usefull only for these samples. A real application should use something like this:
// oFCKeditor.BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
//var sBasePath = document.location.pathname.substring(0,document.location.pathname.lastIndexOf('_samples')) ;

var oFCKeditor = new FCKeditor( 'msg_body' ) ;
oFCKeditor.BasePath	= '../../javascripts/FCKeditor/' ;
oFCKeditor.Height	= 300 ;
oFCKeditor.Value	= '<?php echo $msg_body ?>' ;
oFCKeditor.Create() ;
//-->
		</script>
		            
          <center>

            <input type="hidden" name="oldattach" value="<?php echo $nb[attach] ?>">
            <input type="hidden" name="msg_id" value="<?php echo $nb[msg_id]  ?>">
          </center>
        </div>
      </td>
    </tr>
    <tr> 
      <td colspan="2" valign="middle" bgcolor="#DDDDDD"> 
        <div align="center"> 
          <p>附件： 
            <input name="attach" type="file" size="40" maxlength="30">
        </div>
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="center" valign="middle" bgcolor="#DDDDDD">相關網址： 
        <input type="text" name="txtURL" size="50" value="<?php echo $nb[url]  ?>">
      </td>
    </tr>
    <tr> 

      <td colspan="2" align="center" valign="middle" bgcolor="#DDDDDD"> 
        <input type="checkbox" name="inSchool" value="1" <?php if ($inSchool) echo "checked" ?>>
        只對校內公佈(在校外無法讀取) <br> 
         <input type="radio" name="chkTop" value="1" <?php if ($TopNews==1) echo "checked" ?> >
        重要訊息置頂        
        <input type="radio" name="chkTop" value="2" <?php if ($TopNews==2) echo "checked" ?>  >
        網頁跑馬燈   ，
        有效日期:       
        <input type="text" name="end_date" size="12" value="<?php echo $endday  ?>">
        <font color='red'>(置頂期限不得超過<?php echo $topdays ?>日，網頁跑馬燈不限制！)</font>

        </td>
    </tr>
    <tr> 
      <td colspan="2" align="center" valign="middle" bgcolor="#DDDDDD"> 
        <table border="0" width="100%" cellspacing="0" cellpadding="5">
          <tr> 
            <td width="100%"> 
              <div align="center"> 
                <input type="submit" value="更新" name="Submit">
                <input type="reset" value="清除" name="reset2">
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>  	
  	
  	
<?php 
  }
  //刪除------------------------------------------------------------------
  else if ($do == "delete") {
      $sqlstr = "select * from $tbname where msg_id='$msg_id' " ;  	
      $result =  $CONN->Execute( $sqlstr) ; 	
      $nb=$result->FetchRow() ;  	
      
      if ($nb[userid]!=$session_log_id ) {
      	 redir("news.php" ,1) ; 
      	 echo "你非原公佈者，無權修改本篇文章！" ;
      	 exit ;
      }	       
?>  	

<table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#FFFFFF" bordercolordark="#3333FF">
  <tr>
    <td> 
      <h2>最新消息--刪除 </h2>
      <p>確定刪除第<?php echo $id. "筆: $nb[msg_subject]"  ?> &nbsp; <a href="<?php echo basename($PHP_SELF) . '?do=del2&msg_id=' . $msg_id .'&attch=' . $nb[attach] ?>">是</a> 
        &nbsp;&nbsp; <a href="<?php echo basename($PHP_SELF) . '?do=exit' ?>">否</a> </p>
    </td>
  </tr>
</table>  	

<?php  
}	

  else {	
  //---------------------------------------新增	
  $endday = date("Y-m-") ;
?>  	
  	
  	
<form ENCTYPE="multipart/form-data" method="post" action="<?php echo basename($PHP_SELF) ?>" name="myform" onSubmit="check();return document.returnValue">
  <h2>最新消息--新增 </h2>
  <table border="1" width="100%" cellspacing="1" cellpadding="" bgcolor="#FFFFFF">
    <tr> 
      <th width="11%" align="right" bgcolor="#DDDDDD" valign="middle">主旨： </th>
      <td  bgcolor="#DDDDDD" valign="middle"> 
        <input type="text" name="subject" size="60"  maxlength="40">
      </td>
    </tr>
    <tr> 
      <th colspan="2" align="center" bgcolor="#D8E9FE" valign="middle">內容 </th>
    </tr>
    <tr> 
      <td colspan="2" valign="middle" bgcolor="#DDDDDD"> 
        <div align="center"><font color="#3333FF">
		<script type="text/javascript">
<!--
// Automatically calculates the editor base path based on the _samples directory.
// This is usefull only for these samples. A real application should use something like this:
// oFCKeditor.BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
//var sBasePath = document.location.pathname.substring(0,document.location.pathname.lastIndexOf('_samples')) ;

var oFCKeditor = new FCKeditor( 'msg_body' ) ;
oFCKeditor.BasePath	= '../../javascripts/FCKeditor/' ;
oFCKeditor.Height	= 300 ;
oFCKeditor.Value	= '' ;
oFCKeditor.Create() ;
//-->
		</script>
        </div>
      </td>
    </tr>
    <tr> 
      <td colspan="2" valign="middle" bgcolor="#DDDDDD"> 
        <div align="center"> 附件： 
          <input name="attach" type="file" size="40" maxlength="30"><br>
          <font color='red'>(請儘量以公開格式檔案如html、jpg等格式公佈！)</font>
        </div>
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="center" valign="middle" bgcolor="#DDDDDD">相關網址： 
        <input type="text" name="txtURL" size="50" value="http://">
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="left" valign="middle" bgcolor="#DDDDDD"> 
        <input type="checkbox" name="inSchool" value="1">
        只對校內公佈(在校外無法讀取) <br>
         <input type="radio" name="chkTop" value="1">
        重要訊息置頂        
        <input type="radio" name="chkTop" value="2"  >
        網頁跑馬燈   ，
        有效日期:       
        <input type="text" name="end_date" size="12" value="<?php echo $endday  ?>">
        <font color='red'>(置頂期限不得超過<?php echo $topdays ?>日，網頁跑馬燈不限制！)</font>
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="center" valign="middle" bgcolor="#DDDDDD"> 
        <table border="0" width="100%" cellspacing="0" cellpadding="5">
          <tr> 
            <td width="100%"> 
              <div align="center"> 
                <input type="submit" value="張貼" name="Submit">
                <input type="reset" value="清除" name="reset">
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>

<?php

  }
  foot() ;
?>


