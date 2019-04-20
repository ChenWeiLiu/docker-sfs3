<?php

// $Id:$

// --系統設定檔
include "teach_config.php";
// --處理unicode碼函式
include "my_fun.php";
// --認證 session 
sfs_check();

head("註冊自然人憑證");
print_menu($teach_menu_p);
if ($CDCLOGIN) { ?>
<script type="text/javascript">
//<!--

function setForm(tname,pid,sn,pk){
	var thisForm = document.regform;
	thisForm.id4.value=pid;
	thisForm.serialnumber.value=sn;
	thisForm.pk.value=pk;
	thisForm.submit();
}
	
function doAlert(msg){
	alert(msg);
}
//-->
</script>
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  class=main_body >
<?php
if ($_POST['id4']) {
	$cdc = new CDC();
	$cdc->setCerSn($_POST['serialnumber']);
	$cdc->setCert($_POST['pk']);
	$cdc->readCert();

	if ($cdc->cert_status == "good") {
		$msg = openssl_x509_parse($cdc->cert);
		$FromTime = date("Y-m-d H:i:s",$msg['validFrom_time_t']);
		$ToTime = date("Y-m-d H:i:s",$msg['validTo_time_t']);
		//$TrueName = iconv("UTF-8","BIG5",$msg['subject']['CN']);
                //將utf-8字串轉為unicode字元(若有big5碼不支援時)
                $TrueName = utf8conv2charset($msg['subject']['CN']);
                
		$query="select * from teacher_base where teacher_sn='".$_SESSION['session_tea_sn']."'";
		$res=$CONN->Execute($query);
		$userdata=$res->FetchRow();

		if (substr(trim($userdata['teach_person_id']),-4,4) == $_POST['id4'] && trim($userdata['name']) == $TrueName) {
			$query="update teacher_base set cerno='".$_POST['serialnumber']."' where teacher_sn='".$_SESSION['session_tea_sn']."'";
			$res=$CONN->Execute($query);
			$msg = "通過確認, 登錄成功!";
		} else
			$msg = '「卡片內登載姓名及身分證字號與帳號登錄資料不符」!\n\n未通過個人資料檢核, 無法進行登錄 !';
	} elseif ($cdc->cert_status=="revoked") {
		$msg = "憑證已廢止!";
	} else
		$msg = "憑證無法辨識!";
}

?>
<tr><td>
<applet code="regCDC.class" archive="<?php echo $SFS_PATH_HTML;?>/getCDC.jar" width="340" height="70" MAYSCRIPT>
<param name="setForm" value="setForm">
<param name="doAlert" value="doAlert">
<param name="certtype" value="Sign">
<param name="fontsize" value="14">
<param name="fontname" value="細明體">
<param name="ocsp" value="false">
</applet>
<form name="regform" id="regform" method="post" action="">
<input type="hidden" name="serialnumber" id="serialnumber" />
<input type="hidden" name="id4" id="id4" />
<input type="hidden" name="pk" id="pk" />
</form>
</td></tr>
<?php
$query="select * from teacher_base where teacher_sn='".$_SESSION['session_tea_sn']."'";
$res=$CONN->Execute($query);
$userdata=$res->FetchRow();
if ($userdata['cerno'])
	echo '<tr><td style="text-align: center; background-color: white;">憑證序號 : '.$userdata['cerno'].'</td></tr>';
else
	echo '<tr><td style="text-align: center; background-color: white; color: red;">未註冊憑證</td></tr>';

echo '</table>';

if ($msg) echo '<br><span style="color: red; font-size: 14pt;">'.$msg.'<br><br>';

echo '
<table>
<tr bgcolor="#FBFBC4">
<td><img src="'.$SFS_PATH_HTML.'images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">您必須先安裝<a href="http://www.sfs.project.edu.tw/modules/mydownloads/visit.php?cid=2&lid=47" target="new">臺中市憑證登入元件v0.4版</a>及<a href="http://gca.nat.gov.tw/download/HiCOSClient_v2.1.7.zip" target="new">HiCOS憑證管理程式</a>, 若已安裝過則不須重覆安裝</li>
<li class="small">憑證註冊只須在第一次使用或憑證更換時進行即可</li>
</ol>
</td></tr></table>
';
} else echo '<H1 style="color: red;">本功能未啟用, 詳情請洽管理者!</H1>';
foot();
?>