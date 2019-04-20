<?php
// $Id: help.php 6032 2010-08-25 09:33:51Z infodaes $

include "config.php";
sfs_check();

//秀出網頁
head("使用說明");

//橫向選單標籤
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

$help_doc="<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>教育部十二年國民基本教育資訊網：<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' href='http://12basic.edu.tw/' target='_BLANK'><span style='color: #0000CC; text-decoration: none'>http://12basic.edu.tw/</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>中投區高中職免試入學作業要點：<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' href='http://docs.google.com/a/tc.edu.tw/viewer?a=v&pid=sites&srcid=dGMuZWR1LnR3fHRjMTJleHBsYWlufGd4Ojg3MjRhZmQxYTI2MmVmMQ' target='_BLANK'><span style='color: #0000CC; text-decoration: none'>http://docs.google.com/a/tc.edu.tw/viewer?a=v&pid=sites&srcid=dGMuZWR1LnR3fHRjMTJleHBsYWlufGd4Ojg3MjRhZmQxYTI2MmVmMQ</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>中投區103免試入學超額比序模組操作說明：<span lang='EN-US'> <a href='./103_tcntc_1.0.pdf' target='_BLANK'><img src='./images/on.png' border=0>1.0版</a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<hr>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>2/27新增103中投區免試入學招生檔匯出格式功能，無法正確輸出請先調整php.ini max_execution_time設定。</span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>3/3 增加自動判斷原住民是否通過族語認證功能，使用前須先取回模組變數預設值並設定好native_id(原住民代號)、native_language_sort(族語認證屬性記載順位)、native_language_text(通過族語認證標記文字)</span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>3/4 增加模組變數讓學校依照學校指定聯絡資料輸出來源 (未指定或未取回模組變數則依據原先規則由程式判定)</span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
";
echo $help_doc;
foot();
?>