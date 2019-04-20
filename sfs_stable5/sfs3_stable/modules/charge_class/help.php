<?php

// $Id: help.php 5310 2009-01-10 07:57:56Z hami $



include "config.php";

sfs_check();



//秀出網頁
head("收費管理(導師版)");



//橫向選單標籤

$linkstr="item_id=$item_id";

echo print_menu($MENU_P,$linkstr);



$help_doc="<font color='#FF0000'>使用本模組前 請先安裝 [收費管理(charge)]模組~~~~~~</font>
    <table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#008000' width='100%'><tr>
    <td width='5%' align='center' bgcolor='#CCFF99' height='22'>NO.</td>
    <td width='1%' align='center' bgcolor='#CCFF99' height='22'>類別</td>
    <td width='14%' align='center' bgcolor='#CCFF99' height='22'>連結標籤</td>
    <td width='5%' align='center' bgcolor='#CCFF99' height='22'>導師版</td>
    <td width='6%' align='center' bgcolor='#CCFF99' height='22'>行政版</td>
    <td width='20%' align='center' bgcolor='#CCFF99' height='22'>檔案名稱</td>
    <td width='62%' align='center' bgcolor='#CCFF99' height='22'>說明</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>1</td>
    <td width='6%' align='center' height='16'>說明</td>
    <td width='14%' align='center' height='16'>模組說明</td>
    <td width='5%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>help.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>您目前所見的畫面</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>2</td>
    <td width='6%' align='center' height='64' rowspan='4'>設定</td>
    <td width='14%' align='center' height='16'>項目設定</td>
    <td width='5%' align='center' height='16'></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>item.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>設定收費項目</p>
    <p style='margin-top: 0; margin-bottom: 0'>包括[類別]<span style='font-family: 新細明體'>、</span>[項目名稱]<span style='font-family: 新細明體'>、</span>[管理備註]<span style='font-family: 新細明體'>、</span>[收費日期]<span style='font-family: 新細明體'>、</span>[依據]<span style='font-family: 新細明體'>、</span>[繳款方式]<span style='font-family: 新細明體'>、</span>[單據附記]</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>3</td>
    <td width='14%' align='center' height='16'>細目設定</td>
    <td width='5%' align='center' height='16'></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>detail.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>設定本項收費的細目</p>
    <p style='margin-top: 0; margin-bottom: 0'>設定時需考慮收費單可容納項數</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>4</td>
    <td width='14%' align='center' height='16'>收費名單</td>
    <td width='5%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>list.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>設定參加此項收費的學生名單</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>5</td>
    <td width='14%' align='center' height='16'>減免設定</td>
    <td width='5%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>decrease.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>設定參加此項收費的學生&quot;細目減免&quot;名單</p>
    <p style='margin-top: 0; margin-bottom: 0'>可選擇設定單一學生或依照學生身分類別開列名單</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>6</td>
    <td width='6%' align='center' height='16'>單據</td>
    <td width='14%' align='center' height='16'>收費通知</td>
    <td width='5%' align='center' height='16'></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>announce.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>列印收費單據</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>7</td>
    <td width='6%' align='center' height='48' rowspan='3'>管理</td>
    <td width='14%' align='center' height='16'>繳款登錄</td>
    <td width='5%' align='center' height='16'></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>received.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>以滑鼠點選方式設定繳款者與繳款金額</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>8</td>
    <td width='14%' align='center' height='16'>條碼收款登錄</td>
    <td width='5%' align='center' height='16'></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>barcode.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>以條碼號設定繳款</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>9</td>
    <td width='14%' align='center' height='16'>紀錄維護</td>
    <td width='5%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>record.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>修改或刪除某學生的某收費項目紀錄</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>10</td>
    <td width='6%' align='center' height='48' rowspan='3'>報表</td>
    <td width='14%' align='center' height='16'>催繳清冊</td>
    <td width='5%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>hie.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>顯示尚未繳清學生名單</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>11</td>
    <td width='14%' align='center' height='16'>班級統計</td>
    <td width='5%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>class_summary.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>顯示班級收費情形</p>
    <p style='margin-top: 0; margin-bottom: 0'>包括[應收人數與金額][減免人數與金額][繳款人數與金額]</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>12</td>
    <td width='14%' align='center' height='16'>細目統計</td>
    <td width='5%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>detail_summary.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>分年級分項列示細目應收款情形</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>13</td>
    <td width='6%' align='center' height='32' rowspan='2'>目錄</td>
    <td width='14%' align='center' height='32' rowspan='2'>---</td>
    <td width='5%' align='center' height='16'></td>
    <td width='6%' align='center' height='32' rowspan='2'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>/ooo</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>收費單據置放目錄</p>
    <p style='margin-top: 0; margin-bottom: 0'>內定有 [A4四聯<span style='font-family: 新細明體'>]、[</span>B5三聯<span style='font-family: 新細明體'>]、[</span>A5二聯<span style='font-family: 新細明體'>]、[</span>中一刀半寬收據] 
    四種格式</p>
    <p style='margin-top: 0; margin-bottom: 0'>可將自行編定的報表放進來</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>14</td>
    <td width='5%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>/images</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>相關連結圖形置放目錄</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>15</td>
    <td width='6%' align='center' height='16'>其他</td>
    <td width='14%' align='center' height='16'>---</td>
    <td width='5%' align='center' height='16'></td>
    <td width='6%' align='center' height='16'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>IDAutomationHC39M_Free.ttf</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>免費條碼字型</p>
    <p style='margin-top: 0; margin-bottom: 0'>操作端電腦須安裝以便能於報表中正確顯示及列印條碼</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>16</td>
    <td width='6%' align='center' height='64' rowspan='3'>模組</td>
    <td width='14%' align='center' height='48' rowspan='3'>---</td>
    <td width='5%' align='center' height='48' rowspan='3'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='6%' align='center' height='48' rowspan='3'>
    <span style='font-size: 12.0pt; font-family: 新細明體'>◎</span></td>
    <td width='24%' align='center' height='16'>index.php</td>
    <td width='44%' height='16'>模組內定執行檔</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>17</td>
    <td width='24%' align='center' height='16'>config.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>模組引入檔</td>
  </tr>
  <tr>
    <td width='5%' align='center' height='16'>18</td>
    <td width='24%' align='center' height='16'>module-cfg.php</td>
    <td width='44%' height='16'>
    <p style='margin-top: 0; margin-bottom: 0'>模組定義檔(使用的資料表<span style='font-family: 新細明體'>、</span>內定系統變數<span style='font-family: 新細明體'>、選單定義</span>...)</td>
  </tr>
