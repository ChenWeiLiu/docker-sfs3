<?php

$MODULE_TABLE_NAME[0] = "";

$MODULE_PRO_KIND_NAME = "學習評量階段成績通知單";

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2010-10-10";

//目錄內程式
$menu_p = array("index.php"=>"階段繳交成績課程設定模式","index2.php"=>"全不完整課程設定模式","index3.php"=>"不完整課程平時成績XLS匯出");

//模組變數
//$IS_MODULE_ARR = array("Y"=>"是",""=>"否");
$SFS_MODULE_SETUP[]=array('var'=>"title_font_name", 'msg'=>"標題字型名稱", 'value'=>'標楷體');
$SFS_MODULE_SETUP[]=array('var'=>"title_font_size", 'msg'=>"標題字型大小", 'value'=>'20pt');
$SFS_MODULE_SETUP[]=array('var'=>"title_font_color", 'msg'=>"標題字型顏色", 'value'=>'#0000ff');
$SFS_MODULE_SETUP[]=array('var'=>"report_title", 'msg'=>"階段通知單標題", 'value'=>'學習評量成績通知單');
$SFS_MODULE_SETUP[]=array('var'=>"report_title2", 'msg'=>"不分階段通知單標題", 'value'=>'學習評量成績通知單');
$SFS_MODULE_SETUP[]=array('var'=>"title_break", 'msg'=>"校名與標題間斷行", 'value'=>array("<br>"=>"是",""=>"否"));
$SFS_MODULE_SETUP[]=array('var'=>"logo_link", 'msg'=>"logo連結", 'value'=>'./images/logo.png');
$SFS_MODULE_SETUP[]=array('var'=>"columns", 'msg'=>"學生名單列表欄數 ", 'value'=>7);
$SFS_MODULE_SETUP[]=array('var'=>"font_size", 'msg'=>"字體大小", 'value'=>'12pt');
$SFS_MODULE_SETUP[]=array('var'=>"detail_title_font_size", 'msg'=>"階段分項(定期、平時、平均)字體大小", 'value'=>'10pt');
$SFS_MODULE_SETUP[]=array('var'=>"stage_border_color", 'msg'=>"階段成績資料表格線條顏色", 'value'=>'#000000');
$SFS_MODULE_SETUP[]=array('var'=>"stage_border_width", 'msg'=>"階段成績資料表格線條粗細", 'value'=>'1');
$SFS_MODULE_SETUP[]=array('var'=>"nor_border_color", 'msg'=>"平時成績資料表格線條顏色", 'value'=>'#333333');
$SFS_MODULE_SETUP[]=array('var'=>"nor_border_width", 'msg'=>"平時成績資料表格線條粗細", 'value'=>'1');
$SFS_MODULE_SETUP[]=array('var'=>"stage_bgcolor", 'msg'=>"定期成績資料表格底色", 'value'=>'#ffccff');
$SFS_MODULE_SETUP[]=array('var'=>"nor_bgcolor", 'msg'=>"平時成績資料表格底色", 'value'=>'#ccffff');
$SFS_MODULE_SETUP[]=array('var'=>"avg_bgcolor", 'msg'=>"平均成績資料表格底色", 'value'=>'#ccccff');
$SFS_MODULE_SETUP[]=array('var'=>"default_note_text", 'msg'=>"預設的說明文字", 'value'=>'<br>親愛的家長：<br>　　下列為貴子弟在本校階段成績評量結果，敬請查悉。　若您對成績有疑義，請能洽詢班級導師。<p align=right>教務處感謝您！</p>');
$SFS_MODULE_SETUP[]=array('var'=>"report_footer", 'msg'=>"通知單頁尾說明註記", 'value'=>'');
$SFS_MODULE_SETUP[]=array('var'=>"note_rows", 'msg'=>"說明文字輸入時預設的列數", 'value'=>'5');
$SFS_MODULE_SETUP[]=array('var'=>"stage_item", 'msg'=>"全不完整模式定期考查成績項目選項", 'value'=>'定考1,定考2,定考3');
$SFS_MODULE_SETUP[]=array('var'=>"default_subject", 'msg'=>"預設選取的定期考查科目", 'value'=>'本國語文,英語,數學,社會,自然與生活科技');
$SFS_MODULE_SETUP[]=array('var'=>"default_percision", 'msg'=>"預設成績顯示的精度", 'value'=>array('1'=>'整數','2'=>'小數1位','3'=>'小數2位'));
$SFS_MODULE_SETUP[]=array('var'=>"subject_width", 'msg'=>"學習科目欄位寬度", 'value'=>70);
$SFS_MODULE_SETUP[]=array('var'=>"sign_height", 'msg'=>"任課教師簽名欄高度", 'value'=>50);




?>
