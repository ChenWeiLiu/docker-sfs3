{{* $Id: stud_basic_test_distest4_print.tpl 5871 2010-02-26 13:51:07Z brucelyc $ *}}
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>免試入學定考成績校對單</title>
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
{{assign var=cc value=1}}
{{foreach from=$student_sn item=d name=ss key=seme_class}}
{{foreach from=$d item=sn key=site_num}}
<table border="0" cellspacing="0" cellpadding="0" width="610" {{if $cc mod 2 == 0}}style="page-break-after: always"{{/if}}>
<tr align="right">
<td colspan="29" align="center" style="font-size: 16pt;font-family: 標楷體;font-weight: bold;">{{$sch.sch_cname}}免試入學定考成績校對單<br><br>
<table border="0">
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td>學生姓名：{{$stud_data.$sn.stud_name}}<td width="150"><td>班級座號：{{$seme_class|@substr:-2:2|@intval}} 班 {{$site_num}} 號</td></tr>
<tr style="font-size: 16pt;font-family: 標楷體;font-weight: bold;"><td>學生學號：{{$stud_data.$sn.stud_id}}</b><td><td><b>出生日期：{{$stud_data.$sn.stud_birthday}}</td></tr>
</table>
</tr>
<tr align="center">
<td colspan="2" style="border-style: solid; border-width: 1.5pt 0.75pt 1.5pt 1.5pt;"><font style="font-size: 8pt;">學期 \ 次別 \ 科目</font></td>
{{foreach from=$s_arr item=i name=sj}}
<td style="border-style: solid; border-width: 1.5pt {{if $smarty.foreach.sj.iteration==5}}1.5{{else}}0.75{{/if}}pt 1.5pt 0pt;" width="13%"><font style="font-size: 8pt;">{{$i}}</font></td>
{{/foreach}}
</tr>
{{foreach from=$seme_arr item=cs key=ss}}
{{foreach from=$stage_arr.$ss item=sg}}
<tr align="center">
{{if $sg==1}}
{{assign var=c value=$stage_arr.$ss|@count}}
<td rowspan="{{$c+1}}" style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 1.5pt;"><font style="font-size: 8pt;">{{$cs}}</font></td>
{{/if}}
<td style="border-style: solid; border-width: 0pt 0.75pt 0.75pt 0pt;" width="10%"><font style="font-size: 8pt;">第{{$sg}}次</font></td>
{{foreach from=$s_arr item=i key=j name=sj}}
<td style="border-style:solid; border-width:0pt {{if $smarty.foreach.sj.iteration==5}}1.5{{else}}0.75{{/if}}pt 0.75pt 0pt;">{{if $rowdata.$sn.$ss.$sg.$j.score == ""}}---{{else}}{{$rowdata.$sn.$ss.$sg.$j.score}}{{/if}}</td>
{{/foreach}}
</tr>
{{/foreach}}
<tr align="center">
{{if $ss=="9991"}}
<td colspan="2" style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 1.5pt;"><font style="font-size: 8pt;">總平均</font></td>
{{else}}
<td style="border-style: solid; border-width: 0pt 0.75pt 1.5pt 0pt;" width="10%"><font style="font-size: 8pt;">平均</font></td>
{{/if}}
{{foreach from=$s_arr item=i key=j name=sj}}
<td style="border-style:solid; border-width:0pt {{if $smarty.foreach.sj.iteration==5}}1.5{{else}}0.75{{/if}}pt 1.5pt 0pt;">{{if $rowdata.$sn.$ss.avg.$j.score == ""}}---{{else}}{{$rowdata.$sn.$ss.avg.$j.score}}{{/if}}</td>
{{/foreach}}
{{/foreach}}
<tr>
<td colspan="29">
{{assign var=y value=$smarty.now|date_format:"%Y"}}
<font style="font-size: 15pt;font-family: 標楷體;"><p align="center">中　華　民　國　{{$y-1911}}　年　{{$smarty.now|date_format:"%m"}}　月　{{$smarty.now|date_format:"%d"}}　日</p>{{if $cc mod 2 == 1}}<br><br><br><br><br><br>{{/if}}</font></td>
</tr>
</table>
{{assign var=cc value=$cc+1}}
{{/foreach}}
{{/foreach}}
</center>
</body>
</html>
