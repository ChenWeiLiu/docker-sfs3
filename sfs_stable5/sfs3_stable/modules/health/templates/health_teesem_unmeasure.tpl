{{* $Id: health_teesem_unmeasure.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="print" value="列印清單">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{if $dd.chkOra != 1}}
{{assign var=j value=$j+1}}
{{if $j % 4==1}}
<tr style="background-color:white;">
{{/if}}
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
{{if $j % 4==0}}
</tr>
{{/if}}
{{/if}}
{{/foreach}}
{{if $j==0}}
<tr style="background-color:white;text-align:center;color:red;">
<td colspan="16">無資料</td>
</tr>
{{/if}}
{{foreachelse}}
<tr style="background-color:white;text-align:center;color:red;">
<td colspan="16">無資料</td>
</tr>
{{/foreach}}
</table>
<input type="submit" name="print" value="列印清單">
</td></tr></table>