<html>
<head>
<meta http-equiv="Content-Language" content="zh-tw">
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>{{$report_title}}</title>
</head>
<body>
{{foreach from=$stud_data item=d key=i}}
<p align="center"><font size="5" face="標楷體">{{$report_title}}</font></p>
<table border="0" width="100%" id="table1">
	<tr>
		<td height="31" colspan="2"><font face="標楷體">就讀國中：<font color="{{$data_color}}"><b><u>{{$school_long_name}}</u></b></font></font></td>
		<td height="31" width="43%"><font face="標楷體">就讀國中代碼：<font color="{{$data_color}}"><b><u>{{$sch_id}}</u></b></font></font></td>
	</tr>
	<tr>
		<td width="26%"><font face="標楷體">班級：<font color="{{$data_color}}"><b><u>{{$d.class_id}}</u></b></font></font></td>
		<td width="31%"><font face="標楷體">姓名：<font color="{{$data_color}}"><b><u>{{$d.stud_name}}</u></b></font></font></td>
		<td width="43%"><font face="標楷體">身分證統一編號：<font color="{{$data_color}}"><b><u>{{$d.stud_person_id}}</u></b></font></font></td>
	</tr>
</table>
<div align="center">
	<table  border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr>
			<td colspan="2" align="center" height="41" bgcolor="{{$header_bgcolor}}">比序項目</td>
			<td width="65%" align="center" height="41" bgcolor="{{$header_bgcolor}}">積分核算說明</td>
			<td width="8%" align="center" height="41" bgcolor="{{$header_bgcolor}}">單項<br>積分</td>
			<td width="9%" align="center" height="41" bgcolor="{{$header_bgcolor}}">比序項目積分</td>
		</tr>
		<tr>
			<td rowspan="4" align="center" width="4%">多元學習表現</td>
			<td align="center" width="12%" height="43">競　　賽</td>
			<td width="65%" align="left" height="43">{{foreach from=$competetion_score.$i.detail item=data key=k}}<li>({{$level_array[$data.level]}}-{{$squad_array[$data.squad]}}){{$data.name}}{{if $data.name=='其他'}}-{{$data.memo}}{{/if}}：{{$data.rank}}</li>{{/foreach}}</td>
			<td width="8%" align="center" height="43"><i>
			<font size="4" color="{{$data_color}}">{{if $competetion_score.$i.score}}{{$competetion_score.$i.score}}{{else}}0{{/if}}</font></i></td>
			<td width="9%" align="center" rowspan="4"><b>
			<font size="5" color="{{$data_color}}">{{$diversification_score.$i}}</font></b></td>
		</tr>
		<tr>
			<td align="center" width="12%" height="68">服務學習</td>
			<td width="65%" align="left" height="68">擔任班級幹部、小老師或社團幹部滿
			<font color="{{$data_color}}" size="4"><b><u>{{if $service_score.$i.leader}}{{$service_score.$i.leader}}{{else}}0{{/if}}</u></b></font> 學期。<br>參加校內服務學習課程或活動，或於假日、寒暑假期間參加志工服務或社區服務：滿  
			<font color="{{$data_color}}" size="4"><b><u>{{if $service_score.$i.hours}}{{$service_score.$i.hours}}{{else}}0{{/if}}</u></b></font> 小時。</td>
			<td width="8%" align="center" height="68"><i>
			<font size="4" color="{{$data_color}}">{{$service_score.$i.bonus}}</font></i></td>
		</tr>
		<tr>
			<td align="center" width="12%" height="49">日常生活<br>表現評量</td>
			<td width="65%" align="left" height="49"> 
			累計嘉獎 <font color="{{$data_color}}" size="4"><b><u>{{if $fault_score.$i.1}}{{$fault_score.$i.1}}{{else}}0{{/if}}</u></b></font> 
			次，小功 <font color="{{$data_color}}" size="4"><b><u>{{if $fault_score.$i.3}}{{$fault_score.$i.3}}{{else}}0{{/if}}</u></b></font> 次，大功 
			<font color="{{$data_color}}" size="4"><b><u>{{if $fault_score.$i.9}}{{$fault_score.$i.9}}{{else}}0{{/if}}</u></b></font> 次；<br>　　警告 
			<font color="{{$data_color}}" size="4"><b><u>{{if $fault_score.$i.a}}{{$fault_score.$i.a}}{{else}}0{{/if}}</u></b></font> 次，小過 
			<font color="{{$data_color}}" size="4"><b><u>{{if $fault_score.$i.b}}{{$fault_score.$i.b}}{{else}}0{{/if}}</u></b></font> 次，大過 
			<font color="{{$data_color}}" size="4"><b><u>{{if $fault_score.$i.c}}{{$fault_score.$i.c}}{{else}}0{{/if}}</u></b></font> 次。</td>
			<td width="8%" align="center" height="49"><i>
			<font size="4" color="{{$data_color}}">{{if $fault_score.$i.bonus}}{{$fault_score.$i.bonus}}{{else}}0{{/if}}</font></i></td>
		</tr>
		<tr>
			<td align="center" width="12%" height="88">體 適 能</td>
			<td width="65%" align="left" height="88">肌耐力  
			<font color="{{$data_color}}" size="4"><b><u>{{if $fitness_score.$i.2}}達{{else}}未達{{/if}}</u></b></font> 門檻標準。<br>柔軟度  
			<font color="{{$data_color}}" size="4"><b><u>{{if $fitness_score.$i.1}}達{{else}}未達{{/if}}</u></b></font> 門檻標準。<br>瞬發力  
			<font color="{{$data_color}}" size="4"><b><u>{{if $fitness_score.$i.3}}達{{else}}未達{{/if}}</u></b></font> 門檻標準。<br>心肺耐力  
			<font color="{{$data_color}}" size="4"><b><u>{{if $fitness_score.$i.4}}達{{else}}未達{{/if}}</u></b></font> 門檻標準。</td>
			<td width="8%" align="center" height="88"><i>
			<font size="4" color="{{$data_color}}">{{if $fitness_score.$i.bonus}}{{$fitness_score.$i.bonus}}{{else}}0{{/if}}</font></i></td>
		</tr>
		<tr>
			<td colspan="2" align="center" height="33">技藝優良</td>
			<td width="65%" align="left" height="33">技藝教育課程平均總成績 
			 
			<font color="{{$data_color}}" size="4"><b><u>{{if $particular_score.$i.score}}{{$particular_score.$i.score}}{{else}}0{{/if}}</u></b></font> 分。</td>
			<td width="8%" align="center" height="33"><i>
			<font size="4" color="{{$data_color}}">{{$particular_score.$i.bonus}}</font></i></td>
			<td width="9%" align="center" height="33"><b>
			<font size="5" color="{{$data_color}}">{{$particular_score.$i.bonus}}</font></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" height="35">弱勢身分</td>
			<td width="65%" align="left" height="35">{{if $disadvantage_score.$i.score}}具<font color="{{$data_color}}"> <b>
			<u><font size="4">{{$disadvantage_score.$i.disadvantage_name}}</font></u></b> </font>身分。{{/if}}</td>
			<td width="8%" align="center" height="35"><i>
			<font size="4" color="{{$data_color}}">{{$disadvantage_score.$i.score}}</font></i></td>
			<td width="9%" align="center" height="35"><b>
			<font size="5" color="{{$data_color}}">{{$disadvantage_score.$i.score}}</font></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" height="69">均衡學習</td>
			<td width="65%" align="left" height="69">健康與體育5學期平均成績			 
			<font color="{{$data_color}}" size="4"><b><u>{{$balance_area_score.$i.health.avg}}</u></b></font> 分。<br>藝術與人文5學期平均成績  
			<font color="{{$data_color}}" size="4"><b><u>{{$balance_area_score.$i.art.avg}}</u></b></font> 分。<br>綜合活動5學期平均成績  
			<font color="{{$data_color}}" size="4"><b><u>{{$balance_area_score.$i.complex.avg}}</u></b></font> 分。</td>
			<td width="8%" align="center" height="69"><i>
			<font size="4" color="{{$data_color}}">{{$balance_score_t.$i.score}}</font></i></td>
			<td width="9%" align="center" height="69"><b>
			<font size="5" color="{{$data_color}}">{{$balance_score_t.$i.score}}</font></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" height="83">適性輔導</td>
			<td width="65%" align="left" height="83">國中學生生涯輔導紀錄手冊<span style="font-size: 12.0pt; font-family: 新細明體">「生涯發展規劃書」中</span><span style="font-family: 新細明體"><br>家長意見  
			</span><u><font color="{{$data_color}}"><b><font size="4">{{if $personality_score.$i.score_adaptive_domicile}}勾選{{else}}未勾選{{/if}}</font> </b>
			</font></u><span style="font-family: 新細明體">五專</span>。<span style="font-family: 新細明體"><br>導師意見  
			</span><u><font color="{{$data_color}}" size="4"><b>{{if $personality_score.$i.score_adaptive_tutor}}勾選{{else}}未勾選{{/if}}</b></font></u><span style="font-family: 新細明體"> 
			五專</span>。<span style="font-family: 新細明體"><br>輔導教師意見 </span><u>
			<font color="{{$data_color}}" size="4"><b>{{if $personality_score.$i.score_adaptive_guidance}}勾選{{else}}未勾選{{/if}}</b></font></u><span style="font-family: 新細明體"> 
			五專</span>。</td>
			<td width="8%" align="center" height="83"><i>
			<font size="4" color="{{$data_color}}">{{$personality_score.$i.bonus}}</font></i></td>
			<td width="9%" align="center" height="83"><b>
			<font size="5" color="{{$data_color}}">{{$personality_score.$i.bonus}}</font></b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" bgcolor="{{$header_bgcolor}}" height="50"><font size="4" face="標楷體">合　　　計</font></td>
			<td>
			</td>
			<td align="center">
			</td>
			<td width="9%" align="center"><b><font size="5" color="{{$data_color}}">{{$diversification_score.$i+$particular_score.$i.bonus+$disadvantage_score.$i.score+$balance_score_t.$i.score+$personality_score.$i.bonus}}</font></b></td>
		</tr>
	</table>
</div>
<p>　</p>
<p><font size="5" face="標楷體"><b>就讀國中學校戳章：</b></font></p>
<p style="page-break-after: always">
{{/foreach}}
</body>

</html>
