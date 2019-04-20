{{* $Id: score_nor_five_print.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>{{if $smarty.post.years!=6}}五學期{{else}}畢業{{/if}}日常表現成績表</title>
</head>

<body>
<table border="0" cellspacing="0" cellpadding="0" width="610">
<tr align="right">
<td colspan="{{$ss_num+4}}"><b>{{$class_base[$seme_class]}} {{if $smarty.post.years!=6}}五學期{{else}}畢業{{/if}}日常表現成績表　　　　　 <font size="1" style="font-size: 10pt">列印日期：{{$smarty.now|date_format}}</font></b></td>
</tr>
<tr align="center">
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 1.5pt;"><font size="1" style="font-size: 8pt">座號</font></td>
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 0pt;"><font size="1" style="font-size: 8pt">學號</font></td>
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 0pt;"><font size="1" style="font-size: 8pt">姓名</font></td>
{{foreach from=$show_year item=i key=j}}
<td style="border-style:solid; border-width:1.5pt 0.75pt 1.5pt 0pt;"><font size="1" style="font-size: 8pt">{{$i}}學年度<br>第{{$show_seme[$j]}}學期</font></td>
{{/foreach}}
<td style="border-style:solid; border-width:1.5pt 1.5pt 1.5pt 0.75pt;"><font size="1" style="font-size: 8pt">總平均</font></td>
</tr>
{{foreach from=$student_sn item=sn key=site_num name=ss}}
<tr align="center">
<td style="border-style:solid; border-width:0pt 0.75pt {{if ($smarty.foreach.ss.iteration % 5 == 0) || ($smarty.foreach.ss.iteration == $stud_num)}}1.5{{else}}0.75{{/if}}pt 1.5pt;">{{$site_num}}</td>
<td style="border-style:solid; border-width:0pt 0.75pt {{if ($smarty.foreach.ss.iteration % 5 == 0) || ($smarty.foreach.ss.iteration == $stud_num)}}1.5{{else}}0.75{{/if}}pt 0pt;">{{$stud_id[$site_num]}}</td>
<td style="border-style:solid; border-width:0pt 0.75pt {{if ($smarty.foreach.ss.iteration % 5 == 0) || ($smarty.foreach.ss.iteration == $stud_num)}}1.5{{else}}0.75{{/if}}pt 0pt;">{{$stud_name[$site_num]}}</td>
{{foreach from=$semes item=si key=sj}}
<td style="border-style:solid; border-width:0pt 0.75pt {{if ($smarty.foreach.ss.iteration % 5 == 0) || ($smarty.foreach.ss.iteration == $stud_num)}}1.5{{else}}0.75{{/if}}pt 0pt;">{{if $fin_score.$sn.$si.score == ""}}---{{else}}{{$fin_score.$sn.$si.score}}{{/if}}</td>
{{/foreach}}
<td style="border-style:solid; border-width:0pt 1.5pt {{if ($smarty.foreach.ss.iteration % 5 == 0) || ($smarty.foreach.ss.iteration == $stud_num)}}1.5{{else}}0.75{{/if}}pt 0.75pt;">{{if $fin_score.$sn.avg.score == ""}}---{{else}}{{$fin_score.$sn.avg.score}}{{/if}}</td>
</tr>
{{/foreach}}
</table>
</body>
</html>
