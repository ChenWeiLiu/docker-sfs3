<?php
  // $Id: fixedadmin.php 8163 2014-10-07 15:57:04Z smallduh $
  //  維修通報系統 
  //  林朝敏的半點心工作坊
  //  http://sy3es.tnc.edu.tw/~prolin

  require "config.php" ;


  // 認證檢查
  sfs_check();

  head("維修通報") ;
  print_menu($menu_p);
  //$debug = 1;
  
  $unit_Email =  get_Unit_Email_list() ;
  
  $id=($_GET['id']) ? $_GET['id'] : $_POST['id'];

  $Submit = $_POST['Submit'];    
  $session_tea_name = $_SESSION['session_tea_name'] ;
  $session_log_id = $_SESSION['session_log_id'] ;
  $do = $_GET['do'] ;
  
  if ($Submit) {       //輸入資料，以按鍵文字判斷
      
      $I_selUnit = $_POST['I_selUnit'];    
      $I_even_title = $_POST['I_even_title'];    
      $I_even_doc = $_POST['I_even_doc'];    
      $I_selMode = $_POST['I_selMode'];    
      $I_rep_doc = $_POST['I_rep_doc'];  
      $I_rep_mode = $_POST['I_rep_mode'];  
      $tea_name = addslashes($session_tea_name) ;

      
      switch ($Submit) {
     	case "新增" :
          //把資料寫入
          $sqlstr = "insert into $tbname (ID,even_T,even_doc,unitId,user,userid,even_date,even_mode)
              values ( '0','$I_even_title','$I_even_doc','$I_selUnit', '$tea_name' ,'$session_log_id' ,now(),'$I_selMode') " ;      
              
          $message = " 維修通告內容：\n\n $I_even_doc \n\n報修人：$session_tea_name \n\n 處理：$path_html" 
                 . get_store_path() ." \n\n 本信是自動由維修系統發送，勿直接回覆！ " ;
                 
          if ($unit_Email[$I_selUnit] )  {
          	 $mail_list = $unit_Email[$I_selUnit] ;
             @mail($mail_list, "維修通告：".$I_even_title, $message );      
          }
          break ; 
     	case "修改" :
          //把資料更新
          $sqlstr = "update $tbname set even_T='$I_even_title', even_doc='$I_even_doc' , unitId= '$I_selUnit', even_mode ='$I_selMode' WHERE   id= $id " ;
          break ; 
     	case "回覆" :
          //把資料更新
          $sqlstr = "update $tbname set rep_date=now() ,rep_user='$tea_name', rep_doc='$I_rep_doc' , rep_mode= '$I_rep_mode' WHERE   id= $id " ;
          break ;                     
     	case "刪除" :
          $sqlstr = "delete FROM  $tbname WHERE   id= $id " ; 
          break ; 
     }
     if ($debug) echo "sqlstr = $sqlstr" ;
     $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
     
     //若為回覆，檢查教師是否有設定 E-mail , 以便回覆
     if ($Submit=="回覆") { 
       $sql="select * from $tbname where id=$id";
       
       $res = $CONN->Execute($sql);
       $row=$res->FetchRow();
       $teach_id=$row['userid'];
       $I_even_title=$row['even_T'];
       $I_even_doc=$row['even_doc'];
       $user=$row['user'];
       
       $Teacher_Email=get_teacher_email_by_id($teach_id);
       if ($Teacher_Email!="") {
          $message = " 維修通告內容：\n\n $I_even_doc \n\n報修人：$user \n\n 回覆內容：$I_rep_doc \n\n 處理狀況：".$checkmode[$I_rep_mode]." \n\n 回覆者：$tea_name"." \n\n 本信是自動由維修系統發送，勿直接回覆！ ";
          @mail($Teacher_Email, "回覆維修通告：".$I_even_title, $message );
          $if_email="已嘗試透過 E-mail 回覆通報教師.<br>";
          
       } // end if	     
	   }// end if
       
     echo $if_email."完成，兩秒後，轉回主畫面！" ;
     redir("fixed.php",1) ;
      
     exit;                
   }  
   


   if (isset($id)) {
     //讀取資料
     $sqlstr = "SELECT * FROM $tbname  WHERE   ID= $id " ;
     $result =  $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
     if ($result){
  	   $nb=$result->FetchRow() ;
  	   $even_T = $nb[even_T];		//標題
  	   $even_doc = $nb[even_doc];		//事由
           $unitId = $nb[unitId];		//通知單位代碼
           $unitname = $unitstr[$unitId] ;	//通知單位中文名

           $user = $nb[user];			//填報者
           $userid = $nb[userid];			//填報者
           $even_date = $nb[even_date];		//填報日期
           $even_mode = $nb[even_mode] ;	//事情嚴重度-數字
           $even_modestr = $evenmode[$even_mode] ; //嚴重度-文字
           $rep_doc = $nb[rep_doc];		//回覆內容
           $rep_mode = $nb[rep_mode];		//修復情形
     }      
     else {
       echo "查無此資料！" ;
       redir("fixed.php",1) ;
       exit ;
     }  
  }


  if ($do=="edit") { //要同一人才可以
    //if ($userid != "$session_log_id") {
    if ( (strnatcasecmp ($userid, $session_log_id) ) and (!board_checkid($unitId)) ) {
       echo "非填表本人或該單位成員，無權修改！" ;
       redir("fixed.php",1) ;
       exit ;
    }      	
  }  
  if ($do=="reply") { //要有該組身份
    if(!board_checkid($unitId)){
       echo "非該單位成員，無權回覆！" ;
       redir("fixed.php",1) ;
       exit ;
    }   
  }   
  if ($rep_mode == 2) { //已修復設備，不可刪修
        echo "設備已修復，不可更動" ;
       redir("fixed.php",1) ;
       exit ;       
  }   
  
  
  
