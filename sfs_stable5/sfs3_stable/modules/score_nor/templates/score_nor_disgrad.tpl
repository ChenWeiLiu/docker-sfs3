{{* $Id: score_nor_disgrad.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}} <select name="years" size="1" style="background-color:#FFFFFF;font-size:13px" onchange="this.form.submit()";><option value="5" {{if $smarty.post.years==5}}selected{{/if}}>五學期</option><option value="6" {{if $smarty.post.years==6}}selected{{/if}}>六學期</option></select><br>
<input type="checkbox" checked>日常生活表現成績未達「各學期均在60分以上」<font color="red">(必選)</font><br>
{{if $smarty.post.years==6}}<input type="checkbox" name="chk_last"{{if $smarty.post.chk_last}}checked{{/if}} OnChange="this.form.submit();">且第六學期日常生活表現成績亦未達60分{{/if}}</td>
</tr>
{{if $smarty.post.year_name}}
<tr><td>
<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">
<tr bgcolor="#E1ECFF" align="center">
<td>班級</td>
<td>座號</td>
<td>學號</td>
<td>姓名</td>
{{foreach from=$show_year item=i key=j}}
<td>{{$i}}學年度<br>第{{$show_seme[$j]}}學期</td>
{{/foreach}}
{{if $smarty.post.years!=6}}
<td>總平均</td>
{{/if}}
</tr>
{{foreach from=$show_sn item=sc key=sn}}
<tr bgcolor="#ddddff" align="center">
<td>{{$sclass[$sn]}}</td>
<td>{{$snum[$sn]}}</td>
<td>{{$stud_id[$sn]}}</td>
<td>{{$stud_name[$sn]}}</td>
{{foreach from=$semes item=si key=sj}}
<td>{{if $fin_score.$sn.$si.score < 60}}<font color="red">{{/if}}{{$fin_score.$sn.$si.score}}{{if $fin_score.$sn.$si.score < 60}}</font>{{/if}}</td>
{{/foreach}}
{{if $smarty.post.years!=6}}
<td>{{if $fin_score.$sn.avg.score <60}}<font color="red">{{/if}}{{$fin_score.$sn.avg.score}}{{if $fin_score.$sn.avg.score <60}}</font>{{/if}}</td>
{{/if}}
</tr>
{{/foreach}}
</table>
</td></tr>
{{/if}}
</tr>
</table>
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}