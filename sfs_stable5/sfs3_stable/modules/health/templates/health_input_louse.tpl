{{* $Id: health_input_louse.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="save" value="確定儲存">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr bgcolor="#c4d9ff">
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">檢查結果</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">檢查結果</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">檢查結果</td>
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">檢查結果</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_data.$sn.$year_seme}}
{{if $smarty.foreach.rows.iteration % 4==1}}
<tr style="background-color:white;">
{{/if}}
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td align="center"><input type="checkbox"></td>
{{if $smarty.foreach.rows.iteration % 4==0}}
</tr>
{{/if}}
{{/foreach}}
</table>
<input type="submit" name="save" value="確定儲存">