{{*  *}}
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>{{$show_year.0}}學年第{{$show_seme.0}}學期{{$year_name}}年級{{$m_arr.title}}</title>
</head>
<body>
{{if $m_arr.style}}
<P align="center" style='font-size:{{$m_arr.title_font_size}}; font-family:{{$m_arr.title_font_name}}'>{{$school_long_name}}{{$show_year.0}}學年第{{$show_seme.0}}學期{{$year_name}}年級{{$m_arr.title}}</P>
<table border='2' cellpadding='3' cellspacing='0' style='border-collapse:collapse; font-size:{{$m_arr.text_size}};' bordercolor='#111111' width='100%'>
<tr bgcolor="{{$m_arr.header_bgcolor}}" align="center">
<td rowspan=2 width={{$m_arr.class_width}}>班級</td>
<td rowspan=2 width={{$m_arr.num_width}}>座號</td>
<td rowspan=2 width={{$m_arr.id_width}}>學號</td>
<td rowspan=2 width={{$m_arr.name_width}}>姓名</td>
<td colspan=4>語文領域</td>
<td rowspan=2 width={{$m_arr.area_width}}>健康與體育</td>
<td rowspan=2 width={{$m_arr.area_width}}>數學</td>


{{if $year_name>2}}
<td rowspan=2 width={{$m_arr.area_width}}>社會</td>
<td rowspan=2 width={{$m_arr.area_width}}>藝術與人文</td>
<td rowspan=2 width={{$m_arr.area_width}}>自然與生活科技</td>

{{else}}
<td rowspan=2 width={{$m_arr.area_width}}>生活</td>
{{/if}}

<td rowspan=2 width={{$m_arr.area_width}}>綜合活動</td>
<td rowspan=2 width={{$m_arr.avg_width}}>領域<br>平均</td>
<td colspan=4>日常生活表現</td>
</tr>
<tr bgcolor="{{$m_arr.header_bgcolor}}" align="center">
<td width={{$m_arr.area_width}}>本國語文</td>
<td width={{$m_arr.area_width}}>英語</td>
<td width={{$m_arr.area_width}}>本土語言</td>
<td width={{$m_arr.avg_width}}>平均</td>
<td>日常行為</td>
<td>團體活動</td>
<td>公共服務</td>
<td>特殊表現</td>
</tr>

{{foreach from=$student_data item=data key=sn}}
<tr align="center">
<td>{{$data.class_name}}</td>
<td>{{$data.seme_num}}</td>
<td>{{$data.stud_id}}</td>
<td>{{$data.stud_name}}</td>

<td>{{$fin_score.$sn.chinese.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.english.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.local.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.language.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.health.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.math.$curr_seme.score}}</td>

{{if $year_name>2}}
<td>{{$fin_score.$sn.social.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.art.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.nature.$curr_seme.score}}</td>
{{else}}
<td>{{$fin_score.$sn.life.$curr_seme.score}}</td>
{{/if}}


<td>{{$fin_score.$sn.complex.$curr_seme.score}}</td>
<td bgcolor='{{$m_arr.area_avg_bgcolor}}'>{{$fin_score.$sn.avg.score}}</td>
<td align='left'>{{$student_data.$sn.nor.0}}</td>
<td align='left'>{{$student_data.$sn.nor.1}}</td>
<td align='left'>{{$student_data.$sn.nor.2}}{{$student_data_nor.$sn.nor.3}}</td>
<td align='left'>{{$student_data.$sn.nor.4}}{{$student_data_nor.$sn.nor.5}}</td>
</tr>

{{/foreach}}
</table>

{{else}}

