{{* $Id: index.tpl 5310 2011-01-01 $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
一、範例及使用方法<br>
(1)請【<a href="javascript: msg_form()">按我</a>】開啟彈出畫面。<br>
(2)使用方法通常是在校園首頁設置一個可開啟彈出視窗的選單，超連結位置設定為 {{$SFS_PATH_HTML}}modules/school_msn/main_index.php</br>
建議的 JavaScript 指令如下：<br>
 window.open('{{$SFS_PATH_HTML}}{{$MSN_WINDOW}}','messageWindow','resizable=0,toolbar=no,scrollbars=auto');<br>
<br>
二、說明:<br>
1.校園MSN是一個可以供學校教師之間進行訊息交流或檔案傳遞的模組.<br>
2.介面說明:主畫面為彈出畫面, 共包括四個區域 <br>
<img src='./images/main.png' border='0'><br>
(1)首頁公告區: 會自動抓取 sfs3 的  board 模組或 jboard 模組內的最新公告.<br>
<font color=red><b>※請記得設定模組變數, 進行相關設定。</b></font><br>
(2)校內訊息交流區: 使用者發佈公開訊息時, 該公開訊息會呈現於此.<br>
(3)工具列: 可操作的各種功能.<br>
(4)狀態區: 提示目前「幾人登入」, 若有人發私訊給你, 會出現「<img src='./images/msg.gif' border='0'>訊息(1)」提示, 直接點選即可讀取私訊.<br>
<br>
3.工具列說明:<br>
(1)<img src='./images/reload.jpg' border='0'>：重新整理畫面。<br>
(2)<img src='./images/post.jpg' border='0'>：發送訊息或上傳檔案。<br>
(3)<img src='./images/download.jpg' border='0'>：下載檔案。<br>
(4)<img src='./images/manage.jpg' border='0'>：管理訊息。<br>
(5)<img src='./images/state.jpg' border='0'>：設定自己的狀態。<br>
(6)<img src='./images/logout.jpg' border='0'>：登出。<br>



{{include file="$SFS_TEMPLATE/footer.tpl"}}

<Script language="JavaScript">

function msg_form()
{
 flagWindow=window.open('{{$SFS_PATH_HTML}}{{$MSN_WINDOW}}','messageWindow','resizable=0,toolbar=no,scrollbars=auto');
}

</Script>