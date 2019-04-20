{{* $Id: health_wh_body_count.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">年級</td>
<td rowspan="2">性別</td>
<td colspan="5">體位判讀</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>過輕</td>
<td>適中</td>
<td>過重</td>
<td>超重</td>
<td>合計</td>
</tr>
{{foreach from=$data_arr item=d key=i}}
{{foreach from=$sex_arr item=dd key=j}}
{{if $i!="all"}}
<tr style="background-color:{{if $j=="all"}}#c4d9ff{{else}}white{{/if}};">
<td>{{$i}}</td>
<td>{{$dd}}</td>
<td>{{$d.$j.0}}</td>
<td>{{$d.$j.1}}</td>
<td>{{$d.$j.2}}</td>
<td>{{$d.$j.3}}</td>
<td>{{$d.$j.all}}</td>
</tr>
{{/if}}
{{/foreach}}
{{/foreach}}
<tr style="background-color:#c4d9ff;">
<td colspan="2">總計</td>
<td>{{$data_arr.all.all.0}}</td>
<td>{{$data_arr.all.all.1}}</td>
<td>{{$data_arr.all.all.2}}</td>
<td>{{$data_arr.all.all.3}}</td>
<td>{{$data_arr.all.all.all}}</td>
</tr>
</table>
</td></tr></table>
<input type="submit" name="print" value="列印">
