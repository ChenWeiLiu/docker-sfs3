<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();
?>
<script type="text/javascript" src="./include/functions.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="./include/JSCal2-1.9/src/css/jscal2.css">

<?php

//秀出網頁
head("個人服務學習登記");

$tool_bar=&make_menu($school_menu_p);

//讀取服務類別 $ITEM[0],$ITEM[1].....
$M_SETUP=get_module_setup('stud_service');
$ITEM=explode(",",$M_SETUP['item']);

//列出選單
echo $tool_bar;

echo "
<br>
服務學習模組使用說明:<br>

1.輸入學生服務學習時數 <br>
  (1)一般教師和管理員皆可替學生登錄服務認證. <br>
  (2)輸入服務學習紀錄分為:<br>
  ※新紀錄 <br>
  步驟1:輸入服務日期、服務單位、服務類型、服務內容<br>
  步驟2:按下拉式選單, 請依服務日期選擇適當學期的班級學生, 在學生欄輸入服務分鐘數, 若有特別需註明細項, 請在該生的註記欄輸入註記內容。<br>
  ※舊有的服務紀錄, 增加學生或扣除學生 (只能補登本學期記錄)<br>
  步驟1:點選畫面右邊已存在的紀錄 , 系統列出已登錄本服務的學生名單.<br>
  步驟2:如果本服務項目要補登學生, 請按下拉式選單, 選擇班級, 並勾選學生, 在學生欄輸入服務分鐘數,若有特別需註明細項, 請在該生的註記欄輸入註記內容。<br>
  步驟3:如果本服務項目記錄的學生有誤, 要刪除, 直接點選該生的 x 符號. <br>
  步驟4:如果本服務項目記錄的學生登錄分鐘數有誤, 請勾選該生, 並按下 [調整勾選學生的服務時間]以進行更改.<br>
 ";
 
 ?>