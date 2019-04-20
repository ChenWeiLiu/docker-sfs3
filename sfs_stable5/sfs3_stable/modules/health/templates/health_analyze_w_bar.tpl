{{* $Id: health_analyze_w_bar.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="radio" name="graph_kind" value="bar" {{if $smarty.post.graph_kind=="" || $smarty.post.graph_kind=="bar"}}checked {{/if}}OnClick="this.form.submit();">JPG
<input type="radio" name="graph_kind" value="flashbar" {{if $smarty.post.graph_kind=="flashbar"}}checked {{/if}}OnClick="this.form.submit();">Flash
{{include file="health_graph.tpl"}}
<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small" width="640">
<tr style="text-align:center;background-color:white;">
<td width="16%" style="background-color:#c4d9ff;">男生合計</td>
<td width="16%">{{$data_arr.1.nums}} 人</td>
<td width="16%" style="background-color:#c4d9ff;">男生平均</td>
<td width="16%">{{$data_arr.1.avg|string_format:"%.1f"}} 公斤</td>
<td width="17%" style="background-color:#c4d9ff;">男生標準差</td>
<td>{{$data_arr.1.std|string_format:"%.1f"}} 公斤</td>
</tr>
<tr style="text-align:center;background-color:white;">
<td style="background-color:#c4d9ff;">女生合計</td>
<td>{{$data_arr.2.nums}} 人</td>
<td style="background-color:#c4d9ff;">女生平均</td>
<td>{{$data_arr.2.avg|string_format:"%.1f"}} 公斤</td>
<td style="background-color:#c4d9ff;">女生標準差</td>
<td>{{$data_arr.2.std|string_format:"%.1f"}} 公斤</td>
</tr>
<tr style="text-align:center;background-color:white;">
<td style="background-color:#c4d9ff;">總合計</td>
<td>{{$data_arr.3.nums}} 人</td>
<td style="background-color:#c4d9ff;">總平均</td>
<td>{{$data_arr.3.avg|string_format:"%.1f"}} 公斤</td>
<td style="background-color:#c4d9ff;">總標準差</td>
<td>{{$data_arr.3.std|string_format:"%.1f"}} 公斤</td>
</tr>
</table>
</td></tr></table>