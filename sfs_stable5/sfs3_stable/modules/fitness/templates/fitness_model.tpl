{{* $Id: fitness_model.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="4">
<form action="{{$smarty.server.PHP_SELF}}" method="post">
<tr>
<td bgcolor="#FFFFFF" valign="top">
{{$model_menu}}<br><br>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
{{foreach from=$rowdata item=v key=sex}}
{{assign var=id value=$smarty.post.model_id}}
<tr style="text-align:center;color:white;{{if $sex==2}}background-color:#ff79bc;{{/if}}"><td colspan="20">7-23歲中小學{{if $sex==1}}男{{else}}女{{/if}}學生{{$model_arr.$id}}百分等級常模(單位:{{$k_arr.$id}})</td></tr>
<tr bgcolor="#c4d9ff">
<td align="center">百分<br>等級</td>
{{foreach from=$p_arr item=c key=p}}
<td align="center">{{$p}}%</td>
{{/foreach}}
{{if $smarty.post.model_id>1}}
<tr bgcolor="white">
<td>年齡</td>
<td colspan="4" style="text-align:center;color:red;">&lt;&lt;請加強&gt;&gt;</td>
<td colspan="5" style="text-align:center;color:blue;">&lt;&lt;中等&gt;&gt;</td>
<td colspan="5" style="text-align:center;"><img src="images/award_3rd.gif" alt="銅牌"></td>
<td colspan="2" style="text-align:center;"><img src="images/award_silver.gif" alt="銀牌"></td>
<td colspan="3" style="text-align:center;"><img src="images/award_gold.gif" alt="金牌"></td>
</tr>
{{/if}}
</tr>
{{foreach from=$v item=d key=i}}
<tr bgcolor="white">
<td>{{$d.age}}</td>
{{foreach from=$p_arr item=c key=p}}
{{assign var=pp value=p$p}}
<td bgcolor="{{$c}}">{{$d.$pp}}</td>
{{/foreach}}
</tr>
{{/foreach}}
{{/foreach}}
</table>
<br>
<div class="small">本資料來自<a href="http://www.fitness.org.tw">教育部體適能網站</a>，若有錯誤，請告知<a href="http://sfshelp.tcc.edu.tw"開發人員</a>修正。</div>
</td></tr></form></table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
