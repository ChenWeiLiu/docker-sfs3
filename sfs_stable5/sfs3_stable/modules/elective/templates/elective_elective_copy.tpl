{{* $Id: elective_elective_copy.tpl 8512 2015-09-02 01:44:17Z smallduh $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
<!--
function chg() {
	document.myform.curr.value=1-document.myform.curr.value;
	document.myform.submit();
}
-->
</script>

<table border="0" cellspacing="1" cellpadding="6" width="100%" bgcolor="#B0C0F8">
<tr bgcolor="#FFF6BA">
<td>
<table align="center" width="99%"><tr>
<td width="1%" nowrap>
<form method="post" action="{{$smarty.server.PHP_SELF}}">
{{$class_year_menu}}
</form>
</td>
<td width="1%" nowrap>
{{if $smarty.post.c_year}}
<form method="post" action="{{$smarty.server.PHP_SELF}}">
<input type="hidden" name="c_year" value="{{$smarty.post.c_year}}">
{{$subject_menu}}
</form>
{{/if}}</td>
<td>
{{if $smarty.post.ss_id}}
<form method="post" action="{{$smarty.server.PHP_SELF}}">
<input type="hidden" name="c_year" value="{{$smarty.post.c_year}}">
<input type="hidden" name="ss_id" value="{{$smarty.post.ss_id}}">
{{$class_menu}}
{{if $smarty.post.group_id}}<input type="submit" name="clear" value="清空學生">{{/if}}
</form>
{{/if}}</td>
</tr>
{{if $smarty.post.group_id}}
<tr><td colspan="3">
<table cellspacing="1" cellpadding="6" border="0" bgcolor="#211BC7" width="100%" align="center">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr bgcolor='#FFFFFF'><td>
請選想要複製的來源班級：<input type="checkbox" {{if $smarty.post.curr}}checked{{/if}} OnClick="chg();">只顯示當學期資料<br>
<table cellspacing="1" cellpadding="6" border="0" bgcolor="#211BC7">
<tr bgcolor="#B6BFFB"><td>選取</td><td>學年度</td><td>學期</td><td>分組名稱</td><td>任課教師</td><td>已編人數 / 最多人數</td><td>開放自選</td></tr>
{{foreach from=$rowdata item=d}}
{{assign var=tsn value=$d.teacher_sn}}
{{assign var=gid value=$d.group_id}}
<tr bgcolor="#E1E5F5"><td align="center"><input type="radio" name="sel_group" value="{{$gid}}"></td><td>{{$d.year}}</td><td>{{$d.semester}}</td><td>({{$d.class_year}}年級) {{$d.group_name}}</td><td>{{$tea_arr.$tsn}}</td><td align="center">{{$stu_num.$gid.num|@intval}} / {{$d.member}}</td><td>{{$d.open}}</td>
</tr>
{{/foreach}}
</table><br>
<input type="submit" name="copy" value="開始複製">
</td></tr>
<input type="hidden" name="c_year" value="{{$smarty.post.c_year}}">
<input type="hidden" name="ss_id" value="{{$smarty.post.ss_id}}">
<input type="hidden" name="group_id" value="{{$smarty.post.group_id}}">
<input type="hidden" name="curr" value="{{$smarty.post.curr}}">
</form>
</table>
{{/if}}
</td></tr></table>
</td></tr></table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
