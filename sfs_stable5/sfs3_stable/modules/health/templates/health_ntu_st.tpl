{{* $Id: health_ntu_st.tpl 5707 2009-10-23 14:35:07Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script src="js/DropDownControl.js" language="javascript"></script>
<link href="js/DropDownControl.css"rel="stylesheet" type="text/css"/>

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
{{* 立體感 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="4" style="color:white;text-align:center;">立體感</td>
</tr>
<tr bgcolor="#f4feff">
<td>診斷代號</td><td>診斷</td><td>醫院</td><td>功能選項</td>
</tr>
{{if $data}}
<tr bgcolor="white">
<td>　</td><td>　</td><td>　</td><td><input type="image" src="images/delete.gif" alt="刪除這筆資料"></td>
</tr>
{{else}}
<tr bgcolor="white">
<td colspan="4" style="text-align:center;color:blue;">無資料</td>
</tr>
{{/if}}
</table>
{{if $smarty.post.edit}}
<input type="submit" name="sure" value="確定"> <input type="submit" value="取消">
{{else}}
<input type="submit" name="edit" value="新增資料">
{{/if}}
<input type="button" OnClick="window.opener.renew(1);window.close();" value="關閉本視窗">
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>一個人最多只有一筆資料。</li>
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
</form></table>
{{/if}}
</td></tr></table>
</td></tr></table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
