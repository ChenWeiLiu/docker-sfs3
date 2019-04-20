<body <?php if (!$_REQUEST['cdc']) {?>onload="setfocus()"<?php } ?>>
<!-- $Id: new_login.php 9192 2018-01-05 07:55:48Z smallduh $ -->
<script language="JavaScript">
<!--
function setfocus() {
      document.checkid.log_id.focus();
      return;
}

function setForm(tname,pid,encryptstr,sn,pk){
	var thisForm = document.checkid;
	thisForm.id4.value=pid;
	thisForm.serialnumber.value=sn;
	thisForm.encrypted.value=encryptstr;
	thisForm.pk.value=pk;
	thisForm.submit();
}

function doAlert(msg){
        alert(msg);
}
<?php if($_SESSION['CAPTCHA']['TYPE']==1) {?>

var IE = document.all?true:false;
if (!IE) document.captureEvents(Event.MOUSEMOVE);
document.onclick = getMouseXY;
var tempX = 0;
var tempY = 0;
function getMouseXY(e) {
  if (IE) { // grab the x-y pos.s if browser is IE
    tempX = event.clientX + document.body.scrollLeft;
    tempY = event.clientY + document.body.scrollTop;
  } else {  // grab the x-y pos.s if browser is NS
    tempX = e.pageX;
    tempY = e.pageY;
  }
  // catch possible negative values in NS4
  if (tempX < 0){tempX = 0}
  if (tempY < 0){tempY = 0}
  var objs = document.getElementById("KIMG");
  var x = objs.offsetLeft;
  var y = objs.offsetTop;
  for ( var i = 1; i < 9; i++) {
    newobjs = objs.offsetParent;
	if (newobjs) {
		x += newobjs.offsetLeft;
		y += newobjs.offsetTop;
		objs = newobjs;
	} else
	  break;
  }
  document.getElementById("KIMG").src = "<?php echo $SFS_PATH_HTML; ?>kitten_img.php?x="+(tempX-x)+"&y="+(tempY-y)+"&t="+Math.random();

  ajaxGetValue("nums", "num=1");
  return true;
}

function ajaxGetValue(id, val) {
  if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  } else {// code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) { //傳回值是固定寫法
      document.getElementById(id).innerHTML = xmlhttp.responseText; //[最後]把select出來的資料 傳回前面指定的html位置
    }
  }

  xmlhttp.open("GET", "<?php echo $SFS_PATH_HTML; ?>kitten_img.php?"+val, false);
  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlhttp.send();

  return true;
}
<?php } ?>
 -->
</script>
<p></p>
<?php

//讀取各種身分的登入模式
$E[0]="本機登入";
$E[1]="LDAP登入";
  		$query="select * from ldap limit 1";
  		$res=$CONN->Execute($query); // or die('Error! SQL='.$query);  
  		if (!$res) {
  			$LDAP['enable']=$LDAP['enable1']=0;
  		} else {
  			$LDAP=$res->fetchrow();  
  		}

//讀取學生密碼管理模組設定
$stud_pass=&get_module_setup("stud_pass");
if ($stud_pass['quick_chpass'] and $LDAP['enable1']==0) {
	$stud_pass_url="
	<tr class='small' id=\"stud_chpass\" style='display:none'>
	<td nowrap>密碼變更</td>
	<td>
	<input type=\"button\" style=\"color:#AA0000\" value=\"按我進行密碼變更\" onclick=\"window.location='modules/stud_pass/quick_chpass.php'\"></td>
	</tr>
	";
} else {
	$stud_pass_url="";
}





if ($_REQUEST['cdc'])
	echo login_form3();
else {
	if (chk_login_img("","",1))
		echo login_form();
	else
		echo login_form2();
}
?>

<Script>
//教師
$('#logwho_0').click(function(){
  $('#LoginMode').html('<?php echo $E[$LDAP['enable']];?>');
	$("#stud_chpass").css("display","none");
});

//學生
$('#logwho_1').click(function(){
  $('#LoginMode').html('<?php echo $E[$LDAP['enable1']];?>');
	$("#stud_chpass").css("display","table-row");
});

//家長
$('#logwho_2').click(function(){
  $('#LoginMode').html('本機登入');
	$("#stud_chpass").css("display","none");
});

//其他
$('#logwho_3').click(function(){
  $('#LoginMode').html('本機登入');
	$("#stud_chpass").css("display","none");
});


