{{* $Id: stud_grad_grad_score_print.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>{{if $smarty.post.years==5}}五學期{{else}}畢業{{/if}}成績表</title>
</head>

<body>
{{foreach from=$student_sn item=sn key=site_num name=ss}}
{{if $smarty.foreach.ss.iteration % 4 == 1}}
<table border="0" cellspacing="0" cellpadding="0" width="610" {{if ($smarty.foreach.ss.iteration+4)<$stud_num }}style="page-break-after: always"{{/if}}>
<tr align="right">
<td colspan="{{$ss_num+5}}"><b>{{$class_base[$seme_class]}} {{if $smarty.post.years==5}}五學期{{else}}畢業{{/if}}成績表　　　　　 <font size="1" style="font-size: 10pt">列印日期：{{$smarty.now|date_format}}</font></b></td>
</tr>
<tr align="center">
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 1.5pt;"><font size="1" style="font-size: 8pt">座號</font></td>
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 0pt;"><font size="1" style="font-size: 8pt">學號</font></td>
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 0pt;"><font size="1" style="font-size: 8pt">姓名</font></td>
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 0pt;"><font size="1" style="font-size: 8pt">學習領域</font></td>
{{foreach from=$show_year item=i key=j}}
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 0pt;"><font size="1" style="font-size: 8pt">{{$i}}{{if $jos!=0}}學年度<br>第{{/if}}{{if $jos!=0}}{{$show_seme[$j]}}學期{{else}}{{if $show_seme[$j]==1}}上{{else}}下{{/if}}{{/if}}</font></td>
{{/foreach}}
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 0.75pt;"><font size="1" style="font-size: 8pt">各領域<br>平均</font></td>
<td style="border-style:solid; border-width:1.5pt 1.5pt 1.5pt 0.75pt;"><font size="1" style="font-size: 8pt">總平均</font></td>
</tr>
{{/if}}
{{foreach from=$ss_link item=sl name=ss_link}}
<tr align="center">
{{if $smarty.foreach.ss_link.iteration == 1}}
<td rowspan="{{$ss_num+1}}" style="border-style:solid; border-width:0pt 0.75pt 1.5pt 1.5pt;">{{$site_num}}</td>
<td rowspan="{{$ss_num+1}}" style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0pt;">{{$stud_id[$site_num]}}</td>
<td rowspan="{{$ss_num+1}}" style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0pt;">{{$stud_name[$site_num]}}</td>
{{/if}}
<td align="left" style="border-style:solid; border-width:0pt 0.75pt 0.75pt 0pt;"><font size="1" style="font-size: 8pt">&nbsp;{{$link_ss[$sl]}}</font></td>
{{foreach from=$semes item=si key=sj}}
<td style="border-style:solid; border-width:0pt 0.75pt 0.75pt 0pt;">{{if $fin_score.$sn.$sl.$si.score == ""}}---{{else}}{{$fin_score.$sn.$sl.$si.score}}{{/if}}</td>
{{/foreach}}
{{if $sl!="local" and $sl!="english"}}
<td {{if $sl=="chinese"}}rowspan="3"{{/if}} style="border-style:solid; border-width:0pt 0.75pt 0.75pt 0.75pt;">{{if $sl=="chinese"}}{{$fin_score.$sn.language.avg.score}}{{else}}{{if $fin_score.$sn.$sl.avg.score == ""}}---{{else}}{{$fin_score.$sn.$sl.avg.score}}{{/if}}{{/if}}</td>
{{/if}}
{{if $sl=="chinese"}}<td rowspan="9" style="border-style:solid; border-width:0pt 1.5pt 0.75pt 0.75pt;">{{$fin_score.$sn.avg.score}}<br>({{$fin_score.$sn.avg.str}})</td>{{/if}}
</tr>
{{/foreach}}
<tr align="center">
<td align="left" style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0pt;"><font size="1" style="font-size: 8pt">&nbsp;日常生活表現</font></td>
{{foreach from=$semes item=si key=sj}}
<td style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0pt;">{{if $fin_nor_score.$sn.$si.score == ""}}---{{else}}{{$fin_nor_score.$sn.$si.score}}{{/if}}</td>
{{/foreach}}
<td style="border-style:solid; border-width:0pt 0.75pt 1.5pt 0.75pt;">{{if $fin_nor_score.$sn.avg.score == ""}}---{{else}}{{$fin_nor_score.$sn.avg.score}}{{/if}}</td>
<td style="border-style:solid; border-width:0pt 1.5pt 1.5pt 0.75pt;">---</td>
</tr>
{{if $smarty.foreach.ss.iteration % 4 == 0 || $smarty.foreach.ss_link.iteration == $stud_num}}
</table>
{{/if}}
{{/foreach}}
</body>
</html>
