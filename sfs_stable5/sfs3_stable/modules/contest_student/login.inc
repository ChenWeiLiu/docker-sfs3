<?php
//login ���P�_��
if ($_POST['act']=='login') {
 $query="select * from contest_user where tsn='".$_POST['tsn']."' and student_sn='".$_SESSION['session_tea_sn']."' and ifgroup=''";
 $result=mysql_query($query);
 if ($STUDENT=mysql_fetch_array($result,1)) {
 	$TEST=get_test_setup($_POST['tsn']);
 		if ($_POST['password']==$TEST['password']) {
 			//�n�J���\
  		$_POST['act']="Start";
  		$_POST['option1']=$_POST['tsn'];  //�v�ɪ� tsn
  		//�^�g�n�J�O��
  		$L=$STUDENT['logintimes']+1;
  		$query="update contest_user set lastlogin='".date("Y-m-d H:i:s")."',logintimes='$L' where tsn='".$_POST['tsn']."' and student_sn='".$_SESSION['session_tea_sn']."'"; 
  		mysql_query($query);
  	} else {
    	$INFO="�v�ɱK�X���~!!";
    	$_POST['act']="";
  	}
  } else {
    $INFO="�A�å��ѥ[�������v��, �ΧA�ä��O�ζ������ժ�(�����\�n�J), �Э��s���! ";
    $_POST['act']="";
  } 
  
}// end if act=login
?>