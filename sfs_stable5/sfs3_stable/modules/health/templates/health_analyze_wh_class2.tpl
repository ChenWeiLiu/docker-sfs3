{{* $Id: health_analyze_wh_class2.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<fieldset class="small" style="width:30%;">
<legend style="color:blue;font-size:12pt;">身高範圍</legend>
<input type="text" name="minh" value="{{$smarty.post.minh}}" style="width:40px;"> ～ <input type="text" name="maxh" value="{{$smarty.post.maxh}}" style="width:40px;">公分 (空白代表不限定)
</fieldset>
<fieldset class="small" style="width:30%;">
<legend style="color:blue;font-size:12pt;">體重範圍</legend>
<input type="text" name="minw" value="{{$smarty.post.minw}}" style="width:40px;"> ～ <input type="text" name="maxw" value="{{$smarty.post.maxw}}" style="width:40px;">公斤 (空白代表不限定)
</fieldset>
<br>

<input type="submit" value="開始查詢">
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
<td rowspan="2">體重狀況</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>
<select name="sex" OnChange="this.form.submit();">
<option value="" {{if $smarty.post.sex==""}}selected{{/if}}>全部</option>
<option value="1" {{if $smarty.post.sex=="1"}}selected{{/if}}>男生</option>
<option value="2" {{if $smarty.post.sex=="2"}}selected{{/if}}>女生</option>
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
{{if ($smarty.post.minh=="" || $smarty.post.minh<=$dd.height) && ($smarty.post.maxh=="" || $smarty.post.maxh>=$dd.height) && ($smarty.post.minw=="" || $smarty.post.minw<=$dd.weight) && ($smarty.post.maxw=="" || $smarty.post.maxw>=$dd.weight) && ($smarty.post.sex=="" || $smarty.post.sex==$sex)}}
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