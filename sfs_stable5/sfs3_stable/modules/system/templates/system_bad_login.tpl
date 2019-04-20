{{* $Id: system_bad_login.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="1">
<form name="log" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr>
<td bgcolor="#FFFFFF">
<input type="submit" name="clean" value="清除記錄">
<input type="submit" name="export" value="匯出CSV檔">
<table border="0">
<tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td>登入時間</td><td>登入IP</td><td>登入帳號</td><td>登入狀況</td>
</tr>
{{foreach from=$rowdata item=v key=i}}
<tr bgcolor="white">
<td>{{$v.log_time}}</td><td>{{$v.log_ip}}</td><td>{{$v.log_id}}</td><td>{{$v.err_kind}}</td>
</tr>
{{foreachelse}}
<tr bgcolor="white">
<td colspan="4" style="text-align:center;color:blue;">查無資料</td>
</tr>
{{/foreach}}
</form>
</table>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<form name="v" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2">防護連續登入錯誤</td>
</tr>
{{assign var=c value=$smarty.post.lock-1}}
<tr style="background-color:white;text-align:center;">
<td>防護開關現在狀態</td><td style="color:{{if $c}}red{{else}}green{{/if}};"><div OnClick="document.v.lock.value='{{$c*-1}}';document.v.submit();" style="cursor:pointer;">{{if $smarty.post.lock}}開{{else}}關{{/if}}</div></td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>每分鐘最大錯誤次數</td><td><input type="text" name="err_times" size="2" value="{{$smarty.post.err_times}}">次</td>
</tr>
<input type="hidden" name="lock" value="{{$smarty.post.lock}}">
</form>
</table>
</td></tr></table>
</td>
</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
