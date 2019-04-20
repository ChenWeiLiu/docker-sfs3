{{* $Id: absent_add_record_person.tpl 6236 2010-10-20 03:36:26Z infodaes $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table cellspacing="1" cellpadding="4" bgcolor="#9EBCDD">
<form name="form1" action="{{$smarty.server.SCRIPT_NAME}}" method="post">
<tr class="title_sbody1">
<td class="title_sbody2">學生學號</td>
<td colspan="7" align="left"><input type="text" name="stud_id" value="{{$smarty.post.stud_id}}" size="10"><input type="submit" name="change" value="更換學生"></td>
</tr>
</form>
<form name="form2" action="{{$smarty.server.SCRIPT_NAME}}" method="post">
{{if $stud_rows}}
{{foreach from=$stud_rows item=d key=i}}
<tr class="title_sbody2">
{{if $i==0}}
<td align="center" colspan="2" rowspan="{{$stud_nums}}">學生列表</td>
{{/if}}
{{assign var=d_id value=$d.stud_study_cond}}
<td bgcolor="white" colspan="6" align="left">
<input type="radio" name="student_sn" value="{{$d.student_sn}}" OnClick="this.form.submit();">
<span style="color:{{if $d.stud_sex==1}}blue{{elseif $d.stud_sex==2}}red{{else}}black{{/if}};">{{$d.stud_name}}</span>
({{$d.stud_study_year}}年入學)
({{$study_cond.$d_id}})
</td>
</tr>
{{/foreach}}
{{/if}}
{{if $rowdata && $stud_name}}
<tr class="title_sbody1"><td class="title_sbody2">學生姓名</td><td colspan="7" align="left">{{$stud_name}}</td></tr>
<tr class="title_sbody1"><td class="title_sbody2">在學狀態</td><td colspan="7" align="left">{{$study_cond[$stud_study_cond]}}</td></tr>
<tr class="title_sbody2"><td align="center">學年度</td><td align="center">學期</td>
{{foreach from=$abs_kind item=d}}
<td align="center">{{$d}}</td>
{{/foreach}}
</tr>
{{foreach from=$rowdata item=v key=i}}
<tr class="title_sbody1"><td align="center">{{$i|@substr:0:-1}}</td><td align="center">{{$i|@substr:-1:1}}</td>
{{foreach from=$abs_kind item=d key=j}}
<td align="center"><input type="text" name="abs_data[{{$i}}][{{$j}}]" value="{{$rowdata[$i].$j}}" size="3"></td>
{{/foreach}}
</tr>
{{/foreach}}
</table>
<p style="font-size:3pt"></p>
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="submit" name="sure" value="儲存"><input type="reset" value="回復原有值">
{{/if}}
</form></table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
