{{* $Id: health_analyze_teesem.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2"></td>
<td rowspan="2">痁</td>
<td rowspan="2">厩ネ<br>计</td>
<td rowspan="2">浪<br>计</td>
<td colspan="2">芓睛<br>计</td>
<td colspan="2">芓睛<br>聋计</td>
<td rowspan="2">芓睛<br>计</td>
<td rowspan="2">芓睛<br>脖︽瞯</td>
<td colspan="2"><br>计</td>
<td colspan="2"><br>聋计</td>
<td colspan="2">恶干<br>计</td>
<td colspan="2">恶干<br>聋计</td>
<td rowspan="2">恶干瞯</td>
<td colspan="2">的矫<br>ネぃ▆</td>
<td colspan="2" nowrap>舏<br>(㏄痜)</td>
<td colspan="2">睛<br>ぃタ</td>
<td colspan="2">礚翴</td>
<td rowspan="2">ゼ浪<br>计</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>计</td>
<td></td>
<td>聋计</td>
<td>–<br>キА</td>
<td>计</td>
<td></td>
<td>聋计</td>
<td>–<br>キА</td>
<td>计</td>
<td></td>
<td>聋计</td>
<td>–<br>キА</td>
<td>计</td>
<td></td>
<td>计</td>
<td></td>
<td>计</td>
<td></td>
<td>计</td>
<td></td>
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
<td>璸</td>
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
<td colspan="2">羆璸</td>
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
<span class="small" style="color:blue;"><br>
参璸腹D(d) 芓睛聋计M 聋计F(f) 罛獀聋计e ┺聋计<br><br>
T(t) 芓睛羆聋计=D+M+F(d+e+f)DMF(def)芓睛计=T(t)/羆计<br><br>
</span>
</td></tr></table>