</Script>
<?php
if ($_REQUEST['cdc']) { ?>
<p align="center">
<font size="2">本項服務需檢查憑證，若有任何疑問請洽系統管理者。</font>
<a href="javascript:history.back()">回上頁</a>
</p>
<?php } else { ?>
<p align="center">
<font size="2">本項服務需檢查管理代號密碼，若忘記，請洽系統管理者。</font>
<a href="javascript:history.back()">回上頁</a>
</p>
<?php }
function login_who_radio($checked='教師')
{
	$arr = array('教師','學生','家長','其他');
	$str = '';
	foreach ($arr as $id=>$val) {
		$str .= "<input id='logwho_$id'  type='radio' name='log_who' value='$val' ";
		if ($checked == $val)
			$str.= "checked='checked'";
		$str .= " /> <label for='logwho_$id'>$val</label>"; 
	}
	return  $str;
}

function login_form(){
     global $SFS_PATH_HTML, $go_back,$CONN,$OpenID_enable,$OpenID_dn,$OpenID_header,$stud_pass_url;
     
     //檢查是否啟用 LDAP 登入模組
    $query="select * from sfs_module where dirname='ldap' and islive='1'";
  	$res=$CONN->Execute($query) or die('Error! SQL='.$query);;
     if ($res->RecordCount()>0) {
  		$query="select * from ldap limit 1";
  		$res=$CONN->Execute($query); // or die('Error! SQL='.$query);  
  		if (!$res) {
  			$LDAP['enable']=0;
  		} else {
  			$LDAP=$res->fetchrow();  
  		}
     } else {
      $LDAP['enable']=0;
     }
     
     if (isset($_POST['log_who']))
     	$logStr = login_who_radio($_POST['log_who']);
    else     	
     $logStr = login_who_radio();
     $logMode=($LDAP['enable'])?"LDAP登入":"本機登入";
     $Form = "
	<form action='" . $SFS_PATH_HTML . "login.php' method='post'  name='checkid'>
	<table style='width:100%;'>
	<tr><td style='text-align:center;padding:15px;'>
	<div  class='ui-widget-header ui-corner-top'  style='width:350px; padding:5px; margin:auto'>
	<span style='text-align:center;'>登入檢查</span>
	</div>
	<div  class='ui-widget-content ui-corner-bottom'  style='width:350px; padding:5px; margin:auto'>
	<table cellspacing='0' cellpadding='3' align='center'>
	<tr class='small'>
	<td nowrap>輸入代號</td><td nowrap>
	<input type='text' name='log_id' size='20' maxlength='15'>
	</td>
	</tr>
	<tr class='small'>
	<td nowrap>輸入密碼</td>
	<td nowrap>
	<input type='password' name='log_pass' size='20' maxlength='24'>
	</td>
	</tr>
	<tr class='small'>
	<td nowrap>登入身份</td>
	<td>
	$logStr 
	</td>
	</tr>
	<tr class='small'>
	<td nowrap>認證模式</td>
	<td>
	<table border='1' cellspacing='1' cellpadding='1' style='border-collapse:collapse' bordercolor='#111111'>
	<tr class='small'><td id='LoginMode'>$logMode</td></tr> 
	</table>
	</td>
	</tr>
	$stud_pass_url
	<tr>
	<td  colspan='2' style='text-align:center'>
		<input type='submit' value='確定' name='B1'>
	</td>
	</tr>
	</table>
  	<input type='hidden' name='go_back' value='$go_back'>
	</div>
	</td>
	</tr>
	</table>
	</form>
	";
			if ($OpenID_enable==1) {
	 
	 $Form.="
	 <br>
	  <div style=\"border-width:1px; border-color:black;  padding:3px; font-size:15px;\">
	   <center>
	   <table border='0'>
	   <tr>
	   <td style='color:#0000FF'>§本站允許使用OpenID登入</td>
	   </tr>
	   <tr>
	   <td>
      <form method=\"get\" action=\"include/OIDpackage/authcontrol.php\">
        請輸入你的 ".$OpenID_header." OpenID 帳號<br />
        <input type=\"hidden\" name=\"action\" value=\"verify\" />
        <input type=\"hidden\" name=\"domain\" value=\"".$OpenID_dn."\" />
        <span style=\"color:#777;\">http://<input type=\"text\" name=\"openid_identifier\" value=\"\" size=\"12\" maxlength=\"16\" />.".$OpenID_dn."</span>
        <input type=\"submit\" value=\" 以 OpenID 登入 \" />
      </form>
      </td></tr>
      <tr>
       <td style='color:#700000;font-size:9pt'>※注意:<br/>1.限教師身分。<br/>2.學務系統內的身分證字號資料務必正確才能正常登入!</td>
      </tr>
      </table>
      </center>
    </div>

	 ";	
	} // end if ($OpenID_enable==1)

     return $Form;
     }

     
     
     
