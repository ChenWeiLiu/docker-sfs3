<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>視力不良通知單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>

{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}

{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num }}

{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{assign var=year_name value=$seme_class|@substr:0:-2}}

<TABLE style="border-collapse: collapse; margin: auto; font: 14pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 14pt 標楷體,標楷體,serif;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 16pt;">
          <TD style="font-size:16pt;"><span style="font-size: 14pt;">{{$school_data.sch_cname}}</span>　　<strong>視力不良通知單</strong></TD>
		</TR>
		<TR>
		  <TD style="text-align: left;">
		  親愛的家長：<br>
		  貴子女 <strong>{{$year_data.$year_name}}{{$class_data.$seme_class}}班 {{$seme_num}} 號 {{$health_data->stud_base.$sn.stud_name}}</strong> <br>
		  本學期視力檢查結果為 <strong>視力不良</strong>。<br>
		  請貴家長帶貴子女前往醫院做更進一步的檢查矯治，以免影響學習，並請督促改善貴子女的生活習慣：
		  <ol style="font-size: 12pt;">
			<li>看書寫字時，姿勢要端正，書與眼睛的距離要在35公分間。</li>
			<li>看電視或使用電腦，每30分鐘休息10分鐘。</li>
			<li>看電視時光線不可太暗，要保持二公尺以上距離。</li>
			<li>睡眠要充足，不要熬夜。</li>
			<li>注重均衡的飲食，攝取足量的營養素。</li>
			<li>養成望遠凝視的習慣，並多做戶外活動。</li>
			<li>儘量減少近距離用眼的活動。</li>
			<li>請將回條於 {{$smarty.post.rmonth}} 月 {{$smarty.post.rday}} 日前交回健康中心並做複檢。</li>
		  </ol>
		  　　　　　　　　此致<br>
		  貴家長<br>

          <p style="font-size: 12pt; text-align: right;">{{$school_data.sch_cname}} 健康中心　敬啟　　 {{$smarty.now|date_format:"%Y"}} 年 {{$smarty.now|date_format:"%m"}} 月 {{$smarty.now|date_format:"%d"}} 日　　</p>
		  <p style="border-bottom: dashed 4px;"></p>
		  <p style="font-size: 16pt; text-align: center; height: 16pt;"><strong>醫院（診所）醫生檢查回條</strong></span></p>
		  <strong>{{$year_data.$year_name}}{{$class_data.$seme_class}}班 {{$seme_num}} 號       學生姓名：{{$health_data->stud_base.$sn.stud_name}}</strong><br>
		  一、診療院所名稱：<br>
		  二、檢查日期：　　　年　　　月　　　日<br>
		  三、醫師簽章：<br><br>
		  四、醫師檢查結果：　□正常　□異常（請於下列項目打勾，可複選）<br>
		  　1.弱視：　□雙眼　□右眼　□左眼　　□矯視0.5以下<br>
		  　2.斜視：　□內斜　□外斜　□上下斜　□單眼<br>
		  　3.屈光不正：（散瞳後）<br>
		  　　(1)近視：　□雙眼　□右眼　□左眼　□度數：<u>　　</u><br>
		  　　(2)遠視：　□雙眼　□右眼　□左眼　□度數：<u>　　</u><br>
		  　　(3)散光：　□雙眼　□右眼　□左眼　□度數：<u>　　</u><br>
		  　　(4)不等視：□<br>
		  　4.其他異常：（請註明）<br><br>
		  五、醫師建議處理：（可複選）<br>
		  　□(1)配眼鏡矯正　□(2)換鏡　□(3)遮眼治療　□(4)點藥<br>
		  　□(5)定期追蹤　　□(6)其他<u>　　　　　　　　　　　　</u><br>
		  六、未到眼科複檢或繼續治療原因：<br>
		  　□(1)另類治療　□(2)交通不便　□(3)家長沒時間　□(4)經濟困難<br>
		  　□(5)不需要　　□(6)其他<u>　　　　　　　　　　　　</u>
		  <p style="font-size: 14pt; text-align: right;">家長簽章：　　　　　　　　年　　月　　日</p>
		  </TD>
		</TR>
		</TBODY>
	  </TABLE>
	</TD>
  </TR>
  </TBODY>
</TABLE>
{{/foreach}}
</BODY></HTML>
