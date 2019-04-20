{{* $Id: health_sight_unmeasure.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="save" value="確定儲存">
<input type="submit" name="print" value="匯出XLS檔">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">年級</td>
<td rowspan="2">班級</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td colspan="2">裸視</td>
<td colspan="2">矯正</td>
<td rowspan="2">年級</td>
<td rowspan="2">班級</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td colspan="2">裸視</td>
<td colspan="2">矯正</td>
<td rowspan="2">年級</td>
<td rowspan="2">班級</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td colspan="2">裸視</td>
<td colspan="2">矯正</td>
</tr>
<tr style="background-color:c4d9ff;text-align:center;">
<td>右眼</td>
<td>左眼</td>
<td>右眼</td>
<td>左眼</td>
<td>右眼</td>
<td>左眼</td>
<td>右眼</td>
<td>左眼</td>
<td>右眼</td>
<td>左眼</td>
<td>右眼</td>
<td>左眼</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num}}
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
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][r][sight_o]" value="{{$dd.r.sight_o}}" size="2" style="background-color:#f8f8f8;font-size:12px;"><input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][r][sight_o]" value="{{$dd.r.sight_o}}"></td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][l][sight_o]" value="{{$dd.l.sight_o}}" size="2" style="background-color:#f8f8f8;font-size:12px;"><input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][l][sight_o]" value="{{$dd.l.sight_o}}"></td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][r][sight_r]" value="{{$dd.r.sight_r}}" size="2" style="background-color:#f8f8f8;font-size:12px;"><input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][r][sight_r]" value="{{$dd.r.sight_r}}"></td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][l][sight_r]" value="{{$dd.l.sight_r}}" size="2" style="background-color:#f8f8f8;font-size:12px;"><input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][l][sight_r]" value="{{$dd.l.sight_r}}"></td>
{{if $j % 3==0}}
</tr>
{{/if}}
{{/foreach}}
{{foreachelse}}
<tr style="background-color:white;text-align:center;color:red;">
<td colspan="24">無資料</td>
</tr>
{{/foreach}}
</table>
<input type="submit" name="save" value="確定儲存">
<input type="submit" name="print" value="匯出XLS檔">
</td></tr></table>
