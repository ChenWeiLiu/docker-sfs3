<?php
include_once('config.php');

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

$LDAP=get_ldap_setup();

//POST 後的動作
if ($_POST['act']=="login") {
	$log_id=$_POST['log_id'];
	$log_pass=$_POST['log_pass'];
		
	$server_ip = $LDAP['server_ip'];			//LDAP SERVER IP
	$server_port = $LDAP['server_port'];						//LDAP SERVER PORT
	$bind_dn = $LDAP['bind_dn'];										//LDAP 帳號要 bind 的 DN
	$dn=$log_id."@".$bind_dn;							
	//$bind_dn_x=explode(".",$bind_dn);
	//$rdn="CN=".$log_id;
	//foreach($bind_dn_x as $v) { $rdn.=",DC=".$v; }
	//進行連線
	$ldap_conn=ldap_connect($server_ip,$server_port) or die("SORRY~~Could not cnnect to LDAP SERVER!!");
	//以下兩行務必加上，否則 Windows AD 無法在不指定 OU 下，作搜尋的動作
 	ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
 	ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
	
	//AD方式
	$ldapbind=ldap_bind($ldap_conn,$dn,$log_pass);
	
	//OpenLDAP 格式 , 不加 ou
	if (!$ldapbind) {
		$rdn = $LDAP['base_uid']."=$log_id,".$LDAP['base_dn'];
		$ldapbind=ldap_bind($ldap_conn,$rdn,$log_pass);	
	}

	//OpenLDAP 格式 , 加上教師 ou
	if (!$ldapbind and $LDAP['teacher_ou']!='') {
		$rdn1 = $LDAP['base_uid']."=$log_id,ou=".$LDAP['teacher_ou'].",".$LDAP['base_dn'];
		$ldapbind=ldap_bind($ldap_conn,$rdn1,$log_pass);	
	}

	//OpenLDAP 格式 , 加上學生 ou
	if (!$ldapbind and $LDAP['stud_ou']!='') {
		$rdn2 = $LDAP['base_uid']."=$log_id,ou=".$LDAP['stud_ou'].",".$LDAP['base_dn'];
		$ldapbind=ldap_bind($ldap_conn,$rdn2,$log_pass);	
	}
	
	//OpenLDAP格式 , 但 rdn以 base_dn的設定值重組
	//if (!$ldapbind) {
	//	$rdn1="CN=".$log_id.",".$LDAP['base_dn'];
	//	$ldapbind=ldap_bind($ldap_conn,$rdn1,$log_pass);	
	//}
	
	if ($ldapbind and $log_pass<>"") {
		$INFO="恭喜，以帳號 $log_id 進行LDAP認證成功！";
	} else {
	 $INFO="以 $dn 、 $rdn 、$rdn1 、$rdn2 進行 LDAP 認證皆失敗，若確認LDAP伺服器上的帳號密碼無誤，請勿啟用 LDAP登入！以免造成學務系統無法登入的窘境!!!<br>為安全起見，登入模式已強制設為「主機登入」，請確認登入可成功再啟用 LDAP登入。";
	 $query="update ldap set enable='0' where sn='1'";
   $CONN->Execute($query);	 
	}
  
  ldap_unbind($ldap_conn);
  
}


//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

if (!extension_loaded("ldap")) {
	echo "抱歉！你的 PHP 並未啟用 LDAP 套件，請聯繫系統管理員進行安裝。";
	exit();
 } 



?>
<form name="myform" method="post" act="<?php echo $_SEVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	
	<table border="0" bordercolor="#000000" style="border-collapse:collapse">
		<tr><td style="color:#FF0000"><?php echo $INFO;?></td></tr>
		<tr>
			<td>LDAP 帳號登入測試 </td>
		</tr>
		<tr>
			<td>
			 	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">LDAP登入帳號</td>
								<td bgcolor="#FFFFCC"><input type="text" name="log_id" value="" size="20"></td>
							</tr>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">LDAP帳號密碼</td>
								<td bgcolor="#FFFFCC"><input type="password" name="log_pass" value="" size="30">
								</td>
							</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input type="button" value="登入測試" onclick="document.myform.act.value='login';document.myform.submit();"><font color=red size=2>
				<br>
				※說明：若學務系統有提供學生登入，可一併進行學生帳號測試。
				</td>
		</tr>
	</table>
</form>