function login_form2(){
     global $SFS_PATH_HTML, $go_back,$CONN,$OpenID_enable,$OpenID_dn,$OpenID_header,$stud_pass_url;

     //檢查是否啟用 LDAP 登入模組
    $query="select * from sfs_module where dirname='ldap' and islive='1'";
  	$res=$CONN->Execute($query) or die('Error! SQL='.$query);;
     if ($res->RecordCount()>0) {
  		$query="select * from ldap limit 1";
  		$res=$CONN->Execute($query); // or die('Error! SQL='.$query);  
  		if (!$res) {
  			$LDAP['enable']=0;
  		} else {
  			$LDAP=$res->fetchrow();  
  		}
     } else {
      $LDAP['enable']=0;
     }


     $logStr = login_who_radio();
     $logMode=($LDAP['enable'])?"LDAP登入":"本機登入";

	$Form = "
	<form action='" . $SFS_PATH_HTML . "login.php' method='post'  name='checkid'>
	<table style='width:100%;' id='loginTable'>
	<tr><td style='text-align:center;padding:15px;'>
	<div  class='ui-widget-header ui-corner-top'  style='width:350px; padding:5px; margin:auto'>
	<span style='text-align:center;'>登入檢查</span>
	</div>
	<div  class='ui-widget-content ui-corner-bottom'  style='width:350px; padding:5px; margin:auto'>
	<table cellspacing='0' cellpadding='3' align='center'>
	<tr class='small'>
	<td nowrap>輸入代號</td><td nowrap>
	<input type='text' name='log_id' size='20' maxlength='15'>
	</td>
	</tr>
	<tr class='small'>
	<td nowrap>輸入密碼</td>
	<td nowrap>
	<input type='password' name='log_pass' size='20' maxlength='24'>
	</td>
	</tr>" . (($_SESSION['CAPTCHA']['TYPE']==1)?
	"<tr class='small'><td>小貓驗證<td>請點選圖中的兩隻小貓</td></tr>
	<tr class='small'><td colspan='2'>
	<img src='".$SFS_PATH_HTML."kitten_img.php' style='vertical-align:middle;' id='KIMG'>
	</td></tr>
	<tr class='small'><td colspan='2' style='text-align: center;'>您目前選擇了 <span id='nums'>0</span> 隻動物</td></tr>":
	"<tr class='small'>
	<td nowrap>輸入驗証碼</td>
	<td nowrap>
	<img src='".$SFS_PATH_HTML."pass_img.php' style='vertical-align:middle;' name='PIMG'>
	<input type='text' name='log_pass_chk' size='4' maxlength='15'>
	</td>") .
	"</tr>
	<tr class='small'>
	<td nowrap>登入身份</td>
	<td>
	$logStr 
	</td>
	</tr>
	<tr class='small'>
	<td nowrap>認證模式</td>
	<td>
	<table border='1' cellspacing='1' cellpadding='1' style='border-collapse:collapse' bordercolor='#111111'>
	<tr class='small'><td id='LoginMode'>$logMode</td></tr> 
	</table>
	</td>
	</tr>
	$stud_pass_url
	<tr>
	<td  colspan='2' style='text-align:center'>
	<input type='submit' value='確定' name='B1'>
	<input type='button' value='重取圖' onclick=\"PIMG.src='".$SFS_PATH_HTML."pass_img.php?'+ Math.random();\">
	</td>
	</tr>
	</table>
	</div>
	</td></tr></table>
	<input type='hidden' name='go_back' value='$go_back'>
	
	</form>
	";
			if ($OpenID_enable==1) {
	 
	 $Form.="
	 <br>
	  <div style=\"border-width:1px; border-color:black;  padding:3px; font-size:15px;\">
	   <center>
	   <table border='0'>
	   <tr>
	   <td style='color:#0000FF'>§本站允許使用OpenID登入</td>
	   </tr>
	   <tr>
	   <td>
      <form method=\"get\" action=\"include/OIDpackage/authcontrol.php\">
        請輸入你的 ".$OpenID_header." OpenID 帳號<br />
        <input type=\"hidden\" name=\"action\" value=\"verify\" />
        <input type=\"hidden\" name=\"domain\" value=\"".$OpenID_dn."\" />
        <span style=\"color:#777;\">http://<input type=\"text\" name=\"openid_identifier\" value=\"\" size=\"12\" maxlength=\"16\" />.".$OpenID_dn."</span>
        <input type=\"submit\" value=\" 以 OpenID 登入 \" />
      </form>
      </td></tr>
      <tr>
       <td style='color:#700000;font-size:9pt'>※注意:<br/>1.限教師身分。<br/>2.學務系統內的身分證字號資料務必正確才能正常登入!</td>
      </tr>
      </table>
      </center>
    </div>

	 ";	
	} // end if ($TaiChung_OpenID==1)

	return $Form;
}

