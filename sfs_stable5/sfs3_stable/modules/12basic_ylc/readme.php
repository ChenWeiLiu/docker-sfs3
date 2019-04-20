<?php
// $Id: help.php 6032 2010-08-25 09:33:51Z infodaes $

include "config.php";
sfs_check();

//秀出網頁
head("使用說明");

//橫向選單標籤
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

$help_doc="<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>教育部十二年國民基本教育資訊網：<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='http://12basic.edu.tw/'><span style='color: #0000CC; text-decoration: none'>http://12basic.edu.tw/</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>雲林縣十二年國民基本教育資訊網：<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='https://sites.google.com/a/ms.tnjh.ylc.edu.tw/ylc12/'><span style='color: #0000CC; text-decoration: none'>https://sites.google.com/a/ms.tnjh.ylc.edu.tw/ylc12/</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>雲林區免試入學及其相關應用模組操作說明：<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='https://docs.google.com/document/d/11WsWFk59BC7Sbi1YLAEdbXbk7iPpTdIwqLA9JbKQOvI/edit?usp=sharing'><span style='color: #0000CC; text-decoration: none'>https://docs.google.com/document/d/11WsWFk59BC7Sbi1YLAEdbXbk7iPpTdIwqLA9JbKQOvI/edit?usp=sharing</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>雲林區免試入學12年國教超額比序採計成績對照表：<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='https://drive.google.com/drive/folders/17PY35CHgXVuZlut1FFtJRIpsmB5qYXi6?usp=sharing'><span style='color: #0000CC; text-decoration: none'>https://drive.google.com/drive/folders/17PY35CHgXVuZlut1FFtJRIpsmB5qYXi6?usp=sharing</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>·<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: 標楷體'>106學年度雲林區高級中等學校免試入學委員會考試分發入學系統平臺：<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='https://ylc.entry.edu.tw/NoExamImitate_YL/NoExamImitateHome/Apps/Page/Public/09/ChooseSys.aspx'><span style='color: #0000CC; text-decoration: none'>https://ylc.entry.edu.tw/NoExamImitate_YL/NoExamImitateHome/Apps/Page/Public/09/ChooseSys.aspx</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: 新細明體,serif'>
</span></p>";
$help_doc .= "<div>
				說明：<br>
				<ul>
					<li type='1' value='1' style='margin:6px 30px;'>就近入學與偏遠小校請至<a href='{$SFS_PATH_HTML}modules/sfs_man2/' target='_blank'>【模組權限管理】</a>設定</li>
					<li type='1' value='2' style='margin:6px 30px;'>學生獎勵紀錄與懲處紀錄取自系統<a href='{$SFS_PATH_HTML}modules/reward/' target='_blank'>【學生獎懲】</a>模組。</li>
					<li type='1' value='3' style='margin:6px 30px;'>學生曠課紀錄取自系統<a href='{$SFS_PATH_HTML}modules/absent/' target='_blank'>【缺曠課獎懲管理】</a>模組。</li>
					<li type='1' value='4' style='margin:6px 30px;'>競賽表現請至<a href='{$SFS_PATH_HTML}modules/career_race/' target='_blank'>【生涯輔導競賽記錄】</a>模組登錄。</li>
					<li type='1' value='5' style='margin:6px 30px;'>體適能表現請至<a href='{$SFS_PATH_HTML}modules/fitness/' target='_blank'>【體適能管理】</a>模組登錄。</li>
					<li type='1' value='6' style='margin:6px 30px;'>學生身分設定請至<a href='{$SFS_PATH_HTML}modules/stud_subkind/' target='_blank'>【學生身份子類別】</a>模組登錄。</li>
				</ul>
			</div>";
echo $help_doc;
foot();
?>

