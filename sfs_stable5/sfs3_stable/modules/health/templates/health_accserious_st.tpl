{{* $Id: health_accserious_st.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css" rel="stylesheet" type="text/css">
{{dhtml_calendar_init}}

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
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<form name="myform" action="{{$smarty.post.PHP_SELF}}" method="post">
<tr>
<td colspan="2" style="color:white;text-align:center;">目前編輯學生</td>
</tr>
<tr bgcolor="#f4feff">
<td>統編</td><td>{{$health_data->stud_base.$sn.stud_person_id}}</td>
</tr>
<tr bgcolor="white">
<td>學生</td><td>{{$health_data->stud_base.$sn.stud_name}}</td>
</tr>
<tr bgcolor="#f4feff">
<td>學號</td><td>{{$health_data->stud_base.$sn.stud_id}}</td>
</tr>
<tr bgcolor="white">
<td>生日</td><td>{{$health_data->stud_base.$sn.stud_birthday}}</td>
</tr>
<tr bgcolor="#f4feff">
<td>父親</td><td>{{$health_data->stud_base.$sn.fath_name}}</td>
</tr>
<tr bgcolor="white">
<td>母親</td><td>{{$health_data->stud_base.$sn.moth_name}}</td>
</tr>
<tr bgcolor="#f4feff">
<td>緊急連絡</td><td>{{$health_data->stud_base.$sn.stud_tel_2}}</td>
</tr>
</table>

</td><td valign="top">
{{* 在校期間重大傷病 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="3" style="color:white;text-align:center;">在校期間重大傷病</td>
</tr>
<tr bgcolor="#f4feff">
<td>事件</td><td>日期</td><td>功能選項</td>
</tr>
{{if $data}}
<tr bgcolor="white">
<td>　</td><td>　</td><td><input type="image" src="images/edit.gif" alt="編輯這筆資料"> <input type="image" src="images/delete.gif" alt="刪除這筆資料"></td>
</tr>
{{else}}
<tr bgcolor="white">
<td colspan="3" style="text-align:center;color:blue;">無資料</td>
</tr>
{{/if}}
</table>
{{if $smarty.post.edit}}
<span class="small">傷病或事故：</span><input type="text" style="width:122px;">
<span class="small">日期：</span><input type="text" id="acc_date" style="background-color:#FFFFC0;width:122px;">
<br>
<input type="submit" name="sure" value="確定"> <input type="submit" value="取消">
{{else}}
<input type="submit" name="edit" value="新增資料">
{{/if}}
<input type="button" OnClick="window.close();" value="關閉本視窗">

{{if $smarty.post.edit}}
{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>在「日期欄」中雙擊滑鼠左鍵即可出現「小月曆」。</li>
	</ol>
</td></tr>
</table>
{{/if}}

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

{{if $smarty.post.edit}}
{{*日期輸入設定*}}
<script type="text/javascript">
Calendar.setup({
	inputField  : "acc_date",     // id of the input field
	ifFormat    : "%Y-%m-%d",     // format of the input field (even if hidden, this format will be honored)
	singleClick : false           // double-click mode
});
</script>
{{/if}}

{{include file="$SFS_TEMPLATE/footer.tpl"}}