?>
<html>
<head>
<title>維修通知管理畫面</title>
<script language="JavaScript">

function chk_empty(item) {
   if (item.value=="") { return true; } 
}

function check( mode ) { 
   var errors='' ;
   
   if (mode==1) {
     if (chk_empty(document.myform.I_even_title) || chk_empty(document.myform.I_even_doc) )  
        errors = '主旨、詳細描述部份不可以空白！' ; 
   }     
   else 
     if (chk_empty(document.myform.I_rep_doc))  
        errors = '回覆內容部份不可以空白！' ;    
   
   if (errors) alert (errors) ;
   document.returnValue = (errors == '');
 
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>

<body bgcolor="#FFFFFF">


<?php
//----------------------------------------------------------------------------
//新增一筆
if (!isset($id)) {
   // 身份認証	
?>  	
 <form name="myform" method="post" action="<?php echo basename($PHP_SELF)?>" onSubmit="check(1);return document.returnValue">  
  <h1>報修記錄-新增</h1>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#333333" bordercolordark="#FFFFFF">
    <tr> 
      <td width="21%" bgcolor="#CCCCFF">通知單位：</td>
      <td width="79%" bgcolor="#FFFFCC"> 
        <select name="I_selUnit">
          <?php 
            //顯示單位

            foreach( $unitstr as $key => $value) {
               echo "<option value='$key'>$value</option> \n"  ;
            }          
           ?> 
        </select>
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#CCCCFF">事由主旨：</td>
      <td width="79%" height="26" bgcolor="#FFFFCC"> 
        <input type="text" name="I_even_title" size="60" maxlength="255">
      </td>
    </tr>
    <tr> 
      <td width="21%" height="26" bgcolor="#CCCCFF">詳細描述：</td>
      <td width="79%" bgcolor="#FFFFCC"> 內容描述請填寫詳細！ 
        <textarea name="I_even_doc" rows="5" cols="60">地點：
        	
詳細情形：</textarea>
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#CCCCFF">嚴重程度：</td>
      <td width="79%" bgcolor="#FFFFCC"> 
        <select name="I_selMode">
          <?php 
            foreach( $evenmode as $key => $value) {
                 echo "<option value='$key'>$value</option>" ; 
            }  
          ?>         
        </select>
      </td>
    </tr>
  </table>
  <p> 
    <input type="submit" name="Submit" value="新增">
    <input type="reset" name="Submit2" value="重設">
  </p>
 </form>  
<?php 
}
//-------------------------------------------------------------------------  
//編修
if ($do=="edit") {
?>
 <form name="myform" method="post" action="<?php echo basename($PHP_SELF)?>" onSubmit="check(1);return document.returnValue">  
  <h1>報修記錄-修改</h1>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#666666" bordercolordark="#FFFFFF">
    <tr> 
      <td width="21%" bgcolor="#CCCCFF">通知單位：</td>
      <td width="79%" bgcolor="#FFFFCC"> 
        <select name="I_selUnit">
          <?php 
            foreach( $unitstr as $key => $value) {
               $chkstr = ($key==$unitId) ? "selected" : "" ;

               echo "<option value='$key' $chkstr>$value</option> \n"  ;
            }           

           ?>         
        </select>
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#CCCCFF">事由主旨：</td>
      <td width="79%" height="26" bgcolor="#FFFFCC"> 
        <input type="text" name="I_even_title" size="60" value="<?php echo $even_T ; ?>">
      </td>
    </tr>
    <tr> 
      <td width="21%" height="26" bgcolor="#CCCCFF">詳細描述：</td>
      <td width="79%" bgcolor="#FFFFCC"> 
        <textarea name="I_even_doc" rows="5" cols="60"><?php echo $even_doc ; ?></textarea>
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#CCCCFF">嚴重程度：</td>
      <td width="79%" bgcolor="#FFFFCC"> 
        <select name="I_selMode">
          <?php 

            foreach( $evenmode as $key => $value) {
                $chkstr = ($key==$even_mode) ? "selected" : "" ;
                 echo "<option value='$key' $chkstr >$value</option>" ; 
            }              
          ?>         
        </select>
        <input type="hidden" name="id" value="<?php echo $id ?>">
      </td>
    </tr>
  </table>
  <p> 
    <input type="submit" name="Submit" value="修改">
    <input type="submit" name="Submit" value="刪除">
  </p>
 </form>  


<?php  
}

//-------------------------------------------------------------------------------
//回覆
if ($do=="reply"){
?>  
<form name="myform" method="post" action="<?php echo basename($PHP_SELF)?>" onSubmit="check(2);return document.returnValue">  
  <h1>報修記錄-維修單位回覆</h1>
  <table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#333333" bordercolordark="#FFFFFF">
    <tr>
      <td width="21%" bgcolor="#CCCCFF">事由主旨：</td>
      <td width="79%" bgcolor="#FFFFCC"> 
        <?php echo $even_T ; ?>
      </td>
    </tr>
    <tr>
      <td width="21%" height="26" bgcolor="#CCCCFF">詳細描述：</td>
      <td width="79%" height="26" bgcolor="#FFFFCC">
        <?php echo nl2br($even_doc) ; ?>
      </td>
    </tr>
    <tr>
      <td width="21%" bgcolor="#CCCCFF">嚴重程度：</td>
      <td width="79%" bgcolor="#FFFFCC">
        <?php echo $even_modestr ; ?>
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#CCCCFF">回覆內容：</td>
      <td width="79%" bgcolor="#FFFFCC"> 
        <textarea name="I_rep_doc" rows="5" cols="60"><?php echo $rep_doc ;  ?></textarea>
      </td>
    </tr>
    <tr> 
      <td width="21%" bgcolor="#CCCCFF">處理狀況：</td>
      <td width="79%" bgcolor="#FFFFCC"> 
        <select name="I_rep_mode">
          <?php 
            $ni = count($checkmode) ; 
            for ($i=0 ;$i<$ni;$i++) {
              if ($i == $rep_mode) echo '<option value="' . $i. '" selected>' . $checkmode[$i] . '</option>' ;
              else echo '<option value="' . $i. '">' . $checkmode[$i] . '</option>' ; 
            }  
          ?> 
        </select>
        <input type="hidden" name="id" value="<?php echo $id ?>">
      </td>
    </tr>
  </table>
  <p> 
    <input type="submit" name="Submit" value="回覆">
  </p>
 </form>
<?php 
}  
foot();
?> 

</body>
</html>
