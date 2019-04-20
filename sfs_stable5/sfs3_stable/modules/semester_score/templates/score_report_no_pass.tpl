
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<tr><td bgcolor='#FFFFFF'>
{{if $year_name}}
<tr><td>
<table border='2' cellpadding='3' cellspacing='0' style='border-collapse:collapse; font-size:{{$m_arr.text_size}};' bordercolor='#111111' width='100%'>
<tr bgcolor="#ffcccc" align="center">
<td width={{$m_arr.class_width}}>班級</td>
<td width={{$m_arr.num_width}}>座號</td>
<td width={{$m_arr.id_width}}>學號</td>
<td width={{$m_arr.name_width}}>姓名</td>
<td width={{$m_arr.name_width}}>語文領域</td>
<td width={{$m_arr.area_width}}>健康與體育</td>
<td width={{$m_arr.area_width}}>數學</td>

{{if $year_name>2}}
<td  width={{$m_arr.area_width}}>社會</td>
<td  width={{$m_arr.area_width}}>藝術與人文</td>
<td  width={{$m_arr.area_width}}>自然與生活科技</td>

{{else}}
<td  width={{$m_arr.area_width}}>生活</td>
{{/if}}

<td width={{$m_arr.area_width}}>綜合活動</td>
</tr>

{{foreach from=$student_data item=data key=sn}}
{{if $data.chk==1}}
<tr align="center">
<td>{{$data.class_name}}</td>
<td>{{$data.seme_num}}</td>
<td>{{$data.stud_id}}</td>
<td>{{$data.stud_name}}</td>

	{{if $fin_score.$sn.language.avg.score<60}}
		<td><font color='red'>{{$fin_score.$sn.language.avg.score}}</font></td>
	{{else}}
		<td></td>
	{{/if}}
	
	{{if $fin_score.$sn.health.avg.score<60}}
		<td><font color='red'>{{$fin_score.$sn.health.avg.score}}</font></td>
	{{else}}
		<td></td>
	{{/if}}
	
	{{if $fin_score.$sn.math.avg.score<60}}
		<td><font color='red'>{{$fin_score.$sn.math.avg.score}}</font></td>
	{{else}}
		<td></td>
	{{/if}}

{{if $year_name>2}}
	{{if $fin_score.$sn.social.avg.score<60}}
		<td><font color='red'>{{$fin_score.$sn.social.avg.score}}</font></td>
	{{else}}
		<td></td>
	{{/if}}

	{{if $fin_score.$sn.art.avg.score<60}}
		<td><font color='red'>{{$fin_score.$sn.art.avg.score}}</font></td>
	{{else}}
		<td></td>
	{{/if}}

	{{if $fin_score.$sn.nature.avg.score<60}}
		<td><font color='red'>{{$fin_score.$sn.nature.avg.score}}</font></td>
	{{else}}
		<td></td>
	{{/if}}

{{else}}
	{{if $fin_score.$sn.life.avg.score<60}}
		<td><font color='red'>{{$fin_score.$sn.life.avg.score}}</font></td>
	{{else}}
		<td></td>
	{{/if}}

{{/if}}


{{if $fin_score.$sn.complex.avg.score<60}}
	<td><font color='red'>{{$fin_score.$sn.complex.avg.score}}</font></td>
{{else}}
	<td></td>
{{/if}}


</tr>
{{/if}}

{{/foreach}}
</table>
</td></tr>
{{/if}}
</tr>
</td></tr>
</table>
