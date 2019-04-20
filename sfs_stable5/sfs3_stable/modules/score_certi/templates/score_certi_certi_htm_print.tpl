{{* $Id: score_certi_certi_htm_print.tpl 8291 2015-01-15 14:07:34Z brucelyc $ *}}
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>學生成績證明書</title>
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
<td colspan="29" align="center" style="font-size: 16pt;font-family: 標楷體;font-weight: bold;">{{$sch.sch_cname}}學生成績證明書<br><br>
<table border="0">
{{if $smarty.post.include_no}}
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td colspan=4 align="right">({{$sel_year}}){{$default_pword}}第{{$start_no++}}號</td></tr>
<tr><td>　</td></tr>
{{/if}}
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td>學生姓名：{{$stud_name[$sn]}}<td width="150"><td>出生年月日：{{$stud_birthday[$sn]}}</td></tr>
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td>學生學號：{{$stud_id[$sn]}}</b><td><td><b>入學年月　：{{$stud_study_year}}年08月</td></tr>
</table>
</tr>
<tr align="center">
<td style="border-style: solid; border-width: 1.5pt;"><font style="font-size: 8pt;">領域</font></td>
{{foreach from=$show_year item=i key=j}}
<td colspan="2" style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0pt;"><font style="font-size: 8pt;">{{$i}}學年度<br>第{{$show_seme[$j]}}學期</font></td>
{{/foreach}}
{{if $smarty.post.include_avg}}
<td colspan="2" style="border-style: solid; border-width: 1.5pt 0.75pt 1.5pt 0pt;"><font style="font-size: 8pt;">各領域<br>總平均</font></td>
<td colspan="2" style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0.75pt;"><font style="font-size: 8pt;">各領域加權<br/>總平均</font></td>
{{/if}}
</tr>
{{foreach from=$ss_link item=sl name=ss_link}}
<tr align="center">
<td style="border-style: solid; border-width: 0pt 1.5pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 1.5pt;" align="left"><font style="font-size: 8pt;">&nbsp;{{$link_ss[$sl]}}</font></td>
{{foreach from=$semes item=si key=sj}}
<td style="border-style:solid; border-width:0pt 0.75pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $fin_score.$sn.$sl.$si.score == ""}}---{{else}}{{$fin_score.$sn.$sl.$si.score}}{{/if}}</td>
<td style="border-style:solid; border-width:0pt 1.5pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $fin_score.$sn.$sl.$si.score == ""}}---{{else}}{{$fin_score.$sn.$sl.$si.str}}{{/if}}</td>
{{/foreach}}
{{if $smarty.post.include_avg}}
{{if $sl!="local" and $sl!="english"}}
<td {{if $sl=="chinese"}}rowspan="3"{{/if}} style="border-style: solid; border-width: 0pt 0.75pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $sl=="chinese"}}{{if $fin_score.$sn.language.avg.score==""}}---{{else}}{{$fin_score.$sn.language.avg.score}}{{/if}}{{else}}{{if $fin_score.$sn.$sl.avg.score==""}}---{{else}}{{$fin_score.$sn.$sl.avg.score}}{{/if}}{{/if}}</td>
<td {{if $sl=="chinese"}}rowspan="3"{{/if}} style="border-style: solid; border-width: 0pt 0.75pt {{if $smarty.foreach.ss_link.iteration==$ss_num}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $sl=="chinese"}}{{if $fin_score.$sn.language.avg.score=="" || $fin_score.$sn.language.avg.str==""}}---{{else}}{{$fin_score.$sn.language.avg.str}}{{/if}}{{else}}{{if $fin_score.$sn.$sl.avg.score=="" || $fin_score.$sn.$sl.avg.str==""}}---{{else}}{{$fin_score.$sn.$sl.avg.str}}{{/if}}{{/if}}</td>
{{/if}}
{{if $sl=="chinese" || $sl=="language"}}
<td rowspan="{{$area_span}}" style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0.75pt;">{{if $fin_score.$sn.avg.score == ""}}---{{else}}{{$fin_score.$sn.avg.score}}{{/if}}</td>
<td rowspan="{{$area_span}}" style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 0pt;">{{if $fin_score.$sn.avg.score == "" || $fin_score.$sn.avg.str == ""}}---{{else}}{{$fin_score.$sn.avg.str}}{{/if}}</td></tr>
{{/if}}
{{/if}}
</tr>
{{/foreach}}
{{if !$no_seme_avg && $smarty.post.include_avg}}
<tr align="center">
<td style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 1.5pt;" align="left"><font style="font-size: 8pt;" size="1">&nbsp;學期平均成績</font></td>
{{foreach from=$semes item=si key=sj}}
<td style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0pt;">{{if $fin_score.$sn.$si.avg.score == ""}}---{{else}}{{$fin_score.$sn.$si.avg.score}}{{/if}}</td>
<td style="border-style:solid; border-width:0pt 1.5pt 1.5pt 0pt;">{{if $fin_score.$sn.$si.avg.score == "" || $fin_score.$sn.$si.avg.str == ""}}---{{else}}{{$fin_score.$sn.$si.avg.str}}{{/if}}</td>
{{/foreach}}
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;">---</td>
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;">---</td>
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0.75pt;">---</td>
<td style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 0pt;">---</td></tr>
</tr>
{{/if}}
{{if $smarty.post.include_nor}}
<tr align="center">
<td style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 1.5pt;" align="left"><font style="font-size: 8pt;" size="1">&nbsp;日常表現成績</font></td>
{{foreach from=$semes item=si key=sj}}
<td style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.$si.score == ""}}---{{else}}{{$fin_nor_score.$sn.$si.score}}{{/if}}</td>
<td style="border-style:solid; border-width:0pt 1.5pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.$si.score == "" || $fin_nor_score.$sn.$si.str == ""}}---{{else}}{{$fin_nor_score.$sn.$si.str}}{{/if}}</td>
{{/foreach}}
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.avg.score == ""}}---{{else}}{{$fin_nor_score.$sn.avg.score}}{{/if}}</td>
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.avg.score == "" || $fin_nor_score.$sn.avg.str == ""}}---{{else}}{{$fin_nor_score.$sn.avg.str}}{{/if}}</td>
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0.75pt;">---</td>
<td style="border-style: solid; border-width: 0pt 1.5pt 1.5pt 0pt;">---</td></tr>
</tr>
{{/if}}
<tr>
<td colspan="29"><br>{{$print_str}}
<font style="font-size: 15pt;font-family: 標楷體;"><p align="center">中　華　民　國　{{$year}}　年　{{$month}}　月　{{$day}}　日</p>{{if ($smarty.foreach.ss.iteration) mod 2 == 1}}<br><br><br>{{if !$smarty.post.include_nor}}<br>{{/if}}{{/if}}</font></td>
</tr>
</table>
{{/foreach}}
</center>
</body>
</html>
