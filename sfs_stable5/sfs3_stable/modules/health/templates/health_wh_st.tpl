{{* $Id: health_wh_st.tpl 5708 2009-10-23 15:33:08Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css"rel="stylesheet" type="text/css"/>
<script>
function chk_h() {
	b="h";
	c=document.getElementById(b).value;
	if (c < 70 || c > 226) {
		alert("合理身高範圍應介於70公分～226公分之間！\n請重新輸入！");
		d="oh";
		document.getElementById(b).value=document.getElementById(d).value;
		document.getElementById(b).focus();
	}
}
function chk_w() {
	b="w";
	c=document.getElementById(b).value;
	if (c < 10 || c > 150) {
		alert("合理體重範圍應介於10公斤～150公斤之間！\n請重新輸入！");
		d="ow";
		document.getElementById(b).value=document.getElementById(d).value;
		document.getElementById(b).focus();
	}
}
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
{{* 身高體重 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="5" style="color:white;text-align:center;">身高體重</td>
</tr>
<tr style="background-color:#f4feff;text-align:center;">
<td>學年度</td><td>學期</td><td>身高</td><td>體重</td><td>功能選項</td>
</tr>
{{foreach from=$health_data->health_data.$sn item=d key=ys}}
<tr style="background-color:white;text-align:center;">
<td>{{$ys|@substr:0:-1|@intval}}</td>
<td>{{$ys|@substr:-1:1}}</td>
<td>{{if $smarty.post.edit.$sn.$ys}}<input type="text" name="update[new][{{$sn}}][{{$ys}}][height]" id="h" value="{{$d.height}}" size="5" OnChange="chk_h();"><input type="hidden" name=name="update[old][{{$sn}}][{{$ys}}][height]" id="oh" value="{{$d.height}}">{{else}}{{$d.height}}{{/if}}</td>
<td>{{if $smarty.post.edit.$sn.$ys}}<input type="text" name="update[new][{{$sn}}][{{$ys}}][weight]" id="w" value="{{$d.weight}}" size="5" OnChange="chk_w();"><input type="hidden" name=name="update[old][{{$sn}}][{{$ys}}][weight]" id="ow" value="{{$d.weight}}">{{else}}{{$d.weight}}{{/if}}</td>
<td style="text-align:left;">
{{if $smarty.post.edit.$sn.$ys}}
<input type="image" name="ok" src="images/ok.png" OnClick="this.form.ok.value=1">
<input type="image" src="images/no.png">
{{else}}
<input type="image" src="images/edit.png" name="edit[{{$sn}}][{{$ys}}]">
{{if $d.height!="" && $d.weight!=""}}
<input type="image" src="images/delete.png" name="del[{{$sn}}][{{$ys}}][height]" OnClick="return confirm('確定要刪除 {{$health_data->stud_base.$sn.stud_name}} {{$ys|@substr:0:-1|@intval}}學年度第{{$ys|@substr:-1:1}}學期的身高體重資料 ?');">
{{/if}}
{{/if}}
</td>
</tr>
{{/foreach}}
</table>
<input type="button" OnClick="window.opener.renew(2);window.close();" value="關閉本視窗">
</td></tr>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="nav_prior" value="{{$smarty.post.nav_prior}}">
<input type="hidden" name="nav_next" value="{{$smarty.post.nav_next}}">
<input type="hidden" name="act" value="{{$smarty.post.act}}">
<input type="hidden" name="ok" value="">
</form></table>
{{/if}}
</td></tr></table>
</td></tr></table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
