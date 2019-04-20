<?php
require "config.php";

//使用者認證
sfs_check();

head("停止維護公告");


echo "<br><br><p align='center' style='font-size: 16pt;'>本模組業經官方網站宣告為「停止維護」的模組，<br><br>各校使用前請先確定符合「國民小學及國民中學常態編班及分組學習準則」、「縣市政府規定」與「學校需求」，<br><br>使用學校須自行擔保編班結果的「信度」與「效度」。</p>";

echo "<br><br><p align='center'><a href='continue.php'>確定要繼續使用請按此！</a></p>";

//程式檔尾
foot();

?>

