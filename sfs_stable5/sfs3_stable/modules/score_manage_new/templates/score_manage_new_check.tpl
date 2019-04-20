{{* $Id: score_manage_new_check.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>
{{$year_seme_menu}} {{$class_year_menu}}
<br><br>檢查該學期各階段成績儲存時為「空值」（未輸入成績即儲存）的學生記錄
{{if $err_msg}}<br><font color="red">{{$err_msg}}</font>{{/if}}
</td>
{{if $smarty.post.year_name && !$err_msg}}
<table cellpadding="5" cellspacing="1" border="0" bgcolor="#0000ff" align="left">
<tr bgcolor="#ffffff">
<td>班級<td>座號<td>姓名<td>科目<td>段次<td>成績種類</td>
</tr>
{{foreach from=$score_data item=d}}
{{assign var=sn value=$d.student_sn}}
{{assign var=ss_id value=$d.ss_id}}
{{assign var=subject_id value=$ss_data.$ss_id}}
<tr bgcolor="#ffffff">
<td>{{$seme_data.$sn.seme_class}}<td>{{$seme_data.$sn.seme_num}}<td>{{$d.stud_name}}<td>{{$subject_data.$subject_id}}<td>{{$d.test_sort}}<td>{{$d.test_kind}}</td>
</tr>
{{foreachelse}}
<tr bgcolor="#ffffff">
<td colspan="6" style="text-align:center;color:blue;">該學期該年級無空白成績</td>
</tr>
{{/foreach}}
</table>
{{/if}}
</tr>
</table>
</td></tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
