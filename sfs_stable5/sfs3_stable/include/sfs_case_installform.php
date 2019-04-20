<?php

// $Id: sfs_case_installform.php 5351 2009-01-20 00:39:21Z brucelyc $
// 取代 intall_form.php

$sfsname="sfs3";
$input_width="100%";
$delimit_color="#E5E5E5";
$sfs_url="http://sfs.wpes.tcc.edu.tw";


$sfshostname=$_SERVER['HTTP_HOST'];
$dirname=dirname($_SERVER['SCRIPT_NAME']);

$SFS_INSTALL_URL="http://".$sfshostname.$dirname;
if(substr($SFS_INSTALL_URL,-1,1)=="\\")$SFS_INSTALL_URL=substr($SFS_INSTALL_URL,0,-1);
if(substr($SFS_INSTALL_URL,-1,1)!="/")$SFS_INSTALL_URL.="/";


//$SFS_INSTALL_PATH=$_SERVER['DOCUMENT_ROOT'];
$Install_Path=$_SERVER['SCRIPT_FILENAME'];
$Install_dirName= pathinfo($Install_Path);
$SFS_INSTALL_PATH=$Install_dirName[dirname];

$SCHOOL_INSTALL_URL="http://".$sfshostname."/";
$GIP=explode(".",$_SERVER['SERVER_ADDR']);
$SCHOOL_IP=$GIP[0].".".$GIP[1].".".$GIP[2];
//$UPDATA_PATH={$_SERVER['DOCUMENT_ROOT']}."/$UPLOAD_PATH/";
$UPDATA_PATH=$SFS_INSTALL_PATH."/data/";
$sfsnameman=$sfsname."man";

echo <<<HERE
<html>
<head>
<script language="JavaScript">
<!--
function CHECK() {
  var chkf=document.inst.SFS_PATH.value;
  if (chkf.length==0) { document.inst.SFS_PATH.focus(); alert("程式根目錄路徑不能空白"); return false; }

  var chkf=document.inst.SFS_PATH_HTML.value;
  if (chkf.length==0) { document.inst.SFS_PATH_HTML.focus(); alert("學務管理首頁程式URL不能空白"); return false; }

  var chkf=document.inst.HOME_URL.value;
  if (chkf.length==0) { document.inst.HOME_URL.focus(); alert("學校首頁URL不能空白"); return false; }

  var chkf=document.inst.HOME_IP.value;
  if (chkf.length==0) { document.inst.HOME_IP.focus(); alert("學校IP範圍不能空白"); return false; }

  var chkf=document.inst.mysql_host.value;
  if (chkf.length==0) { document.inst.mysql_host.focus(); alert("mysql主機不能空白"); return false; }

  var chkf=document.inst.mysql_adm_user.value;
  if (chkf.length==0) { document.inst.mysql_adm_user.focus(); alert("mysql管理者不能空白"); return false; }

  var chkf=document.inst.mysql_adm_pass.value;
  if (chkf.length==0) { document.inst.mysql_adm_pass.focus(); alert("mysql管理者密碼不能空白"); return false; }

  var chkf=document.inst.mysql_user.value;
  if (chkf.length==0) { document.inst.mysql_user.focus(); alert("mysql使用者不能空白"); return false; }

  var chkf=document.inst.mysql_pass.value;
  if (chkf.length==0) { document.inst.mysql_pass.focus(); alert("mysql使用者密碼不能空白"); return false; }

  var chkf=document.inst.mysql_db.value;
  if (chkf.length==0) { document.inst.mysql_db.focus(); alert("資料庫名稱不能空白"); return false; }

}
-->

</script>
<title>SFS3.0 學務管理系統安裝程式 ".$Install_Path."</title>
    <style type='text/css'>
    body,td{font-size: 12px}
    .small{font-size: 12px}
    </style>
</head>
<body bgcolor="white">
<div style="color:white ;font-size: 30px">
<b>SFS3.0 學務管理系統安裝程式</b>
</div>
<p></p>
<br>
<form name="inst" method="post" action="$_SERVER[SCRIPT_NAME]" onSubmit="return CHECK()">
<table border=0 align="center" cellspacing="0" cellpadding="1" bgcolor="#E5E5E5">
<tr><td>

<table border=0 align="center" cellspacing="0" cellpadding="3" bgcolor="#E5E5E5">
<tr bgcolor="#1E3B89">
<td colspan="4" nowrap>
<img src="images/logo.png" width="16" height="16" hspace="3" align="middle" border="0">
<font color="white">資料庫以及系統路徑相關設定</font></td>
</tr>

<tr bgcolor="$delimit_color">
<td rowspan="13" width=170 background="images/install_bg.jpg" style="size: 12px; color:white;line-height: 130%; " valign="top">
<font color="yellow"><b>SFS3.0 學務系統安裝說明</b></font>
<p>
需提供MySQL 管理者的帳號密碼，以便自動建立資料庫，僅於安裝時使用。
</p>
<p>請自行設定一組『MySQL 使用者』的帳號密碼，以便日後學務系統可以連接資料庫。</p>
<p>
其他欄位，系統會自動偵測產生，請自行查看修改。
</p>
<p>
<img src="images/whatsnext.png" width=16 height=16 border=0>：有相關需注意事項。
<br>
<img src="images/help.png" alt="說明" width=16 height=16 border=0>：有相關說明。
</p>
<p>
安裝後，除MySQL 管理者的帳號密碼以外，所有參數均會紀錄在include/config.php中。
</p>
</td>
<td><img src="images/checkboxdown.png" width=13 height=14 border=0></td><td nowrap>MySQL 資料庫主機位置</td>
<td><input type="text" name="mysql_host" value="localhost" style="width: $input_width"></td></tr>

