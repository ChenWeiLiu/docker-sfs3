<?php
exit();
?>
<html>
  <head><title>PHP OpenID Authentication Example</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="author" content="台中市教育網路中心 axer@tc.edu.tw" />
  <meta name="description" content="台中市教育網路中心OPENID嵌入網頁範例程式" />
  </head>
  <body>
    <h2>台中市教育網路中心OPENID嵌入網頁PHP範例程式<br /><small>TC Network center PHP OpenID Authentication Example</smll></h2>
    <p style="font-size:11pt;color: #555;">- 原始範例改寫自<a href="http://github.com/openid/php-openid"> PHP OpenID library</a><br />
       - 本範例配合台中市政府教育局公務帳號使用<br />
       - 認證完畢後可取回資料為<sup>1</sup>全名 <sup>2</sup>Email <sup>3</sup>學校區域 <sup>4</sup>學校名 <sup>5</sup>職稱 等。<br />
    </p>

    <div style="border-width:1px; border-color:black;  padding:3px; font-size:15px;">
      <form method="get" action="authcontrol.php">
        請輸入你的公務帳號<br />
        <input type="hidden" name="action" value="verify" />
        <input type="hidden" name="domain" value="tc" />
        <span style="color:#777;">http://<input type="text" name="openid_identifier" value="" size="12" maxlength="16" />.openid.tc.edu.tw</span>
        <input type="submit" value=" 以公務帳號登入 " />
      </form>
    </div>
    <div style="color:#CC7300; font-size:15px;"><?php if(!empty($message)) print $message;  ?></div>

  </body>
</html>
