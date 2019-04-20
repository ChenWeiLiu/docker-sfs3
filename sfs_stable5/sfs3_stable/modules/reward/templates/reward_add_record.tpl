{{* $Id: reward_add_record.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table cellspacing="1" cellpadding="3" bgcolor="#C6D7F2">
<form action="{{$smarty.server.PHP_SELF}}" method="post">
<tr bgcolor="#FFFFFF"><td>
{{$year_seme_sel}} {{$class_year_sel}}

{{if $rowdata}}
<p style="font-size:3pt"></p>
<table cellspacing="1" cellpadding="3" bgcolor="#C6D7F2" class="small">
<tr bgcolor="#E6F2FF">
<td align="center">學號</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center" bgcolor="#FFE6D9">在學狀態</td>
{{foreach from=$reward_kind item=d}}
<td align="center" bgcolor="#ECff8F9">{{$d}}</td>
{{/foreach}}
</tr>
{{section loop=$rowdata name=i}}
<tr bgcolor="#E6F2FF">
<td align="center" bgcolor="white">{{$rowdata[i].stud_id}}</td>
<td align="center" bgcolor="white">{{$rowdata[i].seme_num}}</td>
<td align="center" bgcolor="white">{{$rowdata[i].stud_name}}</td>
{{assign var=d value=$rowdata[i].stud_study_cond}}
{{assign var=cond value=$study_cond[$d]}}
<td align="center" bgcolor="#FFE6D9">{{$cond}}</td>
{{foreach from=$reward_kind item=d key=j}}
{{assign var=id value=$rowdata[i].stud_id}}
<td align="center" bgcolor="#ECff8F9"><input type="text" name="reward_data[{{$rowdata[i].stud_id}}][{{$j}}]" value="{{$reward_data[$id][$j]}}" size="3"></td>
{{/foreach}}
</tr>
{{/section}}
</table>
<p style="font-size:3pt"></p>
<input type="submit" name="sure" value="儲存"><input type="submit" name="reset" value="回復原有值">
{{/if}}
</td>
</tr></form></table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
