{{* $Id: health_import.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table border="0" cellspacing="0" cellpadding="0"><tr><td style="vertical-align:top;">
<table cellspacing="1" cellpadding="3" class="main_body">
<tr bgcolor="#FFFFFF">
</form>
<form name="form0" enctype="multipart/form-data" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>上傳檔案：</td>
<td colspan="2"><input type="file" name="upload_file"><input type="submit" name="doup_key" value="上傳"><input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}"></td>
</form>
</tr>
<tr bgcolor="#FFFFFF">
<form name="form1" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>伺服器內存檔案：</td>
<td colspan="2">{{$file_menu}}{{if $chk_file}} &nbsp;<span style="color:red;">({{$chk_file}})</span>{{/if}}<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}"></td>
</tr>
</table>
</td>
</tr>
</table>