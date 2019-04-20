{{* $Id: health_base.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr>
<td bgcolor="white">
<table style="width:100%;"><tr><td class="small">
{{$sub_menu}} 
{{if $smarty.post.sub_menu_id}}
{{if $smarty.post.sub_menu_id==6}}
{{$year_menu}}
{{else}}
{{$year_seme_menu}} {{$class_menu}} {{$work_menu}}{{if $mfile}} {{include file=$mfile}}{{/if}}<br><br>
{{/if}}{{/if}}
{{if $smarty.post.class_name || $smarty.post.sub_menu_id>=6}}
{{if $ifile}}{{include file=$ifile}}{{/if}}
{{/if}}
</td></tr></table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
