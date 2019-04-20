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
		<li>屏東區高中職免試入學作業要點：<a href='https://sites.google.com/site/aasd123twcaq/home/jiansongxiuzhenghouzhipingdongqugaojizhongdengxuexiaomianshiruxuezuoyeyaodianqingguixiaojiaqiangxuandao' target='_BLANK'>https://sites.google.com/site/aasd123twcaq/home/jiansongxiuzhenghouzhipingdongqugaojizhongdengxuexiaomianshiruxuezuoyeyaodianqingguixiaojiaqiangxuandao</a></li>
		<li>屏東區103免試入學超額比序資料匯出模組操作說明：<a href='./103_ptc_preview.pdf' target='_BLANK'><img src='./images/on.png' border=0>預覽版</a> <a href='./103_ptc_1.0.pdf' target='_BLANK'><img src='./images/on.png' border=0>1.0版</a></li>
		<p>
		<li>屏東區104免試入學超額比序資料匯出模組操作說明：<a href='./104_ptc_1.0.pdf' target='_BLANK'><img src='./images/on.png' border=0>1.0版</a> <a href='./104_ptc_1.1.pdf' target='_BLANK'><img src='./images/on.png' border=0>1.1版(2015/01/03)</a></li>
		</p>
		<p>
		<li>屏東區105免試入學超額比序資料匯出模組操作說明：<a href='./ptc_105_1.0.pdf' target='_BLANK'><img src='./images/on.png' border=0>1.0版</a> <a href='./ptc_105_appendix.pdf' target='_BLANK'><img src='./images/on.png' border=0>學務日常模組操作說明</a></li>
		</p>
";

echo $help_doc;
foot();
?>