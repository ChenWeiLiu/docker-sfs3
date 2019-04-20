{{* $Id: Growthnotification.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>身高不足學生醫療轉介通知單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{foreach from=$health_data->stud_data item=ddd key=seme_class}}
{{foreach from=$ddd item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=year_name value=$seme_class|@substr:0:-2}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{assign var=h value=$health_data->health_data.$sn}}
<TABLE style="border-collapse: collapse; margin: auto; font: 14pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
        <TR style="height: 16pt;">
          <TD style="font-size:16pt;"><span style="font-size: 14pt;">{{$school_data.sch_cname}}</span>　　<strong>身高不足學生醫療轉介通知單</strong></TD>
		</TR>
		<TR>
		  <TD style="text-align: left;">
		  <TABLE style="font: 14pt 標楷體,標楷體,serif;">
		  <TR><TD style="width:65%">
		  親愛的家長：<br>
		  貴子女 <strong>{{$year_data.$year_name}}{{$class_data.$seme_class}}班{{$seme_num}} 號 {{$health_data->stud_base.$sn.stud_name}}</strong> 經本校實施健康檢查身高測量活動，發現有疑似身高生長遲滯現象！為維護貴子女的健康，請帶他前往內分泌專科醫師處進一步檢查，（若是再次被校方通知，且未獲得確定診斷者，建議回原醫院就診），以把握治療的關鍵時機！<br>
		  </TD><TD style="width:35%;vertical-align:top;">
		  <TABLE style="font-size:8pt;" cellSpacing="0" cellPadding="0">
			<TR>
			<TD colSpan="3" style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; text-align:center;">歷年身高體重記錄</TD>
			</TR>
{{foreach from=$h item=hh key=ys}}
{{if ($ys|substr:-1:1)==1}}
			<TR>
			<TD style="border-left: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;">{{$ys|substr:0:-1|intval}}學年:</TD>
			<TD style="border-top: windowtext 0.75pt solid;">{{$hh.height}}公分&nbsp;</TD>
			<TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;">{{$hh.weight}}公斤</TD>
			</TR>
{{/if}}
{{/foreach}}
			<TR>
			<TD colSpan="3" style="border-top: windowtext 1.5pt solid;">&nbsp;</TD>
			</TR>
		  </TABLE>
		  </TD>
		  </TR>
		  </TABLE>
		  　　　　　　　　此致<br>
		  貴家長<br>
		  
          <p style="font-size: 12pt; text-align: right;">{{$school_data.sch_cname}} 健康中心　敬啟　　 {{$smarty.now|date_format:"%Y"}} 年 {{$smarty.now|date_format:"%m"}} 月 {{$smarty.now|date_format:"%d"}} 日　　</p>
		  <p style="border-bottom: dashed 4px;"></p>
		  <p style="font-size: 16pt; text-align: center; height: 16pt;"><strong>就醫回條</strong></span></p>
		  <p style="font-size: 10pt">{{$year_data.$year_name}}{{$class_data.$seme_class}}班{{$seme_num}}號{{$health_data->stud_base.$sn.stud_name}} 身高{{$h.$year_seme.height}}公分 體重{{$h.$year_seme.weight}}公斤<br>
		  父親身高<u>　　　</u>公分 母親身高<u>　　　</u>公分</p>
		  就醫檢查結果（以下由醫院填寫）<br>
		  就診醫院名稱：　　　　　　　　病歷號碼：<br>
		  就診日期：　　年　　月　　日<br>
		  醫師簽章：<br><br>
		  檢查項目內容：<br><br><span style="font-size: 12pt">
		  身高：　　　　公分　標的身高：　　　　公分<br><br>
		  體重：　　　　公斤　出生時體重：　　　　公斤<br><br>
		  □X光 骨齡檢查<br><br>
		  □血液：　　血色素：　　甲狀腺素：　　生長激素：　　染色體：　　IGF-I：<br><br>
		  其他檢查<br><br></span>
		  醫師建議處理：（可複選）<br>
		  1.診斷名稱：<br>
		  　□家族性矮小□體質性矮小□特發性矮小□生長激素缺乏<br>
		  　□透特納氏症□黏多糖症□體骨發育不全□診治正常<br>
		  </TD>
		</TR>
  </TBODY>
</TABLE>
{{/foreach}}
{{/foreach}}
</BODY></HTML>
