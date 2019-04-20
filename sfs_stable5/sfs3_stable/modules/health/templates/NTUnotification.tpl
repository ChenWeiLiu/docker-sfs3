<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>立體感異常通知單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_base item=d key=sn name=rows}}
{{assign var=year_name value=$d.seme_class|@substr:0:-2}}
{{assign var=class_name value=$d.seme_class|@substr:-2:2}}
{{assign var=class_name value=$class_name|@intval}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<TABLE style="border-collapse: collapse; margin: auto; font: 14pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 14pt 標楷體,標楷體,serif;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 16pt;">
          <TD colSpan="6" style="font-size:16pt;"><span style="font-size: 14pt;">{{$school_data.sch_cname}}</span>　　<strong>立體感異常通知單</strong></TD>
		</TR>
		<TR>
		  <TD colSpan="6" style="text-align: left;">
		  <strong>{{$year_data.$year_name}}{{$class_data[$d.seme_class]}}班 {{$d.seme_num}} 號 {{$health_data->stud_base.$sn.stud_name}}</strong><br>
		  親愛的家長：<br>
		  茲為了瞭解貴子弟的視力狀況，本校進行立體感篩檢工作，結果如下：<br>
		  </TD>
		</TR>
        <TR style="height: 20pt;font-size: 12pt;">
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="2">裸視視力</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="2">矯正視力</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="2" rowSpan="2">NTU立體圖篩檢</TD>
		</TR>
        <TR style="height: 20pt;font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >右眼</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" 
          >左眼</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >右眼</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" 
          >左眼</TD>
		</TR>
        <TR style="height: 40pt;font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >{{$dd.r.sight_o}}</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >{{$dd.l.sight_o}}</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-left: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >{{$dd.r.sight_r}}</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >{{$dd.l.sight_r}}</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          colSpan="2">立體感異常</TD>
		</TR>
		<TR>
		  <TD colSpan="6" style="text-align: left;">
		  請速帶貴子弟到眼科醫師處接受進一步診治，並請醫師將診治結果填載後之回條交由級任老師（導師）轉還健康中心。謝謝合作！
          <p style="font-size: 12pt; text-align: right;">{{$school_data.sch_cname}} 健康中心　敬啟　　 {{$smarty.now|date_format:"%Y"}} 年 {{$smarty.now|date_format:"%m"}} 月 {{$smarty.now|date_format:"%d"}} 日　　</p>
		  <p style="border-bottom: dashed 4px;"></p>
		  <p style="font-size: 16pt; text-align: center; height: 16pt;"><strong>醫院（診所）醫生檢查回條</strong></span></p>
		  <strong>{{$year_data.$year_name}}{{$class_data[$d.seme_class]}}班 {{$d.seme_num}} 號       學生姓名：{{$health_data->stud_base.$sn.stud_name}}</strong><br>
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
