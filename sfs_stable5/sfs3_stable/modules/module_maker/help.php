<?php
// $Id: help.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include_once "config.php";
sfs_check();

//秀出網頁
head("模組產生器說明");
echo main_form();
foot();

function main_form(){
	global $school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	
	$main="
	$tool_bar
	<table bgcolor='#000000' cellspacing=1 cellpadding=4>
	<tr bgcolor='white'><td>
	<h3>特色：</h3>
	<ol>
	<li>可以即時產生 SFS 的標準模組的壓縮檔，壓縮檔中會包括 index.php、module-cfg.php、module.sql、config.php、author.txt、INSTALL、NEWS、README 等檔案。
	</li><br>
	<li>產生的模組解壓縮後即可在 SFS3 中使用，不需修改也沒關係。</li><br>
	<li>自動偵測資料表的資料型態，自動選擇適當的表單，此外各種表單欄位可以自行設定預設值、使用 function、或是否要使用該欄位。</li><br>
	<li>模組提供「全部列出」、「修改」、「刪除」等基本操作功能。</li><br>
	<li>亦可直接從「從介面產生模組 」來產生完整的空白模組，方便重頭開始設計。</li><br>
	</ol>
	</td></tr>
	</table>
	
	<h3>範例：</h3>
	
	<table bgcolor='#000000' cellspacing=0 cellpadding=4>
	<col>
	<col>
	<tbody>
		<tr bgcolor='white'>
		<td>		
		<p>
		首先請用 phpMyAdmin 來建立您程式的資料表。
		</p>
		</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help01.png'><img src='images/help01_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>表格設計好之後，請到「SFS3 模組產生器」中。</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help02.png'><img src='images/help02_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>選擇您剛剛自己開出來的資料表，以便從資料表來生出所需程式。</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help03.png'><img src='images/help03_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>這個步驟最重要，決定您的程式內容。
		<ul>
		<li>「<font color='darkBlue'><b>使用</b></font>」：看此欄位是否要使用。</li><br>
		<li>「<font color='darkBlue'><b>欄位名稱</b></font>」：資料庫內的欄位名稱，不可改。</li><br>
		<li>「<font color='darkBlue'><b>欄位中文名稱</b></font>」：設定該欄位在表單中的欄位中文名稱，若沒填則以英文欄位名稱代替。</li><br>
		<li>「<font color='darkBlue'><b>資料型態</b></font>」：資料表中該欄位的資料型態，不可改。</li><br>
		<li>「<font color='darkBlue'><b>表單種類</b></font>」：會自動選擇適合的 HTML 表單型態，可以自行再改。</li><br>
		<li>「<font color='darkBlue'><b>預設</b></font>」：設定該欄位的預設值。可以使用一般文字、變數（例如 teacher_sn 那一欄），也可以使用 function 作為預設值（share_sn 和 adddate，其中 share_sn 那一欄用的是自訂 function ，產生後必須自行加入該 function），欲使用函數請將「函數」的框框打勾即可。</li><br>
		<li>「<font color='darkBlue'><b>大小</b></font>」：如果是「文字輸入」表單，則可設定該表單的大小。如果是「文字區塊」，則為 textarea 的寬度。</li><br>
		<li>「<font color='darkBlue'><b>最大值</b></font>」：如果是「文字輸入」表單，則設定該表單的可以輸入的最大值。如果是「文字區塊」，則為 textarea 的高度。</li><br>
		<li>「<font color='darkBlue'><b>更新、刪除的主要索引值是</b></font>」： 設定以那一個欄位來作為修改或刪除時，主要的依據欄位，通常都是以  PRIMARY 為主。</li><br>
		<li>「<font color='darkBlue'><b>檔名：</b></font>」： 該模組的首頁檔名，不建議修改。</li>
		</ul>
		</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help04.png'><img src='images/help04_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>到這裡，其實 index.php 檔已經產生了，接下來您必須填入一些基本資料以產生其他檔案。<br>
		「<font color='darkBlue'><b>模組中文名稱 </b></font>」請填入模組中文名稱。<br>
		「<font color='darkBlue'><b>模組目錄名稱</b></font>」請填入該模組英文名稱，此名稱會作為模組的目錄名稱。</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help05.png'><img src='images/help05_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>
		<ul>
		<li>「<font color='darkBlue'><b>模組功能描述</b></font>」：這裡的值會寫入 author.txt 中。
		<li>「<font color='darkBlue'><b>安裝說明</b></font>」：這裡的值會寫入 INSTALL 中。
		<li>「<font color='darkBlue'><b>功能增修紀錄</b></font>」：這裡的值會寫入 NEWS 中。
		<li>「<font color='darkBlue'><b>讀我檔案</b></font>」：這裡的值會寫入 README 中。
		</ul>
</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help06.png'><img src='images/help06_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>送出後便會下載該檔案。</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help07.png'><img src='images/help07_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>請將該 zip 壓縮檔放到硬碟中，或者直接放到學務系統的 module 目錄下，解開就能用了。</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help08.png'><img src='images/help08_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>在 Linux 下使用 unzip 將之解開到學務系統程式的 /modules/ 中</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help09.png'><img src='images/help09_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>接著，請到系統管理的新增模組中，找到剛剛新增的模組，然後將之安裝上去就 OK 了！
		</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help10.png'><img src='images/help10_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>安裝的模組已經出現了！</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help11.png'><img src='images/help11_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>這是進去該模組的畫面，所有表單均已製作完成！馬上就可以使用！</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help12.png'><img src='images/help12_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>由於剛剛的表單設定中， share_sn 那一欄用的是自訂 function ，所以必須自行修改 index.php 中的那個 function。系統已經自動產生一個空 function，只要將之修改一下就好了。（如果沒有自訂 function 那就不需修改啦！）</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help13.png'><img src='images/help13_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>自行修改 function 內容。</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help14.png'><img src='images/help14_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>輸入一筆資料</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help15.png'><img src='images/help15_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>輸入結果</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help16.png'><img src='images/help16_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>修改資料（使用複選）。</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help17.png'><img src='images/help17_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>修改結果</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help18.png'><img src='images/help18_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
		<tr bgcolor='white'>
		<td>刪除資料</td>
		</tr>
		<tr bgcolor='white'>
		<td><a href='images/help19.png'><img src='images/help19_1.png'  border=0></a><p>&nbsp;</p></td>
		</tr>
	</tbody>
	</table>	<p>
	簡單吧！</p>
	";
	return $main;
}
?>
