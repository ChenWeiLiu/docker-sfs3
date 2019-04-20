<?php
// $Id: readme.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
require "config.php";
if($_GET['class_year_b']) $class_year_b=$_GET['class_year_b'];
else $class_year_b=$_POST['class_year_b'];

//使用者認證
sfs_check();

//程式檔頭
head("新生編班");

print_menu($menu_p);
//設定主網頁顯示區的背景顏色
echo "
<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
<tr>
<td bgcolor='#FFFFFF'>";
//網頁內容請置於此處

echo "<table>
        <tr bgcolor='#FBFBC4'><td><img src='images/filefind.png' width=16 height=16 hspace=3 border=0>匯入新生相關說明</td></tr>
	    <tr><td style='line-height: 150%;'>
            <ol>
            <li>選擇您所要匯入的年級。</li>
			<li>按瀏覽選擇您所要匯入的檔案。</li>
            <li>檔案的格式請參照<a  href='newstud.csv'>newstud.csv</a></li>
			<li>最後再按『批次建立資料』！</li>
            </ol>
            </td></tr>
            <tr><td></td></tr>
      </table>";        
echo "<table>
        <tr bgcolor='#FBFBC4'><td><img src='images/filefind.png' width=16 height=16 hspace=3 border=0>管理新生相關說明</td></tr>
	    <tr><td style='line-height: 150%;'>
            <ol>
			<li>首先選擇年級和工作項目</li>
			<li>工作項目有『新生基本資料』，『是否就讀本校』，『成績輸入』，『調整編班』等四項。</li>
			<li>剛匯入的新生資料，建議先做『是否就讀本校』確認。</li>
			<li>成績輸入，是提供給有需要依成績來作為自動編班依據的學校建立成績用的，並不一定輸入。</li>
			</ol>
            </td></tr>
            <tr><td></td></tr>
      </table>";
echo "<table>
        <tr bgcolor='#FBFBC4'><td><img src='images/filefind.png' width=16 height=16 hspace=3 border=0>自動編班相關說明</td></tr>
	    <tr><td style='line-height: 150%;'>
            <ol>
			<li>首先選擇班級和編班依據</li>
			<li>在班群欄位輸入每一班群的班級數，當然您也可以只有一個班群。若您需要一某些特質（就是您剛才選的編班依據）來將該年級分群，就可以於班群欄位做設定</li>
			<li>設定學號原則，目前只提供入學年度在加上您自訂的位數，如您自訂位數為4的話，則學號就是920001,920002,920003......</li>
			<li>請選擇學號排序的依據</li>
			<li>最後請按『開始編班』按鈕</li>
			<li>程式會進行自動編班，並切換至『調整編班畫面』讓您可以立即去做編班的修正</li>						
            </ol>
            </td></tr>
            <tr><td></td></tr>
      </table>";
echo "<table>
        <tr bgcolor='#FBFBC4'><td><img src='images/filefind.png' width=16 height=16 hspace=3 border=0>寫入學籍資料表相關說明</td></tr>
	    <tr><td style='line-height: 150%;'>
            <ol>
			<li>首先選擇年級</li>
			<li>再按『寫入正式學籍資料表』，即可</li>						
            </ol>
            </td></tr>
            <tr><td></td></tr>
      </table>";	  	  
echo "<table>
        <tr bgcolor='#FBFBC4'><td><img src='images/filefind.png' width=16 height=16 hspace=3 border=0>報表列印相關說明</td></tr>
	    <tr><td style='line-height: 150%;'>
            <ol>
			<li>請由左方選單選擇學年度和班級</li>
			<li>此時左方畫面會出該班的新生名冊，表格上方有『下載SXW檔』連結，按此即可下載該班的新生名冊，再以openofficec的writer開啟即可！</li>						
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
