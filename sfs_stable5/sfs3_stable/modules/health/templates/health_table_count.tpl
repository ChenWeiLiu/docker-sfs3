{{* $Id: health_table_count.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>年級</td>
<td>班級</td>
{{foreach from=$tb_arr item=dd}}
<td>{{$dd}}</td>
{{/foreach}}
<td>小計</td>
</tr>
{{foreach from=$data_arr item=d key=year}}
{{foreach from=$d item=dd key=class}}
{{if $year!="all" && $class!="all"}}
<tr style="background-color:{{cycle values="white,yellow"}};">
<td>{{$year}}</td>
<td>{{$class}}</td>
{{foreach from=$tb_arr item=ddd}}
<td>{{$dd.$ddd|@intval}}</td>
{{/foreach}}
<td>{{$dd.all|@intval}}</td>
</tr>
{{/if}}
{{/foreach}}
{{if $year!="all"}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td>{{$year}}</td>
<td>小計</td>
{{foreach from=$tb_arr item=ddd}}
<td>{{$d.all.$ddd|@intval}}</td>
{{/foreach}}
<td>{{$d.all.all|@intval}}</td>
</tr>
{{/if}}
{{/foreach}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="2">總計</td>
{{foreach from=$tb_arr item=ddd}}
<td>{{$data_arr.all.all.$ddd|@intval}}</td>
{{/foreach}}
<td>{{$data_arr.all.all.all|@intval}}</td>
</tr>
</table>
<span class="small" style="color:red;"><br>
若班級小計與實際班級人數不符，表示該班尚有學生未測量。
</span>
</td>
</tr></table>