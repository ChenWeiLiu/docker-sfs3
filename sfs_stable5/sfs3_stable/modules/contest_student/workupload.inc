<?php
// �� ct_painting.php , ct_animation.php , ct_impress.php include���{���X
//POST �e�X��,�D�{���ާ@�}�l ====================================================================
//�n�J, �ˬd���v�ɦ��L���ͦW��, �Y��, �}�l, �Y�L, ����
include_once('login.inc');
/***
if ($_POST['act']=='login') {
 $query="select * from contest_user where tsn='".$_POST['tsn']."' and student_sn='".$_SESSION['session_tea_sn']."' and ifgroup=''";
 $result=mysql_query($query);
 if ($STUDENT=mysql_fetch_array($result,1)) {
 	//�n�J���\
  $_POST['act']="Start";
  $_POST['option1']=$_POST['tsn'];  //�v�ɪ� tsn
  //�^�g�n�J�O��
  $L=$STUDENT['logintimes']+1;
  $query="update contest_user set lastlogin='".date("Y-m-d H:i:s")."',logintimes='$L' where tsn='".$_POST['tsn']."' and student_sn='".$_SESSION['session_tea_sn']."'"; 
  mysql_query($query);
  } else {
    $INFO="�A�å��ѥ[�������v��, �ΧA�ä��O�ζ������ժ�, �Э��s���!";
    $_POST['act']="";
  } 
  
}// end if act=login
***/

//�ɮפW�� table: test_record2 , �x�s�� mode �אּ Start
if (@$_POST['act']=='Upload') {
 //����@���O�_�O��
 $TEST=get_test_setup($_POST['option1']);
 $NOWsec=NowAllSec(date("Y-m-d H:i:s"));
 $StartTest=NowAllSec($TEST['sttime']);
 $EndTest=NowAllSec($TEST['endtime']);
 //��30���w�Ĵ���, �קK�����먮
 if ($NOWsec<$EndTest+30) {
	//�ˬd�O�_���t�ɮ�, ���A�B�z
  if ($_FILES['thefile']['name']!="") {
      //�ˬd��Ƨ��O�_���}
      if (!is_dir($UPLOAD_P[$ACTIVE]) && !is_link($UPLOAD_P[$ACTIVE])) {
          if (!is_file($UPLOAD_P[$ACTIVE])) {
              mkdir($UPLOAD_P[$ACTIVE]);
          } else {
              echo "�]�����P�W�ɮ�...��Ƨ��إߥ��� !";
              exit();
          }
      }


      //�ˬd�O�_���_�W��, �Y�O, �h���A insert
  	  $query="select * from contest_record2 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
  	  $result=mysql_query($query);
  	  $D=mysql_num_rows($result);
  	  if ($D>0) {
  	   $F=mysql_fetch_array($result,1);
  	   unlink ($UPLOAD_P[$ACTIVE].$F['filename']);
  	   	if ($TEST['active']==2) {
  	   		$a=explode(".",$F['filename']);
  	   		$filename_s=$a[0]."_s.".$a[1];
  	   		unlink ($UPLOAD_P[$ACTIVE].$filename_s);
  	    } // end if active=2
  	  }
      //������ɦW
      $expand_name=explode(".",$_FILES['thefile']['name']);
      $nn=count($expand_name)-1;
      $ATTR=strtolower($expand_name[$nn]); //��p�g���ɦW
   	  
      //�˴��O�_���\�W�Ǧ������ɮ�
      if (check_file_attr($ATTR)) { 

      //�s�ɦW 
      $filename_1=$_POST['option1']."_".$_SESSION['session_tea_sn'].floor(rand(1000,9999)); //�᭱�[�|�X�ü�, �קK�M���ɭ���, �ɭP���Ǯɤ��|�ݨ��¹�,cache���D
      $filename=$filename_1.".".$ATTR;
       if (copy($_FILES['thefile']['tmp_name'],$UPLOAD_P[$ACTIVE].$filename)) {
       	 //�s�y�Y���� (�R�Aø��)
       	 if ($TEST['active']==2) {
       	  $filename_s=$filename_1."_s.".$ATTR;
       	  if (!ImageResize($UPLOAD_P[2].$filename, $UPLOAD_P[2].$filename_s, 256, 192, 100)) {
       	   echo "ErroR!";
       	   exit();
       	  }       	  
       	 }
       	
      	  if ($D==0) {
           $query="insert into contest_record2 (tsn,student_sn,filename,anstime) values ('".$_POST['option1']."','".$_SESSION['session_tea_sn']."','".$filename."','".date('Y-m-d H:i:s')."')";
          } else {
           $query="update contest_record2 set filename='".$filename."',anstime='".date('Y-m-d H:i:s')."' where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
          }// end if $D==0
         if (!mysql_query($query)) {
           echo "Error! Query=".$query;
           exit();
         }
        $_POST['act']="Start";
      
       }else{
        echo "�ɮפW�ǥ���, ���p���ʦҦѮv!";
        exit();
       } // end if copy
      } else {
       $INFO="�W�ǥ���! �Ъ`�N���ɤW������!";
       $_POST['act']='Start';
      }// end if check_file_attr
  }// end if files exist
 } 
} // end if act=upload