</table>
<p style='margin-top: 0; margin-bottom: 0'>　</p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
<span style='font-size: 12.0pt; font-family: 新細明體'>※</span>使用步驟<span style='font-family: 新細明體'>：</span></font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
<span style='font-family: 新細明體'>　Step 1. &gt;&gt; 設定 [項目]：有&nbsp; (1)複製功能：可自歷年資料複製過來&nbsp; 
(2)格式新增功能：依照特定格式一次產生項目與細目</span></font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
<span style='font-family: 新細明體'>　Step 2. &gt;&gt; 設定 [細目]：(1)</span>排序<span style='font-family: 新細明體'>欄為非必填資料　(2)</span>應收金額(請以,分隔各年級)，以利程式判斷</font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
<span style='font-family: 新細明體'>　Step 3. &gt;&gt; 設定 [收費名單]：可開列[單一學生]、[班級]、[全年級]名單</span></font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
<span style='font-family: 新細明體'>　Step 4. &gt;&gt; 
設定 [減免名單]：設定某一細目[單一學生]或[某類別學生]的&quot;減免百分比&quot;數</span></font></p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#0000FF'>
<span style='font-family: 新細明體'>　Step 5. &gt;&gt; </span><font face='新細明體'>列印 [收費通知]</font><span style='font-family: 新細明體'>：選擇學生與收費單格式後產生OpenOffice檔案以供列印</span></font></p>
<p style='margin-top: 0; margin-bottom: 0'>　</p>
<p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000'><span style='font-size: 12.0pt; font-family: 新細明體'>※</span>PS. 
導師版限制的功能：</font></p>

<p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000'>　1.無法選擇學期</font></p>

<p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000'>　2.只能操作任教班級</font></p>

<p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000'>
　3.操作項目會受收費[起始日]與[結束日]限制</font></p>

<p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000'>　4.無法開列項目與細目</font></p>

<p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000'>　5.無法印製收費單據</font></p>

<p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000'>
　6.其他功能視系統管理員開啟與否

<BR><BR><BR>※系統分析與程式設計：infodaes   2006/8/3

";

echo $help_doc;

foot();

?>