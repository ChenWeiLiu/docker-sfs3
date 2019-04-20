{{* $Id: health_teesem_count.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="3">年級</td>
<td rowspan="3">班級</td>
<td rowspan="3">受檢<br>人數</td>
<td colspan="2" rowspan="2">齲齒盛行率</td>
<td colspan="10">齲齒檢查</td>
<td colspan="2" rowspan="2">口腔衛<br>生不良</td>
<td colspan="2" rowspan="2">牙齦炎<br>(牙周病)</td>
<td colspan="2" rowspan="2">齒列不正</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="5">恆齒(顆數)</td>
<td colspan="5">乳牙(顆數)</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>人數</td>
<td>％</td>
<td>D</td>
<td>M</td>
<td>F</td>
<td>T</td>
<td>DMF</td>
<td>d</td>
<td>e</td>
<td>f</td>
<td>t</td>
<td>def</td>
<td>人數</td>
<td>％</td>
<td>人數</td>
<td>％</td>
<td>人數</td>
<td>％</td>
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
<span class="small" style="color:blue;"><br>
統計代號：　D(d) 齲齒顆數　M 缺牙顆數　F(f) 已矯治顆數　e 待拔牙顆數<br><br>
　　　　　　T(t) 齲齒總顆數=D+M+F(d+e+f)　DMF(def)齲齒指數=T(t)/總人數<br><br>
</span>
</td></tr></table>