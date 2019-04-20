{{* $Id: health_analyze_accident_place.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0">
<tr>
<td style="text-align:center;line-height:30pt;">學生意外事故傷害受傷地點與受傷部位之比較分析</td>
</tr>
<tr>
<td>統計日期：<span style="color:blue">{{if $smarty.post.start_date}}{{$smarty.post.start_date}}{{else}}未指定{{/if}}～{{if $smarty.post.end_date}}{{$smarty.post.end_date}}{{else}}未指定{{/if}}</span></td>
</tr>
<tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>部位\地點</td>
{{foreach from=$aplace item=d}}
<td>{{$d}}</td>
{{/foreach}}
<td>合計</td>
<td>比率</td>
</tr>
{{foreach from=$apart item=dd key=ii}}
<tr style="background-color:{{cycle values="white,#e6f2f2"}};text-align:center;">
<td>{{$dd}}</td>
{{foreach from=$aplace item=d key=i}}
<td>{{if $rowdata.$i.$ii}}{{$rowdata.$i.$ii}}{{else}}-{{/if}}</td>
{{/foreach}}
<td>{{if $rowdata.all.$ii}}{{$rowdata.all.$ii}}{{else}}-{{/if}}</td>
<td style="text-align:right;">{{$rowdata.all.$ii/$rowdata.all.all*100|@round:2}}％&nbsp;</td>
</tr>
{{/foreach}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td>合計</td>
{{foreach from=$aplace item=d key=i}}
<td>{{if $rowdata.$i.all}}{{$rowdata.$i.all}}{{else}}-{{/if}}</td>
{{/foreach}}
<td>{{if $rowdata.all.all}}{{$rowdata.all.all}}{{else}}-{{/if}}</td>
<td style="text-align:right;">{{if $rowdata.all.all}}100％&nbsp;{{else}}-{{/if}}</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>比率</td>
{{foreach from=$aplace item=d key=i}}
<td>{{$rowdata.$i.all/$rowdata.all.all*100|@round:2}}％&nbsp;</td>
{{/foreach}}
<td>{{if $rowdata.all.all}}100％&nbsp;{{else}}-{{/if}}</td>
<td>-</td>
</tr>
</table>
</td></tr>
<tr>
<td style="text-align:center;line-height:30pt;"><br>學生意外事故傷害受傷地點與受傷性質之比較分析</td>
</tr>
<tr>
<td>統計日期：<span style="color:blue;">{{if $smarty.post.start_date}}{{$smarty.post.start_date}}{{else}}未指定{{/if}}～{{if $smarty.post.end_date}}{{$smarty.post.end_date}}{{else}}未指定{{/if}}</span></td>
</tr>
<tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>性質\地點</td>
{{foreach from=$aplace item=d}}
<td>{{$d}}</td>
{{/foreach}}
<td>合計</td>
<td>比率</td>
</tr>
{{foreach from=$astatus item=dd key=ii}}
{{if $ii<=10}}
<tr style="background-color:{{cycle values="white,#e6f2f2"}};text-align:center;">
<td>{{$dd}}</td>
{{foreach from=$aplace item=d key=i}}
<td>{{if $rowdata2.$i.$ii}}{{$rowdata2.$i.$ii}}{{else}}-{{/if}}</td>
{{/foreach}}
<td>{{if $rowdata2.all.$ii}}{{$rowdata2.all.$ii}}{{else}}-{{/if}}</td>
<td style="text-align:right;">{{$rowdata2.all.$ii/$rowdata2.all.all*100|@round:2}}％&nbsp;</td>
</tr>
{{/if}}
{{/foreach}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td>合計</td>
{{foreach from=$aplace item=d key=i}}
<td>{{if $rowdata2.$i.all}}{{$rowdata2.$i.all}}{{else}}-{{/if}}</td>
{{/foreach}}
<td>{{if $rowdata2.all.all}}{{$rowdata2.all.all}}{{else}}-{{/if}}</td>
<td style="text-align:right;">{{if $rowdata.all.all}}100％&nbsp;{{else}}-{{/if}}</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>比率</td>
{{foreach from=$aplace item=d key=i}}
<td>{{$rowdata2.$i.all/$rowdata2.all.all*100|@round:2}}％&nbsp;</td>
{{/foreach}}
<td>{{if $rowdata2.all.all}}100％&nbsp;{{else}}-{{/if}}</td>
<td>-</td>
</tr>
</table>
</td></tr>
</table>