{{* $Id: health_sight_list.tpl 5448 2009-04-14 07:19:57Z brucelyc $ *}}

<input type="submit" name="xls" value="匯出XLS檔">
<input type="submit" name="ods" value="匯出ODS檔">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr bgcolor="#c4d9ff">
<td align="center" rowspan="2">年級</td>
<td align="center" rowspan="2">班級</td>
<td align="center" rowspan="2">座號</td>
<td align="center" rowspan="2">姓名</td>
<td align="center" colspan="2">裸視</td>
<td align="center" colspan="2">矯正</td>
<td align="center" rowspan="2">年級</td>
<td align="center" rowspan="2">班級</td>
<td align="center" rowspan="2">座號</td>
<td align="center" rowspan="2">姓名</td>
<td align="center" colspan="2">裸視</td>
<td align="center" colspan="2">矯正</td>
<td align="center" rowspan="2">年級</td>
<td align="center" rowspan="2">班級</td>
<td align="center" rowspan="2">座號</td>
<td align="center" rowspan="2">姓名</td>
<td align="center" colspan="2">裸視</td>
<td align="center" colspan="2">矯正</td>
</tr>
<tr bgcolor="#c4d9ff">
<td align="center">右眼</td>
<td align="center">左眼</td>
<td align="center">右眼</td>
<td align="center">左眼</td>
<td align="center">右眼</td>
<td align="center">左眼</td>
<td align="center">右眼</td>
<td align="center">左眼</td>
<td align="center">右眼</td>
<td align="center">左眼</td>
<td align="center">右眼</td>
<td align="center">左眼</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=j value=0}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=j value=$j+1}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{if $j % 3==1}}
<tr style="background-color:white;">
{{/if}}
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="text-align:center;color:{{if $dd.r.sight_o<0.9}}red{{else}}blue{{/if}};">{{$dd.r.sight_o}}</td>
<td style="text-align:center;color:{{if $dd.l.sight_o<0.9}}red{{else}}blue{{/if}};">{{$dd.l.sight_o}}</td>
<td style="text-align:center;color:{{if $dd.r.sight_r<0.5}}red{{else}}blue{{/if}};">{{$dd.r.sight_r}}</td>
<td style="text-align:center;color:{{if $dd.l.sight_r<0.5}}red{{else}}blue{{/if}};">{{$dd.l.sight_r}}</td>
{{if $j % 3==0}}
</tr>
{{/if}}
{{foreachelse}}
<tr><td colspan="24" style="background-color:white;text-align:center;color:red;">無資料</td></tr>
{{/foreach}}
{{/foreach}}
</table>
{{if $j>0}}共{{$j}}筆{{/if}}
</td></tr></table>
</td>
</tr>
</table>
