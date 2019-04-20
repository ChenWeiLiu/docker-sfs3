{{* $Id: reward_reward_list.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
<!--
function chg() {
	document.myform.un.value=1-document.myform.un.value;
	document.myform.action='reward_list.php';
	document.myform.target='';
	document.myform.submit();
}
function show(a) {
	document.myform.action='reward_stud_all.php';
	document.myform.target='_blank';
	document.myform.student_sn.value=a;
	document.myform.submit();
}
-->
</script>

<input type="checkbox" OnClick="chg();" {{if $smarty.post.un}}checked{{/if}}><span class="small">只統計未銷過記錄</span>
<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<form name="myform" action="{{$smarty.server.PHP_SELF}}" method="post">
<tr class="title_sbody2" style="text-align:center;">
<td>觀看</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
{{foreach from=$reward_kind item=d}}
<td>{{$d}}</td>
{{/foreach}}
</tr>
{{foreach from=$studata item=d key=i}}
<tr class="title_sbody1" style="text-align:center;background-color:{{cycle values="white,#E6F2FF"}};">
<td><input type="radio" name="sel" OnClick="show('{{$i}}');"></td>
<td>{{$d.class}}</td>
<td>{{$d.num}}</td>
<td style="color:{{if $d.stud_sex==1}}blue{{else}}red{{/if}};">{{$d.stud_name}}</td>
{{foreach from=$reward_kind item=dd key=ii}}
<td>{{$rowdata.$i.$ii}}</td>
{{/foreach}}
</tr>
{{/foreach}}
<input type="hidden" name="un" value="{{$smarty.post.un}}">
<input type="hidden" name="student_sn" value="">
</form>
</table>
</tr></table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
