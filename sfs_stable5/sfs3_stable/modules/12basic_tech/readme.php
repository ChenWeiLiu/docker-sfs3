<?php

include "config.php";
sfs_check();

//秀出網頁
head("使用說明");

//橫向選單標籤
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

$help_doc="<br><br>
<li>教育部十二年國民基本教育資訊網：<a href='http://12basic.edu.tw/' target='_BLANK'>http://12basic.edu.tw/</a></li>
	<li>五專免試入學作業要點：<a href='http://me.moe.edu.tw/junior/index.php' target='_BLANK'>http://12basic.edu.tw/Detail.php?LevelNo=480</a></li>
	<li>五專招生資訊網：<a href='http://me.moe.edu.tw/junior/index.php' target='_BLANK'>http://me.moe.edu.tw/junior/index.php</a></li>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>五專103免試入學超額比序模組使用說明：<span lang='EN-US'> <a href='./103_tech_1.0.pdf' target='_BLANK'><img src='./images/on.png' border=0>1.0版</a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
";

echo $help_doc;
foot();
?>