{{* $Id: health_analyze.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr>
<td bgcolor="#FFFFFF">
<table class="small"><tr><td>{{$sub_menu}} {{$work_menu}} {{if $smarty.post.input_item}}{{$year_seme_menu}} {{$class_menu}}{{/if}}{{if $mfile}} {{include file=$mfile}}{{/if}}</td></tr></table>
{{if $ifile}}{{include file=$ifile}}{{/if}}
</td>
</tr>
<input type="hidden" name="pre_sub_id" value="{{$smarty.post.sub_menu_id}}">
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}