/*
** 原作者，詳見 doc/readme.txt	
** MiG - A general purpose photo gallery management system.
** Copyright (C) 2000 Daniel M. Lowe	<dan@tangledhelix.com>

**	數位相本說明

**	修改 MiG0.98 版 
        管理界面作者
        HaMi(cik@boe.tcc.edu.tw)        
                
**	提供網址：校園自由軟體交流網 (http://sfs.wpes.tcc.edu.tw)
**	本程式提供校園學務管理使用，採GNU/GPL授權方式散佈。
**	本程式管理認證部份，需配合校務系統(參考 http://sfs.wpes.tcc.edu.tw/demo/)。
**	使用平台環境 Linux + Apache + php4.x
**	使用資料庫 MySQL
**	
**	
*/
已知程式 bug 

1.中文目錄名稱如有 "四" 無法利用管理界面刪除目錄，
  必須進入 shell 中下指令才可刪除，各位網友如發現解決方法，
  請 mail 給我 cik@boe.tcc.edu.tw ，謝謝!



安裝說明： (以下安裝目錄提供參考，可自行修改)


1.解開程式檔案至 學務管理目錄
---------------------------------------
	cp sfsmig0.1.tar.gz /home/httpd/www/sfs
	cd /home/httpd/www/sfs
	tar zxvf sfsmig0.1.tar.gz
  
3.更改 mig.cfg 檔案
---------------------------------------
	pico /home/httpd/www/sfs/mig/mig.cfg
	
4.修改 albums 屬性 (圖檔存放位置)
---------------------------------------
	chmod 777 /home/httpd/www/sfs/mig/albums

5.加入 使用權限
---------------------------------------
	進入學務程式管理系統 新增一個類別 並加上管理權限。
		http://yourpath/sfs/admin/prog_kind_list.php
		
     
6.測試
---------------------------------------
 http://yourpath/sfs/mig/
