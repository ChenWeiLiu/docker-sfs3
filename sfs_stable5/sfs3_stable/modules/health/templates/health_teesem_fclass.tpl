{{* $Id: health_teesem_fclass.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="chart" value="列印本班實施表">
<input type="submit" name="allchart" value="列印全校實施表">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="3">週別</td>
{{foreach from=$date_arr item=d key=i}}
<td>{{$d.week_no}}</td>
{{/foreach}}
<td colspan="3">合計</td>
<td rowspan="2">每位<br>學童<br>執行率</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>座號</td>
<td>姓名</td>
<td>參與狀況</td>
{{foreach from=$date_arr item=d key=i}}
<td>{{$d.do_date|@substr:5:2}}<br>／<br>{{$d.do_date|@substr:8:2}}</td>
{{/foreach}}
<td>缺<br><br>席</td>
<td>未<br>漱<br>口</td>
<td>有<br>漱<br>口</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
<tr style="background-color:white;text-align:center;">
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
{{assign var=agree value=$health_data->health_data.$sn.$year_seme.frecord.agree}}
<td>{{if $agree=="0"}}不參與{{elseif $agree==1}}參與{{else}}未設定{{/if}}</td>
{{foreach from=$date_arr item=d key=i}}
<td></td>
{{/foreach}}
<td></td>
<td></td>
<td></td>
<td></td>
{{/foreach}}
</tr>
</table>
