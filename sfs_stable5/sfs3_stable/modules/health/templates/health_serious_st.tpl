{{* $Id: health_serious_st.tpl 5707 2009-10-23 14:35:07Z brucelyc $ *}}
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
{{* 重大傷病卡 *}}
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="3" style="color:white;text-align:center;">重大傷病卡</td>
</tr>
<tr bgcolor="#f4feff">
<td>診斷代號</td><td>疾病名稱</td><td>功能選項</td>
</tr>
{{foreach from=$health_data->stud_base.$sn.serious item=d}}
<tr bgcolor="white">
<td>{{$d}}</td><td>{{$disease_kind_arr.$d}}</td><td><input type="image" src="images/delete.gif" alt="刪除這筆資料" OnClick="document.getElementById('del').value='{{$d}}';"></td>
</tr>
{{foreachelse}}
<tr bgcolor="white">
<td colspan="3" style="text-align:center;color:blue;">無資料</td>
</tr>
{{/foreach}}
</table>
{{if $smarty.post.edit}}
<br><span class="small">診斷代號：</span><input type="text" name="update[{{$sn}}][health_diseaseserious][di_id]" OnDblClick="showDropDownItem(this,'{{$disease_kind_str}}',1,0,2);" style="background-color:#FFFFC0;width:25px;"><input type="submit" name="sure" value="確定"> <input type="submit" value="取消">
{{else}}
<input type="submit" name="edit" value="新增資料">
{{/if}}
<input type="button" OnClick="window.opener.renew(1);window.close();" value="關閉本視窗">

{{if $smarty.post.edit}}
{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>在「診斷代號欄」中雙擊滑鼠左鍵即可出現「診斷代號表」。</li>
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
<input type="hidden" id="del" name="del[{{$sn}}][health_diseaseserious][di_id]" value="">
</form></table>
{{/if}}
</td></tr></table>
</td></tr></table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
