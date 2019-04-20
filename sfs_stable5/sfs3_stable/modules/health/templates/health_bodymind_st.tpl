{{* $Id: health_bodymind_st.tpl 5707 2009-10-23 14:35:07Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css"rel="stylesheet" type="text/css"/>

<script>
function check_value() {
	if (document.getElementById('u1').value=='') {
		alert('未輸入「診斷代號」');
		return false;
	}
	if (document.getElementById('u2').value=='') {
		alert('未輸入「等級」');
		return false;
	}
	return true;
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
{{* 身心障礙手冊 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="4" style="color:white;text-align:center;">身心障礙手冊</td>
</tr>
<tr bgcolor="#f4feff">
<td>診斷代號</td><td>疾病名稱</td><td>等級</td><td>功能選項</td>
</tr>
{{if $health_data->stud_base.$sn.bodymind}}
{{assign var=dd value=$health_data->stud_base.$sn.bodymind.bm_id}}
{{assign var=lv value=$health_data->stud_base.$sn.bodymind.bm_level}}
<tr bgcolor="white">
<td>{{$dd}}</td><td>{{$bodymind_kind_arr.$dd}}</td><td>{{$bodymind_level_arr.$lv}}</td><td><input type="image" src="images/edit.gif" OnClick="this.form.edit.value=1;this.form.submit();" alt="編輯這筆資料"> <input type="image" name="del[{{$sn}}][health_bodymind][bm_id]" src="images/delete.gif" alt="刪除這筆資料"></td>
</tr>
{{else}}
<tr bgcolor="white">
<td colspan="4" style="text-align:center;color:blue;">無資料</td>
</tr>
{{/if}}
</table>
{{if $smarty.post.edit}}
<br>
<span class="small">類別代號：</span><input type="text" id="u1" name="update[{{$sn}}][health_bodymind][bm_id]" OnDblClick="showDropDownItem(this,'{{$bodymind_kind_str}}',1,0,2);" style="background-color:#FFFFC0;width:25px;" {{if $dd}}value="{{$dd}}"{{/if}}>
<span class="small">等級：</span><input type="text" id="u2" name="update[{{$sn}}][health_bodymind][bm_level]" OnDblClick="showDropDownItem(this,'{{$level_str}}',1,0,1);" style="background-color:#FFFFC0;width:25px;" {{if $lv}}value="{{$lv}}"{{/if}}>
<input type="button" value="確定" OnClick="if (check_value()) {this.form.sure.value='1';this.form.submit();}"> <input type="submit" value="取消">
{{elseif !$health_data->stud_base.$sn.bodymind}}
<input type="button" OnClick="this.form.edit.value=1;this.form.submit();" value="新增資料">
{{/if}}
<input type="button" OnClick="window.opener.renew(1);window.close();" value="關閉本視窗">

{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>一個人最多只有一筆資料。</li>
	<li>在「類別代號欄」、「等級欄」中雙擊滑鼠左鍵可即出<br>現「類別代號表」、「等級表」。</li>
	</ol>
</td></tr>
</table>

</td></tr>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="nav_prior" value="{{$smarty.post.nav_prior}}">
<input type="hidden" name="nav_next" value="{{$smarty.post.nav_next}}">
<input type="hidden" name="act" value="{{$smarty.post.act}}">
<input type="hidden" name="edit" value="">
<input type="hidden" name="sure" value="">
</form></table>
{{/if}}
</td></tr></table>
</td></tr></table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
