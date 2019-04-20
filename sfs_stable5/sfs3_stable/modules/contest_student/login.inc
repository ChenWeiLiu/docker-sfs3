<?php
//login 的判斷式
if ($_POST['act']=='login') {
 $query="select * from contest_user where tsn='".$_POST['tsn']."' and student_sn='".$_SESSION['session_tea_sn']."' and ifgroup=''";
 $result=mysql_query($query);
 if ($STUDENT=mysql_fetch_array($result,1)) {
 	$TEST=get_test_setup($_POST['tsn']);
 		if ($_POST['password']==$TEST['password']) {
 			//登入成功
  		$_POST['act']="Start";
  		$_POST['option1']=$_POST['tsn'];  //競賽的 tsn
  		//回寫登入記錄
  		$L=$STUDENT['logintimes']+1;
  		$query="update contest_user set lastlogin='".date("Y-m-d H:i:s")."',logintimes='$L' where tsn='".$_POST['tsn']."' and student_sn='".$_SESSION['session_tea_sn']."'"; 
  		mysql_query($query);
  	} else {
    	$INFO="競賽密碼錯誤!!";
    	$_POST['act']="";
  	}
  } else {
    $INFO="你並未參加本次的競賽, 或你並不是團隊中的組長(不允許登入), 請重新選擇! ";
    $_POST['act']="";
  } 
  
}// end if act=login
?>