{{* $Id: score_nor_disgrad2.tpl 8492 2015-08-19 12:53:57Z brucelyc $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
{{assign var=semeday2 value=$smarty.post.semeday2}}
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}}
<select name="years" size="1" style="background-color:#FFFFFF;font-size:13px" onchange="this.form.submit()";><option value="5" {{if $smarty.post.years==5}}selected{{/if}}>五學期</option><option value="6" {{if $smarty.post.years==6}}selected{{/if}}>六學期</option></select>
<select name="item" size="1" style="background-color:#FFFFFF;font-size:13px" onchange="this.form.submit()";><option value="0" {{if !$smarty.post.item}}selected{{/if}}>勤惰</option><option value="1" {{if $smarty.post.item}}selected{{/if}}>獎懲</option></select><br>
{{if $smarty.post.item}}
<input type="checkbox" name="chk3" checked OnClick="this.form.submit();">在學期間記滿三大過(含)以上者<span style="color:blue;"> (含折算累計：三次警告折算一次小過，三次小過折算一次大過)</span><br>
<input type="checkbox" name="neu" {{if $smarty.post.neu}}checked{{/if}} OnClick="this.form.submit();" value="1">功過不相抵<span style="color:blue;">(僅統計懲戒)</span><br>
{{else}}
<input type="checkbox" name="chk1" {{if $smarty.post.chk1}}checked{{/if}} OnClick="this.form.submit();">任一學期曠課、事假超過<input type="text" name="semeday" value="{{$smarty.post.semeday}}" style="width:20pt" OnChange="this.form.submit();">節<br>
<input type="checkbox" name="chk3" {{if $smarty.post.chk3}}checked{{/if}} OnClick="this.form.submit();">應出席未達2/3(67%)，各學期應出席總節數為<input type="text" name="tdays" value="{{$smarty.post.tdays}}" style="width: 30pt;">節<br>
{{/if}}
<span style="color:red;">(請依各縣市規定選取)</span><br>
{{if $smarty.post.item}}
註：1.為統計方便，計算時全部換算成警告與嘉獎次數，正數代表嘉獎，負數代表警告，最後篩選出總數大於或等於二十七次警告者。<br>
　　2.若獎懲採逐次紀錄者，請先將各學期之獎懲紀錄重新統計，否則已銷過之紀錄仍可能被統計在內。<br>
　　3.若獎懲採學期末填總數者，請自行扣除銷過紀錄，否則篩選出之學生可能有誤。
{{/if}}
</td>
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
<td>{{$i}}學年度<br>第{{$show_seme[$j]}}學期
{{if !$smarty.post.item}}
<br>
{{if $smarty.post.chk1}}事曠節數{{/if}}
{{if $smarty.post.chk1 && $smarty.post.chk2}} | {{/if}}
{{if $smarty.post.chk2}}其他節數{{/if}}
{{/if}}
</td>
{{/foreach}}
{{if $smarty.post.item}}
<td>合計</td>
{{/if}}
{{if $smarty.post.chk3}}
<td>事曠<br>總節數</td>
<td>應出席<br>總節數</td>
<td>出席率</td>
{{/if}}
</tr>
{{foreach from=$show_sn item=sc key=sn}}
{{assign var=dall value=0}}
{{assign var=sall value=0}}
<tr bgcolor="#ddddff" align="center">
<td>{{$sclass[$sn]}}</td>
<td>{{$snum[$sn]}}</td>
<td>{{$stud_id[$sn]}}</td>
<td>{{$stud_name[$sn]}}</td>
{{foreach from=$semes item=si key=sj}}
{{if $smarty.post.item}}
<td>{{$fin_score.$sn.$si.rew.all|intval}}</td>
{{else}}
{{assign var=c value=$fin_score.$sn.$si.abs.all|intval}}
<td>
{{if $smarty.post.chk1}}<span {{if $fin_score.$sn.$si.abs.3 >= $smarty.post.semeday}}style="color:red;"{{/if}}>{{$fin_score.$sn.$si.abs.3|intval}}</span>{{/if}}
{{if $smarty.post.chk1 && $smarty.post.chk2}} | {{/if}}
{{if $smarty.post.chk2}}{{$fin_score.$sn.$si.abs.all-$fin_score.$sn.$si.abs.3|intval}}{{/if}}
</td>
{{assign var=dall value=$dall+$fin_score.$sn.$si.abs.3}}
{{assign var=sall value=$sall+$fin_score.$sn.$si.abs.all-$fin_score.$sn.$si.abs.3}}
{{/if}}
{{/foreach}}
{{if $smarty.post.item}}
<td>{{$fin_score.$sn.all.rew.all}}</td>
{{/if}}
{{if $smarty.post.chk3}}
<td>{{$dall}}</td>
{{assign var=stotal value=$smarty.post.tdays-$sall}}
<td>{{$stotal}}</td>
{{assign var=rtotal value=$stotal-$dall}}
{{php}}
$this->_tpl_vars['rr'] = round($this->_tpl_vars['rtotal'] * 100 / $this->_tpl_vars['stotal']); 
{{/php}}
<td><span style="color: {{if $rr<67}}red{{else}}black{{/if}};">{{$rr}}%</span></td>
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
