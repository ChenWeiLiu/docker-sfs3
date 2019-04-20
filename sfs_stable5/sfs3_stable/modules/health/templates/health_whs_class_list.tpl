{{* $Id: health_whs_class_list.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="xls" value="匯出XLS檔">
<input type="submit" name="ods" value="匯出ODS檔">
<input type="submit" name="ods_all" value="匯出全年級ODS檔">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr bgcolor="#c4d9ff">
<td align="center">年級</td>
<td align="center">班級</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">身高</td>
<td align="center">體重</td>
<td align="center">BMI</td>
<td align="center">體位判讀</td>
<td align="center">裸視右</td>
<td align="center">裸視左</td>
<td align="center">矯正右</td>
<td align="center">矯正左</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=j value=$j+1}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="text-align:center;">{{$dd.height}}</td>
<td style="text-align:center;">{{$dd.weight}}</td>
<td>{{$dd.BMI}}</td>
{{assign var=Bid value=$dd.Bid}}
<td {{if $Bid!=1}}style="color:red;"{{/if}}>{{$Bid_arr.$Bid}}</td>
<td style="text-align:center;color:{{if $dd.r.sight_o<=0.8}}red{{else}}blue{{/if}};">{{$dd.r.sight_o}}</td>
<td style="text-align:center;color:{{if $dd.l.sight_o<=0.8}}red{{else}}blue{{/if}};">{{$dd.l.sight_o}}</td>
<td style="text-align:center;color:{{if $dd.r.sight_r<=0.8}}red{{else}}blue{{/if}};">{{$dd.r.sight_r}}</td>
<td style="text-align:center;color:{{if $dd.l.sight_r<=0.8}}red{{else}}blue{{/if}};">{{$dd.l.sight_r}}</td>
</tr>
{{/foreach}}
{{/foreach}}
</table>
<input type="submit" name="xls" value="匯出XLS檔">
<input type="submit" name="ods" value="匯出ODS檔">
<input type="submit" name="ods_all" value="匯出全年級ODS檔">