<P align="center" style='font-size:{{$m_arr.title_font_size}}; font-family:{{$m_arr.title_font_name}}'>{{$school_long_name}}<br>{{$show_year.0}}學年第{{$show_seme.0}}學期{{$year_name}}年級{{$m_arr.title}}</P>
<table border='2' cellpadding='3' cellspacing='0' style='border-collapse:collapse; font-size:{{$m_arr.text_size}};' bordercolor='#111111' width='100%'>
<tr bgcolor="{{$m_arr.header_bgcolor}}" align="center">
<td rowspan=2>班級</td>
<td rowspan=2>座號</td>
<td rowspan=2>學號</td>
<td rowspan=2>姓名</td>
<td colspan=4>語文領域</td>
<td rowspan=2>健康與體育</td>
<td rowspan=2>數學</td>

{{if $year_name>2}}
<td rowspan=2>社會</td>
<td rowspan=2>藝術與人文</td>
<td rowspan=2>自然與生活科技</td>

{{else}}
<td rowspan=2>生活</td>
{{/if}}

<td rowspan=2>綜合活動</td>
<td rowspan=2>領域<br>平均</td>
</tr>
<tr bgcolor="{{$m_arr.header_bgcolor}}" align="center">
<td>本國語文</td>
<td>英語</td>
<td>本土語言</td>
<td>平均</td>
</tr>

{{foreach from=$student_data item=data key=sn}}
<tr align="center">
<td>{{$data.class_name}}</td>
<td>{{$data.seme_num}}</td>
<td>{{$data.stud_id}}</td>
<td>{{$data.stud_name}}</td>

<td>{{$fin_score.$sn.chinese.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.english.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.local.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.language.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.health.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.math.$curr_seme.score}}</td>

{{if $year_name>2}}
<td>{{$fin_score.$sn.social.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.art.$curr_seme.score}}</td>
<td>{{$fin_score.$sn.nature.$curr_seme.score}}</td>
{{else}}
<td>{{$fin_score.$sn.life.$curr_seme.score}}</td>
{{/if}}

<td>{{$fin_score.$sn.complex.$curr_seme.score}}</td>
<td bgcolor='{{$m_arr.area_avg_bgcolor}}'>{{$fin_score.$sn.avg.score}}</td>
</tr>

{{/foreach}}
</table>
{{if $m_arr.print_sign_row}}<br>{{$m_arr.sign_row}}{{/if}}

<br style="page-break-before:always;">

<P align="center" style='font-size:{{$m_arr.title_font_size}}; font-family:{{$m_arr.title_font_name}}'>{{$school_long_name}}<br>{{$show_year.0}}學年第{{$show_seme.0}}學期{{$year_name}}年級{{$m_arr.title}}</P>
<table border='2' cellpadding='3' cellspacing='0' style='border-collapse:collapse; font-size:{{$m_arr.text_size}};' bordercolor='#111111' width='100%'>
<tr bgcolor="{{$m_arr.header_bgcolor}}" align="center">
<td rowspan=2 width={{$m_arr.class_width}}>班級</td>
<td rowspan=2 width={{$m_arr.num_width}}>座號</td>
<td rowspan=2 width={{$m_arr.id_width}}>學號</td>
<td rowspan=2 width={{$m_arr.name_width}}>姓名</td>
<td colspan=4>日常生活表現</td>
</tr>
<tr bgcolor="{{$m_arr.header_bgcolor}}" align="center">
<td>日常行為</td>
<td>團體活動</td>
<td>公共服務</td>
<td>特殊表現</td>
</tr>

{{foreach from=$student_data item=data key=sn}}
<tr align="center">
<td>{{$data.class_name}}</td>
<td>{{$data.seme_num}}</td>
<td>{{$data.stud_id}}</td>
<td>{{$data.stud_name}}</td>
<td align='left'>{{$student_data.$sn.nor.0}}</td>
<td align='left'>{{$student_data.$sn.nor.1}}</td>
<td align='left'>{{$student_data.$sn.nor.2}}{{$student_data_nor.$sn.nor.3}}</td>
<td align='left'>{{$student_data.$sn.nor.4}}{{$student_data_nor.$sn.nor.5}}</td>
</tr>

{{/foreach}}
</table>


{{/if}}
{{if $m_arr.print_sign_row}}<br>{{$m_arr.sign_row}}{{/if}}
</body>
</html>
