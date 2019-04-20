<?php

// $Id: teach_cpass.php 8917 2016-06-23 13:13:38Z tuheng $

// --系統設定檔
include "teach_config.php";

//載入 ldap 模組函式
include_once('../ldap/my_functions.php');
$LDAP=get_ldap_setup();

// --認證 session 
sfs_check();

head("更改個人密碼");
print_menu($teach_menu_p); 

//判斷是否停用此頁面
if ($disable_this) {
    echo "<script>document.location.href='".$SFS_PATH_HTML."';</script>";
    exit;
}

if ($LDAP['enable']) {
	echo "系統已啟用 LDAP 認證，請直接登入 LDAP 伺服器進行密碼變更。<br>";
	if ($LDAP['chpass_url']!="") {
	 echo "你可以經由以下連結前往變更密碼：<a href=\"".$LDAP['chpass_url']."\">前往變更密碼</a>";
	}
	exit();
}
?>
<script type="text/javascript">
function verifyPassword() {
    var password = $("#login_pass").val();
    var login_id = $("#login_id").attr("name");
    if (password.length < 8){
        $("#ShowVerifyError").html("<font color='red'><strong>密碼長度不足8個</strong></font>");
        $("#login_pass2").attr("disabled", true);
    }
    else if (!password.match(/\D/)){
        $("#ShowVerifyError").html("<font color='red'><strong>密碼至少要有1個英文字或符號</strong></font>");
        $("#login_pass2").attr("disabled", true);
    }
    else if (!password.match(/\d/)){
        $("#ShowVerifyError").html("<font color='red'><strong>密碼至少要有1個數字</strong></font>");
        $("#login_pass2").attr("disabled", true);
    }
    else if (password==login_id){
        $("#ShowVerifyError").html("<font color='red'><strong>密碼不能與帳號相同</strong></font>");
        $("#login_pass2").attr("disabled", true);
    }
    else{
        $("#ShowVerifyError").html("");
        $("#login_pass2").attr("disabled", false);
    }
}

function checkPasswordMatch() {
    var password = $("#login_pass").val();
    var confirmPassword = $("#login_pass2").val();

    if (password != confirmPassword){
        $("#ShowMatchError").html("<font color='red'><strong>兩次密碼不同</strong></font>");
        $("#send_key").attr("disabled", true);
    }
    else{
        $("#ShowMatchError").html("");
        $("#send_key").attr("disabled", false);
    }
}

$(document).ready(function () {
   $("#send_key").attr("disabled", true);
   $("#login_pass2").attr("disabled", true);
   $("#login_pass").keyup(verifyPassword);
   $("#login_pass2").keyup(checkPasswordMatch);
});
</script>
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  class=main_body >
<form method="post" name=cform>
<?php
if ($_POST[key]=="更改密碼") {
	$_GET['alert']="";
	if ($_POST[login_pass]==$_POST[login_pass2]) {
		$err=pass_check(trim($_POST[login_pass]),$_SESSION['session_log_id']);
		if ($err) {
			echo "<tr><td class=title_mbody>$err</td></tr><tr><td align=\"center\" valign=\"top\"><input type=\"submit\" value=\"重新更改\"></td></tr>";
		} else {
			$last_chpass_time=date("Y-m-d");
			$ldap_password = createLdapPassword($_POST['login_pass']);
            
			$sql_select = "select mem_array from teacher_base where teacher_sn ='{$_SESSION['session_tea_sn']}' ";
			$recordSet = $CONN -> Execute($sql_select) or trigger_error("資料連結錯誤：" . $sql_select, E_USER_ERROR);
	        while(list($mem_array) = $recordSet -> FetchRow())
			{
              $pass_array=unserialize($mem_array);
			}
			$can_chpass=false;
			$samepass_arr = get_module_setup("chpass");
            $sd=$samepass_arr['samepass_period']?$samepass_arr['samepass_period']:90;
	
			$samepassdate=date("Y-m-d", strtotime("-$sd days"));
			$ka=array_search(pass_operate($_POST[login_pass]),$pass_array);
			if (!empty($ka))
			{
			 if ($samepassdate<$ka)
			 {
				 $can_chpass=false;				 
			 ?>
				<script language="javascript">
                alert('更改密碼失敗! \n您'+<?php echo $sd;?>+'天內不能設定此用過的密碼。');
                </script> 
				<?php
				$_POST[key]="";
				echo "<script>function kk(){parent.location.href='teach_cpass.php'}setTimeout('kk()',10)</script>";
			 }
			 else
			 { 
		        $can_chpass=true;
			 }
			}
			else
			{
				$can_chpass=true;
			}
	
			
			if ($can_chpass)
			{
			 $pass_array[$last_chpass_time]=pass_operate($_POST[login_pass]); 
			 krsort($pass_array);			
			 $mem_array=serialize($pass_array);			
			 $query = "update teacher_base set mem_array='$mem_array',last_chpass_time='$last_chpass_time',login_pass ='".pass_operate($_POST[login_pass])."' , ldap_password='$ldap_password' where teacher_sn ='{$_SESSION['session_tea_sn']}' ";
			 mysql_query($query,$conID);
			 echo "<tr><td class=title_mbody >密碼更改成功</td></tr>";
			}
			 $_SESSION['session_login_chk']=pass_check(trim($_POST[login_pass]),$_SESSION['session_log_id']);;
		    
		}
	} else {
		echo "<tr><td class=title_mbody>兩次密碼輸入不同，密碼更改失敗！</td></tr><tr><td align=\"center\" valign=\"top\"><input type=\"submit\" value=\"重新更改\"></td></tr>";
	}
}
else
{
?>
<tr>
	<td align="center" valign="top">輸入新密碼:
	<input type="password" size="32" maxlength="32" name="login_pass" id="login_pass" onChange="verifyPassword();" />
        <br>
        <div id="ShowVerifyError">
        </div>
        </td>
</tr>
<tr>
	<td align="center" valign="top">再輸入一次:
	<input type="password" size="32" maxlength="32" name="login_pass2" id="login_pass2" onChange="checkPasswordMatch();" />
        <br>
        <div id="ShowMatchError">
        </div>
        </td>
</tr>
<tr>
	<td align="center" valign="top"><input type="submit" name="key" id= "send_key" value="更改密碼"></td>
</tr>
<?php
}
echo "<input type='hidden' name='{$_SESSION['session_log_id']}' id='login_id' />";
echo "</form></table>為確保資料安全，請務必遵守以下事項：<br>
			1.密碼至少為 8 個數字、字母或符號組成。<br>
			2.密碼須包含英文字母和阿拉伯數字。<br>
			3.密碼不可為系統預設密碼<br>
			4.密碼不可和帳號相同，也不可是自己的身份證字號。";
foot();

if ($_GET['alert']=="ok")
{
$m_arr = get_module_setup("chpass");
$vd=$m_arr['chpass_period']?$m_arr['chpass_period']:30;
?>
<script language="javascript">
alert('您登入的密碼已經超過'+<?php echo $vd;?>+'天未更改!\n為符合資安政策需要，請立即更改密碼');
</script>
<?php
}
?>