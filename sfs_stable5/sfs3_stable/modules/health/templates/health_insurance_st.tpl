{{* $Id: health_insurance_st.tpl 5830 2010-01-15 13:37:49Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css"rel="stylesheet" type="text/css"/>
<style>
.odd  {background:#fff}
.even{background:#f4feff}
</style>
<script>
$(document).ready(function(){
	$("#insurance-table tbody tr:even").addClass('even');
	$("#insurance-table tbody tr:odd").addClass('odd');
});
</script>
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor="white">
<table border="0"><tr><td valign="top">
{{*選單*}}
<table class="tableBg" cellspacing="1" cellpadding="1">
<tr><td align="center" class="leftmenu">
{{$stud_menu}}
</td>
</tr>
</table>
</td><td valign="top">

{{if $smarty.post.student_sn}}
{{assign var=sn value=$smarty.post.student_sn}}
{{include file="health_stud_now.tpl"}}

</td><td valign="top">
{{* 保險 *}}
<table id="insurance-table" bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<thead>
<tr>
<td style="color:white;text-align:center;">保險</td>
</tr>
</thead>
<tbody>
{{foreach from=$ins_arr key=key item=item}}
<tr>
<td><input type="checkbox" name="update[{{$sn}}][health_insurance_record][id][]" value="{{$key}}" {{if $health_data->stud_base.$sn.insurance.$key}}checked{{/if}}>{{$item}}</td>
</tr>
{{/foreach}}
<tr>
<td><input type="checkbox" name="">其他保險<input type="text" name="other_insurance"></td>
</tr>
</tbody>
</table>
<input type="submit" name="sure" value="確定"> <input type="submit" value="取消"> <input type="button" OnClick="window.opener.renew(1);window.close();" value="關閉本視窗">
</td></tr>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="nav_prior" value="{{$smarty.post.nav_prior}}">
<input type="hidden" name="nav_next" value="{{$smarty.post.nav_next}}">
<input type="hidden" name="act" value="{{$smarty.post.act}}">
</form></table>
{{/if}}
</td></tr></table>
</td></tr></table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
