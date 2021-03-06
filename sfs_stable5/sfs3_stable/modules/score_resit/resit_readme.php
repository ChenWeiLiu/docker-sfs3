<?php
$M="
模組使用說明：

※緣起
希望藉由線上測驗減輕人工作業時間，同時減少紙張印刷成本，為綠色環保略盡一份心力。

※準備工作
您除了安裝本模組，還必須安裝補行評量作業（makeup_exam）模組、及學生線上測驗（stud_exam）模組

※作業流程說明

◎步驟1：【學期補考設定】
確認現在要啟用那個學期的補考，以及補考時學生的領卷模式，領卷模式包括：
(1)依試卷設定：在試卷設定的時段內才能領該試卷進行補考。
(2)在某個時段內，目前上線學生可以領本學年學期所有他該補考的考卷。

◎步驟2：【補考名單相關作業】
確認那些人該補考、匯出名單、利用 Excel 及 Word 套印功能通知家長及學生）

◎步驟3：【命題補考考卷】
命題有三個方式
1.線上命題：
這是最傳統的命題方式，一題一題將題目鍵入，也可以插入題幹或選目的附圖。
※關於附圖，請特別注意附圖的大小，避免過大或過小，以免造成作答時，閱讀上的不便。此外，每張圖圖檔大小勿超過 2MB。

2.快貼：可一次快貼許多試題，以下做簡單說明
(1)以下圖word檔的試卷為例，若我要快貼5~9題這5題試題，每一題以一個「段落符號」區隔(紅圈部分)

<img border='1' src='images/3-1.png'>

另外，注意觀察這5題可讓系統分析欄位作為依據的「格式」，例如：每一題的題號後都有個小數點「.」，且都有「(A)」「(B)」「(C)」「(D)」四個選項，只要根據這些條件切格，就能得到題幹及四個選目資料，如下圖所示：

<img border='1' src='images/3-2.png'>

(2)選取這5題，然後複製貼上到快貼功能的表單中

<img border='1' src='images/3-3.png'>

<img border='1' src='images/3-6.png'>

(3)按下【分析試題】，系統根據斷行的條件，進行試題分析，將要儲存的試題打勾，並指定每個欄位對應的資料類別

<img border='1' src='images/3-7.png'>

(4)按下【儲存試題】，即可儲存這5題，由於以上範例並沒有截取到解答，因此在此可按下【調整解答】

<img border='1' src='images/3-8.png'>

(5)按下【調整解答】，可調整每一道試題的解答

<img border='1' src='images/3-9.png'>

※注意！快貼功能無法附圖，若試題包含附圖，您仍必須利用「檢視／編輯試題」的功能，逐題將附圖上傳。

<img border='1' src='images/3-10.png'>

3.匯入：可直接匯入由系統「匯出」的 wit 檔案，此功能會連同圖片一起匯入，等於複製一份完整的試卷。


◎步驟4：線上測驗（stud_exam）模組
這是學生線上測驗專用模組，學校可以安排時間，一批一批學生帶進電腦教室
，直接線上領卷，線上作答，作答完畢成績馬上計算出來。
線上測驗出題方式依據試卷設定，可出試卷全部題目，或把試卷當成題庫，只出
指定題數，出題時題序採亂數排列，選目也採亂數排列。

◎線上測驗進行時：
可以透過【補考名單相關作業】功能，隨時追縱未補考、補考中、已補考名單。

◎線上補考結束：【匯出補考分數】
將各領域補考完的分數匯出至補行評量作業（makeup_exam）模組的資料庫中，
接下來請轉到補行評量作業（makeup_exam）模組，利用該模組「成績輸入/擇優計算學期成績」功能存入學生的領域學期總成績中。 

";

header('Content-type: text/html;charset=big5');
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
//驗證是否登入
sfs_check(); 

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

head();
//列出選單
echo $tool_bar;

$M=preg_replace("/\n/","<br>\n",$M);

echo $M;

?>
