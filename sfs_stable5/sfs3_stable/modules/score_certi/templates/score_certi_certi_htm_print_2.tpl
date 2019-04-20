{{* $Id: score_certi_certi_htm_print.tpl 5841 2010-02-03 15:07:12Z brucelyc $ *}}
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>Academic record proof</title>
</head>
<style type="text/css">
<!--
td {
	font-size: 11pt;
}
-->
</style>
<body>
<center>
{{foreach from=$student_sn item=sn name=ss key=ssi}}
<table border="0" cellspacing="0" cellpadding="0" width="610" {{if ($smarty.foreach.ss.iteration) mod 2 == 0 || $smarty.post.sel_paper == 1}}style="page-break-after: always"{{/if}}>
<tr align="right">
<td colspan="29" align="center" style="font-size: 16pt;font-family: 標楷體;font-weight: bold;">Academic record proof<br>of<br>{{$sch.sch_ename}}<br><br>
<table border="0">
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td>Name:{{$stud_name[$sn]}}<td width="150"><td>Birthday:{{$stud_birthday[$sn]}}</td></tr>
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td>Student Number:{{$stud_id[$sn]}}</b><td><td><b>Enroll Year & Month: Aug,{{$stud_study_year+1911}}</td></tr>
</table>
</tr>
<tr align="center">
<td style="border-style: solid; border-width: 1.5pt;"><font style="font-size: 12pt;">Study Area</font></td>
{{foreach from=$show_year item=i key=j}}
<td colspan="2" style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0pt;"><font style="font-size: 8pt;">Year {{$i}}<br>Semester {{$show_seme[$j]}}</font></td>
{{/foreach}}
{{if $smarty.post.include_avg}}
<td colspan="2" style="border-style: solid; border-width: 1.5pt 0.75pt 1.5pt 0pt;"><font style="font-size: 8pt;">Average</font></td>
<td colspan="2" style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0.75pt;"><font style="font-size: 8pt;">Weighting Average</font></td>
{{/if}}
</tr>
{{foreach from=$ss_link item=sl name=ss_link}}
<tr align="center">
<td style="border-style: solid; border-width: 0pt 1.5pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 1.5pt;"><font style="font-size: 8pt;">&nbsp;{{$link_ss[$sl]}}</font></td>
{{foreach from=$semes item=si key=sj}}
{{assign var="grade_chi" value=$fin_score.$sn.$sl.$si.str}}
<td style="border-style:solid; border-width:0pt 0.75pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $fin_score.$sn.$sl.$si.score == ""}}---{{else}}{{$fin_score.$sn.$sl.$si.score}}{{/if}}</td>
<td style="border-style:solid; border-width:0pt 1.5pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $fin_score.$sn.$sl.$si.score == ""}}---{{else}}{{$grade_eng.$grade_chi}}{{/if}}</td>
{{/foreach}}
{{if $smarty.post.include_avg}}
{{if $sl!="local" and $sl!="english"}}
{{assign var="grade_chi" value=$fin_score.$sn.$sl.avg.str}}
<td {{if $sl=="chinese"}}rowspan="3"{{/if}} style="border-style: solid; border-width: 0pt 0.75pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $sl=="chinese"}}{{if $fin_score.$sn.language.avg.score==""}}---{{else}}{{$fin_score.$sn.language.avg.score}}{{/if}}{{else}}{{if $fin_score.$sn.$sl.avg.score==""}}---{{else}}{{$fin_score.$sn.$sl.avg.score}}{{/if}}{{/if}}</td>
<td {{if $sl=="chinese"}}rowspan="3"{{/if}} style="border-style: solid; border-width: 0pt 0.75pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $sl=="chinese"}}{{if $fin_score.$sn.language.avg.score=="" || $fin_score.$sn.language.avg.str==""}}---{{else}}{{$fin_score.$sn.language.avg.str}}{{/if}}{{else}}{{if $fin_score.$sn.$sl.avg.score=="" || $fin_score.$sn.$sl.avg.str==""}}---{{else}}{{$grade_eng.$grade_chi}}{{/if}}{{/if}}</td>
{{/if}}
{{if $sl=="chinese"}}
{{assign var="grade_chi" value=$fin_score.$sn.avg.str}}
<td rowspan="{{$area_span}}" style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0.75pt;">{{if $fin_score.$sn.avg.score == ""}}---{{else}}{{$fin_score.$sn.avg.score}}{{/if}}</td>
<td rowspan="{{$area_span}}" style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 0pt;">{{if $fin_score.$sn.avg.score == "" || $fin_score.$sn.avg.str == ""}}---{{else}}{{$grade_eng.$grade_chi}}{{/if}}</td></tr>
{{/if}}
{{/if}}
</tr>
{{/foreach}}
{{if !$no_seme_avg && $smarty.post.include_avg}}
<tr align="center">
<td style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 1.5pt;"><font style="font-size: 12pt;" size="1">&nbsp;Average</font></td>
{{foreach from=$semes item=si key=sj}}
{{assign var="grade_chi" value=$fin_score.$sn.$si.avg.str}}
<td style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0pt;">{{if $fin_score.$sn.$si.avg.score == ""}}---{{else}}{{$fin_score.$sn.$si.avg.score}}{{/if}}</td>
<td style="border-style:solid; border-width:0pt 1.5pt 1.5pt 0pt;">{{if $fin_score.$sn.$si.avg.score == "" || $fin_score.$sn.$si.avg.str == ""}}---{{else}}{{$grade_eng.$grade_chi}}{{/if}}</td>
{{/foreach}}
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;">---</td>
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;">---</td>
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0.75pt;">---</td>
<td style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 0pt;">---</td></tr>
</tr>
{{/if}}
{{if $smarty.post.include_nor}}
<tr align="center">
<td style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 1.5pt;" align="left"><font style="font-size: 8pt;" size="1">&nbsp;Daily life performance result</font></td>
{{foreach from=$semes item=si key=sj}}
{{assign var="grade_chi" value=$fin_nor_score.$sn.$si.str}}
<td style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.$si.score == ""}}---{{else}}{{$fin_nor_score.$sn.$si.score}}{{/if}}</td>
<td style="border-style:solid; border-width:0pt 1.5pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.$si.score == "" || $fin_nor_score.$sn.$si.str == ""}}---{{else}}{{$grade_eng.$grade_chi}}{{/if}}</td>
{{/foreach}}
{{assign var="grade_chi" value=$fin_nor_score.$sn.avg.str}}
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.avg.score == ""}}---{{else}}{{$fin_nor_score.$sn.avg.score}}{{/if}}</td>
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.avg.score == "" || $fin_nor_score.$sn.avg.str == ""}}---{{else}}{{$grade_eng.$grade_chi}}{{/if}}</td>
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0.75pt;">---</td>
<td style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 0pt;">---</td></tr>
</tr>
{{/if}}
<tr>
<td colspan="29"><br>{{$print_str}}<br>
<font style="font-size: 12pt;font-family: Arial;"><p align="right">Date of Printed : {{$year}}.{{$month}}.{{$day}}</p>{{if ($smarty.foreach.ss.iteration) mod 2 == 1}}<br><br><br>{{if !$smarty.post.include_nor}}<br>{{/if}}{{/if}}</font></td>
</tr>
</table>
{{/foreach}}
</center>
</body>
</html>
