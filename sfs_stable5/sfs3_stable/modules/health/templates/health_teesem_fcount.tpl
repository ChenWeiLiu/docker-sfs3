{{* $Id: health_teesem_fcount.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>痁</td>
<td>龟悔计</td>
<td>把籔计</td>
<td>ぃ把籔计</td>
<td>ゼ砞﹚计</td>
</tr>
{{foreach from=$rowdata item=d key=i}}
<tr style="background-color:white;text-align:center;">
<td>{{$i}}</td>
<td>{{$d.num|@intval}}</td>
<td>{{$d.y|@intval}}</td>
<td>{{$d.n|@intval}}</td>
<td>{{$d.u|@intval}}</td>
{{/foreach|@intval}}
</tr>
</table>
