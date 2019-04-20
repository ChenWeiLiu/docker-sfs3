<?php
// $Id: chk_account.php 5310 2009-01-10 07:57:56Z hami $
/*引入學務系統設定檔*/
include "config.php";

//使用者認證
sfs_check();

//程式檔頭
head("檢核表填寫說明");

print_menu($school_menu_p);

//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc>
<tr><td bgcolor='#FFFFFF'>";

//網頁內容請置於此處
echo "
		<table width=100%>
		<tr bgcolor='#FBFBC4'><td><img src='../score_input/images/filefind.png' width=16 height=16 hspace=3 border=0>檢核表由來</td></tr>
		<tr><td style='line-height: 150%;'>
				<ol>
				<li>檢核表是依據教育部95年5月10日0950064510號函辦理，日常生活評量不作等第轉化，不打成績，只用文字描述。</li>
				<li>各縣市教育局大多將教育部的參考範例直接轉發給各校。</li>
				</ol>
				</td></tr>
				<tr><td></td></tr>
		</table>";        
echo "
		<table width=100%>
		<tr bgcolor='#FBFBC4'><td><img src='../score_input/images/filefind.png' width=16 height=16 hspace=3 border=0>檢核表實施年級</td></tr>
		<tr><td style='line-height: 150%;'>
				<ol>
				<li>95學年度起小一及國一新生逐年實施。</li>
				</ol>
				</td></tr>
				<tr><td></td></tr>
		</table>";        
echo "
		<table width=100%>
		<tr bgcolor='#FBFBC4'><td><img src='../score_input/images/filefind.png' width=16 height=16 hspace=3 border=0>檢核表項目訂定</td></tr>
		<tr><td style='line-height: 150%;'>
				<ol>
				<li>檢核表中「日常行為表現」項目可由各校依需要自行訂定，每學期需由學務（訓導）處做好項目設定，老師方能填寫。</li>
				</ol>
				</td></tr>
				<tr><td></td></tr>
		</table>";        
echo "
		<table width=100%>
		<tr bgcolor='#FBFBC4'><td><img src='../score_input/images/filefind.png' width=16 height=16 hspace=3 border=0>檢核表填寫建議</td></tr>
		<tr><td style='line-height: 150%;'>
				<ol>
				<li>因為在「學生電子學籍交換標準3.0版」中，並沒有對「表現狀況」欄留欄位，且檢核表可由學校自訂。所以在減低學校老師負擔的前提下，建議<font color=red>列印時採用簡式檢核表</font>，填寫時即可將表現狀況忽略不勾選，具體建議欄則可�秣u填寫（至少填一欄）。</li>
				<li>「具體建議欄」填寫時請根據學生日常表現狀況，依據檢核項目，酌予提供具體建議不作綜合性評價（請勿再給予四字或八字的簡略評語）。</li>
				<li>「團體活動表現」、「公共服務」、「校內外特殊表現」各欄屬事實紀錄，有則填寫，無則留空。</li>
				</ol>
				</td></tr>
				<tr><td></td></tr>
		</table>";        

//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();
?>
