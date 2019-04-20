{{* $Id: health_analyze_inject_count3.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="3">年級</td>
<td rowspan="3">班級</td>
<td colspan="6">B型肝炎疫苗</td>
<td colspan="8">小兒麻痺疫苗</td>
<td colspan="6">破傷風減量白喉<br>混合疫苗</td>
<td colspan="4">MMR</td>
<td colspan="6">日本腦炎疫苗</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="2">第<br>一<br>劑</td>
<td colspan="2">第<br>二<br>劑</td>
<td colspan="2">第<br>三<br>劑</td>
<td colspan="2">第<br>一<br>劑</td>
<td colspan="2">第<br>二<br>劑</td>
<td colspan="2">第<br>三<br>劑</td>
<td colspan="2">第<br>四<br>劑</td>
<td colspan="2">第<br>一<br>劑</td>
<td colspan="2">第<br>二<br>劑</td>
<td colspan="2">第<br>三<br>劑</td>
<td colspan="2">第<br>一<br>劑</td>
<td colspan="2">第<br>二<br>劑</td>
<td colspan="2">第<br>一<br>劑</td>
<td colspan="2">第<br>二<br>劑</td>
<td colspan="2">第<br>三<br>劑</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
<td>應<br>補<br>種<br>數</td>
<td>補<br>種<br>數<br>　</td>
</tr>
{{foreach from=$data_arr item=d key=year}}
{{foreach from=$d item=dd key=class}}
{{if $year!="all" && $class!="all"}}
<tr style="background-color:{{cycle values="white,yellow"}};">
<td>{{$year}}</td>
<td>{{$class}}</td>
<td>{{$dd.nums|@intval}}</td>
<td>{{$dd.ptotal|@intval}}</td>
<td>{{$dd.ptotal/$dd.nums*100|@round:2}}%</td>
<td>{{$dd.1.1|@intval}}</td>
<td>{{$dd.1.2|@intval}}</td>
<td>{{$dd.1.3|@intval}}</td>
<td>{{$dd.1.ttotal|@intval}}</td>
<td>{{$dd.1.ttotal/$dd.nums|@round:2}}</td>
<td>{{$dd.2.1|@intval}}</td>
<td>{{$dd.2.2|@intval}}</td>
<td>{{$dd.2.3|@intval}}</td>
<td>{{$dd.2.ttotal|@intval}}</td>
<td>{{$dd.2.ttotal/$dd.nums|@round:2}}</td>
<td>{{$dd.3|@intval}}</td>
<td>{{$dd.3/$dd.nums*100|@round:2}}%</td>
<td>{{$dd.4|@intval}}</td>
<td>{{$dd.4/$dd.nums*100|@round:2}}%</td>
<td>{{$dd.5|@intval}}</td>
<td>{{$dd.5/$dd.nums*100|@round:2}}%</td>
</tr>
{{/if}}
{{/foreach}}
{{if $year!="all"}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td>{{$year}}</td>
<td>小計</td>
<td>{{$d.all.nums|@intval}}</td>
<td>{{$d.all.ptotal|@intval}}</td>
<td>{{$d.all.ptotal/$d.all.nums*100|@round:2}}%</td>
<td>{{$d.all.1.1|@intval}}</td>
<td>{{$d.all.1.2|@intval}}</td>
<td>{{$d.all.1.3|@intval}}</td>
<td>{{$d.all.1.ttotal|@intval}}</td>
<td>{{$d.all.1.ttotal/$d.all.nums|@round:2}}</td>
<td>{{$d.all.2.1|@intval}}</td>
<td>{{$d.all.2.2|@intval}}</td>
<td>{{$d.all.2.3|@intval}}</td>
<td>{{$d.all.2.ttotal|@intval}}</td>
<td>{{$d.all.2.ttotal/$d.all.nums|@round:2}}</td>
<td>{{$d.all.3|@intval}}</td>
<td>{{$d.all.3/$d.all.nums*100|@round:2}}%</td>
<td>{{$d.all.4|@intval}}</td>
<td>{{$d.all.4/$d.all.nums*100|@round:2}}%</td>
<td>{{$d.all.5|@intval}}</td>
<td>{{$d.all.5/$d.all.nums*100|@round:2}}%</td>
</tr>
{{/if}}
{{/foreach}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="2">總計</td>
<td>{{$data_arr.all.all.nums|@intval}}</td>
<td>{{$data_arr.all.all.ptotal|@intval}}</td>
<td>{{$data_arr.all.all.ptotal/$data_arr.all.all.nums*100|@round:2}}%</td>
<td>{{$data_arr.all.all.1.1|@intval}}</td>
<td>{{$data_arr.all.all.1.2|@intval}}</td>
<td>{{$data_arr.all.all.1.3|@intval}}</td>
<td>{{$data_arr.all.all.1.ttotal|@intval}}</td>
<td>{{$data_arr.all.all.1.ttotal/$data_arr.all.all.nums|@round:2}}</td>
<td>{{$data_arr.all.all.2.1|@intval}}</td>
<td>{{$data_arr.all.all.2.2|@intval}}</td>
<td>{{$data_arr.all.all.2.3|@intval}}</td>
<td>{{$data_arr.all.all.2.ttotal|@intval}}</td>
<td>{{$data_arr.all.all.2.ttotal/$data_arr.all.all.nums|@round:2}}</td>
<td>{{$data_arr.all.all.3|@intval}}</td>
<td>{{$data_arr.all.all.3/$data_arr.all.all.nums*100|@round:2}}%</td>
<td>{{$data_arr.all.all.4|@intval}}</td>
<td>{{$data_arr.all.all.4/$data_arr.all.all.nums*100|@round:2}}%</td>
<td>{{$data_arr.all.all.5|@intval}}</td>
<td>{{$data_arr.all.all.5/$data_arr.all.all.nums*100|@round:2}}%</td>
</tr>
</table>
</td></tr></table>