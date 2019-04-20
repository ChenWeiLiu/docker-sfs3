{{* $Id: health_input.tpl 5963 2010-06-15 02:06:54Z hami $ *}}
{{capture name=injectJavascript}}
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/ui/jquery.ui.dialog.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/external/bgiframe/jquery.bgiframe.js"></script>
{{/capture}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
{{if $smarty.post.sub_menu_id=="12"}}
{{include file="health_whole.tpl"}}{{else}}

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<form name="myform" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<tr>
<td bgcolor="white">
<table><tr><td class="small">
{{$sub_menu}}
{{if $smarty.post.sub_menu_id}}
{{$year_seme_menu}} {{$class_menu}} {{$stud_menu}} {{$work_menu}} {{$work_menu2}}{{if $mfile}} {{include file=$mfile}}{{/if}}<br><br>
{{/if}}
{{if $smarty.post.class_name || $list}}
{{if $ifile}}{{include file=$ifile}}{{/if}}
{{/if}}
</td></tr></table>
</td>
</tr>
</form>
</table>
{{/if}}

{{include file="$SFS_TEMPLATE/footer.tpl"}}


