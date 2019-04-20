{{* $Id: class_health_wh.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>座號</td>
<td>姓名</td>
<td>身高</td>
<td>體重</td>
<td>BMI</td>
<td>體位判讀</td>
</tr>
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="text-align:center;">{{$dd.height}}</td>
<td style="text-align:center;">{{$dd.weight}}</td>
<td>{{$dd.BMI}}</td>
{{assign var=Bid value=$dd.Bid}}
<td {{if $Bid!=1}}style="color:red;"{{/if}}>{{$Bid_arr.$Bid}}</td>
{{assign var=tb value=$dd.height-1}}
{{assign var=tb value=$tb/5|@ceil}}
</tr>
{{/foreach}}
{{/foreach}}
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
