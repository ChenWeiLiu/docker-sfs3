{{* $Id:$ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="1">
<form name="v" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr>
<td bgcolor="#FFFFFF">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2">伺服器憑證設定</td>
</tr>
{{assign var=c value=$smarty.post.ssl-1}}
<tr style="background-color:white;text-align:center;">
<td>https現在狀態</td><td style="color:{{if $c}}red{{else}}green{{/if}};"><div OnClick="document.v.ssl.value='{{$c*-1}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.ssl}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>https域名</td><td>https://<input type="text" name="https_name" size="50" value="{{$HTTPS_NAME}}"></td>
</tr>
<input type="hidden" name="ssl" value="{{$smarty.post.ssl}}">
</table>
<table>
<tr bgcolor="#FBFBC4">
<td><img src="{{$SFS_PATH_HTML}}images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">要開啟功能前，請先填入域名，例如「sfs.xxx.tc.edu.tw」。</li>
</ol>
</td></tr>
</table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
