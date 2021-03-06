<?php
include_once('config.php');

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

sfs_check();


//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

?>
<table border="0">
	<tr>
		<td>
			<font color=blue>※本模組專供教師管理及保存教學過程中的所有隨堂小考成績。</font><br><br>
			<font color=blue>※關於小考管理模組與成績管理模組中的「平常成績」與「學期成績」的差異說明:</font><br>
		 學務系統中的成績管理模組裡, 原本即提供平常成績管理功能, 但因應九年一貫領域成績的計算方式, <br> 
		 並非每科教師在實務上都適合把「平常成績」管理功能拿來當小考成績管理。<br>
		 例如: 社會領域在務實上可能包括地理、歷史、公民三科成績,若由三個老師分科教學, 這三位老師平常<br>
		 可能有各自的小考成績, 在每次段考後再各別提供一次成績至「成績管理」模組的平常成績,<br>
		 最後再調整加權匯出真正的該領域平常成績作為一次學期階段平常成績。<br><br>
			<font color=blue>※操作說明：</font><br>
			1.<font color=red>成績單管理</font>：建立成績單及進行成績單的其他設定。<br>
			(1)新增一份成績單,以便在成績單中輸入成績。<br>
			<img src="images/report_insert.png" border="0"><br>
			(2)新增成績單同時,可進行的相關設定如下:<br>
			a.成績單名稱: 如:一年一班第一週成績、一年十班第一段自然小考成績...等。<br>
			b.班級:可根據任教班級, 設定此份成績單是屬於那一個班的成績。<br>
			c.小老師:設定班級後, 即可指定小老師來協助登打成績.<br>
			d.開放學生查詢: 是否開放學生查詢成績。(系統預設不允許勾選開放學生查詢)<br>
			e.成績單樣式: 當開放學生查詢時, 顯示的是全班的成績總表, 或個人成績, 若為學業成績, 建議選擇個人成績.(系統預設不允許勾選全班成績總表)<br>
			f.成績單提示總分: 是否顯示總分<br>
			g.成績單提示平均: 是否顯示平均<br>
			h.成績單提示排名: 是否顯示排名 (系統預設不允許勾選顯示排名)<br>
			註: 上述預設不允許設定的功能, 依貴校需求由系統管理員調整模組變數, 唯請謹慎使用本工具, 以免招致不必要的紛爭。<br>
			<br>
			2.<font color=red>輸入成績</font>: 僅列出本學期的成績單供選擇, 以便輸入成績.<br>
			註:若需開放小老師登入協助輸入成績, 需請系統管理員將本模組亦授權學生使用, 學生可經由「額外模組」選項看到本模組。<br>
			本模組的功能表中, 學生雖經授權, 亦只能使用<br>
			「輸入成績」(限該成績單小老師)及「觀看成績單」(限該班且有開放瀏覽的成績單)功能。<br>
			<br>
			3.<font color=red>觀看成績單</font>: 系統會保留您歷年來建立的每份成績單, 選擇學期後, 即可瀏覽該學期的所有成績單.<br>
			<br>
			4.<font color=red>匯入階段成績</font>:	當您建立成績單, 並搜集成績後, 若某份成績單的平均, 您想要做為學務系統中<br>
			本學期某階段的平常成績, 即可利用此功能.
		 (1)選擇班級科目: 系統會自動判別您這學期的任教班級科目, 請進行選擇.<br>
		 (2)選擇階段: 選擇完班級科目, 系統會自動判斷目前應該輸入那一個階段的正式成績.
		 (3)當班級科目及階段皆選擇完畢, 系統會自您本學期建立的成績單中, 搜尋屬於該班的成績單供您選擇.<br>
		 (4)列出成績單後, 您可以進一步勾選要以那幾次考試的成績重新計算平均.
		 (5)確認無誤, 再進行「匯出平均分數至『平常成績』作為一次成績」或「直接匯出平均分數至『學期成績』的平常成績」<br>
		 針對相同領均卻分科教學的老師請分別「匯出平均分數至『平常成績』作為一次成績」, 最後再至成績管理的「平常成績」進行名稱及加權的調整, 然後再「匯到學期的階段成績」<br>
		 其他如數學科則無此問題, 統計出平均分數後, 只要「直接匯出平均分數至『學期成績』的平常成績」即可。<br>		
	 <br>
		<font color=red>◎注意! 若某份小考成績單的平均分數已匯出至正式的平常成績, 則該小考成績單將無法再進行任何修改.</font><br>
		
		</td>	
	</tr>

</table>

