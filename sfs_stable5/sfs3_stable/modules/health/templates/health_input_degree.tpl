{{* $Id: health_input_degree.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="save" value="確定儲存">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr bgcolor="#c4d9ff">
<td align="center" rowspan="2">座號</td>
<td align="center" rowspan="2">姓名</td>
<td align="center" colspan="3">右眼</td>
<td align="center" colspan="3">左眼</td>
<td align="center" rowspan="2">瞳孔<br>距離</td>
<td align="center" colspan="3">右眼鏡片</td>
<td align="center" colspan="3">左眼鏡片</td>
</tr>
<tr bgcolor="#c4d9ff">
<td align="center">度數</td>
<td align="center">散光</td>
<td align="center">角度</td>
<td align="center">度數</td>
<td align="center">散光</td>
<td align="center">角度</td>
<td align="center">度數</td>
<td align="center">散光</td>
<td align="center">角度</td>
<td align="center">度數</td>
<td align="center">散光</td>
<td align="center">角度</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_data.$sn.$year_seme}}
{{* if $smarty.foreach.rows.iteration % 4==1 *}}
<tr style="background-color:white;">
{{* /if *}}
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td align="center"><input type="text" value="{{$dd.height}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center"><input type="text" value="{{$dd.weight}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center"><input type="text" value="{{$dd.height}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center" style="background-color:#d0d0ff;"><input type="text" value="{{$dd.weight}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center" style="background-color:#d0d0ff;"><input type="text" value="{{$dd.height}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center" style="background-color:#d0d0ff;"><input type="text" value="{{$dd.weight}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center" style="background-color:#ffd0d0;"><input type="text" value="{{$dd.height}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center"><input type="text" value="{{$dd.weight}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center"><input type="text" value="{{$dd.height}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center"><input type="text" value="{{$dd.weight}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center" style="background-color:#d0d0ff;"><input type="text" value="{{$dd.height}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center" style="background-color:#d0d0ff;"><input type="text" value="{{$dd.weight}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
<td align="center" style="background-color:#d0d0ff;"><input type="text" value="{{$dd.weight}}" size="2" style="background-color:#f8f8f8;font-size:12px;"></td>
{{* if $smarty.foreach.rows.iteration % 4==0 *}}
</tr>
{{* /if *}}
{{/foreach}}
</table>
</td></tr></table>
<input type="submit" name="save" value="確定儲存">