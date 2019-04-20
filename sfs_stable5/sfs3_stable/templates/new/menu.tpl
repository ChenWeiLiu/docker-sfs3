{{*//$Id: menu.tpl 5310 2009-01-10 07:57:56Z hami $*}}
{{*模組內功能表項目*}}
<table cellspacing=1 cellpadding=3><tr>
{{foreach from=$SFS_MENU key=key item=item}}
{{if $CURR_SCRIPT == $key}}<td class='tab' bgcolor='#FFF158'>&nbsp;<a href="{{$key}}{{if $SFS_MENU_LINK}}?{{$SFS_MENU_LINK}}{{/if}}">{{$item}}</a>&nbsp;</td>
{{else}}<td class='tab' bgcolor='#EFEFEF'>&nbsp;<a href="{{$key}}{{if $SFS_MENU_LINK}}?{{$SFS_MENU_LINK}}{{/if}}">{{$item}}</a>&nbsp;</td>{{/if}}
{{/foreach}}
</tr></table>
