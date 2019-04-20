<?php
  // $Id: fixedview.php 8675 2015-12-25 02:46:45Z qfon $
  //  維修通報系統 
  //  林朝敏的半點心工作坊
  //  http://sy3es.tnc.edu.tw/~prolin
  require "config.php" ;

  $id = intval($_GET['id']) ;
  //$debug = 1;
  
  if (!$id)  {  //未指定編號
   header("Location:fixed.php" ) ; 
   exit ;
  }
  
  //讀取資料
  $sqlstr = "SELECT * FROM $tbname where id = $id " ;

  if ($debug) echo $sqlstr ;

  $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 

  	   $nb= $result->FetchRow() ;
  	   $even_T = $nb[even_T];		//標題
  	   $even_doc = nl2br($nb[even_doc]);		//事由
           $unitId = $nb[unitId];		//通知單位代碼
           $unitname = $unitstr[$unitId] ;	//通知單位中文名
           //$unitchk = $unitcheck[$unitId] ;	//單位群組判斷
           			
           $user = $nb[user];                   //填報者
           $even_date = $nb[even_date];		//填報日期
           $even_mode = $nb[even_mode] ;	//事情嚴重度-數字
           $even_modestr = $evenmode[$even_mode] ; //嚴重度-文字
           $rep_doc =nl2br($nb[rep_doc]);		//回覆內容
           $rep_mode = $nb[rep_mode];		//修復情形
           $rep_mode_str = $checkmode[$rep_mode] ;
           $rep_date = $nb[rep_date] ;
           $rep_user = $nb[rep_user] ;

  head("維修通報") ;
  print_menu($menu_p); 
?>
<html>
<head>
<title>維修通知單</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>

<body bgcolor="#FFFFFF">
<h1 align="center">維修通知單</h1>
<table width="95%" border="1" cellspacing="0" cellpadding="4" bordercolorlight="#666666" bordercolordark="#FFFFFF" align="center">
  <tr bgcolor="#CCCCFF"> 
    <td width="20%">
     <?php 
      //編號及圖示
      echo "<img src='$mode_image[$even_mode]'> 編號：$id \n" ;
      
     ?>
    </td>
    <td width="60%"><?php echo $even_T ?></td>
    <td width="20%">嚴重等級：<?php echo $even_modestr ?></td>
  </tr>
  <tr bgcolor="#CCCCFF"> 
    <td >描述：</td>
    <td ><?php echo $even_doc ?>&nbsp;</td>
    <td >&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCFF"> 
    <td colspan="2"> 
      填報日期：<?php echo $even_date ?>
    </td>
    <td >填報人：<?php echo $user ?></td>
  </tr>
  <tr bgcolor="#FFCCCC"> 
    <td ><?php echo "  <img src='$chk_image[$rep_mode]' >" . $rep_mode_str  ;    ?></td>
    <td >&nbsp;</td>
    <td >負責單位：<?php echo $unitname ?></td>
  </tr>
  <tr bgcolor="#FFCCCC">
    <td>回覆內容：</td>
    <td><?php echo $rep_doc ?>&nbsp; </td>
    <td>&nbsp;</td>
  </tr>
  <tr bgcolor="#FFCCCC"> 
    <td colspan="2"> 
      回覆日期：<?php echo $rep_date ?>
    </td>
    <td >回覆者：<?php echo $rep_user ?></td>
  </tr>
</table>
<p align="center"><a href="javascript:history.go(-1);">回上一頁</a></p>
<?php foot(); ?>
</body>
</html>