<tr>
<td><img src="images/checkboxdown.png" width=13 height=14 border=0></td>
<td nowrap>MySQL 管理者帳號</td>
<td>
<input type="text" name="mysql_adm_user" value="root" size="15" style='background-color: #fbec8c;'>
密碼：<input type="password" name="mysql_adm_pass" value="" size="15" style='background-color: #fbec8c;'>
</td></tr>

<tr bgcolor="$delimit_color">
<td><img src="images/checkboxdown.png" width=13 height=14 border=0></td>
<td nowrap>建立 MySQL 使用者</td>
<td><input type="text" name="mysql_user" value="$sfsnameman" size="15">
密碼：<input  type="password" name="mysql_pass" value="" size="15">
</td></tr>

<tr bgcolor="$delimit_color">
<td><img src="images/checkboxdown.png" width=13 height=14 border=0></td>
<td nowrap>資料庫名稱</td>
<td><input type="text" name="mysql_db" value="$sfsname" style="width: $input_width">
</td></tr>


<tr bgcolor="$delimit_color">
<td><img src="images/checkboxdown.png" width=13 height=14 border=0>
</td><td nowrap>貴校為國中或國小？</td>
<td><input type="radio" name="SFS_JHORES" value="1" checked>國小 &nbsp; &nbsp; <input type="radio" name="SFS_JHORES" value="2">國中 &nbsp; &nbsp; <input type="radio" name="SFS_JHORES" value="3">高中職以上
</td></tr>

<!--安裝前準備動作-->

<tr bgcolor="$delimit_color"><td><img src="images/checkboxdown.png" width=13 height=14 border=0></td>
<td nowrap>學務管理系統的 URL
<a href="javascript:alert('注意！！\\nURL 結尾要有 /')"><img src="images/whatsnext.png" width=16 height=16 border=0></a>
</td>
<td><input type="text" name="SFS_PATH_HTML" value ="$SFS_INSTALL_URL" style="width: $input_width"></td>
</tr> 

<tr bgcolor="$delimit_color"><td><img src="images/checkboxdown.png" width=13 height=14 border=0></td>
<td nowrap>學校首頁 URL</td>
<td><input type="text" name="HOME_URL" value="$SCHOOL_INSTALL_URL" style="width: $input_width"></td>
</tr>

<tr bgcolor="$delimit_color"><td><img src="images/checkboxdown.png" width=13 height=14 border=0></td>
<td nowrap>學校IP 範圍
<a href="javascript:alert('多組 IP 範圍設定，例：\\n163.17.169,\\n163.17.168.1-163.17.169.128,\\n163.17.40.1')"><img src="images/help.png" alt="說明" width=16 height=16 border=0></a>
</td>
<td><input type="text" name="HOME_IP" value="$SCHOOL_IP" style="width: $input_width"></td>
</tr>
<tr bgcolor="$delimit_color"><td><img src="images/checkboxdown.png" width=13 height=14 border=0></td>
<td nowrap>程式根目錄路徑</td>
<td>
<input type="text" name="SFS_PATH" value="$SFS_INSTALL_PATH" style="width: $input_width"></td></tr>

<!-- 預留將來使用
<tr><td nowrap>是否使用九年一貫年級制? </td>
<td><input type="radio" name="SFS_ALL9INONE" value="1" checked>是 &nbsp; &nbsp; <input type="radio" name="SFS_ALL9INONE" value="0">否</td></tr>
-->

<!-- default 即可 -->
<!--以下設定使用預設值即可(記得目錄要手動開設)-->
<tr bgcolor="$delimit_color"><td><img src="images/checkboxdown.png" width=13 height=14 border=0></td>
<td nowrap>上傳目錄的絕對路徑
<a href="javascript:alert('注意！！\\nURL 結尾要有 /')"><img src="images/whatsnext.png" width=16 height=16 border=0></a></td>
<td><input type="text" name="UPLOAD_PATH" value="$UPDATA_PATH" style="width: $input_width"></td></tr>

<tr bgcolor="$delimit_color"><td><img src="images/checkboxdown.png" width=13 height=14 border=0><td nowrap>
上傳目錄的別名 (alias)
</td><td><input type="text" name="UPLOAD_URL" value="/upfiles/" style="width: $input_width"></td></tr>

<tr bgcolor="$delimit_color"><td><img src="images/checkboxdown.png" width=13 height=14 border=0><td nowrap>上學期開始月份：<input type="text" name="SFS_SEME1" value="8" size="2"></td>
<td nowrap>下學期開始月份：<input type="text" name="SFS_SEME2" value="2" size="2"></td></tr>


<tr>
<td colspan="4" nowrap align="right">
<input type="hidden" name="installsfs" value="yes_do_it_now">
<br>
<input type="submit" value="開始安裝">
</td>
</tr>

</table>
</td></tr></table><br>

</form>
</body>
</html>

HERE

?>
