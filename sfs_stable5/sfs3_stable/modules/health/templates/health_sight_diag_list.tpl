{{* $Id: health_sight_diag_list.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2" nowrap></td>
<td colspan="2">浪羆计</td>
<td colspan="2">跌ぃ▆<br>计</td>
<td colspan="2">跌计</td>
<td colspan="2">环跌计</td>
<td colspan="2">床计</td>
<td colspan="2">跌床<br>计</td>
<td colspan="2">环跌床<br>计</td>
<td colspan="2">畓跌计</td>
<td colspan="2">弊跌计</td>
<td colspan="2">ㄤ禘耞<br>计</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
<td>╧</td>
<td></td>
</tr>
{{foreach from=$data_arr item=d key=i}}
{{if $i!="all"}}
<tr style="background-color:white;text-align:center;">
<td>{{$i}}</td>
<td>{{$d.all.1}}</td>
<td>{{$d.all.2}}</td>
<td>{{$d.dis.1}}</td>
<td>{{$d.dis.2}}</td>
<td>{{$d.My.1|intval}}</td>
<td>{{$d.My.2|intval}}</td>
<td>{{$d.Hy.1|intval}}</td>
<td>{{$d.Hy.2|intval}}</td>
<td>{{$d.Ast.1|intval}}</td>
<td>{{$d.Ast.2|intval}}</td>
<td>{{$d.MA.1|intval}}</td>
<td>{{$d.MA.2|intval}}</td>
<td>{{$d.HA.1|intval}}</td>
<td>{{$d.HA.2|intval}}</td>
<td>{{$d.Ast.1|intval}}</td>
<td>{{$d.Ast.2|intval}}</td>
<td>{{$d.Oph3.1|intval}}</td>
<td>{{$d.Oph3.2|intval}}</td>
<td>{{$d.other.1|intval}}</td>
<td>{{$d.other.2|intval}}</td>
</tr>
{{/if}}
{{/foreach}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>璸</td>
<td>{{$data_arr.all.all.1}}</td>
<td>{{$data_arr.all.all.2}}</td>
<td>{{$data_arr.all.dis.1}}</td>
<td>{{$data_arr.all.dis.2}}</td>
<td>{{$data_arr.all.My.1|intval}}</td>
<td>{{$data_arr.all.My.2|intval}}</td>
<td>{{$data_arr.all.Hy.1|intval}}</td>
<td>{{$data_arr.all.Hy.2|intval}}</td>
<td>{{$data_arr.all.Ast.1|intval}}</td>
<td>{{$data_arr.all.Ast.2|intval}}</td>
<td>{{$data_arr.all.MA.1|intval}}</td>
<td>{{$data_arr.all.MA.2|intval}}</td>
<td>{{$data_arr.all.HA.1|intval}}</td>
<td>{{$data_arr.all.HA.2|intval}}</td>
<td>{{$data_arr.all.Ast.1|intval}}</td>
<td>{{$data_arr.all.Ast.2|intval}}</td>
<td>{{$data_arr.all.Oph3.1|intval}}</td>
<td>{{$data_arr.all.Oph3.2|intval}}</td>
<td>{{$data_arr.all.other.1|intval}}</td>
<td>{{$data_arr.all.other.2|intval}}</td>
</tr>
</table>
</td></tr></table>
