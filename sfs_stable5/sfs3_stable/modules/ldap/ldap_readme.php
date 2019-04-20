<?php
include_once('config.php');

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);


//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

?>	
	<table border="0" bordercolor="#000000" style="border-collapse:collapse">
		<tr>
			<td>
				※本模組允許 sfs3 學務系統可透過 LDAP伺服器進行帳號檢核登入, 目前僅允許教師及家長的帳號。
			</td>
		</tr>
		<tr>
			<td>
				<br>
				※注意! 你安裝的 PHP5 必須啟用 LDAP 套件，目前系統檢測結果：
				<?php
				if (!extension_loaded("ldap")) {
					echo "<font color=red>抱歉！你的 PHP 並未啟用 LDAP 套件，請聯繫系統管理員進行安裝。</font>";
				} else {
					echo "<font color=blue>已啟用 LDAP 套件。</font>";
				} 
				?>
				<br>
			</td>
		</tr>
		<tr>
			<td><br>
				※啟用本模組之前，請確認LDAP伺服器及學務系統中的帳號是否皆存在，目前模組設計上登入的原理：<br>
				<img src="images/login_introduce.png"><br>
					因此若安裝本模組，並啟用 LDAP登入，使用者以後只要管理 LDAP伺服器內的密碼即可。<br>
			</td>
		</tr>
		<tr>
			<td style="color:red"><br>※重要補充說明：啟用 LDAP登入後，萬一發生LADP server 故障情形，<br>請系統管理者直接進入 MySQL 資料庫，
				修改 ldap 資料表中的 enable 欄位，將其數值由 1 改為 0 ，即可恢復本機登入。
				</td>
		</tr>
		<tr>
			<td><br>
				※有關 PHP5 的 LDAP 套件安裝說明，請參考 http://www.php.net/manual/en/ldap.installation.php 安裝文件。<br>
				以 FreeBSD 為例, 利用 ports 安裝 php-extensions 會非常簡單<br>
#cd /usr/ports/lang/php5-extensions <br>
#make config (之後勾選 OpenLDAP support) <br>
<img src="images/php5-extensions.png"> <br>
#make install FORCE_PKG_REGISTER="yes" <br>
<img src="images/php5-extensions2.png"> <br>
即可安裝完成<br>
			</td>
		</tr>
	</table>
</form>
