{{* $Id: score_certi_stage_print.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>學生定考成績證明書</title>
</head>
<style type="text/css">
<!--
td {
	font-size: 11pt;
}
tr {
	line-height: 25pt;
}
-->
</style>
<body>
<center>
<table border="0" cellspacing="0" cellpadding="0" width="610" {{if ($smarty.foreach.ss.iteration) mod 2 == 0 || $smarty.post.sel_paper == 1}}style="page-break-after: always"{{/if}}>
<tr align="right">
<td colspan="29" align="center" style="font-size: 16pt;font-family: 標楷體;font-weight: bold;">{{$sch.sch_cname}}學生定考成績證明書<br><br>
<table border="0">
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td>學生姓名：{{$stud_name}}<td width="150"><td>出生年月日：{{$stud_birthday}}</td></tr>
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td>學生學號：{{$stud_id}}</b><td><td><b>入學年月　：{{$stud_study_year}}年08月</td></tr>
</table>
</tr>
<tr align="center">
<td style="border-style: solid; border-width: 1.5pt;" rowspan="2"><font style="font-size: 8pt;">科目\學期</font></td>
{{foreach from=$all_score item=d key=i}}
{{foreach from=$d item=dd key=j}}
<td colspan="3" style="border-style: solid; border-width: 1.5pt 1.5pt 0.75pt 0pt;"><font style="font-size: 8pt;">{{$i}}學年度<br>第{{$j}}學期</font></td>
{{/foreach}}
{{/foreach}}
</tr>
<tr align="center">
{{foreach from=$all_score item=d key=i}}
{{foreach from=$d item=dd key=j}}
{{foreach from=$test_sort item=t}}
<td style="border-style: solid; border-width: 0pt {{if $t==3}}1.5{{else}}0.75{{/if}}pt 1.5pt 0pt;"><font style="font-size: 8pt;">{{$t}}</font></td>
{{/foreach}}
{{/foreach}}
{{/foreach}}
</tr>
{{foreach from=$all_ss item=d}}
{{foreach from=$d item=dd key=i}}
<tr align="center">
<td style="border-style: solid; border-width: 0pt 1.5pt 0.75pt 1.5pt;" align="center"><font style="font-size: 8pt;">&nbsp;{{$i}}</font></td>
{{foreach from=$all_score item=ddd key=y}}
{{foreach from=$ddd item=dddd key=s}}
{{foreach from=$test_sort item=t}}
<td style="border-style:solid; border-width:0pt {{if $t==3}}1.5{{else}}0.75{{/if}}pt 0.75pt 0pt;">{{if $dddd.$i.$t == ""}}---{{else}}{{$dddd.$i.$t}}{{/if}}</td>
{{/foreach}}
{{/foreach}}
{{/foreach}}
</tr>
{{/foreach}}
{{/foreach}}
<tr>
<td colspan="29" style="border-style: solid; border-width: 0.75pt 0pt 0pt 0pt;"><br>{{$print_str}}
<font style="font-size: 15pt;font-family: 標楷體;"><p align="center">中　華　民　國　{{$year}}　年　{{$month}}　月　{{$day}}　日</p>{{if ($smarty.foreach.ss.iteration) mod 2 == 1}}<br><br><br>{{if !$smarty.post.include_nor}}<br>{{/if}}{{/if}}</font></td>
</tr>
</table>
</center>
</body>
</html>
