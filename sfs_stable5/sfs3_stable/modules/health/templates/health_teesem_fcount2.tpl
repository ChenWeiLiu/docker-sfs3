{{* $Id: health_teesem_fcount2.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>班級</td>
<td>學生數</td>
<td>不參與數</td>
<td>參與率</td>
<td>未實施人次</td>
<td>應實施人次</td>
<td>實施人次</td>
<td>執行率</td>
</tr>
{{foreach from=$rowdata item=d key=i}}
<tr style="background-color:white;text-align:center;">
<td>{{$i}}</td>
<td>{{$d.num|@intval}}</td>
<td>{{$d.n|@intval}}</td>
<td>{{$d.y/$d.num*100|@round:2}}%</td>
{{assign var=t value=$d.y*$maxd|intval}}
{{assign var=y value=$d.d|intval}}
{{assign var=n value=$t-$y}}
<td>{{$n}}</td>
<td>{{$t}}</td>
<td>{{$y}}</td>
<td>{{$y/$t*100|round:2}}%</td>
{{/foreach|@intval}}
</tr>
</table>
