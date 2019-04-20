{{* $Id: score_manage_new_disgrad.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}} <select name="years" size="1" style="background-color:#FFFFFF;font-size:13px" onchange="this.form.submit()";><option value="5" {{if $smarty.post.years==5}}selected{{/if}}>五學期</option><option value="6" {{if $smarty.post.years==6}}selected{{/if}}>六學期</option></select>學習領域平均成績在60分以上者未達<input type="text" name="fail_num" size="1" value="{{if $smarty.post.fail_num == ""}}3{{else}}{{$smarty.post.fail_num}}{{/if}}">項{{if $smarty.post.year_name}} <input type="submit" name="friendly_print" value="友善列印">{{/if}}</td>
</tr>
{{if $smarty.post.year_name}}
<tr><td>
<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">
<tr bgcolor="#E1ECFF" align="center">
<td>班級</td>
<td>座號</td>
<td>學號</td>
<td>姓名</td>
<td>語文</td>
<td>數學</td>
<td>自然與生活科技</td>
<td>社會</td>
<td>健康與體育</td>
<td>藝術與人文</td>
<td>綜合</td>
</tr>
{{foreach from=$show_sn item=sc key=sn}}
<tr bgcolor="#ddddff" align="center">
<td>{{$sclass[$sn]}}</td>
<td>{{$snum[$sn]}}</td>
<td>{{$stud_id[$sn]}}</td>
<td>{{$stud_name[$sn]}}</td>
{{foreach from=$show_ss item=ssn key=ss}}
<td>{{if $fin_score.$sn.$ss.avg.score < 60}}<font color="red">{{/if}}{{$fin_score.$sn.$ss.avg.score}}{{if $fin_score.$sn.$ss.avg.score < 60}}</font>{{/if}}</td>
{{/foreach}}
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