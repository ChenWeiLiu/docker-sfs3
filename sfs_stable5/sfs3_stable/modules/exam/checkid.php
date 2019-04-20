<?php                                                                                                                             
// $Id: checkid.php 8743 2016-01-08 14:02:58Z qfon $
//載入設定檔
if ($isload != 1)
	include "exam_config.php";
/***	
session_start();
session_register("session_log_id"); //登入教師代號
session_register("session_tea_name"); //登入教師姓名
session_register("session_stud_id");
session_register("session_stud_name");
session_register("session_curr_class_num");
***/

//登出
if ($_GET[logout] == 1) {
	session_destroy();
	$exam = "http://".$_SERVER[HTTP_HOST].$_GET[exename];
	header("Location: $exam");
}

//登入  
if ($_POST[B1] == "登入") {
///mysqli	
	$sql_select = "select count(*),a.stud_id,a.stud_name ,a.curr_class_num,b.stud_pass from stud_base a ,exam_stud_data b  ";
	$sql_select .= "where a.stud_study_cond= 0 and a.stud_id = b.stud_id and a.stud_id = ? and b.stud_pass = ? and a.stud_id <>'' ";

$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($sql_select);
$stmt->bind_param('ss', $_POST[stud_id],$_POST[stud_pass]);
$stmt->execute();
$stmt->bind_result($Rcount,$stud_id,$stud_name,$curr_class_num,$stud_pass);
$stmt->fetch();
$stmt->close();
	if ($Rcount>0 || $_SESSION[session_log_id] !='') {
		$_SESSION[session_stud_id]=$stud_id;
		$_SESSION[session_stud_name]= $stud_name;		
		$_SESSION[session_curr_class_num] = $curr_class_num;
		$exam = "http://".$_SERVER[HTTP_HOST].$_POST[exename];
		header("Location: $exam");
	}


///mysqli
	/*
	$sql_select = "select a.stud_id,a.stud_name ,a.curr_class_num,b.stud_pass from stud_base a ,exam_stud_data b \n";
	$sql_select .= "where a.stud_study_cond= 0 and a.stud_id = b.stud_id and a.stud_id = '".$_POST[stud_id]."' and b.stud_pass = '".$_POST[stud_pass]."' and a.stud_id <>'' ";
//	echo $sql_select;exit;
	$result = $CONN->Execute ($sql_select) or die($sql_select);
	
	if ($result->RecordCount()>0 || $_SESSION[session_log_id] !='') {
		$_SESSION[session_stud_id]=$result->fields["stud_id"];
		$_SESSION[session_stud_name]= $result->fields["stud_name"];		
		$_SESSION[session_curr_class_num] = $result->fields["curr_class_num"];
		$exam = "http://".$_SERVER[HTTP_HOST].$_POST[exename];
		header("Location: $exam");
	}
	*/
}

$exename = $_GET[exename] ;

include "header.php";
?> 

  <body  onload="setfocus()">
  <script language="JavaScript">
  <!--
  function setfocus() {
      document.checkid.stud_id.focus();
      return;
       }
      // --></script>
      <center><font color="#0080FF" size="3" face="標楷體">本項服務需查驗帳號密碼，若造成您的不便，敬請見諒</font></center>
      <form action="<?php echo $PHP_SELF ?>" method="POST" name="checkid">
      
      <?php
       if ($error_time != '')
        {
          if ($error_time < 3 )
            echo "<center><font color=red size=5>代號或密碼錯誤!!請再確認</font></center>";
        else
            echo "<center><B><blink><font color=red size=5>本區需檢驗密碼!!請再確認</font></blink></b></center>";
        }       
        if (!isset($error_time)) $error_time = 1;
          else
        $error_time++;
       ?>
        
      <center><table border="0" cellspacing="1"
       bgcolor="#008000" bordercolor="#FFFFFF"
       bordercolordark="#C0C0C0" bordercolorlight="#FFFFFF">
           <tr>
               <td align="center"><table border="1" cellspacing="1"
               bgcolor="#FFFF00" bordercolor="#FFFFFF"
               bordercolordark="#C0C0C0" bordercolorlight="#FFFFFF">
                   <tr>
                       <td align="center" colspan="2"><font
                       color="#0000FF" size="5"><strong>密碼檢查</strong></font></td>
                   </tr><tr>
                       <td align="center">輸入代號</td>
                       <td align="center"><input type="text"
                       size="20" name="stud_id"> </td>
                   </tr>
                   <tr>
                       <td>輸入密碼</td>
                       <td><input type="password" size="20"
                       name="stud_pass"> </td>
                   </tr>
                   <tr>
                       <td align="center" colspan="2">
                     <input type="hidden" name="error_time" value="<?php echo $error_time ?>">
                     <input  type="submit" name="B1" value="登入"> 
                     &nbsp;&nbsp;<input type="button"  value= "回上頁" onclick="history.back()">
                     </td>
                   </tr>
               </table>
               </td>
           </tr>
         <input type="hidden" name="exename" value="<?php echo $exename ?>">
        </form>
      </table>
     </center></div>
<?php include "footer.php"; ?>
