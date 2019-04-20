{{* $Id: health_analyze_wh_stunting.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>性別</td>
<td>出生年月日</td>
<td>足歲</td>
<td>身高值</td>
<td>家長姓名</td>
<td>連絡住址</td>
<td>連絡電話</td>
<td>診斷狀況</td>
<td>診斷醫院</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{assign var=sex value=$health_data->stud_base.$sn.stud_sex}}
{{if $dd.stunting}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#f4feff;text-align:center;">{{if $health_data->stud_base.$sn.stud_sex==1}}男{{elseif $health_data->stud_base.$sn.stud_sex==2}}女{{else}}--{{/if}}</td>
<td style="text-align:center;">{{$health_data->stud_base.$sn.stud_birthday}}</td>
<td style="text-align:center;">{{$dd.years}}</td>
<td style="text-align:center;">{{$dd.height}}</td>
<td style="text-align:center;">{{$health_data->stud_base.$sn.guardian_name}}</td>
<td>{{$health_data->stud_base.$sn.stud_addr_2}}</td>
<td>{{$health_data->stud_base.$sn.stud_tel_2}}</td>
<td></td>
<td></td>
</tr>
{{/if}}
{{/foreach}}
{{/foreach}}
</table>
</td></tr></table>