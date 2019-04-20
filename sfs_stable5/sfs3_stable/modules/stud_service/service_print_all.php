<html>
	<head>
		<title>列印服務學習全學期明細</title>
	</head>
<body>
<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//目前選定學期
//$c_curr_seme=$_GET['c_curr_seme'];
//目前選定班級
//$c_curr_class=$_GET['c_curr_class'];

//目前選定學期
$c_curr_seme=$_POST['c_curr_seme'];
//目前選定班級
$c_curr_class=$_POST['c_curr_class'];

	$classid=class_id_2_old($c_curr_class);
	
// if ($_GET['list_class_all']!="") {
// 	list_class_all($_GET['list_class_all'],$c_curr_seme,$classid[5]);
//  }	

  //列出勾選 	  
  foreach ($_POST['STUD'] as $student_sn=>$seme_num) {
     		list_service_all($student_sn,$classid[5]);
  }

?>
</body>
</html>