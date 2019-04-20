{{* $Id: system_theme_setup.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="1">
<tr>
<td bgcolor="#FFFFFF">
<table border="0">
<tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" class="small">
<form name="v" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2">佈景設定</td>
</tr>
{{assign var=c value=$smarty.post.chk-1}}
<tr style="background-color:white;text-align:center;">
<td>現在狀態</td><td style="color:{{if $c}}red{{else}}green{{/if}};"><div OnClick="document.v.chk.value='{{$c*-1}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.chk==1}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>分類顏色</td>
<td>
<select name="folder" OnChange="this.form.submit();">
	{{html_options values=$folder_id_arr selected=$smarty.post.folder output=$folder_value_arr}}
</select></td>
</tr>
<input type="hidden" name="chk" value="{{$smarty.post.chk}}">
{{assign var=c value=$smarty.post.chki-1}}
<tr style="background-color:white;text-align:center;">
<td>現在狀態</td><td style="color:{{if $c}}red{{else}}green{{/if}};"><div OnClick="document.v.chki.value='{{$c*-1}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.chki==1}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>圖示色調</td>
<td>
<select name="icon" OnChange="this.form.submit();">
	{{html_options values=$folder_id_arr selected=$smarty.post.icon output=$folder_value_arr}}
</select></td>
</tr>
<input type="hidden" name="chki" value="{{$smarty.post.chki}}">
</form>
</table>
<table>
<tr bgcolor="#FBFBC4">
<td><img src="{{$SFS_PATH_HTML}}images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">本功能目前僅能更換資料匣顏色。</li>
<li class="small">關閉本功能後即可回復系統預設資料匣樣式。</li>
</ol>
</td></tr>
</table>
</td></tr></table>
</td>
</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
