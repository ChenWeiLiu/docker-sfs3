<?php

// $Id: help.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

//sfs_check();
   
  head("輔助說明") ;
  print_menu($menu_p);


?>
<h1>學生競賽榮譽榜使用說明 </h1>

<p>本系統提供學生參加校內、外各項競賽榮譽榜的登錄、列印獎狀、公告、個人得獎紀錄統計等功能。</p>

<p>一、新增榮譽榜</p>

<p>1.請依序輸入各欄位資料：</p>

<p><img border="0" src="help/admin.gif" width="784" height="503"></p>

<p>二、榮譽榜列表及管理</p>
<p>1.在填報期限內可填報及處理列印。</p>
<p>2.超過期限則無法填報及處理，只能查看名單。</p>
<p>3.可修改設定把填報結束日期改到今天即可再填報或處理。</p>
<p>4.系統自動依開始填報日期排序。</p>
<p>5.只列出最新的二十筆，如舊資料需修正，請查出其ID，在網址列修改即可進入。 </p>
<p>三、登錄榮譽榜</p>
<p>1.管理者可處理所有班級，級任只能輸入自己的班級名單。</p>
<p>2.選擇班級後，會出現全班學生名單，請先勾選學生，再點選成績項目，最後按確定新增。</p>
<p><img border="0" src="help/in.gif" width="832" height="562"></p>
<p>四、管理及列印</p>
<p>1.在填報期限內的榮譽榜才可進行管理。</p>
<p>2.校內競賽可列印獎狀，格式為A4橫書，請先印好花邊及關防，其餘內容則由系統套印。</p>
<p>3.校長簽名章及學生照片可先上傳至系統，可以直接列印。</p>
<p>4.另有B5直印格式獎狀，也可自行設計。</p>
<p><img border="0" src="help/print.gif" width="847" height="546"> </p>
<p>五、公告榮譽榜&nbsp;&nbsp;&nbsp;&nbsp; </p>
<p>1.&nbsp;&nbsp;&nbsp; list.php：榮譽榜目錄，依分類顯示，可在學校首頁建立連結。</p>
<p>2.&nbsp;&nbsp;&nbsp; view.php：各榮譽榜內容，點選學生可進入個人榮譽榜列表。</p>
<p>3.&nbsp;&nbsp;&nbsp; show.php：個人榮譽榜列表。</p>
<p>參考網址<a href="http://163.19.178.245/sfs3/modules/cita/list.php"> 
http://163.19.178.245/sfs3/modules/cita/list.php</a></p>
<p>六、相關程式</p>
<p>1.各階段成績考查各班前六名名單可自動匯入榮譽榜。modules/score_manage_new/top.php</p>
<p>2.學期成績各班前六名名單可自動匯入榮譽榜。modules/stud_top/stud_top.php</p>


<?foot(); ?>