<?php
//$Id: index.php 9168 2017-11-10 00:27:40Z chiming $

require_once "config.php";
//認證
sfs_check();

//秀出網頁布景標頭
head("問題工具箱");

//主要內容
$main="";
echo $main;
print_menu($school_menu_p);

//佈景結尾
?>
<ul>
    <font color="maroon" size="4">本模組專為縣市網路中心
    </font>
    <p><font color="maroon" size="4">協助處理各校學籍問題時專用
    </font></p>
    <p><font color="maroon" size="4">學校操作人員於發生問題時，請洽詢各縣市處理中心
    </font></p>
    <p><font color="maroon" size="4">依中心人員指示方可操作本模組。
    </font></p>
    <p><font color="maroon" size="4">對sfs學籍系統</font><font color="blue" size="4"><b>資料表結構</b></font><font color="maroon" size="4">與</font><font color="blue" size="4"><b>相關性</b></font><font color="maroon" size="4">不瞭解者，
    </font></p>
    <p><font color="maroon" size="4">請勿任意操作本模組，否則恐造成資料混亂錯置。</font></p>
    <p><font color="maroon" size="4">【<a href='fix_teacher_ID.php'>全校身份證字號檢查</a>】</font></p>
    <p><font color="maroon" size="4">【<a href='fix_edukey.php'>edu_key產生</a>】</font></p>
</ul>
<p>
<?foot();
?></p>
