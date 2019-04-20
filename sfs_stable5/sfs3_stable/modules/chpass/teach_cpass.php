<?php

// $Id: teach_cpass.php 8917 2016-06-23 13:13:38Z tuheng $

// --�t�γ]�w��
include "teach_config.php";

//���J ldap �Ҳը禡
include_once('../ldap/my_functions.php');
$LDAP=get_ldap_setup();

// --�{�� session 
sfs_check();

head("���ӤH�K�X");
print_menu($teach_menu_p); 

//�P�_�O�_���Φ�����
if ($disable_this) {
    echo "<script>document.location.href='".$SFS_PATH_HTML."';</script>";
    exit;
}

if ($LDAP['enable']) {
	echo "�t�Τw�ҥ� LDAP �{�ҡA�Ъ����n�J LDAP ���A���i��K�X�ܧ�C<br>";
	if ($LDAP['chpass_url']!="") {
	 echo "�A�i�H�g�ѥH�U�s���e���ܧ�K�X�G<a href=\"".$LDAP['chpass_url']."\">�e���ܧ�K�X</a>";
	}
	exit();
}
?>
<script type="text/javascript">
function verifyPassword() {
    var password = $("#login_pass").val();
    var login_id = $("#login_id").attr("name");
    if (password.length < 8){
        $("#ShowVerifyError").html("<font color='red'><strong>�K�X���פ���8��</strong></font>");
        $("#login_pass2").attr("disabled", true);
    }
    else if (!password.match(/\D/)){
        $("#ShowVerifyError").html("<font color='red'><strong>�K�X�ܤ֭n��1�ӭ^��r�βŸ�</strong></font>");
        $("#login_pass2").attr("disabled", true);
    }
    else if (!password.match(/\d/)){
        $("#ShowVerifyError").html("<font color='red'><strong>�K�X�ܤ֭n��1�ӼƦr</strong></font>");
        $("#login_pass2").attr("disabled", true);
    }
    else if (password==login_id){
        $("#ShowVerifyError").html("<font color='red'><strong>�K�X����P�b���ۦP</strong></font>");
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
        $("#ShowMatchError").html("<font color='red'><strong>�⦸�K�X���P</strong></font>");
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
if ($_POST[key]=="���K�X") {
	$_GET['alert']="";
	if ($_POST[login_pass]==$_POST[login_pass2]) {
		$err=pass_check(trim($_POST[login_pass]),$_SESSION['session_log_id']);
		if ($err) {
			echo "<tr><td class=title_mbody>$err</td></tr><tr><td align=\"center\" valign=\"top\"><input type=\"submit\" value=\"���s���\"></td></tr>";
		} else {
			$last_chpass_time=date("Y-m-d");
			$ldap_password = createLdapPassword($_POST['login_pass']);
            
			$sql_select = "select mem_array from teacher_base where teacher_sn ='{$_SESSION['session_tea_sn']}' ";
			$recordSet = $CONN -> Execute($sql_select) or trigger_error("��Ƴs�����~�G" . $sql_select, E_USER_ERROR);
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
                alert('���K�X����! \n�z'+<?php echo $sd;?>+'�Ѥ�����]�w���ιL���K�X�C');
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
			 echo "<tr><td class=title_mbody >�K�X��令�\</td></tr>";
			}
			 $_SESSION['session_login_chk']=pass_check(trim($_POST[login_pass]),$_SESSION['session_log_id']);;
		    
		}
	} else {
		echo "<tr><td class=title_mbody>�⦸�K�X��J���P�A�K�X��異�ѡI</td></tr><tr><td align=\"center\" valign=\"top\"><input type=\"submit\" value=\"���s���\"></td></tr>";
	}
}
else
{
?>
<tr>
	<td align="center" valign="top">��J�s�K�X:
	<input type="password" size="32" maxlength="32" name="login_pass" id="login_pass" onChange="verifyPassword();" />
        <br>
        <div id="ShowVerifyError">
        </div>
        </td>
</tr>
<tr>
	<td align="center" valign="top">�A��J�@��:
	<input type="password" size="32" maxlength="32" name="login_pass2" id="login_pass2" onChange="checkPasswordMatch();" />
        <br>
        <div id="ShowMatchError">
        </div>
        </td>
</tr>
<tr>
	<td align="center" valign="top"><input type="submit" name="key" id= "send_key" value="���K�X"></td>
</tr>
<?php
}
echo "<input type='hidden' name='{$_SESSION['session_log_id']}' id='login_id' />";
echo "</form></table>���T�O��Ʀw���A�аȥ���u�H�U�ƶ��G<br>
			1.�K�X�ܤ֬� 8 �ӼƦr�B�r���βŸ��զ��C<br>
			2.�K�X���]�t�^��r���M���ԧB�Ʀr�C<br>
			3.�K�X���i���t�ιw�]�K�X<br>
			4.�K�X���i�M�b���ۦP�A�]���i�O�ۤv�������Ҧr���C";
foot();

if ($_GET['alert']=="ok")
{
$m_arr = get_module_setup("chpass");
$vd=$m_arr['chpass_period']?$m_arr['chpass_period']:30;
?>
<script language="javascript">
alert('�z�n�J���K�X�w�g�W�L'+<?php echo $vd;?>+'�ѥ����!\n���ŦX��w�F���ݭn�A�ХߧY���K�X');
</script>
<?php
}
?>