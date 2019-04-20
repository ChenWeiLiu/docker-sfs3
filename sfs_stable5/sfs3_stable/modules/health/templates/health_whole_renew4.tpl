{{* $Id: health_whole_renew4.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<form action="{{$smarty.server.SCRIPT_NAME}}" method="post" target="_blank">
{{assign var=sn value=$smarty.post.student_sn}}

{{* 預防接種 *}}
{{assign var=inject value=$health_data->health_data.$sn.inject}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="3" style="color:white;"><input type="image" src="images/edit.gif">預防接種</td>
</tr>
<tr style="background-color:#f4feff;">
<td>項目</td><td>學前</td><td>接種記錄</td>
</tr>
<tr style="background-color:white;">
<td>黃卡</td><td>　</td><td>{{if $inject.0.0.times==1}}<span style="color:blue;">已繳{{else}}<span style="color:red;">未繳{{/if}}</span></td>
</tr>
<tr style="background-color:#f4feff;">
<td>卡介苗</td><td>{{$inject.0.1.times|intval}}</td><td>　</td>
</tr>
<tr style="background-color:white;">
<td>B型肝炎</td><td>{{$inject.0.2.times|intval}}</td><td>　</td>
</tr>
<tr style="background-color:#f4feff;">
<td>小兒麻痺</td><td>{{$inject.0.3.times|intval}}</td><td>　</td>
</tr>
<tr style="background-color:white;">
<td>破傷風白喉</td><td>{{$inject.0.4.times|intval}}</td><td>　</td>
</tr>
<tr style="background-color:#f4feff;">
<td>日本腦炎</td><td>{{$inject.0.5.times|intval}}</td><td>　</td>
</tr>
<tr style="background-color:white;">
<td>MMR</td><td>{{$inject.0.7.times|intval}}</td><td>　</td>
</tr>
</table>

{{* 臨時性預防接種 *}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="2" style="color:white;"><input type="image" src="images/edit.gif">臨時性預防接種</td>
</tr>
</table>

{{* 臨時性檢查 *}}
<table style="background-color:#9ebcdd;" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="2" style="color:white;"><input type="image" src="images/edit.gif">臨時性檢查</td>
</tr>
</table>
<input type="hidden" name="sub_menu_id" value="{{$smarty.post.sub_menu_id}}">
<input type="hidden" name="year_seme" value="{{$smarty.post.year_seme}}">
<input type="hidden" name="class_name" value="{{$smarty.post.class_name}}">
<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">
<input type="hidden" name="act" value="">
</form>
