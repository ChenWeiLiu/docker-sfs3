{{* $Id: health_wh_unmeasure.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="save" value="確定儲存"> <input type="submit" name="print" value="匯出XLS檔">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr bgcolor="#c4d9ff">
<td align="center">年級</td>
<td align="center">班級</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
<td align="center">年級</td>
<td align="center">班級</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
<td align="center">年級</td>
<td align="center">班級</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
<td align="center">年級</td>
<td align="center">班級</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=j value=$j+1}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{if $j % 4==1}}
<tr style="background-color:white;">
{{/if}}
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][height]" value="{{$dd.height}}" size="3" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][weight]" value="{{$dd.weight}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
{{if $j % 4==0}}
</tr>
{{/if}}
{{/foreach}}
{{foreachelse}}
<tr style="background-color:white;">
<td colspan="24" style="color:red;text-align:center;">無資料</td>
</tr>
{{/foreach}}
</table>
<input type="submit" name="save" value="確定儲存"> <input type="submit" name="print" value="匯出XLS檔">