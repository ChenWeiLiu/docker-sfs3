{{* $Id: health_teesem_cclass.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="chart" value="列印本班記錄表">
<input type="submit" name="allchart" value="列印全校記錄表">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>座號</td>
<td>姓名</td>
{{foreach from=$date_arr item=d key=i}}
<td style="width:14;">{{$d}}</td>
{{/foreach}}
<td>合計</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
<tr style="background-color:white;text-align:center;">
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
{{foreach from=$date_arr item=d key=i}}
<td></td>
{{/foreach}}
<td></td>
{{/foreach}}
</tr>
<tr style="background-color:white;text-align:center;">
<td colspan="2" style="background-color:#c4d9ff;">合計</td>
{{foreach from=$date_arr item=d key=i}}
<td></td>
{{/foreach}}
<td></td>
</tr>
</table>
