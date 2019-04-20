{{* $Id: $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="1">
<tr>
<td bgcolor="#FFFFFF">
<table border="0">
<tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" class="small">
<form name="v" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2">自然人憑證登入設定</td>
</tr>
{{assign var=c value=$smarty.post.cdc}}
{{if $c=="ON"}}{{assign var=d value="OFF"}}{{else}}{{assign var=d value="ON"}}{{/if}}
<tr style="background-color:white;text-align:center;">
<td>現在狀態</td><td style="color:{{if $c=="ON"}}green{{else}}red{{/if}};"><div OnClick="document.v.cdc.value='{{$d}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.cdc==ON}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>openssl 程式確認</td>
<td><img src="images/{{if $cdc_arr.pg}}OK{{else}}NO{{/if}}.png"></td>
</tr>
<input type="hidden" name="cdc" value="{{$smarty.post.chk}}">
<tr style="background-color:white;text-align:center;">
<td>openssl 函式確認</td>
<td><img src="images/{{if $cdc_arr.fn}}OK{{else}}NO{{/if}}.png"></td>
</tr>
</form>
</table>
<table>
<tr bgcolor="#FBFBC4">
<td><img src="{{$SFS_PATH_HTML}}images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">本程式用來開啟系統的自然人憑證認證登入。</li>
<li class="small">系統須有openssl程式與支援openssl_pkey_get_public函式才能正常運作。</li>
</ol>
</td></tr>
</table>
</td></tr></table>
</td>
</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
