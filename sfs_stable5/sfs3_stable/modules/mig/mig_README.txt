/*
** 原作者，詳見 doc/readme.txt	
** MiG - A general purpose photo gallery management system.
** Copyright (C) 2000 Daniel M. Lowe	<dan@tangledhelix.com>

**	數位相本說明

**	修改 MiG0.98 版 
        管理界面作者
        (sfs_mig2.0) HaMi(cik@mail.wpes.tcc.edu.tw)        
                     lmw (lmw@sces.tcc.edu.tw)
                
**	提供網址：校園自由軟體交流網 (http://sfs.wpes.tcc.edu.tw)
**	本程式提供校園學務管理使用，採GNU/GPL授權方式散佈。
**	本程式管理認證部份，需配合校務系統(參考 http://sfs.wpes.tcc.edu.tw/demo/)。
**	使用平台環境 Linux + Apache + php4.x
**	使用資料庫 MySQL
**	
**	
*/

設定說明:
參照  mig_config.php 檔說明


注意事項:
1.本版須用到 convert 這個縮圖程式，不同的 Linux(FreeBSD) 版本，其路徑不同，
利用 whereis convert 指令查看，再將值設定在 $convert_path 這個變數上。

2.本版支援 zip 格式上傳，並自動解壓縮及做縮圖動作，如你的系統無法以
zip 格式上傳，需檢查系統有無 unzip 程式及 apache 執行者是否具有權限執行。

3.本版配合 sfs 認證，支援多人使用管理自己的相片檔，系統管理者可以下列方式，
獲得所有管理權：

進入 系統管理 > 學務程式設定 > 使用權管理 
授權管理選項 點選 "授權管理「數位相本」的人員設定 "
