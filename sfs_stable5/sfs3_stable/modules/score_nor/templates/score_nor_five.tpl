{{* $Id: score_nor_five.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}} {{if $smarty.post.year_seme}}{{$class_name_menu}} <select name="years" size="1" style="background-color:#FFFFFF;font-size:13px" onchange="this.form.submit()";><option value="5" {{if $smarty.post.years==5}}selected{{/if}}>五學期</option><option value="6" {{if $smarty.post.years==6}}selected{{/if}}>六學期</option></select>{{/if}}{{if $smarty.post.me}} <input type="submit" name="friendly_print" value="友善列印">{{/if}}</td>
</tr>
{{if $smarty.post.me}}
<tr><td>
<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">
<tr bgcolor="#E1ECFF" align="center">
<td>座號</td>
<td>學號</td>
<td>姓名</td>
{{foreach from=$show_year item=i key=j}}
<td>{{$i}}學年度第{{$show_seme[$j]}}學期</td>
{{/foreach}}
<td>總平均</td>
</tr>
{{foreach from=$student_sn item=sn key=site_num name=ss}}
<tr bgcolor="#ddddff" align="center">
<td>{{$site_num}}</td>
<td>{{$stud_id[$site_num]}}</td>
<td>{{$stud_name[$site_num]}}</td>
{{foreach from=$semes item=si key=sj}}
<td>{{if $fin_score.$sn.$si.score < 60}}<font color="red">{{/if}}{{$fin_score.$sn.$si.score}}{{if $fin_score.$sn.$si.score < 60}}</font>{{/if}}</td>
{{/foreach}}
<td>{{if $fin_score.$sn.avg.score <60}}<font color="red">{{/if}}{{$fin_score.$sn.avg.score}}{{if $fin_score.$sn.avg.score <60}}</font>{{/if}}</td>
</tr>
{{/foreach}}
</table>
<br>
<font color="red">註：計算總平均時，各學期成績若高於100分，則以100分計算；若低於0分，則以0分計算。</font>
</td></tr>
{{/if}}
</tr>
</table>
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}