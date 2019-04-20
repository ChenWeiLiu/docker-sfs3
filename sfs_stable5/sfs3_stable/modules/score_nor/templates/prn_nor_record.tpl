<!-- $Id: prn_nor_record.tpl 9146 2017-09-15 06:22:33Z smallduh $  -->

{{if $break_page != ''}}{{$break_page}}{{/if}}
<table align="center" border="0" cellpadding="0" cellspacing="0" width="610">
<tr>
<td class="empty" rowspan="2" style="font-size: 18pt; line-height: 20pt; font-family: 標楷體;" align="center">{{$school_name}} 學生綜合表現記錄表</td>
<td class="empty" style="font-size: 12pt; line-height: 14pt;" align="left">學號：{{$base.stud_id}}</td>
</tr>
<tr>
<td class="empty" style="font-size: 12pt; line-height: 14pt;" align="left">姓名：{{$base.stud_name}}</td>
</tr>
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="610">
	<tbody><tr>
		<td class="top_left">身分證字號</td>
		<td class="top">{{$base.stud_person_id}}</td>
		<td class="top">出生年月日</td>
		<td class="top">{{$base.stud_birthday}}</td>
{{assign var=sex value=$base.stud_sex}}
		<td class="top">性別</td>
		<td class="top">{{$sex_kind.$sex}}</td>
		<td class="top_right" rowspan="5" style="width: 3.6cm; height: 4.6cm;">{{$base.stud_photo_src}}</td>
	</tr>
	<tr>
		<td class="left_left">連絡電話</td>
		<td>{{$base.stud_tel_2}}</td>
		<td>連絡人</td>
		<td>{{$base.guardian_name}}</td>
		<td>關係</td>
		<td>{{$guar_kind[$base.guardian_relation]}}</td>
	</tr>
	<tr>
		<td class="left_left">連絡住址</td>
		<td colspan="5" align="left">&nbsp;&nbsp;{{$base.stud_addr_2}}</td>
	</tr>
	<tr>
		<td class="left_left">入學資格</td>
		<td>{{$base.stud_mschool_name}}</td>
		<td>畢(修)業證書字號</td>
		<td colspan="3">{{$base.grade_word_num}}</td>
	</tr>
	<tr>
		<td class="left_left">異動情形</td>
		<td colspan="5" style="vertical-align: top;">{{include file=prn_move.tpl}}</td>
	</tr>
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="610">
	<tbody>
	<tr style="height: 16pt;">
		<td class="both_top" width="50">學期別</td><td class="middle_top" width="50">學年度</td><td class="middle_top" width="70">班級座號</td><td class="middle_right">日常生活表現</td>
	</tr>
{{foreach from=$seme_ary key=grade_seme item=data name=semes}}
	<tr style="height: 16pt;">
		<td class="both" width="50">{{$data.cseme}}</td><td class="middle_top" width="50">{{$data.year}}</td><td class="middle_top" width="70">{{if $data.num}}{{$data.num}}{{else}}---{{/if}}</td><td class="middle_right" style="text-align:left;">&nbsp;&nbsp;{{if $data.memo}}{{$data.memo}}{{else}}---{{/if}}</td>
	</tr>
{{/foreach}}
	</tbody>
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="610">
	<tbody>
	<tr style="height: 16pt;">
		<td class="both_top" width="50" rowspan="2">學期別</td>
		<td class="middle_right" colspan="6">獎懲情形 (次)</td>
		<td class="middle_right" colspan="6">出缺席情形 (節)</td>
	</tr>
	<tr style="height: 16pt;">
		<td class="middle_top">大功</td>
		<td class="middle_top">小功</td>
		<td class="middle_top">嘉獎</td>
		<td class="middle_top">大過</td>
		<td class="middle_top">小過</td>
		<td class="middle_right">警告</td>
		<td class="middle_top">事假</td>
		<td class="middle_top">病假</td>
		<td class="middle_top">曠課</td>
		<td class="middle_top">集會</td>
		<td class="middle_top">公假</td>
		<td class="middle_right">其他</td>
	</tr>
{{foreach from=$seme_ary key=grade_seme item=data name=semes}}
	<tr style="height: 16pt;">
		<td class="both">{{$data.cseme}}</td>
{{foreach from=$rew_data.$grade_seme item=d name=rew}}
		<td class="{{if $smarty.foreach.rew.iteration==6}}middle_right{{else}}middle_top{{/if}}">{{if $d}}{{$d}}{{else}}0{{/if}}</td>
{{/foreach}}
{{foreach from=$abs_data.$grade_seme item=d name=abs}}
		<td class="{{if $smarty.foreach.abs.iteration==6}}middle_right{{else}}middle_top{{/if}}">{{if $d}}{{$d}}{{else}}0{{/if}}</td>
{{/foreach}}
	</tr>
{{/foreach}}
	</tbody>
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="610">
	<tbody>
	<tr style="height: 16pt;">
		<td class="left_left" width="70">獎懲日期</td>
		<td class="middle_top" width="50">學期別</td>
		<td class="middle_top" width="70">獎懲類別</td>
		<td class="middle_top">獎懲事由</td>
		<td class="middle_right" width="50">銷過</td>
	</tr>
{{foreach from=$rew_record item=d}}
	<tr style="height: 16pt;">
		<td class="left_left">{{$d.reward_date}}</td>
{{assign var=sid value=$d.reward_year_seme}}
		<td class="middle_top">{{$seme_arr2.$sid}}</td>
{{assign var=rid value=$d.reward_kind}}
		<td class="middle_top">{{$reward_arr.$rid}}</td>
		<td class="middle_top" style="text-align:left">&nbsp;{{$d.reward_reason}}</td>
		<td class="middle_right">{{if $d.reward_div==1}}---{{else}}{{if $d.reward_cancel_date=="0000-00-00"}}否{{else}}是{{/if}}{{/if}}</td>
	</tr>
{{/foreach}}
	</tbody>
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="610">
	<tbody>
	<tr style="height: 16pt;">
		<td class="left_left" width="50">學期別</td>
		<td class="middle_top" width="100">社團名稱</td>
		<td class="middle_top" width="50">檢核</td>
		<td class="middle_top" width="125">教師評語</td>
		<td class="middle_right" width="285">自我省思</td>
	</tr>
	{{foreach from=$club item=d}}
	<tr style="height: 16pt;">
	{{assign var=sid value=$d.seme_year_seme}}
		<td class="left_left">{{$seme_arr2.$sid}}</td>
		<td class="middle_top">{{$d.association_name}}</td>
		<td class="middle_top">{{$d.pass_txt}}</td>
		<td class="middle_middle" style="text-align:left;font-size:10pt" width="125">{{$d.description}}</td>
		<td class="middle_right" style="text-align:left;font-size:10pt" width="285">{{$d.stud_feedback}}</td>
	</tr>	
	{{/foreach}}
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="610">
	<tbody>
	<tr style="height: 16pt;">
		<td class="left_left" width="50">學期別</td>
		<td class="middle_top" width="100">日期</td>
		<td class="middle_top" width="290">參加校內外公共服務學習事項及活動項目</td>
		<td class="middle_top" width="70">時間(分)</td>
		<td class="middle_right" width="100">主辦單位</td>
	</tr>
	{{foreach from=$service item=d}}
	<tr style="height: 16pt;">
	{{assign var=sid value=$d.year_seme}}
		<td class="left_left">{{$seme_arr2.$sid}}</td>
		<td class="middle_top">{{$d.service_date}}</td>
		<td class="middle_top" style="text-align:left;font-size:10pt">{{$d.item}}：{{$d.memo}}：{{$d.studmemo}}</td>
		<td class="middle_middle">{{$d.minutes}}</td>
		<td class="middle_right" >{{$d.sponsor}}</td>
	</tr>	
	{{/foreach}}	
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="610">
	<tbody>
{{if $room_sign==1}}	
	<tr style="height: 32pt;">
		<td class="left_left" width="20%">處室簽章</td>
		<td class="middle_top">承辦人</td>
	</tr>
{{else}}
		<tr style="height: 16pt;">
		<td class="left_left">製 表 人</td>
		<td class="middle_top">學務主任</td>
		<td class="middle_right">校　　長</td>
	</tr>
	<tr style="height: 16pt;">
		<td class="bottom_left">&nbsp;</td>
		<td class="bottom_middle" align="center">
    {{if $title_img_3}}	
     <img src="{{$title_img_3}}">
    {{else}}
		&nbsp;
		{{/if}}
	  </td>
		<td class="bottom_right" align="center">
    {{if $title_img_1}}	
     <img src="{{$title_img_1}}">
    {{else}}
		&nbsp;
		{{/if}}
		</td>	
	</tr>
{{/if}}	
	</tbody>
</table>
