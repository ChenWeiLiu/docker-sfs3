{{* $Id: class_health_sight.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr bgcolor="#c4d9ff">
<td align="center" rowspan="2">座號</td>
<td align="center" rowspan="2">姓名</td>
<td align="center" colspan="2">裸視</td>
<td align="center" colspan="2">矯正</td>
<td align="center" colspan="5">右眼狀況</td>
<td align="center" colspan="5">左眼狀況</td>
</tr>
<tr bgcolor="#c4d9ff">
<td align="center">右眼</td>
<td align="center">左眼</td>
<td align="center">右眼</td>
<td align="center">左眼</td>
<td align="center">近視</td>
<td align="center">遠視</td>
<td align="center">弱視</td>
<td align="center">散光</td>
<td align="center">其他</td>
<td align="center">近視</td>
<td align="center">遠視</td>
<td align="center">弱視</td>
<td align="center">散光</td>
<td align="center">其他</td>
</tr>
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="text-align:center;color:{{if $dd.r.sight_o<0.9}}red{{else}}blue{{/if}};">{{$dd.r.sight_o}}</td>
<td style="text-align:center;color:{{if $dd.l.sight_o<0.9}}red{{else}}blue{{/if}};">{{$dd.l.sight_o}}</td>
<td style="text-align:center;color:{{if $dd.r.sight_r<0.5}}red{{else}}blue{{/if}};background-color:#f0f0ff;">{{$dd.r.sight_r}}</td>
<td style="text-align:center;color:{{if $dd.l.sight_r<0.5}}red{{else}}blue{{/if}};background-color:#f0f0ff;">{{$dd.l.sight_r}}</td>
<td align="center"><input type="checkbox" {{if $dd.r.My}}checked{{/if}} disabled></td>
<td align="center"><input type="checkbox" {{if $dd.r.Hy}}checked{{/if}} disabled></td>
<td align="center"><input type="checkbox" {{if $dd.r.Ast}}checked{{/if}} disabled></td>
<td align="center"><input type="checkbox" {{if $dd.r.Amb}}checked{{/if}} disabled></td>
<td align="center"><input type="checkbox" {{if $dd.r.other}}checked{{/if}} disabled></td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" {{if $dd.l.My}}checked{{/if}} disabled></td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" {{if $dd.l.Hy}}checked{{/if}} disabled></td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" {{if $dd.l.Ast}}checked{{/if}} disabled></td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" {{if $dd.l.Amb}}checked{{/if}} disabled></td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" {{if $dd.l.other}}checked{{/if}} disabled></td>
</tr>
{{/foreach}}
{{/foreach}}
</table>
</td></tr></table>
</td>
</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}