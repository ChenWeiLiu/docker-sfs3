這是一個以SFS3校務佈告欄模組的資料以JSON的形式交換

需分兩個部份安裝, server 端及 client端

Server:
1. httpd.conf 的AddCharset 需註解掉, 系統需支援 php-pdo-mysql
2.將server/modules/json/jsonBoard  複製到SFS3 modules裡
一樣對應到 sfs3/modules/json/jsonBoard
這個位置會對應到 clinet/page.php 
第六行的 $sfs3BoardUrl = "http://163.17.39.135/modules/json/jsonBoard/index.php";



Client:
1.將client資料夾放置將做為公開服務的web server上

2.更改 page.php 第五行 $downloadBaseUrl = "http://163.17.39.135/data/school/board/";
為SFS3的文件下載路徑
client server 需支援 php curl

3.download 設為777   

