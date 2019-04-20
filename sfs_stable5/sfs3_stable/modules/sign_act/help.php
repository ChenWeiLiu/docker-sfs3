<?php

// $Id: help.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

//sfs_check();
   
  head("輔助說明") ;
  print_menu($menu_p);


?>
<h1>校際隊伍報名系統使用說明 </h1>
<h3>登入系統</h3>
<blockquote> 
  <p>輸入學校及密碼，請務必查看下方的說明訊息。<br>
    <img src="help/login.png"></p>
</blockquote>
<h3>新增一筆</h3>
<blockquote> 
  <p>輸入所需的資料(姓名等欄位)</p>
  <p>第一次輸入時，一定要設定自已的密碼。</p>
  <p><img src="help/add.png" width="489" height="190"></p>
</blockquote>
<p>已報名的隊伍資料</p>
<blockquote> 
  <p><img src="help/list.png" width="474" height="101"></p>
</blockquote>
<h3>修改資料</h3>
<blockquote> 
  <p>點選要修改隊伍。</p>
  <p><img src="help/b_edit.png" width="66" height="64"></p>
  <p>出現該隊的資料以供修改。</p>
  <p><img src="help/edit.png" width="494" height="205"></p>
</blockquote>
<h3>刪除資料</h3>
<blockquote> 
  <p>注意，按下刪除即無法再救回。</p>
  <p><img src="help/b_del.png" width="67" height="61"></p>
  <p>&nbsp;</p>
</blockquote>
<p>&nbsp;</p>
