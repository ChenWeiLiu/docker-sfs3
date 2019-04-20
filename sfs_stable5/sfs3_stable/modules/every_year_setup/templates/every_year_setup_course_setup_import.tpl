{{* $Id: every_year_setup_course_setup_import.tpl 5592 2009-08-19 02:21:05Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table cellspacing="0" cellpadding="0" border="0">
<tr valign="top">
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}" enctype="multipart/form-data">
<td>
{{if $ifile}}
{{include file=$ifile}}
{{else}}
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<tr bgcolor="#FFFFFF">
<td>
<table>
<tr>
<td>請選擇欲設定的學期：{{$year_seme_menu}}</td>
</tr>
<tr><td>
<input type="submit" name="act" value="進行匯入作業" class="b1"> 
<input type="submit" name="act" value="清除匯入資料" class="b1">
<input type="submit" name="act" value="回到課表設定" class="b1" OnClick="this.form.import.value='0'"><br>
<input type="submit" name="act" value="進行教師對應" class="b1"> 
<input type="submit" name="act" value="進行課程對應" class="b1">
<input type="submit" name="act" value="寫入課表設定" class="b1">
<input type="hidden" name="import" value="1">
</td></tr>
</table>
</td></tr>
</table>
{{if $smarty.post.act=="開始設定"}}
<td><input type="submit" name="save" value="儲存設定">
{{elseif $smarty.post.act=="觀看設定" || $smarty.post.act=="show"}}
<td><input type="submit" name="act" value="開始設定">
{{/if}}
</td>
</form>
</tr></table>
<br>
<table width="100%" class="small"><tr>
<td style="vertical-align:top;" width="25%">
<fieldset>
<legend>班級對應</legend>
總班數：{{$data.c.0}}<br>
已對應：{{$data.c.1}}<br>
無對應：{{$data.c.2}}
</fieldset>
</td>
<td style="vertical-align:top;" width="25%">
<fieldset>
<legend>教師對應</legend>
總人數：{{$data.t.0}}<br>
已對應：{{$data.t.1}}<br>
無對應：{{$data.t.2}}<br>
</fieldset>
</td>
<td style="vertical-align:top;" width="25%">
<fieldset>
<legend>課程對應</legend>
總課數：{{$data.s.0}}<br>
已對應：{{$data.s.1}}<br>
無對應：{{$data.s.2}}<br>
</fieldset>
</td>
<td style="vertical-align:top;" width="25%">
<fieldset>
<legend>節次對應</legend>
總節數：{{$data.ss.0}}<br>
已對應：{{$data.ss.1}}<br>
無對應：{{$data.ss.2}}<br>
</fieldset>
</td></tr></table>
<br>
{{if $err_msg!=""}}<font color="red">{{$err_msg}}</font><br><br>{{/if}}
<table>
<tr bgcolor="#FBFBC4"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td></tr>
<tr><td style="line-height: 150%;">
<ol>
{{if $step==""}}
<li class="small">請選擇一個學年、學期以做設定。</li>
<li class="small"><span class="like_button">進行匯入作業</span>開始進行該學期課表的匯入作業。</li>
<li class="small"><span class="like_button">清除匯入資料</span>清除已匯入之暫存資料。
{{elseif $step==1}}
<li class="small">請先上傳課表檔以供系統進行解析。</li>
<li class="small">請勿使用中文檔名以免發生錯誤。</li>
<li class="small">本系統目前支援欣河排課系統。</li>
<li class="small">其他排課系統支援請洽系統開發人員。</li>
{{/if}}
</li>
</ol>
{{/if}}
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
