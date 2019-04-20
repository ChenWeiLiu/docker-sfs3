{{* $Id: score_manage_new_top_bank.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>第一階段成績名次參考表</title>
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
<table border="0" cellspacing="0" cellpadding="0" width="610" >
<tr align="right">
<td colspan="17" align="center" style="font-size: 16pt;font-family: 標楷體;font-weight: bold;">{{$school.sch_cname}}{{$sel_year}}學年度第{{$sel_seme}}學期第{{$smarty.post.stage}}階段<br>定期考查成績名次參考表<br><br>
</tr>
<tr align="center">
<td style="border-style: solid; border-width: 1.5pt;width: 50%;"><font style="font-size: 12pt;">全校名次</font></td>
<td colspan="2" style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0pt;"><font style="font-size: 12pt;">總分</font></td>
</tr>
<tr align="center">
<td style="border-style: solid; border-width:  0pt 1.5pt 0.75pt 1.5pt;"><font style="font-size: 12pt;">1</font></td>
<td colspan="2" style="border-style: solid; border-width: 0pt 1.5pt 0.75pt 0pt;"><font style="font-size: 12pt;">{{$rowdata.0.score}}</font></td>
</tr>
{{section loop=$rowdata start=20 step=20 name=s20 max=5}}
<tr align="center">
<td style="border-style: solid; border-width:  0pt 1.5pt 0.75pt 1.5pt;"><font style="font-size: 12pt;">{{$smarty.section.s20.index}}</font></td>
<td colspan="2" style="border-style: solid; border-width: 0pt 1.5pt 0.75pt 0pt;"><font style="font-size: 12pt;">{{$rowdata[s20].score}}</font></td>
</tr>
{{/section}}
{{section loop=$rowdata start=150 step=50 name=s50}}
<tr align="center">
<td style="border-style: solid; border-width:  0pt 1.5pt 0.75pt 1.5pt;"><font style="font-size: 12pt;">{{$smarty.section.s50.index}}</font></td>
<td colspan="2" style="border-style: solid; border-width: 0pt 1.5pt 0.75pt 0pt;"><font style="font-size: 12pt;">{{$rowdata[s50].score}}</font></td>
</tr>
{{/section}}
<tr align="center">
<td style="border-style: solid; border-width:  0.75pt 0pt 0pt 0pt;"><font style="font-size: 12pt;">&nbsp;</font></td>
<td colspan="2" style="border-style: solid; border-width: 0.75pt 0pt 0pt 0pt;"><font style="font-size: 12pt;">&nbsp;</font></td>
</tr>
</table>
</center>
</body>
</html>
