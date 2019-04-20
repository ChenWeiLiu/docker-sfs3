<{* $Id: nor.tpl 5310 2009-01-10 07:57:56Z hami $ *}>
<html><head><meta http-equiv="Content-Language" content="zh-tw"><meta http-equiv="Content-Type" content="text/html; charset=big5">
<title></title></head>
<body>
<p style="text-align:center;"><font face="標楷體" size="3"><{$school_name}> <{$sel_year}> 學年度第 <{$sel_seme}> 學期 <{$class_name}> 日常表現成績 (加減分部份)</font></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="610" align="center"  style="page-break-after:always">
<tr>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 1.5pt 1.5pt; padding: 0cm 1.4pt;" align="center" width="35" rowspan="2">座號</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center" width="65" rowspan="2">學&#12288;號</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center" width="90" rowspan="2">姓&#12288;&#12288;名</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;" align="center" width="60">日常考查成績</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;" align="center" width="240" colspan="4">團體活動成績</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center" valign="bottom" width="60" rowspan="2">公共<br>服務<br>(&plusmn;5)</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 1.5pt 1.5pt 0.75pt; padding: 0cm 1.4pt;" align="center" valign="bottom" width="60" rowspan="2">校外<br>特殊<br>表現<br>(+5)</td>
</tr>
<tr>
	<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center">導師<br>評分<br>(&plusmn;5)</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center">班級<br>活動<br>(&plusmn;5)</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center">社團<br>活動<br>(&plusmn;5)</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center">自治<br>活動<br>(&plusmn;5)</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center">例行<br>活動<br>(&plusmn;5)</td>
</tr>
<{section name=arr_key loop=$stud_score}>
<{if $stud_score[arr_key].site_num is not div by 5}>
	<tr>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><font face="Dotum"><{$stud_score[arr_key].site_num}></font></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;" align="center"><font face="Dotum"><{$stud_score[arr_key].stud_id}></font></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;">&nbsp;<{$stud_score[arr_key].stud_name}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].0}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].1}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].2}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].3}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].4}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].5}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 1.5pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].6}></td>
	</tr>
<{else}>
	<tr>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt 1.5pt; padding: 0cm 1.4pt;" align="center"><font face="Dotum"><{$stud_score[arr_key].site_num}></font></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><font face="Dotum"><{$stud_score[arr_key].stud_id}></font></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;">&nbsp;<{$stud_score[arr_key].stud_name}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].0}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].1}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].2}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].3}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].4}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].5}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 1.5pt 1.5pt 0.75pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].6}></td>
	</tr>
<{/if}>
<{/section}>
</table>
<p style="text-align:center;"><font face="標楷體" size="3"><{$school_name}> <{$sel_year}> 學年度第 <{$sel_seme}> 學期 <{$class_name}> 日常表現成績 (文字描述部份)</font></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="610" align="center">
<tr>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 1.5pt 1.5pt; padding: 0cm 1.4pt;" align="center" width="35">座號</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center" width="65">學&#12288;號</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center" width="90">姓&#12288;&#12288;名</td>
	<td style="border-style: solid; border-color: windowtext; border-width: 1.5pt 1.5pt 1.5pt 0.75pt; padding: 0cm 1.4pt;" align="center" width="420">學習描述文字說明</td>
</tr>
<{section name=arr_key loop=$stud_score}>
<{if $stud_score[arr_key].site_num is not div by 5}>
	<tr>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><font face="Dotum"><{$stud_score[arr_key].site_num}></font></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;" align="center"><font face="Dotum"><{$stud_score[arr_key].stud_id}></font></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt; padding: 0cm 1.4pt;">&nbsp;<{$stud_score[arr_key].stud_name}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;" align="center"><{$stud_score[arr_key].7}></td>
	</tr>
<{else}>
	<tr>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt 1.5pt; padding: 0cm 1.4pt;" align="center"><font face="Dotum"><{$stud_score[arr_key].site_num}></font></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;" align="center"><font face="Dotum"><{$stud_score[arr_key].stud_id}></font></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;">&nbsp;<{$stud_score[arr_key].stud_name}></td>
		<td style="border-style: solid; border-color: windowtext; border-width: 0.75pt 1.5pt 1.5pt 0.75pt; padding: 0cm 1.4pt;"><{$stud_score[arr_key].7}></td>
	</tr>
<{/if}>
<{/section}>
</table>
</body></html>