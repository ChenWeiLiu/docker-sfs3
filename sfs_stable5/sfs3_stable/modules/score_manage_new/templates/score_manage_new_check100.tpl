{{* $Id: score_manage_new_check.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>
◎檢查學期：{{$year_seme_menu}}
{{if $err_msg}}<br><font color="red">{{$err_msg}}</font>{{/if}}
</td>
{{if !$err_msg}}
<table cellpadding="5" cellspacing="1" border="0" bgcolor="#0000ff" align="left">
<tr bgcolor="#ffCCCC" align='center'>
<td>班級</td><td>座號</td><td>姓名</td><td>科目</td><td>段次</td><td>成績種類</td><td>分數</td><td>成績輸入者</td>
</tr>
{{foreach from=$score_data item=d}}
{{assign var=sn value=$d.student_sn}}
{{assign var=teacher_sn value=$d.teacher_sn}}
{{assign var=ss_id value=$d.ss_id}}
{{assign var=subject_id value=$ss_data.$ss_id}}
<tr bgcolor="#ffffff" align='center'>
<td>{{$seme_data.$sn.seme_class}}</td><td>{{$seme_data.$sn.seme_num}}</td><td>{{$d.stud_name}}</td><td>{{$subject_data.$subject_id}}</td><td>{{$d.test_sort}}</td><td>{{$d.test_kind}}</td><td>{{$d.score}}</td><td>{{$teacher_array.$teacher_sn}}</td>
</tr>
{{foreachelse}}
<tr bgcolor="#ffffff">
<td colspan="8" style="text-align:center;color:blue;">該學期無超出100分的成績紀錄</td>
</tr>
{{/foreach}}
</table>
{{/if}}
</tr>
</table>
</td></tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
