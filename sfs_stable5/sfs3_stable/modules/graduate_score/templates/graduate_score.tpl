{{* $Id: graduate_score.tpl 8029 2014-05-13 03:24:05Z infodaes $ *}}
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table width="100%">


<tr><td>{{$rank_list}}

{{if $show_detail=="on"}}<br>

<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">

<tr bgcolor="#E1ECFF" align="center">
<td>座號</td>
<td>學號</td>
<td>姓名</td>
<td>學習領域</td>
{{foreach from=$show_year item=i key=j}}
<td>{{$i}}{{if $jos!=0}}學年度<br>第{{/if}}{{if $jos!=0}}{{$show_seme[$j]}}學期{{else}}{{if $show_seme[$j]==1}}上{{else}}下{{/if}}<BR>(*{{$seme_weight[$j]}}){{/if}}</td>
{{/foreach}}
<td>學期<br>加權總分</td>
<td>領域總分</td>
</tr>
{{foreach from=$student_sn item=sn key=site_num name=ss}}
{{foreach from=$ss_link item=sl name=ss_link}}
<tr bgcolor="#ddddff" align="center">
{{if $smarty.foreach.ss_link.iteration == 1}}
<td rowspan="{{$ss_num}}">{{$curr_class_num[$site_num]}}</td>
<td rowspan="{{$ss_num}}">{{$stud_id[$site_num]}}</td>
<td rowspan="{{$ss_num}}">{{$stud_name[$site_num]}}</td>
{{/if}}
<td align="left">{{$link_ss[$sl]}}(*{{$specific[$sl]}})</td>
{{foreach from=$semes item=si key=sj}}
<td>{{if $fin_score.$sn.$sl.$si.score < 60}}<font color="red">{{/if}}{{$fin_score.$sn.$sl.$si.score}}{{if $fin_score.$sn.$sl.$si.score < 60}}</font>{{/if}}</td>
{{/foreach}}
{{if $sl!="local" and $sl!="english"}}
<td {{if $sl=="chinese"}}rowspan="3"{{/if}}>{{if $sl=="chinese"}}{{if $fin_score.$sn.language.avg.score < 60}}<font color="red">{{/if}}{{$fin_score.$sn.language.avg.score}}{{if $fin_score.$sn.language.avg.score < 60}}</font>{{/if}}{{else}}{{if $fin_score.$sn.$sl.avg.score < 60}}<font color="red">{{/if}}{{$fin_score.$sn.$sl.avg.score}}{{if $fin_score.$sn.$sl.avg.score < 60}}</font>{{/if}}{{/if}}</td>
{{if $sl=="language"}}<td rowspan="9">{{$final_score.$sn}}<br><br>({{$fin_score.$sn.avg.rank}})</td>{{/if}}
{{/if}}
</tr>

{{/foreach}}

{{/foreach}}
</table>
</td></tr>

</tr>
</table>

</td></tr>
</table>
{{/if}}
{{include file="$SFS_TEMPLATE/footer.tpl"}}
