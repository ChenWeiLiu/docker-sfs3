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
  	userdata($nb[userid]) ;		//���o�o�G�̸��
  }	
?>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=big5">
	<title><?php echo "�� $msg_id �����i ($subject)" ?> </title>
	</head>
	<body>
	<?php echo "�@$news_title  �� �� $msg_id �����i" ?><br>
	�i��@���j<?php echo $msg_date . ' ' . $msg_time ?><br>
	�i��@��j<?php echo $group_name ?><br>
	�i�p���H�j<?php echo $user_name ?><br>
	�i�H�@�c�j<?php echo $user_eamil ?><br>
	�i�D�@���j<?php echo $subject ?><br>
	�i���@�e�j<?php echo disphtml($body); ?><br>
	<?php if($attach) { echo "�i���@��j" . disphtml($attach); } ?>
	</center>
	</body>
	</html>
<?


?>