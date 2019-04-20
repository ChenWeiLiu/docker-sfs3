{{* $Id: health_other_dis_input.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<input type="submit" name="save" value="確定儲存">
<input type="reset" value="放棄修改">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>疾病</td>
<td>陳述</td>
<td>照護</td>
</tr>
{{foreach from=$health_data->stud_base item=d key=sn}}
{{if $d.disease}}
{{foreach from=$d.disease item=dd}}
{{assign var=year_name value=$d.seme_class|@substr:0:-2}}
{{assign var=class_name value=$d.seme_class|@substr:-2:2}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$d.seme_num}}</td>
<td style="color:{{if $d.stud_sex==1}}blue{{elseif $d.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$d.stud_name}}</td>
<td>{{$disease_kind_arr.$dd}}</td>
<td><textarea name="update[{{$sn}}][health_status_record][health_disease][{{$dd}}]" rows="3" cols="20">{{$health_data->stud_base.$sn.status_record.disease.$dd|br2nl}}</textarea></td>
<td><textarea name="update[{{$sn}}][health_diag_record][health_disease][{{$dd}}]" rows="3" cols="20">{{$health_data->stud_base.$sn.diag_record.disease.$dd|br2nl}}</textarea></td>
</tr>
{{/foreach}}
{{/if}}
{{/foreach}}
</table>
<input type="submit" name="save" value="確定儲存">
<input type="reset" value="放棄修改">
