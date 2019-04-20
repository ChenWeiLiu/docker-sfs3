{{* $Id: health_setup.tpl 5830 2010-01-15 13:37:49Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<form id="setupForm" name="myform" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr>
<td bgcolor="#FFFFFF">
<table><tr><td>{{$sub_menu}} {{$third_menu}}</td></tr></table>
{{if $ifile}}{{include file=$ifile}}{{/if}}
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}