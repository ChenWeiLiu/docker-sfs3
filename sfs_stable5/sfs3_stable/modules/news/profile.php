<?
  //$Id: profile.php 8952 2016-08-29 02:23:59Z infodaes $
  include "config.php";
  
  $msg_id = intval($_GET['msg_id']) ;
  
  $tsqlstr =  " SELECT * FROM $tbname where msg_id = $msg_id " ; 
  $result = $CONN->Execute( $tsqlstr) ;   
  if($result) {
  	$nb= $result->FetchRow()   ;	
  	$subject = $nb[msg_subject] ;
  	$msg_date= $nb[msg_date] ;
  	$body= $nb[msg_body] ;
  	$attach = $nb[attach];
  	userdata($nb[userid]) ;		//取得發佈者資料
  }	
?>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=big5">
	<title><?php echo "第 $msg_id 號公告 ($subject)" ?> </title>
	</head>
	<body>
	<?php echo "　$news_title  － 第 $msg_id 號公告" ?><br>
	【日　期】<?php echo $msg_date . ' ' . $msg_time ?><br>
	【單　位】<?php echo $group_name ?><br>
	【聯絡人】<?php echo $user_name ?><br>
	【信　箱】<?php echo $user_eamil ?><br>
	【主　旨】<?php echo $subject ?><br>
	【內　容】<?php echo disphtml($body); ?><br>
	<?php if($attach) { echo "【附　件】" . disphtml($attach); } ?>
	</center>
	</body>
	</html>
<?


?>