{{* $Id: reward_add_record_person.tpl 6920 2012-10-01 08:25:16Z infodaes $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table cellspacing="1" cellpadding="4" bgcolor="#9EBCDD">
<form action="{{$smarty.server.PHP_SELF}}" method="post">
<tr class="title_sbody1">
<td align="center">1.抓取學號</td><td align='left' bgcolor='white' colspan="7">{{$year_seme_select}}
{{$class_select}}{{if $smarty.post.class_id}}{{$stud_select}}<input type="submit" value="抓取學號">{{/if}}</td>
</tr>
<tr class="title_sbody1">
<td class="title_sbody2">2.學號或身分字號查詢</td>
<td colspan="7" align="left"><input type="text" name="stud_id" value="{{$smarty.post.stud_id}}" size="10"><input type="submit" name="change" value="更換學生"></td>
</tr>
</form>
{{if $stud_rows}}
<form action="{{$smarty.server.SCRIPT_NAME}}" method="post">
{{foreach from=$stud_rows item=d key=i}}
<tr class="title_sbody2">
{{if $i==0}}
<td align="center" colspan="2" rowspan="{{$stud_nums}}">3.學生列表</td>
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
</form>
{{/if}}
{{if $rowdata && $stud_name}}
<form action="{{$smarty.server.SCRIPT_NAME}}" method="post">
<tr class="title_sbody1"><td class="title_sbody2">學生姓名</td><td colspan="7" align="left">{{$stud_name}}</td></tr>
<tr class="title_sbody1"><td class="title_sbody2">在學狀態</td><td colspan="7" align="left">{{$study_cond[$stud_study_cond]}}</td></tr>
<tr class="title_sbody2"><td align="center">學年度</td><td align="center">學期</td>
{{foreach from=$reward_kind item=d}}
<td align="center">{{$d}}</td>
{{/foreach}}
</tr>
{{foreach from=$rowdata item=v key=i}}
<tr class="title_sbody1"><td align="center">{{$i|@substr:0:-1}}</td><td align="center">{{$i|@substr:-1:1}}</td>
{{foreach from=$reward_kind item=d key=j}}
<td align="center"><input type="text" name="reward_data[{{$i}}][{{$j}}]" value="{{$rowdata[$i].$j}}" size="3"></td>
{{/foreach}}
</tr>
{{/foreach}}
</table>
<p style="font-size:3pt"></p>
<input type="submit" name="sure" value="儲存"><input type="submit" name="reset" value="回復原有值">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
{{/if}}
</form></table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
