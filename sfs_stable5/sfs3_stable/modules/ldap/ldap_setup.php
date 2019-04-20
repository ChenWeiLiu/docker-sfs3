<?php
include_once('config.php');

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//POST 後的動作
if ($_POST['act']=="save") {
 foreach($_POST as $K=>$V) {
  ${$K}=$V;
 }

 $query="update ldap set enable='$enable',server_ip='$server_ip',server_port='$server_port',bind_dn='$bind_dn',base_dn='$base_dn', base_uid='$base_uid',chpass_url='$chpass_url',teacher_ou='$teacher_ou',stud_ou='$stud_ou',enable1='$enable1' where sn='1'";
 if ($CONN->Execute($query)) {
   $INFO=" 己於".date("Y-m-d H:i:s")."進行儲存動作!";
   $INFO.=($enable)?"　注意！請務必進行 LDAP認證測試!!":"";
 } else {
   echo "SQL語法錯誤！ query=".$query;
   exit();
 }
}

$LDAP=get_ldap_setup();


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
		<tr>
			<td>LDAP 帳號登入設定 </td>
		</tr>
		<tr>
			<td>
			 	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">教師登入模式</td>
								<td bgcolor="#FFFFCC">
										<input type="radio" name="enable" value="0"<?php if ($LDAP['enable']==0) echo " checked";?>>本機登入
										<input type="radio" name="enable" value="1"<?php if ($LDAP['enable']==1) echo " checked";?>>LDAP 登入
								</td>
							</tr>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">學生登入模式</td>
								<td bgcolor="#FFFFCC">
										<input type="radio" name="enable1" value="0"<?php if ($LDAP['enable1']==0) echo " checked";?>>本機登入
										<input type="radio" name="enable1" value="1"<?php if ($LDAP['enable1']==1) echo " checked";?>>LDAP 登入
								</td>
							</tr>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">LDAP伺服器IP</td>
								<td bgcolor="#FFFFCC">
										<input type="text" name="server_ip" value="<?php echo $LDAP['server_ip'];?>" size="30">
								</td>
							</tr>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">LDAP port</td>
								<td bgcolor="#FFFFCC">
										<input type="text" name="server_port" value="<?php echo $LDAP['server_port'];?>" size="10">
										<br><font color=blue size=2>一般LDAP伺服器預設使用 389 或 636 port</font>
								</td>
							</tr>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">Windows AD 的 Bind dn</td>
								<td bgcolor="#FFFFCC">
												帳號@<input type="text" name="bind_dn" value="<?php echo $LDAP['bind_dn'];?>" size="30">
												<br><font color=blue size=2>即帳號要加的DN尾碼，例如: smallduh@fnjh.tc.edu.tw，即填入 fnjh.tc.edu.tw</font>
								</td>
							</tr>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">OpenLDAP的Bind dn</td>
								<td bgcolor="#FFFFCC" valign="top">
										帳號欄位：<input type="text" name="base_uid" value="<?php echo $LDAP['base_uid']?>" size="5" /> <br>
										教師帳號 ou 值：<input type="text" name="teacher_ou" value="<?php echo $LDAP['teacher_ou']?>" size="10" />
										，學生帳號 ou 值：<input type="text" name="stud_ou" value="<?php echo $LDAP['stud_ou']?>" size="10" />
										<br />
										Base dn：<input type="text" name="base_dn" value="<?php echo $LDAP['base_dn'];?>" size="40">
										<br><font color=blue size=2>例如: [OU=Users, ] DC=fnjh, DC=tcc, DC=edu, DC=tw</font>
								</td>
							</tr>
							<tr>
								<td bgcolor="#FFCCFF" valign="top">更改密碼的網址url</td>
								<td bgcolor="#FFFFCC"><input type="text" name="chpass_url" value="<?php echo $LDAP['chpass_url'];?>" size="30">
										<br><font color=blue size=2>啟用LDAP登入，將無法更改原本存在學務主機內的密碼，</font>
										<br><font color=blue size=2>請提供更改 LDAP密碼的超連結網址，當使用者要改密碼時可予以提示連結。 </font>
								</td>
							</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input type="button" value="儲存" onclick="document.myform.act.value='save';document.myform.submit();"><font color=red size=2><?php echo $INFO;?></font>
				<br><br><font color=red>※注意! 為達同步效果，採用 LDAP 登入成功，系統會自動覆寫 LDAP 的帳號密碼至學務主機內。</font>
				</td>
		</tr>
		<tr style="color:#0000FF">
		 <td>
		 ※設定說明：<br>
		 1.若您的LDAP是 Windows Server 的 Active Directory 環境，您只要設定 Windows AD 的 bind dn 那一欄即可。<br>
		 2.若您的LDAP是 Linux 的 OpenLDAP，那您的設定時務必注意：<br>
		 (1)帳號搜尋欄位一定要設，一般是 uid 。 <br>
		 (2)「ou」是指帳號所在的容區，您可以直接設定在 base dn 這一欄，也可以依據教師及學生帳號分別指定。<br>
		 (3)當學生與教師的 ou 值不同時，請個別設定在所屬欄位中。<br>
		 (4)若有特別設定教師或學生的 ou 時，系統會把 ou 及 base dn 這兩欄的資料合併成 bind dn.<br>
		 3.僅提供教師與學生兩種身分可利用 LDAP 登入.　
		 </td>
		</tr>
	</table>
</form>
