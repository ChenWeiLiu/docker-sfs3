{{* $Id: health_analyze_wh_class3.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">年級</td>
<td rowspan="2">班級</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td>性別</td>
<td rowspan="2">身高</td>
<td rowspan="2">體重</td>
<td rowspan="2">BMI</td>
<td>體重狀況</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>
<select name="sex" OnChange="this.form.submit();">
<option value="" {{if $smarty.post.sex==""}}selected{{/if}}>全部</option>
<option value="1" {{if $smarty.post.sex=="1"}}selected{{/if}}>男生</option>
<option value="2" {{if $smarty.post.sex=="2"}}selected{{/if}}>女生</option>
</select>
</td>
<td>
<select name="Bid" OnChange="this.form.submit();">
<option value="3" {{if $smarty.post.Bid==3}}selected{{/if}}>超重</option>
<option value="2" {{if $smarty.post.Bid==2}}selected{{/if}}>過重</option>
<option value="1" {{if $smarty.post.Bid==1}}selected{{/if}}>適中</option>
<option value="0" {{if $smarty.post.Bid==0}}selected{{/if}}>過輕</option>
</select>
</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{assign var=sex value=$health_data->stud_base.$sn.stud_sex}}
{{if $smarty.post.Bid==$dd.Bid && ($smarty.post.sex=="" || $smarty.post.sex==$sex)}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#f4feff;text-align:center;">{{if $health_data->stud_base.$sn.stud_sex==1}}男{{elseif $health_data->stud_base.$sn.stud_sex==2}}女{{else}}--{{/if}}</td>
<td style="text-align:center;">{{$dd.height}}</td>
<td style="text-align:center;">{{$dd.weight}}</td>
<td>{{$dd.BMI}}</td>
{{assign var=Bid value=$dd.Bid}}
<td style="text-align:center;">{{$Bid_arr.$Bid}}</td>
</tr>
{{/if}}
{{/foreach}}
{{/foreach}}
</table>
</td></tr></table>