<!-- $Id: class_seme_score_nor.tpl 5310 2009-01-10 07:57:56Z hami $ -->
{{$break_page}}
<p>
<table cellPadding='0' border=0 cellSpacing='0' width='96%' align=center style='font-size:12pt;line-height:14pt;border-collapse:collapse;text-align:center' >
	<tr>
		<td colspan=10 style="font-size:12pt">{{$page_title}}</td>
	</tr>
</table>
<p>
<table cellPadding='0' border=1 cellSpacing='0' width='96%' align=center style='font-size:10pt;line-height:11pt;border-collapse:collapse;text-align:center' >
	<tr >
		<td width=2%>號</td>
		<td width=4%>姓名</td>
		<td width=2% >學號</td>
		{{foreach from=$subject_abs key=abs_kind item=abs_name}}
		<td  width=1%>{{$abs_name}}</td>
		{{/foreach}}
		{{foreach from=$subject_score_nor key=nor_kind item=nor_name}}
		<td  width=3%>{{$nor_name}}</td>
		{{/foreach}}
		{{foreach from=$subject_rew key=rew_kind item=rew_name}}
		<td width=1%>{{$rew_name}}</td>
		{{/foreach}}
		<td width=2%>總分</td><td  align=left>評語</td>
	</tr>

	{{foreach from=$stud_ary key=student_sn item=stud}}
	<tr>
		<td>{{$stud.seme_num}}</td><td nowrap>{{$stud.stud_name}}</td>
		<td>{{$stud.stud_id}}</td>
		<td>{{$stud.seme_abs.abs1}}</td>
		<td>{{$stud.seme_abs.abs2}}</td>
		<td>{{$stud.seme_abs.abs3}}</td>
		<td>{{$stud.seme_abs.abs4}}</td>
		<td>{{$stud.seme_abs.abs5}}</td>
		<td>{{$stud.seme_abs.abs6}}</td>				
		<td>{{$stud.seme_score_nor.score1}}</td>
		<td>{{$stud.seme_score_nor.score2}}</td>
		<td>{{$stud.seme_score_nor.score3}}</td>
		<td>{{$stud.seme_score_nor.score4}}</td>
		<td>{{$stud.seme_score_nor.score5}}</td>
		<td>{{$stud.seme_score_nor.score6}}</td>
		<td>{{$stud.seme_score_nor.score7}}</td>
		<td>{{$stud.seme_rew.sr1}}</td>
		<td>{{$stud.seme_rew.sr2}}</td>
		<td>{{$stud.seme_rew.sr3}}</td>
		<td>{{$stud.seme_rew.sr4}}</td>
		<td>{{$stud.seme_rew.sr5}}</td>
		<td>{{$stud.seme_rew.sr6}}</td>
		<td>{{$stud.seme_nor.nor_score}}</td>
		<td align=left>{{$stud.seme_nor.nor_memo}}</td>
	</tr>
	{{/foreach}}
</table>