//�ɭ��e�{�}�l, �����]�b <form>�� , act�ʧ@ , option1, option2 �Ѽ�2�� return��^�e�@�Ӱʧ@=============================
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>"  enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
 <input type="hidden" name="return" value="<?php echo $_POST['act'];?>">

<?php
//���n�J
 if ($_POST['act']=='') {
   stud_login($ACTIVE,$INFO);
 }

//�n�J���\, �}�l
if ($_POST['act']=='Start') {
 
 //���o�v�ɳ]�w 
 $TEST=get_test_setup($_POST['option1']);				//�v�ɳ]�w

 $STUD=get_student($_SESSION['session_tea_sn']);  //�ǥ͸��
 
 $NOWsec=NowAllSec(date("Y-m-d H:i:s"));
 $StartTest=NowAllSec($TEST['sttime']);
 $EndTest=NowAllSec($TEST['endtime']);

 //���X�v�ɸ��, �Y�٨S�}�l
 if ($NOWsec<$StartTest) {
  //�v�ɩ|���}�l�A���ܦh�[�Y�N�i�� ********************
  $LeaveTime=$StartTest-$NOWsec;
   ?>
   <table border="0" width="100%">
    <tr>
   	 <td style="color:#0000FF">
   	 	�n�J�ǥ͡G<?php echo $STUD['class_name']." ".$STUD['seme_num']."�� ".$STUD['stud_name'];?>�A�Z�����ɶ}�l�٦�<input type="text" name="time" size="6">�A�Ъ`�N�I      	 	
   	 	</td> 
    </tr>
   </table>
   <?php
   test_main($_POST['option1'],0);
   ?>
 <Script language="JavaScript">
	//�����ܼ�
	var ACT="Start";
	//���ɩ|���i��A���ܨ�ƭp�ɫ�}�l
	var inttestsec=<?php echo $LeaveTime;?>;
 
  checkLeaveTime();

</Script>
   
   <?php
  //���ɶi�椤
 } elseif ($NOWsec>=$StartTest and $NOWsec<$EndTest) {
 //�v�ɤw�}�l *****************************************
 //�ӥͤw�@���D��
  $LeaveTime=$EndTest-$NOWsec; //�Ѿl����
  
  //�ˬd�O�_�w�W��
  $query="select * from contest_record2 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
  $result=mysql_query($query);
  $N=0;
 	 if (mysql_num_rows($result)>0) {
 	 $row=mysql_fetch_array($result,1);
   $N=1;
   //�Y���R�A,���R�Y���ɮ�
 	   	if ($TEST['active']==2) {
  	   		$a=explode(".",$row['filename']);
  	   		$filename_s=$a[0]."_s.".$a[1];
	    } // end if active=2
   }

  
  ?>
  <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="5">
  	<tr>
  		<td width="80" align="right">�Ѿl�ɶ�</td>
  		<td><input type="text" name="time" value="" size="8">(�����H�ɪ`�N�@���ɶ��A�O�ɧY�L�k�A�W���ɮ�, ���P���v�C)</td>
  	</tr>
  	<tr>
  		<td width="80" align="right">�ɮפW��</td>
  		<td><input type="file" name="thefile" value="" size="80"></td>
  	</tr>
  </table>
  <input type="button" value="�W���ɮ�" onclick="if (document.myform.thefile.value!='') { document.myform.act.value='Upload';document.myform.submit(); }">
  	<?php
  	//�Y�ˬd�w�g�W��
  	if (@$N>0) {
  	 ?>
	<table border="0" width="100%">
  	 <tr>
  	 	<td style="color:#FF0000">�`�N�I�z�w�g�W���ɮסA�ɶ��I��e�i�H�ɭק��A�W�ǡC�m<a href="<?php echo $UPLOAD_U[$TEST['active']].$row['filename'];?>" target="_blank">�[��</a>�n</td>
  	</tr>
  	
  	<tr>
  	 <td>
  	   <?php
  	    switch ($TEST['active']) {
  	    	case '2':
  	    	
  	 			if ($filename_s!="") {
  	   		?>
  	        <img src="<?php echo $UPLOAD_U[2].$filename_s; ?>" border="0">
  	   		<?php
  	 				} // end if $filename_s!=""
  	      break;
  	    	case '3':
  	   		?>
						<embed src="<?php echo $UPLOAD_U[3].$row['filename'];?>" width=400 height=300 type=application/x-shockwave-flash Wmode="transparent">

  	   		<?php
  	      break;
            case '7':
            ?>
                            <script type="text/javascript" src="../contest_teacher/include/swfobject.js"></script>

                            <script >
                                var flashvars = {
                                    project: "<?php echo $UPLOAD_U[7].$row['filename'];?>" ,
                                    autostart: "false"
                                };

                                var params = {
                                    bgcolor: "#FFFFFF",
                                    allowScriptAccess: "always",
                                    allowFullScreen: "true",
                                    wmode: "window",
                                    menu:"false"

                                };
                                var attributes = {};

                                swfobject.embedSWF("../contest_teacher/include/Scratch.swf", "flashContent", "482", "387", "10.2.0","../contest_teacher/include/expressInstall.swf", flashvars, params, attributes);

                            </script>
                            <div id="flashContent" >

                            </div>

            <?php
            break;
            default:
  	   } // end switch
  	   ?>
  	 </td>
  	</tr>
  </table>
  	 <?php
  	} // end if $N>0 �w�W��
  	?>
  	<br>
<table border="0" width="100%">
	<tr><td style="color:#FF0000" align="left">���ХJ���s���H�U���v�ɬ����Ӹ`...</td></tr>
</table>
<?php
test_main($_POST['option1'],1);
?>

<Script language="JavaScript">
  var ACT="End";
	//���ɶi�椤�A���ܳѾl�ɶ�, �I�s functions.php �̪� java function
	var inttestsec=<?php echo $LeaveTime;?>;
  checkLeaveTime('End');
</Script>

  <?php
 } else { 
  $_POST['act']='End'; //�v�ɵ���
 }// end if $NOW<$StartTest
} // end if start


 //�v�ɤw�g����
 if ($_POST['act']=='End') {
  $TEST=get_test_setup($_POST['option1']);
  $STUD=get_student($_SESSION['session_tea_sn']);
  //�ˬd�O�_�w�W��
  $query="select * from contest_record2 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
  $result=mysql_query($query);
  $N=0;
 	 if (mysql_num_rows($result)>0) {
 	 $row=mysql_fetch_array($result,1);
   $N=1;
   }

  ?>
  <table border="0" width="100%">
    <tr>
   	 <td style="color:#0000FF">
   	 	�n�J�ǥ͡G<?php echo $STUD['class_name']." ".$STUD['seme_num']."�� ".$STUD['stud_name'];?>�A�v�ɮɶ��w����.      	 	
      <?php
      if (@$N==1) {
      	?>
      �m<a href="<?php echo $UPLOAD_U[$TEST['active']].$row['filename'];?>" target="_blank">�[�ݦۤv���@�~</a>�n
   	 	<?php
   	  } // end if N>0
   	 	?>
   	 	</td> 
    </tr>
   </table>
  <?php
		test_main($_POST['option1'],1);  
 }
 
?>

</form>