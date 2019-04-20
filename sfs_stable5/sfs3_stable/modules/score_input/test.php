<?php
// $Id: test.php 5310 2009-01-10 07:57:56Z hami $
/*引入學務系統設定檔*/
include "config.php";

//使用者認證
sfs_check();

//程式檔頭
head("成績列表");

print_menu($menu_p);

//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc>
<tr><td bgcolor='#FFFFFF'>";

//網頁內容請置於此處
echo "<table>
        <tr bgcolor='#FBFBC4'><td><img src='images/filefind.png' width=16 height=16 hspace=3 border=0>平時成績相關說明</td></tr>
	    <tr><td style='line-height: 150%;'>
            <ol>
            <li>平時成績會依照登入帳號的老師所任教且必須考試的科目列出選單提供選擇。</li>
            <li>點選你所要管理的班級與科目，程式會立即判斷目前你可能所要管理的階段成績，若是不合需求，請直接選擇你所要管理階段成績。</li>
            <li>要新增一次的平時成績，請按<span class='like_button'>新增一次平時考成績</span>，程式會自動命名，當然你也可以修改它。</li>
            <li>要輸入成績請按<img src=images/pen.png>，輸入完一次的成績請按儲存再切換到另一次的成績</li>
            <li>要刪除該次成績請按<img src=images/del.png>，或是將加權設為0</li>
            <li>加權數預設為1，你也可以自行修改，以增減該次考試的比重。</li>
            <li>按<span class='like_button'>儲存</span>將平時成績存檔。</li>
            <li>按<span class='like_button'>匯到學期成績</span>將平時成績寫入學期成績資料表，注意！您原有該科的平時成績會被覆寫。</li>
            </ol>
            </td></tr>
            <tr><td></td></tr>
      </table>";        
echo "<table>
        <tr bgcolor='#FBFBC4'><td><img src='images/filefind.png' width=16 height=16 hspace=3 border=0>管理學期成績相關說明</td></tr>
	    <tr><td style='line-height: 150%;'>
            <ol>
            <li>程式會依照登入帳號的老師所任教且必須考試的科目列出選單提供選擇。</li>
            <li>點選你所要管理的科目</li>
            <li>要輸入成績請按<img src=images/pen.png>，輸入完一次的成績請按儲存再切換到另一次的成績</li>
            <li>要刪除該次成績請按<img src=images/del.png></li>
            <li>按<span class='like_button'>儲存</span>將該階段的成績存檔。</li>
            <li>按<span class='like_button'>匯到教務處</span>，將該階段的成績送到教務處，已經送到教務處的成績將不允許在重複傳送，若確實需要修改已經送到教務處的成績，必須請教學組長或是有權限的人將權限打開，始可重傳一次。</li>
            </ol>
            </td></tr>
            <tr><td></td></tr>
      </table>";
if ($is_print=="y") {
echo "<table>
        <tr bgcolor='#FBFBC4'><td><img src='images/filefind.png' width=16 height=16 hspace=3 border=0>顯示學期成績相關說明</td></tr>
	    <tr><td style='line-height: 150%;'>
            <ol>
            <li>點選你所要觀看的成績</li>
            <li>程式會顯示目前尚未傳送到教務處的成績和成績分佈表供老師參考</li>
            <li>正確的成績以匯到教務處的為準！</li>
            </ol>
            </td></tr>
            <tr><td></td></tr>
      </table>";
}
//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();
?>
