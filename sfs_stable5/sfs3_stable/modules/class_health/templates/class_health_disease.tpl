{{* $Id: class_health_disease.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>座號</td>
<td>姓名</td>
<td>疾病</td>
<td>陳述</td>
<td>照護</td>
</tr>
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_base.$sn}}
{{if $dd.disease}}
{{foreach from=$dd.disease item=ddd}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $dd.stud_sex==1}}blue{{elseif $dd.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$dd.stud_name}}</td>
<td>{{$disease_kind_arr.$ddd}}</td>
<td>{{$dd.status_record.disease.$ddd}}</td>
<td>{{$dd.diag_record.disease.$ddd}}</td>
</tr>
{{/foreach}}
{{/if}}
{{/foreach}}
{{/foreach}}
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
