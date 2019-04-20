{{* $Id: stud_list_stud_list.tpl 8964 2016-09-05 07:34:08Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}" target="_blank">
<tr><td bgcolor='#FFFFFF'>
<table width="100%">
<tr><td>
<input type="submit" name="print_out" value="列出名條">
<input type="submit" name="csv_out" value="匯出CVS檔">
<input type="submit" name="csv_out_all" value="匯出全校CVS檔">
<input type="checkbox" name="noOther" value="1">不列備註欄
{{if $sex_enable}}<input type="checkbox" name="sex" value="1">列出性別{{/if}}<br>
<table border="0" cellspacing="1" cellpadding="4" bgcolor="#cccccc" class="main_body">
<tr bgcolor="#E1ECFF" align="center">
<td>第一列班級</td>
<td>第二列班級</td>
<td>第三列班級</td>
</tr>
{{foreach from=$class_arr item=class_name key=cid}}
<tr bgcolor="#FFFFFF">
<td><input type="radio" name="c_id[1]" value="{{$cid}}">{{$class_name}}</td>
<td><input type="radio" name="c_id[2]" value="{{$cid}}">{{$class_name}}</td>
<td><input type="radio" name="c_id[3]" value="{{$cid}}">{{$class_name}}</td>
</tr>
{{/foreach}}
</table>
<input type="submit" name="print_out" value="列出名條">
<input type="submit" name="csv_out" value="匯出CVS檔">
<input type="submit" name="csv_out_all" value="匯出全校CVS檔">
{{if $sex_enable}}<input type="checkbox" name="sex" value="1">列出性別{{/if}}
</td></tr>
</table>
</td></tr>
</form>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
