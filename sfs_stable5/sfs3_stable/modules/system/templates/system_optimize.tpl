{{* $Id: system_optimize.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="1">
<form name="log" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr>
<td bgcolor="#FFFFFF">
<input type="submit" name="optimize" value="進行最佳化">
<table border="0">
<tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr style="text-align:left;color:blue;background-color:#bedcfd;">
<td>序次</td><td>資料表名稱</td><td>已分配但未使用的位元組數</td>
{{if $smarty.post.optimize}}<td>修復</td><td>最佳化</td>{{/if}}
</tr>
{{foreach from=$rowdata item=v key=i name=n}}
<tr bgcolor="white">
<td>{{$smarty.foreach.n.iteration}}</td><td>{{$v.name}}</td><td>{{$v.data_free}}</td>
{{if $smarty.post.optimize}}<td style="text-align:center;"><font color="{{if $v.repair}}blue">成功{{else}}red">失敗{{/if}}</font></td><td style="text-align:center;"><font color="{{if $v.optimize}}blue">成功{{else}}red">失敗{{/if}}</font></td>{{/if}}
</tr>
{{foreachelse}}
<tr bgcolor="white">
<td colspan="4" style="text-align:center;color:blue;">查無資料</td>
</tr>
{{/foreach}}
</form>
</table>
</td></tr></table>
</td>
</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