function login_form3(){
     global $SFS_PATH_HTML, $go_back,$CONN;

     //檢查是否啟用 LDAP 登入模組
    $query="select * from sfs_module where dirname='ldap' and islive='1'";
  	$res=$CONN->Execute($query) or die('Error! SQL='.$query);;
     if ($res->RecordCount()>0) {
  		$query="select * from ldap limit 1";
  		$res=$CONN->Execute($query); // or die('Error! SQL='.$query);  
  		if (!$res) {
  			$LDAP['enable']=0;
  		} else {
  			$LDAP=$res->fetchrow();  
  		}
     } else {
      $LDAP['enable']=0;
     }

     $logStr = login_who_radio();
	
	$Form = "
	<table style='width:100%;'>
	<tr><td style='text-align:center;padding:15px;'>
	<div  class='ui-widget-header ui-corner-top'  style='width:350px; padding:5px; margin:auto'>
	<span style='text-align:center;'>登入檢查</span>
	</div>
	<div  class='ui-widget-content ui-corner-bottom'  style='width:350px; padding:5px; margin:auto'>
	
	<form action='" . $SFS_PATH_HTML . "login.php' method='post'  name='checkid' id='cerloginform'>
	<table cellspacing='0' cellpadding='3' align='center'>
	<tr style='height: 8pt;'><td></td></tr>
	<tr><td>
	<applet code='getCDC.class' archive='".$SFS_PATH_HTML."/getCDC.jar' width='320' height='80' MAYSCRIPT>
	<param name='setForm' value='setForm'>
	<param name='doAlert' value='doAlert'>
	<param name='encrypt' value='".$_SESSION['ToBeSign']."'>
	<param name='certtype' value='Sign'>
	<param name='fontsize' value='14'>
	<param name='fontname' value='細明體'>
	<param name='ocsp' value='false'>
	</applet>
	<input type='hidden' name='encrypted' id='encrypted' />
	<input type='hidden' name='serialnumber' id='serialnumber' />
	<input type='hidden' name='id4' id='id4' />
	<input type='hidden' name='pk' id='pk' />
	<input type='hidden' name='cdc' value='1' />
	<input type='hidden' name='go_back' value='$go_back'>
	<span class='small'> &nbsp; &nbsp; 要使用自然人憑證登入, 您須先 :<br> &nbsp; 1.  安裝<a href='http://gca.nat.gov.tw/download/HiCOSClient_v2.1.9.zip' target='new'>HiCOS憑證管理程式 v2.1.9</a>及<a href='http://www.sfs.project.edu.tw/modules/mydownloads/visit.php?cid=2&lid=47'>臺中市政府教育局憑證登入元件v0.5版</a><br> &nbsp; 
	2. <a href='{$SFS_PATH_HTML}modules/teacher_self/'>註冊自然人憑證</a></span>
	</td></tr></table>
	</form>
	</div>
	</td>
	</tr>
	</table>
	";
	$str = '';
	if (isset($_GET['cdc_error']) and $_GET['cdc_error']==1) {
		$str = '<script>alert("您尚未註冊憑證，請先由一般登入後，進入教師個人資料，註冊憑證");</script>';
	}
	return $Form.$str;
}
