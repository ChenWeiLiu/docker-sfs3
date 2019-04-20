<?php
//$Id: mag_doc.php 5439 2009-03-18 08:57:51Z wkb $
  include_once( "config.php" );
  head("電子校刊使用辦法說明");  
  print_menu($m_menu_p);
?>
<body bgcolor="#FFFFFF" text="#000000">
<h2>電子校刊上傳說明：</h2>
<h3>學生上傳： </h3>
<table width="80%" border="1" cellspacing="0" cellpadding="4" align="center">
  <tr bgcolor="#66FF66"> 
    <td width="74%">第一次上傳</td>
    <td width="26%">補充說明</td>
  </tr>
  <tr> 
    <td width="74%">1. 進入 學務系統 > 校務行政 > 電子校刊</td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1">&nbsp; 
      </font></td>
  </tr>
  <tr> 
    <td width="74%">2.選擇單元(文章或班級點滴等)</td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1">一定要選擇正確的單元</font></td>
  </tr>
  <tr> 
    <td width="74%">3.點選&lt;<font color="#3333FF"><b>學生第一次上傳</b></font>&gt;</td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1">&nbsp;  </font></td>
  </tr>
  <tr> 
    <td width="74%">4.輸入密碼，按下送出鍵。</td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1">密碼公佈在最新消息中</font></td>
  </tr>
  <tr> 
    <td width="74%"> 
      <p>5.輸入各欄位資料(題目、姓名、文章等)，而且要<font color="#FF0000">設定一個新密碼</font>，做為下次修改自已文章時使用。<br>
        按下確定上傳。 </p>
      </td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1">文章可由文書軟體中使用複製剪貼到文字框中。</font></td>
  </tr>
  <tr> 
    <td width="74%">6.點選自已文章標題，查看內容。</td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1"> &nbsp; </font></td>
  </tr>
</table>
<blockquote>
  <p><font color="#FF3333">文章上傳過後，不要再重覆上傳</font>，要作修改訂正錯字時，請看以下步驟：</p>
</blockquote>
<table width="80%" border="1" cellspacing="0" cellpadding="4" align="center">
  <tr bgcolor="#66FF66"> 
    <td width="74%">修改原先資料</td>
    <td width="26%">補充說明</td>
  </tr>
  <tr> 
    <td width="74%">1. 進入 學務系統 > 校務行政 > 電子校刊</td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1">&nbsp; </font></td>
  </tr>
  <tr> 
    <td width="74%">2.點選自已文章右方的&lt;<font color="#3333FF"><b>重新上傳</b></font>&gt;</td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1">&nbsp;  </font></td>
  </tr>
  <tr> 
    <td width="74%">3.輸入密碼，按下送出鍵。</td>
    <td width="26%" bgcolor="#CCCCCC"> 
      <p><font color="#3333FF" size="-1">這個密碼是你第一次上傳時，自定設定的密碼</font></p>
      <p><font color="#3333FF" size="-1">如果遺忘，請和資訊組聯絡。</font></p>
    </td>
  </tr>
  <tr> 
    <td width="74%">4.修改各欄位資料，按下確定修改。</td>
    <td width="26%" bgcolor="#CCCCCC">&nbsp;  </td>
  </tr>
  <tr> 
    <td width="74%">5.點選自已文章標題，查看內容。</td>
    <td width="26%" bgcolor="#CCCCCC"><font color="#3333FF" size="-1">&nbsp;  </font></td>
  </tr>
</table>
<p>&nbsp;</p>
<h3>編輯人員：</h3>
<ul>
  <li>需要做登入的動作，才能做身份判斷。</li>
  <li>選擇所負責單元。</li>
  <li>文章審稿，請依所負責年段文章，點選&lt;<font color="#0000FF">審稿</font>&gt;，直接做修改動作。查看完後，<font color="#0000FF">不論有無修改，要按下確定修改鍵</font>，才會出現已審查的圖示，方便其他編輯人員了解該篇是否已被審查過。<br>
    也可以先點選上方&lt;<font color="#0000FF">文章列出</font>&gt;，把該年級文章列印出來做書面查看。</li>
  <li>美勞作品等拍攝作品，請選&lt;<font color="#0000FF">編輯群上傳</font>&gt;直接傳送圖檔資料。<br>
    如果要再重傳，則點選&lt;<font color="#0000FF">審稿</font>&gt;，可以直接修改要上傳的圖檔。</li>
</ul>
<h3>期別管理人員</h3>
<ol>
  <li>&lt;<strong>期別管理</strong>&gt;中，新增一期，其中<strong>編輯人員</strong>以學務系統中的代號填入。指定上傳密碼，做為學生上傳作品時所需。</li>
  <li><strong>設定單元</strong>，指定各種類型如作文、繪圖、班級訊息、網頁等。而網頁類型要在此上傳，多檔案網頁內容則先壓縮zip後再上傳。</li>
  <li>在編輯人員全部審稿完成後，到<strong>期別管理</strong>期別修改，標記為完成，就無法再做上傳、更改。在完成前可以由編輯中校刊中查看雛形。</li>
  <li>期別管理<strong>列出作品學生名單</strong>，會以csv格式匯出名單。<strong>清理垃圾埇</strong>，外把編輯審稿中標記為刪除的作品真正的刪除。</li>
  <li>期別管理中<strong>刪除</strong>功能，是提供管理者在還沒有完成校刊時，可以將不需要用的期別刪除，如果刪除後，相關上傳的資料也都真正的刪除，請小心!!</li>
</ol>
<?
foot();
?>