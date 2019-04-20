{{* $Id: fitness_print_html.tpl 7069 2013-01-13 08:10:57Z smallduh $ *}}
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>體適能測驗成績表</title>
</head>
<style type="text/css">
<!--
td {
	font-size:10pt;
}
-->
</style>
<body>
<center>
<table border="0" cellspacing="0" cellpadding="0" width="610">
<tr align="right">
<td align="center" style="font-size: 16pt;font-family: 標楷體;font-weight: bold;">{{$sch.sch_cname}}{{$sel_year}}學年度第{{$sel_seme}}學期<br>{{$class_arr.$class_num}}體適能測驗成績表<br>
<table cellspacing="0" cellpadding="0" width="100%">
<tr style="text-align:center;">
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 1.5pt;">座<br>號</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">姓名</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">生月</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">身高<br>(cm)[%]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">體重<br>(kg)[%]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">BMI指數<br>(kg/m<sup>2</sup>)[%]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">坐姿前彎<br>(cm)[%]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">立定跳遠<br>(cm)[%]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">仰臥起坐<br>(次)[%]</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">{{if $IS_JHORES==0}}800{{else}}1600{{/if}}公尺<br>(秒)[%]</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">年齡</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">測驗<br>年月</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">獎章</td>
</tr>
{{foreach from=$rowdata item=d key=i name=fdrows}}
{{assign var=sn value=$d.student_sn}}
<tr style="text-align:right;">
{{if $smarty.foreach.fdrows.iteration mod 5 == 1}}
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 1.5pt;">{{$d.seme_num}}</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;text-align:left;">{{$d.stud_name}}</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">{{$d.stud_birthday}}</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$fd.$sn.tall}}[{{$fd.$sn.prec_t}}]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$fd.$sn.weigh}}[{{$fd.$sn.prec_w}}]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$fd.$sn.bmt}}[{{$fd.$sn.prec_b}}]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$fd.$sn.test1}}[{{$fd.$sn.prec1}}]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$fd.$sn.test3}}[{{$fd.$sn.prec3}}]</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$fd.$sn.test2}}[{{$fd.$sn.prec2}}]</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">{{$fd.$sn.test4}}[{{$fd.$sn.prec4}}]</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;text-align:center;">{{$fd.$sn.age}}</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;text-align:center;">{{$fd.$sn.test_y}}-{{$fd.$sn.test_m}}</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;text-align:center;">{{if $fd.$sn.reward}}{{$fd.$sn.reward}}{{else}}--{{/if}}</td>
{{else}}
<td style="border-style: solid; border-width: 0.75pt 1.5pt 0pt 1.5pt;">{{$d.seme_num}}</td>
<td style="border-style: solid; border-width: 0.75pt 1.5pt 0pt 0pt;text-align:left;">{{$d.stud_name}}</td>
<td style="border-style: solid; border-width: 0.75pt 1.5pt 0pt 0pt;">{{$d.stud_birthday}}</td>
<td style="border-style: solid; border-width: 0.75pt 0.75pt 0pt 0pt;">{{$fd.$sn.tall}}[{{$fd.$sn.prec_t}}]</td>
<td style="border-style: solid; border-width: 0.75pt 0.75pt 0pt 0pt;">{{$fd.$sn.weigh}}[{{$fd.$sn.prec_w}}]</td>
<td style="border-style: solid; border-width: 0.75pt 0.75pt 0pt 0pt;">{{$fd.$sn.bmt}}[{{$fd.$sn.prec_b}}]</td>
<td style="border-style: solid; border-width: 0.75pt 0.75pt 0pt 0pt;">{{$fd.$sn.test1}}[{{$fd.$sn.prec1}}]</td>
<td style="border-style: solid; border-width: 0.75pt 0.75pt 0pt 0pt;">{{$fd.$sn.test3}}[{{$fd.$sn.prec3}}]</td>
<td style="border-style: solid; border-width: 0.75pt 0.75pt 0pt 0pt;">{{$fd.$sn.test2}}[{{$fd.$sn.prec2}}]</td>
<td style="border-style: solid; border-width: 0.75pt 1.5pt 0pt 0pt;">{{$fd.$sn.test4}}[{{$fd.$sn.prec4}}]</td>
<td style="border-style: solid; border-width: 0.75pt 1.5pt 0pt 0pt;text-align:center;">{{$fd.$sn.age}}</td>
<td style="border-style: solid; border-width: 0.75pt 1.5pt 0pt 0pt;text-align:center;">{{$fd.$sn.test_y}}-{{$fd.$sn.test_m}}</td>
<td style="border-style: solid; border-width: 0.75pt 1.5pt 0pt 0pt;text-align:center;">{{if $fd.$sn.reward}}{{$fd.$sn.reward}}{{else}}--{{/if}}</td>
{{/if}}
</tr>
{{/foreach}}
{{foreach from=$avg item=d key=i}}
<tr style="text-align:right;">
<td colspan="3" style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 1.5pt;">{{$avg_title.$i}}平均</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$d.a_tall|@round:1}}</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$d.a_weigh|@round:1}}</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$d.a_bmt|@round:1}}</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$d.a_test1|@round:1}}</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$d.a_test3|@round:1}}</td>
<td style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;">{{$d.a_test2|@round:1}}</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">{{$d.a_test4|@round:1}}</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;text-align:center;">--</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;text-align:center;">---</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;text-align:center;">--</td>
</tr>
{{/foreach}}
<tr style="text-align:right;">
<td colspan="3" style="border-style: solid; border-width: 1.5pt;">50％以上人數</td>
{{foreach from=$cou item=d name=cou}}
{{if $smarty.foreach.cou.last==$smarty.foreach.cou.iteration}}
<td style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0pt;">{{$d}}</td>
{{else}}
<td style="border-style: solid; border-width: 1.5pt 0.75pt 1.5pt 0pt;">{{$d}}</td>
{{/if}}
{{/foreach}}
<td style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0pt;text-align:center;">--</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0pt;text-align:center;">---</td>
<td style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0pt;text-align:center;">--</td>
</tr>
</table>
</td></tr></table>
</body></html>
