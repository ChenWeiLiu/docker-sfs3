{{* $Id: system_del_temp.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}


<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="1">
<form name="log" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr>
<td bgcolor="#FFFFFF">
<input type="submit" name="clean" value="清除暫存檔">
<table border="0">
<tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr style="text-align:left;color:blue;background-color:#bedcfd;">
<td>序次</td><td>檔案名稱</td>
</tr>
{{foreach from=$rowdata item=v key=i name=n}}
<tr bgcolor="white">
<td>{{$smarty.foreach.n.iteration}}</td><td>{{$v}}</td>
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
