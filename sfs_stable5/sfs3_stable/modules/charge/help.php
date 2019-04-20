<?php
// $Id: help.php 6032 2010-08-25 09:33:51Z infodaes $

include "config.php";
sfs_check();

//秀出網頁
head("收費管理");

//橫向選單標籤
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

$help_doc="
<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#008000' width='100%'>
  <tr>
    <td width='5%' align='center' bgcolor='#CCFF99' height='22'>NO.</td>
    <td width='1%' align='center' bgcolor='#CCFF99' height='22'>類別</td>
    <td width='13%' align='center' bgcolor='#CCFF99' height='22'>連結標籤</td>
    <td width='19%' align='center' bgcolor='#CCFF99' height='22'>檔案名稱</td>
    <td width='71%' align='center' bgcolor='#CCFF99' height='22'>說明</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>1</td>
    <td width='6%' align='center' height='16'>說明</td>
    <td width='13%' align='center' height='16'>模組說明</td>
    <td width='23%' align='center' height='16'>help.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>您目前所見的畫面</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>2</td>
    <td width='6%' align='center' height='64' rowspan='4'>設定</td>
    <td width='13%' align='center' height='16'>項目設定</td>
    <td width='23%' align='center' height='16'>item.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>設定收費項目</p>
    <p style='margin-top: 0; margin-bottom: 0'>包括[類別]、[項目名稱]、[管理備註]、[收費日期]、[依據]、[繳款方式]、[單據附記]</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>3</td>
    <td width='13%' align='center' height='16'>細目設定</td>
    <td width='23%' align='center' height='16'>detail.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>設定本項收費的細目</p>
    <p style='margin-top: 0; margin-bottom: 0'>設定時需考慮收費單可容納項數</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>4</td>
    <td width='13%' align='center' height='16'>收費名單</td>
    <td width='23%' align='center' height='16'>list.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>設定參加此項收費的學生名單</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>5</td>
    <td width='13%' align='center' height='16'>減免設定</td>
    <td width='23%' align='center' height='16'>decrease.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>設定參加此項收費的學生&quot;細目減免&quot;名單</p>
    <p style='margin-top: 0; margin-bottom: 0'>可選擇設定單一學生或依照學生身分類別開列名單</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>6</td>
    <td width='6%' align='center' height='16'>單據</td>
    <td width='13%' align='center' height='16'>收費通知</td>
    <td width='23%' align='center' height='16'>announce.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>列印收費單據</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>7</td>
    <td width='6%' align='center' height='48' rowspan='3'>管理</td>
    <td width='13%' align='center' height='16'>繳款登錄</td>
    <td width='23%' align='center' height='16'>received.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>以滑鼠點選方式設定繳款者與繳款金額</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>8</td>
    <td width='13%' align='center' height='16'>條碼收款登錄</td>
    <td width='23%' align='center' height='16'>barcode.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>以條碼號設定繳款</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>9</td>
    <td width='13%' align='center' height='16'>紀錄維護</td>
    <td width='23%' align='center' height='16'>record.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>修改或刪除某學生的某收費項目紀錄</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>10</td>
    <td width='6%' align='center' height='48' rowspan='3'>報表</td>
    <td width='13%' align='center' height='16'>催繳清冊</td>
    <td width='23%' align='center' height='16'>hie.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>顯示尚未繳清學生名單</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>11</td>
    <td width='13%' align='center' height='16'>班級統計</td>
    <td width='23%' align='center' height='16'>class_summary.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>顯示班級收費情形</p>
    <p style='margin-top: 0; margin-bottom: 0'>包括[應收人數與金額][減免人數與金額][繳款人數與金額]</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>12</td>
    <td width='13%' align='center' height='16'>細目統計</td>
    <td width='23%' align='center' height='16'>detail_summary.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>分年級分項列示細目應收款情形</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>13</td>
    <td width='6%' align='center' height='32' rowspan='2'>目錄</td>
    <td width='13%' align='center' height='32' rowspan='2'>---</td>
    <td width='23%' align='center' height='16'>/ooo</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>收費單據置放目錄</p>
    <p style='margin-top: 0; margin-bottom: 0'>內定有 
    <a href='./ooo/A4四聯/收費通知範例_A4四聯.zip'>[A4四聯]</a>、
    <a href='./ooo/B5三聯/收費通知範例_B5三聯.zip'>[B5三聯]</a>、
    <a href='./ooo/A5二聯/收費通知範例_A5二聯.zip'>[A5二聯]</a>、
    <a href='./ooo/中一刀半寬收據/收費通知範例_中一刀半寬收據.zip'>[中一刀半寬收據]</a>
    四種格式</p>
    <p style='margin-top: 0; margin-bottom: 0'>您可將自行編定的報表放進來</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>14</td>
    <td width='23%' align='center' height='16'>/images</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>相關連結圖形置放目錄</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>15</td>
    <td width='6%' align='center' height='16'>其他</td>
    <td width='13%' align='center' height='16'>---</td>
    <td width='23%' align='center' height='16'><a href='http://podtalje.si.eu.org/razno/IDAutomationHC39M_Free.ttf'>IDAutomationHC39M_Free.ttf</a></td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>免費條碼字型，可網路搜尋下載</p>
    <p style='margin-top: 0; margin-bottom: 0'>操作端電腦須安裝以便能於報表中正確顯示及列印條碼</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>16</td>
    <td width='6%' align='center' height='64' rowspan='3'>模組</td>
    <td width='13%' align='center' height='48' rowspan='3'>---</td>
    <td width='23%' align='center' height='16'>index.php</td>
    <td width='53%' height='16'>模組內定執行檔</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>17</td>
    <td width='23%' align='center' height='16'>config.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>模組引入檔</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>18</td>
    <td width='23%' align='center' height='16'>module-cfg.php</td>
    <td width='53%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>模組定義檔(使用的資料表、內定系統變數、選單定義...)</td>
  </tr>
</table>
<p style='margin-top: 0; margin-bottom: 0'>　</p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
※使用步驟：</font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
　Step 1. &gt;&gt; 設定[項目]：有 (1)複製功能：可複製歷年資料 
(2)格式新增功能：依特定格式一次產生項目與細目</font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
　Step 2. &gt;&gt; 設定[細目]：(1)排序欄為非必填資料　(2)應收金額(請以,分隔各年級)，以利程式判斷</font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
　Step 3. &gt;&gt; 設定[收費名單]：可開列[單一學生]、[班級]、[全年級]名單</font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
　Step 4. &gt;&gt; 
設定[減免名單]：設定某一細目[單一學生]或[某類別學生]的&quot;減免百分比&quot;數</font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
　Step 5. &gt;&gt; <font face='新細明體'>列印[收費通知]</font>：選擇學生與收費單格式後產生OpenOffice檔案以供列印</font></p>
<BR><BR><BR>※系統分析與程式設計：infodaes   2006/8/2
";
echo $help_doc;
foot();
?>