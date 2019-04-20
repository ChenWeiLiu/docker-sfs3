<?php
//載入設定檔
require("config.php") ;

// 認證檢查
sfs_check();
$_reUpgrade = 1;

head() ;  

print_menu($menu_p);
?>
<h1>重新升級資料表</h1>
<div style="margin:10px;padding:10px; background: #af6">
<p>Q : 為什麼要重新升級資料表?</p>
<p>A : 因系統調整了畢業生資料表結構, 如貴校系統沒有正常顯示畢業生名冊, 請重新操作此步驟升級</p>
</div>
<?php 
require "module-upgrade.php";
