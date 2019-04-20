{{* $Id: health_other_count.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>疾病種類/年級</td>
{{foreach from=$class_year item=d key=i}}
{{if $i|@intval}}
<td>{{$i}}</td>
{{/if}}
{{/foreach}}
<td>總計</td>
</tr>
{{foreach from=$dis_arr item=dd}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$disease_kind_arr[$dd.di_id]}}</td>
{{assign var=di_id value=$dd.di_id}}
{{foreach from=$class_year item=d key=i}}
{{if $i|@intval}}
<td>{{$rowdata.$i.$di_id}}</td>
{{/if}}
{{/foreach}}
<td>{{$rowdata.all.$di_id}}</td>
</tr>
{{/foreach}}
</table>
</td></tr></table>
