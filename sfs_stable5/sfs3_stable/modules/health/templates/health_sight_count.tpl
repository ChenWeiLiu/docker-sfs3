{{* $Id: health_sight_count.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="4" nowrap>年級</td>
<td colspan="14">裸視視力檢查人數</td>
<td colspan="9">矯正視力檢查人數</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="3" rowspan="2" nowrap>合計</td>
<td colspan="2" rowspan="2">兩眼<br>均達<br>0.9</td>
<td colspan="9">裸視視力不良人數</td>
<td colspan="3" rowspan="2" nowrap>合計</td>
<td colspan="2" rowspan="2">兩眼<br>均達<br>0.5</td>
<td colspan="4">矯正視力不良人數</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="2">0.5~0.8</td>
<td colspan="2">0.1~0.4</td>
<td colspan="2">未達0.1</td>
<td colspan="3">合計</td>
<td colspan="2">0.1~0.4</td>
<td colspan="2">未達0.1</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>合計</td>
<td>男</td>
<td>女</td>
<td>男</td>
<td>女</td>
<td>男</td>
<td>女</td>
<td>男</td>
<td>女</td>
<td>男</td>
<td>女</td>
<td nowrap>合計</td>
<td>男</td>
<td>女</td>
<td nowrap>合計</td>
<td>男</td>
<td>女</td>
<td>男</td>
<td>女</td>
<td>男</td>
<td>女</td>
<td>男</td>
<td>女</td>
</tr>
{{foreach from=$data_arr item=d key=i}}
{{if $i!="all"}}
<tr style="background-color:white;text-align:center;">
<td>{{$i}}</td>
<td>{{$d.0.all}}</td>
<td>{{$d.0.1.all}}</td>
<td>{{$d.0.2.all}}</td>
<td>{{$d.0.1.0}}</td>
<td>{{$d.0.2.0}}</td>
<td>{{$d.0.1.1}}</td>
<td>{{$d.0.2.1}}</td>
<td>{{$d.0.1.2}}</td>
<td>{{$d.0.2.2}}</td>
<td>{{$d.0.1.3|@intval}}</td>
<td>{{$d.0.2.3|@intval}}</td>
<td>{{$d.0.dis}}</td>
<td>{{$d.0.1.dis}}</td>
<td>{{$d.0.2.dis}}</td>
<td>{{$d.1.all}}</td>
<td>{{$d.1.1.all}}</td>
<td>{{$d.1.2.all}}</td>
<td>{{$d.1.1.0}}</td>
<td>{{$d.1.2.0}}</td>
<td>{{$d.1.1.1}}</td>
<td>{{$d.1.2.1}}</td>
<td>{{$d.1.1.2|@intval}}</td>
<td>{{$d.1.2.2|@intval}}</td>
</tr>
{{/if}}
{{/foreach}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>合計</td>
<td>{{$data_arr.all.0.all|@intval}}</td>
<td>{{$data_arr.all.0.1.all|@intval}}</td>
<td>{{$data_arr.all.0.2.all|@intval}}</td>
<td>{{$data_arr.all.0.1.0|@intval}}</td>
<td>{{$data_arr.all.0.2.0|@intval}}</td>
<td>{{$data_arr.all.0.1.1|@intval}}</td>
<td>{{$data_arr.all.0.2.1|@intval}}</td>
<td>{{$data_arr.all.0.1.2|@intval}}</td>
<td>{{$data_arr.all.0.2.2|@intval}}</td>
<td>{{$data_arr.all.0.1.3|@intval}}</td>
<td>{{$data_arr.all.0.2.3|@intval}}</td>
<td>{{$data_arr.all.0.dis|@intval}}</td>
<td>{{$data_arr.all.0.1.dis|@intval}}</td>
<td>{{$data_arr.all.0.2.dis|@intval}}</td>
<td>{{$data_arr.all.1.all|@intval}}</td>
<td>{{$data_arr.all.1.1.all|@intval}}</td>
<td>{{$data_arr.all.1.2.all|@intval}}</td>
<td>{{$data_arr.all.1.1.0|@intval}}</td>
<td>{{$data_arr.all.1.2.0|@intval}}</td>
<td>{{$data_arr.all.1.1.1|@intval}}</td>
<td>{{$data_arr.all.1.2.1|@intval}}</td>
<td>{{$data_arr.all.1.1.2|@intval}}</td>
<td>{{$data_arr.all.1.2.2|@intval}}</td>
</tr>
</table>
</td></tr></table>
