<?php

// $Id: help.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

//sfs_check();
   
  head("輔助說明") ;
  print_menu($menu_p);


?>
<h1>班級報名系統使用說明 </h1>
<p>報名表列冊</p>
<ul>
  <li><img src="help/input.gif" width="77" height="31">目前可以填報的項目。</li>
  <li> <img src="help/stop.gif">已停上報名，無法再進入。</li>
</ul>
<p>班級報名方法：</p>
<p>有兩種方式：</p>
<ol>
  <li>第一次報名，在快速輸入區中直接輸入學生座號，以<b>逗號(,)</b>或<b>空白</b>做分隔均可。會依輸入順序存入到各報名排序中。<br>
    <br>
    <img src="help/qinput.gif" width="602" height="41"><br>
    <br>
    <br>
  </li>
  <li>指定各排序輸入:在淺色區塊中做輸入。 
    <ul>
      <li>姓名欄處<b>建議以座號輸入</b>，會自動取得相關的資料(如學號、地址等)。</li>
      <li>其他所需欄位(例圖中&quot;次數1&quot;)，學務系統中沒有相關資料，必須自行輸入。</li>
      <li>如果要刪除原報名學生，只要把該<b>姓名欄清除</b>即可。</li>
      <li>改變他人參加，也只要在姓名欄輸入<b>新參加者座號</b>即可。</li>
    </ul>
    <br>
    <img src="help/brown.gif" width="184" height="137"><br>
    <br>
  </li>
</ol>
<ul>
  <li>自動由學務系統取得：<br>
    右方深色區塊，則是在(上述)左方姓名欄輸入座號並按下&quot;<b>報名完成</b>&quot; 鍵後會自動取得，<b>無需自已輸入</b>。<br>
    但如果在學務系統中未做詳細登錄，則這區塊資料可會有錯誤，就需要你再次輸入。<br>
    <br>
    <img src="help/blue.gif"><br>
    <br>
    <br>
  </li>
</ul>
<p>&nbsp;</p>
<hr>
<h1>管理者訊息</h1>
<p>建立報名單</p>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td width="62%"><img src="help/ad_kind.gif" width="509" height="131"></td>
    <td width="38%"> 
      <ul>
        <li>同一個報名單中可以同時有多份報名項目。</li>
        <li>當項目名稱為空白時，代表該項目清除。</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td width="62%"><img src="help/ad_data.gif" width="300" height="53"></td>
    <td width="38%"> 
      <ul>
        <li>報名後同時取得該學生的相關資料(學務系統支援的資料)</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td width="62%"><img src="help/ad_input.gif" width="375" height="77"></td>
    <td width="38%"> 
      <ul>
        <li>如果無法由學務系統得知欄位，可以在此設定，在報名時一併要求做輸入。</li>
      </ul>
    </td>
  </tr>
</table>

<?foot(); ?>