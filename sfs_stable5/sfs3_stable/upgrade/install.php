<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN
		"http://www.w3.org/TR/html4/loose.dtd"">
<html>
<head>
  <title>檢查系統設定</title>
  <meta http-equiv="Content-Type" content="text/html; charset=Big5">
  <meta name="GENERATOR" content="Quanta Plus">
</head>
<body>
  
<h3>檢查 include/config.php 的設定是否正確,再進行升級</h3>
<table bgcolor="#cccccc">
  <tbody>
    <tr>
      <td>
<pre>

/**********************************
 系統設定
***********************************/
//程式根目錄 PATH
<font color="red">$SFS_PATH = "";</font>

//學務管理首頁程式 URL (設定時，保留最後的 "/" )
<font color="red">$SFS_PATH_HTML ="";</font> 

//學校首頁 URL
<font color="red">$HOME_URL ="";</font>

/**********************************
  MYSQL 連接設定
***********************************/
// mysql 主機
<font color="red">$mysql_host ="localhost";</font>

// mysql 使用者
<font color="red">$mysql_user ="root";</font>

<font color="red">// mysql 密碼
$mysql_pass ="";
</font>
// 資料庫名稱
<font color="red">$mysql_db   ="sfs3";</font>

</pre>
 
 </td>
    </tr>
  </tbody>
</table>
</body>
</html>